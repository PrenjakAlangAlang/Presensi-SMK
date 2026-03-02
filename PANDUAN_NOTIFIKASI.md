# Panduan Setup Notifikasi Email & WhatsApp

## 📧 Email Notifikasi (SwiftMailer)

Notifikasi email sudah dikonfigurasi dan siap digunakan.

### Konfigurasi yang Sudah Ada:
- **SMTP Server**: smtp.gmail.com
- **Port**: 465 (SSL)
- **Email Pengirim**: kristinluthfi@gmail.com
- **App Password**: Sudah dikonfigurasi di `config/config.php`

### Cara Kerja:
1. Ketika admin/guru menutup sesi presensi
2. System otomatis deteksi siswa yang **ALPHA** (tidak hadir)
3. Email otomatis terkirim ke **email_ortu** yang terdaftar di Buku Induk

---

## 📱 WhatsApp Notifikasi (Fonnte)

### Setup Fonnte:

1. **Daftar di Fonnte.com**
   - Kunjungi: https://fonnte.com/
   - Register akun baru
   - Verifikasi email

2. **Dapatkan Token**
   - Login ke dashboard Fonnte
   - Klik menu **"API"** atau **"Settings"**
   - Copy **API Token** Anda

3. **Update File .env**
   ```env
   FONNTE_TOKEN=paste_token_anda_disini
   FONNTE_APP_NAME=PresensiSMK
   ```

4. **Sambungkan Nomor WhatsApp**
   - Di dashboard Fonnte, scan QR Code dengan WhatsApp Anda
   - Nomor tersebut akan digunakan untuk mengirim notifikasi

### Cara Kerja:
1. Ketika admin/guru menutup sesi presensi
2. System otomatis deteksi siswa yang **ALPHA**
3. WhatsApp otomatis terkirim ke **no_telp_ortu** yang terdaftar di Buku Induk

---

## 📋 Data Orang Tua yang Diperlukan

Agar notifikasi terkirim, **data orang tua harus diisi di Buku Induk**:

### Untuk Admin/Admin Kesiswaan:
1. Login → Menu **"Buku Induk"**
2. Pilih siswa → Isi data:
   - **Email Orang Tua/Wali**: untuk notifikasi email
   - **No. Telepon Orang Tua**: untuk notifikasi WhatsApp (format: 08xxx atau 62xxx)

### Untuk Siswa:
1. Login → Menu **"Buku Induk"**
2. Lengkapi form dengan data orang tua:
   - Email Orang Tua
   - No. Telepon Orang Tua

---

## 🔔 Kapan Notifikasi Terkirim?

### Presensi Sekolah:
- **Admin** menutup sesi → sistem kirim notifikasi ke orang tua siswa yang alpha
- **Auto-close** (expired) → notifikasi juga terkirim otomatis

### Presensi Kelas:
- **Guru** menutup sesi kelas → sistem kirim notifikasi ke orang tua siswa yang alpha

---

## ✅ Test Notifikasi

### Test Email:
1. Isi email orang tua di Buku Induk
2. Buat sesi presensi sekolah
3. Jangan absen sebagai siswa (biarkan alpha)
4. Tutup sesi presensi
5. Cek email orang tua

### Test WhatsApp:
1. Pastikan token Fonnte sudah benar di `.env`
2. Isi nomor WA orang tua di Buku Induk (format: 08xxx)
3. Buat sesi presensi
4. Jangan absen (biarkan alpha)
5. Tutup sesi presensi
6. Cek WhatsApp orang tua

---

## 🐛 Troubleshooting

### Email Tidak Terkirim:
- Cek apakah email_ortu sudah diisi di Buku Induk
- Pastikan App Password Gmail benar di `config/config.php`
- Cek log error di file: `php error_log`

### WhatsApp Tidak Terkirim:
- Pastikan `FONNTE_TOKEN` benar di file `.env`
- Cek nomor WA sudah tersambung di dashboard Fonnte
- Format nomor harus benar: `08123456789` atau `628123456789`
- Cek log error: `error_log('Fonnte API error: ...')`
- Pastikan saldo/quota Fonnte masih ada

### Cek Log Error:
```powershell
# Di terminal PowerShell
Get-Content C:\laragon\www\Presensi-SMK\error.log -Tail 50
```

---

## 📝 Format Notifikasi

### Email:
- Subject: "Notifikasi Ketidakhadiran - [Nama Siswa]"
- Body: Template HTML profesional dengan detail lengkap

### WhatsApp:
```
⚠️ NOTIFIKASI KETIDAKHADIRAN

Kepada Yth. Orang Tua/Wali,

Kami informasikan bahwa:

👤 Nama Siswa: [Nama]
📅 Tanggal: [Tanggal]
🕒 Waktu Penutupan: [Waktu] WIB
📋 Status: ALPHA (Tidak Hadir)
📍 Jenis: Presensi Sekolah/Kelas

Siswa tercatat TIDAK MELAKUKAN PRESENSI...

---
🏫 PresensiSMK
Pesan otomatis, mohon tidak membalas
```

---

## 💡 Tips

1. **Pastikan data orang tua lengkap** - Tanpa email/no HP, notifikasi tidak akan terkirim
2. **Format nomor WhatsApp** - Gunakan '08xxx' atau '62xxx' tanpa spasi/karakter khusus
3. **Gunakan Gmail App Password** - Jangan gunakan password akun langsung
4. **Monitor quota Fonnte** - Pastikan masih ada saldo untuk kirim pesan
5. **Test berkala** - Lakukan test setiap bulan untuk memastikan fitur berjalan

---

## 📞 Support

Jika ada masalah:
1. Cek file log error PHP
2. Cek dokumentasi Fonnte: https://fonnte.com/docs
3. Hubungi support Fonnte untuk masalah API
