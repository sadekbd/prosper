<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogArticleSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('blog_articles')->count() > 0) {
            $this->command->info('Blog articles already exist. Skipping.');
            return;
        }

        // Get the super admin's ID
        $author = DB::table('admin_users')->where('role', 'super_admin')->first();
        if (!$author) {
            $this->command->error('No super admin found. Run SuperAdminSeeder first.');
            return;
        }

        // Get category IDs
        $categories = DB::table('blog_categories')->pluck('id', 'slug');
        if ($categories->isEmpty()) {
            $this->command->error('No blog categories found. Run BlogCategorySeeder first.');
            return;
        }

        $articles = [
            [
                'category_slug' => 'tracking-guides',
                'title'         => 'How to Set Up Server-Side Conversion Tracking in Google Ads 2025',
                'slug'          => 'server-side-conversion-tracking-google-ads-2025',
                'excerpt'       => 'A complete step-by-step guide to deploying a GTM server container, configuring the transport URL, and routing server-side conversion data to Google Ads for maximum accuracy.',
                'read_time'     => 12,
                'content'       => $this->getArticle1Content(),
                'meta_title'    => 'Server-Side Conversion Tracking Google Ads 2025 — Complete Guide',
                'meta_description' => 'Learn how to set up server-side conversion tracking for Google Ads using GTM server container. Step-by-step guide for 2025.',
                'tags'          => json_encode(['server-side-tracking', 'google-ads', 'gtm', 'conversion-tracking']),
                'status'        => 'published',
                'published_at'  => now()->subDays(14),
            ],
            [
                'category_slug' => 'tracking-guides',
                'title'         => 'Server-Side vs Client-Side Tracking: The Complete 2025 Comparison',
                'slug'          => 'server-side-vs-client-side-tracking-2025',
                'excerpt'       => 'Understanding the technical differences between client-side pixels and server-side event APIs — and exactly when each approach maximises your conversion data accuracy.',
                'read_time'     => 9,
                'content'       => $this->getArticle2Content(),
                'meta_title'    => 'Server-Side vs Client-Side Tracking 2025 — Which Do You Need?',
                'meta_description' => 'Compare server-side and client-side tracking approaches. Learn when to use each for maximum data accuracy and GDPR compliance.',
                'tags'          => json_encode(['server-side-tracking', 'client-side', 'gtm', 'meta-pixel']),
                'status'        => 'published',
                'published_at'  => now()->subDays(21),
            ],
            [
                'category_slug' => 'google-ads-tips',
                'title'         => 'Google Ads Smart Bidding: When to Use It and When to Avoid It',
                'slug'          => 'google-ads-smart-bidding-when-to-use-avoid',
                'excerpt'       => 'Smart Bidding is not always smart. This guide explains the data thresholds, campaign conditions, and account structures where automated bidding helps — and where it silently drains budget.',
                'read_time'     => 10,
                'content'       => $this->getArticle3Content(),
                'meta_title'    => 'Google Ads Smart Bidding Guide — When to Use & Avoid It',
                'meta_description' => 'The honest guide to Google Ads Smart Bidding. Learn data thresholds required, best campaign types, and when manual bidding wins.',
                'tags'          => json_encode(['google-ads', 'smart-bidding', 'ppc', 'roas']),
                'status'        => 'published',
                'published_at'  => now()->subDays(35),
            ],
            [
                'category_slug' => 'web-dev-tutorials',
                'title'         => 'Building High-Converting Laravel Landing Pages: A Technical Guide',
                'slug'          => 'high-converting-laravel-landing-pages-technical-guide',
                'excerpt'       => 'The complete technical and psychological framework behind landing pages that consistently convert at 3x the industry average, built with Laravel, Tailwind CSS, and proven UX patterns.',
                'read_time'     => 15,
                'content'       => $this->getArticle4Content(),
                'meta_title'    => 'High-Converting Laravel Landing Pages — Complete Technical Guide',
                'meta_description' => 'Learn how to build landing pages in Laravel that convert at 10%+. Technical guide covering UX, speed, forms, and tracking.',
                'tags'          => json_encode(['laravel', 'landing-page', 'conversion-rate', 'web-development']),
                'status'        => 'published',
                'published_at'  => now()->subDays(48),
            ],
            [
                'category_slug' => 'case-studies',
                'title'         => 'How We Achieved 320% ROAS for an E-Commerce Client in 90 Days',
                'slug'          => '320-percent-roas-ecommerce-case-study-90-days',
                'excerpt'       => 'A detailed breakdown of the campaign strategy, tracking setup, landing page changes, and bidding decisions that turned a struggling Google Ads account into a profitable revenue channel.',
                'read_time'     => 18,
                'content'       => $this->getArticle5Content(),
                'meta_title'    => 'Case Study: 320% ROAS in 90 Days — Google Ads E-Commerce',
                'meta_description' => 'Real case study: How Prosper Media rebuilt a failing e-commerce Google Ads account to achieve 320% ROAS in 90 days.',
                'tags'          => json_encode(['case-study', 'google-ads', 'ecommerce', 'roas']),
                'status'        => 'published',
                'published_at'  => now()->subDays(60),
            ],
        ];

        foreach ($articles as $article) {
            $categorySlug = $article['category_slug'];
            unset($article['category_slug']);

            $categoryId = $categories[$categorySlug] ?? $categories->first();

            DB::table('blog_articles')->insert(array_merge($article, [
                'author_id'   => $author->id,
                'category_id' => $categoryId,
                'views'       => rand(150, 2400),
                'created_at'  => now()->subDays(rand(1, 5)),
                'updated_at'  => now(),
            ]));
        }

        $this->command->info('Blog articles seeded: ' . count($articles) . ' articles.');
    }

    // ── Article Content Blocks ─────────────────────────────────────

    private function getArticle1Content(): string
    {
        return <<<HTML
<p class="lead">Server-side conversion tracking is no longer optional for serious advertisers. With iOS 14+ restrictions, ad blockers affecting up to 30% of traffic, and browser ITP tightening cookie lifetimes, client-side pixels are losing data fast.</p>

<p>This guide walks you through deploying a GTM server container and routing your Google Ads conversions through it — step by step.</p>

<h2>Why Server-Side Tracking Matters</h2>
<p>Client-side tags fire from the user's browser. That means they're subject to:</p>
<ul>
<li>Ad blocker blocking (30–40% of desktop users)</li>
<li>iOS Intelligent Tracking Prevention (ITP)</li>
<li>Browser cookie restrictions (7-day cap in Safari)</li>
<li>Page load failures cutting tag execution</li>
</ul>
<p>Server-side tracking moves the data collection to your server, sending it directly to Google and Meta. Browsers can't block it.</p>

<h2>Step 1: Create Your GTM Server Container</h2>
<p>In your Google Tag Manager account, create a new container and select <strong>Server</strong> as the container type. You'll receive a container snippet and a server URL.</p>

<h2>Step 2: Deploy the Server Container</h2>
<p>You need a server to run the container. Options include:</p>
<ul>
<li>Google Cloud Run (easiest, Google-managed)</li>
<li>App Engine</li>
<li>Your own VPS (advanced)</li>
</ul>
<p>For most setups, Cloud Run is recommended. GTM provides a one-click deployment script.</p>

<h2>Step 3: Configure the Transport URL</h2>
<p>Update your client-side GTM container to point all events to your server container URL instead of directly to Google. This is done in the GTM settings under "Server-side tagging".</p>

<h2>Step 4: Set Up the Google Ads Conversion Tag</h2>
<p>In your server container, add a Google Ads Conversion Tracking tag. Map the event data from the client request to the conversion fields: conversion ID, conversion label, and value.</p>

<h2>Step 5: Test and Debug</h2>
<p>Use GTM's preview mode on the server container to verify events are being received. Check Google Ads Diagnostics to confirm conversions are registering correctly.</p>

<h2>Results You Can Expect</h2>
<p>Our clients typically see 20–40% more reported conversions after switching to server-side tracking — not because more conversions are happening, but because they were always happening and going unrecorded.</p>

<div class="cta-box bg-pm-cyan/10 border border-pm-cyan/20 rounded-xl p-6 my-8">
<h3>Need Help Setting This Up?</h3>
<p>Server-side tracking setup is one of our core services. We handle the entire implementation — container deployment, tag migration, testing, and validation.</p>
<a href="/contact" class="btn-primary">Get a Free Tracking Audit</a>
</div>
HTML;
    }

    private function getArticle2Content(): string
    {
        return <<<HTML
<p class="lead">The debate between server-side and client-side tracking has become one of the most important technical decisions in digital marketing. Understanding the difference determines how much conversion data you actually have to work with.</p>

<h2>What Is Client-Side Tracking?</h2>
<p>Client-side tracking uses JavaScript tags that execute in the user's browser. The browser collects data and sends it directly to advertising platforms (Google, Meta, etc.).</p>
<p><strong>Examples:</strong> Google Analytics gtag.js, Meta Pixel, LinkedIn Insight Tag.</p>
<p><strong>The Problem:</strong> Browsers can block, delay, or restrict these scripts. iOS 14+ aggressively limits cross-site tracking. Ad blockers prevent them from firing entirely.</p>

<h2>What Is Server-Side Tracking?</h2>
<p>Server-side tracking uses your web server (or a dedicated container server) to collect event data and forward it to advertising platforms via their server-to-server APIs.</p>
<p><strong>Examples:</strong> GTM Server Container, Meta Conversion API (CAPI), Google Ads Offline Conversions.</p>
<p><strong>The Advantage:</strong> Your server communicates directly with the platform. No browser restrictions. No ad blockers. More reliable, more accurate data.</p>

<h2>Direct Comparison</h2>
<table>
<thead><tr><th>Factor</th><th>Client-Side</th><th>Server-Side</th></tr></thead>
<tbody>
<tr><td>Ad Blocker Impact</td><td>High (30-40% blocked)</td><td>None</td></tr>
<tr><td>iOS ITP Impact</td><td>High</td><td>None</td></tr>
<tr><td>Setup Complexity</td><td>Low</td><td>Medium-High</td></tr>
<tr><td>Data Accuracy</td><td>60-80%</td><td>90-98%</td></tr>
<tr><td>Cost</td><td>Free</td><td>Server costs (~$10-50/mo)</td></tr>
</tbody>
</table>

<h2>When Should You Use Server-Side?</h2>
<p>Switch to server-side tracking when:</p>
<ul>
<li>You're spending £3,000+/month on paid media</li>
<li>You noticed a drop in reported conversions after iOS 14</li>
<li>Your audience skews toward privacy-conscious users (tech, finance)</li>
<li>You need GDPR-compliant tracking with server-controlled data flows</li>
</ul>

<h2>Can You Use Both?</h2>
<p>Yes — and for Meta advertising, you should. Running client-side Pixel alongside server-side CAPI (with deduplication) gives you the highest possible event match quality. Both events fire, Meta deduplicates them, and you get better attribution than either alone.</p>
HTML;
    }

    private function getArticle3Content(): string
    {
        return <<<HTML
<p class="lead">Google wants you to use Smart Bidding. It benefits them when you do. But Smart Bidding is not always the right choice — and blindly enabling it can drain your budget fast.</p>

<h2>What Is Smart Bidding?</h2>
<p>Smart Bidding is Google's machine learning-based automated bidding. Instead of you setting bids manually, Google's algorithm adjusts bids in real-time for every auction based on dozens of signals: device, location, time, audience, search query intent, and more.</p>

<p><strong>Available Smart Bidding strategies:</strong></p>
<ul>
<li>Target CPA (cost per acquisition)</li>
<li>Target ROAS (return on ad spend)</li>
<li>Maximise Conversions</li>
<li>Maximise Conversion Value</li>
<li>Enhanced CPC (partial automation)</li>
</ul>

<h2>The Data Threshold Problem</h2>
<p>Smart Bidding needs conversion data to optimise. Without enough data, the algorithm guesses — and guesses badly.</p>

<p><strong>Google's minimum recommendation:</strong> 30+ conversions in 30 days per campaign. In practice, we recommend 50+ for stable performance, 100+ for Target ROAS.</p>

<p>Below these thresholds, Smart Bidding will often:</p>
<ul>
<li>Overpay for low-intent clicks</li>
<li>Ignore high-intent queries it hasn't "learned" yet</li>
<li>Swing wildly between over-spending and under-spending</li>
</ul>

<h2>When Smart Bidding Works</h2>
<ul>
<li>Your campaign has 50+ monthly conversions</li>
<li>Your conversion tracking is accurate (server-side, not just pixel)</li>
<li>Your conversion window is properly configured</li>
<li>You have clear, single-action conversion goals (not 8 different "goals")</li>
</ul>

<h2>When to Use Manual Bidding Instead</h2>
<ul>
<li>New campaigns with no conversion history</li>
<li>Low-volume niches with fewer than 30 monthly conversions</li>
<li>Highly seasonal products where historical data misleads the algorithm</li>
<li>When you need precise control over impression share for branded terms</li>
</ul>

<h2>Our Recommended Approach</h2>
<p>Start new campaigns on Maximise Clicks or Manual CPC to gather conversion data. Once you hit 50 conversions, transition to Target CPA or Target ROAS. Set a realistic initial target (not aspirational) and give the algorithm 2–3 weeks to adjust before making changes.</p>
HTML;
    }

    private function getArticle4Content(): string
    {
        return <<<HTML
<p class="lead">Most landing pages fail not because of bad design, but because of poor technical architecture, slow load times, and friction in the conversion path. This guide covers both the technical and psychological elements of landing pages that convert consistently above 10%.</p>

<h2>The Technical Foundation</h2>
<p>Before a single visitor sees your page, the technical stack determines your conversion ceiling. A page that loads in 4 seconds will convert at half the rate of a page that loads in 1.5 seconds — regardless of copy or design.</p>

<h3>Laravel + Tailwind + Vite Setup</h3>
<p>Our preferred stack for high-converting landing pages:</p>
<ul>
<li>Laravel for routing, form handling, and backend validation</li>
<li>Tailwind CSS for utility-first styling (no unused CSS)</li>
<li>Vite for fast asset bundling with CSS/JS code splitting</li>
<li>Alpine.js for lightweight interactivity (modals, toggles)</li>
</ul>

<h2>Core Web Vitals Targets</h2>
<p>Google's Core Web Vitals directly correlate with conversion rates:</p>
<ul>
<li><strong>LCP (Largest Contentful Paint):</strong> Under 2.5s</li>
<li><strong>FID (First Input Delay):</strong> Under 100ms</li>
<li><strong>CLS (Cumulative Layout Shift):</strong> Under 0.1</li>
</ul>

<h2>The Above-the-Fold Rule</h2>
<p>Everything above the fold must answer three questions instantly: What is this? Who is it for? What should I do next? If a visitor has to scroll to understand your offer, you've already lost 40% of them.</p>

<h2>Form Optimisation</h2>
<p>Every extra form field reduces conversion rate by 5–10%. Best practice:</p>
<ul>
<li>Only ask for information you will actually use immediately</li>
<li>Name + Email + Phone is usually enough for a first-touch capture</li>
<li>Use inline validation, not post-submit error pages</li>
<li>Auto-format phone numbers and email addresses</li>
<li>Show a clear privacy statement below the submit button</li>
</ul>

<h2>Tracking Setup</h2>
<p>Every landing page needs these events tracked via GTM:</p>
<ul>
<li>Page view (with traffic source params)</li>
<li>Scroll depth (25%, 50%, 75%, 100%)</li>
<li>Form start (first field interaction)</li>
<li>Form submission (firing on actual success, not button click)</li>
<li>Thank you page view (hard confirmation)</li>
</ul>
HTML;
    }

    private function getArticle5Content(): string
    {
        return <<<HTML
<p class="lead">This is the full breakdown of how we took an e-commerce Google Ads account from 1.2x ROAS to 4.2x in 90 days. No fluff. Just the exact decisions we made and why.</p>

<h2>The Starting Point</h2>
<p>The client — a UK fashion retailer — had been running Google Ads for 18 months. Monthly spend: £8,000. Reported ROAS: 1.2x. They were losing money and about to pull the plug.</p>

<p>Before touching a single setting, we ran a full audit. Here's what we found:</p>
<ul>
<li>Conversion tracking firing on page load, not purchase completion — inflating conversion numbers by 340%</li>
<li>No negative keyword list (ads showing for competitor brand terms, irrelevant queries)</li>
<li>Single broad match campaign with all products mixed together</li>
<li>No remarketing campaigns — every visitor treated as cold traffic</li>
<li>Shopping feed with incorrect pricing for 30% of products</li>
</ul>

<h2>Month 1: Fix the Foundation</h2>
<p>We did not touch the campaigns in month 1. We fixed the foundations first.</p>

<p><strong>Tracking fix:</strong> Rebuilt all conversion tracking via GTM. Purchase events now fire on order confirmation page load, with actual revenue values passed dynamically from the order data. Conversion data immediately dropped 70% — but was now accurate.</p>

<p><strong>Product feed:</strong> Audited and fixed the Merchant Center feed. Correct pricing, better product titles with high-intent keywords, added GTINs for all products.</p>

<p><strong>Negative keywords:</strong> Built a 400-term negative keyword list from 18 months of search term data.</p>

<h2>Month 2: Rebuild Campaign Structure</h2>
<p>With accurate data flowing, we rebuilt the campaign structure by intent stage:</p>
<ul>
<li>Brand campaigns (maximum impression share, manual CPC)</li>
<li>Category search campaigns (high-intent terms per product category)</li>
<li>Smart Shopping campaigns (one per product category)</li>
<li>Dynamic remarketing (past visitors, cart abandoners, past purchasers)</li>
</ul>

<h2>Month 3: Optimise and Scale</h2>
<p>With 6 weeks of clean conversion data, we switched Smart Shopping to Target ROAS. Initial target: 3x. Actual performance: 4.2x by the end of month 3.</p>

<h2>Key Lessons</h2>
<p>The biggest lever was fixing conversion tracking. The account looked like it was performing at 1.2x ROAS with inflated conversion counts. Real ROAS with accurate tracking was actually negative. Every bidding decision Google's algorithm made was based on bad data.</p>

<p>Fix the data first. Always.</p>
HTML;
    }
}