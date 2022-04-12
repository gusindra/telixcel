<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiWaController;
use App\Http\Controllers\TestApiController;
use App\Http\Controllers\ApiTeamWaController;
use App\Http\Controllers\ApiBulkSmsController;

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

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/test',  [TestApiController::class, 'get']);
    Route::post('/test',  [TestApiController::class, 'post']);
    Route::post('/bulksms',  [ApiBulkSmsController::class, 'post']);
});

Route::get('/test/{id}',  [TestApiController::class, 'show']);
Route::get('/send/smsbulk',  [TestApiController::class, 'smsbulk']);
Route::put('/test/{id}',  [TestApiController::class, 'put']);

Route::get('/team-auth',  [ApiTeamWaController::class, 'getAuth']);
Route::post('/post-team-auth',  [ApiTeamWaController::class, 'postTeamAuth'])->name('wa.session');
Route::get('/test/{id}',  [ApiTeamWaController::class, 'getTeam']);
Route::put('/team-auth/{id}',  [ApiTeamWaController::class, 'put']);
