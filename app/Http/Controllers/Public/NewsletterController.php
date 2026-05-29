<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc,dns', 'max:150'],
            'name'  => ['nullable', 'string', 'max:100'],
        ]);

        // Check if already subscribed
        $existing = NewsletterSubscriber::where('email', $validated['email'])->first();

        if ($existing) {
            if ($existing->status === 'unsubscribed') {
                $existing->update(['status' => 'active', 'unsubscribed_at' => null]);
                $message = 'Welcome back! You have been re-subscribed.';
            } else {
                $message = 'You are already subscribed. Thank you!';
            }
        } else {
            NewsletterSubscriber::create([
                'email'         => $validated['email'],
                'name'          => $validated['name'] ?? null,
                'status'        => 'active',
                'ip_address'    => $request->ip(),
                'subscribed_at' => now(),
            ]);
            $message = 'Thank you for subscribing! You will receive our latest guides and tips.';
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('success', $message);
    }
}