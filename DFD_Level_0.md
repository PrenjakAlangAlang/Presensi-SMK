# Data Flow Diagram (DFD) Level 0
## Sistem Informasi Presensi SMK

### Context Diagram (Diagram Konteks)

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                                                                                                    │
│                                                                                                                    │
│    ┌────────────────────┐                                                                                         │
│    │   ADMINISTRATOR    │                                                                                         │
│    └──────────┬─────────┘                                                                                         │
│               │                                                                                                    │
│  • data login                     ┌──────────────────────────────────────────┐                                   │
│  • data user (CRUD)               │                                          │  • info dashboard admin           │
│  • data kelas (CRUD)              │                                          │  • data users (list)              │
│  • data lokasi GPS                │      SISTEM INFORMASI PRESENSI SMK      │  • data kelas (list)              │
│  • data sesi sekolah (CRUD)       │           (Version 1.0)                  │  • data lokasi GPS                │
│  • perpanjang sesi sekolah        │                                          │  • statistik kehadiran global     │
│  • tutup sesi sekolah manual      │                                          │  • laporan presensi keseluruhan   │
│               │                   │                                          │                                   │
│               └──────────────────→│                                          │←─────────────┐                    │
│                                   │                                          │              │                    │
│    ┌────────────────────┐         │                                          │         ┌────┴─────────────────┐  │
│    │       GURU         │         │                                          │         │  ADMIN KESISWAAN     │  │
│    │     (Teacher)      │         │                                          │         │ (Student Affairs)    │  │
│    └──────────┬─────────┘         │                                          │         └────┬─────────────────┘  │
│               │                   │                                          │              │                    │
│  • data login                     │                                          │  • data login                     │
│  • buka sesi kelas                │                                          │  • upload buku induk siswa        │
│  • tutup sesi kelas               │                                          │  • edit buku induk                │
│  • perpanjang sesi kelas          │                                          │  • request laporan sekolah        │
│  • request laporan kelas          │                                          │              │                    │
│  • filter tanggal laporan         │                                          │              │                    │
│               │                   │                                          │              │                    │
│               └──────────────────→│                                          │←─────────────┘                    │
│                                   │                                          │                                   │
│                                   │                                          │  • info dashboard kesiswaan       │
│  • info dashboard guru            │                                          │  • data buku induk siswa          │
│  • data kelas (by guru)           │                                          │  • laporan presensi sekolah       │
│  • daftar siswa per kelas         │                                          │  • statistik kehadiran siswa      │
│  • info sesi kelas aktif          │                                          │                                   │
│  • laporan presensi kelas         │                                          │                                   │
│  • statistik kehadiran kelas      │                                          │                                   │
│  • aktivitas presensi terbaru     │                                          │                                   │
│               ↑                   │                                          │                                   │
│               │                   │                                          │                                   │
│               └───────────────────│                                          │───────────────┐                   │
│                                   │                                          │               │                   │
│                                   │                                          │               ↓                   │
│    ┌────────────────────┐         │                                          │         ┌─────────────────┐       │
│    │   EMAIL SYSTEM     │         │                                          │         │     SISWA       │       │
│    │  (SwiftMailer)     │         │                                          │         │   (Student)     │       │
│    └──────────┬─────────┘         │                                          │         └─────┬───────────┘       │
│               │                   │                                          │               │                   │
│  • kirim notifikasi izin          │                                          │  • data login                     │
│  • status pengiriman email        │                                          │  • data presensi sekolah (masuk)  │
│               │                   │                                          │  • koordinat GPS siswa            │
│               │                   │                                          │  • data presensi kelas            │
│               └──────────────────→│                                          │←• foto bukti izin/sakit           │
│                                   │                                          │  • jenis izin (sakit/izin)        │
│               ┌───────────────────│                                          │  • alasan izin                    │
│               │                   │                                          │               │                   │
│               ↓                   │                                          │               │                   │
│  • request kirim email izin       │                                          │               │                   │
│                                   └──────────────────────────────────────────┘               │                   │
│                                                                                               │                   │
│                                                    • info dashboard siswa                     │                   │
│                                                    • statistik kehadiran pribadi              │                   │
│                                                    • info sesi sekolah aktif                  │                   │
│                                                    • info sesi kelas aktif                    │                   │
│                                                    • data kelas siswa                         │                   │
│                                                    • riwayat presensi sekolah                 │                   │
│                                                    • riwayat presensi kelas                   │                   │
│                                                    • data buku induk pribadi                  │                   │
│                                                    • status validasi GPS                      │                   │
│                                                    • riwayat izin                             │                   │
│                                                                      ↑                        │                   │
│                                                                      └────────────────────────┘                   │
│                                                                                                                    │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## Penjelasan Entitas Eksternal

### 1. **ADMINISTRATOR**
**Deskripsi:** Pengelola sistem dengan akses penuh terhadap semua modul sistem presensi
   
**Data Flow Input ke Sistem:**
| No | Data Input | Deskripsi Detail |
|----|-----------|------------------|
| 1  | Data Login | Username dan password untuk autentikasi administrator |
| 2  | Data User (Create) | Nama lengkap, username, password, role (admin/guru/admin_kesiswaan/siswa), email |
| 3  | Data User (Update) | ID user, field yang diubah (nama/username/role/email) |
| 4  | Data User (Delete) | ID user yang akan dihapus |
| 5  | Data Kelas (Create) | Nama kelas, tahun ajaran, wali kelas (guru_id), daftar siswa |
| 6  | Data Kelas (Update) | ID kelas, field yang diubah |
| 7  | Data Kelas (Delete) | ID kelas yang akan dihapus |
| 8  | Data Lokasi GPS | Latitude dan longitude titik pusat sekolah, radius maksimal (meter) |
| 9  | Data Sesi Sekolah (Create) | Waktu buka sesi, waktu tutup sesi, catatan/keterangan |
| 10 | Perpanjang Sesi Sekolah | ID sesi, waktu tutup yang baru (extended time) |
| 11 | Tutup Sesi Sekolah Manual | ID sesi yang akan ditutup sebelum waktu berakhir |
| 12 | Parameter Laporan | Rentang tanggal, filter kelas, filter siswa, jenis laporan |

