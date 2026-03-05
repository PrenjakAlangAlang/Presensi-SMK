# Struktur Final: Kelas & Mata Pelajaran

## Konsep Penting ⚠️

**SISWA DIKELOLA PER MATA PELAJARAN, BUKAN PER KELAS**

Artinya:
- Siswa **TIDAK** didaftarkan ke kelas secara langsung
- Siswa didaftarkan ke **MATA PELAJARAN** yang ada di dalam kelas
- Satu kelas bisa memiliki banyak mata pelajaran
- Setiap mata pelajaran memiliki daftar siswa sendiri

## Struktur Database

```
kelas
├── id
├── nama_kelas (contoh: "XII RPL 1")
└── tahun_ajaran (contoh: "2023/2024")

mata_pelajaran
├── id
├── nama (contoh: "Matematika")
├── guru_id
├── jadwal (contoh: "Senin, 08:00-10:00")
└── (tahun_ajaran DIHAPUS - sekarang di kelas)

kelas_mata_pelajaran (relasi many-to-many)
├── kelas_id → kelas.id
└── mata_pelajaran_id → mata_pelajaran.id

siswa_mata_pelajaran (relasi siswa dengan mata pelajaran)
├── siswa_id → users.id
└── mata_pelajaran_id → mata_pelajaran.id
```

### Tabel yang TIDAK ADA:
❌ `siswa_kelas` - TIDAK DIGUNAKAN

## Alur Kerja (Workflow)

### 1. Admin Membuat Kelas
```
Menu: Admin → Kelas
Action: Tambah Kelas Baru
Input: 
  - Nama Kelas: "XII RPL 1"
  - Tahun Ajaran: "2023/2024"
```

### 2. Admin Membuat Mata Pelajaran dan Menambahkannya ke Kelas
```
Menu: Admin → Mata Pelajaran
Action: Tambah Mata Pelajaran
Input:
  - Nama: "Matematika"
  - Guru Pengampu: [Pilih Guru]
  - Jadwal: "Senin, 08:00-10:00"
  
Kemudian:
Menu: Admin → Kelas → [Pilih Kelas] → Kelola Mata Pelajaran
Action: Tambahkan mata pelajaran ke kelas
```

### 3. Admin Mengelola Siswa PER MATA PELAJARAN
```
Menu: Admin → Mata Pelajaran → [Pilih Mata Pelajaran]
Action: Kelola Siswa
- Di sini admin menambahkan siswa ke mata pelajaran tertentu
- Siswa hanya akan melihat dan bisa absen di mata pelajaran yang mereka ikuti
```

### 4. Guru Melihat Mata Pelajaran Per Kelas
```
Menu: Guru → Kelas
- Guru melihat mata pelajaran yang dia ampu, dikelompokkan per kelas
- Contoh: "Matematika - XII RPL 1"
- Guru bisa buka sesi presensi untuk mata pelajaran tersebut
```

### 5. Siswa Melihat Mata Pelajaran yang Diikuti
```
Menu: Siswa → Dashboard
- Siswa hanya melihat mata pelajaran yang telah didaftarkan (via siswa_mata_pelajaran)
- Siswa melakukan presensi di mata pelajaran tersebut
```

## Contoh Skenario

### Contoh 1: Satu Kelas, Beberapa Mata Pelajaran
```
Kelas: XII RPL 1 (Tahun Ajaran 2023/2024)

Mata Pelajaran di Kelas:
1. Matematika (Pak Budi, Senin 08:00)
   - Siswa: Andi, Budi, Citra
   
2. Bahasa Indonesia (Bu Ani, Selasa 10:00)
   - Siswa: Andi, Doni, Eka
   
3. Pemrograman Web (Pak Joko, Rabu 13:00)
   - Siswa: Budi, Citra, Eka
```

**Penjelasan:**
- Tidak semua siswa mengikuti semua mata pelajaran
- Setiap mata pelajaran punya daftar siswa sendiri
- Total siswa di kelas = DISTINCT siswa dari semua mata pelajaran = 5 orang (Andi, Budi, Citra, Doni, Eka)

### Contoh 2: Satu Mata Pelajaran di Beberapa Kelas
```
Mata Pelajaran: Matematika (Pak Budi)

Kelas yang menggunakan:
1. XII RPL 1
   - Siswa: Andi, Budi, Citra
   
2. XII TKJ 1
   - Siswa: Zaki, Yuni, Wati
```

