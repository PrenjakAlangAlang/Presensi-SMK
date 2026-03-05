# 🚀 CARA PERBAIKAN & SETUP - Sistem Kelas dan Mata Pelajaran

## ⚠️ PENTING: Baca Ini Dulu!

Database Anda saat ini menggunakan struktur LAMA dimana:
- Tabel `mata_pelajaran` mencampur nama mapel dan kelas (contoh: "PKN-X Akuntansi 1")
- Siswa terdaftar langsung ke mata pelajaran via `siswa_mata_pelajaran`

Sistem BARU yang saya buat:
- Tabel `kelas` terpisah (contoh: "X Akuntansi 1")
- Tabel `mata_pelajaran` hanya nama mapel (contoh: "PKN")
- Junction table `kelas_mata_pelajaran` menghubungkan keduanya
- Siswa terdaftar ke `kelas` via `siswa_kelas`

## 📋 Langkah-Langkah Setup

### Step 1: BACKUP DATABASE ⚠️
```bash
# WAJIB! Export database Anda terlebih dahulu
# Di phpMyAdmin: Export > Go
# Atau via command line:
mysqldump -u root -p presensi_smk > backup_presensi_$(date +%Y%m%d_%H%M%S).sql
```

### Step 2: Jalankan Migration SQL
```bash
# Ada 2 file migration, pilih salah satu:

# OPSI A: Jika database masih kosong atau fresh install
# Gunakan: db/migrations/migration_restructure_kelas_mata_pelajaran.sql

# OPSI B: Jika database sudah ada data (RECOMMENDED untuk Anda)
# Gunakan: db/migrations/migration_kelas_mapel_dari_existing.sql
```

**Cara menjalankan migration:**

1. Buka phpMyAdmin
2. Pilih database `presensi_smk`
3. Klik tab "SQL"
4. Copy-paste isi file migration
5. Klik "Go"
6. Tunggu sampai selesai
7. Cek hasil di tab "Structure"

### Step 3: Verifikasi Struktur Database

Pastikan tabel-tabel ini sudah ada:
```sql
SHOW TABLES LIKE 'kelas';
SHOW TABLES LIKE 'mata_pelajaran';
SHOW TABLES LIKE 'kelas_mata_pelajaran';
SHOW TABLES LIKE 'siswa_kelas';
```

Cek jumlah data:
```sql
SELECT COUNT(*) FROM kelas;
SELECT COUNT(*) FROM mata_pelajaran;
SELECT COUNT(*) FROM kelas_mata_pelajaran;
SELECT COUNT(*) FROM siswa_kelas;
```

### Step 4: Test Sistem

1. **Login sebagai Admin**
   - Username: admin
   - Password: (password Anda)

2. **Buka Menu "Manajemen Kelas"**
   - Url: `http://localhost/Presensi-SMK/index.php?action=admin_kelas`

3. **Test CRUD Kelas**
   - Klik "Tambah Kelas"
   - Isi: Nama Kelas = "X RPL 1", Tahun Ajaran = "2025/2026"
   - Klik Simpan
   - Pastikan kelas muncul di tabel

4. **Test Tambah Mata Pelajaran ke Kelas**
   - Klik tombol "Mata Pelajaran" pada kelas yang baru dibuat
   - Pilih mata pelajaran dari dropdown
   - Pilih guru pengampu (opsional)
   - Isi jadwal (opsional)
   - Klik "Tambah Mata Pelajaran"
   - Pastikan mata pelajaran muncul di list

5. **Test Tambah Siswa ke Kelas**
   - Klik tombol "Siswa" pada kelas
   - Pilih siswa dari dropdown
   - Klik "Tambah Siswa"
   - Pastikan siswa muncul di list

## 🔧 Troubleshooting

### Error: "Table 'kelas' doesn't exist"
**Penyebab:** Migration belum dijalankan
**Solusi:** Jalankan file migration sesuai Step 2

### Error: "Call to undefined method getMataPelajaranInKelas()"
**Penyebab:** File KelasModel.php tidak ter-update
**Solusi:** Pastikan file sudah ter-save dan reload page

### Error: "Access denied for user"
**Penyebab:** Hak akses database tidak cukup
**Solusi:** Pastikan user MySQL Anda punya privilege CREATE, ALTER, DROP

### Modal tidak muncul / JavaScript error
**Penyebab:** JavaScript error di browser
**Solusi:** 
1. Buka Console Browser (F12)
2. Cek error yang muncul
3. Clear browser cache (Ctrl+Shift+Delete)
4. Reload page (Ctrl+F5)

### Data lama hilang
**Penyebab:** Migration tidak berhasil migrasi data
**Solusi:**
1. Cek tabel `mata_pelajaran_old` dan `siswa_mata_pelajaran_old`
2. Data lama ada di sana (backup otomatis)
3. Jika perlu, rollback dengan script di bagian bawah migration file

