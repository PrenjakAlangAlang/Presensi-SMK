# Data Flow Diagram (DFD) Level 2
## Sistem Informasi Presensi SMK
### Detail Proses 3.0: MANAJEMEN PRESENSI

---

## Diagram DFD Level 2 - Proses 3.0: Manajemen Presensi

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                                                                                                  │
│                                   SISWA (External Entity)                                                        │
│                                         │                                                                        │
│                    ┌────────────────────┼────────────────────┐                                                  │
│                    │                    │                    │                                                  │
│                    │                    │                    │                                                  │
│        ┌───────────▼──────────┐   ┌────▼────────────┐   ┌───▼──────────────────┐                              │
│        │  data presensi       │   │ data presensi   │   │  data izin/sakit     │                              │
│        │  sekolah (hadir)     │   │ kelas           │   │  + foto bukti        │                              │
│        └───────────┬──────────┘   └────┬────────────┘   └───┬──────────────────┘                              │
│                    │                   │                    │                                                  │
│                    │                   │                    │                                                  │
│                    ↓                   ↓                    ↓                                                  │
│      ┌─────────────────────────────────────────────────────────────────────────────────────┐                  │
│      │                                                                                       │                  │
│      │  PROSES 3.1                                                                           │                  │
│      │  VALIDASI & PRESENSI SEKOLAH                                                          │                  │
│      │                                                                                       │                  │
│      │  ┌─────────────────────────────────────────────────────────────────────────────┐    │                  │
│      │  │ 3.1.1 CEK SESI SEKOLAH AKTIF                                                 │    │                  │
│      │  │ • Query sesi dari [D7: PRESENSI_SEKOLAH_SESI]                               │    │                  │
│      │  │ • Cek status='aktif' AND waktu_buka <= NOW <= waktu_tutup                   │    │                  │
│      │  │ • Return sesi_id atau NULL                                                   │    │                  │
│      │  └─────────────────────────┬───────────────────────────────────────────────────┘    │                  │
│      │                            │                                                         │                  │
│      │                            ↓                                                         │                  │
│      │  ┌─────────────────────────────────────────────────────────────────────────────┐    │                  │
│      │  │ 3.1.2 CEK DUPLIKASI PRESENSI                                                 │    │                  │
│      │  │ • Query [D5: PRESENSI_SEKOLAH]                                               │    │                  │
│      │  │ • WHERE siswa_id = ? AND sesi_id = ?                                         │    │                  │
│      │  │ • IF EXISTS: Return error "Sudah presensi"                                   │    │                  │
│      │  └─────────────────────────┬───────────────────────────────────────────────────┘    │                  │
│      │                            │                                                         │                  │
│      │                            ↓                                                         │                  │
│      │  ┌─────────────────────────────────────────────────────────────────────────────┐    │                  │
│      │  │ 3.1.3 VALIDASI GPS (Jika status=hadir)                                       │    │                  │
│      │  │ • Get koordinat sekolah dari [D4: LOKASI_SEKOLAH]                            │    │                  │
│      │  │ • Hitung jarak dengan Haversine Formula:                                     │    │                  │
│      │  │   distance = haversine(lat1, lon1, lat2, lon2)                               │    │                  │
│      │  │ • IF distance > radius: Return error "Di luar radius"                        │    │                  │
│      │  │ • IF distance <= radius: Continue                                            │    │                  │
│      │  └─────────────────────────┬───────────────────────────────────────────────────┘    │                  │
│      │                            │                                                         │                  │
│      │                            ↓                                                         │                  │
│      │  ┌─────────────────────────────────────────────────────────────────────────────┐    │                  │
│      │  │ 3.1.4 INSERT DATA PRESENSI SEKOLAH                                           │    │                  │
│      │  │ • Prepare data: siswa_id, sesi_id, tanggal, waktu, status                   │    │                  │
│      │  │ • IF status=hadir: simpan latitude, longitude                                │    │                  │
│      │  │ • IF status=izin/sakit: latitude=0, longitude=0, simpan foto_bukti          │    │                  │
│      │  │ • Execute INSERT INTO presensi_sekolah                                       │    │                  │
│      │  │ • Return presensi_id                                                         │    │                  │
│      │  └─────────────────────────┬───────────────────────────────────────────────────┘    │                  │
│      │                            │                                                         │                  │
│      └────────────────────────────┼─────────────────────────────────────────────────────────┘                  │
│                                   │                                                                            │
│                                   ↓                                                                            │
│                         [D5: PRESENSI_SEKOLAH]                                                                 │
│                                   │                                                                            │
│                                   │                                                                            │
│                                   ↓                                                                            │
│                            ┌──────────────┐                                                                    │
│                            │ status saved │                                                                    │
│                            └──────┬───────┘                                                                    │
│                                   │                                                                            │
│                                   ↓                                                                            │
│                                 SISWA                                                                          │
│                    (konfirmasi: "Presensi berhasil dicatat")                                                   │
│                                                                                                                │
│                                                                                                                │
│  ┌─────────────────────────────────────────────────────────────────────────────────────────────────────┐      │
│  │                                                                                                       │      │
│  │  PROSES 3.2                                                                                           │      │
│  │  PRESENSI KELAS                                                                                       │      │
│  │                                                                                                       │      │
│  │  ┌─────────────────────────────────────────────────────────────────────────────┐                     │      │
│  │  │ 3.2.1 CEK SESI KELAS AKTIF                                                   │                     │      │
│  │  │ • Query [D8: PRESENSI_SESI]                                                  │                     │      │
│  │  │ • WHERE kelas_id = ? AND status='aktif'                                      │                     │      │
│  │  │ • AND waktu_buka <= NOW <= waktu_tutup                                       │                     │      │
│  │  │ • Return sesi_id atau NULL                                                   │                     │      │
│  │  └─────────────────────────┬───────────────────────────────────────────────────┘                     │      │
│  │                            │                                                                          │      │
│  │                            ↓                                                                          │      │
│  │  ┌─────────────────────────────────────────────────────────────────────────────┐                     │      │
│  │  │ 3.2.2 VALIDASI SISWA TERDAFTAR DI KELAS                                      │                     │      │
│  │  │ • Query [D3: KELAS_SISWA]                                                    │                     │      │
│  │  │ • WHERE kelas_id = ? AND siswa_id = ?                                        │                     │      │
│  │  │ • IF NOT EXISTS: Return error "Tidak terdaftar di kelas"                    │                     │      │
│  │  └─────────────────────────┬───────────────────────────────────────────────────┘                     │      │
│  │                            │                                                                          │      │
│  │                            ↓                                                                          │      │
│  │  ┌─────────────────────────────────────────────────────────────────────────────┐                     │      │
│  │  │ 3.2.3 CEK DUPLIKASI PRESENSI KELAS                                           │                     │      │
│  │  │ • Query [D6: PRESENSI_KELAS]                                                 │                     │      │
│  │  │ • WHERE siswa_id = ? AND sesi_id = ?                                         │                     │      │
│  │  │ • IF EXISTS: Return error "Sudah presensi untuk sesi ini"                   │                     │      │
│  │  └─────────────────────────┬───────────────────────────────────────────────────┘                     │      │
│  │                            │                                                                          │      │
│  │                            ↓                                                                          │      │
│  │  ┌─────────────────────────────────────────────────────────────────────────────┐                     │      │
│  │  │ 3.2.4 INSERT PRESENSI KELAS                                                  │                     │      │
│  │  │ • Prepare data: siswa_id, sesi_id, kelas_id, tanggal, waktu                 │                     │      │
│  │  │ • status = 'hadir' (default)                                                 │                     │      │
│  │  │ • Execute INSERT INTO presensi_kelas                                         │                     │      │
│  │  │ • Update counter di [D8: PRESENSI_SESI]: jumlah_presensi++                  │                     │      │
│  │  │ • Return success                                                             │                     │      │
│  │  └─────────────────────────┬───────────────────────────────────────────────────┘                     │      │
│  │                            │                                                                          │      │
│  └────────────────────────────┼──────────────────────────────────────────────────────────────────────────┘      │
│                               │                                                                                 │
│                               ↓                                                                                 │
│                      [D6: PRESENSI_KELAS]                                                                       │
│                               │                                                                                 │
│                               ↓                                                                                 │
│                             SISWA                                                                               │
│                (konfirmasi: "Presensi kelas berhasil")                                                          │
│                                                                                                                 │
│                                                                                                                 │
│  ┌──────────────────────────────────────────────────────────────────────────────────────────────────────┐      │
│  │                                                                                                        │      │
│  │  PROSES 3.3                                                                                            │      │
│  │  PENGAJUAN IZIN/SAKIT                                                                                  │      │
│  │                                                                                                        │      │
│  │  ┌─────────────────────────────────────────────────────────────────────────────┐                      │      │
│  │  │ 3.3.1 VALIDASI FILE UPLOAD                                                   │                      │      │
│  │  │ • Cek ekstensi file: [jpg, jpeg, png]                                        │                      │      │
│  │  │ • Cek ukuran file: max 2MB                                                   │                      │      │
│  │  │ • Validasi MIME type untuk security                                          │                      │      │
│  │  │ • IF invalid: Return error "File tidak valid"                                │                      │      │
│  │  └─────────────────────────┬───────────────────────────────────────────────────┘                      │      │
│  │                            │                                                                           │      │
│  │                            ↓                                                                           │      │
│  │  ┌─────────────────────────────────────────────────────────────────────────────┐                      │      │
│  │  │ 3.3.2 SIMPAN FILE KE SERVER                                                  │                      │      │
│  │  │ • Generate unique filename: timestamp_siswa_id_random.ext                    │                      │      │
│  │  │ • Move uploaded file ke folder: uploads/izin/                                │                      │      │
│  │  │ • Return path_file                                                           │                      │      │
│  │  └─────────────────────────┬───────────────────────────────────────────────────┘                      │      │
│  │                            │                                                                           │      │
│  │                            ↓                                                                           │      │
│  │  ┌─────────────────────────────────────────────────────────────────────────────┐                      │      │
│  │  │ 3.3.3 GET SESI SEKOLAH AKTIF                                                 │                      │      │
│  │  │ • Query [D7: PRESENSI_SEKOLAH_SESI]                                          │                      │      │
│  │  │ • WHERE status='aktif'                                                       │                      │      │
│  │  │ • Return sesi_id (untuk link izin dengan sesi)                               │                      │      │
│  │  └─────────────────────────┬───────────────────────────────────────────────────┘                      │      │
│  │                            │                                                                           │      │
│  │                            ↓                                                                           │      │
│  │  ┌─────────────────────────────────────────────────────────────────────────────┐                      │      │
│  │  │ 3.3.4 INSERT DATA IZIN KE PRESENSI_SEKOLAH                                   │                      │      │
│  │  │ • Prepare data:                                                              │                      │      │
│  │  │   - siswa_id, sesi_id                                                        │                      │      │
│  │  │   - tanggal, waktu                                                           │                      │      │
│  │  │   - status = 'izin' atau 'sakit'                                             │                      │      │
│  │  │   - latitude = 0, longitude = 0 (no GPS validation)                          │                      │      │
│  │  │   - alasan (text)                                                            │                      │      │
│  │  │   - foto_bukti (path file)                                                   │                      │      │
│  │  │ • Execute INSERT INTO presensi_sekolah                                       │                      │      │
│  │  │ • Return presensi_id                                                         │                      │      │
│  │  └─────────────────────────┬───────────────────────────────────────────────────┘                      │      │
│  │                            │                                                                           │      │
│  │                            ↓                                                                           │      │
│  │  ┌─────────────────────────────────────────────────────────────────────────────┐                      │      │
│  │  │ 3.3.5 GET DATA WALI KELAS                                                    │                      │      │
│  │  │ • Query siswa dari [D1: USERS]                                               │                      │      │
│  │  │ • Get kelas siswa dari [D3: KELAS_SISWA]                                     │                      │      │
│  │  │ • Get wali_kelas_id dari [D2: KELAS]                                         │                      │      │
│  │  │ • Get email wali kelas dari [D1: USERS]                                      │                      │      │
│  │  │ • Return email_wali_kelas                                                    │                      │      │
│  │  └─────────────────────────┬───────────────────────────────────────────────────┘                      │      │
│  │                            │                                                                           │      │
│  │                            ↓                                                                           │      │
│  │  ┌─────────────────────────────────────────────────────────────────────────────┐                      │      │
│  │  │ 3.3.6 TRIGGER NOTIFIKASI EMAIL                                               │                      │      │
│  │  │ • Compose data email:                                                        │                      │      │
│  │  │   - to: email_wali_kelas                                                     │                      │      │
│  │  │   - subject: "Pengajuan Izin dari [nama siswa]"                              │                      │      │
│  │  │   - body: detail izin (siswa, kelas, tanggal, alasan)                        │                      │      │
│  │  │   - attachment: link foto bukti                                              │                      │      │
│  │  │ • Send request to PROSES 7.1 (Email Notifikasi)                              │                      │      │
│  │  │ • Return status email                                                        │                      │      │
│  │  └─────────────────────────┬───────────────────────────────────────────────────┘                      │      │
│  │                            │                                                                           │      │
│  └────────────────────────────┼───────────────────────────────────────────────────────────────────────────┘      │
│                               │                                                                                  │
│                               ↓                                                                                  │
│                     [D5: PRESENSI_SEKOLAH]                                                                       │
│                               │                                                                                  │
│                               │ trigger to Proses 7.1                                                            │
│                               ↓                                                                                  │
│                             SISWA                                                                                │
│            (konfirmasi: "Izin berhasil diajukan, email terkirim ke wali kelas")                                 │
│                                                                                                                  │
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## Detail Implementasi Sub-Proses

