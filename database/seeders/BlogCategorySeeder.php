<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogCategorySeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('blog_categories')->count() > 0) {
            $this->command->info('Blog categories already exist. Skipping.');
            return;
        }

        $categories = [
            [
                'name'       => 'Google Ads Tips',
                'slug'       => 'google-ads-tips',
                'description'=> 'Actionable tips and strategies for Google Ads campaigns.',
                'color'      => '#00B4D8',
                'sort_order' => 1,
                'status'     => 'active',
            ],
            [
                'name'       => 'Web Dev Tutorials',
                'slug'       => 'web-dev-tutorials',
                'description'=> 'Tutorials on Laravel, PHP, MySQL and modern web development.',
                'color'      => '#FFB703',
                'sort_order' => 2,
                'status'     => 'active',
            ],
            [
                'name'       => 'Tracking Guides',
                'slug'       => 'tracking-guides',
                'description'=> 'Step-by-step guides for GTM, Meta Pixel, and conversion tracking.',
                'color'      => '#0B132B',
                'sort_order' => 3,
                'status'     => 'active',
            ],
            [
                'name'       => 'Case Studies',
                'slug'       => 'case-studies',
                'description'=> 'Real results from Prosper Media client campaigns.',
                'color'      => '#2D3142',
                'sort_order' => 4,
                'status'     => 'active',
            ],
        ];

        foreach ($categories as $category) {
            DB::table('blog_categories')->insert(array_merge($category, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('Blog categories seeded successfully.');
    }
}