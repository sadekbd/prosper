<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SignupRequest;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;

class SignupController extends Controller
{
    /** Maximum article writers allowed to register */
    private const MAX_WRITERS = 10;

    public function showForm()
    {
        // Check writer limit upfront — show a friendly notice
        $writerCount = AdminUser::where('role', 'article_writer')->count();
        $isFull      = $writerCount >= self::MAX_WRITERS;

        return view('admin.auth.signup', compact('isFull', 'writerCount'));
    }

    public function register(SignupRequest $request)
    {
        // ── Enforce max 10 article writer accounts ────────────
        $writerCount = AdminUser::where('role', 'article_writer')->count();

        if ($writerCount >= self::MAX_WRITERS) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Registration is currently closed. The maximum number of article writers (' . self::MAX_WRITERS . ') has been reached.');
        }

        // ── Create account (pending approval) ────────────────
        AdminUser::create([
            'username'  => $request->username,
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'mobile'    => $request->mobile,
            'password'  => Hash::make($request->password),
            'role'      => 'article_writer',   // always article_writer on signup
            'status'    => 'pending',           // must be approved by super admin
        ]);

        return redirect()->route('admin.login')
            ->with('success', 'Account created successfully! Your account is pending approval. You will be notified once activated.');
    }
}