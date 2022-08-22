<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DevhookController;
use App\Http\Controllers\WebhookController;
use App\Http\Livewire\ShowTemplate;
use App\Http\Controllers\ApiWaController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserBillingController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\CommercialController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\FlowController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleInvitationController;
use App\Jobs\ProcessEmail;
use App\Models\ApiCredential;
use App\Models\BlastMessage;
use App\Models\Client;
use App\Models\FlowSetting;
use App\Models\Notification;
use App\Models\Template;
use App\Models\Request;
use App\Models\SaldoUser;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::group(['middleware' => 'web'], function () {
    // Route::get('api/documentation', '\L5Swagger\Http\Controllers\SwaggerController@api')->name('l5swagger.api');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if(empty(auth()->user()->currentTeam)){
            return redirect()->route('teams.create');
        }
        return view('dashboard');
    })->name('dashboard');

    Route::get('/message', function () {
        return view('message');
    })->name('message');

    Route::get('/client', function () {
        return view('client');
    })->name('client');

    Route::get('/template', function () {
        return view('template.index');
    })->name('template');

    Route::get('/template/create', function () {
        return view('template.form-template');
    })->name('create.template');

    Route::get('/template/{uuid}', function ($uuid) {
        return view('template.show', ['uuid'=> $uuid]);
    })->name('show.template');

    // Route::get('/template/{uuid}', ShowTemplate::class);

    Route::get('/billing', function () {
        return view('billing');
    })->name('billing');

    // Route::put('/agent', ShowTemplate::class)->name('current-agent.update');

    // Route::get('/notif-center', function () {
    //     return view('notification');
    // })->name('notification');

    Route::get('/notif-center', [NotificationController::class, 'index'])->name('notification');
    Route::get('/notif-center/{notification}', [NotificationController::class, 'show'])->name('notification.read');

    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/{user}', [UserController::class, 'show'])->name('user.show');
    Route::get('/user/{user}/balance', [UserController::class, 'balance'])->name('user.show.balance');
    Route::get('/user/{user}/profile', [UserController::class, 'profile'])->name('user.show.profile');
    Route::get('/user-billing', [UserBillingController::class, 'index'])->name('user.billing.index');
    Route::get('/user-billing/generate', [UserBillingController::class, 'generate'])->name('user.billing.generate');
    // Route::get('/user-billing/create', [UserBillingController::class, 'create'])->name('user.billing.create');
    // Route::get('/user-billing/{user}', [UserBillingController::class, 'show'])->name('user.billing.user');
    // Route::get('/user-billing/{user}/{billing}', [UserBillingController::class, 'create'])->name('user.billing.show');
    Route::post('/user-billing/invoice', [UserBillingController::class, 'invoice'])->name('user.billing.create.invoice');
    Route::get('/invoice/{billing}', [UserBillingController::class, 'showInvoice'])->name('user.billing.invoice.show');
    Route::put('/invoice/{billing}', [UserBillingController::class, 'updateInvoice'])->name('user.billing.update.invoice');

    Route::get('/roles', [RoleController::class, 'index'])->name('role.index');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->name('role.show');

    Route::get('/permission', function () {
        return view('permission.index', ['page'=>'permission']);
    })->name('permission.index');
    Route::get('/flow/{model}', [FlowController::class, 'show'])->name('flow.show');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::get('/settings/{page}', [SettingController::class, 'show'])->name('settings.show');

    Route::get('/company/{company}', [SettingController::class, 'company'])->name('settings.company.show');

    Route::get('/assistant',  function () {
        return view('assistant.index');
    })->name('assistant');

    // Route::get('/project',  function () {
    //     return view('assistant.project.index');
    // })->name('project');
    // Route::get('/project/{project}',  function () {
    //     return view('assistant.project.show');
    // })->name('project.show');

    Route::get('/project', [ProjectController::class, 'index'])->name('project');
    Route::get('/project/{project}', [ProjectController::class, 'show'])->name('project.show');

    Route::get('/order', [OrderController::class, 'index'])->name('order');
    Route::get('/order/{order}', [OrderController::class, 'show'])->name('show.order');
    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice');
    Route::get('/invoice-order/{invoice}', [InvoiceController::class, 'show'])->name('show.invoice');
    Route::get('/commission', [CommissionController::class, 'index'])->name('commission');
    Route::get('/commission/{commission}', [CommissionController::class, 'show'])->name('show.commission');

    // Route::get('/order/{order}',  function ($i) {
    //     return $i;
    // });

    // Route::get('/order/{uuid}', function ($uuid) {
    //     return view('assistant.order.show', ['uuid'=> $uuid]);
    // })->name('show.order');

    Route::get('/commercial', [CommercialController::class, 'index'])->name('commercial');
    Route::get('commercial/{key}', [CommercialController::class, 'show'])->name('commercial.show');

    Route::get('report', [ReportController::class, 'index'])->name('report.index');
    Route::get('report/{key}', [ReportController::class, 'show'])->name('report.show');

    Route::get('commercial/{key}/{id}', [CommercialController::class, 'edit'])->name('commercial.edit.show');
    Route::get('commercial/{id}/{type}/print', [CommercialController::class, 'template'])->name('commercial.print');

    Route::get('/payment/deposit', [PaymentController::class, 'index'])->name('payment.deposit');
    Route::get('/payment/topup', [PaymentController::class, 'topup'])->name('payment.topup');
    Route::get('/payment/invoice/{id}', [PaymentController::class, 'invoice'])->name('invoice.topup');
});

