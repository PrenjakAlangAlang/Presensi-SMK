-- Migration: Menambahkan tabel buku_induk_dokumen untuk mendukung multiple file upload
-- Tanggal: 29 Desember 2025

USE `presensi_smk`;

-- Buat tabel buku_induk_dokumen
CREATE TABLE IF NOT EXISTS `buku_induk_dokumen` (
  `id` int NOT NULL AUTO_INCREMENT,
  `buku_induk_id` int NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `path_file` varchar(500) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `buku_induk_id` (`buku_induk_id`),
  CONSTRAINT `fk_buku_induk_dokumen` FOREIGN KEY (`buku_induk_id`) REFERENCES `buku_induk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
