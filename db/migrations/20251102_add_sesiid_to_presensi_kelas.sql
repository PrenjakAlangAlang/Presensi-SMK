-- Migration: add presensi_sesi_id to presensi_kelas
-- Run this SQL in your database if presensi_sesi_id column is not present
ALTER TABLE `presensi_kelas`
ADD COLUMN `presensi_sesi_id` int DEFAULT NULL,
ADD KEY `presensi_sesi_id` (`presensi_sesi_id`);

-- Optionally add foreign key if presensi_sesi table exists
-- ALTER TABLE `presensi_kelas`
-- ADD CONSTRAINT `presensi_kelas_ibfk_3` FOREIGN KEY (`presensi_sesi_id`) REFERENCES `presensi_sesi` (`id`) ON DELETE SET NULL;
