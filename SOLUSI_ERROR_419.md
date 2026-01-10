# Solusi Error 419 | PAGE EXPIRED

## Penyebab Error
Error 419 PAGE EXPIRED terjadi karena:
1. **CSRF Token expired** - Token keamanan Laravel sudah tidak valid
2. **Session tidak tersimpan** - Aplikasi menggunakan session driver `database` tapi tabel `sessions` belum dibuat
3. **Cache yang corrupt**

## Solusi Cepat (Recommended)

### Opsi 1: Ubah Session Driver ke File (Paling Mudah)

1. **Buka file `.env`** di folder `Absence-Backend`

2. **Cari baris** yang berisi `SESSION_DRIVER` (sekitar baris 20-30)

3. **Ubah dari:**
   ```
   SESSION_DRIVER=database
   ```
   
   **Menjadi:**
   ```
   SESSION_DRIVER=file
   ```

4. **Simpan file `.env`**

5. **Stop server Laravel** (tekan Ctrl+C di terminal yang menjalankan `php artisan serve`)

6. **Jalankan command berikut** di terminal (buka terminal baru di folder Absence-Backend):
   ```bash
   C:\xampp\php\php.exe artisan config:clear
   C:\xampp\php\php.exe artisan cache:clear
   ```

7. **Start server lagi:**
   ```bash
   run-server.bat
   ```

8. **Clear browser cache:**
   - Tekan `Ctrl + Shift + Delete`
   - Pilih "Cookies and other site data" dan "Cached images and files"
   - Klik "Clear data"

9. **Coba login lagi** di `http://127.0.0.1:8000/login`

---

### Opsi 2: Buat Tabel Sessions (Jika ingin tetap pakai database)

Jika Anda ingin tetap menggunakan session driver database, ikuti langkah ini:

1. **Buka terminal** di folder `Absence-Backend`

2. **Jalankan command:**
   ```bash
   C:\xampp\php\php.exe artisan session:table
   C:\xampp\php\php.exe artisan migrate
   C:\xampp\php\php.exe artisan config:clear
   C:\xampp\php\php.exe artisan cache:clear
   ```

3. **Restart server** (Ctrl+C lalu jalankan `run-server.bat` lagi)

4. **Clear browser cache** (Ctrl+Shift+Delete)

5. **Coba login lagi**

---

## Troubleshooting Tambahan

### Jika masih error setelah langkah di atas:

1. **Pastikan folder `storage` writable:**
   ```bash
   C:\xampp\php\php.exe artisan storage:link
   ```

2. **Regenerate application key:**
   ```bash
   C:\xampp\php\php.exe artisan key:generate
   ```

3. **Clear semua cache:**
   ```bash
   C:\xampp\php\php.exe artisan optimize:clear
   ```

4. **Restart XAMPP MySQL** (jika menggunakan session database)

5. **Gunakan Incognito/Private browsing** untuk test

---

## Penjelasan Teknis

- **CSRF Token**: Laravel menggunakan token CSRF untuk melindungi dari serangan Cross-Site Request Forgery
- **Session Driver**: Laravel bisa menyimpan session di berbagai tempat (file, database, redis, dll)
- **File Driver**: Menyimpan session di folder `storage/framework/sessions` (lebih simple untuk development)
- **Database Driver**: Menyimpan session di tabel database (butuh tabel `sessions` yang dibuat via migration)

---

## Rekomendasi

Untuk **development/testing**, gunakan **SESSION_DRIVER=file** karena:
- ✅ Lebih mudah setup
- ✅ Tidak perlu migration tambahan
- ✅ Lebih cepat
- ✅ Cocok untuk single server

Untuk **production**, bisa gunakan **database** atau **redis** jika:
- Menggunakan multiple servers (load balancing)
- Butuh session persistence yang lebih reliable
- Butuh session sharing antar servers