**Data Flow Output dari Sistem:**
| No | Data Output | Deskripsi Detail |
|----|-----------|------------------|
| 1  | Info Dashboard Admin | Total users (per role), total kelas, total presensi hari ini |
| 2  | Data Users (List) | Daftar semua user dengan detail lengkap (ID, nama, username, role, email, status) |
| 3  | Data Kelas (List) | Daftar kelas dengan wali kelas, jumlah siswa, tahun ajaran |
| 4  | Data Lokasi GPS | Koordinat sekolah tersimpan, radius validasi |
| 5  | Statistik Kehadiran Global | Persentase hadir/izin/sakit/alpha seluruh siswa |
| 6  | Laporan Presensi Keseluruhan | Rekap kehadiran per periode, per kelas, per siswa |
| 7  | Data Sesi Sekolah | Daftar sesi (auto/manual), status (aktif/ditutup), waktu buka/tutup |
| 8  | Log Aktivitas Sistem | Histori perubahan data oleh admin |

---

### 2. **GURU (Teacher)**
**Deskripsi:** Pengajar yang mengelola presensi kelas yang diampu dan melihat laporan kelas

**Data Flow Input ke Sistem:**
| No | Data Input | Deskripsi Detail |
|----|-----------|------------------|
| 1  | Data Login | Username dan password guru |
| 2  | Buka Sesi Kelas | ID kelas, waktu buka sesi, waktu tutup sesi (durasi) |
| 3  | Tutup Sesi Kelas | ID sesi kelas yang akan ditutup |
| 4  | Perpanjang Sesi Kelas | ID sesi, waktu perpanjangan (menit tambahan) |
| 5  | Request Laporan Kelas | ID kelas, tanggal mulai, tanggal akhir, jenis laporan |
| 6  | Filter Tanggal Laporan | Periode laporan (harian, mingguan, bulanan, custom range) |
| 7  | Request Daftar Siswa | ID kelas untuk melihat daftar siswa dan status kehadiran |

**Data Flow Output dari Sistem:**
| No | Data Output | Deskripsi Detail |
|----|-----------|------------------|
| 1  | Info Dashboard Guru | Total kelas yang diajar, total siswa di semua kelas |
| 2  | Data Kelas (By Guru) | Daftar kelas yang diampu dengan statistik masing-masing |
| 3  | Daftar Siswa per Kelas | Nama siswa, NIS, status kehadiran terakhir |
| 4  | Info Sesi Kelas Aktif | Sesi yang sedang berjalan, waktu tersisa, jumlah siswa presensi |
| 5  | Laporan Presensi Kelas | Rekap hadir/izin/sakit/alpha per sesi atau per periode |
| 6  | Statistik Kehadiran Kelas | Persentase kehadiran, grafik tren kehadiran |
| 7  | Aktivitas Presensi Terbaru | 10 aktivitas presensi terakhir di kelas yang diajar |
| 8  | Notifikasi Izin Siswa | Daftar pengajuan izin/sakit yang perlu diketahui |

---

### 3. **ADMIN KESISWAAN (Student Affairs Admin)**
**Deskripsi:** Pengelola data kesiswaan, buku induk, dan laporan presensi sekolah

**Data Flow Input ke Sistem:**
| No | Data Input | Deskripsi Detail |
|----|-----------|------------------|
| 1  | Data Login | Username dan password admin kesiswaan |
| 2  | Upload Buku Induk Siswa | ID siswa, file dokumen (PDF/JPG), kategori dokumen (ijazah/akta/KK) |
| 3  | Edit Buku Induk | ID dokumen, file pengganti atau update metadata |
| 4  | Request Laporan Sekolah | Rentang tanggal, filter kelas, jenis laporan presensi |
| 5  | Filter Statistik | Parameter untuk melihat statistik kehadiran tertentu |

**Data Flow Output dari Sistem:**
| No | Data Output | Deskripsi Detail |
|----|-----------|------------------|
| 1  | Info Dashboard Kesiswaan | Total siswa, total buku induk, statistik kehadiran global |
| 2  | Data Buku Induk Siswa | Daftar dokumen per siswa, status kelengkapan dokumen |
| 3  | Laporan Presensi Sekolah | Rekap kehadiran seluruh siswa (presensi sekolah, bukan per kelas) |
| 4  | Statistik Kehadiran Siswa | Persentase hadir/izin/sakit/alpha per periode |
| 5  | Data Siswa Lengkap | Informasi biodata, riwayat kehadiran, dokumen buku induk |
| 6  | Export Laporan | File export (Excel/PDF) laporan kehadiran |

---

### 4. **SISWA (Student)**
**Deskripsi:** Pengguna yang melakukan presensi sekolah, presensi kelas, dan mengajukan izin

**Data Flow Input ke Sistem:**
| No | Data Input | Deskripsi Detail |
|----|-----------|------------------|
| 1  | Data Login | Username (NIS) dan password siswa |
| 2  | Data Presensi Sekolah (Hadir) | ID siswa, ID sesi sekolah, latitude GPS, longitude GPS, waktu presensi |
| 3  | Data Presensi Kelas | ID siswa, ID sesi kelas, waktu presensi |
| 4  | Koordinat GPS Siswa | Latitude dan longitude real-time saat presensi |
| 5  | Foto Bukti Izin/Sakit | File gambar (JPG/PNG), upload saat mengajukan izin |
| 6  | Jenis Izin | Kategori: "izin" atau "sakit" |
| 7  | Alasan Izin | Deskripsi text alasan tidak hadir |
| 8  | Request Data Buku Induk | ID siswa untuk melihat dokumen pribadi |

