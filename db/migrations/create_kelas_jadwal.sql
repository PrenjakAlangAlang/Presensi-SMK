CREATE TABLE IF NOT EXISTS `kelas_jadwal` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(100) NOT NULL,
  `tahun_ajaran` varchar(20) DEFAULT NULL,
  `semester` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_kelas_jadwal` (`nama_kelas`, `tahun_ajaran`, `semester`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT IGNORE INTO `kelas_jadwal` (`nama_kelas`, `tahun_ajaran`)
SELECT DISTINCT `nama_kelas`, `tahun_ajaran`
FROM `jadwal_mata_pelajaran`
WHERE `nama_kelas` IS NOT NULL
  AND TRIM(`nama_kelas`) <> '';
