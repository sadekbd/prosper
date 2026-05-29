<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('services')->count() > 0) {
            $this->command->info('Services already exist. Skipping.');
            return;
        }

        $services = [
            [
                'title'       => 'Google Ads Mastery',
                'slug'        => 'google-ads-mastery',
                'subtitle'    => 'Maximise your ROI with data-driven search advertising.',
                'description' => 'We do not just run ads. We engineer profitable campaigns through high-intent keyword research, strategic bidding, compelling ad copy, landing page alignment, and ongoing optimization.',
                'icon'        => 'google-ads',
                'sort_order'  => 1,
                'status'      => 'active',
                'features'    => [
                    'Search & Display Campaigns',
                    'Competitor Analysis & Keyword Strategy',
                    'A/B Testing & Ad Optimization',
                    'Budget Management',
                    'Remarketing Campaigns',
                    'Monthly Performance Reporting',
                ],
            ],
            [
                'title'       => 'Advanced Conversion Tracking',
                'slug'        => 'advanced-conversion-tracking',
                'subtitle'    => 'Stop guessing and start measuring.',
                'description' => 'We bridge the gap between your website and your marketing data using professional-grade tracking setup.',
                'icon'        => 'tracking',
                'sort_order'  => 2,
                'status'      => 'active',
                'features'    => [
                    'Google Tag Manager Setup',
                    'Meta Pixel Setup',
                    'Server-Side Tracking',
                    'API Conversion Tracking',
                    'Custom Events & Goals',
                    'Lead Attribution',
                    'ROI Reporting',
                ],
            ],
            [
                'title'       => 'Professional Web Development',
                'slug'        => 'professional-web-development',
                'subtitle'    => 'High-performance websites built for conversion.',
                'description' => 'A beautiful website is useless if it does not convert. We build fast, secure, responsive, and conversion-focused websites.',
                'icon'        => 'web-dev',
                'sort_order'  => 3,
                'status'      => 'active',
                'features'    => [
                    'Custom Web Development',
                    'Landing Page Optimization',
                    'Database Integration',
                    'Secure Backend Logic',
                    'Speed Optimization',
                    'Mobile-First Design',
                    'Laravel / PHP / MySQL Development',
                ],
            ],
        ];

        foreach ($services as $serviceData) {
            $features = $serviceData['features'];
            unset($serviceData['features']);     // remove before inserting to services table

            $serviceId = DB::table('services')->insertGetId(array_merge($serviceData, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            // Insert features linked to this service
            foreach ($features as $index => $feature) {
                DB::table('service_features')->insert([
                    'service_id' => $serviceId,
                    'feature'    => $feature,
                    'sort_order' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Services and features seeded successfully.');
    }
}