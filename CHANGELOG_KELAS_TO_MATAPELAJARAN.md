# Dokumentasi Perubahan: Kelas → Mata Pelajaran

## Ringkasan Perubahan

Sistem presensi telah diubah dari konsep "Kelas" menjadi "Mata Pelajaran". Perubahan ini mencakup:
- Model: KelasModel.php → MataPelajaranModel.php
- Tabel database: kelas → mata_pelajaran
- Terminologi di seluruh aplikasi

## 1. Perubahan Model

### File Baru
- ✅ **app/models/MataPelajaranModel.php** (dibuat baru)
  - Class: `MataPelajaranModel`
  - Methods diubah dari `getKelas*` → `getMataPelajaran*`
  - Methods diubah dari `getSiswaInKelas` → `getSiswaInMataPelajaran`
  - Methods diubah dari `addSiswaToKelas` → `addSiswaToMataPelajaran`
  - dst.

### File Lama
- ⚠️ **app/models/KelasModel.php** - Dapat dihapus setelah migration selesai

## 2. Perubahan Controller

### AdminController.php
✅ Diupdate dengan perubahan:
- `require_once KelasModel.php` → `MataPelajaranModel.php`
- `private $kelasModel` → `$mataPelajaranModel`
- `new KelasModel()` → `new MataPelajaranModel()`
- Semua method calls diupdate ke MataPelajaranModel
- Variable `$totalKelas` → `$totalMataPelajaran`
- Comments: "kelas" → "mata pelajaran", "wali kelas" → "guru pengampu"

### GuruController.php
✅ Diupdate dengan perubahan serupa:
- Model reference diubah ke MataPelajaranModel
- Comments diupdate: "kelas yang dia asuh" → "mata pelajaran yang dia ampu"
- Error messages: "Kelas tidak dipilih" → "Mata pelajaran tidak dipilih"
- Filename export: `nama_kelas` → `nama_mata_pelajaran`

### SiswaController.php
✅ Diupdate dengan perubahan serupa:
- Model reference diubah ke MataPelajaranModel
- Comments: "kelas yang diikuti siswa" → "mata pelajaran yang diikuti siswa"

### AdminKesiswaanController.php
✅ Diupdate dengan perubahan serupa:
- Model reference diubah ke MataPelajaranModel
- Laporan title: "Laporan Presensi Kelas" → "Laporan Presensi Mata Pelajaran"

## 3. Perubahan Database

### Migration File
✅ **db/migrations/migration_kelas_to_mata_pelajaran.sql**

Melakukan perubahan:
1. `RENAME TABLE kelas TO mata_pelajaran`
2. `nama_kelas` → `nama_mata_pelajaran`
3. `wali_kelas` → `guru_pengampu`
4. `siswa_kelas` → `siswa_mata_pelajaran`
5. `kelas_id` → `mata_pelajaran_id` (di tabel siswa_mata_pelajaran)

### Tabel yang Terpengaruh
- ✅ `mata_pelajaran` (dari `kelas`)
- ✅ `siswa_mata_pelajaran` (dari `siswa_kelas`)
- ⚠️ `presensi_kelas` - masih menggunakan `kelas_id` (optional untuk diubah)
- ⚠️ `presensi_sesi` - masih menggunakan `kelas_id` (optional untuk diubah)
- ⚠️ `laporan_kemajuan` - masih menggunakan `kelas_id` (optional untuk diubah)

## 4. Perubahan View (Masih Perlu Manual Update)

### ⚠️ app/views/admin/kelas.php
Perlu diubah:
- Page title: "Manajemen Kelas" → "Manajemen Mata Pelajaran"
- Button: "Tambah Kelas" → "Tambah Mata Pelajaran"
- Headers: "Nama Kelas" → "Nama Mata Pelajaran", "Wali Kelas" → "Guru Pengampu"
- Modal titles dan form labels
- JavaScript: `$kelasModel` → `$mataPelajaranModel`
- Variabel: `$kelas` → `$mataPelajaran`
- Data attributes: `data-nama_kelas` → `data-nama_mata_pelajaran`, `data-wali_kelas` → `data-guru_pengampu`