### **PROSES 3.1: VALIDASI & PRESENSI SEKOLAH**

#### **Sub-Proses 3.1.1: Cek Sesi Sekolah Aktif**

**Tujuan:** Memastikan ada sesi presensi sekolah yang sedang aktif.

**Algoritma:**
```sql
SELECT * FROM presensi_sekolah_sesi 
WHERE status = 'aktif' 
  AND waktu_buka <= NOW() 
  AND waktu_tutup >= NOW()
LIMIT 1;
```

**Input:**
- Waktu sistem (current timestamp)

**Output:**
- `sesi_id` jika ada sesi aktif
- `NULL` jika tidak ada sesi aktif

**Error Handling:**
- Jika `NULL`: Return error "Tidak ada sesi presensi aktif saat ini"

---

#### **Sub-Proses 3.1.2: Cek Duplikasi Presensi**

**Tujuan:** Mencegah siswa melakukan presensi lebih dari 1x untuk sesi yang sama.

**Algoritma:**
```sql
SELECT COUNT(*) FROM presensi_sekolah 
WHERE siswa_id = ? 
  AND sesi_id = ?;
```

**Input:**
- `siswa_id` (dari session user)
- `sesi_id` (dari sub-proses 3.1.1)

**Output:**
- `count` (jumlah record)

