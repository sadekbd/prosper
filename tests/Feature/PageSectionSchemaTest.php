<?php

namespace Tests\Feature;

use App\Models\PageSection;
use Illuminate\Database\QueryException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PageSectionSchemaTest extends TestCase
{
    private Migration $migration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migration = require database_path('migrations/2026_07_04_000001_create_page_sections_table.php');

        Schema::dropIfExists('page_sections');
        $this->migration->up();
    }

    protected function tearDown(): void
    {
        $this->migration->down();

        parent::tearDown();
    }

    public function test_page_sections_table_columns_and_model_behavior(): void
    {
        $this->assertTrue(Schema::hasTable('page_sections'));

        foreach ([
            'id',
            'page_key',
            'section_key',
            'eyebrow',
            'title',
            'subtitle',
            'body',
            'button_label',
            'button_url',
            'image',
            'payload',
            'status',
            'sort_order',
            'created_at',
            'updated_at',
        ] as $column) {
            $this->assertTrue(Schema::hasColumn('page_sections', $column), "Missing column: {$column}");
        }

        $section = PageSection::create([
            'page_key' => 'home',
            'section_key' => 'hero',
            'eyebrow' => 'Growth Partner',
            'title' => 'Engineering Digital Success',
            'subtitle' => 'Technical marketing systems for measurable growth.',
            'body' => 'Homepage hero body copy.',
            'button_label' => 'Get a Free Audit',
            'button_url' => '/contact',
            'image' => 'uploads/settings/logo.png',
            'payload' => [
                'stats' => [
                    ['label' => 'Projects Done', 'value' => '150+'],
                ],
            ],
            'status' => 'active',
            'sort_order' => 2,
        ]);

        $section->refresh();

        $this->assertSame('home', $section->page_key);
        $this->assertIsArray($section->payload);
        $this->assertSame('Projects Done', $section->payload['stats'][0]['label']);
        $this->assertSame(2, $section->sort_order);
        $this->assertSame($section->id, PageSection::active()->forPage('home')->first()->id);

        PageSection::create([
            'page_key' => 'home',
            'section_key' => 'cta',
            'title' => 'Ready to scale?',
            'status' => 'inactive',
            'sort_order' => 1,
        ]);

        PageSection::create([
            'page_key' => 'about',
            'section_key' => 'hero',
            'title' => 'About Prosper Media',
            'status' => 'active',
            'sort_order' => 0,
        ]);

        $orderedHomeSections = PageSection::forPage('home')->ordered()->pluck('section_key')->all();

        $this->assertSame(['cta', 'hero'], $orderedHomeSections);
        $this->assertSame(1, PageSection::active()->forPage('home')->count());
    }

    public function test_page_key_and_section_key_pair_must_be_unique(): void
    {
        PageSection::create([
            'page_key' => 'home',
            'section_key' => 'hero',
            'title' => 'First hero',
        ]);

        $this->expectException(QueryException::class);

        PageSection::create([
            'page_key' => 'home',
            'section_key' => 'hero',
            'title' => 'Duplicate hero',
        ]);
    }
}
