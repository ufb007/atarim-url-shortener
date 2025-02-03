<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EncodeRequest extends FormRequest
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
            'url' => 'required|url',
        ];
    }

    /**
     * Get the validation messages for the request.
     *
     * @return array<string, string> The validation messages.
     */
    public function messages(): array
    {
        return [
            'url.required' => 'URL is required',
            'url.url' => 'URL is invalid',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * If the validation fails due to an invalid URL, a 404 status code is returned.
     * Otherwise, a 400 status code is returned.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     **/
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $isValidUrl = false;
        
        foreach ($errors->messages() as $messages) {
            foreach ($messages as $message) {
                if (str_contains($message, 'URL is invalid')) {
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
