# 🚀 PANDUAN MENJALANKAN WEB LARAVEL

## ⚠️ PREREQUISITES

Sebelum menjalankan web Laravel, pastikan:

### 1. **XAMPP Sudah Running**
- ✅ XAMPP sudah terinstall di `C:\xampp`
- ✅ Apache server sudah running (untuk web admin)
- ✅ MySQL server sudah running (untuk database)

Cara start XAMPP:
1. Buka XAMPP Control Panel
2. Klik "Start" pada Apache
3. Klik "Start" pada MySQL

### 2. **Database Sudah Dibuat**
1. Buka browser, akses: `http://localhost/phpmyadmin`
2. Klik "New" untuk buat database baru
3. Nama database: `laravel_absence_backend`
4. Collation: `utf8mb4_unicode_ci`
5. Klik "Create"

### 3. **File .env Sudah Dikonfigurasi**
Buka file `.env` di folder `Absence-Backend`, pastikan:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_absence_backend
DB_USERNAME=root
DB_PASSWORD=
```

**Note**: Jika MySQL Anda pakai password, isi `DB_PASSWORD` dengan password Anda.

---

## 🔧 CARA SETUP (OTOMATIS)

### Opsi 1: Menggunakan Script (RECOMMENDED)

1. **Double-click** file `setup.bat`
2. Script akan otomatis:
   - Download Composer (jika belum ada)
   - Install dependencies
   - Generate application key
   - Tanya apakah mau run migrations
   - Tanya apakah mau run seeders
3. Ikuti instruksi di layar

### Opsi 2: Manual Setup

Jika script tidak jalan, buka PowerShell/CMD di folder `Absence-Backend`:

```bash
# 1. Download Composer (jika belum ada)
C:\xampp\php\php.exe -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
C:\xampp\php\php.exe composer-setup.php
C:\xampp\php\php.exe -r "unlink('composer-setup.php');"

# 2. Install dependencies
C:\xampp\php\php.exe composer.phar install

# 3. Generate app key
C:\xampp\php\php.exe artisan key:generate

# 4. Run migrations
C:\xampp\php\php.exe artisan migrate

# 5. Run seeders
C:\xampp\php\php.exe artisan db:seed

# 6. Create storage link
C:\xampp\php\php.exe artisan storage:link
```

---

## 🚀 CARA MENJALANKAN WEB

### Opsi 1: Menggunakan Script (RECOMMENDED)

1. **Double-click** file `run-server.bat`
2. Server akan jalan di `http://localhost:8000`
3. Buka browser, akses `http://localhost:8000`

### Opsi 2: Manual Run

Buka PowerShell/CMD di folder `Absence-Backend`:

```bash
C:\xampp\php\php.exe artisan serve
```

Server akan jalan di `http://localhost:8000`

---

## 🌐 AKSES WEB

### Web Admin (Laravel Blade)
- URL: `http://localhost:8000`
- Login page: `http://localhost:8000` (redirect otomatis)

### API Endpoints
- Base URL: `http://localhost:8000/api`
- Login: `POST http://localhost:8000/api/login`
- QR Generate: `POST http://localhost:8000/api/qr-code/generate`
- dll (lihat `routes/api.php`)

---

## 👤 DEFAULT USER (Setelah Seeder)

Jika Anda sudah run seeder, biasanya ada default user:

**Admin:**
- Email: (cek di `database/seeders/UserSeeder.php`)
- Password: (cek di `database/seeders/UserSeeder.php`)

**Note**: Jika belum ada seeder untuk user, Anda perlu buat manual via database atau buat seeder dulu.

---

## 🐛 TROUBLESHOOTING

### Error: "PHP not found"
**Solusi**: 
- Pastikan XAMPP terinstall di `C:\xampp`
- Atau edit file `setup.bat` dan `run-server.bat`, ubah `PHP_PATH` sesuai lokasi PHP Anda

### Error: "Composer install failed"
**Solusi**:
- Pastikan internet connected (untuk download packages)
- Cek ekstensi PHP yang required sudah enabled di `php.ini`
- Required extensions: `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`

### Error: "SQLSTATE[HY000] [1045] Access denied"
**Solusi**:
- Cek username/password MySQL di file `.env`
- Pastikan MySQL service running di XAMPP

### Error: "SQLSTATE[HY000] [1049] Unknown database"
**Solusi**:
- Buat database `laravel_absence_backend` di phpMyAdmin
- Atau ubah nama database di `.env` sesuai yang ada

### Error: "Class not found"
**Solusi**:
```bash
C:\xampp\php\php.exe composer.phar dump-autoload
```

### Error: "The stream or file could not be opened"
**Solusi**:
```bash
# Buat folder storage jika belum ada
mkdir storage\logs
mkdir storage\framework\cache
mkdir storage\framework\sessions
mkdir storage\framework\views

# Atau run:
C:\xampp\php\php.exe artisan storage:link
```

---

## 📝 CATATAN PENTING

### 1. **Port Conflict**
Jika port 8000 sudah dipakai, ubah port:
```bash
C:\xampp\php\php.exe artisan serve --port=8080
```

### 2. **Akses dari Device Lain**
Jika ingin akses dari HP/device lain di network yang sama:
```bash
C:\xampp\php\php.exe artisan serve --host=0.0.0.0 --port=8000
```
Akses via: `http://[IP-KOMPUTER-ANDA]:8000`

### 3. **Update .env untuk Mobile App**
Jangan lupa update `Variables.baseUrl` di Flutter app:
```dart
// lib/core/constants/variables.dart
static const String baseUrl = 'http://[IP-KOMPUTER]:8000';
```

### 4. **CORS Issue**
Jika ada CORS error saat akses dari mobile:
- Install package: `composer require fruitcake/laravel-cors`
- Atau tambahkan CORS headers di middleware

---

## ✅ CHECKLIST SEBELUM RUN

- [ ] XAMPP Apache running
- [ ] XAMPP MySQL running
- [ ] Database `laravel_absence_backend` sudah dibuat
- [ ] File `.env` sudah dikonfigurasi
- [ ] Dependencies sudah terinstall (`vendor` folder ada)
- [ ] Migrations sudah dijalankan
- [ ] Seeders sudah dijalankan (optional)

---

## 🎯 NEXT STEPS

Setelah web berhasil running:

1. **Test Login** - Coba login dengan user yang ada
2. **Test API** - Gunakan Postman/Thunder Client untuk test endpoints
3. **Generate QR** - Test fitur generate QR code
4. **Connect Mobile App** - Update base URL di Flutter app
5. **Test End-to-End** - Test full flow dari mobile ke backend

---

**Last Updated**: 2026-01-07  
**Status**: Ready to Run ✅
