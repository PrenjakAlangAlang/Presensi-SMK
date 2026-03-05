-- Migration: Rename tabel kelas menjadi mata_pelajaran
-- Tanggal: 2026-03-03
-- Deskripsi: Mengubah sistem dari manajemen kelas menjadi manajemen mata pelajaran

-- Step 1: Rename tabel kelas menjadi mata_pelajaran
RENAME TABLE kelas TO mata_pelajaran;

-- Step 2: Rename kolom dalam tabel mata_pelajaran
ALTER TABLE mata_pelajaran 
    CHANGE COLUMN nama_kelas nama_mata_pelajaran VARCHAR(100) NOT NULL,
    CHANGE COLUMN wali_kelas guru_pengampu INT(11) DEFAULT NULL;

-- Step 3: Rename tabel siswa_kelas menjadi siswa_mata_pelajaran
RENAME TABLE siswa_kelas TO siswa_mata_pelajaran;

-- Step 4: Rename kolom foreign key dalam tabel siswa_mata_pelajaran
ALTER TABLE siswa_mata_pelajaran
    CHANGE COLUMN kelas_id mata_pelajaran_id INT(11) NOT NULL;

-- Step 5: Update references dalam tabel presensi_kelas (tetap pakai nama 'kelas_id' karena masih merujuk ke konsep kelas/sesi)
-- Catatan: Tabel presensi_kelas tetap menggunakan nama kelas_id karena merujuk ke sesi mata pelajaran
-- Jika ingin konsisten, uncomment baris berikut:
-- ALTER TABLE presensi_kelas
--     CHANGE COLUMN kelas_id mata_pelajaran_id INT(11) NOT NULL;

-- Step 6: Update references dalam tabel presensi_sesi
-- Catatan: Tabel presensi_sesi tetap menggunakan nama kelas_id
-- Jika ingin konsisten, uncomment baris berikut:
-- ALTER TABLE presensi_sesi
--     CHANGE COLUMN kelas_id mata_pelajaran_id INT(11) NOT NULL;

-- Step 7: Update references dalam tabel laporan_kemajuan
-- Catatan: Tabel laporan_kemajuan tetap menggunakan nama kelas_id
-- Jika ingin konsisten, uncomment baris berikut:
-- ALTER TABLE laporan_kemajuan
--     CHANGE COLUMN kelas_id mata_pelajaran_id INT(11) NOT NULL;

-- Verifikasi: Tampilkan struktur tabel baru
SHOW CREATE TABLE mata_pelajaran;
SHOW CREATE TABLE siswa_mata_pelajaran;

-- Catatan Penting:
-- 1. Backup database sebelum menjalankan migration ini
-- 2. Parameter kelas_id di tabel lain (presensi_kelas, presensi_sesi, laporan_kemajuan) 
--    masih menggunakan nama 'kelas_id' untuk kompatibilitas
-- 3. Jika ingin mengubah semua referensi, uncomment baris-baris yang sesuai di atas
-- 4. Setelah migration, update semua kode PHP yang mereferensi tabel dan kolom lama
