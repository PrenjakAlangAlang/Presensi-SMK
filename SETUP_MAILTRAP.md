# SETUP EMAIL DENGAN MAILTRAP (GRATIS & MUDAH)

## Kenapa Pakai Mailtrap?
✅ Port SMTP tidak diblokir ISP
✅ Gratis 500 email/bulan (cukup untuk testing)
✅ Email masuk ke inbox virtual (bagus untuk development)
✅ Bisa preview HTML email dengan sempurna
✅ Setup cuma 5 menit

## Langkah Setup (MUDAH!)

### 1. DAFTAR MAILTRAP (2 menit)
   - Buka: https://mailtrap.io/register/signup
   - Daftar dengan email (atau login via Google/GitHub)
   - Verifikasi email jika diminta

### 2. BUAT INBOX (1 menit)
   - Setelah login, klik "Add Inbox"
   - Beri nama: "Presensi SMK"
   - Klik "Create"

### 3. COPY KREDENSIAL SMTP (1 menit)
   - Pada inbox yang baru dibuat, klik tab "SMTP Settings"
   - Pilih integration: "PHP" atau "Other"
   - Akan muncul:
     ```
     Host: sandbox.smtp.mailtrap.io
     Port: 2525 (atau 25, 465, 587, 2525)
     Username: xxxxxxxxxxx (10-12 karakter)
     Password: yyyyyyyyyyy (10-12 karakter)
     ```

### 4. UPDATE CONFIG.PHP (1 menit)
   
   Buka: `config/config.php`
   
   Ganti bagian EMAIL Configuration dengan:
   
   ```php
   // Email Configuration untuk SwiftMailer
   define('EMAIL_HOST', 'sandbox.smtp.mailtrap.io'); // ✅ Mailtrap SMTP
   define('EMAIL_PORT', 2525); // ✅ Port alternatif tidak diblokir
   define('EMAIL_ENCRYPTION', 'tls'); // atau 'ssl' sesuai instruksi Mailtrap
   define('EMAIL_USERNAME', 'your_mailtrap_username'); // ✅ Dari step 3
   define('EMAIL_PASSWORD', 'your_mailtrap_password'); // ✅ Dari step 3
   define('EMAIL_FROM', 'noreply@presensi-smk.test'); // ✅ Email bebas
   define('EMAIL_FROM_NAME', 'Sistem Presensi SMK'); // ✅ Nama pengirim
   ```

### 5. TEST EMAIL (30 detik)
   ```bash
   php test_smtp_connection.php
   ```
   
   Jika berhasil:
   ```
   ✅ CONNECTION SUCCESS!
   ✅ EMAIL SENT SUCCESSFULLY!
   ```

### 6. CEK INBOX MAILTRAP
   - Buka https://mailtrap.io/inboxes
   - Klik inbox "Presensi SMK"
   - Email test akan muncul di sini!
   - Klik untuk preview HTML

## Cara Kerja Mailtrap

📧 **Email TIDAK terkirim ke email asli**, tapi ditangkap di inbox Mailtrap
📊 **Bagus untuk development**: Bisa test notifikasi tanpa spam email asli
🔍 **Preview HTML**: Bisa lihat tampilan email dengan sempurna
✅ **Setelah production**: Ganti kembali ke Gmail SMTP di server live

## Alternatif Lain (Jika Mailtrap Tidak Mau)

### BREVO (SendInBlue) - 300 email/hari GRATIS
```php
define('EMAIL_HOST', 'smtp-relay.brevo.com');
define('EMAIL_PORT', 587);
define('EMAIL_ENCRYPTION', 'tls');
define('EMAIL_USERNAME', 'your_brevo_email@gmail.com');
define('EMAIL_PASSWORD', 'your_smtp_key_from_brevo');
```
Daftar: https://www.brevo.com/

### MAILGUN - 5,000 email/bulan GRATIS
```php
define('EMAIL_HOST', 'smtp.mailgun.org');
define('EMAIL_PORT', 587);
define('EMAIL_ENCRYPTION', 'tls');
define('EMAIL_USERNAME', 'postmaster@your-sandbox-domain.mailgun.org');
define('EMAIL_PASSWORD', 'your_mailgun_smtp_password');
```
Daftar: https://www.mailgun.com/

## FAQ

**Q: Email orang tua akan terkirim ke email asli mereka?**
A: TIDAK, jika pakai Mailtrap. Email masuk ke inbox Mailtrap (virtual).
   Ini bagus untuk testing. Untuk production, ganti ke Gmail/SMTP asli.

**Q: Kapan ganti ke Gmail lagi?**
A: Setelah deploy ke server production (hosting), biasanya port SMTP tidak diblokir.
   Atau pakai VPS/cloud server yang tidak block port SMTP.

**Q: Bisa pakai untuk production?**
A: Mailtrap khusus development. Untuk production pakai:
   - Gmail SMTP (jika server tidak block)
   - Brevo/SendInBlue (gratis 300/hari)
   - Mailgun (gratis 5000/bulan)
   - Amazon SES
   - SendGrid

**Q: Notifikasi WhatsApp jalan?**
A: Ya! WhatsApp pakai Fonnte API (HTTP), tidak kena block port SMTP.

## Test Aplikasi Lengkap

1. Login sebagai Admin
2. Buka menu "Presensi Sekolah"
3. Buat sesi baru atau tunggu auto-create jam 7 pagi
4. Jangan presensi untuk siswa tertentu
5. Tutup sesi presensi
6. Email notifikasi akan masuk ke inbox Mailtrap!
7. Cek di https://mailtrap.io/inboxes

## Butuh Bantuan?

Jika masih ada error setelah setup Mailtrap:
```bash
php test_smtp_connection.php
```

Atau hubungi support Mailtrap (responsif!): support@mailtrap.io
