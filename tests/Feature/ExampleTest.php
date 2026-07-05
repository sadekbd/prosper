<?php

namespace Tests\Feature;

use Database\Seeders\HomePageSectionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    private Migration $migration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        $this->migration = require database_path('migrations/2026_07_04_000001_create_page_sections_table.php');

        Schema::dropIfExists('page_sections');
        $this->migration->up();

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('status')->default('active');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Cache::put('site_settings_all', $this->settings(), 3600);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('services');
        $this->migration->down();

        parent::tearDown();
    }

    public function test_homepage_route_returns_successfully_with_isolated_sqlite_schema(): void
    {
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame('sqlite', DB::connection()->getDriverName());

        $this->seed(HomePageSectionSeeder::class);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Engineering Digital', false);
        $response->assertSee('Technical Precision.', false);
        $response->assertSee('Our Core Services', false);
        $response->assertSee('Ready to scale your business?', false);

        $this->assertSame('sqlite', DB::connection()->getDriverName());
    }

    /**
     * @return array<string, mixed>
     */
    private function settings(): array
    {
        return [
            'default_meta_title' => null,
            'default_meta_description' => null,
            'og_image' => null,
            'favicon' => null,
            'site_logo' => null,
            'site_name' => null,
            'google_analytics_id' => null,
            'facebook_url' => null,
            'linkedin_url' => null,
            'github_url' => null,
            'fiverr_url' => null,
            'business_email' => null,
            'whatsapp_number' => null,
            'business_address' => null,
        ];
    }
}
