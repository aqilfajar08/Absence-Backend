# Sistem Informasi Presensi Karyawan

Sistem presensi karyawan berbasis Laravel dengan QR Code, GPS tracking, dan laporan absensi lengkap.

## Fitur Utama

- **Absensi QR Code** - Check-in/Check-out dengan scan QR Code
- **GPS Tracking** - Validasi lokasi karyawan dengan radius kantor
- **Multi-Role System** - Administrator, Resepsionis, dan Karyawan
- **Laporan Excel** - Export laporan absensi bulanan
- **Notifikasi Push** - Firebase Cloud Messaging untuk pengingat
- **Kalender Riwayat** - Visualisasi kehadiran bulanan
- **Pengaturan Fleksibel** - Konfigurasi jam kerja dan potongan

## Tech Stack

- **Backend**: Laravel 11.x
- **Frontend**: Blade Templates + Tailwind CSS + Alpine.js
- **Database**: MySQL
- **Mobile**: Flutter (repository terpisah)
- **Notifications**: Firebase Cloud Messaging

## Persyaratan Sistem

- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Node.js >= 18.x
- NPM atau Yarn

## Instalasi Development

### 1. Clone Repository

```bash
git clone https://github.com/yourusername/absence-backend.git
cd absence-backend
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### 3. Setup Environment

```bash
# Copy environment file
copy .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_absence_backend
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

### 5. Setup Firebase (Optional untuk Notifikasi)

1. Download `firebase-auth.json` dari Firebase Console
2. Letakkan di folder `storage/app/firebase-auth.json`
3. Update `.env`:

```env
FIREBASE_DATABASE_URL=https://your-project.firebaseio.com
FIREBASE_CREDENTIALS=storage/app/firebase-auth.json
```

### 6. Migrate & Seed Database

```bash
php artisan migrate:fresh --seed
```

**Default Login Credentials:**

- **Admin**: admin@gmail.com / password
- **Resepsionis**: resepsionis@gmail.com / password
- **Karyawan**: user@gmail.com / password

### 7. Build Assets

```bash
npm run build
```

### 8. Create Storage Link

```bash
php artisan storage:link
```

### 9. Run Development Server

```bash
php artisan serve
```

Akses: `http://localhost:8000`

---

## Deployment ke Production

**PENTING**: Baca file `DEPLOYMENT_CHECKLIST.md` untuk panduan lengkap deployment!

### Quick Steps:

1. **Copy `.env.production` ke `.env` di server**
2. **Update semua konfigurasi** (database, APP_URL, dll)
3. **Set APP_DEBUG=false** dan **APP_ENV=production**
4. **Setup HTTPS/SSL Certificate**
5. **Run optimization:**

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

6. **Set proper permissions:**

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## Struktur Project

```
├── app/
│   ├── Http/Controllers/      # Controllers untuk semua fitur
│   ├── Models/                 # Eloquent Models
│   ├── Exports/                # Excel export logic
│   └── Http/Middleware/        # Custom middleware
├── database/
│   ├── migrations/             # Database schema
│   └── seeders/                # Database seeders
├── resources/
│   ├── views/                  # Blade templates
│   └── js/                     # JavaScript files
├── routes/
│   ├── web.php                 # Web routes
│   └── api.php                 # API routes (untuk mobile)
├── public/                     # Public assets
├── storage/
│   ├── app/
│   │   ├── public/avatars/    # User profile images
│   │   └── firebase-auth.json # Firebase credentials
│   └── logs/                   # Application logs
├── .env.example                # Environment template
├── .env.production             # Production environment template
└── DEPLOYMENT_CHECKLIST.md     # Deployment guide
```

---

## Keamanan

- **Rate Limiting** - Login dibatasi 5 percobaan per menit
- **CSRF Protection** - Aktif di semua form
- **Password Hashing** - BCrypt dengan 12 rounds
- **SQL Injection Protection** - Eloquent ORM dengan prepared statements
- **Input Validation** - Validasi ketat di semua input

**PENTING** saat production:

- Set `APP_DEBUG=false`
- Set `SESSION_SECURE_COOKIE=true` (jika pakai HTTPS)
- Gunakan database password yang kuat
- Setup HTTPS dengan SSL certificate
- Hapus atau amankan route `/fix-lateness`

---

## Fitur Detail

### Authentication & Authorization

- Multi-role system (Admin, Resepsionis, Karyawan)
- Laravel Sanctum untuk API authentication
- Session-based auth untuk web

### Absensi QR Code

- QR Code harian yang di-generate resepsionis
- Validasi token dan tanggal
- GPS radius checking
- Auto-calculate keterlambatan

### Sistem Keterlambatan

- **Terlambat 1**: 08:01 - 08:30 (Potongan GPH kecil)
- **Terlambat 2**: 08:31 - 09:00 (Potongan GPH sedang)
- **Terlambat 3**: 09:01 - 12:00 (Potongan GPH besar)
- **Setengah Hari**: > 12:00 (Potongan GPH maksimal)
- Threshold dan persentase potongan bisa diatur admin

### Laporan & Export

- Filter berdasarkan tanggal dan nama
- Export ke Excel dengan format lengkap
- Perhitungan otomatis potongan gaji
- Visualisasi calendar view

### UI/UX Features

- Responsive design (Desktop to Mobile)
- Real-time attendance monitoring
- Interactive calendar
- Badge status berwarna
- Toast notifications

---

## Commands Penting

```bash
# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recreate database
php artisan migrate:fresh --seed

# Build assets for production
npm run build

# Run tests (jika ada)
php artisan test
```

---

## Troubleshooting

### Error: "500 Internal Server Error"

```bash
tail -f storage/logs/laravel.log
```

### Error: "Permission Denied"

```bash
chmod -R 775 storage bootstrap/cache
```

### Assets tidak load

```bash
npm run build
php artisan view:clear
```

---

## Support & Documentation

- **Deployment Guide**: Lihat `DEPLOYMENT_CHECKLIST.md`
- **SRS Document**: Lihat `docs/SRS_Sistem_Presensi.md`
- **API Documentation**: [Coming Soon]

---

## License

This project is proprietary software. All rights reserved.

---

## Credits

Developed for Kantor Kasau TNI AU  
© 2026 - All Rights Reserved

---

## Changelog

### Version 1.0.0 (January 2026)

- Initial release
- QR Code attendance system
- GPS tracking
- Multi-role authorization
- Excel export
- Lateness calculation system
- Firebase notifications
- Calendar history view