Route::get('/role-invitations/{invitation}', [RoleInvitationController::class, 'accept'])->middleware(['signed'])->name('role-invitations.accept');

Route::get('/devhook', [DevhookController::class, 'index']);

Route::post('/webhook/{slug}', [ApiWaController::class, 'inbounceMessage'])->name('webhook.client');

Route::get('/endpoint', [ApiWaController::class, 'checkEndpoint'])->name('endpoint.check');

Route::get('/test', [WebhookController::class, 'index']);

Route::get('/chat/{slug}', function ($slug) {
    return view('chat.show', ['slug'=> $slug]);
});

Route::get('/chating/{slug}', [ChatController::class, 'show'])->name('chat.slug');
Route::get('/upload', [UploadController::class, 'index']);
Route::get('/logout', [AuthController::class, 'destroy'])->name('logout');
Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

Route::get('cache/{id}', function ($id){
    if($id=="clear"){
        \Artisan::call('cache:clear');
    }
    dd("Job is done");
});

Route::get('queue/{id}', function ($id) {
    if($id=="work"){
        \Artisan::call('queue:work --tries=3 --stop-when-empty --timeout=60');
    }elseif($id=="restart"){
        \Artisan::call('queue:restart');
    }elseif($id=='json'){
        $path = storage_path() . "/csvjson.json";
        $path = public_path() . "/csvjson.json";
        $content = json_decode(file_get_contents($path), true);
        try {
            foreach($content as $sms){
                $msg_id = preg_replace('/\s+/', '', $sms['Message ID']);
                $msisdn = preg_replace('/\s+/', '', $sms['Send to']);
                $user_id = 16;
                // return $sms['Date/Time'];
                // return $sms['From'];
                // return $sms['Send to'];
                // return $sms['Message Title'];
                // return $sms['Message Content'];
                // return $sms['Message Status'];
                $myDate = $sms['Date/Time'];
                $smsDate = Carbon::createFromFormat('d/m/Y H:i', $myDate)->format('Y-m-d H:i');
                $client = Client::where('phone', $msisdn)->where('user_id', $user_id)->firstOr(function () use ($msisdn, $user_id) {
                    return Client::create([
                        'phone' => $msisdn,
                        'user_id' => $user_id,
                        'uuid' => Str::uuid()
                    ]);
                });
                $modelData = [
                    'msg_id'    => $msg_id,
                    'user_id'   => $user_id,
                    'client_id' => $client->uuid,
                    'sender_id' => $sms['From'],
                    'type'      => '0',
                    'status'    => $sms['Message Status'],
                    'code'      => '200',
                    'message_content'  => $sms['Message Content'],
                    'currency'  => 'IDR',
                    'price'     => 500,
                    'balance'   => 0,
                    'msisdn'    => $msisdn,
                    'created_by'=> $date,
                    'updated_by'=> $date,
                ];
                $blast = BlastMessage::create($modelData);

                $blast->created_at = $smsDate;
                $blast->updated_at = $smsDate;
                $blast->save();
            }
        } catch (\Throwable $th) {
            dd($th);
        }

    }
    dd("Job is done");
});

