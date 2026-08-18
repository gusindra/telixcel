<?php

namespace App\Http\Controllers\Api\Ai;

use App\Exceptions\AiGatewayException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ai\ChatCompletionRequest;
use App\Models\AiApplication;
use App\Models\AiModel;
use App\Services\Ai\AiGatewayService;
use App\Services\Ai\AiModelService;
use App\Services\Ai\AiPermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiGatewayController extends Controller
{
    public function models(Request $request, AiPermissionService $permissions, AiModelService $modelService)
    {
        /** @var AiApplication $application */
        $application = $request->attributes->get('ai_application');
        $requestId = (string) $request->attributes->get('ai_request_id');

        $models = $modelService->publicCatalog($permissions->allowedModelIds($application));

        return response()->json([
            'object' => 'list',
            'data' => $models->map(fn (AiModel $model) => [
                'id' => $model->model_identifier,
                'object' => 'model',
                'name' => $model->publicName(),
                'owned_by' => $model->sourceLabel(),
            ])->values(),
            'default' => optional($models->first())->model_identifier,
        ])->header('X-Request-ID', $requestId);
    }

    public function completions(ChatCompletionRequest $request, AiGatewayService $gateway)
    {
        $requestId = (string) $request->attributes->get('ai_request_id');

        try {
            return $gateway->handle($request);
        } catch (AiGatewayException $e) {
            $e->requestId = $e->requestId ?: $requestId;

            return $e->toResponse();
        } catch (\Throwable $e) {
            Log::error('AI gateway internal error', [
                'request_id' => $requestId,
            ]);

            return AiGatewayException::internal($requestId)->toResponse();
        }
    }
}
