# Panduan Struktur Kelas dan Mata Pelajaran Baru

## Ringkasan Perubahan

Sistem telah direstrukturisasi untuk memisahkan **Kelas** dan **Mata Pelajaran** menjadi entitas yang berbeda dengan hubungan many-to-many. Ini memungkinkan satu kelas memiliki banyak mata pelajaran.

## Struktur Database Baru

### 1. Table `kelas`
Table untuk menyimpan daftar kelas yang tersedia.

**Kolom:**
- `id` (INT, PRIMARY KEY)
- `nama_kelas` (VARCHAR) - Contoh: "X RPL 1", "XI RPL 2"
- `tahun_ajaran` (VARCHAR) - Contoh: "2025/2026"

### 2. Table `mata_pelajaran`
Table untuk menyimpan daftar mata pelajaran.

**Kolom:**
- `id` (INT, PRIMARY KEY)
- `nama_mata_pelajaran` (VARCHAR) - Contoh: "Matematika", "Pemrograman Web"
- `guru_pengampu` (INT, FOREIGN KEY ke users)
- `jadwal` (TEXT, optional)

**Perubahan:** Field `tahun_ajaran` dihapus karena sekarang ada di table `kelas`.

### 3. Table `kelas_mata_pelajaran` (Junction Table)
Table penghubung antara kelas dan mata pelajaran (many-to-many relationship).

**Kolom:**
- `id` (INT, PRIMARY KEY)
- `kelas_id` (INT, FOREIGN KEY ke kelas)
- `mata_pelajaran_id` (INT, FOREIGN KEY ke mata_pelajaran)

**Fungsi:** Satu kelas bisa memiliki banyak mata pelajaran, dan satu mata pelajaran bisa diajarkan di banyak kelas.

### 4. Table `siswa_kelas`
Hubungan siswa dengan kelas (tetap seperti sebelumnya).

**Kolom:**
- `id` (INT, PRIMARY KEY)
- `siswa_id` (INT, FOREIGN KEY ke users)
- `kelas_id` (INT, FOREIGN KEY ke kelas)

### 5. Table `siswa_mata_pelajaran`
Hubungan siswa dengan mata pelajaran (tetap seperti sebelumnya).

**Kolom:**
- `id` (INT, PRIMARY KEY)
- `siswa_id` (INT, FOREIGN KEY ke users)
- `mata_pelajaran_id` (INT, FOREIGN KEY ke mata_pelajaran)

### 6. Table `presensi_sesi`
**Perubahan Penting:** Kolom `kelas_id` diubah menjadi `mata_pelajaran_id`.

**Kolom:**
- `id` (INT, PRIMARY KEY)
- `mata_pelajaran_id` (INT, FOREIGN KEY) - **PERUBAHAN UTAMA**
- `guru_id` (INT, FOREIGN KEY ke users)
- `waktu_buka` (DATETIME)
- `waktu_tutup` (DATETIME)
- `status` (ENUM: 'open', 'closed')

**Alasan:** Presensi dibuka per mata pelajaran, bukan per kelas.

## Cara Menjalankan Migration

1. **Backup database Anda terlebih dahulu!**
   ```bash
   # Di phpMyAdmin atau MySQL command line
   mysqldump -u root presensi_smk > backup_before_migration.sql
   ```

2. **Jalankan migration SQL:**
   ```bash
   # Import file migration
   mysql -u root presensi_smk < db/migrations/migration_kelas_matapelajaran_restructure.sql
   ```

3. **Verifikasi struktur table:**
   - Cek bahwa table `kelas` dan `kelas_mata_pelajaran` sudah dibuat
   - Cek bahwa `mata_pelajaran` tidak lagi memiliki kolom `tahun_ajaran`
   - Cek bahwa `presensi_sesi` sekarang menggunakan `mata_pelajaran_id`

4. **Sesuaikan data sample:**
   Edit file migration untuk menyesuaikan data kelas dan relasi kelas-mata pelajaran sesuai kebutuhan sekolah Anda.

## Perubahan Kode

### Models yang Diupdate

1. **KelasModel.php**
   - Mengelola CRUD untuk table `kelas`
   - Mengelola relasi siswa-kelas
   - Mengelola relasi kelas-mata pelajaran
   - Method baru: `getMataPelajaranInKelas()`, `addMataPelajaranToKelas()`, dll.