**Data Flow Output dari Sistem:**
| No | Data Output | Deskripsi Detail |
|----|-----------|------------------|
| 1  | Info Dashboard Siswa | Statistik kehadiran pribadi, sesi aktif, aktivitas terbaru |
| 2  | Statistik Kehadiran Pribadi | Total hadir/izin/sakit/alpha bulan ini, persentase kehadiran |
| 3  | Info Sesi Sekolah Aktif | Status sesi (buka/tutup), waktu buka, waktu tutup, waktu tersisa |
| 4  | Info Sesi Kelas Aktif | Daftar sesi kelas yang sedang buka, mata pelajaran, waktu tersisa |
| 5  | Data Kelas Siswa | Daftar kelas yang diikuti siswa dengan jadwal |
| 6  | Riwayat Presensi Sekolah | Histori presensi masuk sekolah dengan tanggal, waktu, status |
| 7  | Riwayat Presensi Kelas | Histori presensi per kelas, per sesi dengan status |
| 8  | Data Buku Induk Pribadi | Dokumen-dokumen milik siswa yang di-upload admin kesiswaan |
| 9  | Status Validasi GPS | Hasil validasi: "Dalam radius sekolah" atau "Diluar radius" |
| 10 | Riwayat Izin | Daftar pengajuan izin/sakit dengan status dan tanggal |
| 11 | Notifikasi Presensi | Pesan sukses/gagal presensi, reminder sesi akan ditutup |

---

### 5. **EMAIL SYSTEM (SwiftMailer)**
**Deskripsi:** Sistem eksternal untuk mengirim notifikasi email otomatis

**Data Flow Input ke Sistem:**
| No | Data Input | Deskripsi Detail |
|----|-----------|------------------|
| 1  | Status Pengiriman Email | Response dari SMTP server (sukses/gagal) |
| 2  | Error Message | Pesan error jika pengiriman gagal |

**Data Flow Output dari Sistem:**
| No | Data Output | Deskripsi Detail |
|----|-----------|------------------|
| 1  | Request Kirim Email Izin | Data izin siswa (nama, kelas, alasan, foto), dikirim ke wali kelas |
| 2  | Email Reset Password | Link reset password dan token untuk user yang lupa password |
| 3  | Email Pemberitahuan Sistem | Notifikasi penting dari sistem (maintenance, update) |
| 4  | Email Laporan Periodik | Rekap kehadiran mingguan/bulanan ke admin/guru |

---

## Detail Proses Utama dalam Sistem

### **Proses 1: Autentikasi & Autorisasi**
**Input:**
- Username dan password dari user (semua role)
- Session token untuk validasi akses halaman

**Proses:**
1. Validasi kredensial dengan database users
2. Generate session ID dan simpan ke PHP session
3. Cek role user untuk menentukan akses menu
4. Redirect ke dashboard sesuai role

**Output:**
- Session data (user_id, nama, role)
- Token autentikasi
- Redirect URL ke dashboard

**Data Store:**
- Table `users` (untuk validasi login)
- PHP Session storage

---

### **Proses 2: Manajemen Data Master**

#### **2.1 Manajemen User**
**Input:**
- Data user baru (nama, username, password, role, email)
- ID user untuk edit/delete
- Parameter filter dan search

**Proses:**
1. Validasi input (unique username, format email)
2. Hash password menggunakan password_hash()
3. Insert/Update/Delete ke database
4. Log aktivitas admin

**Output:**
- Success/error message
- Data user yang ter-update
- Konfirmasi operasi CRUD

**Data Store:**
- Table `users`

#### **2.2 Manajemen Kelas**
**Input:**
- Data kelas (nama_kelas, tahun_ajaran, wali_kelas_id)
- Array siswa_id untuk assignment siswa ke kelas
- ID kelas untuk edit/delete

**Proses:**
1. Validasi wali kelas (harus role guru)
2. Insert/Update data kelas
3. Manage relasi many-to-many siswa-kelas (table kelas_siswa)
4. Update counter total_siswa per kelas

**Output:**
- Data kelas lengkap dengan daftar siswa
- Statistik kelas (total siswa, wali kelas)

**Data Store:**
- Table `kelas`
- Table `kelas_siswa` (junction table)

#### **2.3 Manajemen Lokasi GPS**
**Input:**
- Latitude dan longitude titik pusat sekolah
- Radius validasi dalam meter (default 100m)

**Proses:**
1. Simpan koordinat GPS ke database
2. Validasi format koordinat
3. Hitung batas area validasi

**Output:**
- Koordinat sekolah tersimpan
- Radius validasi tersimpan

**Data Store:**
- Table `lokasi_sekolah`

---

### **Proses 3: Manajemen Presensi**

#### **3.1 Presensi Sekolah (Check-in Harian)**
**Input:**
- ID siswa, ID sesi sekolah aktif
- Latitude dan longitude siswa (real-time GPS)
- Jenis presensi (hadir/izin/sakit)
- Alasan (jika izin/sakit)
- Foto bukti (jika izin/sakit)

**Proses:**
1. Cek sesi sekolah aktif (waktu_buka < now < waktu_tutup)
2. Validasi GPS siswa dengan lokasi sekolah (haversine formula)
3. Cek duplikasi (siswa hanya bisa presensi 1x per sesi)
4. Jika izin/sakit: skip validasi GPS, simpan foto ke uploads/izin/
5. Insert data ke table presensi_sekolah
6. Jika izin/sakit: kirim notifikasi email ke wali kelas

**Output:**
- Status presensi: sukses/gagal
- Validasi GPS: dalam radius / di luar radius
- Waktu presensi tercatat
- Konfirmasi via JSON response

**Data Store:**
- Table `presensi_sekolah`
- Table `presensi_sekolah_sesi`
- Folder `uploads/izin/`

#### **3.2 Presensi Kelas (Check-in per Mata Pelajaran)**
**Input:**
- ID siswa, ID sesi kelas aktif
- Waktu presensi

**Proses:**
1. Cek sesi kelas aktif untuk kelas siswa
2. Validasi siswa terdaftar di kelas tersebut
3. Cek duplikasi presensi untuk sesi yang sama
4. Insert data ke table presensi_kelas
5. Update counter jumlah_presensi di sesi

**Output:**
- Status presensi kelas: sukses/gagal
- Waktu presensi tercatat
- Jumlah siswa yang sudah presensi dalam sesi

