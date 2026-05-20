-- Restore presensi mata pelajaran berbasis jadwal_mata_pelajaran.

CREATE TABLE IF NOT EXISTS `presensi_mapel_sesi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jadwal_mata_pelajaran_id` int NOT NULL,
  `guru_id` int NOT NULL,
  `waktu_buka` datetime NOT NULL,
  `waktu_tutup` datetime NOT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  `laporan_kemajuan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_presensi_mapel_sesi_jadwal_tanggal` (`jadwal_mata_pelajaran_id`, `waktu_buka`),
  KEY `idx_presensi_mapel_sesi_status` (`status`, `waktu_tutup`),
  KEY `idx_presensi_mapel_sesi_guru` (`guru_id`),
  CONSTRAINT `fk_presensi_mapel_sesi_jadwal` FOREIGN KEY (`jadwal_mata_pelajaran_id`) REFERENCES `jadwal_mata_pelajaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_presensi_mapel_sesi_guru` FOREIGN KEY (`guru_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `presensi_mapel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `presensi_sesi_id` int DEFAULT NULL,
  `user_id` int NOT NULL,
  `jadwal_mata_pelajaran_id` int NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `jarak` decimal(10,2) DEFAULT NULL,
  `status` enum('valid','invalid') NOT NULL DEFAULT 'valid',
  `waktu` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `jenis` enum('hadir','izin','sakit','alpha') NOT NULL DEFAULT 'hadir',
  `alasan` text DEFAULT NULL,
  `foto_bukti` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_presensi_mapel_user_sesi` (`user_id`, `presensi_sesi_id`),
  KEY `idx_presensi_mapel_jadwal` (`jadwal_mata_pelajaran_id`),
  KEY `idx_presensi_mapel_sesi` (`presensi_sesi_id`),
  CONSTRAINT `fk_presensi_mapel_buku_induk` FOREIGN KEY (`user_id`) REFERENCES `buku_induk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_presensi_mapel_jadwal` FOREIGN KEY (`jadwal_mata_pelajaran_id`) REFERENCES `jadwal_mata_pelajaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_presensi_mapel_sesi` FOREIGN KEY (`presensi_sesi_id`) REFERENCES `presensi_mapel_sesi` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
