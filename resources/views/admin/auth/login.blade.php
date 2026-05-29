@extends('layouts.auth')

@section('title', 'Admin Login')

@section('content')

  <div class="text-center mb-8">
    <h2 class="text-2xl font-extrabold text-white font-heading">Welcome Back</h2>
    <p class="text-gray-400 text-sm mt-2">Sign in to your admin account</p>
  </div>

  <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-5">
    @csrf

    {{-- Username or Email --}}
    <div>
      <label class="block text-sm font-semibold text-gray-300 mb-2" for="login">
        Username or Email
      </label>
      <input type="text" id="login" name="login"
             value="{{ old('login') }}"
             placeholder="Enter username or email"
             autofocus autocomplete="username"
             class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-white
                    placeholder-gray-500 text-sm focus:outline-none focus:ring-2
                    focus:ring-pm-cyan/40 focus:border-pm-cyan transition-all
                    @error('login') border-red-500/50 @enderror">
      @error('login')
        <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
          <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
          {{ $message }}
        </p>
      @enderror
    </div>

    {{-- Password --}}
    <div x-data="{ show: false }">
      <div class="flex items-center justify-between mb-2">
        <label class="text-sm font-semibold text-gray-300" for="password">Password</label>
        <a href="{{ route('admin.forgot') }}" class="text-xs text-pm-cyan hover:underline">
          Forgot password?
        </a>
      </div>
      <div class="relative">
        <input :type="show ? 'text' : 'password'"
               id="password" name="password"
               placeholder="Enter your password"
               autocomplete="current-password"
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-white
                      placeholder-gray-500 text-sm pr-12 focus:outline-none focus:ring-2
                      focus:ring-pm-cyan/40 focus:border-pm-cyan transition-all
                      @error('password') border-red-500/50 @enderror">
        <button type="button" @click="show = !show"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300">
          <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
          </svg>
          <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
          </svg>
        </button>
      </div>
      @error('password')
        <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
      @enderror
    </div>

    {{-- Remember me --}}
    <label class="flex items-center gap-3 cursor-pointer">
      <input type="checkbox" name="remember" value="1"
             class="w-4 h-4 rounded border-white/20 bg-white/5 text-pm-cyan
                    focus:ring-pm-cyan/40 focus:ring-2">
      <span class="text-gray-400 text-sm">Keep me signed in</span>
    </label>

    {{-- Submit --}}
    <button type="submit"
            class="w-full btn-primary justify-center py-4 text-base font-bold">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
      </svg>
      Sign In to Admin Panel
    </button>
  </form>

  <div class="mt-6 pt-6 border-t border-white/10 text-center">
    <p class="text-gray-500 text-xs">
      Need article writer access?
      <a href="{{ route('admin.signup') }}" class="text-pm-cyan hover:underline font-medium">
        Request an account
      </a>
    </p>
  </div>

@endsection