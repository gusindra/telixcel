<?php

use App\Http\Controllers\Api\Ai\AiGatewayController;
use Illuminate\Support\Facades\Route;

Route::get('/models', [AiGatewayController::class, 'models'])
    ->name('ai.models');

Route::post('/chat/completions', [AiGatewayController::class, 'completions'])
    ->name('ai.chat.completions');
