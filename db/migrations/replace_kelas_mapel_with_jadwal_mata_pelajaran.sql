-- Ganti master kelas/mata_pelajaran lama menjadi jadwal_mata_pelajaran.
-- Jalankan setelah backup database.

CREATE TABLE IF NOT EXISTS `jadwal_mata_pelajaran` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(100) NOT NULL,
  `nama_mata_pelajaran` varchar(120) NOT NULL,
  `guru_pengampu` int DEFAULT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `ruang` varchar(80) DEFAULT NULL,
  `tahun_ajaran` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_jadwal_guru` (`guru_pengampu`),
  KEY `idx_jadwal_kelas_hari` (`nama_kelas`, `hari`, `jam_mulai`),
  CONSTRAINT `fk_jadwal_guru` FOREIGN KEY (`guru_pengampu`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `jadwal_mata_pelajaran_siswa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jadwal_mata_pelajaran_id` int NOT NULL,
  `siswa_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_jadwal_siswa` (`jadwal_mata_pelajaran_id`, `siswa_id`),
  KEY `idx_jadwal_siswa_siswa` (`siswa_id`),
  CONSTRAINT `fk_jadwal_siswa_jadwal` FOREIGN KEY (`jadwal_mata_pelajaran_id`) REFERENCES `jadwal_mata_pelajaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_jadwal_siswa_buku_induk` FOREIGN KEY (`siswa_id`) REFERENCES `buku_induk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DELIMITER //
CREATE PROCEDURE migrate_old_mapel_if_exists()
BEGIN
  IF EXISTS (
    SELECT 1 FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'mata_pelajaran'
  ) THEN
    DROP TEMPORARY TABLE IF EXISTS `tmp_jadwal_migrasi`;
    CREATE TEMPORARY TABLE `tmp_jadwal_migrasi` AS
    SELECT
      mp.`id` AS `old_mata_pelajaran_id`,
      k.`id` AS `old_kelas_id`,
      COALESCE(k.`nama_kelas`, 'Belum Ditentukan') AS `nama_kelas`,
      mp.`nama_mata_pelajaran`,
      mp.`guru_pengampu`,
      CASE
        WHEN SUBSTRING_INDEX(COALESCE(mp.`jadwal`, ''), ',', 1) IN ('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')
          THEN SUBSTRING_INDEX(mp.`jadwal`, ',', 1)
      ELSE 'Senin'
      END AS `hari`,
      CAST('07:00:00' AS TIME) AS `jam_mulai`,
      CAST('07:40:00' AS TIME) AS `jam_selesai`,
      NULL AS `ruang`,
      k.`tahun_ajaran`
    FROM `mata_pelajaran` mp
    LEFT JOIN `kelas_mata_pelajaran` kmp ON mp.`id` = kmp.`mata_pelajaran_id`
    LEFT JOIN `kelas` k ON kmp.`kelas_id` = k.`id`;

    INSERT INTO `jadwal_mata_pelajaran`
    (`nama_kelas`, `nama_mata_pelajaran`, `guru_pengampu`, `hari`, `jam_mulai`, `jam_selesai`, `ruang`, `tahun_ajaran`)
    SELECT
      t.`nama_kelas`,
      t.`nama_mata_pelajaran`,
      t.`guru_pengampu`,
      t.`hari`,
      t.`jam_mulai`,
      t.`jam_selesai`,
      t.`ruang`,
      t.`tahun_ajaran`
    FROM `tmp_jadwal_migrasi` t
    WHERE NOT EXISTS (
      SELECT 1
      FROM `jadwal_mata_pelajaran` j
      WHERE j.`nama_kelas` = t.`nama_kelas`
        AND j.`nama_mata_pelajaran` = t.`nama_mata_pelajaran`
        AND (j.`guru_pengampu` <=> t.`guru_pengampu`)
        AND j.`hari` = t.`hari`
        AND j.`jam_mulai` = t.`jam_mulai`
        AND j.`jam_selesai` = t.`jam_selesai`
        AND (j.`ruang` <=> t.`ruang`)
        AND (j.`tahun_ajaran` <=> t.`tahun_ajaran`)
    );
  END IF;

  IF EXISTS (
    SELECT 1 FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'siswa_mata_pelajaran'
  ) AND EXISTS (
    SELECT 1 FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'mata_pelajaran'
  ) THEN
    INSERT IGNORE INTO `jadwal_mata_pelajaran_siswa` (`jadwal_mata_pelajaran_id`, `siswa_id`)
    SELECT j.`id`, smp.`siswa_id`
    FROM `siswa_mata_pelajaran` smp
    INNER JOIN `tmp_jadwal_migrasi` t ON t.`old_mata_pelajaran_id` = smp.`mata_pelajaran_id`
    INNER JOIN `jadwal_mata_pelajaran` j
      ON j.`nama_kelas` = t.`nama_kelas`
      AND j.`nama_mata_pelajaran` = t.`nama_mata_pelajaran`
      AND (j.`guru_pengampu` <=> t.`guru_pengampu`)
      AND j.`hari` = t.`hari`
      AND j.`jam_mulai` = t.`jam_mulai`
      AND j.`jam_selesai` = t.`jam_selesai`
      AND (j.`ruang` <=> t.`ruang`)
      AND (j.`tahun_ajaran` <=> t.`tahun_ajaran`);
  END IF;

  DROP TEMPORARY TABLE IF EXISTS `tmp_jadwal_migrasi`;
END//

CREATE PROCEDURE drop_fk_if_exists(IN table_name_in varchar(64), IN fk_name_in varchar(64))
BEGIN
  IF EXISTS (
    SELECT 1
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND TABLE_NAME = table_name_in
      AND CONSTRAINT_NAME = fk_name_in
      AND CONSTRAINT_TYPE = 'FOREIGN KEY'
  ) THEN
    SET @sql = CONCAT('ALTER TABLE `', table_name_in, '` DROP FOREIGN KEY `', fk_name_in, '`');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END//
DELIMITER ;

CALL migrate_old_mapel_if_exists();
CALL drop_fk_if_exists('kelas', 'fk_kelas_wali_kelas');
CALL drop_fk_if_exists('kelas_mata_pelajaran', 'fk_kelas_mapel_kelas');
CALL drop_fk_if_exists('kelas_mata_pelajaran', 'fk_kelas_mapel_mapel');
CALL drop_fk_if_exists('mata_pelajaran', 'fk_mata_pelajaran_guru');
CALL drop_fk_if_exists('laporan_kemajuan', 'laporan_kemajuan_ibfk_1');
CALL drop_fk_if_exists('presensi_mapel', 'fk_presensi_mapel_mapel');
CALL drop_fk_if_exists('presensi_mapel', 'presensi_mapel_ibfk_2');
CALL drop_fk_if_exists('presensi_mapel_sesi', 'presensi_mapel_sesi_ibfk_1');
CALL drop_fk_if_exists('siswa_mata_pelajaran', 'siswa_mata_pelajaran_ibfk_1');
CALL drop_fk_if_exists('siswa_mata_pelajaran', 'siswa_mata_pelajaran_ibfk_2');

DROP PROCEDURE drop_fk_if_exists;
DROP PROCEDURE migrate_old_mapel_if_exists;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `siswa_mata_pelajaran`;
DROP TABLE IF EXISTS `kelas_mata_pelajaran`;
DROP TABLE IF EXISTS `kelas`;
DROP TABLE IF EXISTS `mata_pelajaran`;
SET FOREIGN_KEY_CHECKS = 1;
