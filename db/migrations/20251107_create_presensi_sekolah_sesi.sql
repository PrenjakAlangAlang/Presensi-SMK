-- Migration: create presensi_sekolah_sesi table
-- Run this SQL in your database if presensi_sekolah_sesi doesn't exist
CREATE TABLE IF NOT EXISTS `presensi_sekolah_sesi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `waktu_buka` datetime NOT NULL,
  `waktu_tutup` datetime NOT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  `is_manual` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
