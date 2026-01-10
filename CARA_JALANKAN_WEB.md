# 🚀 CARA MENJALANKAN WEB LARAVEL - LANGKAH MUDAH

## ⚡ QUICK START (3 Langkah Saja!)

### 📋 PERSIAPAN (Lakukan Sekali Saja)

#### 1️⃣ **Start XAMPP**
- Buka **XAMPP Control Panel**
- Klik tombol **Start** pada **Apache**
- Klik tombol **Start** pada **MySQL**
- Tunggu sampai kedua service berwarna hijau

#### 2️⃣ **Buat Database**
- Buka browser
- Akses: `http://localhost/phpmyadmin`
- Klik tab **"Databases"** di atas
- Di kolom **"Create database"**, ketik: `laravel_absence_backend`
- Klik tombol **"Create"**
- ✅ Database siap!

---

## 🔧 INSTALASI (Lakukan Sekali Saja)

### ❌ **JIKA setup.bat STUCK (Sudah 16+ menit):**

1. **Stop setup.bat yang lama:**
   - Tekan **Ctrl+C** di terminal
   - Atau close terminal tersebut

2. **Jalankan script baru yang lebih cepat:**
   - Double-click file: **`install-dependencies.bat`**
   - Tunggu proses selesai (5-10 menit)
   - Akan muncul "INSTALLATION COMPLETE!"

3. **Setup Database:**
   - Double-click file: **`migrate-database.bat`**
   - Tekan Enter untuk konfirmasi
   - Tunggu sampai selesai
   - Akan muncul kredensial login

---

## 🌐 MENJALANKAN WEB SERVER

### Setelah instalasi selesai:

1. **Double-click file:** `run-server.bat`

2. **Tunggu sampai muncul:**
   ```
   INFO  Server running on [http://127.0.0.1:8000]
   ```

3. **Buka browser, akses:**
   ```
   http://localhost:8000
   ```

4. **Login dengan:**
   - Email: `admin@gmail.com`
   - Password: `12345678`

5. **✅ SELESAI! Web sudah jalan!**

---

## 📊 PROGRESS TRACKER

Centang setiap langkah yang sudah selesai:

### Persiapan:
- [ ] XAMPP Apache running (hijau)
- [ ] XAMPP MySQL running (hijau)
- [ ] Database `laravel_absence_backend` sudah dibuat

### Instalasi:
- [ ] `install-dependencies.bat` selesai
- [ ] `migrate-database.bat` selesai
- [ ] Muncul pesan "Test users created"

### Running:
- [ ] `run-server.bat` running
- [ ] Browser bisa akses `http://localhost:8000`
- [ ] Bisa login dengan `admin@gmail.com`

---

## ⏱️ ESTIMASI WAKTU

| Langkah | Waktu |
|---------|-------|
| Start XAMPP | 30 detik |
| Buat Database | 1 menit |
| Install Dependencies | 5-10 menit |
| Migrate Database | 1-2 menit |
| Run Server | 5 detik |
| **TOTAL** | **~10-15 menit** |

---

## 🐛 TROUBLESHOOTING

### "PHP not found"
**Solusi:** Pastikan XAMPP terinstall di `C:\xampp`

### "Composer install failed"
**Solusi:** 
1. Cek koneksi internet
2. Coba lagi: double-click `install-dependencies.bat`

### "Migration failed"
**Solusi:**
1. Pastikan MySQL running (hijau di XAMPP)
2. Pastikan database sudah dibuat
3. Cek file `.env`, pastikan:
   ```
   DB_DATABASE=laravel_absence_backend
   DB_USERNAME=root
   DB_PASSWORD=
   ```

### "Port 8000 already in use"
**Solusi:** Ada aplikasi lain yang pakai port 8000
1. Close aplikasi yang pakai port 8000
2. Atau edit `run-server.bat`, tambahkan `--port=8080`

---

## 📞 BANTUAN

Jika masih ada masalah:
1. Screenshot error yang muncul
2. Tanya saya dengan detail errornya
3. Saya akan bantu troubleshoot

---

## 🎯 SETELAH WEB JALAN

Anda bisa:
- ✅ Login ke web admin
- ✅ Kelola user
- ✅ Lihat attendance
- ✅ Generate QR code (login sebagai security)
- ✅ Test API dengan Postman

---

**Status Setup.bat Lama:** ❌ Stuck (16+ menit)  
**Solusi:** ✅ Gunakan `install-dependencies.bat` (lebih cepat & reliable)

**Siap untuk install? Double-click `install-dependencies.bat` sekarang!** 🚀