2. **MataPelajaranModel.php**
   - `createMataPelajaran()` - tidak lagi menerima `tahun_ajaran`
   - `updateMataPelajaran()` - tidak lagi menerima `tahun_ajaran`
   - Method lain tetap sama

3. **PresensiSesiModel.php**
   - `createSession()` - sekarang menggunakan `mata_pelajaran_id`
   - `closeSession()` - sekarang menggunakan `mata_pelajaran_id`
   - `getActiveSessionByMataPelajaran()` - method baru
   - `getSessionsByMataPelajaran()` - method baru
   - Backward compatibility: `getActiveSessionByKelas()` dan `getSessionsByKelas()` tetap ada sebagai alias

### Controllers yang Diupdate

1. **AdminController.php**
   - Menambahkan `KelasModel` sebagai dependency
   - `kelas()` - sekarang menampilkan data kelas DAN mata pelajaran
   - `createKelas()`, `updateKelas()`, `deleteKelas()` - sekarang support kedua entity (kelas dan mata pelajaran) dengan parameter `type`
   - Method baru untuk manage relasi kelas-mata pelajaran:
     - `getMataPelajaranDalamKelas()`
     - `addMataPelajaranToKelas()`
     - `removeMataPelajaranFromKelas()`
     - `getSiswaInKelasEntity()`
     - dll.

2. **GuruController.php**
   - `bukaPresensiKelas()` - sekarang bekerja dengan `mata_pelajaran_id`
   - `tutupPresensiKelas()` - sekarang bekerja dengan `mata_pelajaran_id`
   - `kelas()` - menggunakan `getActiveSessionByMataPelajaran()`
   - `laporan()` - menggunakan `getSessionsByMataPelajaran()`

3. **SiswaController.php**
   - `presensi()` - menggunakan `getActiveSessionByMataPelajaran()`
   - `izin()` - menggunakan `getActiveSessionByMataPelajaran()`

## Cara Menggunakan Sistem Baru

### Untuk Admin

1. **Mengelola Kelas:**
   - Buka halaman Admin > Kelas
   - Tab "Daftar Kelas" untuk manage kelas (X RPL 1, XI RPL 2, dll)
   - Tambah kelas baru dengan nama dan tahun ajaran
   - Edit atau hapus kelas yang ada

2. **Mengelola Mata Pelajaran:**
   - Tab "Daftar Mata Pelajaran" untuk manage mata pelajaran
   - Tambah mata pelajaran dengan nama, guru pengampu, dan jadwal
   - Edit atau hapus mata pelajaran yang ada

3. **Menghubungkan Kelas dengan Mata Pelajaran:**
   - Klik "Manage Mata Pelajaran" pada kelas tertentu
   - Tambah mata pelajaran yang akan diajarkan di kelas tersebut
   - Satu kelas bisa memiliki banyak mata pelajaran
   - Satu mata pelajaran bisa diajarkan di banyak kelas

4. **Mengelola Siswa:**
   - Tambah siswa ke kelas (siswa_kelas)
   - Tambah siswa ke mata pelajaran (siswa_mata_pelajaran)

### Untuk Guru

1. **Membuka Presensi:**
   - Guru membuka presensi **per mata pelajaran**, bukan per kelas
   - Semua siswa yang terdaftar di mata pelajaran tersebut bisa melakukan presensi

2. **Menutup Presensi:**
   - Tutup presensi per mata pelajaran
   - Siswa yang belum presensi otomatis ditandai alpha

### Untuk Siswa

1. **Melakukan Presensi:**
   - Siswa melihat daftar mata pelajaran yang diikuti
   - Jika sesi presensi mata pelajaran dibuka, siswa bisa melakukan presensi

## Diagram Hubungan

```
┌─────────────┐         ┌──────────────────────┐         ┌─────────────────┐
│   Kelas     │◄────────│ kelas_mata_pelajaran │─────────►│ Mata Pelajaran  │
│             │  1:N    │   (junction table)   │   N:1   │                 │
│ - id        │         │ - kelas_id           │         │ - id            │
│ - nama_kelas│         │ - mata_pelajaran_id  │         │ - nama_mapel    │
│ - thn_ajaran│         └──────────────────────┘         │ - guru_pengampu │
└─────────────┘                                           │ - jadwal        │
      ▲                                                   └─────────────────┘
      │                                                            ▲
      │ 1:N                                                        │ 1:N
      │                                                            │
┌─────────────┐                                          ┌─────────────────┐
│ siswa_kelas │                                          │ siswa_mapel     │
│             │                                          │                 │
│ - siswa_id  │                                          │ - siswa_id      │
│ - kelas_id  │                                          │ - mapel_id      │
└─────────────┘                                          └─────────────────┘
```

