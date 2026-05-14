-- Migration: ganti dokumen_pdf dan tabel buku_induk_dokumen menjadi kolom dokumen tetap.
-- Kolom baru:
-- - dokumen_ijasah
-- - dokumen_pas_foto
-- - dokumen_akta_kelahiran
-- - dokumen_kk

DROP PROCEDURE IF EXISTS add_column_if_not_exists;
DROP PROCEDURE IF EXISTS drop_column_if_exists;
DROP PROCEDURE IF EXISTS migrate_old_dokumen_pdf_if_exists;

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

CREATE PROCEDURE drop_column_if_exists(IN p_table VARCHAR(64), IN p_column VARCHAR(64))
BEGIN
  IF EXISTS (
    SELECT 1
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = p_table
      AND COLUMN_NAME = p_column
  ) THEN
    SET @sql = CONCAT('ALTER TABLE `', p_table, '` DROP COLUMN `', p_column, '`');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END$$

CREATE PROCEDURE migrate_old_dokumen_pdf_if_exists()
BEGIN
  IF EXISTS (
    SELECT 1
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'buku_induk'
      AND COLUMN_NAME = 'dokumen_pdf'
  ) THEN
    SET @sql = 'UPDATE `buku_induk`
                SET `dokumen_ijasah` = `dokumen_pdf`
                WHERE (`dokumen_ijasah` IS NULL OR `dokumen_ijasah` = '''')
                  AND `dokumen_pdf` IS NOT NULL
                  AND `dokumen_pdf` <> ''''';
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END$$

DELIMITER ;

CALL add_column_if_not_exists('buku_induk', 'dokumen_ijasah', '`dokumen_ijasah` varchar(255) DEFAULT NULL AFTER `email_ortu`');
CALL add_column_if_not_exists('buku_induk', 'dokumen_pas_foto', '`dokumen_pas_foto` varchar(255) DEFAULT NULL AFTER `dokumen_ijasah`');
CALL add_column_if_not_exists('buku_induk', 'dokumen_akta_kelahiran', '`dokumen_akta_kelahiran` varchar(255) DEFAULT NULL AFTER `dokumen_pas_foto`');
CALL add_column_if_not_exists('buku_induk', 'dokumen_kk', '`dokumen_kk` varchar(255) DEFAULT NULL AFTER `dokumen_akta_kelahiran`');

-- Pindahkan dokumen lama ke dokumen_ijasah agar file lama tidak langsung hilang dari tampilan.
CALL migrate_old_dokumen_pdf_if_exists();

CALL drop_column_if_exists('buku_induk', 'dokumen_pdf');

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `buku_induk_dokumen`;
SET FOREIGN_KEY_CHECKS = 1;

DROP PROCEDURE IF EXISTS add_column_if_not_exists;
DROP PROCEDURE IF EXISTS drop_column_if_exists;
DROP PROCEDURE IF EXISTS migrate_old_dokumen_pdf_if_exists;