**Data Store:**
- Table `presensi_kelas`
- Table `presensi_sesi`

#### **3.3 Pengajuan Izin/Sakit**
**Input:**
- ID siswa, jenis izin (izin/sakit)
- Alasan text
- Foto bukti (file upload)
- Tanggal izin

**Proses:**
1. Validasi file upload (format, ukuran max 2MB)
2. Generate unique filename dengan timestamp
3. Simpan file ke folder uploads/izin/
4. Insert data ke presensi_sekolah dengan status izin/sakit
5. Kirim email notifikasi ke wali kelas via EmailService

**Output:**
- Konfirmasi pengajuan izin
- URL foto bukti tersimpan
- Email terkirim ke wali kelas

**Data Store:**
- Table `presensi_sekolah`
- Folder `uploads/izin/`
- Queue email (via SwiftMailer)

---

### **Proses 4: Manajemen Sesi Presensi**

#### **4.1 Sesi Presensi Sekolah**

**4.1.1 Buka Sesi Otomatis**
**Input:**
- Waktu buka dan tutup dari konfigurasi
- Trigger: Cron job atau scheduler

**Proses:**
1. Cek waktu sistem vs jadwal buka sesi
2. Auto-create sesi dengan flag is_manual=0
3. Set status='aktif', created_by=NULL (system)

**Output:**
- Sesi baru dengan status aktif
- Notifikasi sistem sesi dibuka

**Data Store:**
- Table `presensi_sekolah_sesi`

**4.1.2 Buka Sesi Manual (oleh Admin)**
**Input:**
- Waktu buka, waktu tutup (dari admin)
- Catatan/keterangan
- ID admin pembuat

**Proses:**
1. Validasi tidak ada sesi aktif lain
2. Insert sesi baru dengan is_manual=1
3. Set created_by = admin user_id

**Output:**
- Sesi manual aktif
- Log aktivitas admin

**Data Store:**
- Table `presensi_sekolah_sesi`

**4.1.3 Tutup Sesi (Otomatis/Manual)**
**Input:**
- ID sesi
- Trigger: waktu habis atau admin close

**Proses:**
1. Tandai semua siswa yang belum presensi sebagai alpha
2. Update status sesi menjadi 'ditutup'
3. Hitung statistik sesi (total hadir, izin, sakit, alpha)

**Output:**
- Sesi ditutup
- Jumlah siswa alpha tercatat
- Statistik sesi final

**Data Store:**
- Table `presensi_sekolah_sesi`
- Table `presensi_sekolah` (update status alpha)

**4.1.4 Perpanjang Sesi**
**Input:**
- ID sesi, waktu tutup baru

**Proses:**
1. Validasi sesi masih aktif
2. Update field waktu_tutup
3. Log perpanjangan

**Output:**
- Waktu sesi diperpanjang
- Notifikasi siswa sesi extended

**Data Store:**
- Table `presensi_sekolah_sesi`

#### **4.2 Sesi Presensi Kelas**

**4.2.1 Buka Sesi Kelas (oleh Guru)**
**Input:**
- ID kelas, ID guru
- Waktu buka, durasi (menit)
- Keterangan (nama mata pelajaran/topik)

**Proses:**
1. Validasi guru mengajar kelas tersebut
2. Hitung waktu_tutup = waktu_buka + durasi
3. Insert sesi baru dengan status='aktif'

**Output:**
- Sesi kelas aktif
- Notifikasi ke siswa kelas tersebut

**Data Store:**
- Table `presensi_sesi`

**4.2.2 Tutup Sesi Kelas**
**Input:**
- ID sesi kelas
- ID guru (validasi)

**Proses:**
1. Tandai siswa yang belum presensi sebagai alpha
2. Update status='ditutup'
3. Hitung statistik sesi kelas

**Output:**
- Sesi kelas ditutup
- Laporan singkat kehadiran sesi

**Data Store:**
- Table `presensi_sesi`
- Table `presensi_kelas`

**4.2.3 Perpanjang Sesi Kelas**
**Input:**
- ID sesi, menit tambahan

**Proses:**
1. Validasi guru berwenang
2. Update waktu_tutup
3. Log perpanjangan

**Output:**
- Sesi diperpanjang
- Waktu tutup baru

**Data Store:**
- Table `presensi_sesi`

---

### **Proses 5: Manajemen Buku Induk**

**Input:**
- ID siswa
- File dokumen (PDF/JPG/PNG)
- Kategori dokumen (ijazah, akta_kelahiran, kartu_keluarga, dll)

**Proses:**
1. Validasi file (format, ukuran max 5MB)
2. Generate unique filename: {nis}_{kategori}_{timestamp}
3. Simpan file ke folder uploads/buku_induk/
4. Insert metadata ke table buku_induk
5. Link dokumen ke siswa (user_id)

**Output:**
- File tersimpan di server
- Metadata dokumen di database
- Path dokumen untuk akses

**Data Store:**
- Table `buku_induk`
- Folder `uploads/buku_induk/`

---

### **Proses 6: Pelaporan**

#### **6.1 Laporan Presensi Harian**
**Input:**
- Tanggal laporan
- Filter kelas (optional)
- Filter siswa (optional)

**Proses:**
1. Query presensi_sekolah/presensi_kelas untuk tanggal tersebut
2. Join dengan table users dan kelas
3. Hitung total hadir, izin, sakit, alpha
4. Format data dalam tabel

**Output:**
- Tabel laporan harian
- Statistik ringkasan
- Export option (Excel/PDF)

**Data Store:**
- Table `presensi_sekolah`
- Table `presensi_kelas`
- Table `users`
- Table `kelas`

#### **6.2 Laporan Presensi Bulanan**
**Input:**
- Bulan dan tahun
- Filter kelas (optional)

**Proses:**
1. Aggregate data presensi per siswa per bulan
2. Hitung persentase kehadiran per siswa
3. Identifikasi siswa dengan kehadiran rendah (<75%)
4. Generate grafik tren kehadiran

**Output:**
- Rekap bulanan per siswa
- Grafik statistik
- Highlight siswa perlu perhatian
- Export laporan

