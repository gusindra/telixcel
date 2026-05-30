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
