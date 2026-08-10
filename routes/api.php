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

/*
|--------------------------------------------------------------------------
| AI agent data API — 1 endpoint
|--------------------------------------------------------------------------
|
| POST /api/agent
| Auth: X-Agent-Token + X-Agent-User-Id (middleware agent.api)
|
| Body examples:
|   { "action": "health" }
|   { "action": "schema" }
|   { "action": "query", "model": "task", "filters": [], "limit": 20 }
|   { "action": "execute", "tool": "query_records", "arguments": { ... } }
|   { "tool": "query_records", "arguments": { "model": "task" } }  // shorthand execute
*/
Route::post('/agent', \App\Http\Controllers\AgentApiController::class)
    ->middleware(['agent.api', 'throttle:60,1']);