**Business Logic:**
- `IF count > 0`: Return error "Anda sudah melakukan presensi untuk sesi ini"
- `IF count = 0`: Continue ke sub-proses berikutnya

---

#### **Sub-Proses 3.1.3: Validasi GPS**

**Tujuan:** Memvalidasi lokasi siswa berada dalam radius sekolah (hanya untuk status 'hadir').

**Algoritma Haversine Formula:**
```php
function haversine($lat1, $lon1, $lat2, $lon2) {
    $R = 6371000; // Radius bumi dalam meter
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $distance = $R * $c; // Jarak dalam meter
    
    return $distance;
}
```

**Input:**
- `latitude_siswa`, `longitude_siswa` (dari GPS device)
- `latitude_sekolah`, `longitude_sekolah` (dari [D4: LOKASI_SEKOLAH])
- `radius` (dari [D4: LOKASI_SEKOLAH])

**Proses:**
1. Get koordinat sekolah dari database
2. Hitung jarak dengan haversine formula
3. Compare dengan radius

**Output:**
- `distance` (jarak dalam meter)
- `is_valid` (boolean)

**Business Logic:**
- `IF distance <= radius`: Valid, continue
- `IF distance > radius`: Return error "Anda berada di luar radius sekolah"

**Special Case:**
- `IF status = 'izin' OR status = 'sakit'`: SKIP validasi GPS

