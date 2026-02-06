# Panduan Setup Notifikasi WhatsApp dengan Fonnte

Panduan ini menjelaskan langkah-langkah untuk mengaktifkan notifikasi WhatsApp otomatis ke orang tua siswa menggunakan Fonnte API.

## 📋 Mengapa Fonnte?

- ✅ **Support Indonesia** - Tidak ada batasan geografis
- ✅ **Mudah digunakan** - Setup cepat, hanya perlu token
- ✅ **Harga terjangkau** - Cocok untuk sekolah
- ✅ **Tidak perlu sandbox** - Langsung kirim ke nomor manapun
- ✅ **Dashboard lengkap** - Monitor pesan yang terkirim

## 🚀 Langkah-Langkah Setup

### 1. Registrasi Akun Fonnte

1. Kunjungi [https://fonnte.com](https://fonnte.com)
2. Klik **Daftar** atau **Sign Up**
3. Isi form registrasi:
   - Email
   - Password
   - Nama
4. Verifikasi email Anda
5. Login ke Dashboard Fonnte

### 2. Hubungkan WhatsApp Anda

#### A. Pilih Metode Koneksi

Fonnte menawarkan 2 cara:
- **WhatsApp Personal** (Gratis untuk testing)
- **WhatsApp Business API** (Berbayar, untuk production)

#### B. Setup WhatsApp Personal (Gratis - Recommended untuk Testing)

1. Di Dashboard Fonnte, pilih **Device** → **Connect Device**
2. Scan **QR Code** dengan WhatsApp Anda:
   - Buka WhatsApp di HP
   - Tap menu (⋮) → **Linked Devices**
   - Tap **Link a Device**
   - Scan QR Code di dashboard Fonnte
3. Tunggu sampai status **Connected** ✅
4. WhatsApp Anda sekarang terhubung dengan Fonnte

**⚠️ Penting:**
- HP harus tetap online dan WhatsApp aktif
- Jangan logout dari WhatsApp Web
- Pesan akan terkirim dari nomor WhatsApp Anda sendiri

### 3. Dapatkan Token API

1. Di Dashboard Fonnte, buka menu **Settings** atau **API**
2. Anda akan melihat **Token** Anda
3. Copy token tersebut (contoh: `abc123xyz456...`)
4. **Jangan share token** ke publik!

### 4. Konfigurasi di Aplikasi

Update file `.env` dengan token Fonnte Anda:

```env
# Fonnte WhatsApp Configuration
FONNTE_TOKEN=your-fonnte-token-from-dashboard
FONNTE_APP_NAME=PresensiSMK
```

**Contoh:**
```env
FONNTE_TOKEN=abc123xyz456def789ghi012jkl345mno678pqr901stu234
FONNTE_APP_NAME=PresensiSMK
```

### 5. Format Nomor WhatsApp Orang Tua

Pastikan nomor WhatsApp orang tua di database (tabel `buku_induk`, kolom `no_telp_ortu`) sudah dalam format yang benar:

**Format yang diterima:**
- `08123456789` ✅
- `628123456789` ✅
- `+628123456789` ✅
- `8123456789` ✅

Sistem akan otomatis mengkonversi ke format Fonnte: `628XXXXXXXXX`

### 6. Testing Notifikasi

#### A. Test Manual dengan File Test

1. Akses: `http://localhost/Presensi-SMK/test_whatsapp.php`
2. Pastikan status konfigurasi **✅ Valid**
3. Masukkan nomor HP Indonesia (format 08xxx atau 628xxx)
4. Klik **"📤 Kirim Test WhatsApp"**
5. Cek WhatsApp penerima, seharusnya menerima 2 pesan:
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

### 7. Monitoring

#### Dashboard Fonnte
1. Login ke [Fonnte Dashboard](https://fonnte.com)
2. Menu **History** atau **Messages**
3. Lihat semua pesan yang terkirim
4. Cek status: Success, Failed, Pending

#### Cek Saldo/Quota
- Menu **Pricing** atau **Credits**
- Pastikan saldo cukup untuk kirim pesan
- Top-up jika diperlukan

## 💰 Harga Fonnte (2026)

**WhatsApp Personal (HP Pribadi):**
- Gratis untuk testing (limited)
- Rp 150-250/pesan untuk production
- Minimal top-up: Rp 50.000

**WhatsApp Business API:**
- Rp 300-500/pesan
- Lebih stabil dan professional
- Support blast message

*Harga dapat berubah, cek fonnte.com untuk info terbaru*

## 📱 Cara Kerja Sistem

1. **Sesi ditutup** → Sistem cek siswa yang belum presensi
2. **Mark as Alpha** → Siswa tidak hadir ditandai alpha di database
3. **Get parent contact** → Ambil nomor WA dan email dari tabel `buku_induk`
4. **Send notifications**:
   - ✉️ Email via EmailService (SwiftMailer)
   - 📱 WhatsApp via WhatsAppService (Fonnte)

## 🔧 Troubleshooting

### Error: "Token Fonnte belum dikonfigurasi"
- Pastikan `FONNTE_TOKEN` sudah diisi di file `.env`
- Jangan gunakan nilai default `your-fonnte-token-here`
- Copy langsung dari Fonnte Dashboard

### Pesan tidak terkirim
1. **Cek koneksi device:**
   - Pastikan WhatsApp di HP masih terhubung
   - Cek status di Dashboard: harus **Connected** ✅
   
2. **Cek saldo:**
   - Pastikan masih ada quota/credits
   - Top-up jika habis

3. **Cek format nomor:**
   - Harus nomor Indonesia valid (08xxx atau 628xxx)
   - Nomor penerima harus aktif WhatsApp

4. **Cek token:**
   - Pastikan token tidak expired
   - Re-generate token jika perlu

### Error: "Device not connected"
- WhatsApp di HP logout atau tidak online
- Scan ulang QR Code di Dashboard
- Pastikan internet HP stabil

### Pesan delay atau pending
- Server Fonnte sedang sibuk (normal)
- Tunggu beberapa menit
- Cek di History dashboard

## 🔐 Keamanan

1. **Jangan commit file .env** ke Git
2. **Backup token** di tempat aman
3. **Monitor penggunaan** secara berkala
4. **Rotate token** secara periodik (opsional)

## 📞 Support

- Website: [https://fonnte.com](https://fonnte.com)
- WhatsApp CS: Cek di website Fonnte
- Dokumentasi API: [https://fonnte.com/api](https://fonnte.com/api)

## ✅ Checklist Setup

- [ ] Registrasi akun Fonnte
- [ ] Hubungkan WhatsApp device
- [ ] Copy token dari dashboard
- [ ] Update file .env dengan token
- [ ] Test kirim pesan via test_whatsapp.php
- [ ] Verifikasi pesan diterima
- [ ] Test otomatis dengan tutup sesi presensi
- [ ] Monitor di dashboard Fonnte

---

**Selamat! 🎉** Sistem notifikasi WhatsApp dengan Fonnte sudah siap digunakan.
