-- Migration: Add Kelas Structure
-- Tanggal: 2026-03-04
-- Deskripsi: Menambahkan tabel kelas dan kelas_mata_pelajaran, 
-- serta memodifikasi struktur mata_pelajaran

-- 1. Buat tabel kelas untuk menampung data kelas
CREATE TABLE IF NOT EXISTS `kelas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(100) NOT NULL,
  `tahun_ajaran` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Backup data mata_pelajaran lama (optional, for safety)
CREATE TABLE IF NOT EXISTS `mata_pelajaran_backup` AS SELECT * FROM `mata_pelajaran`;

-- 3. Buat tabel mata_pelajaran baru dengan struktur yang dimodifikasi
-- Hapus kolom tahun_ajaran karena akan ada di tabel kelas
CREATE TABLE IF NOT EXISTS `mata_pelajaran_new` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_mata_pelajaran` varchar(100) NOT NULL,
  `guru_pengampu` int(11) DEFAULT NULL,
  `jadwal` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `guru_pengampu` (`guru_pengampu`),
  CONSTRAINT `fk_mata_pelajaran_guru` FOREIGN KEY (`guru_pengampu`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Migrate data dari mata_pelajaran lama ke baru (hanya data unik mata pelajaran)
-- Karena struktur berubah, kita ambil mata pelajaran unik berdasarkan nama dan guru
INSERT INTO `mata_pelajaran_new` (`id`, `nama_mata_pelajaran`, `guru_pengampu`, `jadwal`)
SELECT `id`, `nama_mata_pelajaran`, `guru_pengampu`, `jadwal` FROM `mata_pelajaran`;

-- 5. Buat tabel kelas_mata_pelajaran untuk mapping kelas dengan mata pelajaran
CREATE TABLE IF NOT EXISTS `kelas_mata_pelajaran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kelas_id` int(11) NOT NULL,
  `mata_pelajaran_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_kelas_mapel` (`kelas_id`, `mata_pelajaran_id`),
  KEY `kelas_id` (`kelas_id`),
  KEY `mata_pelajaran_id` (`mata_pelajaran_id`),
  CONSTRAINT `fk_kelas_mapel_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kelas_mapel_mapel` FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajaran_new` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. CATATAN: Siswa tetap dikelola PER MATA PELAJARAN
-- Tabel siswa_mata_pelajaran yang lama TETAP DIGUNAKAN
-- Siswa di-assign ke mata_pelajaran (yang sudah ada dalam konteks kelas via kelas_mata_pelajaran)
-- TIDAK PERLU tabel siswa_kelas karena siswa tidak otomatis masuk semua mapel

-- 7. Drop tabel mata_pelajaran lama dan rename yang baru
DROP TABLE IF EXISTS `mata_pelajaran`;
RENAME TABLE `mata_pelajaran_new` TO `mata_pelajaran`;

-- 9. Update presensi_sesi untuk menggunakan kelas_mata_pelajaran_id
-- Backup dulu
CREATE TABLE IF NOT EXISTS `presensi_sesi_backup` AS SELECT * FROM `presensi_sesi`;

-- Tambah kolom baru
ALTER TABLE `presensi_sesi` 
ADD COLUMN `kelas_mata_pelajaran_id` int(11) DEFAULT NULL AFTER `kelas_id`,
ADD KEY `kelas_mata_pelajaran_id` (`kelas_mata_pelajaran_id`);

-- Note: kelas_id akan tetap ada untuk backward compatibility sementara
-- Nanti bisa dihapus setelah semua data ter-migrate

-- 10. Update presensi untuk menggunakan kelas_mata_pelajaran_id
ALTER TABLE `presensi` 
ADD COLUMN `kelas_mata_pelajaran_id` int(11) DEFAULT NULL AFTER `kelas_id`,
ADD KEY `kelas_mata_pelajaran_id` (`kelas_mata_pelajaran_id`);

-- 11. Update laporan untuk menggunakan kelas_mata_pelajaran_id  
ALTER TABLE `laporan` 
ADD COLUMN `kelas_mata_pelajaran_id` int(11) DEFAULT NULL AFTER `kelas_id`,
ADD KEY `kelas_mata_pelajaran_id` (`kelas_mata_pelajaran_id`);

-- ============================================
-- INSTRUKSI PENGGUNAAN:
-- ============================================
-- 1. Jalankan migration ini di database
-- 2. Admin perlu membuat kelas-kelas terlebih dahulu
-- 3. Admin menambahkan mata pelajaran ke masing-masing kelas
-- 4. Admin memasukkan siswa ke dalam kelas
-- 5. Siswa otomatis terdaftar di semua mata pelajaran dalam kelasnya
-- ============================================
