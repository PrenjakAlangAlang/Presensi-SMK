-- Migration: add presensi_sekolah_sesi_id to presensi_sekolah and FK constraint
-- Run this SQL in your database to link presensi_sekolah to presensi_sekolah_sesi
ALTER TABLE `presensi_sekolah`
  ADD COLUMN `presensi_sekolah_sesi_id` int DEFAULT NULL AFTER `id`;

-- add index and foreign key constraint (set null on delete to preserve historical presensi)
ALTER TABLE `presensi_sekolah`
  ADD KEY `fk_presensi_sekolah_sesi` (`presensi_sekolah_sesi_id`),
  ADD CONSTRAINT `fk_presensi_sekolah_sesi` FOREIGN KEY (`presensi_sekolah_sesi_id`) REFERENCES `presensi_sekolah_sesi`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
