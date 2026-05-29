<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class InstallProsperMedia extends Command
{
    protected $signature   = 'prosper:install';
    protected $description = 'Create all 12 Prosper Media database tables in one command';

    public function handle(): void
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════╗');
        $this->info('║     Prosper Media — Database Installer   ║');
        $this->info('╚══════════════════════════════════════════╝');
        $this->info('');

        // ── Drop all custom tables first (safe re-run) ──────────────
        $this->warn('⚡ Dropping existing custom tables...');
        $this->dropTables();

        // ── Create all 12 tables ────────────────────────────────────
        $tables = [
            'admin_users'              => fn() => $this->createAdminUsers(),
            'admin_password_resets'    => fn() => $this->createPasswordResets(),
            'site_settings'            => fn() => $this->createSiteSettings(),
            'services'                 => fn() => $this->createServices(),
            'service_features'         => fn() => $this->createServiceFeatures(),
            'portfolio_projects'       => fn() => $this->createPortfolioProjects(),
            'blog_categories'          => fn() => $this->createBlogCategories(),
            'blog_articles'            => fn() => $this->createBlogArticles(),
            'contact_messages'         => fn() => $this->createContactMessages(),
            'newsletter_subscribers'   => fn() => $this->createNewsletterSubscribers(),
            'pages'                    => fn() => $this->createPages(),
            'activity_logs'            => fn() => $this->createActivityLogs(),
        ];

        $this->info('');
        $this->info('🚀 Creating tables...');
        $this->info('');

        $bar = $this->output->createProgressBar(count($tables));
        $bar->start();

        foreach ($tables as $tableName => $creator) {
            try {
                $creator();
                $this->info('  ✅  ' . $tableName);
            } catch (\Exception $e) {
                $this->error('  ❌  ' . $tableName . ' — ' . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info('');
        $this->info('');

        // ── Run Seeders ─────────────────────────────────────────────
        if ($this->confirm('✅ All tables created. Run seeders now? (default data)', true)) {
            $this->info('');
            $this->call('db:seed');
        }

        $this->info('');
        $this->info('╔══════════════════════════════════════════╗');
        $this->info('║   ✅  Prosper Media is ready to build!   ║');
        $this->info('╚══════════════════════════════════════════╝');
        $this->info('');
    }

    // ════════════════════════════════════════════════════════════════
    // DROP — in reverse FK order so no constraint errors
    // ════════════════════════════════════════════════════════════════
    private function dropTables(): void
    {
        Schema::disableForeignKeyConstraints();

        $tables = [
            'activity_logs',
            'blog_articles',
            'blog_categories',
            'portfolio_projects',
            'service_features',
            'services',
            'newsletter_subscribers',
            'contact_messages',
            'pages',
            'site_settings',
            'admin_password_resets',
            'admin_users',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();
    }

    // ════════════════════════════════════════════════════════════════
    // TABLE 1 — admin_users
    // ════════════════════════════════════════════════════════════════
    private function createAdminUsers(): void
    {
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('full_name', 100);
            $table->string('email', 150)->unique();
            $table->string('mobile', 20);
            $table->string('password', 255);
            $table->enum('role', ['super_admin', 'admin', 'article_writer'])
                  ->default('article_writer');
            $table->enum('status', ['active', 'pending', 'blocked'])
                  ->default('pending');
            $table->string('profile_image', 255)->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->index('role');
            $table->index('status');
        });
    }

    // ════════════════════════════════════════════════════════════════
    // TABLE 2 — admin_password_resets
    // ════════════════════════════════════════════════════════════════
    private function createPasswordResets(): void
    {
        Schema::create('admin_password_resets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('admin_users')
                  ->onDelete('cascade');
            $table->string('token', 255);
            $table->timestamp('expires_at');
            $table->boolean('used')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->index('token');
            $table->index('user_id');
        });
    }

    // ════════════════════════════════════════════════════════════════
    // TABLE 3 — site_settings
    // ════════════════════════════════════════════════════════════════
    private function createSiteSettings(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('group', 50)->default('general');
            $table->string('label', 150);
            $table->enum('type', ['text','textarea','email','url','tel','image','boolean'])
                  ->default('text');
            $table->timestamps();
            $table->index('group');
        });
    }

    // ════════════════════════════════════════════════════════════════
    // TABLE 4 — services
    // ════════════════════════════════════════════════════════════════
    private function createServices(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('slug', 180)->unique();
            $table->string('subtitle', 255)->nullable();
            $table->text('description');
            $table->string('icon', 100)->nullable();
            $table->string('image', 255)->nullable();
            $table->string('meta_title', 200)->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->index('slug');
            $table->index('status');
        });
    }

    // ════════════════════════════════════════════════════════════════
    // TABLE 5 — service_features
    // ════════════════════════════════════════════════════════════════
    private function createServiceFeatures(): void
    {
        Schema::create('service_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')
                  ->constrained('services')
                  ->onDelete('cascade');
            $table->string('feature', 255);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index('service_id');
        });
    }

    // ════════════════════════════════════════════════════════════════
    // TABLE 6 — portfolio_projects
    // ════════════════════════════════════════════════════════════════
    private function createPortfolioProjects(): void
    {
        Schema::create('portfolio_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 230)->unique();
            $table->enum('category', [
                'google_ads',
                'tracking_setup',
                'web_development',
                'landing_page',
                'automation',
            ]);
            $table->string('short_description', 500);
            $table->longText('full_description')->nullable();
            $table->json('technologies')->nullable();
            $table->string('result_summary', 400)->nullable();
            $table->string('client_name', 150)->nullable();
            $table->string('project_url', 255)->nullable();
            $table->string('featured_image', 255)->nullable();
            $table->json('gallery_images')->nullable();
            $table->string('meta_title', 200)->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->enum('status', ['published', 'draft'])->default('draft');
            $table->timestamps();
            $table->index('slug');
            $table->index('category');
            $table->index('status');
            $table->index('is_featured');
        });
    }

    // ════════════════════════════════════════════════════════════════
    // TABLE 7 — blog_categories
    // ════════════════════════════════════════════════════════════════
    private function createBlogCategories(): void
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 130)->unique();
            $table->string('description', 300)->nullable();
            $table->string('color', 7)->default('#00B4D8');
            $table->unsignedInteger('sort_order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->index('slug');
            $table->index('status');
        });
    }

    // ════════════════════════════════════════════════════════════════
    // TABLE 8 — blog_articles
    // ════════════════════════════════════════════════════════════════
    private function createBlogArticles(): void
    {
        Schema::create('blog_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')
                  ->constrained('admin_users')
                  ->onDelete('cascade');
            $table->foreignId('category_id')
                  ->constrained('blog_categories')
                  ->onDelete('cascade');
            $table->string('title', 250);
            $table->string('slug', 280)->unique();
            $table->string('excerpt', 500)->nullable();
            $table->longText('content');
            $table->string('featured_image', 255)->nullable();
            $table->string('meta_title', 200)->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->string('og_title', 200)->nullable();
            $table->string('og_description', 300)->nullable();
            $table->string('og_image', 255)->nullable();
            $table->json('tags')->nullable();
            $table->unsignedInteger('read_time')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->enum('status', ['draft','review','published','rejected'])
                  ->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index('slug');
            $table->index('status');
            $table->index('author_id');
            $table->index('category_id');
            $table->index('published_at');
        });
    }

    // ════════════════════════════════════════════════════════════════
    // TABLE 9 — contact_messages
    // ════════════════════════════════════════════════════════════════
    private function createContactMessages(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 150);
            $table->string('mobile', 20)->nullable();
            $table->string('website_url', 255)->nullable();
            $table->enum('service_interest', [
                'google_ads_management',
                'conversion_tracking',
                'web_development',
                'landing_page_optimization',
                'technical_consultation',
                'other',
            ])->default('other');
            $table->text('message');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 300)->nullable();
            $table->enum('status', ['new','read','replied','archived'])
                  ->default('new');
            $table->text('admin_notes')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('email');
            $table->index('created_at');
        });
    }

    // ════════════════════════════════════════════════════════════════
    // TABLE 10 — newsletter_subscribers
    // ════════════════════════════════════════════════════════════════
    private function createNewsletterSubscribers(): void
    {
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email', 150)->unique();
            $table->string('name', 100)->nullable();
            $table->enum('status', ['active', 'unsubscribed'])->default('active');
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('subscribed_at')->useCurrent();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
            $table->index('status');
        });
    }

    // ════════════════════════════════════════════════════════════════
    // TABLE 11 — pages
    // ════════════════════════════════════════════════════════════════
    private function createPages(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 230)->unique();
            $table->longText('content')->nullable();
            $table->string('meta_title', 200)->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->string('og_title', 200)->nullable();
            $table->string('og_image', 255)->nullable();
            $table->boolean('is_system')->default(false);
            $table->enum('status', ['published', 'draft'])->default('published');
            $table->timestamps();
            $table->index('slug');
            $table->index('status');
        });
    }

    // ════════════════════════════════════════════════════════════════
    // TABLE 12 — activity_logs
    // ════════════════════════════════════════════════════════════════
    private function createActivityLogs(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('admin_users')
                  ->onDelete('set null');
            $table->string('action', 100);
            $table->string('model_type', 100)->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index('user_id');
            $table->index(['model_type', 'model_id']);
            $table->index('created_at');
        });
    }
}