### Pattern nama tidak sesuai
**Contoh data Anda:** "PKN-X Akuntansi 1"
**Pattern yang diharapkan:** `NamaMapel-NamaKelas`

Jika data Anda menggunakan pattern berbeda:
1. Edit migration file
2. Ubah query SUBSTRING_INDEX di STEP 6, 7, 8, 9
3. Adjust sesuai pattern data Anda

## 📊 Struktur Data Baru

### Contoh Data

**Tabel kelas:**
```
| id | nama_kelas      | tahun_ajaran |
|----|----------------|--------------|
| 1  | X Akuntansi 1  | 2025/2026    |
| 2  | XI Perhotelan  | 2025/2026    |
```

**Tabel mata_pelajaran:**
```
| id | nama_mata_pelajaran | kode_mata_pelajaran |
|----|-------------------|-------------------|
| 1  | PKN              | PKN              |
| 2  | Bahasa Indonesia | BIN              |
```

**Tabel kelas_mata_pelajaran:**
```
| id | kelas_id | mata_pelajaran_id | guru_pengampu | jadwal              |
|----|---------|------------------|--------------|---------------------|
| 1  | 1       | 1                | 2            | Senin, 07:30-09:00  |
| 2  | 1       | 2                | 6            | Selasa, 08:00-09:30 |
```

**Tabel siswa_kelas:**
```
| id | siswa_id | kelas_id |
|----|---------|---------|
| 1  | 3       | 1       |
| 2  | 5       | 1       |
```

## 🔄 Rollback (Jika Ada Masalah)

Jika terjadi masalah dan ingin kembali ke struktur lama:

```sql
-- 1. Hapus tabel baru
DROP TABLE IF EXISTS siswa_kelas;
DROP TABLE IF EXISTS kelas_mata_pelajaran;
DROP TABLE IF EXISTS mata_pelajaran;
DROP TABLE IF EXISTS kelas;

-- 2. Kembalikan tabel lama
RENAME TABLE mata_pelajaran_old TO mata_pelajaran;
RENAME TABLE siswa_mata_pelajaran_old TO siswa_mata_pelajaran;

-- 3. Restore backup jika diperlukan
-- mysql -u root -p presensi_smk < backup_file.sql
```

## 📝 Checklist Setelah Migration

- [ ] Backup database berhasil
- [ ] Migration SQL berjalan tanpa error
- [ ] Tabel baru sudah ada (kelas, mata_pelajaran, kelas_mata_pelajaran, siswa_kelas)
- [ ] Data berhasil dimigrasikan (cek COUNT di setiap tabel)
- [ ] Login admin berhasil
- [ ] Halaman "Manajemen Kelas" bisa dibuka
- [ ] Bisa tambah kelas baru
- [ ] Bisa tambah mata pelajaran ke kelas
- [ ] Bisa tambah siswa ke kelas
- [ ] Bisa edit kelas
- [ ] Bisa hapus kelas
- [ ] Tidak ada JavaScript error di console

## 🎯 Fitur yang Sudah Dibuat

### ✅ Selesai
- [x] Migration SQL dengan auto-migrasi data
- [x] KelasModel (CRUD kelas + manage mata pelajaran & siswa)
- [x] MataPelajaranModel (CRUD mata pelajaran)
- [x] AdminController methods baru (12 methods)
- [x] View admin/kelas.php (UI lengkap dengan modal)
- [x] Routing di index.php
- [x] Dokumentasi lengkap

### ⏳ Belum Selesai (TODO Next)
- [ ] Update GuruController (untuk melihat kelas yang diampu)
- [ ] Update view guru/kelas.php
- [ ] Update SiswaController (untuk melihat kelas siswa)
- [ ] Update view siswa/dashboard.php & presensi.php
- [ ] Update PresensiModel (gunakan kelas_mata_pelajaran_id)
- [ ] Update LaporanModel (query berdasarkan struktur baru)

## 📞 Support

Jika masih ada masalah:

1. **Cek Console Browser** (F12 > Console) untuk error JavaScript
2. **Cek PHP Error Log** (`/laragon/mysql/log/error.log`)
3. **Test Query di phpMyAdmin** untuk debugging database
4. **Screenshot error** dan catat langkah reproduksi masalah

## 🎓 Video Tutorial

Coming soon: Akan saya buat video tutorial lengkap cara:
- Jalankan migration
- Test CRUD kelas
- Kelola mata pelajaran
- Kelola siswa

---

**Dibuat:** 4 Maret 2026  
**Developer:** GitHub Copilot  
**Version:** 2.1.0
