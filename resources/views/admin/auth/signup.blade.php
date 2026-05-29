@extends('layouts.auth')

@section('title', 'Request Access')

@section('content')

  <div class="text-center mb-8">
    <h2 class="text-2xl font-extrabold text-white font-heading">Request Article Access</h2>
    <p class="text-gray-400 text-sm mt-2">Register as an article writer — subject to admin approval</p>
  </div>

  {{-- Writer limit notice --}}
  @if($isFull)
    <div class="mb-6 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl p-5 text-sm text-center">
      <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>
      <p class="font-bold mb-1">Registration Closed</p>
      <p class="text-xs opacity-80">Maximum limit of {{ config('prosper.max_writers', 10) }} article writers reached. Contact the administrator.</p>
    </div>
  @else
    <div class="mb-6 bg-pm-cyan/10 border border-pm-cyan/20 rounded-xl px-4 py-3 flex items-start gap-3">
      <svg class="w-4 h-4 text-pm-cyan mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
      </svg>
      <div>
        <p class="text-pm-cyan text-xs font-semibold">{{ 10 - $writerCount }} spots remaining</p>
        <p class="text-gray-400 text-xs mt-0.5">Your account will need approval before you can log in.</p>
      </div>
    </div>
  @endif

  @if(!$isFull)
  <form action="{{ route('admin.signup.post') }}" method="POST" class="space-y-4">
    @csrf

    <div class="grid grid-cols-2 gap-4">
      {{-- Username --}}
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5" for="username">Username *</label>
        <input type="text" id="username" name="username" value="{{ old('username') }}"
               placeholder="john_doe" autocomplete="username"
               class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-3 text-white
                      placeholder-gray-500 text-sm focus:outline-none focus:ring-2
                      focus:ring-pm-cyan/40 focus:border-pm-cyan transition-all
                      @error('username') border-red-500/50 @enderror">
        @error('username')
          <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Full Name --}}
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5" for="full_name">Full Name *</label>
        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}"
               placeholder="John Doe"
               class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-3 text-white
                      placeholder-gray-500 text-sm focus:outline-none focus:ring-2
                      focus:ring-pm-cyan/40 focus:border-pm-cyan transition-all
                      @error('full_name') border-red-500/50 @enderror">
        @error('full_name')
          <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p>
        @enderror
      </div>
    </div>

    {{-- Email --}}
    <div>
      <label class="block text-xs font-semibold text-gray-300 mb-1.5" for="email">Email Address *</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}"
             placeholder="you@email.com" autocomplete="email"
             class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-3 text-white
                    placeholder-gray-500 text-sm focus:outline-none focus:ring-2
                    focus:ring-pm-cyan/40 focus:border-pm-cyan transition-all
                    @error('email') border-red-500/50 @enderror">
      @error('email')
        <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Mobile --}}
    <div>
      <label class="block text-xs font-semibold text-gray-300 mb-1.5" for="mobile">Mobile Number *</label>
      <input type="tel" id="mobile" name="mobile" value="{{ old('mobile') }}"
             placeholder="+880 1700 000000"
             class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-3 text-white
                    placeholder-gray-500 text-sm focus:outline-none focus:ring-2
                    focus:ring-pm-cyan/40 focus:border-pm-cyan transition-all
                    @error('mobile') border-red-500/50 @enderror">
      @error('mobile')
        <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
      {{-- Password --}}
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5" for="password">Password *</label>
        <input type="password" id="password" name="password" placeholder="Min 8 chars"
               class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-3 text-white
                      placeholder-gray-500 text-sm focus:outline-none focus:ring-2
                      focus:ring-pm-cyan/40 focus:border-pm-cyan transition-all
                      @error('password') border-red-500/50 @enderror">
        @error('password')
          <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Confirm --}}
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5" for="password_confirmation">Confirm *</label>
        <input type="password" id="password_confirmation" name="password_confirmation"
               placeholder="Repeat password"
               class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-3 text-white
                      placeholder-gray-500 text-sm focus:outline-none focus:ring-2
                      focus:ring-pm-cyan/40 focus:border-pm-cyan transition-all">
      </div>
    </div>

    <button type="submit" class="w-full btn-primary justify-center py-3.5 text-sm font-bold mt-2">
      Submit Registration Request
    </button>
  </form>
  @endif

  <div class="mt-6 pt-5 border-t border-white/10 text-center">
    <p class="text-gray-500 text-xs">
      Already have an account?
      <a href="{{ route('admin.login') }}" class="text-pm-cyan hover:underline font-medium">Sign in</a>
    </p>
  </div>

@endsection