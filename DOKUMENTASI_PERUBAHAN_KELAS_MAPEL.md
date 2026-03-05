# DOKUMENTASI PERUBAHAN: Sistem Kelas dan Mata Pelajaran

**Tanggal:** 4 Maret 2026  
**Developer:** GitHub Copilot

## 📌 Overview

Sistem telah diubah dari model **Mata Pelajaran sebagai Unit Utama** menjadi **Kelas sebagai Unit Utama yang menampung banyak Mata Pelajaran**.

## 🔄 Perubahan Konsep

### Sebelumnya:
- Setiap mata pelajaran memiliki guru pengampu dan jadwal sendiri
- Siswa langsung ditambahkan ke mata pelajaran
- Presensi berdasarkan mata pelajaran

### Sekarang:
- **Kelas** (contoh: X Akuntansi 1, XI Perhotelan) sebagai wadah utama
- Kelas dapat menampung **banyak mata pelajaran**
- Setiap mata pelajaran di kelas tertentu dapat memiliki **guru pengampu** dan **jadwal** tersendiri
- Siswa terdaftar di **kelas**, dan otomatis mengikuti semua mata pelajaran di kelas tersebut

## 📊 Struktur Database Baru

### 1. Tabel `kelas`
```sql
- id (PK)
- nama_kelas (contoh: "X Akuntansi 1")
- tahun_ajaran (contoh: "2025/2026")
- created_at
- updated_at
```

### 2. Tabel `mata_pelajaran`
```sql
- id (PK)
- nama_mata_pelajaran (contoh: "Matematika")
- kode_mata_pelajaran (contoh: "MTK")
- created_at
- updated_at
```

### 3. Tabel `kelas_mata_pelajaran` (Junction Table)
```sql
- id (PK)
- kelas_id (FK -> kelas.id)
- mata_pelajaran_id (FK -> mata_pelajaran.id)
- guru_pengampu (FK -> users.id) [nullable]
- jadwal (contoh: "Senin, 07:30-09:00") [nullable]
- created_at
```

### 4. Tabel `siswa_kelas` (Relasi Siswa-Kelas)
```sql
- id (PK)
- siswa_id (FK -> users.id)
- kelas_id (FK -> kelas.id)
- created_at
```

## 📁 File yang Diubah

### 1. Database Migration
**File:** `db/migrations/migration_restructure_kelas_mata_pelajaran.sql`

**Isi:**
- Create tabel `kelas`
- Create tabel `mata_pelajaran`
- Create tabel `kelas_mata_pelajaran`
- Create tabel `siswa_kelas`
- Contoh data dummy
- Update tabel presensi untuk kompatibilitas

### 2. Model Files

#### a. KelasModel.php
**File:** `app/models/KelasModel.php`

**Method Utama:**
- `getAllKelas()` - Ambil semua kelas
- `getKelasById($id)` - Ambil detail kelas
- `createKelas($data)` - Buat kelas baru
- `updateKelas($data)` - Update kelas
- `deleteKelas($id)` - Hapus kelas
- `getMataPelajaranInKelas($kelas_id)` - Ambil semua mata pelajaran dalam kelas
- `addMataPelajaranToKelas($data)` - Tambah mata pelajaran ke kelas
- `removeMataPelajaranFromKelas($kelas_id, $mata_pelajaran_id)` - Hapus mata pelajaran dari kelas
- `getAvailableMataPelajaran($kelas_id)` - Ambil mata pelajaran yang belum ada di kelas
- `getSiswaInKelas($kelas_id)` - Ambil siswa dalam kelas
- `addSiswaToKelas($siswa_id, $kelas_id)` - Tambah siswa ke kelas
- `removeSiswaFromKelas($siswa_id, $kelas_id)` - Hapus siswa dari kelas
- `getAvailableSiswa($kelas_id)` - Ambil siswa yang belum terdaftar di kelas

#### b. MataPelajaranModel.php
**File:** `app/models/MataPelajaranModel.php`

**Method Utama:**
- `getAllMataPelajaran()` - Ambil semua mata pelajaran
- `getMataPelajaranById($id)` - Ambil detail mata pelajaran
- `createMataPelajaran($data)` - Buat mata pelajaran baru
- `updateMataPelajaran($data)` - Update mata pelajaran
- `deleteMataPelajaran($id)` - Hapus mata pelajaran
- `getKelasUsingMataPelajaran($mata_pelajaran_id)` - Ambil kelas yang menggunakan mata pelajaran
- `getTotalKelasUsingMataPelajaran($mata_pelajaran_id)` - Hitung berapa kelas menggunakan mata pelajaran

### 3. Controller

#### AdminController.php
**File:** `app/controllers/AdminController.php`

**Method Baru:**
- `kelas()` - Halaman manajemen kelas
- `createKelas()` - Create kelas baru
- `updateKelas()` - Update kelas
- `deleteKelas()` - Hapus kelas
- `getMataPelajaranDalamKelas()` - API: Get mata pelajaran dalam kelas (JSON)
- `getMataPelajaranTersedia()` - API: Get mata pelajaran tersedia (JSON)
- `addMataPelajaranToKelas()` - API: Tambah mata pelajaran ke kelas
- `removeMataPelajaranFromKelas()` - API: Hapus mata pelajaran dari kelas
- `getSiswaDalamKelas()` - API: Get siswa dalam kelas (JSON)
- `getSiswaTersedia()` - API: Get siswa tersedia (JSON)
- `addSiswaToKelas()` - API: Tambah siswa ke kelas
- `removeSiswaFromKelas()` - API: Hapus siswa dari kelas

