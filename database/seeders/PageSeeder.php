<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('pages')->where('slug', 'privacy-policy')->exists()) {
            $this->command->info('Pages already exist. Skipping.');
            return;
        }

        DB::table('pages')->insert([
            'title'            => 'Privacy Policy',
            'slug'             => 'privacy-policy',
            'meta_title'       => 'Privacy Policy — Prosper Media',
            'meta_description' => 'How Prosper Media collects, uses, and protects your personal data.',
            'is_system'        => 1,
            'status'           => 'published',
            'content'          => 'managed_by_blade',
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        $this->command->info('Pages seeded.');
    }
}