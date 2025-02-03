<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class DecodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // no user to authorize so return true
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => [
                'required', 
                'string',
                function ($attribute, $value, $fail) {
                    if (!Cache::has($value)) {
                        $fail('code not found');
                    }
                }
            ]
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * If the validation fails due to a "Short URL not found" error, 
     * a 404 status code is returned. Otherwise, a 400 status code is returned.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $isValidUrl = false;
        
        foreach ($errors->messages() as $messages) {
            foreach ($messages as $message) {
                if (str_contains($message, 'code not found')) {
                    $isValidUrl = true;
                    break 2;
                }
            }
        }

        $status = $isValidUrl ? Response::HTTP_NOT_FOUND : Response::HTTP_BAD_REQUEST;

        throw new HttpResponseException(
            response()->json([
                'error' => $errors->first()
            ], $status)
        );
    }
}
