<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use Database\Seeders\HomePageSectionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HomepageUpdateValidationTest extends TestCase
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
        $this->seed(HomePageSectionSeeder::class);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('admin_users');
        $this->pageSectionsMigration->down();

        parent::tearDown();
    }

    public function test_rejects_unsafe_html_urls_classes_colors_svg_counts_and_domain_card_data(): void
    {
        $payload = $this->validPayload([
            'page_key' => 'home',
            'hero.eyebrow' => '<strong>Unsafe</strong>',
            'hero.primary_route' => '',
            'hero.primary_url' => 'javascript:alert(1)',
            'hero.dashboard.metrics.0.color' => 'text-red-evil arbitrary-class',
            'hero.dashboard.chart.bar_heights.0' => 999,
            'stats.items.0.suffix' => 'bad',
            'trust_bar.items.0.color' => '#000000',
            'difference.cards.0.color' => 'purple',
            'difference.cards.0.icon_path' => '<svg onload="alert(1)"></svg>',
            'services_intro.card_icons.0' => 'M10 20; javascript',
            'primary_cta.title_lines' => ['Only one line'],
            'primary_cta.proof_points' => ['One', 'Two'],
            'portfolio_intro.projects' => [
                ['title' => 'Should Not Be Accepted'],
            ],
        ]);

        $this->actingAs($this->adminUser('admin'), 'admin')
            ->from(route('admin.homepage.edit'))
            ->put(route('admin.homepage.update'), $payload)
            ->assertRedirect(route('admin.homepage.edit'))
            ->assertSessionHasErrors([
                'page_key',
                'hero.eyebrow',
                'hero.primary_url',
                'hero.dashboard.metrics.0.color',
                'hero.dashboard.chart.bar_heights.0',
                'stats.items.0.suffix',
                'trust_bar.items.0.color',
                'difference.cards.0.color',
                'difference.cards.0',
                'services_intro.card_icons.0',
                'primary_cta.title_lines',
                'primary_cta.proof_points',
                'portfolio_intro',
            ]);
    }

    public function test_valid_arrows_punctuation_and_fixed_payload_shapes_are_accepted(): void
    {
        $payload = $this->validPayload([
            'hero.dashboard.metrics.0.change' => '↑ 44%',
            'hero.dashboard.floating_badges.0.value' => '↑ 400%',
            'difference.cards.0.body' => "Every click, every conversion — tracked with care.",
            'primary_cta.subtitle' => "We've got the data covered. Let's build something precise.",
        ]);

        $this->actingAs($this->adminUser('admin'), 'admin')
            ->put(route('admin.homepage.update'), $payload)
            ->assertRedirect(route('admin.homepage.edit'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('page_sections', [
            'page_key' => 'home',
            'section_key' => 'hero',
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'updated_homepage_sections',
        ]);
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
                    ['badge' => 'No. 01', 'color' => 'cyan', 'icon_path' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4', 'title' => 'Tech-First Marketing', 'body' => 'We code the tracking setup that other agencies miss.'],
                    ['badge' => 'No. 02', 'color' => 'gold', 'icon_path' => 'M9 19v-6a2 2 0 00-2-2H5a2', 'title' => 'ROI Focused', 'body' => 'Every click is treated as an investment.'],
                    ['badge' => 'No. 03', 'color' => 'cyan', 'icon_path' => 'M15 12a3 3 0 11-6 0', 'title' => 'Transparent Data', 'body' => 'Clear reporting.'],
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
                'card_icons' => ['M9 19v-6a2 2 0 00-2-2H5a2', 'M9 3v2m6-2v2M9 19v2m6-2v2', 'M10 20l4-16m4 4l4 4-4 4'],
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
    }
}
