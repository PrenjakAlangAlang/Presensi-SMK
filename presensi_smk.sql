-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 02, 2025 at 03:01 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `presensi_smk`
--

-- --------------------------------------------------------

--
-- Table structure for table `izin_siswa`
--

CREATE TABLE `izin_siswa` (
  `id` int NOT NULL,
  `siswa_id` int NOT NULL,
  `tanggal` date NOT NULL,
  `alasan` text NOT NULL,
  `foto_bukti` varchar(255) DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak') DEFAULT 'pending',
  `waktu_pengajuan` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` int NOT NULL,
  `nama_kelas` varchar(100) NOT NULL,
  `tahun_ajaran` varchar(20) DEFAULT NULL,
  `wali_kelas` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `nama_kelas`, `tahun_ajaran`, `wali_kelas`, `created_at`) VALUES
(1, 'XI RPL 1', '2025/2026', 2, '2025-10-31 07:43:25');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_kemajuan`
--

CREATE TABLE `laporan_kemajuan` (
  `id` int NOT NULL,
  `kelas_id` int NOT NULL,
  `guru_id` int NOT NULL,
  `tanggal` date NOT NULL,
  `catatan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `aksi` varchar(255) DEFAULT NULL,
  `deskripsi` text,
  `waktu` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lokasi_sekolah`
--

CREATE TABLE `lokasi_sekolah` (
  `id` int NOT NULL,
  `nama_sekolah` varchar(150) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `radius_presensi` int DEFAULT '100',
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lokasi_sekolah`
--

INSERT INTO `lokasi_sekolah` (`id`, `nama_sekolah`, `latitude`, `longitude`, `radius_presensi`, `updated_by`, `created_at`) VALUES
(1, 'SMK Negeri 7 Yogyakarta', -7.7956, 110.3695, 100, 1, '2025-10-31 07:43:39'),
(2, 'SMK Negeri 7 Yogyakarta', -7.64961, 110.413032, 100, 1, '2025-11-02 06:45:36');

-- --------------------------------------------------------

--
-- Table structure for table `orangtua_siswa`
--

CREATE TABLE `orangtua_siswa` (
  `id` int NOT NULL,
  `orangtua_id` int NOT NULL,
  `siswa_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orangtua_siswa`
--

INSERT INTO `orangtua_siswa` (`id`, `orangtua_id`, `siswa_id`) VALUES
(5, 4, 3);

-- --------------------------------------------------------

--
-- Table structure for table `presensi_kelas`
--

CREATE TABLE `presensi_kelas` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `kelas_id` int NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `jarak` double NOT NULL,
  `status` enum('valid','invalid') DEFAULT 'invalid',
  `waktu` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `presensi_sekolah`
--

CREATE TABLE `presensi_sekolah` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `jarak` double NOT NULL,
  `status` enum('valid','invalid') DEFAULT 'invalid',
  `waktu` datetime DEFAULT CURRENT_TIMESTAMP,
  `jenis` enum('hadir','izin','sakit','alpha') DEFAULT 'hadir'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `siswa_kelas`
--

CREATE TABLE `siswa_kelas` (
  `id` int NOT NULL,
  `siswa_id` int NOT NULL,
  `kelas_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `siswa_kelas`
--

INSERT INTO `siswa_kelas` (`id`, `siswa_id`, `kelas_id`) VALUES
(1, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','guru','siswa','orangtua') NOT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `foto_profil`, `created_at`) VALUES
(1, 'Admin Utama', 'admin@smk7.sch.id', 'admin123', 'admin', NULL, '2025-10-31 07:43:18'),
(2, 'Guru Informatika', 'guru@smk7.sch.id', 'guru123', 'guru', NULL, '2025-10-31 07:43:18'),
(3, 'Luthfi', 'siswa@smk7.sch.id', 'siswa123', 'siswa', NULL, '2025-10-31 07:43:18'),
(4, 'Ortu Siswa A', 'ortu@smk7.sch.id', 'ortu123', 'orangtua', NULL, '2025-10-31 07:43:18'),
(5, 'Fakhri', 'fakhri@gmail.com', '123', 'siswa', NULL, '2025-11-02 02:43:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `izin_siswa`
--
ALTER TABLE `izin_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wali_kelas` (`wali_kelas`);

--
-- Indexes for table `laporan_kemajuan`
--
ALTER TABLE `laporan_kemajuan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `lokasi_sekolah`
--
ALTER TABLE `lokasi_sekolah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `orangtua_siswa`
--
ALTER TABLE `orangtua_siswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orangtua_id` (`orangtua_id`,`siswa_id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indexes for table `presensi_kelas`
--
ALTER TABLE `presensi_kelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_presensi_kelas_user` (`user_id`),
  ADD KEY `fk_presensi_kelas_kelas` (`kelas_id`);

--
-- Indexes for table `presensi_sekolah`
--
ALTER TABLE `presensi_sekolah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_presensi_sekolah_user` (`user_id`);

--
-- Indexes for table `siswa_kelas`
--
ALTER TABLE `siswa_kelas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `siswa_id` (`siswa_id`,`kelas_id`),
  ADD KEY `kelas_id` (`kelas_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `izin_siswa`
--
ALTER TABLE `izin_siswa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `laporan_kemajuan`
--
ALTER TABLE `laporan_kemajuan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lokasi_sekolah`
--
ALTER TABLE `lokasi_sekolah`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orangtua_siswa`
--
ALTER TABLE `orangtua_siswa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `presensi_kelas`
--
ALTER TABLE `presensi_kelas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `presensi_sekolah`
--
ALTER TABLE `presensi_sekolah`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `siswa_kelas`
--
ALTER TABLE `siswa_kelas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `izin_siswa`
--
ALTER TABLE `izin_siswa`
  ADD CONSTRAINT `izin_siswa_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kelas`
--
ALTER TABLE `kelas`
  ADD CONSTRAINT `kelas_ibfk_1` FOREIGN KEY (`wali_kelas`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `laporan_kemajuan`
--
ALTER TABLE `laporan_kemajuan`
  ADD CONSTRAINT `laporan_kemajuan_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `laporan_kemajuan_ibfk_2` FOREIGN KEY (`guru_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lokasi_sekolah`
--
ALTER TABLE `lokasi_sekolah`
  ADD CONSTRAINT `lokasi_sekolah_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orangtua_siswa`
--
ALTER TABLE `orangtua_siswa`
  ADD CONSTRAINT `orangtua_siswa_ibfk_1` FOREIGN KEY (`orangtua_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orangtua_siswa_ibfk_2` FOREIGN KEY (`siswa_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `presensi_kelas`
--
ALTER TABLE `presensi_kelas`
  ADD CONSTRAINT `fk_presensi_kelas_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_presensi_kelas_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `presensi_kelas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `presensi_kelas_ibfk_2` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `presensi_sekolah`
--
ALTER TABLE `presensi_sekolah`
  ADD CONSTRAINT `fk_presensi_sekolah_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `presensi_sekolah_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `siswa_kelas`
--
ALTER TABLE `siswa_kelas`
  ADD CONSTRAINT `siswa_kelas_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `siswa_kelas_ibfk_2` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