**Penjelasan:**
- Satu mata pelajaran bisa diajarkan di beberapa kelas
- Setiap kombinasi kelas-mata pelajaran punya daftar siswa sendiri
- Pak Budi akan melihat "Matematika - XII RPL 1" dan "Matematika - XII TKJ 1" sebagai item terpisah

## File-file yang Diubah

### 1. Database Migration
**File:** `db/migrations/migration_add_kelas_structure.sql`
- Membuat tabel `kelas` dan `kelas_mata_pelajaran`
- Menghapus kolom `tahun_ajaran` dari `mata_pelajaran`
- TIDAK membuat tabel `siswa_kelas`

### 2. Model
**File:** `app/models/KelasModel.php`
- `getAllKelas()` - Hitung siswa dari `siswa_mata_pelajaran` via JOIN
- `getKelasById()` - Sama, hitung siswa dengan DISTINCT
- `getSiswaInKelas()` - Ambil DISTINCT siswa yang ada di mata pelajaran kelas ini
- `getTotalSiswaByKelas()` - Hitung DISTINCT siswa
- ❌ Method yang dihapus: `addSiswaToKelas()`, `removeSiswaFromKelas()`, `getAvailableSiswa()`, `getKelasBySiswa()`

**File:** `app/models/MataPelajaranModel.php`
- `createMataPelajaran()` - Tidak lagi menyimpan tahun_ajaran
- `updateMataPelajaran()` - Tidak lagi update tahun_ajaran
- `getSiswaByMataPelajaran()` - Ambil siswa per mata pelajaran
- `addSiswaToMataPelajaran()` - Tambah siswa ke mata pelajaran
- `removeSiswaFromMataPelajaran()` - Hapus siswa dari mata pelajaran

### 3. Controller
**File:** `app/controllers/AdminController.php`
- `kelas()` - Halaman kelola kelas
- `createKelas()`, `updateKelas()`, `deleteKelas()` - CRUD kelas
- `getMataPelajaranDalamKelas()` - API get mata pelajaran di kelas
- `getMataPelajaranTersediaKelas()` - API get mata pelajaran available
- `addMataPelajaranToKelas()` - API tambah mata pelajaran ke kelas
- `removeMataPelajaranFromKelas()` - API hapus mata pelajaran dari kelas
- ❌ Method yang dihapus: `getSiswaDalamKelas()`, `getSiswaTersediaKelas()`, `addSiswaToKelas()`, `removeSiswaFromKelas()`

### 4. View
**File:** `app/views/admin/kelas.php`
- Tampilan card untuk setiap kelas
- Tombol "Kelola Mata Pelajaran" (bukan "Kelola Siswa")
- Modal untuk manage mata pelajaran per kelas
- ❌ Yang dihapus: Tombol "Kelola Siswa", Modal kelola siswa, Function JS terkait siswa

**File:** `app/views/admin/mata_pelajaran.php`
- Tampilan tabel semua mata pelajaran
- CRUD mata pelajaran
- Modal untuk **Kelola Siswa PER MATA PELAJARAN**

**File:** `app/views/guru/kelas.php`
- Guru melihat mata pelajaran yang diampu, dikelompokkan per kelas
- Contoh: "Matematika - XII RPL 1"

### 5. Routing
**File:** `index.php`
- ❌ Route yang dihapus:
  - `admin_get_siswa_kelas`
  - `admin_get_siswa_tersedia_kelas`
  - `admin_add_siswa_kelas`
  - `admin_remove_siswa_kelas`
  
- ✅ Route yang masih ada:
  - `admin_kelas` - Halaman kelas
  - `admin_create_kelas`, `admin_update_kelas`, `admin_delete_kelas`
  - `admin_get_mapel_kelas`, `admin_get_mapel_tersedia_kelas`
  - `admin_add_mapel_kelas`, `admin_remove_mapel_kelas`
  - `admin_mata_pelajaran` - Halaman mata pelajaran

## Query Examples

### Mendapatkan Semua Siswa di Kelas (DISTINCT)
```sql
SELECT DISTINCT u.* 
FROM users u 
INNER JOIN siswa_mata_pelajaran smp ON u.id = smp.siswa_id 
INNER JOIN kelas_mata_pelajaran kmp ON smp.mata_pelajaran_id = kmp.mata_pelajaran_id
WHERE kmp.kelas_id = ? AND u.role = 'siswa'
ORDER BY u.nama ASC
```