**Data Store:**
- Table `presensi_sekolah`
- Table `presensi_kelas`

#### **6.3 Laporan Statistik Dashboard**
**Input:**
- Role user (menentukan scope data)
- Filter periode (default: bulan ini)

**Proses:**
1. Admin: statistik global semua siswa
2. Guru: statistik kelas yang diajar
3. Admin Kesiswaan: statistik sekolah
4. Siswa: statistik pribadi
5. Hitung persentase per kategori kehadiran
6. Generate card widgets untuk dashboard

**Output:**
- Card statistik (total, persentase)
- Grafik chart (pie, line)
- Tren kehadiran

**Data Store:**
- Table `presensi_sekolah`
- Table `presensi_kelas`
- Table `users`
- Table `kelas`

---

### **Proses 7: Notifikasi**

#### **7.1 Notifikasi Email Izin**
**Input:**
- Data izin siswa (dari pengajuan izin)
- Email wali kelas

**Proses:**
1. Load EmailService (SwiftMailer)
2. Compose email dengan template
3. Attach foto bukti izin
4. Kirim via SMTP
5. Log status pengiriman

**Output:**
- Email terkirim ke wali kelas
- Log pengiriman (sukses/gagal)

**Data Store:**
- Table `email_log` (optional, untuk tracking)

#### **7.2 Notifikasi In-App (Dashboard)**
**Input:**
- Event trigger (sesi buka, sesi tutup, izin diajukan)
- Target user (role/individual)

**Proses:**
1. Detect event dari sistem
2. Generate notifikasi message
3. Display di dashboard/alert

**Output:**
- Alert/badge notifikasi
- Message text

**Data Store:**
- Session storage (temporary)

---

## Data Store (Database Tables)

### **1. Table: users**
**Deskripsi:** Menyimpan data semua user sistem

| Field | Type | Deskripsi |
|-------|------|-----------|
| id | INT PRIMARY KEY | ID unik user |
| nama | VARCHAR(255) | Nama lengkap |
| username | VARCHAR(100) UNIQUE | Username login (NIS untuk siswa) |
| password | VARCHAR(255) | Password ter-hash |
| role | ENUM | admin, guru, admin_kesiswaan, siswa |
| email | VARCHAR(255) | Email user |
| created_at | TIMESTAMP | Waktu registrasi |

---

### **2. Table: kelas**
**Deskripsi:** Menyimpan data kelas

| Field | Type | Deskripsi |
|-------|------|-----------|
| id | INT PRIMARY KEY | ID kelas |
| nama_kelas | VARCHAR(100) | Nama kelas (contoh: XII RPL 1) |
| tahun_ajaran | VARCHAR(20) | Tahun ajaran (2024/2025) |
| wali_kelas_id | INT FK | ID guru sebagai wali kelas |
| created_at | TIMESTAMP | Waktu dibuat |

---

### **3. Table: kelas_siswa**
**Deskripsi:** Junction table many-to-many kelas dan siswa

| Field | Type | Deskripsi |
|-------|------|-----------|
| id | INT PRIMARY KEY | ID relasi |
| kelas_id | INT FK | ID kelas |
| siswa_id | INT FK | ID siswa (user) |
| created_at | TIMESTAMP | Waktu assignment |

---

### **4. Table: lokasi_sekolah**
**Deskripsi:** Menyimpan koordinat GPS sekolah

| Field | Type | Deskripsi |
|-------|------|-----------|
| id | INT PRIMARY KEY | ID lokasi |
| latitude | DECIMAL(10,8) | Latitude titik pusat sekolah |
| longitude | DECIMAL(11,8) | Longitude titik pusat sekolah |
| radius | INT | Radius validasi dalam meter |
| updated_at | TIMESTAMP | Terakhir diubah |

---

### **5. Table: presensi_sekolah_sesi**
**Deskripsi:** Menyimpan sesi presensi sekolah harian

| Field | Type | Deskripsi |
|-------|------|-----------|
| id | INT PRIMARY KEY | ID sesi |
| waktu_buka | DATETIME | Waktu sesi dibuka |
| waktu_tutup | DATETIME | Waktu sesi ditutup |
| status | ENUM | aktif, ditutup |
| is_manual | BOOLEAN | 0=otomatis, 1=manual oleh admin |
| created_by | INT FK | ID admin pembuat (NULL jika auto) |
| note | TEXT | Catatan/keterangan |
| created_at | TIMESTAMP | Waktu dibuat |

---

### **6. Table: presensi_sekolah**
**Deskripsi:** Menyimpan data presensi harian siswa ke sekolah

| Field | Type | Deskripsi |
|-------|------|-----------|
| id | INT PRIMARY KEY | ID presensi |
| siswa_id | INT FK | ID siswa |
| sesi_id | INT FK | ID sesi presensi sekolah |
| tanggal | DATE | Tanggal presensi |
| waktu | TIME | Waktu presensi |
| status | ENUM | hadir, izin, sakit, alpha |
| latitude | DECIMAL(10,8) | GPS siswa saat presensi |
| longitude | DECIMAL(11,8) | GPS siswa saat presensi |
| alasan | TEXT | Alasan jika izin/sakit |
| foto_bukti | VARCHAR(255) | Path file foto bukti |
| created_at | TIMESTAMP | Waktu tercatat |

---

### **7. Table: presensi_sesi**
**Deskripsi:** Menyimpan sesi presensi per kelas (dibuka guru)

| Field | Type | Deskripsi |
|-------|------|-----------|
| id | INT PRIMARY KEY | ID sesi kelas |
| kelas_id | INT FK | ID kelas |
| guru_id | INT FK | ID guru pembuka sesi |
| waktu_buka | DATETIME | Waktu sesi dibuka |
| waktu_tutup | DATETIME | Waktu sesi berakhir |
| status | ENUM | aktif, ditutup |
| keterangan | VARCHAR(255) | Nama mapel/topik |
| created_at | TIMESTAMP | Waktu dibuat |

---

### **8. Table: presensi_kelas**
**Deskripsi:** Menyimpan data presensi siswa per kelas

