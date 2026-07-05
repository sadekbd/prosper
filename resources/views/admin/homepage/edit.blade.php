@extends('layouts.admin')
@section('title','Homepage')
@section('page_title','Homepage Content')
@section('page_subtitle','Edit fixed homepage sections without changing domain records')

@section('content')

@php
  $tabs = [
    'hero' => 'Hero',
    'stats' => 'Stats',
    'trust_bar' => 'Trust Bar',
    'difference' => 'Difference',
    'services_intro' => 'Services',
    'portfolio_intro' => 'Portfolio',
    'blog_intro' => 'Blog',
    'primary_cta' => 'Final CTA',
  ];
  $firstError = collect($errors->keys())->first();
  $initialTab = $firstError ? explode('.', $firstError)[0] : 'hero';
  $activeTab = array_key_exists($initialTab, $tabs) ? $initialTab : 'hero';
  $inputClass = 'w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors';
  $textareaClass = 'w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm resize-y focus:outline-none focus:border-pm-cyan transition-colors';
  $selectClass = 'w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors';
  $labelClass = 'block text-xs text-gray-400 mb-2';
  $helpClass = 'text-gray-500 text-xs mt-1';
@endphp

<div x-data="{ tab: @js($activeTab) }" class="space-y-6">
  <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
    <div>
      <p class="text-gray-400 text-sm">These fields edit only the eight fixed homepage <code class="text-pm-cyan">page_sections</code> records.</p>
      <p class="text-gray-500 text-xs mt-1">Service, portfolio, and blog cards remain managed in their own modules.</p>
    </div>
    <a href="{{ route('home') }}" target="_blank" rel="noopener"
       class="btn-secondary text-sm self-start">
      Preview Homepage
    </a>
  </div>

  <form action="{{ route('admin.homepage.update') }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-2 overflow-x-auto">
      <div class="flex gap-2 min-w-max">
        @foreach($tabs as $key => $label)
          @php $hasError = collect($errors->keys())->contains(fn ($errorKey) => str_starts_with($errorKey, $key.'.') || $errorKey === $key); @endphp
          <button type="button"
                  @click="tab = '{{ $key }}'"
                  :class="tab === '{{ $key }}' ? 'bg-pm-cyan/15 text-pm-cyan' : 'text-gray-400 hover:text-white hover:bg-white/5'"
                  class="px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
            {{ $label }}
            @if($hasError)
              <span class="w-2 h-2 rounded-full bg-red-400"></span>
            @endif
          </button>
        @endforeach
      </div>
    </div>

    <section x-show="tab === 'hero'" x-cloak class="bg-[#1a2540] border border-white/5 rounded-2xl p-6 space-y-6">
      <div>
        <h3 class="text-white font-bold text-lg">Hero</h3>
        <p class="{{ $helpClass }}">Title lines preserve the current highlighted line break structure.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label class="{{ $labelClass }}">Eyebrow *</label>
          <input name="hero[eyebrow]" value="{{ old('hero.eyebrow', data_get($form, 'hero.eyebrow')) }}" class="{{ $inputClass }}">
          @error('hero.eyebrow') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="{{ $labelClass }}">Primary CTA Label *</label>
          <input name="hero[primary_label]" value="{{ old('hero.primary_label', data_get($form, 'hero.primary_label')) }}" class="{{ $inputClass }}">
          @error('hero.primary_label') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @for($i = 0; $i < 4; $i++)
          <div>
            <label class="{{ $labelClass }}">Title Line {{ $i + 1 }} *</label>
            <input name="hero[title_lines][{{ $i }}]" value="{{ old("hero.title_lines.$i", data_get($form, "hero.title_lines.$i")) }}" class="{{ $inputClass }}">
            @error("hero.title_lines.$i") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
          </div>
        @endfor
      </div>

      <div>
        <label class="{{ $labelClass }}">Subtitle *</label>
        <textarea name="hero[subtitle]" rows="3" class="{{ $textareaClass }}">{{ old('hero.subtitle', data_get($form, 'hero.subtitle')) }}</textarea>
        @error('hero.subtitle') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label class="{{ $labelClass }}">Primary CTA Route</label>
          <select name="hero[primary_route]" class="{{ $selectClass }}">
            @foreach($routeOptions as $route => $label)
              <option value="{{ $route }}" @selected(old('hero.primary_route', data_get($form, 'hero.primary_route')) === $route)>{{ $label }}</option>
            @endforeach
          </select>
          <p class="{{ $helpClass }}">Known routes are stored as safe homepage-compatible paths.</p>
          @error('hero.primary_route') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="{{ $labelClass }}">Primary CTA URL</label>
          <input name="hero[primary_url]" value="{{ old('hero.primary_url', data_get($form, 'hero.primary_url')) }}" class="{{ $inputClass }}">
          @error('hero.primary_url') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      <div class="border-t border-white/5 pt-5 grid grid-cols-1 md:grid-cols-3 gap-5">
        <div>
          <label class="{{ $labelClass }}">Secondary Label *</label>
          <input name="hero[secondary_button][label]" value="{{ old('hero.secondary_button.label', data_get($form, 'hero.secondary_button.label')) }}" class="{{ $inputClass }}">
          @error('hero.secondary_button.label') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="{{ $labelClass }}">Secondary Route</label>
          <select name="hero[secondary_button][route]" class="{{ $selectClass }}">
            @foreach($routeOptions as $route => $label)
              <option value="{{ $route }}" @selected(old('hero.secondary_button.route', data_get($form, 'hero.secondary_button.route')) === $route)>{{ $label }}</option>
            @endforeach
          </select>
          @error('hero.secondary_button.route') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="{{ $labelClass }}">Secondary URL</label>
          <input name="hero[secondary_button][url]" value="{{ old('hero.secondary_button.url', data_get($form, 'hero.secondary_button.url')) }}" class="{{ $inputClass }}">
          @error('hero.secondary_button.url') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      <div class="border-t border-white/5 pt-5 space-y-5">
        <h4 class="text-white font-semibold">Dashboard Mockup</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
          @foreach(['eyebrow' => 'Dashboard Eyebrow', 'title' => 'Dashboard Title', 'status' => 'Dashboard Status'] as $field => $label)
            <div>
              <label class="{{ $labelClass }}">{{ $label }} *</label>
              <input name="hero[dashboard][{{ $field }}]" value="{{ old("hero.dashboard.$field", data_get($form, "hero.dashboard.$field")) }}" class="{{ $inputClass }}">
              @error("hero.dashboard.$field") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
          @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
          @for($i = 0; $i < 3; $i++)
            <div class="bg-white/5 border border-white/5 rounded-xl p-4 space-y-3">
              <p class="text-white font-semibold text-sm">Metric {{ $i + 1 }}</p>
              @foreach(['label' => 'Label', 'value' => 'Value', 'change' => 'Change'] as $field => $label)
                <div>
                  <label class="{{ $labelClass }}">{{ $label }} *</label>
                  <input name="hero[dashboard][metrics][{{ $i }}][{{ $field }}]" value="{{ old("hero.dashboard.metrics.$i.$field", data_get($form, "hero.dashboard.metrics.$i.$field")) }}" class="{{ $inputClass }}">
                  @error("hero.dashboard.metrics.$i.$field") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
              @endforeach
              <div>
                <label class="{{ $labelClass }}">Color *</label>
                <select name="hero[dashboard][metrics][{{ $i }}][color]" class="{{ $selectClass }}">
                  @foreach($metricColors as $color)
                    <option value="{{ $color }}" @selected(old("hero.dashboard.metrics.$i.color", data_get($form, "hero.dashboard.metrics.$i.color")) === $color)>{{ $color }}</option>
                  @endforeach
                </select>
                @error("hero.dashboard.metrics.$i.color") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
              </div>
            </div>
          @endfor
        </div>

        <div>
          <label class="{{ $labelClass }}">Chart Label *</label>
          <input name="hero[dashboard][chart][label]" value="{{ old('hero.dashboard.chart.label', data_get($form, 'hero.dashboard.chart.label')) }}" class="{{ $inputClass }}">
          @error('hero.dashboard.chart.label') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 md:grid-cols-7 gap-3">
          @for($i = 0; $i < 7; $i++)
            <div>
              <label class="{{ $labelClass }}">Day {{ $i + 1 }}</label>
              <input name="hero[dashboard][chart][days][{{ $i }}]" value="{{ old("hero.dashboard.chart.days.$i", data_get($form, "hero.dashboard.chart.days.$i")) }}" class="{{ $inputClass }}">
              @error("hero.dashboard.chart.days.$i") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
              <label class="{{ $labelClass }}">Height {{ $i + 1 }}</label>
              <input type="number" min="0" max="100" name="hero[dashboard][chart][bar_heights][{{ $i }}]" value="{{ old("hero.dashboard.chart.bar_heights.$i", data_get($form, "hero.dashboard.chart.bar_heights.$i")) }}" class="{{ $inputClass }}">
              @error("hero.dashboard.chart.bar_heights.$i") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
          @endfor
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
          <div>
            <label class="{{ $labelClass }}">Tracking Label</label>
            <input name="hero[dashboard][tracking][label]" value="{{ old('hero.dashboard.tracking.label', data_get($form, 'hero.dashboard.tracking.label')) }}" class="{{ $inputClass }}">
            @error('hero.dashboard.tracking.label') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
          </div>
          @for($i = 0; $i < 4; $i++)
            <div>
              <label class="{{ $labelClass }}">Tracking Item {{ $i + 1 }}</label>
              <input name="hero[dashboard][tracking][items][{{ $i }}]" value="{{ old("hero.dashboard.tracking.items.$i", data_get($form, "hero.dashboard.tracking.items.$i")) }}" class="{{ $inputClass }}">
              @error("hero.dashboard.tracking.items.$i") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
          @endfor
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          @for($i = 0; $i < 2; $i++)
            <div class="bg-white/5 border border-white/5 rounded-xl p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
              <div>
                <label class="{{ $labelClass }}">Badge {{ $i + 1 }} Label</label>
                <input name="hero[dashboard][floating_badges][{{ $i }}][label]" value="{{ old("hero.dashboard.floating_badges.$i.label", data_get($form, "hero.dashboard.floating_badges.$i.label")) }}" class="{{ $inputClass }}">
                @error("hero.dashboard.floating_badges.$i.label") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
              </div>
              <div>
                <label class="{{ $labelClass }}">Badge {{ $i + 1 }} Value</label>
                <input name="hero[dashboard][floating_badges][{{ $i }}][value]" value="{{ old("hero.dashboard.floating_badges.$i.value", data_get($form, "hero.dashboard.floating_badges.$i.value")) }}" class="{{ $inputClass }}">
                @error("hero.dashboard.floating_badges.$i.value") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
              </div>
            </div>
          @endfor
        </div>
      </div>
    </section>

    <section x-show="tab === 'stats'" x-cloak class="bg-[#1a2540] border border-white/5 rounded-2xl p-6 space-y-5">
      <h3 class="text-white font-bold text-lg">Stats</h3>
      <p class="{{ $helpClass }}">Exactly four stats are rendered. Editors cannot add, remove, or reorder rows.</p>
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        @for($i = 0; $i < 4; $i++)
          <div class="bg-white/5 border border-white/5 rounded-xl p-4 space-y-3">
            <p class="text-white font-semibold text-sm">Stat {{ $i + 1 }}</p>
            @foreach(['value' => 'Value', 'label' => 'Label'] as $field => $label)
              <div>
                <label class="{{ $labelClass }}">{{ $label }} *</label>
                <input name="stats[items][{{ $i }}][{{ $field }}]" value="{{ old("stats.items.$i.$field", data_get($form, "stats.items.$i.$field")) }}" class="{{ $inputClass }}">
                @error("stats.items.$i.$field") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
              </div>
            @endforeach
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="{{ $labelClass }}">Suffix</label>
                <select name="stats[items][{{ $i }}][suffix]" class="{{ $selectClass }}">
                  @foreach(['' => 'None', '+' => '+', '%' => '%', 'x' => 'x'] as $value => $label)
                    <option value="{{ $value }}" @selected(old("stats.items.$i.suffix", data_get($form, "stats.items.$i.suffix")) === $value)>{{ $label }}</option>
                  @endforeach
                </select>
                @error("stats.items.$i.suffix") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
              </div>
              <div>
                <label class="{{ $labelClass }}">Separator</label>
                <input name="stats[items][{{ $i }}][sep]" value="{{ old("stats.items.$i.sep", data_get($form, "stats.items.$i.sep")) }}" class="{{ $inputClass }}">
                @error("stats.items.$i.sep") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
              </div>
            </div>
          </div>
        @endfor
      </div>
    </section>

    <section x-show="tab === 'trust_bar'" x-cloak class="bg-[#1a2540] border border-white/5 rounded-2xl p-6 space-y-5">
      <h3 class="text-white font-bold text-lg">Trust Bar</h3>
      <div>
        <label class="{{ $labelClass }}">Title *</label>
        <input name="trust_bar[title]" value="{{ old('trust_bar.title', data_get($form, 'trust_bar.title')) }}" class="{{ $inputClass }}">
        @error('trust_bar.title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        @for($i = 0; $i < 7; $i++)
          <div class="bg-white/5 border border-white/5 rounded-xl p-4 space-y-3">
            <p class="text-white font-semibold text-sm">Item {{ $i + 1 }}</p>
            <label class="{{ $labelClass }}">Label *</label>
            <input name="trust_bar[items][{{ $i }}][label]" value="{{ old("trust_bar.items.$i.label", data_get($form, "trust_bar.items.$i.label")) }}" class="{{ $inputClass }}">
            @error("trust_bar.items.$i.label") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            <label class="{{ $labelClass }}">Approved Color *</label>
            <select name="trust_bar[items][{{ $i }}][color]" class="{{ $selectClass }}">
              @foreach($trustColors as $color)
                <option value="{{ $color }}" @selected(old("trust_bar.items.$i.color", data_get($form, "trust_bar.items.$i.color")) === $color)>{{ $color }}</option>
              @endforeach
            </select>
            @error("trust_bar.items.$i.color") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
          </div>
        @endfor
      </div>
    </section>

    <section x-show="tab === 'difference'" x-cloak class="bg-[#1a2540] border border-white/5 rounded-2xl p-6 space-y-5">
      <h3 class="text-white font-bold text-lg">Difference</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @foreach(['eyebrow' => 'Eyebrow', 'title' => 'Title'] as $field => $label)
          <div>
            <label class="{{ $labelClass }}">{{ $label }} *</label>
            <input name="difference[{{ $field }}]" value="{{ old("difference.$field", data_get($form, "difference.$field")) }}" class="{{ $inputClass }}">
            @error("difference.$field") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
          </div>
        @endforeach
      </div>
      <div>
        <label class="{{ $labelClass }}">Subtitle *</label>
        <textarea name="difference[subtitle]" rows="3" class="{{ $textareaClass }}">{{ old('difference.subtitle', data_get($form, 'difference.subtitle')) }}</textarea>
        @error('difference.subtitle') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        @for($i = 0; $i < 3; $i++)
          <div class="bg-white/5 border border-white/5 rounded-xl p-4 space-y-3">
            <p class="text-white font-semibold text-sm">Card {{ $i + 1 }}</p>
            @foreach(['badge' => 'Badge', 'title' => 'Title'] as $field => $label)
              <div>
                <label class="{{ $labelClass }}">{{ $label }} *</label>
                <input name="difference[cards][{{ $i }}][{{ $field }}]" value="{{ old("difference.cards.$i.$field", data_get($form, "difference.cards.$i.$field")) }}" class="{{ $inputClass }}">
                @error("difference.cards.$i.$field") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
              </div>
            @endforeach
            <div>
              <label class="{{ $labelClass }}">Color *</label>
              <select name="difference[cards][{{ $i }}][color]" class="{{ $selectClass }}">
                @foreach(['cyan' => 'Cyan', 'gold' => 'Gold'] as $value => $label)
                  <option value="{{ $value }}" @selected(old("difference.cards.$i.color", data_get($form, "difference.cards.$i.color")) === $value)>{{ $label }}</option>
                @endforeach
              </select>
              @error("difference.cards.$i.color") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
              <label class="{{ $labelClass }}">Icon Path *</label>
              <textarea name="difference[cards][{{ $i }}][icon_path]" rows="3" class="{{ $textareaClass }}">{{ old("difference.cards.$i.icon_path", data_get($form, "difference.cards.$i.icon_path")) }}</textarea>
              <p class="{{ $helpClass }}">SVG path data only. Do not paste full SVG markup.</p>
              @error("difference.cards.$i.icon_path") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
              <label class="{{ $labelClass }}">Body *</label>
              <textarea name="difference[cards][{{ $i }}][body]" rows="5" class="{{ $textareaClass }}">{{ old("difference.cards.$i.body", data_get($form, "difference.cards.$i.body")) }}</textarea>
              @error("difference.cards.$i.body") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
          </div>
        @endfor
      </div>
    </section>

    @foreach(['services_intro' => 'Services', 'portfolio_intro' => 'Portfolio', 'blog_intro' => 'Blog'] as $section => $label)
      <section x-show="tab === '{{ $section }}'" x-cloak class="bg-[#1a2540] border border-white/5 rounded-2xl p-6 space-y-5">
        <h3 class="text-white font-bold text-lg">{{ $label }} Intro</h3>
        <p class="{{ $helpClass }}">Cards for this section remain managed by the {{ $label }} domain module.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          @foreach(['eyebrow' => 'Eyebrow', 'title' => 'Title', 'cta_label' => 'CTA Label', 'card_link_label' => 'Card Link Label'] as $field => $fieldLabel)
            <div>
              <label class="{{ $labelClass }}">{{ $fieldLabel }} *</label>
              <input name="{{ $section }}[{{ $field }}]" value="{{ old("$section.$field", data_get($form, "$section.$field")) }}" class="{{ $inputClass }}">
              @error("$section.$field") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
          @endforeach
        </div>
        <div>
          <label class="{{ $labelClass }}">Subtitle *</label>
          <textarea name="{{ $section }}[subtitle]" rows="3" class="{{ $textareaClass }}">{{ old("$section.subtitle", data_get($form, "$section.subtitle")) }}</textarea>
          @error("$section.subtitle") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="{{ $labelClass }}">CTA Route</label>
            <select name="{{ $section }}[cta_route]" class="{{ $selectClass }}">
              @foreach($routeOptions as $route => $routeLabel)
                <option value="{{ $route }}" @selected(old("$section.cta_route", data_get($form, "$section.cta_route")) === $route)>{{ $routeLabel }}</option>
              @endforeach
            </select>
            @error("$section.cta_route") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="{{ $labelClass }}">CTA URL</label>
            <input name="{{ $section }}[cta_url]" value="{{ old("$section.cta_url", data_get($form, "$section.cta_url")) }}" class="{{ $inputClass }}">
            @error("$section.cta_url") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
          </div>
        </div>
        @if($section === 'services_intro')
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            @for($i = 0; $i < 3; $i++)
              <div>
                <label class="{{ $labelClass }}">Presentation Icon Path {{ $i + 1 }} *</label>
                <textarea name="services_intro[card_icons][{{ $i }}]" rows="4" class="{{ $textareaClass }}">{{ old("services_intro.card_icons.$i", data_get($form, "services_intro.card_icons.$i")) }}</textarea>
                <p class="{{ $helpClass }}">SVG path data only.</p>
                @error("services_intro.card_icons.$i") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
              </div>
            @endfor
          </div>
        @endif
      </section>
    @endforeach

    <section x-show="tab === 'primary_cta'" x-cloak class="bg-[#1a2540] border border-white/5 rounded-2xl p-6 space-y-5">
      <h3 class="text-white font-bold text-lg">Final CTA</h3>
      <p class="{{ $helpClass }}">The second title line keeps the current highlighted styling.</p>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label class="{{ $labelClass }}">Eyebrow *</label>
          <input name="primary_cta[eyebrow]" value="{{ old('primary_cta.eyebrow', data_get($form, 'primary_cta.eyebrow')) }}" class="{{ $inputClass }}">
          @error('primary_cta.eyebrow') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="{{ $labelClass }}">Primary Label *</label>
          <input name="primary_cta[primary_label]" value="{{ old('primary_cta.primary_label', data_get($form, 'primary_cta.primary_label')) }}" class="{{ $inputClass }}">
          @error('primary_cta.primary_label') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @for($i = 0; $i < 2; $i++)
          <div>
            <label class="{{ $labelClass }}">Title Line {{ $i + 1 }} *</label>
            <input name="primary_cta[title_lines][{{ $i }}]" value="{{ old("primary_cta.title_lines.$i", data_get($form, "primary_cta.title_lines.$i")) }}" class="{{ $inputClass }}">
            @error("primary_cta.title_lines.$i") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
          </div>
        @endfor
      </div>
      <div>
        <label class="{{ $labelClass }}">Subtitle *</label>
        <textarea name="primary_cta[subtitle]" rows="3" class="{{ $textareaClass }}">{{ old('primary_cta.subtitle', data_get($form, 'primary_cta.subtitle')) }}</textarea>
        @error('primary_cta.subtitle') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label class="{{ $labelClass }}">Primary Route</label>
          <select name="primary_cta[primary_route]" class="{{ $selectClass }}">
            @foreach($routeOptions as $route => $label)
              <option value="{{ $route }}" @selected(old('primary_cta.primary_route', data_get($form, 'primary_cta.primary_route')) === $route)>{{ $label }}</option>
            @endforeach
          </select>
          @error('primary_cta.primary_route') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="{{ $labelClass }}">Primary URL</label>
          <input name="primary_cta[primary_url]" value="{{ old('primary_cta.primary_url', data_get($form, 'primary_cta.primary_url')) }}" class="{{ $inputClass }}">
          @error('primary_cta.primary_url') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div>
          <label class="{{ $labelClass }}">Secondary Label *</label>
          <input name="primary_cta[secondary_button][label]" value="{{ old('primary_cta.secondary_button.label', data_get($form, 'primary_cta.secondary_button.label')) }}" class="{{ $inputClass }}">
          @error('primary_cta.secondary_button.label') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="{{ $labelClass }}">Secondary Route</label>
          <select name="primary_cta[secondary_button][route]" class="{{ $selectClass }}">
            @foreach($routeOptions as $route => $label)
              <option value="{{ $route }}" @selected(old('primary_cta.secondary_button.route', data_get($form, 'primary_cta.secondary_button.route')) === $route)>{{ $label }}</option>
            @endforeach
          </select>
          @error('primary_cta.secondary_button.route') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="{{ $labelClass }}">Secondary URL</label>
          <input name="primary_cta[secondary_button][url]" value="{{ old('primary_cta.secondary_button.url', data_get($form, 'primary_cta.secondary_button.url')) }}" class="{{ $inputClass }}">
          @error('primary_cta.secondary_button.url') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @for($i = 0; $i < 4; $i++)
          <div>
            <label class="{{ $labelClass }}">Proof Point {{ $i + 1 }} *</label>
            <input name="primary_cta[proof_points][{{ $i }}]" value="{{ old("primary_cta.proof_points.$i", data_get($form, "primary_cta.proof_points.$i")) }}" class="{{ $inputClass }}">
            @error("primary_cta.proof_points.$i") <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
          </div>
        @endfor
      </div>
    </section>

    <div class="sticky bottom-4 z-10 bg-[#1a2540]/95 backdrop-blur border border-white/10 rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <p class="text-gray-400 text-sm">Save updates all eight fixed homepage sections in one transaction.</p>
      <div class="flex items-center gap-3">
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn-secondary text-sm">Preview Homepage</a>
        <button type="submit" class="btn-primary px-8 py-3">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          Save Homepage
        </button>
      </div>
    </div>
  </form>
</div>

@endsection
