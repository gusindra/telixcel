<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiWaController;
use App\Http\Controllers\TestApiController;
use App\Http\Controllers\ApiTeamWaController;
use App\Http\Controllers\ApiBulkSmsController;
use App\Http\Controllers\ApiChatController;
use App\Http\Controllers\ApiSmsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/wa/{messege}',  [ApiWaController::class, 'show']);
Route::post('/wa',  [ApiWaController::class, 'retriveNewMessage']);
Route::post('/webhook/{slug}',  [ApiWaController::class, 'inbounceMessage'])->name('api.client.webhook');
// Route::get('/chat/{phone}',  [TestApiController::class, 'get']);

Route::middleware(['auth:sanctum'])->group(function () {
    // sample key : kHy717zKGKN9Xwt1GdD14JryEBsLFApJSEiG1Gmy = telixcel
    // sample 1 : QxYBf46DyeSSsuXKf6tWWpd0rZBVT1a8dFeFSOyM = gusin
    // Route::get('/test',  [TestApiController::class, 'get']);
    Route::post('/test',  [TestApiController::class, 'post']);
    Route::post('/bulksms',  [ApiBulkSmsController::class, 'post']);

    // API for chat
    Route::get('/getmsg/{id}',  [TestApiController::class, 'get']);
    Route::post('/sendmsg/{id}',  [TestApiController::class, 'post']);

    // API for chat
    Route::get('/chat/{phone}',  [ApiChatController::class, 'show']);
    Route::post('/chat',  [ApiChatController::class, 'post']);
    Route::post('/chat/bulk',  [ApiChatController::class, 'post']);
    // API for sms
    Route::get('/sms',  [ApiSmsController::class, 'index']);
    Route::get('/sms/{phone}',  [ApiSmsController::class, 'show']);
    Route::post('/sms',  [ApiSmsController::class, 'post']);
    Route::post('/sms/bulk',  [ApiSmsController::class, 'sendBulk']);

});

Route::get('/test',  [TestApiController::class, 'get']);
Route::get('/test/{id}',  [TestApiController::class, 'show']);
Route::get('/send/smsbulk',  [TestApiController::class, 'smsbulk']);
Route::put('/test/{id}',  [TestApiController::class, 'put']);

Route::get('/team-auth',  [ApiTeamWaController::class, 'getAuth']);
Route::post('/post-team-auth',  [ApiTeamWaController::class, 'postTeamAuth'])->name('wa.session');
Route::get('/test/{id}',  [ApiTeamWaController::class, 'getTeam']);
Route::put('/team-auth/{id}',  [ApiTeamWaController::class, 'put']);

Route::get('/receive-sms-status',  [ApiBulkSmsController::class, 'status']);
