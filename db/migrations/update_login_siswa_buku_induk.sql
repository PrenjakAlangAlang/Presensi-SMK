-- Migration: login siswa melalui NIS dari buku_induk.
-- Setelah migrasi ini, tabel users hanya menyimpan admin, admin_kesiswaan, dan guru.
-- Jalankan backup database terlebih dahulu karena user dengan role siswa akan dihapus.

DROP PROCEDURE IF EXISTS drop_fk_if_exists;
DROP PROCEDURE IF EXISTS add_column_if_not_exists;
DROP PROCEDURE IF EXISTS add_index_if_not_exists;
DROP PROCEDURE IF EXISTS add_fk_if_not_exists;

DELIMITER $$

CREATE PROCEDURE drop_fk_if_exists(IN p_table VARCHAR(64), IN p_fk VARCHAR(64))
BEGIN
  IF EXISTS (
    SELECT 1
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND TABLE_NAME = p_table
      AND CONSTRAINT_NAME = p_fk
      AND CONSTRAINT_TYPE = 'FOREIGN KEY'
  ) THEN
    SET @sql = CONCAT('ALTER TABLE `', p_table, '` DROP FOREIGN KEY `', p_fk, '`');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END$$

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

CREATE PROCEDURE add_index_if_not_exists(IN p_table VARCHAR(64), IN p_index VARCHAR(64), IN p_columns TEXT)
BEGIN
  IF NOT EXISTS (
    SELECT 1
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = p_table
      AND INDEX_NAME = p_index
  ) THEN
    SET @sql = CONCAT('ALTER TABLE `', p_table, '` ADD INDEX `', p_index, '` (', p_columns, ')');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END$$

CREATE PROCEDURE add_fk_if_not_exists(IN p_table VARCHAR(64), IN p_fk VARCHAR(64), IN p_definition TEXT)
BEGIN
  IF NOT EXISTS (
    SELECT 1
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND TABLE_NAME = p_table
      AND CONSTRAINT_NAME = p_fk
      AND CONSTRAINT_TYPE = 'FOREIGN KEY'
  ) THEN
    SET @sql = CONCAT('ALTER TABLE `', p_table, '` ADD CONSTRAINT `', p_fk, '` ', p_definition);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END$$

DELIMITER ;

-- 1) Hapus tabel proses presensi mata pelajaran jika masih ada.
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `presensi_mapel`;
DROP TABLE IF EXISTS `presensi_mapel_sesi`;
SET FOREIGN_KEY_CHECKS = 1;

-- 2) Lepaskan relasi lama yang menganggap siswa sebagai users.
CALL drop_fk_if_exists('buku_induk', 'fk_buku_induk_user');
CALL drop_fk_if_exists('presensi_sekolah', 'fk_presensi_sekolah_user');
CALL drop_fk_if_exists('presensi_sekolah', 'presensi_sekolah_ibfk_1');
CALL drop_fk_if_exists('siswa_mata_pelajaran', 'siswa_mata_pelajaran_ibfk_1');

-- 3) Siapkan password login siswa di buku_induk.
ALTER TABLE `buku_induk`
  MODIFY `user_id` int DEFAULT NULL;

CALL add_column_if_not_exists('buku_induk', 'password', '`password` varchar(255) DEFAULT NULL AFTER `email_ortu`');

-- Salin password siswa lama dari users ke buku_induk sebelum user siswa dihapus.
UPDATE `buku_induk` bi
INNER JOIN `users` u ON u.id = bi.user_id AND u.role = 'siswa'
SET bi.`password` = u.`password`
WHERE bi.`password` IS NULL OR bi.`password` = '';

-- 4) Pindahkan data relasi lama dari users.id ke buku_induk.id.
UPDATE `presensi_sekolah` ps
INNER JOIN `buku_induk` bi ON bi.user_id = ps.user_id
INNER JOIN `users` u ON u.id = bi.user_id AND u.role = 'siswa'
SET ps.user_id = bi.id;

UPDATE `siswa_mata_pelajaran` smp
INNER JOIN `buku_induk` bi ON bi.user_id = smp.siswa_id
INNER JOIN `users` u ON u.id = bi.user_id AND u.role = 'siswa'
SET smp.siswa_id = bi.id;

-- Buku induk menjadi sumber akun siswa. user_id hanya disisakan nullable untuk data lama.
UPDATE `buku_induk` bi
INNER JOIN `users` u ON u.id = bi.user_id AND u.role = 'siswa'
SET bi.user_id = NULL;

-- 5) Hapus akun siswa dari users dan hapus role siswa dari enum.
DELETE FROM `users`
WHERE `role` = 'siswa';

ALTER TABLE `users`
  MODIFY `email` varchar(100) NOT NULL,
  MODIFY `role` enum('admin','admin_kesiswaan','guru') NOT NULL;

-- 6) Pasang relasi baru ke buku_induk.
CALL add_fk_if_not_exists(
  'presensi_sekolah',
  'fk_presensi_sekolah_buku_induk',
  'FOREIGN KEY (`user_id`) REFERENCES `buku_induk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE'
);

CALL add_fk_if_not_exists(
  'siswa_mata_pelajaran',
  'fk_siswa_mapel_buku_induk',
  'FOREIGN KEY (`siswa_id`) REFERENCES `buku_induk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE'
);

-- 7) Index untuk mempercepat login siswa berdasarkan NIS.
CALL add_index_if_not_exists('buku_induk', 'idx_buku_induk_nis', '`nis`');

-- 8) Cek NIS duplikat sebelum menjadikan NIS unik.
-- Jika query ini mengembalikan baris, rapikan dulu data NIS yang duplikat.
SELECT `nis`, COUNT(*) AS total
FROM `buku_induk`
GROUP BY `nis`
HAVING COUNT(*) > 1;

-- Opsional tapi direkomendasikan setelah NIS duplikat sudah bersih.
-- ALTER TABLE `buku_induk`
--   ADD UNIQUE KEY `uniq_buku_induk_nis` (`nis`);

DROP PROCEDURE IF EXISTS drop_fk_if_exists;
DROP PROCEDURE IF EXISTS add_column_if_not_exists;
DROP PROCEDURE IF EXISTS add_index_if_not_exists;
DROP PROCEDURE IF EXISTS add_fk_if_not_exists;
