DELIMITER //
CREATE PROCEDURE drop_laporan_kemajuan_if_exists()
BEGIN
  IF EXISTS (
    SELECT 1
    FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'laporan_kemajuan'
  ) THEN
    SET FOREIGN_KEY_CHECKS = 0;
    DROP TABLE `laporan_kemajuan`;
    SET FOREIGN_KEY_CHECKS = 1;
  END IF;
END//
DELIMITER ;

CALL drop_laporan_kemajuan_if_exists();
DROP PROCEDURE drop_laporan_kemajuan_if_exists;
