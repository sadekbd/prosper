<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'username'              => ['required','string','min:3','max:50','unique:admin_users,username','regex:/^[a-zA-Z0-9_]+$/'],
            'full_name'             => ['required','string','min:2','max:100'],
            'email'                 => ['required','email:rfc,dns','max:150','unique:admin_users,email'],
            'mobile'                => ['required','string','min:7','max:20'],
            'password'              => ['required','string','min:8','confirmed'],
            'password_confirmation' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Please choose a username.',
            'username.unique'   => 'This username is already taken.',
            'username.regex'    => 'Username can only contain letters, numbers, and underscores.',
            'email.unique'      => 'This email address is already registered.',
            'password.min'      => 'Password must be at least 8 characters.',
            'password.confirmed'=> 'Passwords do not match.',
        ];
    }
}