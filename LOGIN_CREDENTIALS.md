# 🔐 DEFAULT LOGIN CREDENTIALS

Setelah menjalankan seeder (`php artisan db:seed`), Anda akan memiliki 3 user test:

## 👤 USERS

### 1. Admin User
- **Email**: `admin@gmail.com`
- **Password**: `12345678`
- **Role**: Admin
- **Permissions**: Full access (semua fitur)
- **Department**: Management
- **Position**: CEO

### 2. Receptionist User
- **Email**: `receptionist@gmail.com`
- **Password**: `12345678`
- **Role**: Receptionist
- **Permissions**: 
  - Generate QR Code ✅
  - Deactivate QR Code ✅
  - View QR Code ✅
  - View Attendance ✅
  - View Users ✅
- **Department**: Front Office
- **Position**: Receptionist

### 3. Employee User
- **Email**: `employee@gmail.com`
- **Password**: `12345678`
- **Role**: Employee
- **Permissions**:
  - View Attendance ✅
  - Create Attendance ✅
  - View Permissions ✅
- **Department**: IT
- **Position**: Staff

---

## 🔑 CARA LOGIN

### Web Admin (Laravel Blade)
1. Buka browser
2. Akses: `http://localhost:8000`
3. Masukkan email & password
4. Klik "Login"

### Mobile App (Flutter)
1. Buka aplikasi mobile
2. Masukkan email & password
3. Tap "Login"

### API (Postman/Thunder Client)
```http
POST http://localhost:8000/api/login
Content-Type: application/json

{
  "email": "admin@gmail.com",
  "password": "12345678"
}
```

Response:
```json
{
  "message": "successful",
  "access_token": "1|xxxxxxxxxxxxx",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@gmail.com",
    ...
  }
}
```

---

## 🎯 TESTING WORKFLOW

### Test QR Code Generation (Receptionist/Admin Only)

**1. Login sebagai Receptionist:**
```http
POST http://localhost:8000/api/login
{
  "email": "receptionist@gmail.com",
  "password": "12345678"
}
```

**2. Generate QR Code:**
```http
POST http://localhost:8000/api/qr-code/generate
Authorization: Bearer {access_token}
```

Response:
```json
{
  "status": "success",
  "message": "QR code generated successfully",
  "data": {
    "qr_code": "20260107-A1B2C3D4",
    "valid_date": "2026-01-07",
    "expires_at": "2026-01-07 23:59:59",
    "generated_by": "Receptionist User"
  }
}
```

**3. Validate QR Code (Employee):**
```http
POST http://localhost:8000/api/qr-code/validate
Authorization: Bearer {employee_access_token}
{
  "qr_code": "20260107-A1B2C3D4"
}
```

---

## ⚠️ CATATAN KEAMANAN

### Production Environment:
1. **GANTI SEMUA PASSWORD** - Jangan gunakan `12345678` di production!
2. **Hapus/Disable Test Users** - Atau ganti dengan data real
3. **Update .env** - Set `APP_ENV=production` dan `APP_DEBUG=false`
4. **Gunakan HTTPS** - Jangan gunakan HTTP di production
5. **Backup Database** - Lakukan backup rutin

### Recommended Password Policy:
- Minimal 12 karakter
- Kombinasi huruf besar, kecil, angka, simbol
- Tidak menggunakan kata yang mudah ditebak
- Berbeda untuk setiap user

---

## 🔄 RESET PASSWORD

Jika lupa password, bisa reset via database:

```sql
-- Reset password menjadi '12345678'
UPDATE users 
SET password = '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5iGy2JlVKQQCu'
WHERE email = 'admin@gmail.com';
```

Atau via artisan tinker:
```bash
php artisan tinker

>>> $user = User::where('email', 'admin@gmail.com')->first();
>>> $user->password = Hash::make('newpassword');
>>> $user->save();
```

---

**Last Updated**: 2026-01-07  
**Status**: Ready ✅
