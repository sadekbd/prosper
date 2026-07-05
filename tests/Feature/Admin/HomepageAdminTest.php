<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\PageSection;
use App\Support\HomepageContent;
use Database\Seeders\HomePageSectionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HomepageAdminTest extends TestCase
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
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('blog_articles');
        Schema::dropIfExists('portfolio_projects');
        Schema::dropIfExists('services');
        Schema::dropIfExists('admin_users');
        $this->pageSectionsMigration->down();

        parent::tearDown();
    }

    public function test_authorization_for_homepage_editor(): void
    {
        $this->get(route('admin.homepage.edit'))
            ->assertRedirect(route('admin.login'));

        $this->actingAs($this->adminUser('super_admin'), 'admin')
            ->get(route('admin.homepage.edit'))
            ->assertOk()
            ->assertSee('Homepage Content')
            ->assertSee('Hero')
            ->assertSee('Final CTA');

        $this->actingAs($this->adminUser('admin'), 'admin')
            ->get(route('admin.homepage.edit'))
            ->assertOk();

        $writer = $this->adminUser('article_writer');

        $this->actingAs($writer, 'admin')
            ->get(route('admin.homepage.edit'))
            ->assertForbidden();

        $this->actingAs($writer, 'admin')
            ->put(route('admin.homepage.update'), $this->validPayload())
            ->assertForbidden();
    }

    public function test_edit_screen_loads_seeded_values_defaults_and_does_not_create_missing_records(): void
    {
        $this->seed(HomePageSectionSeeder::class);
        PageSection::where('section_key', 'blog_intro')->delete();

        $before = PageSection::count();

        $response = $this->actingAs($this->adminUser('admin'), 'admin')
            ->get(route('admin.homepage.edit'));

        $response->assertOk();
        $response->assertSee('Be Optimistic');
        $response->assertSee('Projects Done');
        $response->assertSee('Technologies &amp; Platforms We Master', false);
        $response->assertSee('Knowledge Hub');
        $response->assertSee('Preview Homepage');
        $response->assertSee('Save Homepage');
        $response->assertSee('SVG path data only');
        $response->assertDontSee('Delete', false);

        $this->assertSame($before, PageSection::count());
        $this->assertDatabaseMissing('page_sections', ['section_key' => 'blog_intro']);
    }

    public function test_successful_update_writes_all_fixed_sections_recreates_missing_record_and_logs_activity(): void
    {
        $this->seed(HomePageSectionSeeder::class);
        PageSection::where('section_key', 'blog_intro')->delete();

        $payload = $this->validPayload([
            'hero.eyebrow' => 'Updated Hero Eyebrow',
            'hero.title_lines.0' => 'Updated Digital',
            'stats.items.0.value' => '175',
            'trust_bar.items.0.label' => 'Google Ads Pro',
            'difference.cards.1.title' => 'Updated ROI Focused',
            'services_intro.card_link_label' => 'Explore Service',
            'portfolio_intro.card_link_label' => 'Open Case Study',
            'blog_intro.card_link_label' => 'Open Article',
            'primary_cta.proof_points.0' => 'Updated Proof Point',
        ]);

        $this->actingAs($this->adminUser('admin'), 'admin')
            ->put(route('admin.homepage.update'), $payload)
            ->assertRedirect(route('admin.homepage.edit'))
            ->assertSessionHas('success', 'Homepage content updated successfully.');

        $this->assertSame(8, PageSection::where('page_key', 'home')->count());
        $this->assertSame(8, PageSection::query()->select('page_key', 'section_key')->distinct()->count());
        $this->assertSame([10, 20, 30, 40, 50, 60, 70, 80], PageSection::ordered()->pluck('sort_order')->all());
        $this->assertTrue(PageSection::where('page_key', 'home')->get()->every(fn (PageSection $section) => $section->status === 'active'));

        $hero = PageSection::where('section_key', 'hero')->firstOrFail();
        $this->assertSame('Updated Hero Eyebrow', $hero->eyebrow);
        $this->assertSame('Updated Digital Success with Technical Precision.', $hero->title);
        $this->assertSame('/services', $hero->button_url);
        $this->assertSame('Get a Free Audit', $hero->payload['secondary_button']['label']);

        $this->assertDatabaseHas('page_sections', ['section_key' => 'blog_intro']);
        $this->assertDatabaseHas('activity_logs', ['action' => 'updated_homepage_sections']);
        $this->assertSame(1, DB::table('services')->count());
        $this->assertSame(1, DB::table('portfolio_projects')->count());
        $this->assertSame(1, DB::table('blog_articles')->count());

        $normalized = app(HomepageContent::class)->hero($hero);
        $this->assertSame('Updated Hero Eyebrow', $normalized['eyebrow']);
        $this->assertSame('Updated Digital', $normalized['title_lines'][0]);
    }

    public function test_transaction_rolls_back_when_one_section_write_fails(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $originalHeroEyebrow = PageSection::where('section_key', 'hero')->value('eyebrow');

        DB::unprepared("
            CREATE TRIGGER fail_blog_intro_update
            BEFORE UPDATE ON page_sections
            WHEN NEW.section_key = 'blog_intro'
            BEGIN
                SELECT RAISE(ABORT, 'forced blog_intro failure');
            END
        ");

        $this->actingAs($this->adminUser('admin'), 'admin')
            ->put(route('admin.homepage.update'), $this->validPayload([
                'hero.eyebrow' => 'Should Roll Back',
                'blog_intro.title' => 'Forced Blog Failure',
            ]))
            ->assertStatus(500);

        $this->assertSame($originalHeroEyebrow, PageSection::where('section_key', 'hero')->value('eyebrow'));
        $this->assertSame(0, DB::table('activity_logs')->count());
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        $payload = [
            'hero' => [
                'eyebrow' => 'Be Optimistic',
                'title_lines' => ['Engineering Digital', 'Success', 'with', 'Technical Precision.'],
                'subtitle' => 'Professional Google Ads management, conversion tracking, and high-performance web development built to turn visitors into loyal customers.',
                'primary_label' => 'View Our Services',
                'primary_route' => 'services',
                'primary_url' => '/services',
                'secondary_button' => ['label' => 'Get a Free Audit', 'route' => 'contact', 'url' => '/contact'],
                'dashboard' => [
                    'eyebrow' => 'Live Dashboard',
                    'title' => 'Q2 Campaign Performance',
                    'status' => 'Live',
                    'metrics' => [
                        ['label' => 'Conversions', 'value' => '1,248', 'change' => '↑ 34%', 'color' => 'text-pm-cyan'],
                        ['label' => 'ROAS', 'value' => '4.8x', 'change' => '↑ 12%', 'color' => 'text-pm-gold'],
                        ['label' => 'CTR', 'value' => '7.2%', 'change' => '↑ 8%', 'color' => 'text-white'],
                    ],
                    'chart' => [
                        'label' => 'Weekly Conversions',
                        'days' => ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
                        'bar_heights' => [35, 55, 42, 70, 60, 85, 75],
                    ],
                    'tracking' => ['label' => 'Tracking:', 'items' => ['GTM', 'GA4', 'Meta API', 'Server-Side']],
                    'floating_badges' => [
                        ['label' => 'ROI', 'value' => '↑ 340%'],
                        ['label' => 'Tracking Active', 'value' => ''],
                    ],
                ],
            ],
            'stats' => [
                'items' => [
                    ['value' => '150', 'suffix' => '+', 'sep' => '', 'label' => 'Projects Done'],
                    ['value' => '98', 'suffix' => '%', 'sep' => '', 'label' => 'Client Satisfaction'],
                    ['value' => '5', 'suffix' => 'x', 'sep' => '', 'label' => 'Average ROAS'],
                    ['value' => '3', 'suffix' => '+', 'sep' => 'yr', 'label' => 'Experience'],
                ],
            ],
            'trust_bar' => [
                'title' => 'Technologies & Platforms We Master',
                'items' => [
                    ['label' => 'Google Ads', 'color' => '#4285F4'],
                    ['label' => 'Tag Manager', 'color' => '#F57C00'],
                    ['label' => 'Meta Pixel', 'color' => '#1877F2'],
                    ['label' => 'Laravel', 'color' => '#FF2D20'],
                    ['label' => 'MySQL', 'color' => '#4479A1'],
                    ['label' => 'Analytics GA4', 'color' => '#E37400'],
                    ['label' => 'PHP 8', 'color' => '#777BB4'],
                ],
            ],
            'difference' => [
                'eyebrow' => 'Why Choose Us',
                'title' => 'The Prosper Media Difference',
                'subtitle' => "We combine deep technical expertise with marketing intelligence to deliver results others simply can't match.",
                'cards' => [
                    ['badge' => 'No. 01', 'color' => 'cyan', 'icon_path' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4', 'title' => 'Tech-First Marketing', 'body' => 'We code the tracking setup that other agencies miss. Every pixel, every event, every conversion — captured with precision using GTM, server-side tracking, and custom API integrations.'],
                    ['badge' => 'No. 02', 'color' => 'gold', 'icon_path' => 'M9 19v-6a2 2 0 00-2-2H5a2', 'title' => 'ROI Focused', 'body' => 'Every click is treated as an investment, not an expense.'],
                    ['badge' => 'No. 03', 'color' => 'cyan', 'icon_path' => 'M15 12a3 3 0 11-6 0', 'title' => 'Transparent Data', 'body' => 'Clear reporting, measurable results, and honest technical support.'],
                ],
            ],
            'services_intro' => [
                'eyebrow' => 'What We Do',
                'title' => 'Our Core Services',
                'subtitle' => 'Three pillars of technical excellence powering your entire digital growth engine.',
                'cta_label' => 'View All Services',
                'cta_route' => 'services',
                'cta_url' => '/services',
                'card_link_label' => 'Learn More',
                'card_icons' => [
                    'M9 19v-6a2 2 0 00-2-2H5a2',
                    'M9 3v2m6-2v2M9 19v2m6-2v2',
                    'M10 20l4-16m4 4l4 4-4 4',
                ],
            ],
            'portfolio_intro' => [
                'eyebrow' => 'Our Work',
                'title' => 'Recent Projects',
                'subtitle' => 'Real results from real campaigns. See how we engineer digital success.',
                'cta_label' => 'All Projects',
                'cta_route' => 'portfolio',
                'cta_url' => '/portfolio',
                'card_link_label' => 'View Case Study',
            ],
            'blog_intro' => [
                'eyebrow' => 'Knowledge Hub',
                'title' => 'Latest Articles',
                'subtitle' => 'Practical insights, tutorials and guides from our technical team.',
                'cta_label' => 'All Articles',
                'cta_route' => 'blog',
                'cta_url' => '/blog',
                'card_link_label' => 'Read Article',
            ],
            'primary_cta' => [
                'eyebrow' => "Let's Work Together",
                'title_lines' => ['Ready to scale your business?', 'Be Optimistic.'],
                'subtitle' => "We've got the data covered. Let's engineer your digital success together with the precision your business deserves.",
                'primary_label' => 'Start Growing Today',
                'primary_route' => 'contact',
                'primary_url' => '/contact',
                'secondary_button' => ['label' => 'See Our Work', 'route' => 'portfolio', 'url' => '/portfolio'],
                'proof_points' => ['No Long-Term Contracts', 'Free Audit Consultation', 'ROI-Focused Approach', '100% Transparent Reporting'],
            ],
        ];

        foreach ($overrides as $path => $value) {
            data_set($payload, $path, $value);
        }

        return $payload;
    }

    private function adminUser(string $role): AdminUser
    {
        return AdminUser::create([
            'username' => $role . '_' . uniqid(),
            'full_name' => ucfirst(str_replace('_', ' ', $role)),
            'email' => $role . uniqid() . '@example.test',
            'mobile' => '123456789',
            'password' => 'password',
            'role' => $role,
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

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
        });
        DB::table('services')->insert(['title' => 'Existing Service']);

        Schema::create('portfolio_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
        });
        DB::table('portfolio_projects')->insert(['title' => 'Existing Project']);

        Schema::create('blog_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
        });
        DB::table('blog_articles')->insert(['title' => 'Existing Article']);
    }
}
