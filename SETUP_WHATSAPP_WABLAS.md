# 📱 Setup WhatsApp Notifikasi dengan Wablas

Panduan lengkap untuk mengintegrasikan notifikasi WhatsApp menggunakan layanan **Wablas** pada aplikasi Presensi SMK.

---

## 📋 Daftar Isi
1. [Apa itu Wablas?](#apa-itu-wablas)
2. [Keuntungan Wablas](#keuntungan-wablas)
3. [Cara Daftar Wablas](#cara-daftar-wablas)
4. [Konfigurasi di Aplikasi](#konfigurasi-di-aplikasi)
5. [Testing Koneksi](#testing-koneksi)
6. [Troubleshooting](#troubleshooting)

---

## 🤔 Apa itu Wablas?

**Wablas** adalah layanan WhatsApp API Gateway yang memungkinkan pengiriman pesan WhatsApp otomatis dari aplikasi ke pengguna. Wablas menggunakan WhatsApp Business API resmi dan sangat populer di Indonesia.

### Fitur Utama:
- ✅ **WhatsApp Business API Resmi**
- ✅ **Mudah digunakan & dokumentasi lengkap**
- ✅ **Dashboard berbahasa Indonesia**
- ✅ **Server di Indonesia (response cepat)**
- ✅ **Harga terjangkau & paket fleksibel**
- ✅ **Support 24/7**
- ✅ **Tidak perlu verifikasi nomor penerima** (tidak seperti Twilio sandbox)

---

## 🎯 Keuntungan Wablas

| Fitur | Wablas | Twilio |
|-------|--------|--------|
| Bahasa | 🇮🇩 Indonesia | 🇺🇸 English |
| Server | Indonesia | Singapore/US |
| Harga | Murah (Rp) | Mahal ($) |
| Setup | Mudah | Kompleks |
| Sandbox | ❌ Tidak perlu | ✅ Perlu join |
| Support | WhatsApp/Telegram | Email/Ticket |
| Verifikasi Penerima | ❌ Tidak perlu | ✅ Perlu |

---

## 📝 Cara Daftar Wablas

### Langkah 1: Buat Akun Wablas

1. **Kunjungi**: https://wablas.com
2. **Klik** tombol **"Daftar"** atau **"Register"** (biasanya di pojok kanan atas)
3. **Isi form pendaftaran**:
   - Nama lengkap
   - Email aktif
   - Nomor WhatsApp
   - Password
4. **Verifikasi email** yang dikirim Wablas
5. **Login** ke dashboard Wablas

### Langkah 2: Verifikasi Nomor WhatsApp

1. Di dashboard, pilih menu **"Device"** atau **"Perangkat"**
2. Klik **"Add Device"** atau **"Tambah Perangkat"**
3. **Scan QR Code** menggunakan WhatsApp di HP Anda:
   - Buka WhatsApp → Menu (3 titik) → **Linked Devices**
   - Tap **"Link a Device"**
   - Scan QR Code yang muncul di dashboard Wablas
4. Tunggu hingga status menjadi **"Connected"** ✅

> ⚠️ **Penting**: Gunakan nomor WhatsApp yang tidak sedang aktif di HP lain. Sebaiknya gunakan nomor khusus untuk notifikasi.

### Langkah 3: Beli Paket / Top Up

1. Pilih menu **"Paket"** atau **"Pricing"**
2. Pilih paket yang sesuai kebutuhan:
   - **Starter**: 100-500 pesan/bulan (~Rp 50.000)
   - **Basic**: 1.000-2.000 pesan/bulan (~Rp 100.000)
   - **Pro**: 5.000+ pesan/bulan (~Rp 200.000+)
3. Lakukan pembayaran via:
   - Transfer Bank
   - E-wallet (OVO, Dana, GoPay)
   - Virtual Account
4. Tunggu konfirmasi (biasanya otomatis dalam 5-15 menit)

### Langkah 4: Dapatkan Token API

1. Masuk ke dashboard Wablas
2. Pilih menu **"Device"** atau **"Perangkat"**
3. Pada device yang aktif, klik **"Settings"** atau icon **⚙️**
4. **Copy Token API** yang ditampilkan (contoh: `A1b2C3d4E5f6G7h8I9j0K1L2M3N4O5P6`)
5. Pilih **Domain Server**:
   - `solo.wablas.com` (Jawa Tengah)
   - `pati.wablas.com` (Jawa Tengah)
   - `banten.wablas.com` (Banten)
   - `jogja.wablas.com` (Yogyakarta)
   
> 💡 **Tips**: Pilih server yang paling dekat dengan lokasi sekolah untuk response lebih cepat.

---

## ⚙️ Konfigurasi di Aplikasi

### Langkah 1: Edit File `.env`

Buka file `.env` di root folder project dan update konfigurasi:

```env
# Wablas WhatsApp Configuration
WABLAS_DOMAIN=https://solo.wablas.com
WABLAS_TOKEN=A1b2C3d4E5f6G7h8I9j0K1L2M3N4O5P6
WABLAS_APP_NAME=PresensiSMK
```

**Penjelasan**:
- `WABLAS_DOMAIN`: URL domain Wablas sesuai server yang dipilih
- `WABLAS_TOKEN`: Token API dari dashboard Wablas (ganti dengan token Anda)
- `WABLAS_APP_NAME`: Nama aplikasi yang akan muncul di notifikasi

### Langkah 2: Contoh Konfigurasi Lengkap

Berikut contoh file `.env` yang sudah lengkap:

```env
# Base URL Configuration
BASE_URL=http://localhost/Presensi-SMK

# Wablas WhatsApp Configuration
WABLAS_DOMAIN=https://solo.wablas.com
WABLAS_TOKEN=A1b2C3d4E5f6G7h8I9j0K1L2M3N4O5P6
WABLAS_APP_NAME=SMK Negeri 7 Yogyakarta
```

### Langkah 3: Load Environment Variables

Pastikan file `.env` di-load dengan benar. Jika menggunakan `vlucas/phpdotenv`, tambahkan di `config/config.php`:

```php
<?php
// Load .env file
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

session_start();

// ... konfigurasi lainnya ...
```

---

## 🧪 Testing Koneksi

### Opsi 1: Buat File Test

Buat file `test_wablas.php` di root folder:

```php
<?php
// Load environment
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Load config
require_once __DIR__ . '/config/config.php';

// Load WhatsAppService
require_once __DIR__ . '/app/services/WhatsAppService.php';

// Test connection
$waService = new WhatsAppService();
$result = $waService->testConnection();

echo "<h2>Test Koneksi Wablas</h2>";
echo "<pre>";
print_r($result);
echo "</pre>";

// Test kirim pesan
if ($result['success']) {
    echo "<hr>";
    echo "<h3>Test Kirim Pesan</h3>";
    
    // GANTI dengan nomor WhatsApp Anda untuk testing
    $testPhone = "628123456789"; // Format: 62xxx
    $testName = "Budi Santoso";
    $testDate = date('Y-m-d');
    $testTime = date('H:i:s');
    
    echo "Mengirim pesan test ke: $testPhone<br>";
    
    $sent = $waService->sendAlphaNotificationSekolah(
        $testPhone,
        $testName,
        $testDate,
        $testTime
    );
    
    if ($sent) {
        echo "<p style='color: green;'>✅ Pesan berhasil dikirim!</p>";
    } else {
        echo "<p style='color: red;'>❌ Gagal mengirim pesan. Cek error log.</p>";
    }
}
?>
```

### Opsi 2: Test via Browser

1. Buka browser
2. Akses: `http://localhost/Presensi-SMK/test_wablas.php`
3. Ganti nomor test dengan nomor WhatsApp Anda
4. Refresh halaman
5. Cek WhatsApp Anda untuk melihat notifikasi test

### Opsi 3: Test Manual via cURL

Test langsung ke API Wablas via terminal:

```bash
curl -X POST https://solo.wablas.com/api/send-message \
-H "Authorization: YOUR_WABLAS_TOKEN" \
-H "Content-Type: application/json" \
-d '{
  "phone": "628123456789",
  "message": "Test pesan dari Sistem Presensi SMK"
}'
```

**Response Sukses**:
```json
{
  "status": true,
  "message": "Message sent successfully",
  "data": {
    "message_id": "xxxxx",
    "phone": "628123456789"
  }
}
```

---

## 🔍 Format Nomor Telepon

Wablas mendukung berbagai format nomor:

| Input | Output (Wablas) | Status |
|-------|-----------------|--------|
| `08123456789` | `628123456789` | ✅ Valid |
| `+628123456789` | `628123456789` | ✅ Valid |
| `628123456789` | `628123456789` | ✅ Valid |
| `8123456789` | `628123456789` | ✅ Valid |

> 💡 **Tips**: Sistem akan otomatis format nomor ke format `628xxx` sebelum dikirim ke Wablas.

---

## 🎯 Cara Kerja Notifikasi

### Skenario 1: Presensi Sekolah Alpha

```
1. Admin buka sesi presensi sekolah (07:00)
2. Admin tutup sesi (07:30)
3. Sistem cek siswa yang belum presensi
4. Sistem kirim notifikasi WA ke orang tua:
   ⚠️ NOTIFIKASI KETIDAKHADIRAN
   👤 Nama Siswa: Budi Santoso
   📅 Tanggal: 02 Februari 2026
   🕒 Waktu Penutupan: 07:30 WIB
   📋 Status: ALPHA (Tidak Hadir)
```

### Skenario 2: Presensi Kelas Alpha

```
1. Guru buka sesi presensi kelas Matematika (08:00)
2. Guru tutup sesi (08:45)
3. Sistem cek siswa yang belum presensi
4. Sistem kirim notifikasi WA ke orang tua:
   ⚠️ NOTIFIKASI KETIDAKHADIRAN KELAS
   👤 Nama Siswa: Ani Wijaya
   📚 Kelas: Matematika - XII RPL 1
   📅 Tanggal: 02 Februari 2026
   🕒 Waktu Penutupan: 08:45 WIB
   📋 Status: ALPHA (Tidak Hadir)
```

---

## 🐛 Troubleshooting

### 1️⃣ Pesan Tidak Terkirim

**Cek hal berikut**:

```php
// Pastikan token sudah benar
echo WABLAS_TOKEN; // Harus ada isinya, bukan 'your-wablas-token-here'

// Pastikan domain sudah benar
echo WABLAS_DOMAIN; // Harus https://solo.wablas.com atau domain lain
```

**Solusi**:
- Update token di file `.env`
- Pastikan tidak ada spasi di awal/akhir token
- Pastikan device masih connected di dashboard Wablas

### 2️⃣ Error: "Unauthorized" atau HTTP 401

**Penyebab**: Token tidak valid atau salah

**Solusi**:
1. Login ke dashboard Wablas
2. Pilih menu Device
3. Copy ulang token yang benar
4. Update di file `.env`
5. Restart server (jika pakai Apache/Nginx)

### 3️⃣ Error: "Device Not Connected"

**Penyebab**: Device WhatsApp disconnect

**Solusi**:
1. Login ke dashboard Wablas
2. Cek status device
3. Jika "Disconnected", scan ulang QR Code
4. Pastikan HP WhatsApp tetap online

### 4️⃣ Error: "Insufficient Balance"

**Penyebab**: Saldo/paket habis

**Solusi**:
1. Login ke dashboard Wablas
2. Cek saldo/kuota pesan
3. Top up atau beli paket baru
4. Tunggu konfirmasi pembayaran

### 5️⃣ Nomor Tidak Menerima Pesan

**Cek**:
- Apakah nomor WhatsApp aktif?
- Apakah format nomor sudah benar? (62xxx)
- Apakah nomor diblokir oleh penerima?
- Cek di dashboard Wablas → History, apakah pesan terkirim?

**Status di Wablas**:
- ✅ **Sent**: Terkirim ke server WhatsApp
- ✅ **Delivered**: Sampai ke HP penerima
- ✅ **Read**: Dibaca penerima
- ❌ **Failed**: Gagal terkirim

### 6️⃣ Cek Error Log

Lihat file error log PHP:

```bash
# Windows (Laragon)
C:\laragon\www\Presensi-SMK\logs\error.log

# atau cek di
C:\laragon\bin\apache\logs\error.log
```

Atau tambahkan logging di `WhatsAppService.php`:

```php
error_log('Wablas Response: ' . print_r($responseData, true));
```

---

## 📊 Monitoring & Analytics

### Dashboard Wablas

Login ke dashboard Wablas (akses melalui https://wablas.com → Login) untuk melihat:

1. **Device Status**: Online/Offline
2. **Message History**: Semua pesan yang dikirim
3. **Delivery Report**: Status pengiriman (sent/delivered/read)
4. **Usage Statistics**: Jumlah pesan terpakai
5. **Balance**: Sisa kuota/saldo

### Laporan Bulanan

Rekomendasi untuk monitoring:

- **Total pesan dikirim**: Pantau penggunaan bulanan
- **Success rate**: Persentase pesan terkirim
- **Failed messages**: Pesan yang gagal (untuk investigasi)
- **Peak hours**: Jam sibuk pengiriman

---

## 💰 Estimasi Biaya

### Perhitungan Kebutuhan

**Contoh SMK dengan 500 siswa**:

| Skenario | Perhitungan | Total/Bulan |
|----------|-------------|-------------|
| Presensi Sekolah Alpha (5% siswa) | 20 hari × 25 siswa = 500 pesan | 500 |
| Presensi Kelas Alpha (3% per sesi) | 20 hari × 8 sesi × 15 siswa = 2.400 pesan | 2.400 |
| **TOTAL** | | **~2.900 pesan/bulan** |

**Rekomendasi Paket**: Paket Pro (5.000 pesan) ~ Rp 200.000/bulan

### Tips Hemat Kuota

1. **Filter notifikasi**: Hanya kirim untuk alpha, skip untuk hadir
2. **Batching**: Kirim 1x di akhir hari (gabungkan semua kelas)
3. **Threshold**: Kirim hanya jika alpha ≥3x dalam seminggu
4. **Weekend**: Nonaktifkan notifikasi di hari libur

---

## 📞 Dukungan

### Kontak Wablas Support

- **Website**: https://wablas.com
- **WhatsApp**: +62 823-3838-5000
- **Email**: support@wablas.com
- **Telegram**: @wablasofficial
- **Dokumentasi**: https://wablas.com/docs

### Grup Komunitas

- **Facebook Group**: Wablas Indonesia
- **Telegram Group**: @wablas_group

---

## 📚 Referensi API

### Endpoint: Send Message

**URL**: `POST https://{domain}/api/send-message`

**Headers**:
```
Authorization: YOUR_TOKEN
Content-Type: application/json
```

**Body**:
```json
{
  "phone": "628123456789",
  "message": "Isi pesan disini"
}
```

**Response Success**:
```json
{
  "status": true,
  "message": "Message sent successfully",
  "data": {
    "message_id": "ABC123",
    "phone": "628123456789"
  }
}
```

**Response Error**:
```json
{
  "status": false,
  "message": "Error description",
  "code": "ERROR_CODE"
}
```

### Error Codes

| Code | Deskripsi | Solusi |
|------|-----------|--------|
| `INVALID_TOKEN` | Token tidak valid | Cek token di dashboard |
| `DEVICE_NOT_CONNECTED` | Device offline | Scan ulang QR Code |
| `INSUFFICIENT_BALANCE` | Kuota habis | Top up saldo |
| `INVALID_PHONE` | Format nomor salah | Gunakan format 62xxx |
| `RATE_LIMIT` | Terlalu banyak request | Tunggu beberapa detik |

---

## ✅ Checklist Setup

Gunakan checklist ini untuk memastikan setup sudah benar:

- [ ] Sudah daftar akun Wablas
- [ ] Sudah verifikasi email
- [ ] Sudah scan QR Code (device connected)
- [ ] Sudah beli paket/top up saldo
- [ ] Sudah copy token API dari dashboard
- [ ] Sudah update file `.env` dengan token
- [ ] Sudah update `WABLAS_DOMAIN` sesuai server
- [ ] Sudah test koneksi (file `test_wablas.php`)
- [ ] Sudah test kirim pesan ke nomor sendiri
- [ ] Sudah cek pesan masuk di WhatsApp
- [ ] Sudah input nomor orang tua siswa di database

---

## 🎉 Selesai!

Setup Wablas sudah selesai! Sistem sekarang dapat mengirim notifikasi WhatsApp otomatis ke orang tua siswa ketika siswa alpha (tidak hadir).

**Next Steps**:
1. Input nomor WhatsApp orang tua di menu Users
2. Test dengan tutup sesi presensi
3. Monitor di dashboard Wablas
4. Sesuaikan template pesan jika diperlukan

---

**Dibuat oleh**: Sistem Presensi SMK  
**Terakhir update**: Februari 2026  
**Versi**: 1.0
