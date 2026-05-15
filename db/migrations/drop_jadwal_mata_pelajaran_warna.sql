DELIMITER //
CREATE PROCEDURE drop_jadwal_warna_if_exists()
BEGIN
  IF EXISTS (
    SELECT 1
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'jadwal_mata_pelajaran'
      AND COLUMN_NAME = 'warna'
  ) THEN
    ALTER TABLE `jadwal_mata_pelajaran` DROP COLUMN `warna`;
  END IF;
END//
DELIMITER ;

CALL drop_jadwal_warna_if_exists();
DROP PROCEDURE drop_jadwal_warna_if_exists;
