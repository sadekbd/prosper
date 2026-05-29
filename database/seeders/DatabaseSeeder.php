<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,       // 1. Super admin user first
            SiteSettingsSeeder::class,     // 2. Site settings
            BlogCategorySeeder::class,     // 3. Blog categories
            ServiceSeeder::class,          // 4. Services + features
            PortfolioSeeder::class,        // 5. Portfolio projects
            BlogArticleSeeder::class,      // 6. Blog articles (needs author + categories)
            PageSeeder::class,             // 7. Static pages (privacy policy)
        ]);
    }
}