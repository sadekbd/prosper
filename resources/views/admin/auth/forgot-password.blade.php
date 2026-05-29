@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')

  <div class="text-center mb-8">
    <div class="w-14 h-14 bg-pm-gold/10 border border-pm-gold/20 rounded-2xl
                flex items-center justify-center mx-auto mb-5">
      <svg class="w-7 h-7 text-pm-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
      </svg>
    </div>
    <h2 class="text-2xl font-extrabold text-white font-heading">Forgot Password</h2>
    <p class="text-gray-400 text-sm mt-2">Verify your identity to reset your password</p>
  </div>

  <div class="bg-pm-gold/5 border border-pm-gold/15 rounded-xl p-4 mb-6">
    <p class="text-pm-gold text-xs font-semibold mb-1">Security Verification</p>
    <p class="text-gray-400 text-xs leading-relaxed">
      Enter the exact username, email, and mobile number registered on your account.
      All three must match to proceed.
    </p>
  </div>

  <form action="{{ route('admin.forgot.post') }}" method="POST" class="space-y-4">
    @csrf

    {{-- Username --}}
    <div>
      <label class="block text-sm font-semibold text-gray-300 mb-2" for="username">Username</label>
      <input type="text" id="username" name="username" value="{{ old('username') }}"
             placeholder="Your exact username" autofocus
             class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-white
                    placeholder-gray-500 text-sm focus:outline-none focus:ring-2
                    focus:ring-pm-cyan/40 focus:border-pm-cyan transition-all
                    @error('username') border-red-500/50 @enderror">
      @error('username')
        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Email --}}
    <div>
      <label class="block text-sm font-semibold text-gray-300 mb-2" for="email">Email Address</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}"
             placeholder="Your registered email"
             class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-white
                    placeholder-gray-500 text-sm focus:outline-none focus:ring-2
                    focus:ring-pm-cyan/40 focus:border-pm-cyan transition-all
                    @error('email') border-red-500/50 @enderror">
      @error('email')
        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Mobile --}}
    <div>
      <label class="block text-sm font-semibold text-gray-300 mb-2" for="mobile">Mobile Number</label>
      <input type="tel" id="mobile" name="mobile" value="{{ old('mobile') }}"
             placeholder="Your registered mobile number"
             class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-white
                    placeholder-gray-500 text-sm focus:outline-none focus:ring-2
                    focus:ring-pm-cyan/40 focus:border-pm-cyan transition-all
                    @error('mobile') border-red-500/50 @enderror">
      @error('mobile')
        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
      @enderror
    </div>

    <button type="submit" class="w-full btn-primary justify-center py-4 text-base font-bold mt-2">
      Verify & Continue
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
      </svg>
    </button>
  </form>

  <div class="mt-6 pt-5 border-t border-white/10 text-center">
    <a href="{{ route('admin.login') }}" class="text-gray-500 text-xs hover:text-pm-cyan transition-colors flex items-center justify-center gap-1">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
      </svg>
      Back to Login
    </a>
  </div>

@endsection