### ⚠️ app/views/guru/kelas.php
Perlu diubah:
- Page title: "Kelas Saya" → "Mata Pelajaran yang Diampu"
- Text: "Kelas yang Diampu" → "Mata Pelajaran yang Diampu"
- Field references: `nama_kelas` → `nama_mata_pelajaran`
- Error messages di JavaScript

### ⚠️ app/views/siswa/presensi.php
Perlu diubah:
- Form labels dan dropdown untuk mata pelajaran
- JavaScript: references ke `nama_kelas` → `nama_mata_pelajaran`

### ⚠️ app/views/admin/dashboard.php
Perlu diubah:
- Statistik: "Total Kelas" → "Total Mata Pelajaran"
- Variabel: `$totalKelas` → `$totalMataPelajaran`

### ⚠️ app/views/guru/dashboard.php
Perlu diubah:
- Text dan comments yang merujuk ke "kelas"

### ⚠️ app/views/siswa/dashboard.php
Perlu diubah:
- References ke mata pelajaran yang diikuti

## 5. Langkah-Langkah Implementasi

### Sudah Selesai ✅
1. Membuat MataPelajaranModel.php baru
2. Update AdminController.php
3. Update GuruController.php
4. Update SiswaController.php
5. Update AdminKesiswaanController.php
6. Membuat migration SQL

### Masih Perlu Dilakukan ⚠️
1. **BACKUP DATABASE** sebelum jalankan migration
2. Jalankan migration SQL: `migration_kelas_to_mata_pelajaran.sql`
3. Update view files (lihat daftar di atas)
4. Update routing di index.php jika ada parameter yang perlu diubah
5. Testing menyeluruh:
   - CRUD mata pelajaran
   - Assign siswa ke mata pelajaran
   - Presensi kelas (mata pelajaran)
   - Laporan
   - Export Excel/PDF

## 6. Kompatibilitas Backward

### Yang Masih Menggunakan Nama Lama
Untuk kemudahan dan kompatibilitas, beberapa hal masih menggunakan nama lama:
- URL parameters: `kelas_id` (dapat diubah nanti jika perlu)
- Nama action: `admin_kelas`, `admin_create_kelas`, dll (untuk kompatibilitas routing)
- Tabel lain yang reference: `presensi_kelas.kelas_id`, `presensi_sesi.kelas_id`, dll

### Rekomendasi
Ubah secara bertahap jika diperlukan untuk menghindari break pada production.

## 7. Testing Checklist

- [ ] Login sebagai Admin
- [ ] Buat mata pelajaran baru
- [ ] Edit mata pelajaran
- [ ] Tambah siswa ke mata pelajaran
- [ ] Hapus siswa dari mata pelajaran
- [ ] Login sebagai Guru
- [ ] Lihat mata pelajaran yang diampu
- [ ] Buka sesi presensi mata pelajaran
- [ ] Tutup sesi presensi
- [ ] Export laporan Excel
- [ ] Export laporan PDF
- [ ] Login sebagai Siswa
- [ ] Presensi ke mata pelajaran
- [ ] Lihat riwayat presensi mata pelajaran

## 8. Catatan Penting

1. **Backup Database**: WAJIB backup sebelum jalankan migration
2. **Testing**: Test semua fitur setelah migration
3. **View Files**: Update manual diperlukan untuk view files
4. **Konsistensi**: Pastikan semua terminologi konsisten di UI
5. **Documentation**: Update user manual jika ada

## 9. Rollback Plan

Jika terjadi masalah, restore database dari backup dan:
1. Revert file models ke KelasModel.php
2. Revert controllers ke versi sebelumnya
3. Restore database dari backup

---
**Tanggal Perubahan**: 3 Maret 2026
**Developer**: GitHub Copilot
**Status**: Backend Complete, Views Need Manual Update
