-- Tambah role admin_kesiswaan dan tabel buku_induk

-- 1) Tambah nilai enum role
ALTER TABLE `users`
  MODIFY `role` enum('admin','admin_kesiswaan','guru','siswa','orangtua') NOT NULL;

-- 2) Tabel buku_induk
CREATE TABLE IF NOT EXISTS `buku_induk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `nama` varchar(150) NOT NULL,
  `nis` varchar(50) NOT NULL,
  `nisn` varchar(50) NOT NULL,
  `tempat_lahir` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text,
  `dokumen_pdf` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_buku_induk` (`user_id`),
  CONSTRAINT `fk_buku_induk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
