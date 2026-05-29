<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('admin.auth.login');
    }

    public function login(LoginRequest $request)
    {
        // ── Rate limiting — max 5 attempts per minute ──────────
        $key = 'admin-login:' . Str::lower($request->input('login')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()
                ->withInput($request->only('login'))
                ->with('error', "Too many login attempts. Please try again in {$seconds} seconds.");
        }

        // ── Find user by username OR email ─────────────────────
        $loginInput = $request->input('login');
        $user = AdminUser::where('username', $loginInput)
                         ->orWhere('email', $loginInput)
                         ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60);
            return back()
                ->withInput($request->only('login'))
                ->with('error', 'Invalid credentials. Please check your username and password.');
        }

        // ── Check account status ──────────────────────────────
        if ($user->status === 'pending') {
            return back()
                ->withInput($request->only('login'))
                ->with('error', 'Your account is pending approval. Contact the administrator.');
        }

        if ($user->status === 'blocked') {
            return back()
                ->withInput($request->only('login'))
                ->with('error', 'Your account has been suspended. Contact support.');
        }

        // ── Authenticate ──────────────────────────────────────
        RateLimiter::clear($key);
        auth('admin')->login($user, $request->boolean('remember'));

        // Update last login info
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Welcome back, ' . $user->full_name . '!');
    }

    public function logout(Request $request)
    {
        auth('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login')
            ->with('success', 'You have been logged out successfully.');
    }
}