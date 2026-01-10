# 🎨 Panduan Lengkap: Edit Web Admin Laravel

Panduan ini akan membantu Anda memahami struktur folder Laravel dan file-file mana yang perlu diubah untuk mengedit web admin.

---

## 📁 **Struktur Folder Utama**

```
Absence-Backend/
├── app/                    # Logic aplikasi (Controllers, Models, dll)
├── config/                 # File konfigurasi
├── database/              # Migrations, Seeders, Factories
├── public/                # File public (CSS, JS, Images yang sudah di-compile)
├── resources/             # ⭐ PALING SERING DIEDIT (Views, CSS, JS source)
├── routes/                # ⭐ Routing/URL aplikasi
├── storage/               # File upload, logs, cache
└── .env                   # ⭐ Konfigurasi environment
```

---

## 🎯 **File/Folder yang Sering Diubah untuk Web Admin**

### 1️⃣ **TAMPILAN (UI/UX)** - `resources/views/`

Ini adalah folder **PALING PENTING** untuk mengubah tampilan web admin.

```
resources/views/
├── layouts/              # Template utama (header, sidebar, footer)
│   ├── app.blade.php    # Layout utama aplikasi
│   ├── auth.blade.php   # Layout untuk halaman login/register
│   └── sidebar.blade.php # Sidebar menu
│
├── components/           # Komponen reusable (button, card, dll)
│   ├── header.blade.php
│   ├── sidebar.blade.php
│   └── ...
│
└── pages/               # ⭐ Halaman-halaman aplikasi
    ├── dashboard.blade.php        # Halaman dashboard
    ├── auth/                      # Halaman login/register
    │   ├── login.blade.php
    │   └── register.blade.php
    ├── users/                     # Halaman manajemen user
    │   ├── index.blade.php       # List user
    │   ├── create.blade.php      # Form tambah user
    │   └── edit.blade.php        # Form edit user
    ├── attendances/              # Halaman absensi
    │   └── index.blade.php
    ├── company/                  # Halaman company/profile
    │   ├── index.blade.php
    │   └── edit.blade.php
    └── permissions/              # Halaman permissions
        ├── index.blade.php
        ├── create.blade.php
        └── edit.blade.php
```

**📝 Format File:** `.blade.php` (Blade adalah template engine Laravel)

**Contoh Edit:**
- Ubah warna dashboard → Edit `resources/views/pages/dashboard.blade.php`
- Ubah tampilan login → Edit `resources/views/pages/auth/login.blade.php`
- Ubah sidebar menu → Edit `resources/views/components/sidebar.blade.php`

---

### 2️⃣ **LOGIC/BACKEND** - `app/Http/Controllers/`

Controller mengatur **logic** dan **data** yang ditampilkan di view.

```
app/Http/Controllers/
├── HomeController.php           # Controller untuk dashboard
├── UserController.php           # ⭐ Controller untuk manajemen user
├── AttendanceController.php     # ⭐ Controller untuk absensi
├── CompanyController.php        # Controller untuk company
├── PermissionController.php     # Controller untuk permissions
└── Api/                         # ⭐ API untuk mobile app
    ├── AuthController.php       # API login/register
    ├── AttendanceController.php # API absensi
    ├── CompanyController.php    # API company
    └── UserController.php       # API user
```

**Contoh Edit:**
- Ubah logic saat user login → Edit `app/Http/Controllers/UserController.php`
- Ubah logic generate QR code → Edit `app/Http/Controllers/AttendanceController.php`
- Ubah API response → Edit `app/Http/Controllers/Api/...`

---

### 3️⃣ **DATABASE** - `database/migrations/` & `app/Models/`

#### **Migrations** (Struktur tabel database)
```
database/migrations/
├── 2024_xx_xx_create_users_table.php
├── 2024_xx_xx_create_attendances_table.php
├── 2024_xx_xx_create_companies_table.php
└── ...
```

**Kapan Edit:**
- Tambah kolom baru di tabel
- Ubah tipe data kolom
- Buat tabel baru

**Cara Jalankan:**
```bash
php artisan migrate        # Jalankan migration
php artisan migrate:fresh  # Reset database & migrate ulang
```

#### **Models** (Representasi tabel di code)
```
app/Models/
├── User.php
├── Attendance.php
├── Company.php
└── ...
```

**Kapan Edit:**
- Tambah relationship antar tabel
- Tambah custom function
- Ubah fillable/guarded fields

---

### 4️⃣ **ROUTING (URL)** - `routes/`

Mengatur **URL** dan **controller** mana yang dipanggil.

```
routes/
├── web.php    # ⭐ Route untuk web admin
└── api.php    # ⭐ Route untuk API mobile app
```

**Contoh `routes/web.php`:**
```php
Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
Route::resource('users', UserController::class);
Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
```

**Kapan Edit:**
- Tambah halaman baru
- Ubah URL
- Tambah middleware (auth, role, dll)

---

### 5️⃣ **STYLING (CSS/JS)** - `resources/css/` & `resources/js/`

```
resources/
├── css/
│   └── app.css          # CSS custom Anda
└── js/
    ├── app.js           # JavaScript custom Anda
    └── bootstrap.js     # Bootstrap JS
```

**Catatan:** File di `resources/` perlu di-compile ke `public/` menggunakan Vite/Laravel Mix.

