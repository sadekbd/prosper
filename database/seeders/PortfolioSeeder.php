<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('portfolio_projects')->count() > 0) {
            $this->command->info('Portfolio projects already exist. Skipping.');
            return;
        }

        $projects = [
            [
                'title'             => 'E-Commerce Google Ads Full Funnel Rebuild',
                'slug'              => 'ecommerce-google-ads-full-funnel-rebuild',
                'category'          => 'google_ads',
                'short_description' => 'Complete Google Ads account rebuild — search, shopping, display and remarketing — with Smart Bidding strategy and audience segmentation.',
                'full_description'  => '<p>This client came to us with a Google Ads account that had been mismanaged for over two years. Their ROAS sat at 1.2x and they were about to shut down their campaigns entirely.</p><p>We started with a full audit: identified irrelevant keywords draining budget, found missing negative keyword lists, discovered that conversion tracking was firing on page load instead of actual purchases, and noted that there was zero audience segmentation.</p><h3>What We Did</h3><ul><li>Rebuilt conversion tracking with GTM — purchase events only, with revenue values</li><li>Restructured campaigns by intent stage (branded, non-branded, competitor)</li><li>Implemented Smart Shopping with a 90-day data feed to accelerate learning</li><li>Built RLSA campaigns for cart abandoners and past purchasers</li><li>Created dynamic remarketing with product feed integration</li></ul><h3>Results</h3><p>Within 90 days, ROAS climbed from 1.2x to 4.2x. By month 6, it reached 6.8x with 40% lower CPCs.</p>',
                'technologies'      => json_encode(['Google Ads', 'Google Tag Manager', 'GA4', 'Smart Bidding', 'Google Merchant Center']),
                'result_summary'    => '4.2x ROAS in 90 days (from 1.2x)',
                'client_name'       => 'E-Commerce Retailer',
                'project_url'       => null,
                'featured_image'    => null,
                'gallery_images'    => json_encode([]),
                'meta_title'        => 'E-Commerce Google Ads Case Study — 4.2x ROAS',
                'meta_description'  => 'How Prosper Media rebuilt a failing Google Ads account and achieved 4.2x ROAS in 90 days.',
                'is_featured'       => 1,
                'sort_order'        => 1,
                'status'            => 'published',
            ],
            [
                'title'             => 'GTM Server-Side + Meta CAPI Implementation',
                'slug'              => 'gtm-server-side-meta-capi-implementation',
                'category'          => 'tracking_setup',
                'short_description' => 'Server-side GTM container deployment with Facebook Conversion API, recovering 85% of lost conversion data after iOS 14+ changes.',
                'full_description'  => '<p>After iOS 14.5, this e-commerce client saw their Meta reported conversions drop by 60%. Facebook ROAS reporting became unreliable, making budget decisions almost impossible.</p><h3>The Problem</h3><p>Client-side pixels rely on browser data — blocked by iOS, ad blockers, and ITP. Server-side tracking sends events directly from the server to Meta, bypassing these restrictions entirely.</p><h3>What We Built</h3><ul><li>GTM Server container on Google Cloud Run</li><li>Facebook CAPI integration with event deduplication</li><li>Custom transport URL for all client-side events to pass through server</li><li>Event Match Quality score improved from 4.2 to 8.7/10</li><li>Hashed PII (email, phone) for improved matching</li></ul>',
                'technologies'      => json_encode(['GTM Server', 'Meta CAPI', 'Google Cloud Run', 'Node.js', 'PHP']),
                'result_summary'    => '85% conversion data recovered — EMQ score 4.2 → 8.7',
                'client_name'       => 'Fashion E-Commerce Brand',
                'project_url'       => null,
                'featured_image'    => null,
                'gallery_images'    => json_encode([]),
                'meta_title'        => 'GTM Server-Side & Meta CAPI Case Study — 85% Data Recovery',
                'meta_description'  => 'How Prosper Media implemented server-side tracking to recover 85% of lost conversion data.',
                'is_featured'       => 1,
                'sort_order'        => 2,
                'status'            => 'published',
            ],
            [
                'title'             => 'Laravel SaaS Landing Page — Conversion Optimisation',
                'slug'              => 'laravel-saas-landing-page-conversion-optimisation',
                'category'          => 'web_development',
                'short_description' => 'High-converting SaaS landing page built with Laravel, featuring A/B testing, heatmap integration, and Core Web Vitals optimisation.',
                'full_description'  => '<p>This B2B SaaS client had a landing page converting at 4.2% — decent, but below the 8–12% range typical for their market. They needed a complete rebuild focused on conversion mechanics, not just design.</p><h3>Technical Stack</h3><ul><li>Laravel 11 + Blade + Tailwind CSS</li><li>Vite for asset bundling (LCP under 1.2s)</li><li>HotJar for heatmaps and session recordings</li><li>Google Optimize for A/B testing (3 variants)</li><li>GTM for all tracking events</li></ul><h3>Results</h3><p>The winning variant converted at 11.8% — a 180% improvement. Annual recurring revenue increased by £180,000 attributable to the page rebuild alone.</p>',
                'technologies'      => json_encode(['Laravel 11', 'Tailwind CSS', 'MySQL', 'Vite', 'GTM', 'HotJar']),
                'result_summary'    => 'Conversion rate 4.2% → 11.8% (+180%)',
                'client_name'       => 'B2B SaaS Company',
                'project_url'       => null,
                'featured_image'    => null,
                'gallery_images'    => json_encode([]),
                'meta_title'        => 'Laravel SaaS Landing Page — 180% Conversion Improvement',
                'meta_description'  => 'How Prosper Media rebuilt a SaaS landing page to achieve 11.8% conversion rate.',
                'is_featured'       => 1,
                'sort_order'        => 3,
                'status'            => 'published',
            ],
            [
                'title'             => 'Legal Services Lead Gen Landing Page',
                'slug'              => 'legal-services-lead-gen-landing-page',
                'category'          => 'landing_page',
                'short_description' => 'Redesigned a poorly converting legal services landing page with trust signals, form optimisation, and speed improvements.',
                'full_description'  => '<p>A local law firm was running Google Ads to a generic website homepage. CPL sat at £420 per lead — far too high for their service margins.</p><p>We built a dedicated landing page with legal-specific trust signals, a simplified form, and instant call tracking.</p>',
                'technologies'      => json_encode(['HTML5', 'Tailwind CSS', 'GTM', 'HotJar', 'CallRail']),
                'result_summary'    => 'CPL reduced from £420 to £85 — leads increased 210%',
                'client_name'       => 'Legal Services Firm',
                'project_url'       => null,
                'featured_image'    => null,
                'gallery_images'    => json_encode([]),
                'meta_title'        => 'Legal Services Landing Page — 210% More Leads',
                'meta_description'  => null,
                'is_featured'       => 0,
                'sort_order'        => 4,
                'status'            => 'published',
            ],
            [
                'title'             => 'Local Business Google Ads — Performance Max',
                'slug'              => 'local-business-google-ads-performance-max',
                'category'          => 'google_ads',
                'short_description' => 'Local search + Performance Max campaigns for a home services business with call tracking and lead form extensions.',
                'full_description'  => '<p>A plumbing company spending £3,000/month on Google Ads with no proper tracking in place. We introduced call tracking, rebuilt campaign structure, and launched Performance Max targeting their service areas.</p>',
                'technologies'      => json_encode(['Google Ads', 'Performance Max', 'Call Tracking', 'GTM', 'GA4']),
                'result_summary'    => 'CPL reduced by 62% — call volume tripled',
                'client_name'       => 'Home Services Business',
                'project_url'       => null,
                'featured_image'    => null,
                'gallery_images'    => json_encode([]),
                'meta_title'        => null,
                'meta_description'  => null,
                'is_featured'       => 0,
                'sort_order'        => 5,
                'status'            => 'published',
            ],
            [
                'title'             => 'AI Lead Qualification Automation Pipeline',
                'slug'              => 'ai-lead-qualification-automation-pipeline',
                'category'          => 'automation',
                'short_description' => 'Automated lead scoring, CRM population, and email sequence trigger pipeline using Laravel webhooks, OpenAI, and Make.com.',
                'full_description'  => '<p>This digital agency was manually reviewing every inbound lead form — a process taking 14+ hours per week. We automated it entirely.</p><p>When a form submits, a Laravel webhook fires, sends the lead data to OpenAI for qualification scoring, populates the CRM, assigns a score, triggers the correct email sequence, and posts a Slack notification — all in under 3 seconds.</p>',
                'technologies'      => json_encode(['Laravel', 'OpenAI API', 'Make.com', 'Webhooks', 'MySQL']),
                'result_summary'    => '14 hours/week saved — 100% of leads scored automatically',
                'client_name'       => 'Digital Marketing Agency',
                'project_url'       => null,
                'featured_image'    => null,
                'gallery_images'    => json_encode([]),
                'meta_title'        => null,
                'meta_description'  => null,
                'is_featured'       => 0,
                'sort_order'        => 6,
                'status'            => 'published',
            ],
        ];

        foreach ($projects as $project) {
            DB::table('portfolio_projects')->insert(array_merge($project, [
                'created_at' => now()->subDays(rand(10, 180)),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('Portfolio projects seeded: ' . count($projects) . ' projects.');
    }
}