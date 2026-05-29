@extends('layouts.public')

@section('meta_title',       'Contact Prosper Media — Get a Free Digital Marketing Audit')
@section('meta_description', 'Get in touch with Prosper Media for Google Ads, conversion tracking, or web development. Request a free technical audit today.')

@section('content')

{{-- Hero --}}
<section class="bg-hero-gradient py-20 relative overflow-hidden">
  <div class="absolute inset-0 grid-overlay pointer-events-none"></div>
  <div class="container-custom relative z-10 text-center max-w-3xl mx-auto">
    <p class="section-label mb-4">Get In Touch</p>
    <h1 class="text-4xl md:text-5xl font-extrabold text-white font-heading leading-tight mb-5">
      Let's Build Something
      <span class="text-gradient"> Remarkable</span>
    </h1>
    <p class="text-gray-300 text-xl">
      Tell us about your business goals and we'll respond with a
      clear, honest technical plan within 24 hours.
    </p>
  </div>
</section>

{{-- Contact Section --}}
<section class="section-padding bg-pm-grey">
  <div class="container-custom">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 items-start">

      {{-- ── Contact Form (3/5 width) ── --}}
      <div class="lg:col-span-3">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 md:p-10">

          <h2 class="text-2xl font-extrabold text-pm-navy font-heading mb-2">Send Us a Message</h2>
          <p class="text-gray-500 text-sm mb-8">
            Fill in the form below and we'll get back to you within 24 hours.
          </p>

          <form id="contact-form"
                action="{{ route('contact.store') }}"
                method="POST"
                novalidate>
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
              {{-- Name --}}
              <div>
                <label class="form-label" for="name">
                  Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name"
                       value="{{ old('name') }}"
                       placeholder="Your full name"
                       class="input-field @error('name') input-error @enderror"
                       required>
                @error('name')
                  <p class="error-msg">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                  </p>
                @enderror
              </div>

              {{-- Email --}}
              <div>
                <label class="form-label" for="email">
                  Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       placeholder="your@email.com"
                       class="input-field @error('email') input-error @enderror"
                       required>
                @error('email')
                  <p class="error-msg">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                  </p>
                @enderror
              </div>

              {{-- Mobile --}}
              <div>
                <label class="form-label" for="mobile">Mobile / WhatsApp</label>
                <input type="tel" id="mobile" name="mobile"
                       value="{{ old('mobile') }}"
                       placeholder="+880 1700 000000"
                       class="input-field @error('mobile') input-error @enderror">
                @error('mobile')
                  <p class="error-msg">{{ $message }}</p>
                @enderror
              </div>

              {{-- Website --}}
              <div>
                <label class="form-label" for="website_url">Your Website URL</label>
                <input type="url" id="website_url" name="website_url"
                       value="{{ old('website_url') }}"
                       placeholder="https://yourwebsite.com"
                       class="input-field @error('website_url') input-error @enderror">
                @error('website_url')
                  <p class="error-msg">{{ $message }}</p>
                @enderror
              </div>
            </div>

            {{-- Service Interest --}}
            <div class="mb-5">
              <label class="form-label" for="service_interest">
                Service Interest <span class="text-red-500">*</span>
              </label>
              <select id="service_interest" name="service_interest"
                      class="input-field @error('service_interest') input-error @enderror"
                      required>
                <option value="" disabled {{ old('service_interest') ? '' : 'selected' }}>
                  — Select a service —
                </option>
                @foreach([
                  'google_ads_management'     => 'Google Ads Management',
                  'conversion_tracking'       => 'Conversion Tracking',
                  'web_development'           => 'Web Development',
                  'landing_page_optimization' => 'Landing Page Optimisation',
                  'technical_consultation'    => 'Technical Consultation',
                  'other'                     => 'Other / Not Sure Yet',
                ] as $val => $label)
                  <option value="{{ $val }}" {{ old('service_interest') === $val ? 'selected' : '' }}>
                    {{ $label }}
                  </option>
                @endforeach
              </select>
              @error('service_interest')
                <p class="error-msg">{{ $message }}</p>
              @enderror
            </div>

            {{-- Message --}}
            <div class="mb-8">
              <label class="form-label" for="message">
                Your Message <span class="text-red-500">*</span>
              </label>
              <textarea id="message" name="message" rows="5"
                        placeholder="Tell us about your business, current challenges, goals, and what you'd like help with..."
                        class="input-field resize-none @error('message') input-error @enderror"
                        required>{{ old('message') }}</textarea>
              <p class="text-xs text-gray-400 mt-1.5">Minimum 20 characters. Be as detailed as possible for a better response.</p>
              @error('message')
                <p class="error-msg">{{ $message }}</p>
              @enderror
            </div>

            {{-- Honey pot spam protection --}}
            <div class="hidden" aria-hidden="true">
              <input type="text" name="website_confirm" tabindex="-1" autocomplete="off">
            </div>

            <button type="submit" class="btn-primary w-full justify-center text-base py-4">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
              </svg>
              Send Message
            </button>

            <p class="text-xs text-gray-400 text-center mt-4">
              We typically respond within 24 hours. Your information is never shared.
            </p>
          </form>
        </div>
      </div>

      {{-- ── Contact Info (2/5 width) ── --}}
      <div class="lg:col-span-2 space-y-6">

        {{-- Info card --}}
        <div class="bg-pm-navy rounded-2xl p-8 text-white relative overflow-hidden">
          <div class="absolute inset-0 grid-overlay pointer-events-none rounded-2xl"></div>
          <div class="relative z-10">
            <div class="inline-flex items-center gap-2 badge-gold mb-6">
              <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
              Be Optimistic
            </div>
            <h3 class="text-xl font-extrabold font-heading mb-3">Contact Information</h3>
            <p class="text-gray-400 text-sm mb-8">We are here to help you grow. Reach out any time.</p>

            <div class="space-y-5">
              @if(!empty($settings['business_email']))
                <div class="flex items-start gap-4">
                  <div class="w-10 h-10 bg-pm-cyan/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-pm-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-0.5">Email</p>
                    <a href="mailto:{{ $settings['business_email'] }}"
                       class="text-white text-sm hover:text-pm-cyan transition-colors">
                      {{ $settings['business_email'] }}
                    </a>
                  </div>
                </div>
              @endif

              @if(!empty($settings['whatsapp_number']))
                <div class="flex items-start gap-4">
                  <div class="w-10 h-10 bg-pm-cyan/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-pm-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-0.5">WhatsApp</p>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp_number']) }}"
                       target="_blank"
                       class="text-white text-sm hover:text-pm-cyan transition-colors">
                      {{ $settings['whatsapp_number'] }}
                    </a>
                  </div>
                </div>
              @endif

              @if(!empty($settings['business_address']))
                <div class="flex items-start gap-4">
                  <div class="w-10 h-10 bg-pm-cyan/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-pm-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-0.5">Location</p>
                    <p class="text-white text-sm">{{ $settings['business_address'] }}</p>
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>

        {{-- Response time card --}}
        <div class="card">
          <h4 class="font-extrabold text-pm-navy font-heading mb-4 flex items-center gap-2">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
            We're Currently Available
          </h4>
          <div class="space-y-3">
            @foreach([
              ['Response Time',  '< 24 hours', 'text-green-600'],
              ['Free Audit',     'Included',   'text-pm-cyan'],
              ['Consultation',   'No Pressure', 'text-pm-gold'],
            ] as [$label, $val, $col])
              <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <span class="text-gray-500 text-sm">{{ $label }}</span>
                <span class="text-sm font-bold {{ $col }}">{{ $val }}</span>
              </div>
            @endforeach
          </div>
        </div>

        {{-- Social links --}}
        <div class="card">
          <h4 class="font-extrabold text-pm-navy font-heading mb-4">Find Us Online</h4>
          <div class="grid grid-cols-2 gap-3">
            @foreach([
              ['facebook_url', 'Facebook',  '#1877F2'],
              ['linkedin_url', 'LinkedIn',  '#0A66C2'],
              ['github_url',   'GitHub',    '#333'],
              ['fiverr_url',   'Fiverr',    '#1DBF73'],
            ] as [$key, $name, $color])
              @if(!empty($settings[$key]))
                <a href="{{ $settings[$key] }}" target="_blank" rel="noopener noreferrer"
                   class="flex items-center gap-2 p-3 bg-pm-grey rounded-xl hover:bg-pm-navy/5
                          transition-colors group text-sm">
                  <div class="w-2 h-2 rounded-full" style="background-color: {{ $color }}"></div>
                  <span class="text-pm-slate font-medium group-hover:text-pm-navy text-xs">{{ $name }}</span>
                  <svg class="w-3 h-3 text-gray-300 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                  </svg>
                </a>
              @endif
            @endforeach
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

@endsection