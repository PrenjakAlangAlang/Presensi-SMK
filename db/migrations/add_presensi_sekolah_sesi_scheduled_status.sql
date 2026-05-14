-- Migration: kembalikan status sesi presensi sekolah ke mode manual.
-- Backup database terlebih dahulu sebelum menjalankan migration ini.

UPDATE `presensi_sekolah_sesi`
SET `status` = 'open'
WHERE `status` = 'scheduled';

ALTER TABLE `presensi_sekolah_sesi`
  MODIFY `status` enum('open','closed') NOT NULL DEFAULT 'open';