| Field | Type | Deskripsi |
|-------|------|-----------|
| id | INT PRIMARY KEY | ID presensi kelas |
| siswa_id | INT FK | ID siswa |
| sesi_id | INT FK | ID sesi kelas |
| kelas_id | INT FK | ID kelas |
| tanggal | DATE | Tanggal presensi |
| waktu | TIME | Waktu presensi |
| status | ENUM | hadir, alpha |
| created_at | TIMESTAMP | Waktu tercatat |

---

### **9. Table: buku_induk**
**Deskripsi:** Menyimpan metadata dokumen buku induk siswa

| Field | Type | Deskripsi |
|-------|------|-----------|
| id | INT PRIMARY KEY | ID dokumen |
| siswa_id | INT FK | ID siswa pemilik dokumen |
| kategori | VARCHAR(100) | Jenis dokumen (ijazah, akta, KK) |
| nama_file | VARCHAR(255) | Nama file asli |
| path_file | VARCHAR(255) | Path file di server |
| uploaded_by | INT FK | ID admin kesiswaan uploader |
| uploaded_at | TIMESTAMP | Waktu upload |

---

## Alur Data (Data Flow) Detail

### **Alur 1: Login Siswa → Presensi Sekolah**

```
SISWA
  ↓ [username, password]
LOGIN PROCESS (AuthController)
  ↓ validasi ke [users table]
  ↓ generate session
DASHBOARD SISWA
  ↓ klik "Presensi Sekolah"
CEK SESI AKTIF
  ↓ query [presensi_sekolah_sesi table]
  ↓ return sesi aktif atau null
HALAMAN PRESENSI
  ↓ [latitude, longitude] dari browser GPS
SUBMIT PRESENSI
  ↓ [siswa_id, sesi_id, lat, lng, waktu]
VALIDASI GPS (LocationModel)
  ↓ query [lokasi_sekolah table]
  ↓ haversine formula calculate distance
  ↓ jika dalam radius: lanjut
  ↓ jika di luar radius: reject
INSERT DATA
  ↓ [presensi_sekolah table]
RESPONSE
  ↓ JSON {success: true, message: "Presensi berhasil"}
SISWA (feedback)
```

---

### **Alur 2: Guru Buka Sesi Kelas → Siswa Presensi Kelas**

```
GURU
  ↓ akses halaman "Kelas Saya"
PILIH KELAS
  ↓ klik "Buka Sesi Presensi"
  ↓ [kelas_id, waktu_buka, durasi, keterangan]
VALIDASI GURU
  ↓ cek guru mengajar kelas ini
  ↓ cek tidak ada sesi aktif lain
INSERT SESI (PresensiSesiModel)
  ↓ [presensi_sesi table]
  ↓ status='aktif'
NOTIFIKASI SISWA
  ↓ siswa refresh dashboard melihat sesi aktif
SISWA
  ↓ klik "Presensi Kelas"
  ↓ pilih kelas dengan sesi aktif
SUBMIT PRESENSI KELAS
  ↓ [siswa_id, sesi_id, kelas_id, waktu]
VALIDASI
  ↓ cek siswa terdaftar di kelas
  ↓ cek sesi masih aktif
  ↓ cek tidak duplikat
INSERT DATA
  ↓ [presensi_kelas table]
RESPONSE
  ↓ JSON {success: true}
GURU
  ↓ refresh halaman kelas
  ↓ lihat jumlah siswa sudah presensi
  ↓ klik "Tutup Sesi"
TUTUP SESI (mark alpha)
  ↓ update [presensi_kelas] siswa belum presensi = alpha
  ↓ update [presensi_sesi] status='ditutup'
```

---

### **Alur 3: Siswa Ajukan Izin → Email Notifikasi**

```
SISWA
  ↓ akses halaman "Izin"
  ↓ pilih jenis: izin/sakit
  ↓ isi alasan text
  ↓ upload foto bukti
SUBMIT FORM IZIN
  ↓ [siswa_id, jenis, alasan, file]
UPLOAD FILE
  ↓ validasi format dan size
  ↓ simpan ke [uploads/izin/]
  ↓ generate filename unique
INSERT PRESENSI
  ↓ [presensi_sekolah table]
  ↓ status='izin' atau 'sakit'
  ↓ foto_bukti=path file
KIRIM EMAIL (EmailService)
  ↓ load SwiftMailer
  ↓ compose email
  ↓ to: wali_kelas->email
  ↓ subject: "Pengajuan Izin dari [nama siswa]"
  ↓ body: alasan + link foto
  ↓ send via SMTP
LOG EMAIL
  ↓ status pengiriman
RESPONSE SISWA
  ↓ "Izin berhasil diajukan dan email terkirim ke wali kelas"
WALI KELAS
  ↓ terima email notifikasi
  ↓ buka lampiran foto bukti
```

---

### **Alur 4: Admin Generate Laporan Bulanan**

```
ADMIN
  ↓ akses halaman "Laporan"
  ↓ pilih bulan dan tahun
  ↓ pilih kelas (optional)
  ↓ klik "Generate Laporan"
REQUEST LAPORAN
  ↓ [bulan, tahun, kelas_id]
QUERY DATA (LaporanModel)
  ↓ SELECT dari [presensi_sekolah]
  ↓ WHERE tanggal BETWEEN start_date AND end_date
  ↓ JOIN [users] untuk data siswa
  ↓ JOIN [kelas] untuk data kelas
  ↓ GROUP BY siswa_id
HITUNG STATISTIK
  ↓ COUNT status='hadir' AS total_hadir
  ↓ COUNT status='izin' AS total_izin
  ↓ COUNT status='sakit' AS total_sakit
  ↓ COUNT status='alpha' AS total_alpha
  ↓ CALCULATE persentase kehadiran
FORMAT DATA
  ↓ generate array laporan
  ↓ sort by persentase ASC (yang rendah dulu)
RENDER VIEW
  ↓ tampilkan tabel laporan
  ↓ grafik chart statistik
  ↓ highlight siswa kehadiran <75%
ADMIN
  ↓ klik "Export Excel"
EXPORT FILE
  ↓ generate Excel dengan PHPSpreadsheet
  ↓ download file
```

