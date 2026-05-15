DELIMITER //
CREATE PROCEDURE drop_jadwal_legacy_columns_if_exists()
BEGIN
  IF EXISTS (
    SELECT 1
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'jadwal_mata_pelajaran'
      AND COLUMN_NAME = 'legacy_mata_pelajaran_id'
  ) THEN
    ALTER TABLE `jadwal_mata_pelajaran` DROP COLUMN `legacy_mata_pelajaran_id`;
  END IF;

  IF EXISTS (
    SELECT 1
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'jadwal_mata_pelajaran'
      AND COLUMN_NAME = 'legacy_kelas_id'
  ) THEN
    ALTER TABLE `jadwal_mata_pelajaran` DROP COLUMN `legacy_kelas_id`;
  END IF;
END//
DELIMITER ;

CALL drop_jadwal_legacy_columns_if_exists();
DROP PROCEDURE drop_jadwal_legacy_columns_if_exists;
