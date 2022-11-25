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
        if($key=='quotation'){
            $data = Quotation::find($id);
            if($data){
                return view('assistant.commercial.quotation.show', ['code'=>$id, 'quote' => $data]);
            }
        }elseif($key=='contract'){
            $data = Contract::find($id);
            if($data){
                return view('assistant.commercial.contract.show', ['code'=>$id, 'contract' => $data]);
            }
        }
        $data = CommerceItem::find($id);
        if($data){
            return view('assistant.commercial.show', ['code'=>$id, 'data' => $data]);
        }
        abort(404);

    }

    public function template($key, $id){
        // return $key;
        if($id=='quotation'){
            $q = Quotation::find($key);
            return view('assistant.commercial.quotation.template', ['data' => $q]);
        }elseif($id=='contract'){
            $c = Contract::find($key);
            return view('assistant.commercial.contract.template', ['code'=>$c]);
        }elseif($id=='invoice'){
            $o = Order::find($key);
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
