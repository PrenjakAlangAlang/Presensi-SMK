-- Migration: Restructure kelas dan mata_pelajaran
-- Tanggal: 2026-03-04
-- Deskripsi: Membuat struktur baru dimana kelas dapat menampung banyak mata pelajaran

-- BACKUP DATABASE TERLEBIH DAHULU SEBELUM MENJALANKAN MIGRATION INI!

-- Step 1: Rollback migration sebelumnya jika sudah dijalankan
-- Kembalikan nama tabel mata_pelajaran ke kelas jika perlu
-- DROP TABLE IF EXISTS kelas;
-- RENAME TABLE mata_pelajaran TO kelas_old;
-- RENAME TABLE siswa_mata_pelajaran TO siswa_kelas_old;

-- Step 2: Buat tabel kelas baru (untuk menampung kelas seperti "X Akuntansi 1", "XI Perhotelan")
CREATE TABLE IF NOT EXISTS `kelas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_kelas` VARCHAR(100) NOT NULL COMMENT 'Contoh: X Akuntansi 1, XI Perhotelan',
  `tahun_ajaran` VARCHAR(20) NOT NULL COMMENT 'Contoh: 2025/2026',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Step 3: Buat tabel mata_pelajaran baru (untuk mata pelajaran seperti "Matematika", "Bahasa Indonesia")
CREATE TABLE IF NOT EXISTS `mata_pelajaran` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_mata_pelajaran` VARCHAR(100) NOT NULL COMMENT 'Contoh: Matematika, Bahasa Indonesia, PKN',
  `kode_mata_pelajaran` VARCHAR(20) DEFAULT NULL COMMENT 'Kode mata pelajaran',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Step 4: Buat tabel relasi kelas_mata_pelajaran (many-to-many)
CREATE TABLE IF NOT EXISTS `kelas_mata_pelajaran` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `kelas_id` INT(11) NOT NULL,
  `mata_pelajaran_id` INT(11) NOT NULL,
  `guru_pengampu` INT(11) DEFAULT NULL COMMENT 'ID guru yang mengampu mata pelajaran ini di kelas ini',
  `jadwal` VARCHAR(50) DEFAULT NULL COMMENT 'Contoh: Senin, 07:30-09:00',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_kelas_mapel` (`kelas_id`, `mata_pelajaran_id`),
  KEY `fk_kmp_kelas` (`kelas_id`),
  KEY `fk_kmp_mapel` (`mata_pelajaran_id`),
  KEY `fk_kmp_guru` (`guru_pengampu`),
  CONSTRAINT `fk_kmp_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kmp_mapel` FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kmp_guru` FOREIGN KEY (`guru_pengampu`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Step 5: Buat tabel siswa_kelas (siswa yang terdaftar di kelas tertentu)
CREATE TABLE IF NOT EXISTS `siswa_kelas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `siswa_id` INT(11) NOT NULL,
  `kelas_id` INT(11) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_siswa_kelas` (`siswa_id`, `kelas_id`),
  KEY `fk_sk_siswa` (`siswa_id`),
  KEY `fk_sk_kelas` (`kelas_id`),
  CONSTRAINT `fk_sk_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sk_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Step 6: Update tabel presensi_kelas untuk menggunakan kelas_mata_pelajaran_id
-- Tambahkan kolom baru untuk referensi ke tabel kelas_mata_pelajaran
ALTER TABLE `presensi_kelas` 
  ADD COLUMN `kelas_mata_pelajaran_id` INT(11) DEFAULT NULL AFTER `kelas_id`,
  ADD KEY `fk_pk_kmp` (`kelas_mata_pelajaran_id`);

-- Step 7: Update tabel presensi_sesi untuk menggunakan kelas_mata_pelajaran_id
ALTER TABLE `presensi_sesi`
  ADD COLUMN `kelas_mata_pelajaran_id` INT(11) DEFAULT NULL AFTER `kelas_id`,
  ADD KEY `fk_ps_kmp` (`kelas_mata_pelajaran_id`);

-- Step 8: Update tabel laporan_kemajuan untuk menggunakan kelas_mata_pelajaran_id  
ALTER TABLE `laporan_kemajuan`
  ADD COLUMN `kelas_mata_pelajaran_id` INT(11) DEFAULT NULL AFTER `kelas_id`,
  ADD KEY `fk_lk_kmp` (`kelas_mata_pelajaran_id`);

-- Step 9: Contoh data dummy untuk kelas
INSERT INTO `kelas` (`nama_kelas`, `tahun_ajaran`) VALUES
('X Akuntansi 1', '2025/2026'),
('X Akuntansi 2', '2025/2026'),
('XI Perhotelan', '2025/2026'),
('XI RPL 1', '2025/2026'),
('XII DPB', '2025/2026');

-- Step 10: Contoh data dummy untuk mata_pelajaran
INSERT INTO `mata_pelajaran` (`nama_mata_pelajaran`, `kode_mata_pelajaran`) VALUES
('Matematika', 'MTK'),
('Bahasa Indonesia', 'BIN'),
('Bahasa Inggris', 'BING'),
('Pendidikan Agama Islam', 'PAI'),
('Pendidikan Kewarganegaraan', 'PKN'),
('Sejarah Indonesia', 'SEJ'),
('Kimia', 'KIM'),
('Fisika', 'FIS'),
('Ekonomi', 'EKO'),
('Akuntansi Dasar', 'AKD');

-- CATATAN PENTING:
-- 1. Backup database sebelum menjalankan migration ini
-- 2. Setelah migration, Anda perlu:
--    a. Migrasi data dari tabel lama (jika ada) ke struktur baru
--    b. Update semua kode PHP yang mereferensi struktur lama
--    c. Update foreign key constraints di tabel presensi_kelas, presensi_sesi, dan laporan_kemajuan
-- 3. Untuk rollback, restore dari backup database
-- 4. Tabel lama (jika ada) tidak dihapus untuk keamanan, hapus manual setelah verifikasi

-- Verifikasi struktur tabel
SHOW CREATE TABLE kelas;
SHOW CREATE TABLE mata_pelajaran;
SHOW CREATE TABLE kelas_mata_pelajaran;
SHOW CREATE TABLE siswa_kelas;