---

#### **Sub-Proses 3.1.4: Insert Data Presensi Sekolah**

**Tujuan:** Menyimpan data presensi ke database.

**Algoritma:**
```sql
INSERT INTO presensi_sekolah (
    siswa_id, 
    sesi_id, 
    tanggal, 
    waktu, 
    status, 
    latitude, 
    longitude, 
    alasan, 
    foto_bukti, 
    created_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW());
```

**Input:**
- `siswa_id` (dari session)
- `sesi_id` (dari sub-proses 3.1.1)
- `tanggal` (current date)
- `waktu` (current time)
- `status` ('hadir', 'izin', 'sakit')
- `latitude`, `longitude` (GPS siswa atau 0 jika izin/sakit)
- `alasan` (text, NULL jika hadir)
- `foto_bukti` (path file, NULL jika hadir)

**Output:**
- `presensi_id` (ID record baru)
- `success` (boolean)

**Post-Processing:**
- Log activity ke audit trail (optional)
- Clear cache statistik dashboard

---

### **PROSES 3.2: PRESENSI KELAS**

#### **Sub-Proses 3.2.1: Cek Sesi Kelas Aktif**

**Tujuan:** Memastikan ada sesi presensi kelas yang dibuka oleh guru.

**Algoritma:**
```sql
SELECT * FROM presensi_sesi 
WHERE kelas_id = ? 
  AND status = 'aktif' 
  AND waktu_buka <= NOW() 
  AND waktu_tutup >= NOW()
LIMIT 1;
```

