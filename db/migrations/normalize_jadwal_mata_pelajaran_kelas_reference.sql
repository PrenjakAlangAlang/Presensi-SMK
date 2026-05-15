CREATE TABLE IF NOT EXISTS `kelas_jadwal` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(100) NOT NULL,
  `tahun_ajaran` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_kelas_jadwal` (`nama_kelas`, `tahun_ajaran`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DELIMITER //
CREATE PROCEDURE normalize_jadwal_kelas_reference()
BEGIN
  IF NOT EXISTS (
    SELECT 1
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'jadwal_mata_pelajaran'
      AND COLUMN_NAME = 'kelas_jadwal_id'
  ) THEN
    ALTER TABLE `jadwal_mata_pelajaran`
      ADD COLUMN `kelas_jadwal_id` int DEFAULT NULL AFTER `id`;
  END IF;

  IF EXISTS (
    SELECT 1
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'jadwal_mata_pelajaran'
      AND COLUMN_NAME = 'tahun_ajaran'
  ) THEN
    INSERT IGNORE INTO `kelas_jadwal` (`nama_kelas`, `tahun_ajaran`)
    SELECT DISTINCT `nama_kelas`, `tahun_ajaran`
    FROM `jadwal_mata_pelajaran`
    WHERE `nama_kelas` IS NOT NULL
      AND TRIM(`nama_kelas`) <> '';

    UPDATE `jadwal_mata_pelajaran` j
    INNER JOIN `kelas_jadwal` k
      ON k.`nama_kelas` = j.`nama_kelas`
      AND (k.`tahun_ajaran` <=> j.`tahun_ajaran`)
    SET j.`kelas_jadwal_id` = k.`id`
    WHERE j.`kelas_jadwal_id` IS NULL;
  ELSE
    INSERT IGNORE INTO `kelas_jadwal` (`nama_kelas`, `tahun_ajaran`)
    SELECT DISTINCT `nama_kelas`, NULL
    FROM `jadwal_mata_pelajaran`
    WHERE `nama_kelas` IS NOT NULL
      AND TRIM(`nama_kelas`) <> '';

    UPDATE `jadwal_mata_pelajaran` j
    INNER JOIN `kelas_jadwal` k
      ON k.`nama_kelas` = j.`nama_kelas`
      AND k.`tahun_ajaran` IS NULL
    SET j.`kelas_jadwal_id` = k.`id`
    WHERE j.`kelas_jadwal_id` IS NULL;
  END IF;

  IF EXISTS (
    SELECT 1
    FROM `jadwal_mata_pelajaran`
    WHERE `kelas_jadwal_id` IS NULL
    LIMIT 1
  ) THEN
    INSERT IGNORE INTO `kelas_jadwal` (`nama_kelas`, `tahun_ajaran`)
    VALUES ('Belum Ditentukan', NULL);

    UPDATE `jadwal_mata_pelajaran` j
    INNER JOIN `kelas_jadwal` k
      ON k.`nama_kelas` = 'Belum Ditentukan'
      AND k.`tahun_ajaran` IS NULL
    SET j.`kelas_jadwal_id` = k.`id`,
        j.`nama_kelas` = 'Belum Ditentukan'
    WHERE j.`kelas_jadwal_id` IS NULL;
  END IF;

  ALTER TABLE `jadwal_mata_pelajaran`
    MODIFY `kelas_jadwal_id` int NOT NULL;

  IF NOT EXISTS (
    SELECT 1
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'jadwal_mata_pelajaran'
      AND INDEX_NAME = 'idx_jadwal_kelas_ref'
  ) THEN
    ALTER TABLE `jadwal_mata_pelajaran`
      ADD KEY `idx_jadwal_kelas_ref` (`kelas_jadwal_id`);
  END IF;

  IF NOT EXISTS (
    SELECT 1
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND TABLE_NAME = 'jadwal_mata_pelajaran'
      AND CONSTRAINT_NAME = 'fk_jadwal_kelas_ref'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY'
  ) THEN
    ALTER TABLE `jadwal_mata_pelajaran`
      ADD CONSTRAINT `fk_jadwal_kelas_ref`
      FOREIGN KEY (`kelas_jadwal_id`) REFERENCES `kelas_jadwal` (`id`)
      ON DELETE CASCADE ON UPDATE CASCADE;
  END IF;

  IF EXISTS (
    SELECT 1
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'jadwal_mata_pelajaran'
      AND COLUMN_NAME = 'tahun_ajaran'
  ) THEN
    ALTER TABLE `jadwal_mata_pelajaran` DROP COLUMN `tahun_ajaran`;
  END IF;

  IF EXISTS (
    SELECT 1
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'jadwal_mata_pelajaran'
      AND COLUMN_NAME = 'created_at'
  ) THEN
    ALTER TABLE `jadwal_mata_pelajaran` DROP COLUMN `created_at`;
  END IF;

  IF EXISTS (
    SELECT 1
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'jadwal_mata_pelajaran'
      AND COLUMN_NAME = 'updated_at'
  ) THEN
    ALTER TABLE `jadwal_mata_pelajaran` DROP COLUMN `updated_at`;
  END IF;
END//
DELIMITER ;

CALL normalize_jadwal_kelas_reference();
DROP PROCEDURE normalize_jadwal_kelas_reference;