### 4. View

#### admin/kelas.php
**File:** `app/views/admin/kelas.php`

**Fitur Baru:**
- **Tabel Kelas** - Menampilkan semua kelas dengan info jumlah siswa dan mata pelajaran
- **Modal Tambah/Edit Kelas** - Form untuk menambah/edit kelas
- **Modal Kelola Mata Pelajaran** - Interface untuk menambah/hapus mata pelajaran dari kelas
- **Modal Kelola Siswa** - Interface untuk menambah/hapus siswa dari kelas
- **Real-time Updates** - Menggunakan AJAX untuk update tanpa reload halaman

## 🚀 Cara Menggunakan Sistem Baru

### 1. Menjalankan Migration
```bash
# Backup database terlebih dahulu!
# Export database Anda sebelum melanjutkan

# Jalankan migration SQL di phpMyAdmin atau MySQL CLI
mysql -u username -p database_name < db/migrations/migration_restructure_kelas_mata_pelajaran.sql
```

### 2. Workflow Admin

#### a. Membuat Kelas
1. Buka halaman "Manajemen Kelas" (menu Admin)
2. Klik tombol "Tambah Kelas"
3. Isi nama kelas (contoh: "X Akuntansi 1") dan tahun ajaran (contoh: "2025/2026")
4. Klik "Simpan"

#### b. Menambah Mata Pelajaran ke Kelas
1. Pada tabel kelas, klik tombol "Mata Pelajaran" pada kelas yang diinginkan
2. Di modal yang muncul, pilih mata pelajaran dari dropdown
3. (Opsional) Pilih guru pengampu
4. (Opsional) Isi jadwal (contoh: "Senin, 07:30-09:00")
5. Klik "Tambah Mata Pelajaran"

#### c. Menambah Siswa ke Kelas
1. Pada tabel kelas, klik tombol "Siswa" pada kelas yang diinginkan
2. Di modal yang muncul, pilih siswa dari dropdown
3. Klik "Tambah Siswa"

### 3. Contoh Data

#### Membuat Kelas
```
Nama Kelas: X Akuntansi 1
Tahun Ajaran: 2025/2026
```

#### Menambah Mata Pelajaran
```
Mata Pelajaran: Matematika
Guru Pengampu: Bu Siti Aminah
Jadwal: Senin, 07:30-09:00

Mata Pelajaran: Bahasa Indonesia
Guru Pengampu: Pak Budi Santoso
Jadwal: Selasa, 08:00-09:30
```

#### Menambah Siswa
```
- Ahmad Rizki
- Budi Santoso
- Citra Dewi
```

## ⚠️ Catatan Penting

### 1. Kompatibilitas dengan Presensi
- Tabel `presensi_kelas`, `presensi_sesi`, dan `laporan_kemajuan` masih menggunakan `kelas_id`
- Di migration sudah ditambahkan kolom `kelas_mata_pelajaran_id` untuk referensi ke junction table
- **TODO:** Update logic presensi untuk menggunakan struktur baru

### 2. Data Lama
- Migration ini **tidak menghapus** tabel lama secara otomatis
- Jika Anda punya data di tabel `kelas` lama (yang sudah direname jadi `mata_pelajaran`), Anda perlu migrasi manual
- Backup data terlebih dahulu!

### 3. Relasi Siswa
- Siswa terdaftar di **kelas**, bukan di mata pelajaran individual
- Siswa yang terdaftar di kelas otomatis mengikuti **semua** mata pelajaran di kelas tersebut

## 📝 TODO / Pekerjaan Selanjutnya

1. **Update GuruController dan View Guru**
   - Update method `getKelasByGuru()` untuk mendapatkan mata pelajaran yang diampu di kelas tertentu
   - Update view `guru/kelas.php` untuk menampilkan kelas dan mata pelajaran yang diampu

2. **Update SiswaController dan View Siswa**
   - Update untuk menampilkan kelas yang diikuti siswa
   - Update view `siswa/dashboard.php` dan `siswa/presensi.php`

3. **Update PresensiModel dan Logic Presensi**
   - Update untuk menggunakan `kelas_mata_pelajaran_id` 
   - Update query untuk mendapatkan sesi presensi berdasarkan kelas dan mata pelajaran

4. **Update LaporanModel**
   - Update query laporan untuk menggunakan struktur baru
   - Update laporan per kelas dan per mata pelajaran

5. **Testing Menyeluruh**
   - Test CRUD kelas
   - Test penambahan/penghapusan mata pelajaran dari kelas
   - Test penambahan/penghapusan siswa dari kelas
   - Test presensi dengan struktur baru
   - Test laporan dengan struktur baru

## 🔍 Troubleshooting

### Error: Table doesn't exist
- Pastikan migration sudah dijalankan dengan benar
- Check apakah tabel sudah dibuat: `SHOW TABLES LIKE 'kelas';`

### Error: Foreign key constraint fails
- Pastikan tabel parent sudah dibuat sebelum tabel child
- Check foreign key constraints: `SHOW CREATE TABLE kelas_mata_pelajaran;`

### Data tidak muncul di view
- Check console browser untuk error JavaScript
- Check network tab untuk melihat response API
- Pastikan controller method sudah ter-route dengan benar di `index.php`

## 📞 Support

Jika ada pertanyaan atau menemukan bug, silakan dokumentasikan di:
- File ini (tambahkan di section Issues)
- Commit message saat push
- Team chat/Slack

---

**Last Updated:** 4 Maret 2026  
**Version:** 2.0.0
