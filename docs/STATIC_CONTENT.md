# Static And Hard-Coded Editable Content

This lists editable website content still embedded in Blade, config, route/controller constants, seeders, or public files. It excludes purely structural UI text that is not normally content-managed.

## Global Brand, Navigation, Footer
- `resources/views/components/public/header.blade.php:29-32`: fallback brand text `ProsperMedia` and slogan `Be Optimistic`.
- `resources/views/components/public/header.blade.php:43-48`: navigation labels and route list are hard-coded.
- `resources/views/components/public/header.blade.php:86,146`: CTA button text/link are hard-coded.
- `resources/views/components/public/footer.blade.php:22-24`: fallback brand text and slogan are hard-coded.
- `resources/views/components/public/footer.blade.php:40-42`: social platform labels/icons are hard-coded around dynamic URLs.
- `resources/views/components/public/footer.blade.php:67`: footer navigation labels and route list are hard-coded.
- `resources/views/components/public/footer.blade.php:83-94`: footer service links/slugs are hard-coded instead of pulling active services.
- `resources/views/components/public/footer.blade.php:161-178`: footer CTA, copyright brand link, and utility links are hard-coded.
- `config/prosper.php:4-8`: app name, slogan, tagline, email fallback, and max writers are static config values.
- `public/robots.txt:13`: sitemap URL still uses `https://yourdomain.com/sitemap.xml`.

## Homepage
- `resources/views/public/home.blade.php:3-4`: home meta title/description are static Blade sections.
- `resources/views/public/home.blade.php:27-55`: hero badge, heading, copy, and CTA label are hard-coded.
- `resources/views/public/home.blade.php:62-64`: hero statistics are hard-coded (`Projects Done`, `Client Satisfaction`, `Average ROAS`).
- `resources/views/public/home.blade.php:84-141`: decorative dashboard labels, metrics, chart labels, tracking labels, and status text are hard-coded.
- `resources/views/public/home.blade.php:161-178`: trust bar label and tool/logo list are hard-coded.
- `resources/views/public/home.blade.php:192-221`: `Why Choose Us` heading and three difference cards are hard-coded.
- `resources/views/public/home.blade.php:285`: fallback service card data is hard-coded.
- `resources/views/public/home.blade.php:344-361`: portfolio section labels and fallback projects are hard-coded.
- `resources/views/public/home.blade.php:424-437`: latest article section title and fallback articles are hard-coded.
- `resources/views/public/home.blade.php:484-511`: final CTA heading, copy, buttons, and proof points are hard-coded.

## About Page
- `resources/views/public/about.blade.php:3-4`: meta title/description are static.
- `resources/views/public/about.blade.php:13-20`: hero label, heading, and intro copy are hard-coded.
- `resources/views/public/about.blade.php:32-41`: story section heading and body copy are hard-coded.
- `resources/views/public/about.blade.php:57-62`: mission/philosophy/commitment cards, icons, and copy are hard-coded arrays.
- `resources/views/public/about.blade.php:85-115`: why/skills section headings and copy are hard-coded.
- `resources/views/public/about.blade.php:124`: technical skill labels and values are hard-coded.
- `resources/views/public/about.blade.php:151-158`: CTA heading and copy are hard-coded.

## Services
- `resources/views/public/services.blade.php:3-4`: services index meta title/description are static.
- `resources/views/public/services.blade.php:14`: page hero heading is hard-coded.
- `resources/views/public/services.blade.php:27-61`: fallback services, slugs, descriptions, icons, and feature lists are hard-coded in Blade even though services are already dynamic.
- `resources/views/public/services.blade.php:233-240`: final CTA heading and copy are hard-coded.
- `resources/views/public/services-single.blade.php:66-78`: service detail sidebar CTA label/copy/buttons are hard-coded, with only service title injected.
- `resources/views/public/services-single.blade.php:91`: `Other Services` heading is hard-coded.
- `resources/views/admin/services/create.blade.php:88-89`: icon name is a free text convention rather than a managed icon registry.

