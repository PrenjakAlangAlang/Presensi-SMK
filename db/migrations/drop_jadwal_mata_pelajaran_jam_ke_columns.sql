DELIMITER //
CREATE PROCEDURE drop_jadwal_jam_ke_columns_if_exists()
BEGIN
  IF EXISTS (
    SELECT 1
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'jadwal_mata_pelajaran'
      AND COLUMN_NAME = 'jam_ke_mulai'
  ) THEN
    ALTER TABLE `jadwal_mata_pelajaran` DROP COLUMN `jam_ke_mulai`;
  END IF;

  IF EXISTS (
    SELECT 1
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'jadwal_mata_pelajaran'
      AND COLUMN_NAME = 'jam_ke_selesai'
  ) THEN
    ALTER TABLE `jadwal_mata_pelajaran` DROP COLUMN `jam_ke_selesai`;
  END IF;
END//
DELIMITER ;

CALL drop_jadwal_jam_ke_columns_if_exists();
DROP PROCEDURE drop_jadwal_jam_ke_columns_if_exists;
