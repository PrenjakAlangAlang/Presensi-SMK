<?php
// config_mailtrap_example.php
// CONTOH KONFIGURASI UNTUK MAILTRAP
// Copy konfigurasi ini ke config/config.php setelah dapat kredensial dari Mailtrap

/*
================================================================================
LANGKAH-LANGKAH SETUP MAILTRAP:
================================================================================

1. DAFTAR DI MAILTRAP
   https://mailtrap.io/register/signup

2. BUAT INBOX BARU
   - Setelah login, klik "Add Inbox"
   - Nama: "Presensi SMK"

3. COPY KREDENSIAL
   - Klik inbox "Presensi SMK"
   - Tab "SMTP Settings"
   - Pilih Integration: "Other" atau "PHP"
   - Copy Username dan Password

4. UPDATE CONFIG.PHP
   Ganti bagian Email Configuration dengan konfigurasi di bawah ini

5. TEST
   php test_smtp_connection.php

================================================================================
*/

// KONFIGURASI MAILTRAP (GANTI VALUES SESUAI KREDENSIAL ANDA)
define('EMAIL_HOST', 'sandbox.smtp.mailtrap.io'); 
define('EMAIL_PORT', 2525); // Bisa juga: 25, 465, 587, 2525
define('EMAIL_ENCRYPTION', 'tls'); // atau 'ssl' untuk port 465
define('EMAIL_USERNAME', 'GANTI_DENGAN_USERNAME_MAILTRAP'); // ← Dari step 3
define('EMAIL_PASSWORD', 'GANTI_DENGAN_PASSWORD_MAILTRAP'); // ← Dari step 3
define('EMAIL_FROM', 'noreply@presensi-smk.test'); // Email bebas (tidak harus real)
define('EMAIL_FROM_NAME', 'Sistem Presensi SMK');

/*
================================================================================
CONTOH SETELAH DIGANTI:
================================================================================

define('EMAIL_HOST', 'sandbox.smtp.mailtrap.io'); 
define('EMAIL_PORT', 2525);
define('EMAIL_ENCRYPTION', 'tls');
define('EMAIL_USERNAME', 'a1b2c3d4e5f6g7'); // ← Username dari Mailtrap
define('EMAIL_PASSWORD', 'x9y8z7w6v5u4t3'); // ← Password dari Mailtrap
define('EMAIL_FROM', 'noreply@presensi-smk.test');
define('EMAIL_FROM_NAME', 'Sistem Presensi SMK');

================================================================================
CEK EMAIL DI MAILTRAP:
================================================================================

Setelah aplikasi kirim email:
1. Buka https://mailtrap.io/inboxes
2. Klik inbox "Presensi SMK"
3. Email akan muncul di sini (BUKAN di Gmail asli)

Ini bagus untuk development/testing!

================================================================================
UNTUK PRODUCTION (Server Live):
================================================================================

Ganti kembali ke Gmail SMTP:

define('EMAIL_HOST', 'smtp.gmail.com');
define('EMAIL_PORT', 587);
define('EMAIL_ENCRYPTION', 'tls');
define('EMAIL_USERNAME', 'kristinluthfi@gmail.com');
define('EMAIL_PASSWORD', 'ggwh hjvb rzja jkfh'); // Gmail App Password
define('EMAIL_FROM', 'kristinluthfi@gmail.com');
define('EMAIL_FROM_NAME', 'Sistem Presensi SMK');

Atau gunakan Brevo (gratis 300 email/hari):

define('EMAIL_HOST', 'smtp-relay.brevo.com');
define('EMAIL_PORT', 587);
define('EMAIL_ENCRYPTION', 'tls');
define('EMAIL_USERNAME', 'your_brevo_email@gmail.com');
define('EMAIL_PASSWORD', 'your_brevo_smtp_key');
define('EMAIL_FROM', 'your_brevo_email@gmail.com');
define('EMAIL_FROM_NAME', 'Sistem Presensi SMK');

================================================================================
*/
?>
