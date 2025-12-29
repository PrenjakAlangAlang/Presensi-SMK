# Data Flow Diagram (DFD) Level 1
## Sistem Informasi Presensi SMK

### Diagram DFD Level 1

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                                                                                             │
│  ADMINISTRATOR    GURU         ADMIN_KESISWAAN    SISWA          EMAIL_SYSTEM                               │
│       │            │                 │             │                   │                                     │
│       ├────────────┼─────────────────┼─────────────┘                   │                                     │
│       │            │                 │                                 │                                     │
│       │ login      │ login           │ login           login           │                                     │
│       ↓            ↓                 ↓                 ↓               │                                     │
│  ┌────────────────────────────────────────────────────────────┐       │                                     │
│  │  PROSES 1.0                                                 │       │                                     │
│  │  AUTENTIKASI & AUTORISASI                                   │       │                                     │
│  │  • Validasi login                                           │       │                                     │
│  │  • Generate session                                         │       │                                     │
│  │  • Role-based access control                                │       │                                     │
│  └────────────┬────────────────────────────────────────────────┘       │                                     │
│               │                                                        │                                     │
│               ↓                                                        │                                     │
│         [D1: USERS]                                                    │                                     │
│               │                                                        │                                     │
│               │ data user                                              │                                     │
│               │                                                        │                                     │
│               │                                                        │                                     │
│               │                                                        │                                     │
│               │     ┌────────────────────────────┐                    │                                     │
│               │     │                            │                    │                                     │
│               │     │ koordinat GPS              │                    │                                     │
│               │     ↓                            ↓                    │                                     │
│               │  ┌──────────────────────────────────────────────────────────┐                    │
│               │  │  PROSES 2.0                                              │                    │
│               │  │  MANAJEMEN PRESENSI                                      │                    │
│               │  │  • 2.1 Presensi Sekolah (GPS Validation)                │                    │
│               │  │  • 2.2 Presensi Kelas                                   │                    │
│               │  │  • 2.3 Pengajuan Izin/Sakit                             │                    │
│               │  └───┬──────────────────┬───────────────────────────────┬──┘                    │
│               │      │                  │                               │                       │
│               │      ↓                  ↓                               │ request email         │
│               │  [D5: PRESENSI_     [D6: PRESENSI_                      └──────────────────────→│
│               │       SEKOLAH]           KELAS]                                                 │
│               │      │                  │                                                       │
│               │      │                  │                                                       │
│               └──────┼──────────────────┼───────────────────┐                                   │
│                      │                  │                   │                                   │
│                      │                  │ data siswa/kelas  │                                   │
│                      ↓                  ↓                   ↓                                   │
│               ┌──────────────────────────────────────────────────────────┐                      │
│               │  PROSES 3.0                                              │                      │
│               │  MANAJEMEN SESI PRESENSI                                 │                      │
│               │  • 3.1 Sesi Presensi Sekolah (Auto/Manual)              │                      │
│               │  • 3.2 Sesi Presensi Kelas (Manual by Guru)             │                      │
│               │  • 3.3 Perpanjang Sesi                                   │                      │
│               │  • 3.4 Tutup Sesi & Mark Alpha                           │                      │
│               └───┬──────────────────┬───────────────────────────────┬───┘                      │
│                     │                  │                               │                          │
│                     ↓                  ↓                               │                          │
│              [D7: PRESENSI_      [D8: PRESENSI_SESI]                  │                          │
│                   SEKOLAH_SESI]       │                                │                          │
│                     │                  │                               │                          │
│                     │ sesi aktif       │ sesi kelas                    │                          │
│                     └──────────────────┴───────────────────┐           │                          │
│                                                            │           │                          │
│                                                            │           │                          │
│                                                            │           │                          │
│                                                            │           │                          │
│  data siswa                                                │           │                          │
│  ────────────────────────────────────────────────────────┼───────────┘                          │
│                                                            │                                      │
│                                                            ↓                                      │
│                                                 ┌───────────────────────────────────────────┐     │
│                                                 │  PROSES 4.0                               │     │
│                                                 │  MANAJEMEN BUKU INDUK                     │     │
│                                                 │  • Upload dokumen                         │     │
│                                                 │  • View dokumen per siswa                 │     │
│                                                 │  • Edit metadata                          │     │
│                                                 └───────────┬───────────────────────────────┘     │
│                                                             │                                     │
│                                                             ↓                                     │
│                                                      [D9: BUKU_INDUK]                             │
│                                                             │                                     │
│                                                             │                                     │
│                                                             │ data presensi                       │
│                                                             ↓                                     │
│                                                 ┌───────────────────────────────────────────┐     │
│                                                 │  PROSES 5.0                               │     │
│                                                 │  PELAPORAN                                │     │
│                                                 │  • 5.1 Laporan Harian                     │     │
│                                                 │  • 5.2 Laporan Bulanan                    │     │
│                                                 │  • 5.3 Statistik Dashboard                │     │
│                                                 │  • 5.4 Export Excel/PDF                   │     │
│                                                 └───────────┬───────────────────────────────┘     │
│                                                             │                                     │
│                                                             │ read data                           │
│                                           ┌─────────────────┼────────────────────┐                │
│                                           │                 │                    │                │
│                                           ↓                 ↓                    ↓                │
│                                     [D5: PRESENSI_   [D6: PRESENSI_   [D1: USERS]                │
│                                          SEKOLAH]         KELAS]          [D2: KELAS]             │
│                                                             │                                     │
│                                                             │                                     │
│                                                             ↓                                     │
│                                                 ┌───────────────────────────────────────────┐     │
│                                                 │  PROSES 6.0                               │     │
│                                                 │  NOTIFIKASI                               │     │
│                                                 │  • 6.1 Email Notifikasi Izin              │     │
│                                                 │  • 6.2 In-App Notification                │     │
│                                                 └───────────────────────────────────────────┘     │
│                                                                      │                                     │
│                                                                      │ request send email                  │
│                                                                      └─────────────────────────────────────→│
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## Penjelasan Detail Setiap Proses

