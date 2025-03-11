<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubscribeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

   /**
     * Define validation rules.
     */
    public function rules(): array
    {
        return [
            'email' => 'bail|required|email:rfc,dns|unique:subscribers,email',
            'percent' => 'bail|required|numeric',
            'period' => 'bail|required|in:1,6,24'
        ];
    }

    /**
     * Custom error messages (Optional)
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email is required.',
            'email.email' => 'Provide a valid email address.',
            'email.unique' => 'This email is already subscribed.',
            'percent.required' => 'Percent value is required.',
            'percent.numeric' => 'Percent should be a number.',
            'period.required' => 'Period is required.',
            'period.in' => 'Period must be 1, 6, or 24 hours.',
        ];
    }

    /**
     * Handle validation failure and return a custom JSON response.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['error' => $validator->errors()->first()], 400)
        );
    }
}
