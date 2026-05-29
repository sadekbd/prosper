@extends('layouts.auth')

@section('title', 'Set New Password')

@section('content')

  <div class="text-center mb-8">
    <div class="w-14 h-14 bg-green-500/10 border border-green-500/20 rounded-2xl
                flex items-center justify-center mx-auto mb-5">
      <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
    </div>
    <h2 class="text-2xl font-extrabold text-white font-heading">Set New Password</h2>
    <p class="text-gray-400 text-sm mt-2">Identity verified. Choose a strong new password.</p>
  </div>

  <form action="{{ route('admin.reset.post') }}" method="POST" class="space-y-5"
        x-data="{ showPass: false }">
    @csrf

    <div>
      <label class="block text-sm font-semibold text-gray-300 mb-2" for="password">New Password</label>
      <div class="relative">
        <input :type="showPass ? 'text' : 'password'"
               id="password" name="password" placeholder="Minimum 8 characters" autofocus
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-white
                      placeholder-gray-500 text-sm pr-12 focus:outline-none focus:ring-2
                      focus:ring-pm-cyan/40 focus:border-pm-cyan transition-all
                      @error('password') border-red-500/50 @enderror">
        <button type="button" @click="showPass = !showPass"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
          </svg>
        </button>
      </div>
      @error('password')
        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div>
      <label class="block text-sm font-semibold text-gray-300 mb-2" for="password_confirmation">Confirm New Password</label>
      <input :type="showPass ? 'text' : 'password'"
             id="password_confirmation" name="password_confirmation"
             placeholder="Repeat new password"
             class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-white
                    placeholder-gray-500 text-sm focus:outline-none focus:ring-2
                    focus:ring-pm-cyan/40 focus:border-pm-cyan transition-all">
    </div>

    <button type="submit" class="w-full btn-primary justify-center py-4 text-base font-bold">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
      </svg>
      Reset Password
    </button>
  </form>

@endsection