**Input:**
- `kelas_id` (dari request siswa)

**Output:**
- `sesi_id`, `guru_id`, `keterangan`, `waktu_tutup`
- `NULL` jika tidak ada sesi aktif

**Error Handling:**
- Jika `NULL`: Return error "Tidak ada sesi presensi kelas aktif untuk kelas ini"

---

#### **Sub-Proses 3.2.2: Validasi Siswa Terdaftar di Kelas**

**Tujuan:** Memastikan siswa terdaftar di kelas yang akan dipresensi.

**Algoritma:**
```sql
SELECT COUNT(*) FROM kelas_siswa 
WHERE kelas_id = ? 
  AND siswa_id = ?;
```

**Input:**
- `kelas_id` (dari request)
- `siswa_id` (dari session)

**Output:**
- `count` (jumlah record)

**Business Logic:**
- `IF count = 0`: Return error "Anda tidak terdaftar di kelas ini"
- `IF count = 1`: Continue

---

#### **Sub-Proses 3.2.3: Cek Duplikasi Presensi Kelas**

**Tujuan:** Mencegah siswa presensi lebih dari 1x untuk sesi kelas yang sama.

**Algoritma:**
```sql
SELECT COUNT(*) FROM presensi_kelas 
WHERE siswa_id = ? 
  AND sesi_id = ?;
```

**Input:**
- `siswa_id`, `sesi_id`

**Output:**
- `count`

**Business Logic:**
- `IF count > 0`: Return error "Anda sudah presensi untuk sesi kelas ini"
- `IF count = 0`: Continue

---

#### **Sub-Proses 3.2.4: Insert Presensi Kelas**

