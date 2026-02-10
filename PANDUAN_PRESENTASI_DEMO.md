# 📋 PANDUAN PRESENTASI DEMO APLIKASI PRESENSI SMK

## 🎯 Tujuan Presentasi
Memberikan pemahaman lengkap tentang sistem presensi digital berbasis GPS dengan validasi algoritma Haversine untuk meningkatkan akurasi dan efisiensi pencatatan kehadiran siswa.

---

## 🗂️ STRUKTUR PRESENTASI (25-30 Menit)

### 1️⃣ PEMBUKAAN (3 menit)
**Pengenalan Masalah:**
- Sistem presensi manual memakan waktu dan rawan manipulasi
- Sulit tracking kehadiran real-time
- Laporan kehadiran membutuhkan waktu lama
- Komunikasi dengan orangtua tidak efisien

**Solusi:**
- Aplikasi Presensi Digital berbasis GPS
- Validasi lokasi menggunakan Algoritma Haversine
- Notifikasi otomatis ke orangtua via WhatsApp
- Dashboard monitoring real-time

---

## 2️⃣ PENJELASAN CARA KERJA SISTEM PRESENSI (10 menit)

### A. KONSEP DASAR PRESENSI

#### **Dua Jenis Presensi:**

**1. Presensi Sekolah** 
- Presensi kehadiran siswa ke sekolah (pagi)
- Dikelola oleh Admin melalui sistem sesi
- Berlaku untuk seluruh siswa di sekolah

**2. Presensi Kelas**
- Presensi kehadiran di kelas tertentu
- Dikelola oleh Guru mata pelajaran
- Masing-masing kelas bisa membuka sesi sendiri

### B. TEKNOLOGI VALIDASI GPS (ALGORITMA HAVERSINE)

**Cara Kerja:**
```
1. Siswa membuka aplikasi untuk presensi
2. Sistem mendeteksi koordinat GPS siswa (latitude, longitude)
3. Algoritma Haversine menghitung jarak antara:
   - Posisi siswa (GPS)
   - Lokasi sekolah yang sudah ditentukan
4. Sistem memvalidasi:
   - Jika jarak ≤ radius yang ditentukan (misal 100m) → VALID ✅
   - Jika jarak > radius → DITOLAK ❌
5. Data presensi disimpan dengan informasi lengkap
```

**Rumus Haversine:**
```
Formula matematis untuk menghitung jarak terpendek antara dua titik 
di permukaan bumi berdasarkan koordinat latitude dan longitude.

Menghasilkan jarak dalam satuan meter dengan akurasi tinggi.
```

**Keunggulan:**
- ✅ Akurat menghitung jarak bahkan untuk jarak dekat
- ✅ Memperhitungkan kelengkungan bumi
- ✅ Mencegah kecurangan/fake GPS
- ✅ Real-time validation

---

### C. FLOW PRESENSI SISWA (DEMO)

#### **Skenario 1: Presensi Sekolah (Hadir)**

**Langkah-langkah:**
```
1. Admin membuka Sesi Presensi Sekolah
   - Menentukan waktu mulai dan selesai (misal: 06:30 - 07:30)
   - Sistem otomatis mencatat semua siswa sebagai "alpha" jika tidak presensi

2. Siswa login ke aplikasi
   - Dashboard menampilkan status "Sesi Presensi Aktif"

3. Siswa masuk ke halaman Presensi
   - Pilih "Presensi Sekolah"
   - Sistem otomatis mendeteksi lokasi GPS
   
4. Validasi Lokasi
   - Status Lokasi: Menampilkan apakah siswa dalam radius
   - Jarak: Menampilkan jarak siswa dari sekolah
   - Tombol presensi hanya aktif jika lokasi VALID

5. Siswa klik "Presensi Sekolah"
   - Jika VALID: Data tersimpan ✅
   - Notifikasi ke Orangtua via WhatsApp
   - Status berubah menjadi "Hadir"

6. Jika siswa di LUAR radius:
   - Tombol presensi tetap aktif
   - Tetapi sistem akan TOLAK saat submit
   - Pesan error: "Anda berada di luar radius sekolah"
```

#### **Skenario 2: Presensi Izin/Sakit**

**Langkah-langkah:**
```
1. Siswa pilih jenis presensi "Izin" atau "Sakit"
   - Validasi GPS DINONAKTIFKAN
   - Form alasan muncul (wajib diisi)

2. Siswa isi alasan
   - Contoh: "Sakit demam tinggi"
   - Bisa upload bukti (foto surat dokter/surat izin)

3. Submit presensi
   - Data tersimpan tanpa validasi lokasi
   - Notifikasi ke Guru dan Orangtua
   - Status: Izin/Sakit ✅

4. Guru/Admin bisa verifikasi bukti di dashboard
```

#### **Skenario 3: Presensi Kelas**