### **PROSES 1.0: AUTENTIKASI & AUTORISASI**

**Deskripsi:**  
Proses untuk validasi identitas user dan menentukan hak akses berdasarkan role.

**Input:**
| Source | Data Flow | Deskripsi |
|--------|-----------|-----------|
| Administrator | data login | Username + password admin |
| Guru | data login | Username + password guru |
| Admin Kesiswaan | data login | Username + password admin kesiswaan |
| Siswa | data login | Username + password siswa |

**Proses:**
1. Terima kredensial login (username, password)
2. Query table USERS untuk validasi
3. Verifikasi password dengan `password_verify()`
4. Jika valid: generate PHP session dengan data user
5. Set session variables: user_id, nama, role, email
6. Redirect ke dashboard sesuai role

**Output:**
| Destination | Data Flow | Deskripsi |
|-------------|-----------|-----------|
| Administrator | session data + redirect | Dashboard admin |
| Guru | session data + redirect | Dashboard guru |
| Admin Kesiswaan | session data + redirect | Dashboard kesiswaan |
| Siswa | session data + redirect | Dashboard siswa |

**Data Store:**
- **Read:** [D1: USERS] - validasi kredensial
- **Write:** PHP Session Storage

---

### **PROSES 2.0: MANAJEMEN PRESENSI**

**Deskripsi:**  
Proses inti sistem untuk mencatat kehadiran siswa (sekolah & kelas) dan pengajuan izin.

#### **Sub-Proses 3.1: Presensi Sekolah**

**Input:**
| Source | Data Flow | Deskripsi |
|--------|-----------|-----------|
| Siswa | data presensi sekolah | siswa_id, sesi_id, lat, lng, waktu, jenis (hadir/izin/sakit) |
| [D7: PRESENSI_SEKOLAH_SESI] | sesi aktif | Data sesi yang sedang buka |
| [D4: LOKASI_SEKOLAH] | koordinat GPS | Koordinat pusat sekolah + radius |

