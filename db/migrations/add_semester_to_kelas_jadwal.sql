SET @has_semester_column = (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'kelas_jadwal'
    AND COLUMN_NAME = 'semester'
);

SET @sql = IF(
  @has_semester_column = 0,
  'ALTER TABLE `kelas_jadwal` ADD COLUMN `semester` varchar(20) DEFAULT NULL AFTER `tahun_ajaran`',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_semester_unique = (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'kelas_jadwal'
    AND INDEX_NAME = 'uniq_kelas_jadwal'
    AND COLUMN_NAME = 'semester'
);

SET @has_unique = (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'kelas_jadwal'
    AND INDEX_NAME = 'uniq_kelas_jadwal'
);

SET @sql = IF(
  @has_semester_unique = 0 AND @has_unique > 0,
  'ALTER TABLE `kelas_jadwal` DROP INDEX `uniq_kelas_jadwal`',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_unique = (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'kelas_jadwal'
    AND INDEX_NAME = 'uniq_kelas_jadwal'
);

SET @sql = IF(
  @has_unique = 0,
  'ALTER TABLE `kelas_jadwal` ADD UNIQUE KEY `uniq_kelas_jadwal` (`nama_kelas`, `tahun_ajaran`, `semester`)',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