Route::get('/restart-service', function(){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);

    $header[0] = "Authorization: whm $user:$token";
    curl_setopt($curl,CURLOPT_HTTPHEADER,$header);
    curl_setopt($curl, CURLOPT_URL, $query);

    $result = curl_exec($curl);

    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ($http_status != 200) {
        echo "[!] Error: " . $http_status . " returned\n";
    } else {
        $json = json_decode($result);
        echo "[+] Current cPanel users on the system:\n";
            echo "\t" . $result . "\n";
    }

    curl_close($curl);
    return 'success';
});

// TESTING
Route::get('/testing', function(){
    return base64_encode('SITC01'.':'.'92f70cad-1fa4-40de-bbd8-39dbfd6a7242');
    // $request = Request::find(244);
    // return $request->client->team->detail;
    // return $userCredention->team->detail;

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://pickup.sicepat.com:8087/api/partner/requestpickuppackage',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "auth_key": "C70575F6ADB1457DBBB0AE825FC04542",
            "reference_number": "SICEPAT-TEST-SCPT123",
            "pickup_request_date": "2021-01-01 09:00",
            "pickup_merchant_name": "Telixmart",
            "pickup_method": "PICKUP",
            "pickup_address": "Jalan Daan Mogot II No. 100, M-NN No.RT.6, RT.6/RW.5, Duri Kepa, Kec. Kb. Jeruk, Kota Jakarta Barat, Daerah Khusus Ibukota Jakarta 11510",
            "pickup_city": "KOTA JAKARTA BARAT",
            "pickup_merchant_phone": "02150200050",
            "pickup_merchant_email": "support@jarvis-store.com",
            "PackageList": [
                {
                    "receipt_number": "999888777111",
                    "origin_code": "CGK",
                    "delivery_type": "BEST",
                    "parcel_category": "Clothing",
                    "parcel_content": "Kaos Katun Polos",
                    "parcel_qty": 2,
                    "parcel_uom": "Pcs",
                    "parcel_value": 199000,
                    "total_weight": 0.6,
                    "shipper_name": "Sicepat Telixmart",
                    "shipper_address": "Jalan Daan Mogot II No. 100, M-NN No.RT.6, RT.6/RW.5, Duri Kepa, Kec. Kb. Jeruk, Kota Jakarta Barat, Daerah Khusus Ibukota Jakarta 1510",
                    "shipper_province": "DKI JAKARTA",
                    "shipper_city": "KOTA JAKARTA BARAT",
                    "shipper_district": "KEBON JERUK",
                    "shipper_zip": "11510",
                    "shipper_phone": "02150200050",
                    "shipper_longitude": "-6.155960",
                    "shipper_latitude": "106.708860",
                    "recipient_title": "Mrs",
                    "recipient_name": "Ratna Galih",
                    "recipient_address": "testing tanpa lang & lat recipient",
                    "recipient_province": "JAWA TENGAH",
                    "recipient_city": "KAB. BANYUMAS",
                    "recipient_district": "PURWOKERTO BARAT",
                    "recipient_zip": "53132",
                    "recipient_phone": "087888888888",
                    "destination_code": "SRG10424"
                }
            ]
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;

});