---

## Catatan Implementasi Teknis

### **Arsitektur Sistem**
- **Pattern:** Model-View-Controller (MVC)
- **Backend:** PHP 7.4+
- **Database:** MySQL/MariaDB
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Session:** PHP Native Session Management
- **Routing:** Custom router dengan .htaccess

### **Teknologi GPS & Validasi Lokasi**
- **API:** HTML5 Geolocation API (navigator.geolocation)
- **Formula:** Haversine Formula untuk calculate jarak GPS
  ```
  distance = 2 * R * asin(sqrt(sin²(Δlat/2) + cos(lat1) * cos(lat2) * sin²(Δlon/2)))
  ```
- **Akurasi:** ±10-50 meter (tergantung device dan signal)
- **Fallback:** Jika GPS tidak tersedia, tampilkan error ke user

### **Email Service**
- **Library:** SwiftMailer (via Composer)
- **Protocol:** SMTP
- **Config:** 
  - Host: configurable (Gmail, Mailtrap, custom SMTP)
  - Port: 587 (TLS) atau 465 (SSL)
  - Auth: username/password
- **Template:** Plain text dan HTML email support
- **Error Handling:** Log email failure ke database/file

### **Security Implementation**
1. **Password Hashing:** `password_hash()` dengan BCRYPT
2. **SQL Injection Prevention:** Prepared statements (PDO)
3. **XSS Prevention:** `htmlspecialchars()` pada output
4. **CSRF Protection:** Token validation untuk form submission
5. **Session Hijacking Prevention:** 
   - Regenerate session ID after login
   - HTTPOnly flag untuk cookie
   - Timeout session setelah inaktif
6. **File Upload Security:**
   - Validasi MIME type
   - Rename file dengan unique hash
   - Size limit enforcement
   - Restricted folder permissions

### **Database Design Principles**
- **Normalization:** 3NF (Third Normal Form)
- **Indexing:** PRIMARY KEY, FOREIGN KEY, INDEX pada field pencarian
- **Timestamps:** created_at, updated_at untuk audit trail
- **Soft Delete:** Tidak implementasi, langsung hard delete
- **Referential Integrity:** ON DELETE CASCADE untuk beberapa relasi

### **File Structure**
```
Presensi-SMK/
├── app/
│   ├── controllers/      # Business logic controllers
│   ├── models/           # Database models
│   ├── views/            # HTML templates
│   └── services/         # External services (EmailService)
├── config/
│   └── config.php        # Database & app configuration
├── public/
│   ├── index.php         # Entry point & router
│   └── assets/           # CSS, JS, images
├── uploads/              # User uploaded files
│   ├── buku_induk/
│   └── izin/
├── vendor/               # Composer dependencies
└── composer.json         # Dependency management
```

### **API Endpoints (AJAX)**
Sistem menggunakan endpoints JSON untuk operasi AJAX:

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | /presensi/submit-sekolah | Submit presensi sekolah |
| POST | /presensi/submit-kelas | Submit presensi kelas |
| POST | /presensi/submit-izin | Submit pengajuan izin |
| GET | /sesi/status-sekolah | Cek sesi sekolah aktif |
| GET | /sesi/status-kelas/{id} | Cek sesi kelas aktif |
| POST | /sesi/buka-kelas | Guru buka sesi kelas |
| POST | /sesi/tutup-kelas | Guru tutup sesi kelas |
| POST | /sesi/perpanjang-kelas | Perpanjang sesi kelas |
| POST | /admin/buka-sesi-sekolah | Admin buka sesi sekolah |
| POST | /admin/tutup-sesi-sekolah | Admin tutup sesi sekolah |
| GET | /laporan/export-excel | Export laporan ke Excel |

### **Validation Rules**

#### **User Input**
- **Username:** 3-50 karakter, alphanumeric + underscore
- **Password:** Min 6 karakter (production: min 8, harus ada huruf+angka)
- **Email:** Valid email format (filter_var dengan FILTER_VALIDATE_EMAIL)
- **Nama:** Max 255 karakter, tidak boleh kosong
- **NIS:** Numeric, unique, 6-10 digit

#### **File Upload**
- **Foto Izin:** JPG, JPEG, PNG | Max 2MB
- **Buku Induk:** PDF, JPG, JPEG, PNG | Max 5MB
- **Naming Convention:** {timestamp}_{userid}_{random}.{ext}

#### **GPS Coordinates**
- **Latitude:** -90 to 90 (decimal)
- **Longitude:** -180 to 180 (decimal)
- **Radius Sekolah:** Default 100 meter (adjustable)

### **Business Rules**

1. **Presensi Sekolah**
   - Siswa hanya bisa presensi 1x per sesi sekolah
   - Validasi GPS hanya untuk status "hadir"
   - Izin/Sakit tidak perlu validasi GPS
   - Setelah sesi ditutup, siswa yang belum presensi = alpha

2. **Presensi Kelas**
   - Siswa bisa presensi multiple kelas per hari (sesuai jadwal)
   - Hanya bisa presensi jika sesi aktif dibuka guru
   - Tidak bisa presensi setelah sesi ditutup
   - 1 presensi per sesi per siswa (no duplicate)

3. **Sesi Presensi**
   - Sesi sekolah: otomatis buka pagi (misal 06:00), tutup sore (16:00)
   - Sesi kelas: manual oleh guru, durasi fleksibel (default 45-90 menit)
   - Perpanjangan sesi: maksimal 3x per sesi (prevent abuse)
   - Sesi expired auto-close dan mark absent students

4. **Role & Permission**
   - Admin: full access semua fitur
   - Guru: manage kelas sendiri, lihat siswa, buka/tutup sesi, laporan kelas
   - Admin Kesiswaan: manage buku induk, lihat laporan global, no edit presensi
   - Siswa: presensi, lihat riwayat pribadi, ajukan izin, lihat buku induk pribadi

