# Notifikasi Email untuk Sistem Presensi SMK

## Instalasi SwiftMailer

Sistem ini menggunakan SwiftMailer untuk mengirim notifikasi email ke orang tua siswa ketika siswa alpha (tidak hadir).

### Langkah 1: Install Composer Dependencies

Jalankan perintah berikut di root folder project:

```bash
composer install
```

### Langkah 2: Konfigurasi Email

Edit file `config/config.php` dan sesuaikan konfigurasi email:

```php
// Email Configuration untuk SwiftMailer
define('EMAIL_HOST', 'smtp.gmail.com'); // Ganti dengan SMTP server Anda
define('EMAIL_PORT', 587); // 587 untuk TLS, 465 untuk SSL
define('EMAIL_ENCRYPTION', 'tls'); // 'tls' atau 'ssl'
define('EMAIL_USERNAME', 'kristinluthfi@gmail.com'); // Email pengirim
define('EMAIL_PASSWORD', 'your-app-password'); // App password atau password email
define('EMAIL_FROM', 'your-email@gmail.com'); // Email pengirim
define('EMAIL_FROM_NAME', 'Sistem Presensi SMK'); // Nama pengirim
```

### Konfigurasi Gmail

Jika menggunakan Gmail, Anda perlu:

1. **Aktifkan 2-Step Verification** di akun Google Anda
2. **Buat App Password**:
   - Buka https://myaccount.google.com/apppasswords
   - Pilih "Mail" sebagai aplikasi
   - Pilih "Other" sebagai device dan beri nama (misal: "Presensi SMK")
   - Copy password 16 digit yang dihasilkan
   - Gunakan password tersebut di `EMAIL_PASSWORD`

### Konfigurasi SMTP Lainnya

Untuk provider email lain, sesuaikan konfigurasi:

**Outlook/Hotmail:**
```php
define('EMAIL_HOST', 'smtp-mail.outlook.com');
define('EMAIL_PORT', 587);
define('EMAIL_ENCRYPTION', 'tls');
```

**Yahoo:**
```php
define('EMAIL_HOST', 'smtp.mail.yahoo.com');
define('EMAIL_PORT', 587);
define('EMAIL_ENCRYPTION', 'tls');
```

**SMTP Custom:**
```php
define('EMAIL_HOST', 'your-smtp-server.com');
define('EMAIL_PORT', 587); // atau sesuai provider
define('EMAIL_ENCRYPTION', 'tls'); // atau 'ssl'
```

### Langkah 3: Pastikan Data Email Orang Tua Terisi

Email notifikasi akan dikirim ke email orang tua yang tersimpan di **Buku Induk Siswa**.

Pastikan data berikut terisi di buku induk:
- Email Orang Tua (`email_ortu`)

## Cara Kerja Notifikasi

### Notifikasi Presensi Sekolah

Ketika **Admin Kesiswaan** menutup sesi presensi sekolah:
1. Sistem otomatis menandai siswa yang tidak presensi sebagai ALPHA
2. Email notifikasi dikirim ke orang tua siswa yang alpha
3. Email berisi informasi:
   - Nama siswa
   - Status: ALPHA (Tidak Hadir)
   - Jenis: Presensi Sekolah
   - Tanggal dan waktu penutupan sesi

### Notifikasi Presensi Kelas

Ketika **Guru** menutup sesi presensi kelas:
1. Sistem otomatis menandai siswa yang tidak presensi sebagai ALPHA
2. Email notifikasi dikirim ke orang tua siswa yang alpha
3. Email berisi informasi:
   - Nama siswa
   - Status: ALPHA (Tidak Hadir)
   - Jenis: Presensi Kelas
   - Nama kelas
   - Tanggal dan waktu penutupan sesi

## Template Email

Email yang dikirim menggunakan template HTML profesional dengan:
- Header berwarna merah (peringatan)
- Info box yang jelas
- Informasi lengkap tentang ketidakhadiran
- Saran tindak lanjut untuk orang tua
- Footer dengan informasi sistem

## Troubleshooting

### Email tidak terkirim

1. **Cek error log**: Periksa file error log PHP
2. **Verifikasi kredensial**: Pastikan EMAIL_USERNAME dan EMAIL_PASSWORD benar
3. **Cek firewall**: Pastikan port 587/465 tidak diblokir
4. **Test koneksi SMTP**: 
   ```bash
   telnet smtp.gmail.com 587
   ```

### Email masuk ke Spam

1. Gunakan email domain sekolah (bukan Gmail personal)
2. Setup SPF dan DKIM records di DNS domain
3. Minta orang tua untuk menandai email sebagai "Not Spam"

### Email orang tua tidak terisi

1. Pastikan admin kesiswaan mengisi email orang tua di Buku Induk
2. Cek validitas format email
3. Sistem akan skip siswa yang tidak memiliki email orang tua (tidak error)

## File yang Terlibat

- `app/services/EmailService.php` - Service untuk mengirim email
- `app/models/PresensiModel.php` - Integrasi notifikasi saat marking alpha
- `app/models/BukuIndukModel.php` - Mengambil data kontak orang tua
- `config/config.php` - Konfigurasi email SMTP
- `composer.json` - Dependencies SwiftMailer

## Testing

Untuk testing notifikasi email:

1. Pastikan minimal satu siswa memiliki email orang tua di buku induk
2. Buka sesi presensi sekolah/kelas
3. Jangan lakukan presensi untuk siswa tersebut
4. Tutup sesi presensi
5. Cek inbox email orang tua

## Catatan Penting

- Email dikirim secara asynchronous (tidak menghambat proses marking alpha)
- Jika pengiriman email gagal, error akan di-log tapi proses tetap berlanjut
- Pastikan server memiliki akses internet untuk koneksi ke SMTP server
- Untuk production, disarankan menggunakan email domain sekolah resmi
