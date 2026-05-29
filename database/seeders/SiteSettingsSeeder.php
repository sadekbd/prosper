<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [

            // ── General ────────────────────────────────────────────
            [
                'key'   => 'site_name',
                'value' => 'Prosper Media',
                'group' => 'general',
                'label' => 'Site Name',
                'type'  => 'text',
            ],
            [
                'key'   => 'site_slogan',
                'value' => 'Be Optimistic',
                'group' => 'general',
                'label' => 'Site Slogan',
                'type'  => 'text',
            ],
            [
                'key'   => 'site_tagline',
                'value' => 'Engineering Digital Success with Technical Precision.',
                'group' => 'general',
                'label' => 'Site Tagline',
                'type'  => 'text',
            ],
            [
                'key'   => 'site_logo',
                'value' => null,
                'group' => 'general',
                'label' => 'Site Logo',
                'type'  => 'image',
            ],
            [
                'key'   => 'favicon',
                'value' => null,
                'group' => 'general',
                'label' => 'Favicon',
                'type'  => 'image',
            ],

            // ── Contact ────────────────────────────────────────────
            [
                'key'   => 'business_email',
                'value' => 'hello@prospermedia.com',
                'group' => 'contact',
                'label' => 'Business Email',
                'type'  => 'email',
            ],
            [
                'key'   => 'business_mobile',
                'value' => '+8801700000000',
                'group' => 'contact',
                'label' => 'Business Mobile',
                'type'  => 'tel',
            ],
            [
                'key'   => 'whatsapp_number',
                'value' => '+8801700000000',
                'group' => 'contact',
                'label' => 'WhatsApp Number',
                'type'  => 'tel',
            ],
            [
                'key'   => 'business_address',
                'value' => 'Dhaka, Bangladesh',
                'group' => 'contact',
                'label' => 'Business Address',
                'type'  => 'textarea',
            ],

            // ── Social ─────────────────────────────────────────────
            [
                'key'   => 'facebook_url',
                'value' => 'https://facebook.com/prospermedia',
                'group' => 'social',
                'label' => 'Facebook URL',
                'type'  => 'url',
            ],
            [
                'key'   => 'linkedin_url',
                'value' => 'https://linkedin.com/company/prospermedia',
                'group' => 'social',
                'label' => 'LinkedIn URL',
                'type'  => 'url',
            ],
            [
                'key'   => 'github_url',
                'value' => 'https://github.com/prospermedia',
                'group' => 'social',
                'label' => 'GitHub URL',
                'type'  => 'url',
            ],
            [
                'key'   => 'fiverr_url',
                'value' => 'https://fiverr.com/prospermedia',
                'group' => 'social',
                'label' => 'Fiverr URL',
                'type'  => 'url',
            ],

            // ── SEO ────────────────────────────────────────────────
            [
                'key'   => 'default_meta_title',
                'value' => 'Prosper Media — Engineering Digital Success with Technical Precision',
                'group' => 'seo',
                'label' => 'Default Meta Title',
                'type'  => 'text',
            ],
            [
                'key'   => 'default_meta_description',
                'value' => 'Prosper Media is a tech-first AI automation, digital marketing and web development agency helping businesses grow through technical precision and measurable results.',
                'group' => 'seo',
                'label' => 'Default Meta Description',
                'type'  => 'textarea',
            ],
            [
                'key'   => 'og_image',
                'value' => null,
                'group' => 'seo',
                'label' => 'Default OG Share Image',
                'type'  => 'image',
            ],
            [
                'key'   => 'google_analytics_id',
                'value' => null,
                'group' => 'seo',
                'label' => 'Google Analytics ID',
                'type'  => 'text',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $setting['key']],          // match condition
                array_merge($setting, [               // data to insert/update
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('Site settings seeded successfully.');
    }
}