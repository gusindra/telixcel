<?php

namespace App\Http\Controllers;

use App\Models\CommerceItem;
use App\Models\Contract;
use App\Models\Order;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Stock;
use App\Models\Syn;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class CommercialController extends Controller
{
    public $user_info;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Your auth here
            $id = array("PRODUCT", "QUOTATION", "CONTRACT");
            $permission = checkPermisissions($id);

            if($permission){
                return $next($request);
            }
            abort(404);
        });
    }

    public function index()
    {
        return view('assistant.commercial.index', ['key'=>'item']);
    }

    public function create(Request $request)
    {
        if($request->get('data')=='quotation'){
            return view('assistant.commercial.quotation.create');
        }elseif($request->get('data')=='contract'){
            return view('assistant.commercial.contract.create');
        }else{
            return view('assistant.commercial.create');
        }
    }

    public function show($key)
    {
        if($key=='quotation'){
            return view('assistant.commercial.quotation.index', ['key'=>$key]);
        }elseif($key=='contract'){
            return view('assistant.commercial.contract.index', ['key'=>$key]);
        }
        return view('assistant.commercial.index', ['key'=>$key]);
    }

    public function edit($key, $id)
    {
        if ($key == 'quotation') {
            $data = Quotation::findPublic($id);
            if ($data) {
                if ($redir = $this->redirectCommercialUuid('quotation', $data, $id)) {
                    return $redir;
                }

                return view('assistant.commercial.quotation.show', ['code' => $data->id, 'quote' => $data]);
            }
        } elseif ($key == 'contract') {
            $data = Contract::findPublic($id);
            if ($data) {
                if ($redir = $this->redirectCommercialUuid('contract', $data, $id)) {
                    return $redir;
                }

                return view('assistant.commercial.contract.show', ['code' => $data->id, 'contract' => $data]);
            }
        }

        $data = CommerceItem::findPublic($id);
        if ($data) {
            if ($redir = $this->redirectCommercialUuid('item', $data, $id)) {
                return $redir;
            }

            return view('assistant.commercial.show', ['code' => $data->id, 'data' => $data]);
        }
        abort(404);
    }

    private function redirectCommercialUuid(string $key, $model, $id)
    {
        if ($model->uuid && (string) $id !== (string) $model->uuid && ctype_digit((string) $id)) {
            return redirect()->route('commercial.edit.show', array_merge(
                ['key' => $key, 'id' => $model->uuid],
                request()->query()
            ));
        }

        return null;
    }

    public function template($key, $id){
        // return $key;
        if($id=='quotation'){
            // clientRef = the customer chosen in Customer Information (client_id);
            // company/project/client load the Source entity (model_id) for the logo.
            $q = Quotation::with(['clientRef', 'company', 'project.company', 'client', 'items'])->findPublic($key);
            return view('assistant.commercial.quotation.template', ['data' => $q]);
        }elseif($id=='contract'){
            $c = Contract::findPublic($key);
            return view('assistant.commercial.contract.template', ['code'=>$c]);
        }elseif($id=='invoice'){
            $o = Order::findPublic($key);
            return view('assistant.order.template', ['data'=>$o]);
        }
    }

    public function sync(){
        $commerce_items = CommerceItem::get();
        $syn = Syn::where('user_id', 1)->get();
        return view('assistant.commercial.sync.index', ['syns'=>$syn, 'item'=>$commerce_items]);
    }

    public function syncPost(Request $request){
        // $json = json_decode(Syn::find(1)->details);
        // return $json->stock->availableStock;
        // return $request;
        $item = 0;
        foreach(explode(',', $request->group_id) as $synId){
            $syn = Syn::find($synId);
            if($syn->product){
                $json = json_decode($syn->details);
                if(in_array("stock", $request->field)){
                    Stock::updateOrCreate(
                        ['product_id' => $syn->product->id],
                        ['type' => "available", 'stock' => is_null($json->stock->availableStock)?0:$json->stock->availableStock, 'warehouse_id' => 1]
                    );
                    $syn->update([
                        'status' => 'import',
                        'info' => $request->field
                    ]);
                }
                if(in_array("price", $request->field)){
                    $syn->product->update([
                        'unit_price' => $json->purchasePrice
                    ]);
                    $syn->update([
                        'status' => 'import',
                        'info' => $request->field
                    ]);
                }
                if(in_array("dimensions", $request->field)){
                    // return 1;
                    Stock::updateOrCreate(
                        ['product_id' => $syn->product->id],
                        ['type' => "available", 'warehouse_id' => 1, 'length' => $json->length, 'height' => $json->height, 'width' => $json->width, 'weight' => $json->weight ]
                    );
                    $syn->update([
                        'status' => 'import',
                        'info' => $request->field
                    ]);
                }
                $item += 1;
            }
        }
        return redirect()->back()->banner(
            __($item.' total data Success Import to Master Product..!!')
        );
    }
}