**Proses:**
1. Cek apakah ada sesi sekolah aktif
2. Jika jenis = 'hadir': validasi GPS dengan haversine formula
3. Cek duplikasi (1 siswa hanya bisa 1x presensi per sesi)
4. Insert data ke table PRESENSI_SEKOLAH
5. Return status presensi

**Output:**
| Destination | Data Flow | Deskripsi |
|-------------|-----------|-----------|
| Siswa | status presensi | Success/gagal + validasi GPS + waktu |

**Data Store:**
- **Read:** [D7: PRESENSI_SEKOLAH_SESI], [D4: LOKASI_SEKOLAH]
- **Write:** [D5: PRESENSI_SEKOLAH]

#### **Sub-Proses 3.2: Presensi Kelas**

**Input:**
| Source | Data Flow | Deskripsi |
|--------|-----------|-----------|
| Siswa | data presensi kelas | siswa_id, sesi_id, kelas_id, waktu |
| [D8: PRESENSI_SESI] | sesi kelas aktif | Data sesi kelas yang dibuka guru |

**Proses:**
1. Cek sesi kelas aktif untuk kelas tersebut
2. Validasi siswa terdaftar di kelas
3. Cek duplikasi presensi
4. Insert data ke table PRESENSI_KELAS
5. Update counter jumlah presensi di sesi

**Output:**
| Destination | Data Flow | Deskripsi |
|-------------|-----------|-----------|
| Siswa | status presensi kelas | Success/gagal + waktu |

**Data Store:**
- **Read:** [D8: PRESENSI_SESI], [D3: KELAS_SISWA]
- **Write:** [D6: PRESENSI_KELAS]

#### **Sub-Proses 2.3: Pengajuan Izin/Sakit**

**Input:**
| Source | Data Flow | Deskripsi |
|--------|-----------|-----------|
| Siswa | data izin | siswa_id, jenis (izin/sakit), alasan, foto_bukti |

**Proses:**
1. Validasi file upload (format, size max 2MB)
2. Simpan foto ke folder uploads/izin/
3. Insert data ke PRESENSI_SEKOLAH dengan status='izin'/'sakit'
4. Trigger notifikasi email ke wali kelas (Proses 7.1)

**Output:**
| Destination | Data Flow | Deskripsi |
|-------------|-----------|-----------|
| Siswa | konfirmasi izin | Izin tercatat + email terkirim |
| Proses 7.0 | request email notifikasi | Data izin untuk dikirim email |

**Data Store:**
- **Write:** [D5: PRESENSI_SEKOLAH]
- **Write:** File System (uploads/izin/)

---

### **PROSES 3.0: MANAJEMEN SESI PRESENSI**

**Deskripsi:**  
Proses untuk membuka, menutup, dan memperpanjang sesi presensi (sekolah & kelas).

#### **Sub-Proses 3.1: Sesi Presensi Sekolah**

**Input:**
| Source | Data Flow | Deskripsi |
|--------|-----------|-----------|
| Administrator | buka sesi manual | waktu_buka, waktu_tutup, note |
| System (Cron) | trigger auto open | Jadwal otomatis buka sesi |
| Administrator | tutup sesi manual | sesi_id |
| Administrator | perpanjang sesi | sesi_id, waktu_tutup_baru |

**Proses:**
1. **Buka Sesi:** Insert sesi baru ke table PRESENSI_SEKOLAH_SESI
2. **Tutup Sesi:** Update status='ditutup', mark absent students as 'alpha'
3. **Perpanjang Sesi:** Update waktu_tutup

**Output:**
| Destination | Data Flow | Deskripsi |
|-------------|-----------|-----------|
| Administrator | status sesi | Sesi dibuka/ditutup/diperpanjang |
| Siswa | info sesi aktif | Notifikasi sesi buka/tutup |
2
**Data Store:**
- **Read/Write:** [D7: PRESENSI_SEKOLAH_SESI]
- **Write:** [D5: PRESENSI_SEKOLAH] (mark alpha)

