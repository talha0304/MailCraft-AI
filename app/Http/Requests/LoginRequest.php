<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email:strict,dns,spoof', // Strict email validation with DNS and spoofing checks
                'regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix', // Regex for valid email format
                'exists:users,email', // Ensure the email exists in the `users` table
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'min:12', // Increased minimum password length
                'max:64', // Added maximum password length
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/', // Strong password rules
            ]
        ];
    }



    public function attributes()
    {
        return [
            'email' => 'Email',
            'password' => 'Password',
        ];
    }

    public function messages()
    {
        return [
            '*.required' => ':attribute field cannot be left empty.',
            'email.email' => 'Please enter a valid :attribute address.',
            'email.exists' => 'The provided :attribute does not exist in our records.',
            'password.min' => 'The :attribute must be at least :min characters long.',
            'password.max' => 'The :attribute must not exceed :max characters.',
            'password.regex' => 'The :attribute must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ];
    }

}

// 'email' => ['required', 'string', 'email', 'regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix', 'max:255', 'unique:users'],