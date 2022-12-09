<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Syn;
use Illuminate\Http\Request;

class SynProductController extends Controller
{
    public function index(Request $request)
    {
        if($request->has('test')){
            //dummy json
            $url = 'https://gist.githubusercontent.com/gusindra/7b4823799d901c17d34ce51489e22db0/raw/b6e2197134572995d3537c4b53c1a59f02e98c25/gistfile1.json';
            $response = '';

            $response = Http::get($url, [
                'detail' => 1,
            ]);

            $json = json_encode($response);

            //write json to file
            file_put_contents(public_path()."/data.json", $json);
            // return "save file";
            foreach ($response["data"]["content"] as $content){
                // return $content["inventoryId"];
                //check client && array
                foreach ($content["variationBriefs"] as $variant){
                    $modelData = [
                        'source_id' => $variant["id"],
                        'source'    => 'Inventory Ginee',
                        'sku'       => $variant["sku"],
                        'name'      => $content["name"],
                        'user_id'   => 1,
                        'details'   => json_encode($variant),
                    ];
                    if($data = Syn::where('source_id', $variant["id"])->where('sku', $variant["sku"])->first()){
                        $data->details = json_encode($content);
                        $data->status = 'new';
                        $data->save();
                    }else{
                        Syn::create($modelData);
                    }
                }
            }
            return "success";
        }else{
            // production
            $request_host = 'https://genie-sandbox.advai.net';
            $http_method = 'POST';

            $access_key = 'd20254aee13cc156';
            $secret_key = 'b3436f168a4402b7';

            $request_uri = '/openapi/inventory/v1/sku/list';
            $param_json = '{"page":0,"size":50}';

            $newline = '$';
            $sign_str = $http_method . $newline . $request_uri . $newline;
            $authorization = sprintf('%s:%s', $access_key, base64_encode(hash_hmac('sha256', $sign_str, $secret_key, TRUE)));
            // echo sprintf('signature string is:%s', $sign_str . PHP_EOL);


            $header_array = array(
                'Authorization: ' . $authorization,
                'Content-Type: ' . 'application/json',
                'X-Advai-Country: ' . 'ID'
            );

            // var_dump($header_array);

            $http_header = array(
                'http' => array('method' => $http_method, 'header' => $header_array, 'content' => $param_json)
            );

            $context = stream_context_create($http_header);
            $response = file_get_contents($request_host . $request_uri, false, $context, 0);

            $response = json_decode($response);
            // return $response->code;
            // Log::debug($response);

            if($response->code!='SUCCESS'){
                return $msg = "Missing parameter or invalid field type";
            }elseif($response->code=='SUCCESS'){
                $array_res = [];

                foreach ($response->data->content as $content){
                    // return $content->inventoryId;
                    //check client && array
                    $modelData = [
                        'source_id' => $content->inventoryId,
                        'source'    => 'Inventory Ginee',
                        'sku'       => $content->inventorySku,
                        'name'      => $content->inventoryName,
                        'user_id'   => 1,
                        'details'   => json_encode($content),
                    ];
                    if($data = Syn::where('source_id', $content->inventoryId)->where('sku', $content->inventorySku)->first()){
                        $data->details = json_encode($content);
                        $data->status = 'new';
                        $data->save();
                    }else{
                        Syn::create($modelData);
                    }
                }
            }else{
                return $msg = "Error";
            }
            return redirect('product/commercial/syn')->banner(
                __('Success get data from Ginee.')
            );
        }
    }
}