5. **Laporan**
   - Update real-time setiap ada presensi baru
   - Cache statistik dashboard (refresh setiap 5 menit)
   - Export format: Excel (XLSX), PDF (future)
   - Rentang laporan max 1 tahun

### **Performance Optimization**
- **Query Optimization:** 
  - Index pada field sering di-query (user_id, tanggal, kelas_id)
  - Avoid SELECT *, specific columns only
  - Use JOIN efficiently, limit result set
- **Caching:** 
  - Session cache untuk user data
  - Browser cache untuk static assets
- **Lazy Loading:** Load data on-demand (pagination untuk list panjang)
- **AJAX:** Prevent full page reload untuk operasi cepat

### **Error Handling**
- **Database Errors:** Try-catch block, log ke file error.log
- **File Upload Errors:** Validate before move, show user-friendly message
- **GPS Errors:** Fallback message jika GPS unavailable
- **Email Errors:** Queue retry mechanism (manual resend)
- **Session Timeout:** Auto-redirect ke login page

### **Deployment Requirements**
- **Server:** Apache 2.4+ atau Nginx
- **PHP:** 7.4+ (recommended 8.0+)
- **Database:** MySQL 5.7+ atau MariaDB 10.3+
- **Extensions:** PDO, mbstring, gd, fileinfo
- **Composer:** Untuk install dependencies
- **HTTPS:** Wajib untuk production (GPS requirement)

### **Future Enhancements**
1. Real-time notification dengan WebSocket/Pusher
2. Mobile app (Android/iOS) dengan native GPS
3. Face recognition untuk presensi tambahan
4. Integration dengan API SIMAK BM/Dapodik
5. QR Code untuk presensi alternatif
6. Multi-language support (Bahasa & English)
7. Dashboard analytics dengan Chart.js
8. Auto-backup database daily
9. SMS notification (selain email)
10. Biometric integration (fingerprint)

---

## Diagram Relationships

### **Entity Relationship Diagram (ERD) Simplified**

```
┌─────────────┐         ┌─────────────┐         ┌─────────────────┐
│    USERS    │────────→│    KELAS    │←───────│  KELAS_SISWA    │
│             │ 1     * │             │  1   * │  (junction)     │
│ - id        │ wali_   │ - id        │        │ - kelas_id      │
│ - nama      │ kelas   │ - nama      │        │ - siswa_id      │
│ - username  │         │ - tahun     │        └─────────────────┘
│ - password  │         │             │                ↑
│ - role      │         └─────────────┘                │ *
│ - email     │                 │                      │
└─────────────┘                 │ 1                    │ 1
       │                        │                      │
       │ 1                      ↓ *              ┌─────┴───────────┐
       │                 ┌──────────────┐        │ PRESENSI_KELAS  │
       │                 │ PRESENSI_    │        │                 │
       │                 │ SESI         │───────→│ - siswa_id      │
       │                 │              │  1  *  │ - sesi_id       │
       │                 │ - kelas_id   │        │ - kelas_id      │
       │                 │ - guru_id    │        │ - status        │
       │                 │ - waktu_buka │        └─────────────────┘
       │                 └──────────────┘
       │
       ↓ 1
┌──────────────────┐         ┌────────────────────────┐
│ PRESENSI_        │    *  1 │ PRESENSI_SEKOLAH_SESI  │
│ SEKOLAH          │←────────│                        │
│                  │         │ - waktu_buka           │
│ - siswa_id       │         │ - waktu_tutup          │
│ - sesi_id        │         │ - status               │
│ - tanggal        │         │ - is_manual            │
│ - status         │         └────────────────────────┘
│ - latitude       │
│ - longitude      │
│ - foto_bukti     │         ┌─────────────────┐
└──────────────────┘         │ LOKASI_SEKOLAH  │
       │                     │                 │
       │ 1                   │ - latitude      │
       ↓ *                   │ - longitude     │
┌──────────────────┐         │ - radius        │
│  BUKU_INDUK      │         └─────────────────┘
│                  │
│ - siswa_id       │
│ - kategori       │
│ - path_file      │
└──────────────────┘
```

### **Relasi Antar Entity**
1. **USERS (1) → KELAS (*):** Satu guru (wali kelas) bisa mengelola banyak kelas
2. **KELAS (1) → KELAS_SISWA (*):** Satu kelas punya banyak siswa
3. **USERS/Siswa (1) → KELAS_SISWA (*):** Satu siswa bisa di banyak kelas
4. **KELAS (1) → PRESENSI_SESI (*):** Satu kelas punya banyak sesi presensi
5. **PRESENSI_SESI (1) → PRESENSI_KELAS (*):** Satu sesi punya banyak record presensi
6. **USERS/Siswa (1) → PRESENSI_SEKOLAH (*):** Satu siswa punya banyak record presensi sekolah
7. **PRESENSI_SEKOLAH_SESI (1) → PRESENSI_SEKOLAH (*):** Satu sesi sekolah punya banyak presensi
8. **USERS/Siswa (1) → BUKU_INDUK (*):** Satu siswa punya banyak dokumen buku induk

---

## Summary Data Flow

### **Input Sources**
1. **Manual Input:** Admin, Guru, Admin Kesiswaan, Siswa via web form
2. **Automated Input:** GPS dari device siswa via browser
3. **File Upload:** Foto, PDF dokumen
4. **External API:** Email via SMTP (SwiftMailer)

### **Processing**
- **Validation:** Input validation, business rules check
- **Computation:** GPS distance calculation, statistics aggregation
- **Transformation:** Data formatting, hash generation, file naming
- **Integration:** Email sending, file system operations

### **Output Destinations**
1. **Web Interface:** Dashboard, laporan, notifikasi
2. **Database:** Persistent storage semua data
3. **File System:** Uploaded files (foto, dokumen)
4. **Email:** Notifikasi eksternal
5. **Export Files:** Excel, PDF laporan

---

**Dibuat:** 28 Desember 2024  
**Versi:** 2.0 (Detail Version)  
**Sistem:** Sistem Informasi Presensi SMK  
**Dokumentasi:** DFD Level 0 - Diagram Konteks Detail  
**Penulis:** System Analyst - Presensi SMK Team
