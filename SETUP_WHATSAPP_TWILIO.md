# Panduan Setup Notifikasi WhatsApp dengan Twilio

Panduan ini menjelaskan langkah-langkah untuk mengaktifkan notifikasi WhatsApp otomatis ke orang tua siswa ketika siswa tidak hadir (alpha) menggunakan Twilio API.

## 📋 Persyaratan

1. Akun Twilio (gratis untuk testing dengan credits)
2. Nomor WhatsApp untuk menerima notifikasi testing
3. PHP dengan ekstensi cURL aktif

## 🚀 Langkah-Langkah Setup

### 1. Registrasi Akun Twilio

1. Kunjungi [https://www.twilio.com/try-twilio](https://www.twilio.com/try-twilio)
2. Klik **Sign up** untuk membuat akun baru
3. Isi form registrasi:
   - Email
   - Password
   - First Name & Last Name
4. Verifikasi email Anda
5. Verifikasi nomor telepon Anda (untuk keamanan)
6. Login ke Twilio Console

### 2. Dapatkan Kredensial API

Setelah login ke [Twilio Console](https://console.twilio.com/):

1. Di dashboard, Anda akan melihat:
   - **Account SID** (contoh: ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx)
   - **Auth Token** (klik "View" untuk melihat)
2. **Copy dan simpan** kedua nilai tersebut dengan aman
3. Jangan share kredensial ini ke publik!

### 3. Setup WhatsApp Sandbox (Testing - GRATIS)

Untuk testing gratis, gunakan Twilio Sandbox for WhatsApp:

#### A. Aktivasi Sandbox

1. Di Twilio Console, buka menu **Messaging** → **Try it out** → **Send a WhatsApp message**
2. Atau langsung ke: [https://console.twilio.com/us1/develop/sms/try-it-out/whatsapp-learn](https://console.twilio.com/us1/develop/sms/try-it-out/whatsapp-learn)
3. Anda akan melihat:
   - **Sandbox Number**: `+1 415 523 8886` (nomor WhatsApp Twilio)
   - **Join Code**: kata unik untuk join sandbox (contoh: `join <word>-<word>`)

#### B. Join Sandbox dengan WhatsApp Anda

1. Buka **WhatsApp** di HP Anda
2. Buat chat baru dengan nomor: **+1 415 523 8886**
3. Kirim pesan dengan format yang tertera di console
   - Contoh: `join carbon-taught`
4. Tunggu balasan dari Twilio:
   - ✅ "**Twilio Sandbox: ✅ You are all set!**"
5. Nomor Anda sekarang **sudah terdaftar** dan bisa menerima pesan dari sandbox

**Penting:** 
- Setiap nomor yang akan menerima pesan harus join sandbox dulu
- Sandbox gratis dan cocok untuk testing
- Pesan akan ada prefix "Twilio Sandbox:"

### 4. Konfigurasi di Aplikasi

Buka file `config/config.php` dan update konfigurasi berikut:

```php
// WhatsApp Configuration untuk Twilio
define('TWILIO_ACCOUNT_SID', 'YOUR_ACCOUNT_SID_HERE'); // Account SID dari console
define('TWILIO_AUTH_TOKEN', 'YOUR_AUTH_TOKEN_HERE'); // Auth Token dari console
define('TWILIO_WHATSAPP_FROM', 'whatsapp:+14155238886'); // Sandbox number (default)
define('TWILIO_APP_NAME', 'PresensiSMK'); // Nama aplikasi
```

**Contoh konfigurasi (sandbox mode):**
```php
define('TWILIO_ACCOUNT_SID', 'your_account_sid');
define('TWILIO_AUTH_TOKEN', 'your_auth_token_here_32_chars');
define('TWILIO_WHATSAPP_FROM', 'whatsapp:+14155238886'); // Nomor sandbox Twilio
define('TWILIO_APP_NAME', 'PresensiSMK');
```

### 5. Format Nomor WhatsApp Orang Tua

Pastikan nomor WhatsApp orang tua di database (tabel `buku_induk`, kolom `no_telp_ortu`) sudah dalam format yang benar:

**Format yang diterima:**
- `08123456789` ✅
- `628123456789` ✅
- `+628123456789` ✅
- `8123456789` ✅

Sistem akan otomatis mengkonversi ke format Twilio: `whatsapp:+628XXXXXXXXX`

### 6. Testing Notifikasi

#### A. Test Manual dengan File Test

1. Akses: `http://localhost/Presensi-SMK/test_whatsapp.php`
2. Pastikan status konfigurasi **✅ Valid**
3. Masukkan **nomor HP yang sudah join sandbox**
4. Klik **"📤 Kirim Test WhatsApp"**
5. Cek WhatsApp Anda, seharusnya menerima 2 pesan:
   - Notifikasi Presensi Sekolah
   - Notifikasi Presensi Kelas

#### B. Test Otomatis saat Alpha

1. Login sebagai **Admin** atau **Guru**
2. Buka sesi presensi (sekolah atau kelas)
3. Tutup sesi (manual atau otomatis)
4. Siswa yang tidak hadir akan ditandai **alpha**
5. Sistem akan otomatis kirim notifikasi:
   - ✉️ Email ke orang tua
   - 📱 WhatsApp ke orang tua (jika nomor valid)

### 7. Upgrade ke Production (Opsional)

Jika ingin menggunakan nomor WhatsApp sendiri (tanpa prefix "Twilio Sandbox"):

#### Persyaratan:
- Akun Twilio berbayar (isi credits)
- Nomor WhatsApp Business yang sudah diverifikasi
- Biaya: ~$0.005 per pesan (harga bervariasi per negara)

#### Langkah:
1. Di Twilio Console: **Messaging** → **WhatsApp senders**
2. Klik **Request to enable your Twilio number for WhatsApp**
3. Pilih nomor Twilio atau beli baru
4. Ikuti proses verifikasi dengan Facebook Business Manager
5. Tunggu approval (bisa 1-3 hari kerja)
6. Update config dengan nomor production:
   ```php
   define('TWILIO_WHATSAPP_FROM', 'whatsapp:+628XXXXXXXXXX');
   ```

### 8. Monitoring dan Logs

#### Twilio Console Logs
1. Login ke [Twilio Console](https://console.twilio.com/)
2. Menu **Monitor** → **Logs** → **Messaging**
3. Lihat semua pesan yang terkirim/gagal
4. Detail error code dan status

#### PHP Error Logs
Cek file log di server:
- `C:\laragon\www\Presensi-SMK\storage\logs\` (jika ada)
- `C:\laragon\tmp\error.log`
- Error prefix: `WhatsApp cURL error:` atau `WhatsApp API error:`

## 📱 Cara Kerja Sistem

1. **Sesi ditutup** → Sistem cek siswa yang belum presensi
2. **Mark as Alpha** → Siswa tidak hadir ditandai alpha di database
3. **Get parent contact** → Ambil nomor WA dan email dari tabel `buku_induk`
4. **Send notifications**:
   - ✉️ Email via EmailService (SwiftMailer)
   - 📱 WhatsApp via WhatsAppService (Twilio)

## 🔧 Troubleshooting

### Error: "Account SID belum dikonfigurasi"
- Pastikan `TWILIO_ACCOUNT_SID` sudah diisi di `config.php`
- Jangan gunakan nilai default `your-twilio-account-sid-here`
- Copy dari Twilio Console dashboard

### Error: "Auth Token belum dikonfigurasi"
- Pastikan `TWILIO_AUTH_TOKEN` sudah diisi di `config.php`
- Jangan gunakan nilai default
- Klik "View" di Twilio Console untuk melihat token

### Pesan tidak terkirim (Sandbox)
- **Penerima belum join sandbox:**
  - Kirim `join <code>` ke +1 415 523 8886 di WhatsApp
  - Tunggu konfirmasi dari Twilio
- **Nomor salah format:**
  - Pastikan nomor valid (08xxx atau 628xxx)
- **Kredensial salah:**
  - Cek lagi Account SID dan Auth Token
  - Copy paste langsung dari console

### Error: "21211: Invalid 'To' Phone Number"
- Format nomor tidak valid
- Pastikan nomor dimulai dengan 0, 62, +62, atau 8
- Cek panjang nomor (minimal 10 digit)

### Error: "21408: Permission to send an SMS has not been enabled"
- Akun Twilio belum diverifikasi
- Verifikasi nomor telepon di console
- Atau nomor penerima belum join sandbox

### Error: "cURL error"
- Pastikan PHP cURL extension aktif
- Cek koneksi internet server
- Cek firewall tidak memblokir api.twilio.com

## 📊 Dashboard & Monitoring

### Twilio Console Features:
- **Messaging Logs**: Track semua pesan yang terkirim
- **Debugger**: Lihat error detail dengan code
- **Usage**: Monitor penggunaan dan biaya
- **Analytics**: Grafik delivery rate

Akses: [https://console.twilio.com/](https://console.twilio.com/)

### Informasi Delivery Status:
- **Queued**: Pesan dalam antrian
- **Sent**: Terkirim ke provider
- **Delivered**: Sampai ke penerima ✅
- **Failed**: Gagal terkirim ❌
- **Undelivered**: Tidak bisa dikirim

## 💰 Pricing (Info)

### Sandbox (Testing):
- ✅ **Gratis** untuk testing
- ✅ Unlimited pesan (untuk testing)
- ⚠️ Limited recipients (hanya yang join sandbox)
- ⚠️ Pesan ada prefix "Twilio Sandbox:"
- ✅ Cocok untuk development

### Production (WhatsApp Business API):
- 💰 **Berbayar** per pesan
- 💰 Indonesia: ~$0.005 - $0.01 per pesan
- ✅ Unlimited recipients
- ✅ Tanpa prefix sandbox
- ✅ Custom sender name/number
- ⚠️ Perlu verifikasi bisnis

**Credits Gratis:**
- Akun baru dapat trial credits ($15-$20)
- Cukup untuk testing ~3000 pesan
- Tidak perlu input kartu kredit untuk sandbox

## 🔐 Keamanan

### Best Practices:
1. **Jangan commit** kredensial ke Git/GitHub
2. Tambahkan `config.php` ke `.gitignore`
3. Gunakan **environment variables** untuk production
4. **Rotate** Auth Token secara berkala
5. **Batasi** IP access di Twilio (jika perlu)

### .gitignore (recommended):
```
config/config.php
.env
*.log
```

### Environment Variables (production):
```php
// Gunakan getenv() untuk production
define('TWILIO_ACCOUNT_SID', getenv('TWILIO_ACCOUNT_SID'));
define('TWILIO_AUTH_TOKEN', getenv('TWILIO_AUTH_TOKEN'));
```

## 📞 Support & Resources

### Dokumentasi Resmi:
- Twilio WhatsApp API: [https://www.twilio.com/docs/whatsapp](https://www.twilio.com/docs/whatsapp)
- Quick Start Guide: [https://www.twilio.com/docs/whatsapp/quickstart](https://www.twilio.com/docs/whatsapp/quickstart)
- Error Codes: [https://www.twilio.com/docs/api/errors](https://www.twilio.com/docs/api/errors)

### Support:
- Help Center: [https://support.twilio.com/](https://support.twilio.com/)
- Community Forum: [https://www.twilio.com/community](https://www.twilio.com/community)
- Email: help@twilio.com

## ✅ Checklist Setup

- [ ] Akun Twilio sudah dibuat dan terverifikasi
- [ ] Account SID dan Auth Token sudah didapat
- [ ] Nomor HP sudah join Twilio Sandbox (+1 415 523 8886)
- [ ] Config.php sudah diupdate dengan kredensial Twilio
- [ ] Nomor HP orang tua sudah diisi di buku induk (database)
- [ ] Test manual berhasil (test_whatsapp.php)
- [ ] Test otomatis saat alpha berhasil

## 🎯 Quick Start (TL;DR)

1. **Daftar** di [twilio.com/try-twilio](https://www.twilio.com/try-twilio)
2. **Copy** Account SID & Auth Token dari console
3. **Join** sandbox: kirim `join <code>` ke +1 415 523 8886 di WhatsApp
4. **Update** `config/config.php` dengan kredensial
5. **Test** via `test_whatsapp.php`
6. **Done!** 🎉

---

**Selamat!** Sistem notifikasi WhatsApp dengan Twilio sudah aktif. Orang tua akan menerima notifikasi otomatis ketika siswa alpha. 📱✅