**Cara Compile:**
```bash
npm run dev    # Development (auto-reload)
npm run build  # Production (optimized)
```

---

### 6️⃣ **ASSETS STATIC** - `public/`

File yang **sudah di-compile** atau **static assets**.

```
public/
├── css/              # CSS compiled
├── js/               # JS compiled
├── img/              # ⭐ Gambar/logo
├── library/          # Library CSS/JS (Bootstrap, jQuery, dll)
└── index.php         # Entry point Laravel
```

**Kapan Edit:**
- Ganti logo → Taruh di `public/img/`
- Tambah gambar → Taruh di `public/img/`
- Tambah library CSS/JS → Taruh di `public/library/`

---

### 7️⃣ **KONFIGURASI** - `.env` & `config/`

#### **`.env`** (Environment variables)
```env
APP_NAME=Laravel Absence
APP_URL=http://localhost:8000
DB_DATABASE=laravel_absence_backend
SESSION_DRIVER=file
...
```

**Kapan Edit:**
- Ubah nama aplikasi
- Ubah database connection
- Ubah session driver
- Tambah API key (Firebase, dll)

#### **`config/`** (File konfigurasi)
```
config/
├── app.php          # Konfigurasi aplikasi
├── database.php     # Konfigurasi database
├── session.php      # Konfigurasi session
└── ...
```

---

## 🎯 **Workflow Edit Web Admin**

### **Scenario 1: Ubah Tampilan Dashboard**

1. **Edit View:**
   ```
   resources/views/pages/dashboard.blade.php
   ```

2. **Edit Controller (jika perlu ubah data):**
   ```
   app/Http/Controllers/HomeController.php
   ```

3. **Refresh browser** → Lihat perubahan

---

### **Scenario 2: Tambah Halaman Baru (Misal: Laporan)**

1. **Buat Controller:**
   ```bash
   php artisan make:controller ReportController
   ```

2. **Buat View:**
   ```
   resources/views/pages/reports/index.blade.php
   ```

3. **Tambah Route:**
   ```php
   // routes/web.php
   Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
   ```

4. **Tambah Menu di Sidebar:**
   ```
   resources/views/components/sidebar.blade.php
   ```

---

### **Scenario 3: Ubah Fitur QR Code**

1. **Edit Controller:**
   ```
   app/Http/Controllers/AttendanceController.php
   ```

2. **Edit View (jika perlu):**
   ```
   resources/views/pages/attendances/index.blade.php
   ```

3. **Edit Model (jika perlu):**
   ```
   app/Models/Attendance.php
   ```

---

## 🔥 **Tips Penting**

### ✅ **DO's:**
1. **Selalu backup** sebelum edit file penting
2. **Test di local** dulu sebelum deploy
3. **Gunakan Git** untuk version control
4. **Clear cache** setelah edit config:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

### ❌ **DON'Ts:**
1. **Jangan edit** file di `vendor/` (akan hilang saat composer update)
2. **Jangan edit** file di `public/build/` (auto-generated)
3. **Jangan commit** file `.env` ke Git (sensitive data)
4. **Jangan edit** langsung di production (test dulu di local)

---

## 🚀 **Command Artisan yang Berguna**

```bash
# Generate files
php artisan make:controller NamaController
php artisan make:model NamaModel
php artisan make:migration create_nama_table

# Database
php artisan migrate              # Jalankan migration
php artisan migrate:fresh        # Reset & migrate ulang
php artisan db:seed              # Jalankan seeder

# Cache
php artisan config:clear         # Clear config cache
php artisan cache:clear          # Clear application cache
php artisan view:clear           # Clear view cache
php artisan route:clear          # Clear route cache
php artisan optimize:clear       # Clear semua cache

# Development
php artisan serve                # Jalankan server
php artisan tinker               # Interactive shell
php artisan route:list           # List semua route
```

---

## 📚 **Referensi Cepat**

| Ingin Ubah | Edit File |
|------------|-----------|
| Tampilan halaman | `resources/views/pages/...` |
| Sidebar menu | `resources/views/components/sidebar.blade.php` |
| Header | `resources/views/components/header.blade.php` |
| Layout utama | `resources/views/layouts/app.blade.php` |
| Logic/Data | `app/Http/Controllers/...` |
| URL/Route | `routes/web.php` |
| Database struktur | `database/migrations/...` |
| Model/Relationship | `app/Models/...` |
| CSS custom | `resources/css/app.css` |
| JS custom | `resources/js/app.js` |
| Gambar/Logo | `public/img/...` |
| Konfigurasi | `.env` atau `config/...` |

---

## 🎨 **Contoh: Ubah Warna Tema**

Jika web admin Anda menggunakan template (seperti Stisla), biasanya ada file CSS di:

```
public/css/style.css
public/css/components.css
```

Atau edit source-nya di:
```
resources/css/app.css
```

Lalu compile dengan:
```bash
npm run dev
```

---

## 💡 **Next Steps**

Sekarang Anda sudah tahu struktur foldernya! Apa yang ingin Anda ubah?

1. **Tampilan dashboard?**
2. **Fitur QR code?**
3. **Sidebar menu?**
4. **Halaman user management?**
5. **Atau yang lain?**

Beritahu saya, dan saya akan bantu guide lebih detail! 😊
