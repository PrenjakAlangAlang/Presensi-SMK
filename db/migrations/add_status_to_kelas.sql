SET @column_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'kelas'
    AND COLUMN_NAME = 'status'
);

SET @sql := IF(
  @column_exists = 0,
  'ALTER TABLE `kelas` ADD COLUMN `status` ENUM(''active'',''archived'') NOT NULL DEFAULT ''active'' AFTER `semester`',
  'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE `kelas`
SET `status` = 'active'
WHERE `status` IS NULL;
