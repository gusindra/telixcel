<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiWaController;

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