**Tujuan:** Menyimpan data presensi kelas dan update counter sesi.

**Algoritma:**
```sql
-- Insert presensi
INSERT INTO presensi_kelas (
    siswa_id, 
    sesi_id, 
    kelas_id, 
    tanggal, 
    waktu, 
    status, 
    created_at
) VALUES (?, ?, ?, ?, ?, 'hadir', NOW());

-- Update counter sesi
UPDATE presensi_sesi 
SET jumlah_presensi = jumlah_presensi + 1 
WHERE id = ?;
```

**Input:**
- Data presensi (siswa_id, sesi_id, kelas_id, tanggal, waktu)

**Output:**
- `presensi_id`
- `success` (boolean)

**Transaction:**
- Kedua query harus berhasil (COMMIT) atau rollback

---

### **PROSES 3.3: PENGAJUAN IZIN/SAKIT**

#### **Sub-Proses 3.3.1: Validasi File Upload**

**Tujuan:** Memvalidasi file foto bukti izin/sakit.

**Validation Rules:**
```php
$allowed_ext = ['jpg', 'jpeg', 'png'];
$max_size = 2 * 1024 * 1024; // 2MB in bytes
$allowed_mime = ['image/jpeg', 'image/png'];

// Validasi ekstensi
$file_ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
if (!in_array($file_ext, $allowed_ext)) {
    return error("Format file tidak valid. Gunakan JPG/PNG");
}

// Validasi ukuran
if ($_FILES['foto']['size'] > $max_size) {
    return error("Ukuran file maksimal 2MB");
}

// Validasi MIME type (security)
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $_FILES['foto']['tmp_name']);
finfo_close($finfo);

if (!in_array($mime, $allowed_mime)) {
    return error("File bukan gambar valid");
}
```

**Input:**
- `$_FILES['foto']` (uploaded file)

**Output:**
- `is_valid` (boolean)
- `error_message` (jika invalid)

---

#### **Sub-Proses 3.3.2: Simpan File ke Server**

**Tujuan:** Menyimpan file foto bukti dengan nama unik.

**Algoritma:**
```php
// Generate unique filename
$timestamp = time();
$siswa_id = $_SESSION['user_id'];
$random = bin2hex(random_bytes(8));
$extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
$filename = "{$timestamp}_{$siswa_id}_{$random}.{$extension}";

// Target path
$target_dir = __DIR__ . '/../../uploads/izin/';
$target_path = $target_dir . $filename;

// Ensure directory exists
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true);
}

// Move uploaded file
if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_path)) {
    $path_file = "uploads/izin/" . $filename;
    return $path_file;
} else {
    return error("Gagal menyimpan file");
}
```

**Input:**
- Temporary uploaded file

**Output:**
- `path_file` (relative path untuk disimpan di database)

---

#### **Sub-Proses 3.3.3: Get Sesi Sekolah Aktif**

**Tujuan:** Mendapatkan sesi sekolah aktif untuk link izin dengan sesi.

**Algoritma:**
```sql
SELECT id FROM presensi_sekolah_sesi 
WHERE status = 'aktif' 
ORDER BY waktu_buka DESC 
LIMIT 1;
```

**Note:**
- Jika tidak ada sesi aktif, izin tetap bisa diajukan tapi `sesi_id = NULL`
- Nanti akan di-link dengan sesi saat sesi dibuka

---

#### **Sub-Proses 3.3.4: Insert Data Izin**

**Tujuan:** Menyimpan data izin/sakit ke table presensi_sekolah.

**Algoritma:**
```sql
INSERT INTO presensi_sekolah (
    siswa_id, 
    sesi_id, 
    tanggal, 
    waktu, 
    status, 
    latitude, 
    longitude, 
    alasan, 
    foto_bukti, 
    created_at
) VALUES (?, ?, ?, ?, ?, 0, 0, ?, ?, NOW());
```

**Special:**
- `latitude = 0`, `longitude = 0` (tidak perlu validasi GPS)
- `status` = 'izin' atau 'sakit' (sesuai pilihan siswa)

