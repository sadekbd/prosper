<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showForm()
    {
        return view('admin.auth.forgot-password');
    }

    /** Step 1 — Verify identity (username + email + mobile) */
    public function verify(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'email'    => ['required', 'email'],
            'mobile'   => ['required', 'string'],
        ]);

        // Find user where all three fields match
        $user = AdminUser::where('username', $request->username)
                         ->where('email',    $request->email)
                         ->where('mobile',   $request->mobile)
                         ->first();

        if (!$user) {
            return back()
                ->withInput($request->only('username', 'email'))
                ->with('error', 'No account found matching those details. Please check and try again.');
        }

        // Delete any existing reset tokens for this user
        DB::table('admin_password_resets')->where('user_id', $user->id)->delete();

        // Generate a secure reset token (hashed in DB)
        $plainToken = Str::random(64);
        DB::table('admin_password_resets')->insert([
            'user_id'    => $user->id,
            'token'      => Hash::make($plainToken),
            'expires_at' => now()->addMinutes(30),
            'used'       => false,
            'created_at' => now(),
        ]);

        // Pass token via session (not URL — more secure)
        session(['pwd_reset_user_id' => $user->id, 'pwd_reset_token' => $plainToken]);

        return redirect()->route('admin.reset.form')
            ->with('success', 'Identity verified. Please set your new password below.');
    }

    /** Step 2 — Show new password form */
    public function showResetForm(Request $request)
    {
        if (!session('pwd_reset_user_id') || !session('pwd_reset_token')) {
            return redirect()->route('admin.forgot')
                ->with('error', 'Session expired. Please start the password reset process again.');
        }

        return view('admin.auth.reset-password');
    }

    /** Step 3 — Save new password */
    public function reset(Request $request)
    {
        $request->validate([
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        $userId     = session('pwd_reset_user_id');
        $plainToken = session('pwd_reset_token');

        if (!$userId || !$plainToken) {
            return redirect()->route('admin.forgot')
                ->with('error', 'Session expired. Please start over.');
        }

        // Find valid, unused token for this user
        $resetRecord = DB::table('admin_password_resets')
            ->where('user_id', $userId)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$resetRecord || !Hash::check($plainToken, $resetRecord->token)) {
            return redirect()->route('admin.forgot')
                ->with('error', 'Reset token is invalid or has expired. Please start over.');
        }

        // Update password
        AdminUser::where('id', $userId)->update([
            'password' => Hash::make($request->password),
        ]);

        // Mark token as used
        DB::table('admin_password_resets')
            ->where('id', $resetRecord->id)
            ->update(['used' => true]);

        // Clear session
        session()->forget(['pwd_reset_user_id', 'pwd_reset_token']);

        return redirect()->route('admin.login')
            ->with('success', 'Password reset successfully. Please log in with your new password.');
    }
}