#### **Sub-Proses 3.2: Sesi Presensi Kelas**

**Input:**
| Source | Data Flow | Deskripsi |
|--------|-----------|-----------|
| Guru | buka sesi kelas | kelas_id, waktu_buka, durasi, keterangan |
| Guru | tutup sesi kelas | sesi_id |
| Guru | perpanjang sesi kelas | sesi_id, menit_tambahan |

**Proses:**
1. **Buka Sesi:** Validasi guru mengajar kelas, insert sesi ke PRESENSI_SESI
2. **Tutup Sesi:** Update status='ditutup', mark absent as 'alpha'
3. **Perpanjang:** Update waktu_tutup

**Output:**
| Destination | Data Flow | Deskripsi |
|-------------|-----------|-----------|
| Guru | status sesi kelas | Konfirmasi operasi sesi |
| Siswa | info sesi kelas aktif | Notifikasi sesi kelas buka/tutup |

**Data Store:**
- **Read/Write:** [D8: PRESENSI_SESI]
- **Write:** [D6: PRESENSI_KELAS] (mark alpha)
- **Read:** [D2: KELAS] (validasi guru)

---

### **PROSES 4.0: MANAJEMEN BUKU INDUK**

**Deskripsi:**  
Proses untuk mengelola dokumen buku induk siswa.

**Input:**
| Source | Data Flow | Deskripsi |
|--------|-----------|-----------|
| Admin Kesiswaan | upload dokumen | siswa_id, kategori, file (PDF/JPG) |
| Admin Kesiswaan | edit dokumen | dokumen_id, metadata |
| Siswa | request view dokumen | siswa_id |

**Proses:**
1. Validasi file upload (format, size max 5MB)
2. Simpan file ke folder uploads/buku_induk/
3. Insert metadata ke table BUKU_INDUK
4. Return list dokumen atau konfirmasi upload

**Output:**
| Destination | Data Flow | Deskripsi |
|-------------|-----------|-----------|
| Admin Kesiswaan | konfirmasi upload | File tersimpan + metadata |
| Siswa | data buku induk pribadi | List dokumen milik siswa |

**Data Store:**
- **Read/Write:** [D9: BUKU_INDUK]
- **Read:** [D1: USERS] (untuk validasi siswa)
- **Write:** File System (uploads/buku_induk/)

---

### **PROSES 5.0: PELAPORAN**

**Deskripsi:**  
Proses untuk generate laporan dan statistik kehadiran.

#### **Sub-Proses 5.1: Laporan Harian**

**Input:**
| Source | Data Flow | Deskripsi |
|--------|-----------|-----------|
| Admin/Guru/Admin Kesiswaan | request laporan harian | tanggal, filter_kelas, filter_siswa |

**Proses:**
1. Query data presensi untuk tanggal tersebut
2. Join dengan table USERS dan KELAS
3. Hitung total hadir, izin, sakit, alpha
4. Format data dalam array

**Output:**
| Destination | Data Flow | Deskripsi |
|-------------|-----------|-----------|
| Requester | laporan harian | Tabel presensi + statistik |

**Data Store:**
- **Read:** [D5: PRESENSI_SEKOLAH], [D6: PRESENSI_KELAS], [D1: USERS], [D2: KELAS]

#### **Sub-Proses 5.2: Laporan Bulanan**

**Input:**
| Source | Data Flow | Deskripsi |
|--------|-----------|-----------|
| Admin/Guru/Admin Kesiswaan | request laporan bulanan | bulan, tahun, filter_kelas |

**Proses:**
1. Aggregate data presensi per siswa per bulan
2. Hitung persentase kehadiran
3. Identifikasi siswa dengan kehadiran < 75%
4. Generate grafik statistik

**Output:**
| Destination | Data Flow | Deskripsi |
|-------------|-----------|-----------|
| Requester | laporan bulanan | Rekap + grafik + highlight |