---

#### **Sub-Proses 3.3.5: Get Data Wali Kelas**

**Tujuan:** Mendapatkan email wali kelas untuk notifikasi.

**Algoritma:**
```sql
-- Get kelas siswa
SELECT k.id, k.wali_kelas_id 
FROM kelas k
JOIN kelas_siswa ks ON k.id = ks.kelas_id
WHERE ks.siswa_id = ?;

-- Get email wali kelas
SELECT email FROM users 
WHERE id = ?;  -- wali_kelas_id
```

**Note:**
- Jika siswa punya multiple kelas, kirim email ke semua wali kelas

---

#### **Sub-Proses 3.3.6: Trigger Notifikasi Email**

**Tujuan:** Mengirim request ke Proses 7.1 untuk kirim email.

**Data yang dikirim:**
```php
$email_data = [
    'to' => $email_wali_kelas,
    'subject' => "Pengajuan Izin dari {$nama_siswa}",
    'body' => "
        Siswa: {$nama_siswa}
        Kelas: {$nama_kelas}
        Tanggal: {$tanggal}
        Jenis: {$jenis_izin}
        Alasan: {$alasan}
        
        Foto bukti terlampir.
    ",
    'attachment' => $foto_bukti_path
];
```

---

## Data Flow Antar Sub-Proses

### **Flow Presensi Sekolah (Hadir):**
```
SISWA → 3.1.1 (Cek Sesi) → 3.1.2 (Cek Duplikasi) → 3.1.3 (Validasi GPS) → 3.1.4 (Insert) → SISWA (Response)
            ↓                    ↓                         ↓                      ↓
      [D7: SESI]           [D5: PRESENSI]           [D4: LOKASI]          [D5: PRESENSI]
```

### **Flow Presensi Kelas:**
```
SISWA → 3.2.1 (Cek Sesi Kelas) → 3.2.2 (Validasi Siswa) → 3.2.3 (Cek Duplikasi) → 3.2.4 (Insert) → SISWA
            ↓                            ↓                         ↓                      ↓
      [D8: SESI]                  [D3: KELAS_SISWA]         [D6: PRESENSI]         [D6: PRESENSI]
                                                                                    [D8: SESI] (update counter)
```

### **Flow Pengajuan Izin:**
```
SISWA → 3.3.1 (Validasi File) → 3.3.2 (Simpan File) → 3.3.3 (Get Sesi) → 3.3.4 (Insert Izin) → 3.3.5 (Get Wali) → 3.3.6 (Email) → PROSES 7.1
                                          ↓                   ↓                     ↓                    ↓                ↓
                                   File System          [D7: SESI]          [D5: PRESENSI]         [D1,D2,D3]       Email Service
```

---

## Error Handling Matrix

| Sub-Proses | Kondisi Error | Error Code | Error Message |
|------------|---------------|------------|---------------|
| 3.1.1 | Tidak ada sesi aktif | 404 | "Tidak ada sesi presensi aktif saat ini" |
| 3.1.2 | Sudah presensi | 409 | "Anda sudah melakukan presensi untuk sesi ini" |
| 3.1.3 | GPS di luar radius | 403 | "Anda berada di luar radius sekolah (jarak: X meter)" |
| 3.1.4 | Database error | 500 | "Gagal menyimpan presensi, coba lagi" |
| 3.2.1 | Tidak ada sesi kelas | 404 | "Tidak ada sesi presensi kelas aktif" |
| 3.2.2 | Tidak terdaftar | 403 | "Anda tidak terdaftar di kelas ini" |
| 3.2.3 | Sudah presensi kelas | 409 | "Anda sudah presensi untuk sesi kelas ini" |
| 3.2.4 | Database error | 500 | "Gagal menyimpan presensi kelas" |
| 3.3.1 | File invalid | 400 | "Format file tidak valid (gunakan JPG/PNG)" |
| 3.3.1 | File terlalu besar | 413 | "Ukuran file maksimal 2MB" |
| 3.3.2 | Upload gagal | 500 | "Gagal menyimpan file, coba lagi" |
| 3.3.4 | Database error | 500 | "Gagal menyimpan data izin" |
| 3.3.6 | Email gagal | 502 | "Izin tersimpan, namun email gagal terkirim" |