**Langkah-langkah:**
```
1. Guru membuka Sesi Presensi Kelas
   - Pilih kelas yang akan diajar
   - Tentukan durasi sesi (misal: 90 menit)
   - Sistem mencatat waktu mulai

2. Siswa masuk ke halaman Presensi
   - Pilih "Presensi Kelas"
   - Pilih kelas dari daftar kelas yang diikuti

3. Info Kelas muncul:
   - Nama kelas, Wali kelas, Jadwal
   - Status Sesi: "Sesi Aktif" atau "Tidak Ada Sesi"

4. Validasi lokasi sama seperti presensi sekolah

5. Siswa klik "Presensi Kelas"
   - Data tersimpan untuk kelas spesifik
   - Guru bisa monitoring real-time di dashboard

6. Guru tutup sesi
   - Siswa yang belum presensi otomatis "Alpha"
   - Laporan otomatis tersimpan
```

---

### D. FITUR PENCEGAHAN MANIPULASI

**1. Validasi Duplikasi**
```
- Sistem cek apakah siswa sudah presensi di sesi yang sama
- Jika sudah: Tolak presensi dengan pesan error
- Mencegah presensi berkali-kali
```

**2. Validasi Sesi Aktif**
```
- Presensi hanya bisa dilakukan saat ada sesi aktif
- Sesi kadaluarsa otomatis ditutup
- Mencegah presensi di luar jam yang ditentukan
```

**3. Validasi GPS Real-time**
```
- Koordinat GPS tidak bisa di-hardcode
- Sistem langsung hitung jarak saat submit
- Mencegah fake GPS apps
```

**4. Tracking Lengkap**
```
- Setiap presensi menyimpan:
  • Koordinat GPS (latitude, longitude)
  • Jarak dari sekolah
  • Waktu presensi
  • Jenis presensi
  • Status validasi
```

---

## 3️⃣ DEMO INTERAKTIF (12 menit)

### Demo Alur Lengkap:

#### **A. Login & Dashboard (2 menit)**
```
1. Login sebagai Siswa
2. Dashboard menampilkan:
   - Statistik kehadiran bulan ini
   - Presensi terakhir
   - Status sesi aktif
   - Kelas yang diikuti
```

#### **B. Demo Presensi Sekolah (3 menit)**
```
1. Buka halaman Presensi
2. Tunjukkan deteksi lokasi GPS
3. Tunjukkan perhitungan jarak real-time
4. Submit presensi (sukses)
5. Tunjukkan notifikasi WhatsApp ke orangtua
```

**Tampilkan di layar:**
- Status: "Lokasi Valid ✅"
- Jarak: "45 meter dari sekolah"
- Waktu: Real-time
- Radius: "Maksimal 100 meter"

#### **C. Demo Presensi Kelas (3 menit)**
```
1. Login sebagai Guru
2. Buka sesi presensi kelas
3. Login kembali sebagai Siswa
4. Presensi ke kelas tadi
5. Kembali ke Guru, tunjukkan monitoring real-time
```

**Tunjukkan:**
- List siswa yang sudah/belum presensi
- Update otomatis saat ada siswa presensi
- Waktu presensi masing-masing siswa

#### **D. Demo Izin/Sakit (2 menit)**
```
1. Pilih jenis "Sakit"
2. Isi alasan
3. Upload bukti surat dokter
4. Submit
5. Tunjukkan data di dashboard guru
```

#### **E. Demo Laporan & Statistik (2 menit)**
```
1. Tunjukkan Laporan Kehadiran
   - Filter per hari/minggu/bulan
   - Export ke Excel/PDF
   
2. Tunjukkan Statistik
   - Chart kehadiran
   - Persentase hadir/izin/sakit/alpha
   - Rekap per siswa

3. Tunjukkan Buku Induk Digital
   - Data lengkap siswa
   - Riwayat kehadiran
   - Catatan perilaku
```

---

## 4️⃣ FITUR TAMBAHAN (3 menit)

### Multi-Level User

**1. Admin**
- Kelola lokasi sekolah (GPS & radius)
- Kelola pengguna (CRUD users)
- Kelola kelas
- Buka/tutup sesi presensi sekolah
- Laporan keseluruhan

**2. Admin Kesiswaan**
- Kelola buku induk siswa
- Monitoring presensi sekolah
- Laporan kehadiran

**3. Guru**
- Kelola kelas yang diajar
- Buka/tutup sesi presensi kelas
- Monitoring kehadiran siswa
- Laporan per kelas

**4. Siswa**
- Presensi sekolah & kelas
- Lihat riwayat presensi
- Ajukan izin/sakit
- Lihat buku induk pribadi

### Notifikasi Otomatis
- WhatsApp ke orangtua saat siswa presensi
- Email notification
- Support multiple gateway (Fonnte, Wablas, Twilio)

### Keamanan
- Password terenkripsi
- Session management
- Role-based access control
- SQL injection prevention

---

