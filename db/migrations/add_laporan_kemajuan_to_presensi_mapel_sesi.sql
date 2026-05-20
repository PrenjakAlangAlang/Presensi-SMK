-- Tambah catatan laporan kemajuan per sesi presensi mapel.

DELIMITER $$

CREATE PROCEDURE add_laporan_kemajuan_presensi_mapel_sesi()
BEGIN
  IF NOT EXISTS (
    SELECT 1
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'presensi_mapel_sesi'
      AND COLUMN_NAME = 'laporan_kemajuan'
  ) THEN
    ALTER TABLE `presensi_mapel_sesi`
      ADD COLUMN `laporan_kemajuan` text DEFAULT NULL AFTER `status`;
  END IF;

END$$

DELIMITER ;

CALL add_laporan_kemajuan_presensi_mapel_sesi();
DROP PROCEDURE add_laporan_kemajuan_presensi_mapel_sesi;
