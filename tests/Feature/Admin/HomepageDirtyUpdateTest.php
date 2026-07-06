<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\PageSection;
use App\Support\HomepageContent;
use Database\Seeders\HomePageSectionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HomepageDirtyUpdateTest extends TestCase
{
    private Migration $pageSectionsMigration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'session.driver' => 'array',
        ]);

        $this->pageSectionsMigration = require database_path('migrations/2026_07_04_000001_create_page_sections_table.php');
        $this->createTables();
        Cache::put('site_settings_all', [], 3600);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('admin_users');
        $this->pageSectionsMigration->down();

        parent::tearDown();
    }

    public function test_single_section_change_updates_only_that_section_and_logs_once(): void
    {
        $this->seed(HomePageSectionSeeder::class);
        $this->setSectionTimestamps('2026-01-01 00:00:00');

        $before = $this->sectionSnapshot();

        Carbon::setTestNow('2026-01-01 00:10:00');

        $this->actingAs($this->adminUser(), 'admin')
            ->put(route('admin.homepage.update'), $this->validPayload([
                'hero.eyebrow' => 'Be Optimistic Test',
            ]))
            ->assertRedirect(route('admin.homepage.edit'))
            ->assertSessionHas('success', 'Homepage content updated successfully.');

        $after = $this->sectionSnapshot();

        $this->assertSame('Be Optimistic Test', PageSection::where('section_key', 'hero')->value('eyebrow'));
        $this->assertNotSame($before['hero']['updated_at'], $after['hero']['updated_at']);

        foreach (array_diff(HomepageContent::SECTION_KEYS, ['hero']) as $sectionKey) {
            $this->assertSame($before[$sectionKey], $after[$sectionKey], "{$sectionKey} should not be touched.");
        }

        $this->assertSame(1, DB::table('activity_logs')->where('action', 'updated_homepage_sections')->count());
    }

    public function test_no_op_save_does_not_touch_sections_or_log_activity(): void
    {
        $this->seed(HomePageSectionSeeder::class);
        $this->setSectionTimestamps('2026-01-01 00:00:00');

        $before = $this->sectionSnapshot();

        Carbon::setTestNow('2026-01-01 00:10:00');

        $this->actingAs($this->adminUser(), 'admin')
            ->put(route('admin.homepage.update'), $this->validPayload())
            ->assertRedirect(route('admin.homepage.edit'))
            ->assertSessionHas('success', 'Homepage content is already up to date.');

        $this->assertSame($before, $this->sectionSnapshot());
        $this->assertSame(0, DB::table('activity_logs')->count());
    }

    public function test_multiple_section_change_updates_only_changed_sections_and_logs_once(): void
    {
        $this->seed(HomePageSectionSeeder::class);
        $this->setSectionTimestamps('2026-01-01 00:00:00');

        $before = $this->sectionSnapshot();

        Carbon::setTestNow('2026-01-01 00:10:00');

        $this->actingAs($this->adminUser(), 'admin')
            ->put(route('admin.homepage.update'), $this->validPayload([
                'hero.eyebrow' => 'Be Optimistic Test',
                'primary_cta.eyebrow' => 'Ready When You Are',
            ]))
            ->assertRedirect(route('admin.homepage.edit'))
            ->assertSessionHas('success', 'Homepage content updated successfully.');

        $after = $this->sectionSnapshot();

        foreach (['hero', 'primary_cta'] as $sectionKey) {
            $this->assertNotSame($before[$sectionKey]['updated_at'], $after[$sectionKey]['updated_at']);
        }

        foreach (array_diff(HomepageContent::SECTION_KEYS, ['hero', 'primary_cta']) as $sectionKey) {
            $this->assertSame($before[$sectionKey], $after[$sectionKey], "{$sectionKey} should not be touched.");
        }

        $this->assertSame(1, DB::table('activity_logs')->where('action', 'updated_homepage_sections')->count());
    }

    public function test_missing_record_is_recreated_without_touching_unchanged_existing_records(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $payload = $this->validPayload();

        PageSection::where('section_key', 'blog_intro')->delete();
        $this->setSectionTimestamps('2026-01-01 00:00:00');

        $before = $this->sectionSnapshot();

        Carbon::setTestNow('2026-01-01 00:10:00');

        $this->actingAs($this->adminUser(), 'admin')
            ->put(route('admin.homepage.update'), $payload)
            ->assertRedirect(route('admin.homepage.edit'))
            ->assertSessionHas('success', 'Homepage content updated successfully.');

        $this->assertSame(8, PageSection::where('page_key', 'home')->count());
        $this->assertDatabaseHas('page_sections', ['page_key' => 'home', 'section_key' => 'blog_intro']);

        $after = $this->sectionSnapshot();

        foreach (array_diff(HomepageContent::SECTION_KEYS, ['blog_intro']) as $sectionKey) {
            $this->assertSame($before[$sectionKey], $after[$sectionKey], "{$sectionKey} should not be touched.");
        }

        $this->assertSame(1, DB::table('activity_logs')->where('action', 'updated_homepage_sections')->count());
    }

    public function test_transaction_rolls_back_dirty_writes_and_activity_log_on_failure(): void
    {
        $this->seed(HomePageSectionSeeder::class);
        $this->setSectionTimestamps('2026-01-01 00:00:00');

        $before = $this->sectionSnapshot();

        DB::unprepared("
            CREATE TRIGGER fail_primary_cta_update
            BEFORE UPDATE ON page_sections
            WHEN NEW.section_key = 'primary_cta'
            BEGIN
                SELECT RAISE(ABORT, 'forced primary_cta failure');
            END
        ");

        Carbon::setTestNow('2026-01-01 00:10:00');

        $this->actingAs($this->adminUser(), 'admin')
            ->put(route('admin.homepage.update'), $this->validPayload([
                'hero.eyebrow' => 'Should Roll Back',
                'primary_cta.eyebrow' => 'Forced Failure',
            ]))
            ->assertStatus(500);

        $this->assertSame($before, $this->sectionSnapshot());
        $this->assertSame(0, DB::table('activity_logs')->count());
    }

    public function test_equivalent_json_shapes_do_not_create_false_dirty_updates(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $stats = PageSection::where('section_key', 'stats')->firstOrFail();
        $stats->forceFill([
            'payload' => [
                'items' => [
                    ['label' => 'Projects Done', 'suffix' => '+', 'value' => 150],
                    ['label' => 'Client Satisfaction', 'suffix' => '%', 'value' => 98],
                    ['label' => 'Average ROAS', 'suffix' => 'x', 'value' => 5],
                    ['label' => 'Experience', 'sep' => 'yr', 'suffix' => '+', 'value' => 3],
                ],
            ],
        ])->save();

        $this->setSectionTimestamps('2026-01-01 00:00:00');
        $before = $this->sectionSnapshot();

        Carbon::setTestNow('2026-01-01 00:10:00');

        $this->actingAs($this->adminUser(), 'admin')
            ->put(route('admin.homepage.update'), $this->validPayload())
            ->assertRedirect(route('admin.homepage.edit'))
            ->assertSessionHas('success', 'Homepage content is already up to date.');

        $this->assertSame($before, $this->sectionSnapshot());
        $this->assertSame(0, DB::table('activity_logs')->count());
    }

    public function test_mysql_loaded_repeated_payload_representation_does_not_create_false_dirty_updates(): void
    {
        $this->seed(HomePageSectionSeeder::class);
        $this->applyMysqlRepeatedPayloadRepresentation();
        $this->setSectionTimestamps('2026-01-01 00:00:00');

        $before = $this->sectionSnapshot();

        Carbon::setTestNow('2026-01-01 00:10:00');

        $this->actingAs($this->adminUser(), 'admin')
            ->put(route('admin.homepage.update'), $this->validPayload([
                'hero.eyebrow' => 'Be Optimistic Test',
            ]))
            ->assertRedirect(route('admin.homepage.edit'))
            ->assertSessionHas('success', 'Homepage content updated successfully.');

        $after = $this->sectionSnapshot();

        $this->assertSame('Be Optimistic Test', PageSection::where('section_key', 'hero')->value('eyebrow'));
        $this->assertNotSame($before['hero']['updated_at'], $after['hero']['updated_at']);

        foreach (['stats', 'trust_bar', 'difference', 'services_intro', 'primary_cta'] as $sectionKey) {
            $this->assertSame($before[$sectionKey], $after[$sectionKey], "{$sectionKey} should not be touched by equivalent MySQL payloads.");
        }

        $this->assertSame(1, DB::table('activity_logs')->where('action', 'updated_homepage_sections')->count());
    }

    public function test_meaningful_zero_values_are_preserved_and_numeric_changes_are_not_hidden(): void
    {
        $this->seed(HomePageSectionSeeder::class);
        $this->setSectionTimestamps('2026-01-01 00:00:00');

        Carbon::setTestNow('2026-01-01 00:10:00');

        $this->actingAs($this->adminUser(), 'admin')
            ->put(route('admin.homepage.update'), $this->validPayload([
                'stats.items.0.value' => '0',
            ]))
            ->assertRedirect(route('admin.homepage.edit'))
            ->assertSessionHas('success', 'Homepage content updated successfully.');

        $stats = PageSection::where('section_key', 'stats')->firstOrFail();

        $this->assertSame('0', $stats->payload['items'][0]['value']);
        $this->assertSame(1, DB::table('activity_logs')->where('action', 'updated_homepage_sections')->count());
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        $sections = PageSection::query()
            ->forPage('home')
            ->ordered()
            ->get()
            ->keyBy('section_key');

        $content = app(HomepageContent::class);
        $hero = $content->hero($sections->get('hero'));
        $stats = $content->stats($sections->get('stats'));
        $trustBar = $content->trustBar($sections->get('trust_bar'));
        $difference = $content->difference($sections->get('difference'));
        $servicesIntro = $content->servicesIntro($sections->get('services_intro'));
        $portfolioIntro = $content->portfolioIntro($sections->get('portfolio_intro'));
        $blogIntro = $content->blogIntro($sections->get('blog_intro'));
        $primaryCta = $content->primaryCta($sections->get('primary_cta'));

        $payload = [
            'hero' => [
                'eyebrow' => $hero['eyebrow'],
                'title_lines' => $hero['title_lines'],
                'subtitle' => $hero['subtitle'],
                'primary_label' => $hero['primary_label'],
                'primary_route' => $this->routeNameForUrl($sections->get('hero')?->button_url, 'services'),
                'primary_url' => $sections->get('hero')?->button_url ?? '/services',
                'secondary_button' => [
                    'label' => $hero['secondary_label'],
                    'route' => data_get($sections->get('hero')?->payload, 'secondary_button.route', 'contact'),
                    'url' => data_get($sections->get('hero')?->payload, 'secondary_button.url', '/contact'),
                ],
                'dashboard' => [
                    'eyebrow' => $hero['dashboard']['eyebrow'],
                    'title' => $hero['dashboard']['title'],
                    'status' => $hero['dashboard']['status'],
                    'metrics' => $hero['dashboard']['metrics'],
                    'chart' => [
                        'label' => $hero['dashboard']['chart_label'],
                        'days' => $hero['dashboard']['chart_days'],
                        'bar_heights' => $hero['dashboard']['bar_heights'],
                    ],
                    'tracking' => [
                        'label' => $hero['dashboard']['tracking_label'],
                        'items' => $hero['dashboard']['tracking_items'],
                    ],
                    'floating_badges' => [
                        [
                            'label' => data_get($hero, 'dashboard.top_badge.label', ''),
                            'value' => data_get($hero, 'dashboard.top_badge.value', ''),
                        ],
                        [
                            'label' => data_get($hero, 'dashboard.bottom_badge.label', ''),
                            'value' => data_get($hero, 'dashboard.bottom_badge.value', ''),
                        ],
                    ],
                ],
            ],
            'stats' => [
                'items' => array_map(fn (array $item) => [
                    'value' => (string) $item['value'],
                    'suffix' => $item['suffix'],
                    'sep' => $item['sep'] ?? '',
                    'label' => $item['label'],
                ], $stats['items']),
            ],
            'trust_bar' => $trustBar,
            'difference' => [
                'eyebrow' => $difference['eyebrow'],
                'title' => $difference['title'],
                'subtitle' => $difference['subtitle'],
                'cards' => array_map(fn (array $card) => [
                    'badge' => $card['badge'],
                    'color' => $card['color'],
                    'icon_path' => $card['icon'],
                    'title' => $card['title'],
                    'body' => $card['desc'],
                ], $difference['cards']),
            ],
            'services_intro' => [
                'eyebrow' => $servicesIntro['eyebrow'],
                'title' => $servicesIntro['title'],
                'subtitle' => $servicesIntro['subtitle'],
                'cta_label' => $servicesIntro['cta_label'],
                'cta_route' => $this->routeNameForUrl($sections->get('services_intro')?->button_url, 'services'),
                'cta_url' => $sections->get('services_intro')?->button_url ?? '/services',
                'card_link_label' => $servicesIntro['card_link_label'],
                'card_icons' => $servicesIntro['card_icons'],
            ],
            'portfolio_intro' => [
                'eyebrow' => $portfolioIntro['eyebrow'],
                'title' => $portfolioIntro['title'],
                'subtitle' => $portfolioIntro['subtitle'],
                'cta_label' => $portfolioIntro['cta_label'],
                'cta_route' => $this->routeNameForUrl($sections->get('portfolio_intro')?->button_url, 'portfolio'),
                'cta_url' => $sections->get('portfolio_intro')?->button_url ?? '/portfolio',
                'card_link_label' => $portfolioIntro['card_link_label'],
            ],
            'blog_intro' => [
                'eyebrow' => $blogIntro['eyebrow'],
                'title' => $blogIntro['title'],
                'subtitle' => $blogIntro['subtitle'],
                'cta_label' => $blogIntro['cta_label'],
                'cta_route' => $this->routeNameForUrl($sections->get('blog_intro')?->button_url, 'blog'),
                'cta_url' => $sections->get('blog_intro')?->button_url ?? '/blog',
                'card_link_label' => $blogIntro['card_link_label'],
            ],
            'primary_cta' => [
                'eyebrow' => $primaryCta['eyebrow'],
                'title_lines' => $primaryCta['title_lines'],
                'subtitle' => $primaryCta['subtitle'],
                'primary_label' => $primaryCta['primary_label'],
                'primary_route' => $this->routeNameForUrl($sections->get('primary_cta')?->button_url, 'contact'),
                'primary_url' => $sections->get('primary_cta')?->button_url ?? '/contact',
                'secondary_button' => [
                    'label' => $primaryCta['secondary_label'],
                    'route' => data_get($sections->get('primary_cta')?->payload, 'secondary_button.route', 'portfolio'),
                    'url' => data_get($sections->get('primary_cta')?->payload, 'secondary_button.url', '/portfolio'),
                ],
                'proof_points' => $primaryCta['proof_points'],
            ],
        ];

        foreach ($overrides as $path => $value) {
            data_set($payload, $path, $value);
        }

        return $payload;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function sectionSnapshot(): array
    {
        return PageSection::query()
            ->forPage('home')
            ->ordered()
            ->get()
            ->mapWithKeys(fn (PageSection $section) => [
                $section->section_key => [
                    'updated_at' => $section->updated_at?->toDateTimeString(),
                    'attributes' => [
                        'page_key' => $section->page_key,
                        'section_key' => $section->section_key,
                        'eyebrow' => $section->eyebrow,
                        'title' => $section->title,
                        'subtitle' => $section->subtitle,
                        'body' => $section->body,
                        'button_label' => $section->button_label,
                        'button_url' => $section->button_url,
                        'image' => $section->image,
                        'payload' => $section->payload,
                        'status' => $section->status,
                        'sort_order' => $section->sort_order,
                    ],
                ],
            ])
            ->all();
    }

    private function setSectionTimestamps(string $timestamp): void
    {
        PageSection::query()->update([
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }

    private function applyMysqlRepeatedPayloadRepresentation(): void
    {
        PageSection::where('section_key', 'stats')->firstOrFail()->forceFill([
            'payload' => [
                'items' => [
                    ['sep' => null, 'label' => 'Client Satisfaction', 'value' => '98', 'suffix' => '%'],
                    ['sep' => null, 'label' => 'Average ROAS', 'value' => '5', 'suffix' => 'x'],
                    ['sep' => null, 'label' => 'Projects Done', 'value' => '150', 'suffix' => '+'],
                    ['sep' => 'yr', 'label' => 'Experience', 'value' => '3', 'suffix' => '+'],
                ],
            ],
        ])->save();

        PageSection::where('section_key', 'trust_bar')->firstOrFail()->forceFill([
            'payload' => [
                'items' => [
                    ['color' => '#777BB4', 'label' => 'PHP 8'],
                    ['color' => '#E37400', 'label' => 'Analytics GA4'],
                    ['color' => '#4285F4', 'label' => 'Google Ads'],
                    ['color' => '#F57C00', 'label' => 'Tag Manager'],
                    ['color' => '#FF2D20', 'label' => 'Laravel'],
                    ['color' => '#1877F2', 'label' => 'Meta Pixel'],
                    ['color' => '#4479A1', 'label' => 'MySQL'],
                ],
            ],
        ])->save();

        PageSection::where('section_key', 'difference')->firstOrFail()->forceFill([
            'payload' => [
                'cards' => [
                    [
                        'body' => "Clear reporting, measurable results, and honest technical support. You always know exactly what is happening with your campaigns and why it's working.",
                        'badge' => 'No. 03',
                        'color' => 'cyan',
                        'title' => 'Transparent Data',
                        'icon_path' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                    ],
                    [
                        'body' => 'Every click is treated as an investment, not an expense. We obsess over ROAS, CPA, and conversion rates â€” building campaigns that compound in profitability over time.',
                        'badge' => 'No. 02',
                        'color' => 'gold',
                        'title' => 'ROI Focused',
                        'icon_path' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                    ],
                    [
                        'body' => 'We code the tracking setup that other agencies miss. Every pixel, every event, every conversion â€” captured with precision using GTM, server-side tracking, and custom API integrations.',
                        'badge' => 'No. 01',
                        'color' => 'cyan',
                        'title' => 'Tech-First Marketing',
                        'icon_path' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
                    ],
                ],
            ],
        ])->save();

        PageSection::where('section_key', 'services_intro')->firstOrFail()->forceFill([
            'payload' => [
                'card_icons' => [
                    'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z',
                    'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                    'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
                ],
                'card_link_label' => 'Learn More',
            ],
        ])->save();

        PageSection::where('section_key', 'primary_cta')->firstOrFail()->forceFill([
            'title' => "Be Optimistic.\nReady to scale your business?",
            'payload' => [
                'title_lines' => ['Be Optimistic.', 'Ready to scale your business?'],
                'proof_points' => [
                    'No Long-Term Contracts',
                    'Free Audit Consultation',
                    'ROI-Focused Approach',
                    '100% Transparent Reporting',
                ],
                'secondary_button' => [
                    'url' => '/portfolio',
                    'label' => 'See Our Work',
                    'route' => 'portfolio',
                ],
            ],
        ])->save();
    }

    private function routeNameForUrl(?string $url, string $fallback): string
    {
        return match ($url) {
            '/' => 'home',
            '/services' => 'services',
            '/portfolio' => 'portfolio',
            '/blog' => 'blog',
            '/contact' => 'contact',
            default => $fallback,
        };
    }

    private function adminUser(): AdminUser
    {
        return AdminUser::create([
            'username' => 'admin_' . uniqid(),
            'full_name' => 'Admin User',
            'email' => 'admin' . uniqid() . '@example.test',
            'mobile' => '123456789',
            'password' => 'password',
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    private function createTables(): void
    {
        Schema::dropIfExists('page_sections');
        $this->pageSectionsMigration->up();

        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('mobile')->nullable();
            $table->string('password');
            $table->string('role');
            $table->string('status')->default('active');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action');
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }
}
