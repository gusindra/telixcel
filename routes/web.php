<?php

use App\Http\Controllers\AdminSmsController;
use App\Http\Controllers\ApiBulkSmsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DevhookController;
use App\Http\Controllers\WebhookController;
use App\Http\Livewire\ShowTemplate;
use App\Http\Livewire\Dashboard\DashboardOverview;
use App\Http\Livewire\Dashboard\CalendarView;
use App\Http\Livewire\Dashboard\GanttView;
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
use App\Http\Controllers\RecordDeleteController;
use App\Http\Controllers\RoleInvitationController;
use App\Http\Controllers\SynProductController;
use App\Http\Controllers\TeamInvitationController;
use App\Http\Controllers\TemplateController;
use App\Jobs\ProcessEmail;
use App\Models\ApiCredential;
use App\Models\BlastMessage;
use App\Models\Client;
use App\Models\Contract;
use App\Models\FlowSetting;
use App\Models\Notification;
use App\Models\OperatorPhoneNumber;
use App\Models\OrderProduct;
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
    // return view('welcome');
    if (Auth::check()) {
        return redirect()->route('dashboard');
    } else {
        return redirect()->route('login');
    }
})->name('welcome');

// Language switcher — stores chosen locale in session, applied by SetLocale middleware.
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, \App\Http\Middleware\SetLocale::SUPPORTED, true)) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

Route::group(['middleware' => 'web'], function () {
    // Route::get('api/documentation', '\L5Swagger\Http\Controllers\SwaggerController@api')->name('l5swagger.api');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if(empty(auth()->user()->currentTeam)){
            return redirect()->route('teams.create');
        }
        return view('dashboard', ['dashboard' => DashboardOverview::class]);
    })->name('dashboard');

    Route::get('/calendar', function () {
        return view('dashboard.calendar', ['calendar' => CalendarView::class]);
    })->name('calendar.view');

    Route::get('/gantt', function () {
        return view('dashboard.gantt', ['gantt' => GanttView::class]);
    })->name('gantt.view');

    Route::get('/message', function () {
        return view('message');
    })->name('message');

    Route::get('/agent-console', function () {
        return view('agent-console');
    })->name('agent');

    Route::get('/client', function () {
        return view('client');
    })->name('client');

    Route::get('/template', function () {
        return view('template.index');
    })->name('template');

    Route::get('/template/create', function () {
        return view('template.form-template');
    })->name('create.template');

    Route::get('/template/tree/view', [TemplateController::class, 'view'])->name('view.template');

    Route::get('/template/{uuid}', [TemplateController::class, 'show'])->name('show.template');
    Route::get('/template/{template}/edit', [TemplateController::class, 'edit'])->name('edit.template');

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
    Route::get('/notif-center/read/all', [NotificationController::class, 'readAll'])->name('notification.read.all');

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

    Route::delete('/records/{type}/{id}', [RecordDeleteController::class, 'destroy'])
        ->name('records.destroy');

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

    // Ticket page: shows all quotations (read-only listing).
    Route::get('/ticket', fn () => view('ticket.index'))->name('ticket');

    Route::get('report', [ReportController::class, 'index'])->name('report.index');
        Route::get('report/{key}', [ReportController::class, 'show'])->name('report.show');
        Route::get('/reports/{id}/download', [ReportController::class, 'download'])->name('reports.download');
        Route::get('/reports/{id}/view', [ReportController::class, 'view'])->name('reports.view');

    Route::get('commercial/{key}/{id}', [CommercialController::class, 'edit'])->name('commercial.edit.show');
    Route::get('commercial/{id}/{type}/print', [CommercialController::class, 'template'])->name('commercial.print');
    Route::get('product/commercial/syn', [CommercialController::class, 'sync'])->name('commercial.sync');
    Route::post('product/commercial/syn', [CommercialController::class, 'syncPost'])->name('commercial.sync.post');

    Route::get('/payment/deposit', [PaymentController::class, 'index'])->name('payment.deposit');
    Route::get('/payment/topup', [PaymentController::class, 'topup'])->name('payment.topup');
    Route::get('/payment/invoice/{id}', [PaymentController::class, 'invoice'])->name('invoice.topup');

    Route::get('/update-sms/{id}/{status}', [AdminSmsController::class, 'updateStatus'])->name('admin.update.sms.status');
});

Route::get('/role-invitations/{invitation}', [RoleInvitationController::class, 'accept'])->middleware(['signed'])->name('role-invitations.accept');
Route::get('/team/invitations/{invitation}', [TeamInvitationController::class, 'accept'])->middleware(['signed'])->name('team.invitations.accept');

Route::get('/devhook', [DevhookController::class, 'index']);

Route::post('/webhook/{slug}', [ApiWaController::class, 'inbounceMessage'])->name('webhook.client');

Route::get('/endpoint', [ApiWaController::class, 'checkEndpoint'])->name('endpoint.check');

Route::get('/test', [WebhookController::class, 'index']);

Route::get('/chat/{slug}', function ($slug) {
    return view('chat.show', ['slug'=> $slug]);
});

Route::get('/chating/{slug}', [ChatController::class, 'show'])->name('chat.slug');
Route::get('/chat-me', [ChatController::class, 'chatme'])->name('chatme');
Route::get('/upload', [UploadController::class, 'index']);
Route::get('/logout', [AuthController::class, 'destroy'])->name('logout');
Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

Route::get('cache/{id}', function ($id){
    if($id=="clear"){
        \Artisan::call('cache:clear');
    }
    if($id=="view-clear"){
        \Artisan::call('view:clear');
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