### Menghitung Total Siswa di Kelas
```sql
SELECT COUNT(DISTINCT smp.siswa_id) as total 
FROM kelas_mata_pelajaran kmp
INNER JOIN siswa_mata_pelajaran smp ON kmp.mata_pelajaran_id = smp.mata_pelajaran_id
WHERE kmp.kelas_id = ?
```

### Mendapatkan Mata Pelajaran dalam Kelas dengan Info Siswa
```sql
SELECT 
    mp.*,
    k.nama_kelas,
    u.nama as nama_guru,
    COUNT(DISTINCT smp.siswa_id) as jumlah_siswa
FROM mata_pelajaran mp
INNER JOIN kelas_mata_pelajaran kmp ON mp.id = kmp.mata_pelajaran_id
INNER JOIN kelas k ON kmp.kelas_id = k.id
LEFT JOIN users u ON mp.guru_id = u.id
LEFT JOIN siswa_mata_pelajaran smp ON mp.id = smp.mata_pelajaran_id
WHERE kmp.kelas_id = ?
GROUP BY mp.id
```

## Cara Setup

1. **Backup Database**
   ```bash
   # Backup dulu sebelum migrasi
   mysqldump -u root presensi_smk > backup_sebelum_migrasi.sql
   ```

2. **Jalankan Migration**
   ```bash
   # Import via phpMyAdmin atau MySQL CLI
   mysql -u root presensi_smk < db/migrations/migration_add_kelas_structure.sql
   ```

3. **Verifikasi Structure**
   - Cek tabel `kelas` ada
   - Cek tabel `kelas_mata_pelajaran` ada
   - Cek tabel `mata_pelajaran` tidak ada kolom `tahun_ajaran`
   - Cek tabel `siswa_kelas` TIDAK ADA

4. **Test Workflow**
   - Login sebagai admin
   - Buat kelas baru
   - Tambah mata pelajaran ke kelas
   - Kelola siswa dari menu Mata Pelajaran

## FAQ

### Q: Bagaimana cara mendaftarkan siswa ke kelas?
**A:** Siswa TIDAK didaftarkan ke kelas. Siswa didaftarkan ke MATA PELAJARAN yang ada di kelas. Masuk ke menu Mata Pelajaran → Kelola Siswa.

### Q: Bagaimana siswa bisa pindah kelas?
**A:** Karena siswa tidak terikat ke kelas, cukup pindahkan pendaftaran mata pelajaran siswa tersebut. Misal siswa tadinya di "Matematika - XII RPL 1", hapus dan daftarkan ke "Matematika - XII RPL 2".

### Q: Bagaimana cara melihat semua siswa di satu kelas?
**A:** Di menu Admin → Kelas, klik kelas yang ingin dilihat. Jumlah siswa ditampilkan di kartu (DISTINCT dari semua mata pelajaran di kelas tersebut).

### Q: Apakah satu mata pelajaran bisa diajarkan di beberapa kelas?
**A:** Ya. Satu mata pelajaran bisa di-assign ke beberapa kelas (many-to-many via `kelas_mata_pelajaran`).

### Q: Bagaimana guru melihat kelas yang diampu?
**A:** Guru login → Menu Kelas. Akan muncul daftar mata pelajaran yang diampu, dikelompokkan per kelas (contoh: "Matematika - XII RPL 1").

## Perbedaan dengan Versi Lama

| Aspek | Versi Lama | Versi Baru |
|-------|------------|------------|
| Struktur Kelas | Tidak ada tabel kelas | Ada tabel `kelas` |
| Tahun Ajaran | Di tabel `mata_pelajaran` | Di tabel `kelas` |
| Relasi Kelas-Mapel | Tidak ada | Many-to-many via `kelas_mata_pelajaran` |
| Siswa | Langsung ke mata pelajaran | Tetap ke mata pelajaran (via `siswa_mata_pelajaran`) |
| Tampilan Guru | List mata pelajaran | List mata pelajaran per kelas |

## Kesimpulan

✅ **Yang Benar:**
- Admin buat kelas
- Admin tambah mata pelajaran ke kelas
- Admin kelola siswa PER MATA PELAJARAN
- Siswa terdaftar di mata pelajaran, bukan di kelas
- Total siswa kelas = DISTINCT siswa dari semua mata pelajaran di kelas

❌ **Yang Salah:**
- Siswa didaftarkan ke kelas langsung
- Ada tabel `siswa_kelas`
- Siswa otomatis dapat semua mata pelajaran di kelas
- Tahun ajaran di `mata_pelajaran`

---

**Dibuat:** 2024
**Versi:** 2.0 - Final Structure
**Status:** ✅ Complete
