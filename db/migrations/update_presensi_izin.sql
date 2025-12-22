-- Migration: Update sistem presensi untuk integrasi izin
-- Tanggal: 2025-12-22
-- Deskripsi: Menghapus tabel izin_siswa dan menambahkan kolom alasan & foto_bukti ke tabel presensi

-- 1. Tambahkan kolom alasan dan foto_bukti ke tabel presensi_sekolah
ALTER TABLE `presensi_sekolah` 
ADD COLUMN `alasan` TEXT NULL AFTER `jenis`,
ADD COLUMN `foto_bukti` VARCHAR(255) NULL AFTER `alasan`;

-- 2. Tambahkan kolom alasan dan foto_bukti ke tabel presensi_kelas
ALTER TABLE `presensi_kelas` 
ADD COLUMN `alasan` TEXT NULL AFTER `jenis`,
ADD COLUMN `foto_bukti` VARCHAR(255) NULL AFTER `alasan`;

-- 3. Hapus tabel izin_siswa (tidak diperlukan lagi)
DROP TABLE IF EXISTS `izin_siswa`;

-- 4. Update struktur lengkap presensi_sekolah (untuk referensi)
-- CREATE TABLE `presensi_sekolah` (
--   `id` int NOT NULL,
--   `presensi_sekolah_sesi_id` int DEFAULT NULL,
--   `user_id` int NOT NULL,
--   `latitude` double NOT NULL,
--   `longitude` double NOT NULL,
--   `jarak` double NOT NULL,
--   `status` enum('valid','invalid') DEFAULT 'invalid',
--   `waktu` datetime DEFAULT CURRENT_TIMESTAMP,
--   `jenis` enum('hadir','izin','sakit','alpha') DEFAULT 'hadir',
--   `alasan` TEXT NULL,
--   `foto_bukti` VARCHAR(255) NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 5. Update struktur lengkap presensi_kelas (untuk referensi)
-- CREATE TABLE `presensi_kelas` (
--   `id` int NOT NULL,
--   `user_id` int NOT NULL,
--   `kelas_id` int NOT NULL,
--   `latitude` double NOT NULL,
--   `longitude` double NOT NULL,
--   `jarak` double NOT NULL,
--   `status` enum('valid','invalid') DEFAULT 'invalid',
--   `waktu` datetime DEFAULT CURRENT_TIMESTAMP,
--   `presensi_sesi_id` int DEFAULT NULL,
--   `jenis` enum('hadir','izin','sakit','alpha') DEFAULT 'hadir',
--   `alasan` TEXT NULL,
--   `foto_bukti` VARCHAR(255) NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