**Data Store:**
- **Read:** [D5: PRESENSI_SEKOLAH], [D6: PRESENSI_KELAS], [D1: USERS], [D2: KELAS]

#### **Sub-Proses 5.3: Statistik Dashboard**

**Input:**
| Source | Data Flow | Deskripsi |
|--------|-----------|-----------|
| All Roles | request statistik | user_id, role, filter_periode |

**Proses:**
1. Sesuaikan scope data berdasarkan role
2. Hitung total dan persentase kehadiran
3. Generate card widgets

**Output:**
| Destination | Data Flow | Deskripsi |
|-------------|-----------|-----------|
| Requester | statistik dashboard | Cards + charts + tren |

**Data Store:**
- **Read:** [D5: PRESENSI_SEKOLAH], [D6: PRESENSI_KELAS], [D1: USERS], [D2: KELAS]

#### **Sub-Proses 5.4: Export Excel/PDF**

**Input:**
| Source | Data Flow | Deskripsi |
|--------|-----------|-----------|
| Admin/Guru/Admin Kesiswaan | request export | data_laporan, format (excel/pdf) |

**Proses:**
1. Generate file Excel/PDF dari data laporan
2. Return file download

**Output:**
| Destination | Data Flow | Deskripsi |
|-------------|-----------|-----------|
| Requester | file export | File Excel/PDF untuk download |

---

### **PROSES 6.0: NOTIFIKASI**

**Deskripsi:**  
Proses untuk mengirim notifikasi email dan in-app.

#### **Sub-Proses 6.1: Email Notifikasi Izin**

**Input:**
| Source | Data Flow | Deskripsi |
|--------|-----------|-----------|
| Proses 3.3 | request email | data_izin (siswa, kelas, alasan, foto) |

**Proses:**
1. Load EmailService (SwiftMailer)
2. Compose email dengan template
3. Get email wali kelas dari USERS
4. Send via SMTP

**Output:**
| Destination | Data Flow | Deskripsi |
|-------------|-----------|-----------|
| EMAIL_SYSTEM | email data | Email terkirim ke wali kelas |

**Data Store:**
- **Read:** [D1: USERS] (email wali kelas)
- **Read:** [D5: PRESENSI_SEKOLAH] (data izin)

#### **Sub-Proses 6.2: In-App Notification**

**Input:**
| Source | Data Flow | Deskripsi |
|--------|-----------|-----------|
| Various Processes | event trigger | Event sistem (sesi buka/tutup, izin diajukan) |

**Proses:**
1. Detect event dari sistem
2. Generate notifikasi message
3. Display di dashboard/alert

**Output:**
| Destination | Data Flow | Deskripsi |
|-------------|-----------|-----------|
| Target User | notifikasi in-app | Alert message + badge |

**Data Store:**
- **Write:** PHP Session (temporary notification)

---

## Data Store (Database Tables) - Detail

### **[D1: USERS]**
Menyimpan data semua pengguna sistem.

**Fields:** id, nama, username, password, role, email, created_at

**Diakses oleh:**
- Proses 1.0 (Read: validasi login)
- Proses 5.0 (Read: laporan)
- Proses 6.0 (Read: email wali kelas)

---

### **[D2: KELAS]**
Menyimpan data kelas.

**Fields:** id, nama_kelas, tahun_ajaran, wali_kelas_id, created_at

**Diakses oleh:**
- Proses 2.2 (Read/Write: CRUD kelas)
- Proses 3.2 (Read: validasi guru)
- Proses 5
---

### **[D3: KELAS_SISWA]**
Junction table untuk relasi many-to-many kelas-siswa.

**Fields:** id, kelas_id, siswa_id, created_at

**Diakses oleh:**
- Proses 2.2 (Write: assignment siswa ke kelas)
- Proses 3.2 (Read: validasi siswa di kelas)


### **[D4: LOKASI_SEKOLAH]**
Menyimpan koordinat GPS sekolah.

**Fields:** id, latitude, longitude, radius, updated_at