## Portfolio
- `resources/views/public/portfolio.blade.php:3-4`: portfolio meta title/description are static.
- `resources/views/public/portfolio.blade.php:14`: page hero heading is hard-coded.
- `resources/views/public/portfolio.blade.php:46-47`: portfolio filters/categories are hard-coded in Blade.
- `resources/views/public/portfolio.blade.php:62-67`: category colors are hard-coded in Blade.
- `resources/views/public/portfolio.blade.php:151-156`: fallback project records are hard-coded in Blade.
- `resources/views/public/portfolio.blade.php:206`: bottom CTA heading is hard-coded.
- `resources/views/public/portfolio-single.blade.php:32,49-66,79-113`: section labels and badges are hard-coded around dynamic project data.
- `resources/views/public/portfolio-single.blade.php:174-190`: side CTA/share copy is hard-coded.
- `resources/views/public/portfolio-single.blade.php:223,237-258,270`: gallery/bottom CTA/related headings and copy are hard-coded.
- `app/Models/PortfolioProject.php:34-43`: portfolio category labels are hard-coded in a model accessor.
- `app/Http/Controllers/Public/PortfolioController.php:13-15`: valid portfolio category keys are hard-coded.
- `app/Http/Controllers/Admin/PortfolioController.php:37,98`: admin validation hard-codes portfolio category enum values.
- `resources/views/admin/portfolio/create.blade.php:33` and `edit.blade.php:31`: category select options are hard-coded.

## Blog
- `resources/views/public/blog.blade.php:3-4`: blog index meta title/description are static.
- `resources/views/public/blog.blade.php:14`: blog hero heading is hard-coded.
- `resources/views/public/blog.blade.php:130-132`: fallback article cards are hard-coded in Blade.
- `resources/views/public/blog.blade.php:178-185`: newsletter sidebar heading, copy, and button label are hard-coded.
- `resources/views/public/blog.blade.php:201,226`: sidebar headings are hard-coded.
- `resources/views/public/blog-single.blade.php:143-165`: share labels/platforms are hard-coded.
- `resources/views/public/blog-single.blade.php:174-186`: author fallback initial/name/role and author bio copy are hard-coded.
- `resources/views/public/blog-single.blade.php:191-213`: article CTA block is hard-coded.
- `resources/views/public/blog-single.blade.php:251-268`: newsletter sidebar copy is hard-coded.
- `resources/views/public/blog-single.blade.php:293`: service link list in article sidebar is hard-coded.
- `resources/views/public/blog-single.blade.php:321,353`: related articles heading and read-more label are hard-coded.

## Contact
- `resources/views/public/contact.blade.php:3-4`: contact meta title/description are static.
- `resources/views/public/contact.blade.php:12-19`: contact page heading and response-time copy are hard-coded.
- `resources/views/public/contact.blade.php:35,167`: form intro and privacy/response promise are hard-coded.
- `resources/views/public/contact.blade.php:118-129`: service-interest options are hard-coded in Blade.
- `app/Http/Requests/Public/ContactFormRequest.php:14`: service-interest allowed values are hard-coded.
- `app/Console/Commands/InstallProsperMedia.php:313-321`: `contact_messages.service_interest` enum values are hard-coded.
- `resources/views/public/contact.blade.php:186-253`: contact card labels and response stat cards are hard-coded, while values are partly dynamic.

## Privacy Policy And Generic Pages
- `resources/views/public/privacy-policy.blade.php`: entire privacy policy is a static Blade page.
- `routes/web.php:40`: `/privacy-policy` route returns the static Blade page instead of using the existing `pages` table.
- `database/seeders/PageSeeder.php`: seeds privacy policy content into `pages`, but this content is not consumed by the current route.

## SEO And Schema
- `resources/views/components/public/seo-meta.blade.php:42,48,56`: organization description, address fallback, and service list are hard-coded.
- `app/Http/Controllers/Public/SitemapController.php:16-23`: static public pages and priorities/changefreq values are hard-coded.
- `resources/views/public/*`: page-level meta sections are repeated in each view instead of coming from a page/section model.

## Seeders And Fallback Duplication
- `database/seeders/ServiceSeeder.php`: service seed content duplicates Blade fallback content in `services.blade.php` and `home.blade.php`.
- `database/seeders/PortfolioSeeder.php`: portfolio seed content overlaps with fallback cards in `portfolio.blade.php` and `home.blade.php`.
- `database/seeders/BlogArticleSeeder.php`: seeded article content overlaps with fallback cards in `blog.blade.php` and `home.blade.php`.
- `database/seeders/SiteSettingsSeeder.php`: settings seed content overlaps with hard-coded header/footer/meta fallbacks.
