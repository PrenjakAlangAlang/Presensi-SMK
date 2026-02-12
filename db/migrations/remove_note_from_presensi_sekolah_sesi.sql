-- Migration: Remove note column from presensi_sekolah_sesi table
-- Date: 2026-02-12

ALTER TABLE `presensi_sekolah_sesi` DROP COLUMN `note`;
