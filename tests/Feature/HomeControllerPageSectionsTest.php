<?php

namespace Tests\Feature;

use App\Http\Controllers\Public\HomeController;
use App\Models\PageSection;
use Database\Seeders\HomePageSectionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\View\View;
use Tests\TestCase;

class HomeControllerPageSectionsTest extends TestCase
{
    private Migration $migration;

    protected function setUp(): void
    {
        parent::setUp();

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
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('services');
        $this->migration->down();

        parent::tearDown();
    }

    public function test_home_controller_passes_keyed_active_home_sections_to_existing_view(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        PageSection::create([
            'page_key' => 'home',
            'section_key' => 'inactive_home_test',
            'title' => 'Inactive home test',
            'status' => 'inactive',
            'sort_order' => 5,
            'payload' => [],
        ]);

        PageSection::create([
            'page_key' => 'about',
            'section_key' => 'hero',
            'title' => 'About hero',
            'status' => 'active',
            'sort_order' => 5,
            'payload' => [],
        ]);

        $view = app(HomeController::class)->index();

        $expectedKeys = [
            'hero',
            'stats',
            'trust_bar',
            'difference',
            'services_intro',
            'portfolio_intro',
            'blog_intro',
            'primary_cta',
        ];

        $this->assertInstanceOf(View::class, $view);
        $this->assertSame('public.home', $view->name());
        $this->assertArrayHasKey('services', $view->getData());
        $this->assertArrayHasKey('portfolios', $view->getData());
        $this->assertArrayHasKey('articles', $view->getData());
        $this->assertArrayHasKey('homeSections', $view->getData());

        $homeSections = $view->getData()['homeSections'];

        $this->assertInstanceOf(Collection::class, $homeSections);
        $this->assertSame($expectedKeys, $homeSections->keys()->all());
        $this->assertSame([10, 20, 30, 40, 50, 60, 70, 80], $homeSections->pluck('sort_order')->all());
        $this->assertFalse($homeSections->has('inactive_home_test'));
        $this->assertSame('home', $homeSections->get('hero')->page_key);

        foreach ($homeSections as $sectionKey => $section) {
            $this->assertSame($sectionKey, $section->section_key);
            $this->assertSame('home', $section->page_key);
            $this->assertSame('active', $section->status);
        }
    }

    public function test_home_controller_passes_empty_keyed_collection_when_home_sections_are_missing(): void
    {
        PageSection::create([
            'page_key' => 'about',
            'section_key' => 'hero',
            'title' => 'About hero',
            'status' => 'active',
            'sort_order' => 10,
            'payload' => [],
        ]);

        $view = app(HomeController::class)->index();
        $homeSections = $view->getData()['homeSections'];

        $this->assertSame('public.home', $view->name());
        $this->assertInstanceOf(Collection::class, $homeSections);
        $this->assertTrue($homeSections->isEmpty());
    }
}
