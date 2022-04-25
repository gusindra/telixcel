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
use App\Http\Controllers\ReportController;
use App\Models\ApiCredential;
use App\Models\Client;
use App\Models\Template;
use App\Models\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

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
});

Route::group(['middleware' => 'web'], function () {
    // Route::get('api/documentation', '\L5Swagger\Http\Controllers\SwaggerController@api')->name('l5swagger.api');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
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

    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::get('/settings/{page}', [SettingController::class, 'show'])->name('settings.show');

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

    Route::get('/order',  function () {
        return view('assistant.order.index');
    })->name('order');

    Route::get('/commercial', [CommercialController::class, 'index'])->name('commercial');
    Route::get('commercial/{key}', [CommercialController::class, 'show'])->name('commercial.show');

    Route::get('report', [ReportController::class, 'index'])->name('report.index');
    Route::get('report/{key}', [ReportController::class, 'show'])->name('report.show');

});


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

Route::get('/tester', function(){
    // echo $hash =  hash_hmac('sha256', 'abcde', 'abc');
    return $user = Client::where('phone', 1234543)->where('user_id', 4)->firstOr(function () {
        return Client::create([
            'phone' => 1234543,
            'user_id' => 4,
            'uuid' => Str::uuid()
        ]);
    });

    // $client = Client::firstOrNew(
    //     ['phone' =>  1234543],
    //     ['user_id' => 4]
    // );
    // $client->save();
    // return $client;
});

Route::get('/email', function (){
    Mail::raw('Text to e-mail', function($message)
    {
        $message->from('saritune@gmail.com', 'Laravel');

        $message->to('gusin44@yahoo.com')->cc('web@sbimanning.co.id');
    });
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