Route::get('/tester', function(HttpRequest $request){
    // return auth()->user()->super->first()->role;
    return $flow = FlowSetting::where('model', 'QUOTATION')->where('team_id', auth()->user()->currentTeam->id)->get();
    // return $request;
    $url = $request->url;
    $secretkey = $request->key;
    $accesskey = 'd20254aee13cc156';
    $POSTFIELDS = array();

    $varian = array(
        "averageCostPrice" => array(
            "amount" => 0,
            "currencyCode" => "IDR"
        ),
        "boundVariationCount" => 0,
        "images" => [],
        "optionValues" => ["-"],
        "sellingPrice" => array(
            "amount" => 10,
            "currencyCode" => "IDR"
        ),
        "sku" => "0125001-001",
        "status" => "ACTIVE",
        "stock" => array(
            "availableStock" => 10,
            "safetyAlert" => false,
            "safetyStock" => 0
        ),
        "bundleVariations" => array(
            array(
                "quantity" => 1,
                "bundleVariationId" => "MV111100000222220"
            )
        ),
        "type" => "BUNDLE"
    );

    if($request->format == 'add_product'){
        $POSTFIELDS = array(
            'id' => null,
            'brand' => "",
            'type' => "NORMAL",
            'variantOptions' => [],
            'name' => "Test Produk Lagi",
            'saleStatus' => "FOR_SALE",
            'condition' => "NEW",
            'minPurchase' => 10,
            'shortDescription' => "Test Produk Lagi",
            'description' => "Test HTMl Produk Lagi",
            "variations" => array(array(
                "sellingPrice" => array(
                    "amount" => 20000,
                    "currencyCode" => "IDR"
                ),
                "sku" => "2022007-001",
                "stock" => array(
                    "availableStock" => 10,
                    "safetyAlert" => false,
                    "safetyStock" => 0
                ),
                "status" => "ACTIVE",
                "type" => "NORMAL",
                "purchasePrice" => array(
                    "amount" => 20000,
                    "currencyCode" => "IDR"
                ),
            )),
            "images" => [],
            "status" => "PENDING_REVIEW"

        );
        // $POSTFIELDS = '{
        //     "id": null,
        //     "brand": "",
        //     "type": "BUNDLE",
        //     "variantOptions": [],
        //     "name": "Test 0125001",
        //     "fullCategoryId": ["100534", "100577"],
        //     "saleStatus": "FOR_SALE",
        //     "condition": "NEW",
        //     "minPurchase": 10,
        //     "shortDescription": "Test",
        //     "description": "<p>Test</p>",
        //     "extraInfo": {
        //         "preOrder": {
        //             "settingType": "PRODUCT_OFF",
        //             "timeUnit": "DAY"
        //         }
        //     },
        //     "variations": [{
        //         "averageCostPrice": {
        //             "amount": 0,
        //             "currencyCode": "IDR"
        //         },
        //         "boundVariationCount": 0,
        //         "images": [],
        //         "optionValues": ["-"],
        //         "sellingPrice": {
        //             "amount": 10,
        //             "currencyCode": "IDR"
        //         },
        //         "sku": "0125001-001",
        //         "status": "ACTIVE",
        //         "stock": {
        //             "availableStock": 10,
        //             "safetyAlert": false,
        //             "safetyStock": 0
        //         },
        //         "bundleVariations": [{
        //                 "quantity": 1,
        //                 "bundleVariationId": "MV111100000222220"
        //         }],
        //         "type": "BUNDLE"
        //     }],
        //     "images": [],
        //     "delivery": {
        //         "lengthUnit": "cm",
        //         "weightUnit": "g"
        //     },
        //     "costInfo": {
        //         "purchasingTimeUnit": "HOUR",
        //         "salesTax": {
        //             "currencyCode": "IDR"
        //         }
        //     },
        //     "status": "PENDING_REVIEW"
        // }';
        $POSTFIELDS = json_encode($POSTFIELDS);
    }elseif($request->format == 'show_variation'){
        $POSTFIELDS = array(
            'masterVariationIds' => array("MV62C51CC789701100017EA5A6"),
            'page' => 0,
            'size' => 5
        );
        $POSTFIELDS = json_encode($POSTFIELDS);
    }else{
        if($request->has('post')){
            foreach(explode(",", $request->post) as $key => $posts){
                $post = explode(":", $posts);
                if(is_numeric($post[1])){
                    $POSTFIELDS[$post[0]] = (int)$post[1] ;
                }else{
                    $POSTFIELDS[$post[0]] = $post[1];
                }
                $POSTFIELDS[$post[0]] = is_numeric($post[1]) ? (int)$post[1] : $post[1];
            }
        }
        $POSTFIELDS = json_encode($POSTFIELDS);
    }

    $sign_ginee = Http::get('http://jarvis1.pythonanywhere.com/welcome/default/signature_genie?url='.$url.'&key='.$secretkey);
    $signature = $sign_ginee['signature'];
    $signature = $accesskey.':'.$sign_ginee['signature'];

    $ginee_url = 'https://genie-sandbox.advai.net';
    $method = $request->has('method') ? $request->method : 'GET';

    // echo $hash =  hash_hmac('sha256', 'abcde', 'abc');
    // return $user = Client::where('phone', 1234543)->where('user_id', 4)->firstOr(function () {
    //     return Client::create([
    //         'phone' => 1234543,
    //         'user_id' => 4,
    //         'uuid' => Str::uuid()
    //     ]);
    // });

    // $client = Client::firstOrNew(
    //     ['phone' =>  1234543],
    //     ['user_id' => 4]
    // );
    // $client->save();
    // return $client;

    $headers = array(
        'X-Advai-Country: ID',
        'Authorization: '.$signature,
        'Content-Type: application/json'
    );

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $ginee_url.$url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_POSTFIELDS =>$POSTFIELDS,
        CURLOPT_HTTPHEADER => $headers,
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    //MAKE CURL
    echo 'curl -X '.$method.' \<br>';
    foreach($headers as $k => $head){
        echo '-H '.$head.' \<br>';
    }
    echo '-d '.$POSTFIELDS.' \<br>';
    echo '"'.$ginee_url.$url.'" <br>';
    //END CURL
    echo '<br><br>';
    // echo $signature.'<br>';
    // echo $url.'<br><br>';
    echo $response;

    // curl_setopt($ch, CURLOPT_URL, 'https://genie-sandbox.advai.net/openapi/shop/v1/list');
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt($ch, CURLOPT_POST, 1);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n    \"page\":0,\n    \"size\":10,\n}");

    // $headers = array();
    // $headers[] = 'X-Advai-Country: ID';
    // $headers[] = 'Authorization: b3436f168a4402b7:VHlSfqFIY3gCMKWO6BKkmn7VmBKPF+KUCk/9o+gbURE=';
    // $headers[] = 'Content-Type: application/json';
    // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Generated @ codebeautify.org

    // curl_setopt($ch, CURLOPT_URL, 'https://genie-sandbox.advai.net/openapi/shop/v1/categories/list');
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


    // $headers = array();
    // $headers[] = 'X-Advai-Country: ID';
    // $headers[] = 'Authorization: b3436f168a4402b7:zOuRJ7s/pbNPl8AjCe7R2Wm2+uBIHPKxEak1LrXQTHI=';
    // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);



    // echo $result = curl_exec($ch);
    // if (curl_errno($ch)) {
    //     echo 'Error:' . curl_error($ch);
    // }
    // curl_close($ch);
});