## 5️⃣ PENUTUP & TANYA JAWAB (2-5 menit)

### Ringkasan Keunggulan:
```
✅ Akurat - Validasi GPS dengan Algoritma Haversine
✅ Real-time - Monitoring langsung
✅ Efisien - Otomatis, hemat waktu
✅ Transparan - Notifikasi ke orangtua
✅ Komprehensif - Laporan lengkap
✅ User-friendly - Interface intuitif
✅ Secure - Sistem keamanan berlapis
```

### Call to Action:
- Siap untuk implementasi di sekolah
- Support training & onboarding
- Maintenance & update berkala
- Customisasi sesuai kebutuhan

---

## 📊 TIPS PRESENTASI

### Persiapan:
1. ✅ Pastikan GPS aktif di device demo
2. ✅ Siapkan akun demo untuk tiap role
3. ✅ Test koneksi internet
4. ✅ Siapkan backup data untuk demo
5. ✅ Screenshot fitur-fitur utama

### Saat Demo:
1. 🎯 Fokus pada problem-solution
2. 🎯 Tunjukkan perhitungan jarak real-time
3. 🎯 Demonstrasikan validasi GPS
4. 🎯 Tunjukkan notifikasi WhatsApp langsung
5. 🎯 Bandingkan sebelum vs sesudah pakai sistem

### Antisipasi Pertanyaan:
```
Q: Bagaimana jika siswa lupa HP/lowbat?
A: Bisa presensi manual oleh guru/admin sebagai fallback

Q: Bagaimana jika GPS tidak akurat?
A: Set radius yang reasonable (50-100m) untuk toleransi

Q: Bagaimana jika ada siswa yang fake GPS?
A: Sistem validasi server-side, plus tracking history lokasi

Q: Biaya operasional?
A: Hemat! Hanya butuh koneksi internet, no special hardware

Q: Bagaimana training untuk guru/admin?
A: User-friendly, training 1-2 jam sudah cukup
```

---

## 🎬 SCRIPT PEMBUKAAN DEMO

**"Selamat pagi/siang Bapak/Ibu,**

**Hari ini saya akan mendemonstrasikan Aplikasi Presensi Digital SMK yang dirancang khusus untuk mengatasi permasalahan presensi manual yang memakan waktu dan rawan manipulasi.**

**Aplikasi ini menggunakan teknologi GPS dengan validasi Algoritma Haversine - sebuah formula matematis yang menghitung jarak dengan akurat berdasarkan koordinat bumi.**

**Mari saya tunjukkan bagaimana cara kerjanya...**

**[Mulai Demo]**

**Saat siswa membuka aplikasi untuk presensi, sistem otomatis mendeteksi lokasi mereka. Di sini Anda bisa lihat...**
- **Status Lokasi menunjukkan apakah siswa berada dalam radius sekolah**
- **Jarak real-time dari sekolah ditampilkan dalam meter**
- **Tombol presensi hanya aktif ketika lokasi valid**

**Mari kita coba submit presensi... [Klik]**

**Berhasil! Dan lihat, orangtua langsung menerima notifikasi WhatsApp bahwa anaknya sudah sampai di sekolah.**

**Ini meningkatkan transparansi dan memudahkan monitoring orangtua...**"

---

## 📄 DOKUMEN PENDUKUNG

Siapkan:
- [ ] Slide presentasi (PowerPoint/PDF)
- [ ] Video demo (backup jika demo live gagal)
- [ ] Brosur fitur
- [ ] Dokumentasi teknis
- [ ] Perhitungan ROI (Return on Investment)
- [ ] Testimoni user (jika ada)
- [ ] Proposal implementasi

---

## ✅ CHECKLIST SEBELUM PRESENTASI

**Teknis:**
- [ ] Device demo tercharge penuh
- [ ] GPS aktif dan akurat
- [ ] Internet stabil
- [ ] Database terisi data demo realistis
- [ ] Phone untuk demo notifikasi WA

**Akun Demo:**
- [ ] Admin: admin@demo / password
- [ ] Guru: guru@demo / password  
- [ ] Siswa: siswa@demo / password

**Material:**
- [ ] Laptop & charger
- [ ] Proyektor/TV
- [ ] Pointer/remote
- [ ] Backup file
- [ ] Contact person & proposal

---

## 🎯 SASARAN PRESENTASI

**Setelah presentasi, audience harus:**
1. ✅ Memahami cara kerja validasi GPS (Haversine)
2. ✅ Mengerti alur presensi siswa & guru
3. ✅ Melihat value proposition yang jelas
4. ✅ Tertarik untuk implementasi
5. ✅ Percaya sistem secure & reliable

---

**Semoga sukses presentasinya! 🚀**

---

*Catatan: Sesuaikan durasi dan konten dengan audience. Untuk audience teknis, tambahkan detail algoritma. Untuk audience non-teknis, fokus pada manfaat dan kemudahan penggunaan.*
