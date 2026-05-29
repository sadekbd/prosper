@extends('layouts.public')

@section('meta_title',       'Privacy Policy — Prosper Media')
@section('meta_description', 'How Prosper Media collects, uses and protects your personal data. Our commitment to transparency.')

@section('content')

{{-- Hero --}}
<section class="bg-hero-gradient py-20 relative overflow-hidden">
  <div class="absolute inset-0 grid-overlay pointer-events-none"></div>
  <div class="container-custom relative z-10 max-w-3xl">
    <p class="section-label mb-4">Legal</p>
    <h1 class="text-4xl md:text-5xl font-extrabold text-white font-heading mb-4">Privacy Policy</h1>
    <p class="text-gray-400 text-sm">
      Last updated: {{ date('F j, Y') }} &nbsp;·&nbsp;
      Effective: January 1, 2025
    </p>
  </div>
</section>

{{-- Content --}}
<section class="section-padding bg-white">
  <div class="container-custom">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-16">

      {{-- Table of Contents --}}
      <aside class="lg:col-span-1">
        <div class="sticky top-28 bg-pm-grey rounded-2xl p-6">
          <h3 class="text-pm-navy font-extrabold font-heading mb-4 text-sm uppercase tracking-wider">Contents</h3>
          <nav class="space-y-2 text-sm">
            @foreach([
              ['#overview',     'Overview'],
              ['#data-collect', 'Data We Collect'],
              ['#why-collect',  'Why We Collect It'],
              ['#how-use',      'How We Use It'],
              ['#third-party',  'Third-Party Tools'],
              ['#cookies',      'Cookies'],
              ['#your-rights',  'Your Rights'],
              ['#security',     'Security'],
              ['#updates',      'Policy Updates'],
              ['#contact',      'Contact Us'],
            ] as [$href, $label])
              <a href="{{ $href }}"
                 class="block text-gray-500 hover:text-pm-cyan transition-colors py-1 border-l-2
                        border-transparent hover:border-pm-cyan pl-3">
                {{ $label }}
              </a>
            @endforeach
          </nav>
        </div>
      </aside>

      {{-- Policy Content --}}
      <div class="lg:col-span-3 prose prose-slate max-w-none
                  prose-headings:font-heading prose-headings:text-pm-navy
                  prose-a:text-pm-cyan prose-a:no-underline hover:prose-a:underline">

        {{-- Human note --}}
        <div id="overview" class="bg-pm-cyan/8 border border-pm-cyan/20 rounded-2xl p-6 mb-10 not-prose">
          <div class="flex gap-4">
            <div class="w-10 h-10 bg-pm-cyan/15 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-pm-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div>
              <h4 class="font-extrabold text-pm-navy font-heading mb-2">A Human Note</h4>
              <p class="text-gray-600 text-sm leading-relaxed">
                We are a small technical agency. We collect only what we need to run our business and serve you well.
                We do not sell your data, we do not spam you, and we are always happy to answer questions about how
                your information is used. If something is unclear, just email us.
              </p>
            </div>
          </div>
        </div>

        <h2 id="data-collect">1. Data We Collect</h2>
        <p>We collect information in the following ways:</p>

        <h3>Information You Provide Directly</h3>
        <ul>
          <li><strong>Contact Form:</strong> Name, email address, mobile number, website URL, service interest, and your message.</li>
          <li><strong>Newsletter Signup:</strong> Email address and optionally your name.</li>
          <li><strong>Client Engagements:</strong> Any information you share during project discussions (business goals, current challenges, account access credentials for platforms we manage on your behalf).</li>
        </ul>

        <h3>Information Collected Automatically</h3>
        <ul>
          <li><strong>Usage Data:</strong> Pages visited, time on site, referral source, browser type, and device information.</li>
          <li><strong>IP Address:</strong> Logged for security and spam prevention purposes.</li>
          <li><strong>Cookies:</strong> Session cookies for site functionality. See the <a href="#cookies">Cookies</a> section below.</li>
        </ul>

        <h2 id="why-collect">2. Why We Collect Data</h2>
        <p>We collect data for specific, legitimate purposes:</p>
        <ul>
          <li>To respond to your enquiries and project requests</li>
          <li>To deliver the services you have engaged us to provide</li>
          <li>To send newsletters and technical guides to subscribers who opted in</li>
          <li>To improve the performance and user experience of this website</li>
          <li>To detect and prevent spam, fraud, or abuse</li>
          <li>To comply with legal and regulatory requirements</li>
        </ul>

        <h2 id="how-use">3. How We Use Your Information</h2>
        <p>Your information is used exclusively by Prosper Media and is never sold, rented, or traded to third parties for marketing purposes.</p>

        <p>Specific uses include:</p>
        <ul>
          <li><strong>Contact form submissions</strong> — reviewed by our team to understand your needs and formulate a response within 24 hours.</li>
          <li><strong>Newsletter subscriptions</strong> — used to deliver educational content you opted in to receive. You can unsubscribe at any time.</li>
          <li><strong>Project client data</strong> — used solely to perform the contracted services. Retained only for the duration of the engagement plus any statutory retention periods.</li>
        </ul>

        <h2 id="third-party">4. Third-Party Tools</h2>
        <p>We use reputable third-party services to operate this website and our business. Each operates under their own privacy policy:</p>

        <div class="not-prose overflow-x-auto">
          <table class="w-full text-sm border border-gray-100 rounded-xl overflow-hidden">
            <thead class="bg-pm-grey">
              <tr>
                <th class="text-left p-4 font-semibold text-pm-navy">Tool</th>
                <th class="text-left p-4 font-semibold text-pm-navy">Purpose</th>
                <th class="text-left p-4 font-semibold text-pm-navy">Data Shared</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @foreach([
                ['Google Analytics 4', 'Website analytics',        'Anonymous usage data'],
                ['Google Tag Manager', 'Tag management',            'Configured event data'],
                ['Cloudflare',         'CDN & security',            'IP address, request data'],
                ['SMTP Mail Server',   'Email delivery',            'Email address, message content'],
              ] as [$tool, $purpose, $data])
                <tr class="hover:bg-pm-grey/50">
                  <td class="p-4 font-medium text-pm-navy">{{ $tool }}</td>
                  <td class="p-4 text-gray-600">{{ $purpose }}</td>
                  <td class="p-4 text-gray-600">{{ $data }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <h2 id="cookies">5. Cookies</h2>
        <p>This website uses minimal cookies:</p>
        <ul>
          <li><strong>Session Cookie:</strong> A temporary cookie that expires when you close your browser. Required for form submissions (CSRF protection).</li>
          <li><strong>Analytics Cookies:</strong> Google Analytics cookies to understand aggregate website traffic. No personally identifiable information is stored.</li>
        </ul>
        <p>You can disable cookies in your browser settings. This may affect form functionality on the site.</p>

        <h2 id="your-rights">6. Your Rights (GDPR)</h2>
        <p>If you are located in the European Economic Area (EEA) or the United Kingdom, you have the following rights:</p>
        <ul>
          <li><strong>Right to Access:</strong> Request a copy of all personal data we hold about you.</li>
          <li><strong>Right to Rectification:</strong> Request correction of inaccurate data.</li>
          <li><strong>Right to Erasure:</strong> Request deletion of your personal data ("right to be forgotten").</li>
          <li><strong>Right to Restrict Processing:</strong> Request that we limit how we use your data.</li>
          <li><strong>Right to Object:</strong> Object to our processing of your data for direct marketing purposes.</li>
          <li><strong>Right to Data Portability:</strong> Request your data in a structured, machine-readable format.</li>
        </ul>
        <p>To exercise any of these rights, email us at
          <a href="mailto:{{ $settings['business_email'] ?? 'hello@prospermedia.com' }}">
            {{ $settings['business_email'] ?? 'hello@prospermedia.com' }}
          </a>.
          We will respond within 30 days.
        </p>

        <h2 id="security">7. Security</h2>
        <p>We take data security seriously:</p>
        <ul>
          <li>All passwords are hashed using bcrypt — we never store plain-text passwords</li>
          <li>This website is served over HTTPS with SSL/TLS encryption</li>
          <li>All database connections use encrypted channels</li>
          <li>Admin panel access is restricted by role-based authentication</li>
          <li>Form submissions include CSRF protection</li>
          <li>Server access is restricted by IP allowlisting and SSH key authentication</li>
        </ul>
        <p>No method of electronic storage or transmission is 100% secure. While we use commercially reasonable means to protect your data, we cannot guarantee absolute security.</p>

        <h2 id="updates">8. Policy Updates</h2>
        <p>We may update this Privacy Policy from time to time to reflect changes in our practices, technology, or legal requirements. When we make material changes, we will update the "Last updated" date at the top of this page.</p>
        <p>We encourage you to review this page periodically.</p>

        <h2 id="contact">9. Contact Us</h2>
        <p>If you have any questions about this Privacy Policy or how we handle your data, please contact us:</p>

        <div class="not-prose bg-pm-grey rounded-2xl p-6">
          <p class="font-extrabold text-pm-navy font-heading mb-3">Prosper Media</p>
          <div class="space-y-2 text-sm text-gray-600">
            @if(!empty($settings['business_email']))
              <p>Email: <a href="mailto:{{ $settings['business_email'] }}" class="text-pm-cyan hover:underline">{{ $settings['business_email'] }}</a></p>
            @endif
            @if(!empty($settings['business_address']))
              <p>Address: {{ $settings['business_address'] }}</p>
            @endif
          </div>
          <div class="mt-5">
            <a href="{{ route('contact') }}" class="btn-primary text-sm">Send Us a Message</a>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

@endsection