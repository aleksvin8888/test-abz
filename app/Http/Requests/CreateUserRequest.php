<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:60',
            'email' => 'required|email:rfc,dns|max:255',
            'phone' => [
                'required',
                'regex:/^\+380\d{9}$/',
            ],
            'position_id' => 'required|exists:positions,id',
            'photo' => 'required|image|mimes:jpg,jpeg|max:5120|dimensions:min_width=70,min_height=70',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.min' => 'The name must be at least 2 characters.',
            'name.max' => 'The name may not be greater than 60 characters.',

            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than 255 characters.',

            'phone.required' => 'The phone field is required.',
            'phone.regex' => 'The phone must start with +380 and contain 9 digits after the code.',

            'position_id.required' => 'The position_id field is required.',
            'position_id.exists' => 'The selected position does not exist.',

            'photo.required' => 'The photo field is required.',
            'photo.image' => 'The photo must be an image.',
            'photo.mimes' => 'The photo must be a file of type: jpg, jpeg.',
            'photo.max' => 'The photo may not be greater than 5MB.',
            'photo.dimensions' => 'The photo must be at least 70x70 pixels in resolution.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $response = response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'fails' => $validator->errors()
        ], 422);

        throw new HttpResponseException($response);
    }

}
