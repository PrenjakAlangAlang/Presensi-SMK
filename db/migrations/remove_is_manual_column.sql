-- Migration: Remove is_manual column from presensi_sekolah_sesi table
-- Date: 2026-01-03
-- Description: Menghapus kolom is_manual yang tidak diperlukan lagi

ALTER TABLE `presensi_sekolah_sesi` DROP COLUMN `is_manual`;
