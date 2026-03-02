# CARA MENGAKTIFKAN EMAIL NOTIFIKASI

## Masalah yang Terdeteksi
Port SMTP Gmail (465 dan 587) diblokir oleh firewall/network.

## Solusi 1: Matikan Windows Firewall (Sementara untuk Test)

1. Buka Windows Security
2. Pilih "Firewall & network protection"
3. Matikan "Private network" firewall
4. Test lagi dengan: `php test_smtp_connection.php`
5. Jika berhasil, tambahkan exception untuk PHP di firewall

## Solusi 2: Tambah Exception di Windows Firewall

1. Buka Windows Defender Firewall > Advanced Settings
2. Klik "Outbound Rules"
3. Klik "New Rule"
4. Pilih "Port" > Next
5. Pilih "TCP" dan ketik: 587,465
6. Pilih "Allow the connection"
7. Apply to all profiles
8. Beri nama: "PHP SMTP Gmail"

## Solusi 3: Cek Antivirus

Jika menggunakan antivirus (Avast, McAfee, Norton, dll):
- Matikan "Email Protection" atau "SMTP Scanning"
- Atau tambahkan exception untuk smtp.gmail.com

## Solusi 4: Gunakan SMTP Relay Alternatif (Mailtrap untuk Development)

Untuk development/testing, gunakan Mailtrap.io:

1. Daftar di https://mailtrap.io (gratis)
2. Buat inbox baru
3. Salin kredensial SMTP
4. Update di `config/config.php`:

```php
define('EMAIL_HOST', 'sandbox.smtp.mailtrap.io');
define('EMAIL_PORT', 2525);
define('EMAIL_ENCRYPTION', 'tls');
define('EMAIL_USERNAME', 'your_mailtrap_username');
define('EMAIL_PASSWORD', 'your_mailtrap_password');
```

5. Email akan masuk ke inbox Mailtrap (tidak ke email asli)

## Solusi 5: Gunakan Gmail SMTP via PHPMailer dengan OAuth2

Jika masalah persist, pertimbangkan migrasi ke PHPMailer dengan OAuth2.

## Test Setelah Fix

Jalankan:
```bash
php test_smtp_connection.php
```

Jika berhasil, akan muncul:
```
✅ CONNECTION SUCCESS!
✅ EMAIL SENT SUCCESSFULLY!
```

## Verifikasi Email Terkirim

1. Tutup sesi presensi (admin/guru)
2. Siswa yang tidak presensi akan ditandai alpha
3. Email otomatis terkirim ke orang tua
4. Cek inbox email_ortu di tabel buku_induk

## Troubleshooting

Jika masih gagal:
1. Ping smtp.gmail.com dari CMD: `ping smtp.gmail.com`
2. Test telnet: `telnet smtp.gmail.com 587`
3. Cek dengan ISP kalau port SMTP di-block
4. Gunakan VPN jika ISP memblokir SMTP

## Catatan Penting

⚠️ Pastikan data `email_ortu` sudah diisi di tabel `buku_induk`:
- Login sebagai Admin/Admin Kesiswaan
- Menu Buku Induk
- Edit data siswa
- Isi field "Email Orang Tua"
- Save

Tanpa email_ortu, notifikasi tidak akan terkirim!
