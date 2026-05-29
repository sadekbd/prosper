<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class ContactFormRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'min:2', 'max:100'],
            'email'            => ['required', 'email:rfc,dns', 'max:150'],
            'mobile'           => ['nullable', 'string', 'max:20'],
            'website_url'      => ['nullable', 'url', 'max:255'],
            'service_interest' => ['required', 'in:google_ads_management,conversion_tracking,web_development,landing_page_optimization,technical_consultation,other'],
            'message'          => ['required', 'string', 'min:20', 'max:3000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'Please enter your full name.',
            'email.required'            => 'A valid email address is required.',
            'email.email'               => 'Please enter a valid email address.',
            'service_interest.required' => 'Please select a service you are interested in.',
            'message.required'          => 'Please describe what you need help with.',
            'message.min'               => 'Your message must be at least 20 characters.',
        ];
    }
}