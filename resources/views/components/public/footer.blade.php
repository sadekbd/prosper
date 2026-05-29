<footer class="bg-pm-navy text-gray-400">

  <div class="container-custom pt-20 pb-10">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">

      {{-- Brand --}}
      <div>
        <a href="{{ route('home') }}" class="flex items-center gap-3 mb-6 group">
          <div class="w-11 h-11 bg-pm-cyan rounded-xl flex items-center justify-center">
            <span class="text-white font-black text-xl font-heading">P</span>
          </div>
          <div>
            <div class="text-white font-extrabold text-lg font-heading">
              Prosper<span class="text-pm-cyan">Media</span>
            </div>
            <div class="text-pm-gold text-[10px] font-semibold tracking-widest uppercase">Be Optimistic</div>
          </div>
        </a>

        <p class="text-sm text-gray-400 leading-relaxed mb-6 max-w-xs">
          Tech-first AI automation, digital marketing and web development agency.
          Engineering your digital success with technical precision.
        </p>

        <div class="flex gap-2">
          @php
            $socials = [
              'facebook_url' => ['Facebook', '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>'],
              'linkedin_url' => ['LinkedIn', '<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>'],
              'github_url'   => ['GitHub',   '<path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>'],
              'fiverr_url'   => ['Fiverr',   '<path d="M23.004 15.588a.995.995 0 1 0 .002-1.99.995.995 0 0 0-.002 1.99zm-.996-3.705h-.85c-.546 0-.84.41-.84 1.092v2.466h-1.61v-3.558h-.684c-.547 0-.84.41-.84 1.092v2.466h-1.61v-4.874h1.61v.74c.264-.574.626-.74 1.163-.74h1.972v.74c.264-.574.625-.74 1.162-.74h.527v1.316zm-6.786 1.501h-3.359c.088.648.43.97 1.006.97.43 0 .732-.175.87-.526l1.425.4c-.351.955-1.2 1.494-2.295 1.494-1.578 0-2.573-1.024-2.573-2.574 0-1.543.98-2.575 2.525-2.575 1.47 0 2.4.97 2.4 2.575v.236zm-1.562-.88c-.04-.56-.43-.882-.946-.882-.556 0-.9.295-.984.882h1.93zm-4.17 2.822h-1.678l-.527-1.71-.527 1.71H5.1l-1.21-4.874h1.642l.567 2.256.567-2.256H8.1l.566 2.256.567-2.256H10.9l-1.21 4.874zm-7.114 0v-3.558H1.61v3.558H0V8.672h1.61v3.117c.293-.505.733-.74 1.358-.74.996 0 1.66.712 1.66 1.815v2.447H3.02v-2.135c0-.526-.215-.77-.606-.77s-.605.244-.605.77v2.135z"/>'],
            ];
          @endphp

          @foreach($socials as $key => [$name, $path])
            @if(!empty($settings[$key]))
              <a href="{{ $settings[$key] }}" target="_blank" rel="noopener noreferrer"
                 title="{{ $name }}"
                 class="w-9 h-9 bg-white/5 hover:bg-pm-cyan rounded-lg flex items-center justify-center
                        transition-all duration-200 group hover:scale-110">
                <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors"
                     fill="currentColor" viewBox="0 0 24 24">
                  {!! $path !!}
                </svg>
              </a>
            @endif
          @endforeach
        </div>
      </div>

      {{-- Quick Links --}}
      <div>
        <h4 class="text-white text-sm font-bold uppercase tracking-widest mb-6 font-heading">Quick Links</h4>
        <ul class="space-y-3">
          @foreach([['Home','home'],['About Us','about'],['Services','services'],['Portfolio','portfolio'],['Blog','blog'],['Contact','contact']] as [$lbl,$rt])
            <li>
              <a href="{{ route($rt) }}"
                 class="text-sm text-gray-400 hover:text-pm-cyan transition-colors flex items-center gap-2 group">
                <svg class="w-3 h-3 text-pm-cyan/40 group-hover:text-pm-cyan transition-colors flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                {{ $lbl }}
              </a>
            </li>
          @endforeach
        </ul>
      </div>

      {{-- Services --}}
      <div>
        <h4 class="text-white text-sm font-bold uppercase tracking-widest mb-6 font-heading">Our Services</h4>
        <ul class="space-y-3">
          @foreach([
            ['Google Ads Mastery',         'google-ads-mastery'],
            ['Conversion Tracking',        'advanced-conversion-tracking'],
            ['Web Development',            'professional-web-development'],
            ['Landing Page Optimization',  '#'],
            ['AI Automation',              '#'],
            ['Technical Consultation',     '#'],
          ] as [$lbl,$slug])
            <li>
              <a href="{{ $slug === '#' ? route('services') : route('services.show', $slug) }}"
                 class="text-sm text-gray-400 hover:text-pm-cyan transition-colors flex items-center gap-2 group">
                <svg class="w-3 h-3 text-pm-cyan/40 group-hover:text-pm-cyan transition-colors flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                {{ $lbl }}
              </a>
            </li>
          @endforeach
        </ul>
      </div>

      {{-- Contact --}}
      <div>
        <h4 class="text-white text-sm font-bold uppercase tracking-widest mb-6 font-heading">Get In Touch</h4>
        <ul class="space-y-4 mb-6">
          @if(!empty($settings['business_email']))
            <li class="flex items-start gap-3">
              <div class="w-8 h-8 bg-pm-cyan/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-pm-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
              </div>
              <div>
                <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-0.5">Email</p>
                <a href="mailto:{{ $settings['business_email'] }}"
                   class="text-sm text-gray-300 hover:text-pm-cyan transition-colors">
                  {{ $settings['business_email'] }}
                </a>
              </div>
            </li>
          @endif

          @if(!empty($settings['whatsapp_number']))
            <li class="flex items-start gap-3">
              <div class="w-8 h-8 bg-pm-cyan/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-pm-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
              </div>
              <div>
                <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-0.5">WhatsApp</p>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp_number']) }}"
                   target="_blank"
                   class="text-sm text-gray-300 hover:text-pm-cyan transition-colors">
                  {{ $settings['whatsapp_number'] }}
                </a>
              </div>
            </li>
          @endif

          @if(!empty($settings['business_address']))
            <li class="flex items-start gap-3">
              <div class="w-8 h-8 bg-pm-cyan/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-pm-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
              </div>
              <div>
                <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-0.5">Location</p>
                <p class="text-sm text-gray-300">{{ $settings['business_address'] }}</p>
              </div>
            </li>
          @endif
        </ul>

        <a href="{{ route('contact') }}" class="btn-primary w-full justify-center text-sm py-3">
          Start a Project
        </a>
      </div>

    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
      <p class="text-sm text-gray-500">
        &copy; {{ date('Y') }}
        <a href="{{ route('home') }}" class="text-white font-semibold hover:text-pm-cyan transition-colors">Prosper Media</a>.
        All rights reserved.
      </p>
      <div class="flex items-center gap-6 text-sm">
        <a href="{{ route('privacy-policy') }}" class="text-gray-500 hover:text-pm-cyan transition-colors">Privacy Policy</a>
        <a href="{{ route('contact') }}"        class="text-gray-500 hover:text-pm-cyan transition-colors">Contact</a>
        <a href="{{ route('blog') }}"           class="text-gray-500 hover:text-pm-cyan transition-colors">Blog</a>
      </div>
      <p class="text-xs text-gray-600 flex items-center gap-1">
        Built with <span class="text-pm-cyan font-medium">Technical Precision</span>
        <svg class="w-3 h-3 text-pm-gold" fill="currentColor" viewBox="0 0 20 20">
          <path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/>
        </svg>
      </p>
    </div>
  </div>
</footer>