## Contoh Skenario

### Skenario 1: Kelas X RPL 1 dengan 3 Mata Pelajaran

1. **Buat Kelas:**
   - Nama: X RPL 1
   - Tahun Ajaran: 2025/2026

2. **Buat Mata Pelajaran:**
   - Matematika (Guru: Pak Budi)
   - Pemrograman Web (Guru: Pak Andi)
   - Basis Data (Guru: Bu Siti)

3. **Hubungkan:**
   - X RPL 1 ↔ Matematika
   - X RPL 1 ↔ Pemrograman Web
   - X RPL 1 ↔ Basis Data

4. **Tambah Siswa ke Kelas:**
   - Siswa A, B, C masuk ke kelas X RPL 1

5. **Tambah Siswa ke Mata Pelajaran:**
   - Siswa A, B, C secara otomatis bisa ditambahkan ke semua mata pelajaran di kelas tersebut

### Skenario 2: Guru Membuka Presensi

1. Pak Budi login sebagai guru
2. Buka menu "Kelas Saya"
3. Pilih "Matematika"
4. Klik "Buka Presensi" → Sesi presensi mata pelajaran Matematika dibuka
5. Siswa yang terdaftar di Matematika bisa melakukan presensi
6. Setelah selesai, Pak Budi tutup presensi dan beri catatan

## Troubleshooting

### Error: Column 'tahun_ajaran' not found in mata_pelajaran
**Solusi:** Pastikan migration sudah dijalankan dengan benar. Field `tahun_ajaran` sudah dipindah ke table `kelas`.

### Error: Column 'kelas_id' not found in presensi_sesi
**Solusi:** Migration mengubah `kelas_id` menjadi `mata_pelajaran_id`. Jalankan migration dengan benar.

### Presensi tidak bisa dibuka
**Solusi:** Pastikan mata pelajaran sudah dihubungkan dengan kelas menggunakan table `kelas_mata_pelajaran`.

### Siswa tidak muncul di daftar presensi
**Solusi:** Pastikan siswa sudah ditambahkan ke:
1. Kelas (table `siswa_kelas`)
2. Mata pelajaran (table `siswa_mata_pelajaran`)

## Catatan Penting

1. **Backward Compatibility:** Beberapa method lama masih ada sebagai alias untuk menghindari breaking changes pada views yang belum diupdate.

2. **Views Perlu Diupdate:** File-file view (PHP) perlu diupdate untuk menampilkan UI baru yang support manajemen kelas dan mata pelajaran secara terpisah.

3. **Index Routes:** Pastikan routing di `index.php` sudah ditambahkan untuk action baru seperti:
   - `admin_get_matapelajaran_dalam_kelas`
   - `admin_add_matapelajaran_to_kelas`
   - `admin_remove_matapelajaran_from_kelas`
   - dll.

4. **Testing:** Test semua fungsi setelah migration:
   - [ ] Create/Edit/Delete Kelas
   - [ ] Create/Edit/Delete Mata Pelajaran
   - [ ] Link/Unlink Kelas ↔ Mata Pelajaran
   - [ ] Buka/Tutup Presensi per Mata Pelajaran
   - [ ] Siswa melakukan presensi
   - [ ] Laporan presensi

## Keuntungan Struktur Baru

1. **Fleksibilitas:** Satu kelas bisa memiliki banyak mata pelajaran
2. **Reusability:** Satu mata pelajaran bisa diajarkan di banyak kelas tanpa duplikasi data
3. **Skalabilitas:** Mudah menambah kelas atau mata pelajaran baru
4. **Maintenance:** Perubahan data guru pengampu atau jadwal mata pelajaran tidak perlu update di banyak tempat
5. **Clarity:** Pemisahan yang jelas antara konsep "kelas" (kelompok siswa) dan "mata pelajaran" (subject yang diajarkan)

## Support

Jika ada pertanyaan atau masalah, silakan hubungi developer atau check dokumentasi lain:
- CHANGELOG_KELAS_TO_MATAPELAJARAN.md
- DOKUMENTASI_PERUBAHAN_KELAS_MAPEL.md
- SETUP_KELAS_MAPEL.md

---
**Tanggal Update:** 4 Maret 2026
**Versi:** 2.0
