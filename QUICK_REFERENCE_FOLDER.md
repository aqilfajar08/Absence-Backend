# 🗂️ Quick Reference: Folder Structure Web Admin

## 📍 **Lokasi File Berdasarkan Kebutuhan**

### 🎨 **1. UBAH TAMPILAN/UI**
```
📁 resources/views/
  │
  ├── 📁 pages/                    ⭐ HALAMAN-HALAMAN UTAMA
  │   ├── 📄 dashboard.blade.php   → Dashboard utama
  │   │
  │   ├── 📁 auth/                 → Halaman login/register
  │   │   ├── 📄 login.blade.php
  │   │   └── 📄 register.blade.php
  │   │
  │   ├── 📁 users/                → Manajemen user
  │   │   ├── 📄 index.blade.php   (List user)
  │   │   ├── 📄 create.blade.php  (Form tambah)
  │   │   ├── 📄 edit.blade.php    (Form edit)
  │   │   └── 📄 show.blade.php    (Detail user)
  │   │
  │   ├── 📁 attendances/          → Halaman absensi
  │   │   └── 📄 index.blade.php
  │   │
  │   ├── 📁 company/              → Halaman company
  │   │   ├── 📄 index.blade.php
  │   │   └── 📄 edit.blade.php
  │   │
  │   └── 📁 permissions/          → Halaman permissions
  │       ├── 📄 index.blade.php
  │       ├── 📄 create.blade.php
  │       └── 📄 edit.blade.php
  │
  ├── 📁 components/               ⭐ KOMPONEN REUSABLE
  │   ├── 📄 header.blade.php      → Header/Navbar
  │   ├── 📄 sidebar.blade.php     → Sidebar menu
  │   └── 📄 ...
  │
  └── 📁 layouts/                  ⭐ TEMPLATE UTAMA
      ├── 📄 app.blade.php         → Layout utama (header+sidebar+content)
      └── 📄 auth.blade.php        → Layout untuk login
```

---

### ⚙️ **2. UBAH LOGIC/FUNGSI**
```
📁 app/Http/Controllers/
  │
  ├── 📄 HomeController.php           → Logic dashboard
  ├── 📄 UserController.php           → Logic manajemen user ⭐
  ├── 📄 AttendanceController.php     → Logic absensi ⭐
  ├── 📄 CompanyController.php        → Logic company
  ├── 📄 PermissionController.php     → Logic permissions
  │
  └── 📁 Api/                         ⭐ API UNTUK MOBILE APP
      ├── 📄 AuthController.php       → API login/register
      ├── 📄 AttendanceController.php → API absensi
      ├── 📄 CompanyController.php    → API company
      └── 📄 UserController.php       → API user
```

---

### 🗄️ **3. UBAH DATABASE**
```
📁 database/
  │
  ├── 📁 migrations/               ⭐ STRUKTUR TABEL
  │   ├── 📄 create_users_table.php
  │   ├── 📄 create_attendances_table.php
  │   └── 📄 ...
  │
  └── 📁 seeders/                  ⭐ DATA AWAL
      ├── 📄 DatabaseSeeder.php
      ├── 📄 RolePermissionSeeder.php
      └── 📄 UserSeeder.php

📁 app/Models/                     ⭐ MODEL (REPRESENTASI TABEL)
  ├── 📄 User.php
  ├── 📄 Attendance.php
  ├── 📄 Company.php
  └── 📄 ...
```

---

### 🔗 **4. UBAH URL/ROUTING**
```
📁 routes/
  ├── 📄 web.php    ⭐ Route untuk WEB ADMIN
  └── 📄 api.php    ⭐ Route untuk API MOBILE
```

**Contoh isi `web.php`:**
```php
Route::get('/dashboard', [HomeController::class, 'index']);
Route::resource('users', UserController::class);
Route::get('/attendances', [AttendanceController::class, 'index']);
```

---

### 🎨 **5. UBAH STYLE (CSS/JS)**
```
📁 resources/
  ├── 📁 css/
  │   └── 📄 app.css              ⭐ CSS custom Anda
  │
  └── 📁 js/
      ├── 📄 app.js               ⭐ JavaScript custom
      └── 📄 bootstrap.js

📁 public/                         ⭐ FILE STATIC (sudah compiled)
  ├── 📁 css/                      → CSS compiled
  ├── 📁 js/                       → JS compiled
  ├── 📁 img/                      → Gambar/Logo ⭐
  └── 📁 library/                  → Library (Bootstrap, jQuery, dll)
```

---

### ⚙️ **6. KONFIGURASI**
```
📄 .env                            ⭐ KONFIGURASI UTAMA
   ↓
   APP_NAME=Laravel Absence
   DB_DATABASE=laravel_absence_backend
   SESSION_DRIVER=file
   ...

📁 config/                         → Konfigurasi detail
  ├── 📄 app.php
  ├── 📄 database.php
  └── 📄 session.php
```

---

## 🎯 **Cheat Sheet: Mau Ubah Apa?**

| **Mau Ubah Apa?** | **Edit File Ini** |
|-------------------|-------------------|
| 🎨 Tampilan Dashboard | `resources/views/pages/dashboard.blade.php` |
| 🎨 Tampilan Login | `resources/views/pages/auth/login.blade.php` |
| 🎨 Sidebar Menu | `resources/views/components/sidebar.blade.php` |
| 🎨 Header/Navbar | `resources/views/components/header.blade.php` |
| 🎨 Layout Utama | `resources/views/layouts/app.blade.php` |
| ⚙️ Logic User | `app/Http/Controllers/UserController.php` |
| ⚙️ Logic Absensi | `app/Http/Controllers/AttendanceController.php` |
| ⚙️ Logic QR Code | `app/Http/Controllers/AttendanceController.php` |
| 🔗 URL/Route Web | `routes/web.php` |
| 🔗 URL/Route API | `routes/api.php` |
| 🗄️ Struktur Tabel | `database/migrations/...` |
| 🗄️ Model | `app/Models/...` |
| 🎨 CSS Custom | `resources/css/app.css` |
| 🎨 JS Custom | `resources/js/app.js` |
| 🖼️ Ganti Logo/Gambar | `public/img/...` |
| ⚙️ Konfigurasi | `.env` |

---

## 🔥 **Workflow Cepat**

### **Ubah Tampilan Halaman:**
1. Edit file `.blade.php` di `resources/views/pages/...`
2. Refresh browser
3. Done! ✅

### **Ubah Logic/Data:**
1. Edit Controller di `app/Http/Controllers/...`
2. Refresh browser
3. Done! ✅

### **Tambah Halaman Baru:**
1. Buat Controller: `php artisan make:controller NamaController`
2. Buat View: `resources/views/pages/nama.blade.php`
3. Tambah Route: Edit `routes/web.php`
4. Tambah Menu: Edit `resources/views/components/sidebar.blade.php`
5. Done! ✅

### **Ubah Database:**
1. Edit Migration: `database/migrations/...`
2. Jalankan: `php artisan migrate:fresh`
3. Done! ✅

---

## 💡 **Tips:**

- ✅ File `.blade.php` = Template HTML dengan PHP
- ✅ Controller = Logic/Fungsi
- ✅ Model = Representasi tabel database
- ✅ Route = Mapping URL ke Controller
- ✅ Migration = Struktur tabel database

---

Sekarang sudah jelas kan? 😊 Mau ubah yang mana dulu?
