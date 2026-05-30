# Prosper Media — Production Deployment Checklist

## 1. Server Requirements
- [ ] PHP 8.2+ with extensions: BCMath, Ctype, cURL, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD
- [ ] MySQL 8.0+ or MariaDB 10.6+
- [ ] Nginx or Apache with mod_rewrite
- [ ] Composer 2.x
- [ ] Node.js 18+ & npm (for build)
- [ ] SSL certificate (Let's Encrypt or purchased)

## 2. Upload Files
- [ ] Upload all project files (exclude: .git, node_modules, vendor, .env)
- [ ] Set file permissions: `chmod -R 755 .` then `chmod -R 777 storage bootstrap/cache`

## 3. Environment Configuration
- [ ] Copy `.env.example` to `.env`
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_URL=https://yourdomain.com`
- [ ] Set all database credentials
- [ ] Set mail credentials (SMTP)
- [ ] Run: `php artisan key:generate`

## 4. Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

## 5. Database Setup
```bash
php artisan migrate --force
php artisan db:seed --force
```

## 6. Optimize Laravel
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan storage:link
php artisan icons:cache   # if using icon packages
```

## 7. Update robots.txt
- [ ] Replace `yourdomain.com` in `public/robots.txt` with actual domain
- [ ] Update sitemap URL in robots.txt

## 8. Nginx Configuration
```nginx
server {
    listen 80;
    listen 443 ssl;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/prosper-media/public;
    index index.php;

    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;

    # Force HTTPS
    if ($scheme != "https") {
        return 301 https://$server_name$request_uri;
    }

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* { deny all; }

    # Cache static assets
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

## 9. Apache .htaccess (if using Apache)
The default Laravel `.htaccess` in `/public` handles routing. Ensure `mod_rewrite` is enabled.

## 10. Cron Job (for scheduled tasks)
Add to crontab (`crontab -e`):
```bash
* * * * * cd /var/www/prosper-media && php artisan schedule:run >> /dev/null 2>&1
```

## 11. After Deployment — Verify
- [ ] Home page loads at https://yourdomain.com
- [ ] Admin login works at https://yourdomain.com/admin/login
- [ ] Contact form submits correctly
- [ ] Sitemap accessible: https://yourdomain.com/sitemap.xml
- [ ] robots.txt accessible: https://yourdomain.com/robots.txt
- [ ] Images upload and display correctly
- [ ] SSL certificate valid (green padlock)
- [ ] Security headers present (check with securityheaders.com)

## 12. Post-Launch Security
- [ ] Change default admin password immediately
- [ ] Update `.env` with real SMTP credentials
- [ ] Remove `/telescope` and `/horizon` routes if not needed
- [ ] Enable database backups (daily automated)
- [ ] Set up server monitoring (UptimeRobot — free)
- [ ] Submit sitemap to Google Search Console
- [ ] Submit sitemap to Bing Webmaster Tools

## 13. Cache Invalidation (when updating content)
```bash
php artisan optimize:clear  # clear all caches
php artisan optimize        # rebuild optimized caches
```

## 14. Maintenance Mode
```bash
php artisan down  --message="Back in 5 minutes. Be Optimistic!"
# ... perform updates ...
php artisan up
```