SET @column_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'buku_induk'
    AND COLUMN_NAME = 'email'
);

SET @sql := IF(
  @column_exists = 0,
  'ALTER TABLE `buku_induk` ADD COLUMN `email` VARCHAR(100) NULL AFTER `nis`',
  'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE `buku_induk`
SET `email` = LOWER(CONCAT(TRIM(`nis`), '@smk7.sch.id'))
WHERE (`email` IS NULL OR `email` = '')
  AND `nis` IS NOT NULL
  AND TRIM(`nis`) <> '';

SET @index_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'buku_induk'
    AND INDEX_NAME = 'idx_buku_induk_email'
);

SET @sql := IF(
  @index_exists = 0,
  'ALTER TABLE `buku_induk` ADD INDEX `idx_buku_induk_email` (`email`)',
  'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