**Diakses oleh:**
- Proses 2.3 (Read/Write: update lokasi)
- Proses 3.1 (Read: validasi GPS presensi)

---
### **[D5: PRESENSI_SEKOLAH]**
Menyimpan data presensi harian siswa ke sekolah.

**Fields:** id, siswa_id, sesi_id, tanggal, waktu, status, latitude, longitude, alasan, foto_bukti, created_at

**Diakses oleh:**
- Proses 3.1 (Write: insert presensi sekolah)
- Proses 3.3 (Write: insert izin/sakit)
- Proses 4.1 (Write: mark alpha saat tutup sesi)
- Proses 6.0 (Read: generate laporan)
- Proses 2.1 (Write: insert presensi sekolah)
- Proses 2.3 (Write: insert izin/sakit)
- Proses 3.1 (Write: mark alpha saat tutup sesi)
- Proses 5.0 (Read: generate laporan)
- Proses 6 PRESENSI_KELAS]**
Menyimpan data presensi siswa per kelas.

**Fields:** id, siswa_id, sesi_id, kelas_id, tanggal, waktu, status, created_at

**Diakses oleh:**
- Proses 3.2 (Write: insert presensi kelas)
- Proses 4.2 (Write: mark alpha saat tutup sesi)
- Proses 6.0 (Read: generate laporan)

---2.2 (Write: insert presensi kelas)
- Proses 3.2 (Write: mark alpha saat tutup sesi)
- Proses 5 PRESENSI_SEKOLAH_SESI]**
Menyimpan sesi presensi sekolah harian.

**Fields:** id, waktu_buka, waktu_tutup, status, is_manual, created_by, note, created_at

**Diakses oleh:**
- Proses 3.1 (Read: cek sesi aktif)
- Proses 4.1 (Read/Write: manage sesi sekolah)

---
2.1 (Read: cek sesi aktif)
- Proses 3 PRESENSI_SESI]**
Menyimpan sesi presensi per kelas.

**Fields:** id, kelas_id, guru_id, waktu_buka, waktu_tutup, status, keterangan, created_at

**Diakses oleh:**
- Proses 3.2 (Read: cek sesi kelas aktif)
- Proses 4.2 (Read/Write: manage sesi kelas)

---
2.2 (Read: cek sesi kelas aktif)
- Proses 3 BUKU_INDUK]**
Menyimpan metadata dokumen buku induk siswa.

**Fields:** id, siswa_id, kategori, nama_file, path_file, uploaded_by, uploaded_at

**Diakses oleh:**
- Proses 5.0 (Read/Write: manage dokumen)

---

## Ketera4gan Simbol DFD

| Simbol | Makna |
|--------|-------|
| ⬭ (Rectangle) | External Entity (Admin, Guru, Siswa, dll) |
| ○ (Circle) | Process (Proses 1.0, 2.0, dst) |
| ═ (Open Rectangle) | Data Store ([D1: USERS], [D2: KELAS], dst) |
| → (Arrow) | Data Flow (aliran data antar komponen) |

---

## Aturan Konsistensi DFD

1. **Setiap proses harus memiliki:**
   - Minimal 1 input data flow
   - Minimal 1 output data flow
   - Nomor unik (1.0, 2.0, dst)
   - Nama yang deskriptif (verb + noun)

2. **Data Store:**
   - Hanya diakses oleh proses, tidak langsung dari/ke external entity
   - Diberi notasi [Dx: NAMA_TABLE]

3. **Data Flow:**
   - Harus memiliki nama yang jelas
   - Arah aliran ditunjukkan dengan panah
   - Tidak boleh ada aliran langsung antar external entity

4. **Balancing:**
   - DFD Level 1 harus konsisten dengan Level 0
   - Input/output di Level 0 harus match dengan Level 1

---

**Dibuat:** 28 Desember 2024  
**Versi:** 2.0  
**Sistem:** Sistem Informasi Presensi SMK  
**Dokumentasi:** DFD Level 1 - Decomposisi Proses Utama  
**Penulis:** System Analyst - Presensi SMK Team