---

## Performance Considerations

### **Indexing Database:**
```sql
-- Untuk sub-proses 3.1.1, 3.2.1 (query sesi aktif)
CREATE INDEX idx_sesi_status ON presensi_sekolah_sesi(status, waktu_buka, waktu_tutup);
CREATE INDEX idx_sesi_kelas_status ON presensi_sesi(kelas_id, status, waktu_buka, waktu_tutup);

-- Untuk sub-proses 3.1.2, 3.2.3 (cek duplikasi)
CREATE INDEX idx_presensi_siswa_sesi ON presensi_sekolah(siswa_id, sesi_id);
CREATE INDEX idx_presensi_kelas_siswa_sesi ON presensi_kelas(siswa_id, sesi_id);

-- Untuk sub-proses 3.2.2 (validasi siswa di kelas)
CREATE INDEX idx_kelas_siswa ON kelas_siswa(kelas_id, siswa_id);
```

### **Caching Strategy:**
- Cache hasil validasi GPS (coordinate sekolah) selama 1 hari
- Cache list sesi aktif di Redis/Memcached untuk reduce DB query
- Invalidate cache saat sesi dibuka/ditutup

### **Optimization Tips:**
1. **Validasi GPS:** Pre-calculate min/max lat/lng untuk bounding box sebelum haversine
2. **File Upload:** Compress image sebelum save (optional)
3. **Batch Processing:** Queue email notifikasi untuk async processing
4. **Transaction:** Use database transaction untuk atomic operations

---

## Security Measures

### **SQL Injection Prevention:**
```php
// BENAR: Gunakan prepared statement
$stmt = $pdo->prepare("SELECT * FROM presensi_sekolah WHERE siswa_id = ? AND sesi_id = ?");
$stmt->execute([$siswa_id, $sesi_id]);

// SALAH: Jangan langsung interpolasi
// $query = "SELECT * FROM presensi_sekolah WHERE siswa_id = $siswa_id"; // VULNERABLE!
```

### **File Upload Security:**
1. Validasi MIME type dengan `finfo_file()`, jangan hanya ekstensi
2. Rename file dengan unique name (timestamp + random)
3. Store di luar document root atau restrict access
4. Implement file quarantine/scanning (optional)

### **GPS Spoofing Prevention:**
1. Cross-check dengan WiFi/Cell tower location (jika available)
2. Log suspicious activities (GPS jump, impossible speed)
3. Require multiple consecutive GPS readings
4. Admin dapat review dan flag suspicious presensi

---

## Testing Checklist

### **Unit Testing:**
- [ ] Test haversine formula dengan koordinat known distance
- [ ] Test file upload validation (valid/invalid cases)
- [ ] Test duplikasi check dengan mock data
- [ ] Test sesi aktif query dengan berbagai waktu

### **Integration Testing:**
- [ ] Test full flow presensi sekolah (hadir)
- [ ] Test full flow presensi kelas
- [ ] Test full flow pengajuan izin + email
- [ ] Test concurrent presensi (race condition)

### **User Acceptance Testing:**
- [ ] Siswa dapat presensi dalam radius sekolah
- [ ] Siswa tidak dapat presensi di luar radius
- [ ] Siswa dapat ajukan izin dengan foto
- [ ] Wali kelas terima email notifikasi
- [ ] Error message jelas dan informatif

---

**Dibuat:** 28 Desember 2024  
**Versi:** 2.0  
**Sistem:** Sistem Informasi Presensi SMK  
**Dokumentasi:** DFD Level 2 - Detail Proses Manajemen Presensi  
**Penulis:** System Analyst - Presensi SMK Team
