-- Migration: tambah data akademik ringan di buku_induk.
-- Kolom ini dipakai untuk filter siswa saat memasukkan peserta ke mata pelajaran.

DROP PROCEDURE IF EXISTS add_column_if_not_exists;

DELIMITER $$

CREATE PROCEDURE add_column_if_not_exists(IN p_table VARCHAR(64), IN p_column VARCHAR(64), IN p_definition TEXT)
BEGIN
  IF NOT EXISTS (
    SELECT 1
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = p_table
      AND COLUMN_NAME = p_column
  ) THEN
    SET @sql = CONCAT('ALTER TABLE `', p_table, '` ADD COLUMN ', p_definition);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END$$

DELIMITER ;

CALL add_column_if_not_exists('buku_induk', 'kelas', '`kelas` varchar(50) DEFAULT NULL AFTER `nisn`');
CALL add_column_if_not_exists('buku_induk', 'jurusan', '`jurusan` varchar(100) DEFAULT NULL AFTER `kelas`');
CALL add_column_if_not_exists('buku_induk', 'tanggal_diterima', '`tanggal_diterima` date DEFAULT NULL AFTER `jurusan`');

DROP PROCEDURE IF EXISTS add_column_if_not_exists;
