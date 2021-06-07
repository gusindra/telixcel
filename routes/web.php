<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DevhookController;
use App\Http\Controllers\WebhookController;
use App\Http\Livewire\ShowTemplate;
use App\Http\Controllers\ApiWaController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AuthController;
use App\Models\Template;
use App\Models\Request;

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


});


Route::get('/devhook', [DevhookController::class, 'index']);

Route::get('/webhook/{slug}', [ApiWaController::class, 'index'])->name('webhook.client');

Route::get('/endpoint', [ApiWaController::class, 'checkEndpoint'])->name('endpoint.check');

Route::get('/test', [WebhookController::class, 'index']);

Route::get('/chat/{slug}', function ($slug) {
    return view('chat.show', ['slug'=> $slug]);
});

Route::get('/chating/{slug}', [ChatController::class, 'show'])->name('chat.slug');

Route::get('/logout', [AuthController::class, 'destroy'])->name('logout');
Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

Route::get('/testing', function(){
    $request = Request::find(191);
    return $request->team->id;
});
