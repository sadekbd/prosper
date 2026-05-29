<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\ContactFormRequest;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('public.contact');
    }

    public function store(ContactFormRequest $request)
    {
        // Save message to database
        ContactMessage::create([
            'name'             => $request->name,
            'email'            => $request->email,
            'mobile'           => $request->mobile,
            'website_url'      => $request->website_url,
            'service_interest' => $request->service_interest,
            'message'          => $request->message,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'status'           => 'new',
        ]);

        return redirect()->route('contact')
            ->with('success', 'Thank you! Your message has been sent. We will be in touch within 24 hours.');
    }
}