# 🚀 DEPLOYMENT CHECKLIST - LARAVEL ABSENCE SYSTEM

## ✅ Yang Sudah Dilakukan di Kode

- [x] Rate limiting untuk login (5 attempts/minute)
- [x] CSRF protection aktif
- [x] Input validation di semua form
- [x] Password hashing dengan bcrypt
- [x] SQL injection protection (Eloquent ORM)

---

## 🔧 Yang Harus Dilakukan Saat Deploy (WAJIB!)

### 1. Environment Configuration

**File: `.env` di server**

```bash
# Copy template production
cp .env.production .env

# Edit file .env dan ubah:
APP_ENV=production
APP_DEBUG=false                    # ⚠️ WAJIB FALSE!
APP_URL=https://your-domain.com    # Ganti dengan domain asli

# Database
DB_PASSWORD=<strong-password-here>  # ⚠️ HARUS ADA PASSWORD!

# Session (jika pakai HTTPS)
SESSION_SECURE_COOKIE=true         # ⚠️ WAJIB TRUE jika pakai HTTPS!
```

### 2. Setup HTTPS/SSL Certificate

**Pilih salah satu:**

**Option A: Menggunakan Let's Encrypt (Gratis)**

```bash
# Install certbot
sudo apt-get install certbot python3-certbot-nginx

# Generate certificate
sudo certbot --nginx -d your-domain.com
```

**Option B: Menggunakan Cloudflare (Gratis)**

- Daftar di cloudflare.com
- Add domain
- Aktifkan SSL mode: Full/Strict

### 3. Optimize Laravel untuk Production

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### 4. Set Permissions yang Benar

```bash
# Storage dan cache harus writable
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 5. Database Production

```bash
# Jalankan migration
php artisan migrate --force

# Seed data jika perlu (HATI-HATI!)
php artisan db:seed --force
```

### 6. Security Headers (Nginx)

Tambahkan di config nginx:

```nginx
add_header X-Frame-Options "SAMEORIGIN";
add_header X-Content-Type-Options "nosniff";
add_header X-XSS-Protection "1; mode=block";
```

### 7. Firewall Configuration

```bash
# Hanya allow port 80 (HTTP) dan 443 (HTTPS)
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### 8. Backup Strategy

**Setup Automatic Backup:**

```bash
# Buat cron job untuk backup database harian
0 2 * * * mysqldump -u username -p'password' database_name > /backups/db_$(date +\%Y\%m\%d).sql

# Backup files mingguan
0 3 * * 0 tar -czf /backups/laravel_$(date +\%Y\%m\%d).tar.gz /var/www/html/laravel
```

### 9. Monitor Logs

```bash
# Check error logs regularly
tail -f storage/logs/laravel.log
```

---

## ⚠️ SECURITY WARNINGS

### 🔴 CRITICAL (Harus Dilakukan!)

1. ❌ **APP_DEBUG = false** - Error details tidak boleh terlihat publik
2. ❌ **Database Password** - Harus strong password
3. ❌ **HTTPS Aktif** - Data terenkripsi saat transmisi
4. ❌ **Session Secure Cookie** - Prevent session hijacking

### 🟡 PENTING (Sangat Direkomendasikan)

1. ⚠️ **Rate Limiting** - Sudah ditambahkan di login
2. ⚠️ **Backup Database** - Setup cron job untuk backup rutin
3. ⚠️ **Update Dependencies** - `composer update` dan `npm update` rutin
4. ⚠️ **Firewall** - Tutup port yang tidak digunakan

### 🟢 OPTIONAL (Nice to Have)

1. ✓ **Cloudflare CDN** - DDoS protection & caching
2. ✓ **Monitoring Tools** - Setup New Relic, Sentry, atau Laravel Telescope
3. ✓ **CI/CD Pipeline** - Automate deployment

---

## 📝 Testing Before Go-Live

### Checklist Testing:

- [ ] Login berfungsi dengan benar
- [ ] HTTPS aktif (https://)
- [ ] Tidak ada error 500 di halaman apapun
- [ ] Upload file berfungsi
- [ ] Database connection stabil
- [ ] Email/notification berfungsi (jika ada)
- [ ] QR Code generation berfungsi
- [ ] Mobile app bisa connect ke API

### Test Rate Limiting:

```bash
# Coba login 6x dengan password salah
# Harus muncul error "Too Many Attempts" setelah attempt ke-5
```

---

## 🆘 Troubleshooting

### Error: "500 Internal Server Error"

```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Error: "Permission Denied"

```bash
# Fix permissions
chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Error: "Database Connection Failed"

```bash
# Check .env database credentials
# Check if MySQL service running
sudo systemctl status mysql
```

---

## 📞 Contact & Support

Jika ada masalah saat deployment, hubungi:

- Developer: [Your Contact]
- Database: Check `/var/log/mysql/error.log`
- Web Server: Check `/var/log/nginx/error.log` atau `/var/log/apache2/error.log`

---

## 📚 Resources

- [Laravel Deployment Documentation](https://laravel.com/docs/deployment)
- [Let's Encrypt SSL Setup](https://letsencrypt.org/getting-started/)
- [Laravel Security Best Practices](https://laravel.com/docs/security)

---

**Last Updated:** 2026-01-20
**Version:** 1.0
