<?php

namespace App\Http\Requests\Ai;

use App\Exceptions\AiGatewayException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ChatCompletionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'model' => 'required|string|max:191',
            'messages' => 'required|array|min:1|max:40',
            'messages.*.role' => 'required|string|in:system,user,assistant',
            'messages.*.content' => 'required',
            'stream' => 'sometimes|boolean',
            'temperature' => 'sometimes|numeric|min:0|max:2',
            'top_p' => 'sometimes|numeric|min:0|max:1',
            'max_tokens' => 'sometimes|integer|min:1|max:8192',
            'max_completion_tokens' => 'sometimes|integer|min:1|max:8192',
            'stop' => 'sometimes|nullable',
            'presence_penalty' => 'sometimes|numeric|min:-2|max:2',
            'frequency_penalty' => 'sometimes|numeric|min:-2|max:2',
            'response_format' => 'sometimes|array',
            'response_format.type' => 'required_with:response_format|string|in:text,json_object',
            'user' => 'sometimes|string|max:191',
            'metadata' => 'sometimes|array',
            'metadata.end_user_id' => 'sometimes|string|max:191',
            'metadata.end_user_name' => 'sometimes|string|max:191',
            'metadata.end_user_email' => 'sometimes|string|max:191',
            'metadata.feature' => 'sometimes|string|max:191',
            'metadata.session_id' => 'sometimes|string|max:191',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $requestId = $this->attributes->get('ai_request_id');

        throw AiGatewayException::validation($validator->errors()->first(), $requestId);
    }
}
