# Sistem Presensi SMK

Aplikasi sistem presensi berbasis web untuk SMK dengan fitur geolocation dan notifikasi WhatsApp.

## Cara Menjalankan Aplikasi

### 1. Persiapan Database
- Buka phpMyAdmin di `http://localhost/phpmyadmin`
- Buat database baru dengan nama `presensi_smk`
- Import file SQL dari `db/migrations/presensi_smk (4).sql`

### 2. Konfigurasi Environment
- File `.env` sudah tersedia di root directory
- Pastikan `FONNTE_TOKEN` sudah diisi dengan token dari https://fonnte.com (jika ingin menggunakan fitur WhatsApp)

### 3. Akses Aplikasi
- Buka browser dan akses: `http://localhost/Presensi-SMK`
- Atau langsung: `http://localhost/Presensi-SMK/index.php`

### 4. Login Default
Gunakan kredensial berikut untuk login:
- **Admin**: (cek database untuk user dengan role 'admin')
- **Guru**: (cek database untuk user dengan role 'guru')
- **Siswa**: (cek database untuk user dengan role 'siswa')

## Struktur URL
Aplikasi menggunakan routing sederhana dengan parameter `action`:
- Login: `index.php?action=login`
- Dashboard Admin: `index.php?action=admin_dashboard`
- Dashboard Guru: `index.php?action=guru_dashboard`
- Dashboard Siswa: `index.php?action=siswa_dashboard`

## Konfigurasi
File konfigurasi utama ada di `config/config.php`:
- Database credentials
- Base URL
- Email configuration (SwiftMailer)
- WhatsApp configuration (Fonnte)
- Geolocation settings

## File Penting
- `index.php` - Front controller utama
- `config/config.php` - File konfigurasi
- `.env` - Environment variables
- `app/controllers/` - Controller files
- `app/models/` - Model files
- `app/views/` - View files

## Troubleshooting
1. **Error "Class not found"**: Pastikan path `require_once` sudah benar
2. **Database connection error**: Cek kredensial di `config/config.php`
3. **Session error**: Pastikan PHP session enabled
4. **File upload error**: Cek permission folder `public/uploads/`

## Teknologi
- PHP 7.4+
- MySQL/MariaDB
- Composer (untuk dependencies)
- SwiftMailer (email)
- Dotenv (environment variables)
- Vanilla JavaScript
- Tailwind CSS
