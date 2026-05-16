-- Migration: rename tabel kelas_jadwal menjadi kelas.
-- Kolom relasi jadwal_mata_pelajaran.kelas_jadwal_id tetap dipertahankan agar perubahan kode tetap kecil.

DROP PROCEDURE IF EXISTS rename_kelas_jadwal_to_kelas;

DELIMITER $$

CREATE PROCEDURE rename_kelas_jadwal_to_kelas()
BEGIN
  IF EXISTS (
    SELECT 1
    FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'kelas_jadwal'
  ) AND NOT EXISTS (
    SELECT 1
    FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'kelas'
  ) THEN
    RENAME TABLE `kelas_jadwal` TO `kelas`;
  END IF;
END$$

DELIMITER ;

CALL rename_kelas_jadwal_to_kelas();

DROP PROCEDURE IF EXISTS rename_kelas_jadwal_to_kelas;
