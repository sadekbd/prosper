<?php

namespace Tests\Feature;

use App\Models\PageSection;
use Database\Seeders\HomePageSectionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HomePrimaryCtaBladeIntegrationTest extends TestCase
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

        Cache::put('site_settings_all', $this->settings(), 3600);
    }

    protected function tearDown(): void
    {
        $this->migration->down();

        parent::tearDown();
    }

    public function test_primary_cta_renders_seeded_content_links_and_proof_points(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $html = $this->primaryCtaHtml();

        $this->assertStringContainsString('Let&#039;s Work Together', $html);
        $this->assertStringContainsString('Ready to scale your business?', $html);
        $this->assertStringContainsString('<span class="text-gradient">Be Optimistic.</span>', $html);
        $this->assertStringContainsString("We&#039;ve got the data covered. Let&#039;s engineer your digital success together with the precision your business deserves.", $html);
        $this->assertStringContainsString('Start Growing Today', $html);
        $this->assertStringContainsString('href="http://localhost:8000/contact"', $html);
        $this->assertStringContainsString('See Our Work', $html);
        $this->assertStringContainsString('href="http://localhost:8000/portfolio"', $html);
        $this->assertProofPointsInOrder($html, [
            'No Long-Term Contracts',
            'Free Audit Consultation',
            'ROI-Focused Approach',
            '100% Transparent Reporting',
        ]);
        $this->assertSame(4, substr_count($html, '<span class="flex items-center gap-2">'));
    }

    public function test_primary_cta_falls_back_when_record_is_missing_or_inactive(): void
    {
        $this->assertFallbackContent($this->primaryCtaHtml());

        $this->seed(HomePageSectionSeeder::class);

        PageSection::where('page_key', 'home')
            ->where('section_key', 'primary_cta')
            ->update(['status' => 'inactive']);

        $this->assertFallbackContent($this->primaryCtaHtml());
    }

    public function test_malformed_title_lines_and_short_proof_points_fall_back_safely(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $section = PageSection::where('page_key', 'home')->where('section_key', 'primary_cta')->firstOrFail();
        $payload = $section->payload;
        $payload['title_lines'] = ['Only one valid line'];
        $payload['proof_points'] = ['One', 'Two'];
        $section->update(['payload' => $payload]);

        $html = $this->primaryCtaHtml();

        $this->assertFallbackContent($html);
        $this->assertStringNotContainsString('Only one valid line', $html);
        $this->assertSame(4, substr_count($html, '<span class="flex items-center gap-2">'));
    }

    public function test_more_than_four_proof_points_do_not_change_rendered_count(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $section = PageSection::where('page_key', 'home')->where('section_key', 'primary_cta')->firstOrFail();
        $payload = $section->payload;
        $payload['proof_points'] = [
            'One Proof',
            'Two Proof',
            'Three Proof',
            'Four Proof',
            'Five Proof',
        ];
        $section->update(['payload' => $payload]);

        $html = $this->primaryCtaHtml();

        $this->assertProofPointsInOrder($html, ['One Proof', 'Two Proof', 'Three Proof', 'Four Proof']);
        $this->assertStringNotContainsString('Five Proof', $html);
        $this->assertSame(4, substr_count($html, '<span class="flex items-center gap-2">'));
    }

    public function test_unsafe_button_urls_and_html_text_are_rejected(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        PageSection::where('page_key', 'home')
            ->where('section_key', 'primary_cta')
            ->firstOrFail()
            ->update([
                'eyebrow' => '<strong>Unsafe Eyebrow</strong>',
                'subtitle' => '<script>alert(1)</script>',
                'button_label' => '<em>Unsafe Primary</em>',
                'button_url' => 'javascript:alert(1)',
                'payload' => [
                    'title_lines' => ['<strong>Unsafe Title</strong>', 'Be Optimistic.'],
                    'secondary_button' => [
                        'label' => '<span>Unsafe Secondary</span>',
                        'url' => 'data:text/html,bad',
                    ],
                    'proof_points' => [
                        '<strong>Unsafe Proof</strong>',
                        'Two',
                        'Three',
                        'Four',
                    ],
                ],
            ]);

        $html = $this->primaryCtaHtml();

        $this->assertFallbackContent($html);
        $this->assertStringContainsString('href="http://localhost:8000/contact"', $html);
        $this->assertStringContainsString('href="http://localhost:8000/portfolio"', $html);
        $this->assertStringNotContainsString('javascript:alert(1)', $html);
        $this->assertStringNotContainsString('data:text/html,bad', $html);
        $this->assertStringNotContainsString('<strong>Unsafe Eyebrow</strong>', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringNotContainsString('<em>Unsafe Primary</em>', $html);
        $this->assertStringNotContainsString('<span>Unsafe Secondary</span>', $html);
        $this->assertStringNotContainsString('<strong>Unsafe Proof</strong>', $html);
    }

    private function assertFallbackContent(string $html): void
    {
        $this->assertStringContainsString('Let&#039;s Work Together', $html);
        $this->assertStringContainsString('Ready to scale your business?', $html);
        $this->assertStringContainsString('Be Optimistic.', $html);
        $this->assertStringContainsString("We&#039;ve got the data covered. Let&#039;s engineer your digital success together with the precision your business deserves.", $html);
        $this->assertStringContainsString('Start Growing Today', $html);
        $this->assertStringContainsString('See Our Work', $html);
        $this->assertProofPointsInOrder($html, [
            'No Long-Term Contracts',
            'Free Audit Consultation',
            'ROI-Focused Approach',
            '100% Transparent Reporting',
        ]);
    }

    private function assertProofPointsInOrder(string $html, array $expected): void
    {
        $lastPosition = -1;

        foreach ($expected as $proof) {
            $position = strpos($html, $proof);

            $this->assertNotFalse($position, "Missing proof point: {$proof}");
            $this->assertGreaterThan($lastPosition, $position, "Proof point out of order: {$proof}");

            $lastPosition = $position;
        }
    }

    private function primaryCtaHtml(): string
    {
        preg_match('/<section class="bg-cta-gradient relative overflow-hidden py-28">(.*?)<\/section>/s', $this->renderHome(), $matches);

        return $matches[1] ?? '';
    }

    private function renderHome(): string
    {
        return view('public.home', [
            'settings' => $this->settings(),
            'services' => collect(),
            'portfolios' => collect(),
            'articles' => collect(),
            'homeSections' => PageSection::query()
                ->active()
                ->forPage('home')
                ->ordered()
                ->get()
                ->keyBy('section_key'),
        ])->render();
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