Route::get('/email', function (){
    // $request = SaldoUser::create([
    //     'currency'      => 'idr',
    //     'amount'        => 100,
    //     'mutation'      => 'credit',
    //     'description'   => 'TEST Pemotongan sms / delete notif',
    //     'user_id'       => '18'
    // ]);
    // $notif = 0;
    // // if($request->mutation == 'debit'){
    // //     $notif_count = Notification::where('model', 'Balance')->where('user_id', $request->user_id)->count();
    // //     if(($notif_count==1 && $request->balance <= 50000) || ($notif_count==0 && $request->balance <= 100000)){
    // //         $notif = Notification::create([
    // //             'type'          => 'email',
    // //             'model_id'      => $request->id,
    // //             'model'         => 'Balance',
    // //             'notification'  => 'Balance Alert. Your current balance remaining Rp'.number_format($request->balance) ,
    // //             'user_id'       => $request->user_id,
    // //             'status'        => 'unread',
    // //         ]);

    // //         if($notif){
    // //             ProcessEmail::dispatch($request, 'alert_balance');
    // //         }
    // //     }
    // // }

    // if($request->mutation == 'credit'){
    //     $notif = Notification::where('type', 'email')->where('model', 'Balance')->where('user_id', $request->user_id)->delete();
    // }

    dd($notif, $request);

    // return $notif;
    // Mail::raw('Text to e-mail', function($message)
    // {
    //     $message->from('saritune@gmail.com', 'Laravel');

    //     $message->to('gusin44@yahoo.com')->cc('web@sbimanning.co.id');
    // });
});

// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//     return view('dashboard');
// })->name('dashboard');
// Route::middleware(['auth:sanctum', 'verified'])->get('/client', function () {
//     return view('client');
// })->name('client');
// Route::middleware(['auth:sanctum', 'verified'])->get('/message', function () {
//     return view('message');
// })->name('message');
// Route::middleware(['auth:sanctum', 'verified'])->get('/template', function () {
//     return view('template');
// })->name('template');
// Route::middleware(['auth:sanctum', 'verified'])->get('/billing', function () {
//     return view('billing');
// })->name('billing');
