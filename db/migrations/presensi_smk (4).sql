-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 31, 2026 at 04:44 PM
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
-- Table structure for table `buku_induk`
--

CREATE TABLE `buku_induk` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `nama` varchar(150) NOT NULL,
  `nis` varchar(50) NOT NULL,
  `nisn` varchar(50) NOT NULL,
  `tempat_lahir` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text,
  `nama_ayah` varchar(150) DEFAULT NULL,
  `nama_ibu` varchar(150) DEFAULT NULL,
  `nama_wali` varchar(100) DEFAULT NULL,
  `no_telp_ortu` varchar(20) DEFAULT NULL,
  `email_ortu` varchar(255) DEFAULT NULL COMMENT 'Email orang tua untuk notifikasi',
  `dokumen_pdf` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `buku_induk`
--

INSERT INTO `buku_induk` (`id`, `user_id`, `nama`, `nis`, `nisn`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `nama_ayah`, `nama_ibu`, `nama_wali`, `no_telp_ortu`, `email_ortu`, `dokumen_pdf`, `created_at`, `updated_at`) VALUES
(1, 5, 'Fakhri', '7627', '11134567', 'Sleman', '2025-12-01', 'Ngampel', '', '', '', '0895363611056', 'luthfinurafiq76@gmail.com', 'http://localhost/Presensi-SMK/public/uploads/buku_induk/buku-induk-694239e508e54.pdf', '2025-12-17 05:04:37', '2026-01-12 05:15:40'),
(2, 8, 'Zola', '7627', '11134567', 'Sleman', '2025-12-01', 'fgsdfs', 'dsfsdf', 'sdfdsfsd', NULL, '089644755532', 'luthfinurafiq76@gmail.com', 'http://localhost/Presensi-SMK/public/uploads/buku_induk/buku-induk-69516fc03cadd.pdf', '2025-12-24 15:57:41', '2025-12-28 17:58:24');

-- --------------------------------------------------------

--
-- Table structure for table `buku_induk_dokumen`
--

CREATE TABLE `buku_induk_dokumen` (
  `id` int NOT NULL,
  `buku_induk_id` int NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `path_file` varchar(500) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `buku_induk_dokumen`
--

INSERT INTO `buku_induk_dokumen` (`id`, `buku_induk_id`, `nama_file`, `path_file`, `keterangan`, `created_at`) VALUES
(1, 2, 'Kelompok 9_124220021_Luthfi_Tugas2.pdf', 'http://localhost/Presensi-SMK/public/uploads/buku_induk/buku-induk-69516fc03d788.pdf', '', '2025-12-28 17:58:24'),
(2, 2, 'Kelompok 9_124220021_Luthfi_Tugas1 (revisi).pdf', 'http://localhost/Presensi-SMK/public/uploads/buku_induk/buku-induk-69516fc03e151.pdf', '', '2025-12-28 17:58:24');

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` int NOT NULL,
  `nama_kelas` varchar(100) NOT NULL,
  `tahun_ajaran` varchar(20) DEFAULT NULL,
  `wali_kelas` int DEFAULT NULL,
  `jadwal` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `nama_kelas`, `tahun_ajaran`, `wali_kelas`, `jadwal`, `created_at`) VALUES
(1, 'XI RPL 1', '2025/2026', 2, 'Senin, 07:30-09:00', '2025-10-31 07:43:25'),
(3, 'XII Multimedia', '2025/2026', 2, 'Senin, 09:30-11:00', '2025-11-07 07:14:50'),
(4, 'X TKR 2', '2025/2026', 6, 'Senin, 11.00-12.30', '2026-01-30 14:03:05');

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

--
-- Dumping data for table `laporan_kemajuan`
--

INSERT INTO `laporan_kemajuan` (`id`, `kelas_id`, `guru_id`, `tanggal`, `catatan`, `created_at`) VALUES
(1, 1, 2, '2025-11-02', 'jh', '2025-11-02 15:25:14'),
(2, 1, 2, '2025-11-03', 'sEMUA hADIR', '2025-11-03 06:58:27'),
(3, 3, 2, '2025-12-22', 'mmno', '2025-12-22 02:27:07'),
(4, 3, 2, '2025-12-22', 'bhjb', '2025-12-22 02:36:13'),
(5, 1, 2, '2025-12-23', 'b hkb kj', '2025-12-23 17:02:57'),
(6, 1, 2, '2025-12-23', ' nhgfn', '2025-12-23 17:04:03'),
(7, 1, 2, '2025-12-25', 'eqwdfs', '2025-12-25 05:07:53'),
(8, 1, 2, '2025-12-25', 'hgf', '2025-12-25 05:22:00'),
(9, 1, 2, '2025-12-25', 'hgf', '2025-12-25 05:22:00'),
(10, 1, 2, '2026-01-02', '', '2026-01-02 15:41:15'),
(11, 1, 2, '2026-01-06', 'gdfgfd', '2026-01-06 15:15:05'),
(12, 1, 2, '2026-01-08', 'h8nu', '2026-01-08 06:12:59'),
(13, 1, 2, '2026-01-08', 'h8nu', '2026-01-08 06:12:59');

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
(2, 'SMK Negeri 7 Yogyakarta', -7.64961, 110.413032, 100, 1, '2025-11-02 06:45:36'),
(3, 'SMK Negeri 7 Yogyakarta', -7.652036, 110.412129, 300, 1, '2026-01-11 14:00:37'),
(4, 'SMK Negeri 7 Yogyakarta', -7.652164, 110.41374, 400, 1, '2026-01-11 14:00:54'),
(5, 'SMK Negeri 7 Yogyakarta', -7.649973, 110.412988, 400, 1, '2026-01-11 14:01:38'),
(6, 'SMK Negeri 7 Yogyakarta', -7.649731, 110.416681, 400, 1, '2026-01-30 14:05:23');

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
  `waktu` datetime DEFAULT CURRENT_TIMESTAMP,
  `presensi_sesi_id` int DEFAULT NULL,
  `jenis` enum('hadir','izin','sakit','alpha') DEFAULT 'hadir',
  `alasan` text,
  `foto_bukti` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `presensi_kelas`
--

INSERT INTO `presensi_kelas` (`id`, `user_id`, `kelas_id`, `latitude`, `longitude`, `jarak`, `status`, `waktu`, `presensi_sesi_id`, `jenis`, `alasan`, `foto_bukti`) VALUES
(1, 3, 1, -7.649809168880289, 110.41317558958572, 27.219115022648, 'valid', '2025-11-02 22:41:04', 4, 'hadir', NULL, NULL),
(4, 3, 1, 0, 0, 0, 'valid', '2025-12-23 22:35:23', 7, 'izin', 'non', 'http://localhost/Presensi-SMK/public/uploads/izin/bukti-694ab6bb9a3ef.jpeg'),
(5, 5, 1, 0, 0, 0, 'valid', '2025-12-23 22:40:00', 7, 'izin', '3e23ewd', 'http://localhost/Presensi-SMK/public/uploads/izin/bukti-694ab7d06cecb.jpeg'),
(6, 3, 1, 0, 0, 0, 'valid', '2025-12-24 00:04:03', 8, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(7, 5, 1, 0, 0, 0, 'valid', '2025-12-24 00:04:03', 8, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(8, 3, 1, 0, 0, 0, 'valid', '2025-12-25 12:07:53', 9, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(9, 5, 1, 0, 0, 0, 'valid', '2025-12-25 12:07:53', 9, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(10, 3, 1, 0, 0, 0, 'valid', '2025-12-25 12:21:59', 10, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(11, 5, 1, 0, 0, 0, 'valid', '2025-12-25 12:21:59', 10, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(12, 3, 1, 0, 0, 0, 'valid', '2026-01-02 22:41:12', 11, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(13, 5, 1, 0, 0, 0, 'valid', '2026-01-02 22:41:12', 11, 'hadir', 'Tidak hadir saat sesi ditutup', ''),
(14, 3, 1, 0, 0, 0, 'valid', '2026-01-06 22:15:01', 12, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(15, 5, 1, 0, 0, 0, 'valid', '2026-01-06 22:15:01', 12, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(16, 8, 1, 0, 0, 0, 'valid', '2026-01-06 22:15:04', 12, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(17, 3, 1, 0, 0, 0, 'valid', '2026-01-08 13:12:53', 13, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(18, 5, 1, 0, 0, 0, 'valid', '2026-01-08 13:12:53', 13, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(19, 8, 1, 0, 0, 0, 'valid', '2026-01-08 13:12:57', 13, 'alpha', 'Tidak hadir saat sesi ditutup', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `presensi_sekolah`
--

CREATE TABLE `presensi_sekolah` (
  `id` int NOT NULL,
  `presensi_sekolah_sesi_id` int DEFAULT NULL,
  `user_id` int NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `jarak` double NOT NULL,
  `status` enum('valid','invalid') DEFAULT 'invalid',
  `waktu` datetime DEFAULT CURRENT_TIMESTAMP,
  `jenis` enum('hadir','izin','sakit','alpha') DEFAULT 'hadir',
  `alasan` text,
  `foto_bukti` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `presensi_sekolah`
--

INSERT INTO `presensi_sekolah` (`id`, `presensi_sekolah_sesi_id`, `user_id`, `latitude`, `longitude`, `jarak`, `status`, `waktu`, `jenis`, `alasan`, `foto_bukti`) VALUES
(2, 3, 3, -7.649853, 110.41310899999999, 28.321532878069, 'valid', '2025-11-07 23:03:47', 'hadir', NULL, NULL),
(3, 4, 8, -7.649859, 110.413132, 29.800198709058, 'valid', '2025-12-20 00:17:50', 'hadir', NULL, NULL),
(5, 5, 8, -7.6498202263171775, 110.41319353214884, 29.382694675745, 'valid', '2025-12-22 01:18:02', 'hadir', NULL, NULL),
(8, 16, 8, -7.649859, 110.413132, 29.800198709058, 'valid', '2025-12-22 11:40:53', 'izin', 'VFDVFD', 'http://localhost/Presensi-SMK/public/uploads/izin/bukti-6948cbd516828.jpeg'),
(9, 17, 8, 0, 0, 0, 'valid', '2025-12-22 11:50:15', 'izin', ' ;\'l,\';,', NULL),
(10, 18, 7, 0, 0, 0, 'valid', '2025-12-23 15:55:32', 'izin', 'niunin', 'http://localhost/Presensi-SMK/public/uploads/izin/bukti-694a59045bae8.jpeg'),
(11, 19, 3, 0, 0, 0, 'valid', '2025-12-24 00:07:14', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(12, 19, 5, 0, 0, 0, 'valid', '2025-12-24 00:07:14', 'hadir', 'Tidak hadir saat sesi ditutup', ''),
(13, 19, 7, 0, 0, 0, 'valid', '2025-12-24 00:07:14', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(14, 19, 8, 0, 0, 0, 'valid', '2025-12-24 00:07:14', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(15, 20, 5, 0, 0, 0, 'valid', '2025-12-24 00:08:53', 'hadir', '', ''),
(16, 20, 3, 0, 0, 0, 'valid', '2025-12-24 00:09:11', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(17, 20, 7, 0, 0, 0, 'valid', '2025-12-24 00:09:11', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(18, 20, 8, 0, 0, 0, 'valid', '2025-12-24 00:09:11', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(19, 21, 3, 0, 0, 0, 'valid', '2025-12-24 23:08:24', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(20, 21, 5, 0, 0, 0, 'valid', '2025-12-24 23:08:24', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(21, 21, 7, 0, 0, 0, 'valid', '2025-12-24 23:08:24', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(22, 21, 8, 0, 0, 0, 'valid', '2025-12-24 23:08:24', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(23, 22, 3, 0, 0, 0, 'valid', '2025-12-24 23:19:15', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(24, 22, 5, 0, 0, 0, 'valid', '2025-12-24 23:19:15', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(25, 22, 7, 0, 0, 0, 'valid', '2025-12-24 23:19:15', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(26, 22, 8, 0, 0, 0, 'valid', '2025-12-24 23:19:15', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(27, 23, 3, 0, 0, 0, 'valid', '2025-12-24 23:50:07', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(28, 23, 5, 0, 0, 0, 'valid', '2025-12-24 23:50:07', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(29, 23, 7, 0, 0, 0, 'valid', '2025-12-24 23:50:07', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(30, 23, 8, 0, 0, 0, 'valid', '2025-12-24 23:50:07', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(31, 24, 3, 0, 0, 0, 'valid', '2025-12-24 23:54:07', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(32, 24, 5, 0, 0, 0, 'valid', '2025-12-24 23:54:07', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(33, 24, 7, 0, 0, 0, 'valid', '2025-12-24 23:54:07', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(34, 24, 8, 0, 0, 0, 'valid', '2025-12-24 23:54:07', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(35, 25, 3, 0, 0, 0, 'valid', '2025-12-25 00:05:40', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(36, 25, 5, 0, 0, 0, 'valid', '2025-12-25 00:05:40', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(37, 25, 7, 0, 0, 0, 'valid', '2025-12-25 00:05:40', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(38, 25, 8, 0, 0, 0, 'valid', '2025-12-25 00:05:40', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(39, 28, 3, 0, 0, 0, 'valid', '2025-12-25 00:08:06', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(40, 28, 5, 0, 0, 0, 'valid', '2025-12-25 00:08:06', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(41, 28, 7, 0, 0, 0, 'valid', '2025-12-25 00:08:06', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(42, 28, 8, 0, 0, 0, 'valid', '2025-12-25 00:08:06', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(43, 26, 3, 0, 0, 0, 'valid', '2025-12-25 00:22:44', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(44, 26, 5, 0, 0, 0, 'valid', '2025-12-25 00:22:44', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(45, 26, 7, 0, 0, 0, 'valid', '2025-12-25 00:22:44', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(46, 26, 8, 0, 0, 0, 'valid', '2025-12-25 00:22:44', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(47, 27, 3, 0, 0, 0, 'valid', '2025-12-25 00:22:44', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(48, 27, 5, 0, 0, 0, 'valid', '2025-12-25 00:22:44', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(49, 27, 7, 0, 0, 0, 'valid', '2025-12-25 00:22:44', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(50, 27, 8, 0, 0, 0, 'valid', '2025-12-25 00:22:44', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(51, 29, 3, 0, 0, 0, 'valid', '2025-12-25 00:22:56', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(52, 29, 5, 0, 0, 0, 'valid', '2025-12-25 00:22:56', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(53, 29, 7, 0, 0, 0, 'valid', '2025-12-25 00:22:56', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(54, 29, 8, 0, 0, 0, 'valid', '2025-12-25 00:22:56', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(55, 30, 3, 0, 0, 0, 'valid', '2025-12-25 12:23:41', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(56, 30, 5, 0, 0, 0, 'valid', '2025-12-25 12:23:41', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(57, 30, 7, 0, 0, 0, 'valid', '2025-12-25 12:23:41', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(58, 30, 8, 0, 0, 0, 'valid', '2025-12-25 12:23:41', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(59, 31, 3, 0, 0, 0, 'valid', '2025-12-25 12:48:27', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(60, 31, 5, 0, 0, 0, 'valid', '2025-12-25 12:48:27', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(61, 31, 7, 0, 0, 0, 'valid', '2025-12-25 12:48:28', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(62, 31, 8, 0, 0, 0, 'valid', '2025-12-25 12:48:28', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(63, 32, 3, 0, 0, 0, 'valid', '2025-12-25 12:56:11', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(64, 32, 5, 0, 0, 0, 'valid', '2025-12-25 12:56:11', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(65, 32, 7, 0, 0, 0, 'valid', '2025-12-25 12:56:11', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(66, 32, 8, 0, 0, 0, 'valid', '2025-12-25 12:56:11', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(67, 33, 3, 0, 0, 0, 'valid', '2025-12-25 13:02:24', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(68, 33, 5, 0, 0, 0, 'valid', '2025-12-25 13:02:24', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(69, 33, 7, 0, 0, 0, 'valid', '2025-12-25 13:02:25', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(70, 33, 8, 0, 0, 0, 'valid', '2025-12-25 13:02:25', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(71, 34, 3, 0, 0, 0, 'valid', '2025-12-25 13:04:53', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(72, 34, 5, 0, 0, 0, 'valid', '2025-12-25 13:04:53', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(73, 34, 7, 0, 0, 0, 'valid', '2025-12-25 13:04:54', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(74, 34, 8, 0, 0, 0, 'valid', '2025-12-25 13:04:54', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(75, 35, 3, 0, 0, 0, 'valid', '2025-12-25 13:10:05', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(76, 35, 5, 0, 0, 0, 'valid', '2025-12-25 13:10:05', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(77, 35, 7, 0, 0, 0, 'valid', '2025-12-25 13:10:05', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(78, 35, 8, 0, 0, 0, 'valid', '2025-12-25 13:10:05', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(79, 36, 3, 0, 0, 0, 'valid', '2025-12-25 13:21:26', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(80, 36, 5, 0, 0, 0, 'valid', '2025-12-25 13:21:26', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(81, 36, 7, 0, 0, 0, 'valid', '2025-12-25 13:21:27', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(82, 36, 8, 0, 0, 0, 'valid', '2025-12-25 13:21:27', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(83, 37, 3, 0, 0, 0, 'valid', '2025-12-25 13:25:46', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(84, 37, 5, 0, 0, 0, 'valid', '2025-12-25 13:25:46', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(85, 37, 7, 0, 0, 0, 'valid', '2025-12-25 13:25:46', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(86, 37, 8, 0, 0, 0, 'valid', '2025-12-25 13:25:46', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(87, 38, 3, 0, 0, 0, 'valid', '2025-12-25 14:03:26', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(88, 38, 5, 0, 0, 0, 'valid', '2025-12-25 14:03:26', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(89, 38, 7, 0, 0, 0, 'valid', '2025-12-25 14:03:47', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(90, 38, 8, 0, 0, 0, 'valid', '2025-12-25 14:03:47', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(91, 39, 3, 0, 0, 0, 'valid', '2025-12-25 14:11:08', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(92, 39, 5, 0, 0, 0, 'valid', '2025-12-25 14:11:08', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(93, 39, 7, 0, 0, 0, 'valid', '2025-12-25 14:11:10', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(94, 39, 8, 0, 0, 0, 'valid', '2025-12-25 14:11:11', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(95, 40, 3, 0, 0, 0, 'valid', '2026-01-02 17:20:46', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(96, 40, 5, 0, 0, 0, 'valid', '2026-01-02 17:20:46', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(97, 40, 7, 0, 0, 0, 'valid', '2026-01-02 17:20:49', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(98, 40, 8, 0, 0, 0, 'valid', '2026-01-02 17:20:49', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(99, 41, 3, 0, 0, 0, 'valid', '2026-01-03 11:03:33', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(100, 41, 5, 0, 0, 0, 'valid', '2026-01-03 11:03:33', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(101, 41, 7, 0, 0, 0, 'valid', '2026-01-03 11:03:35', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(102, 41, 8, 0, 0, 0, 'valid', '2026-01-03 11:03:35', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(103, 42, 8, -7.6498202263171775, 110.41319353214884, 29.382694675745, 'valid', '2026-01-03 11:04:16', 'hadir', NULL, NULL),
(104, 42, 3, 0, 0, 0, 'valid', '2026-01-03 11:04:33', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(105, 42, 5, 0, 0, 0, 'valid', '2026-01-03 11:04:33', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(106, 42, 7, 0, 0, 0, 'valid', '2026-01-03 11:04:36', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(107, 43, 3, 0, 0, 0, 'valid', '2026-01-06 22:19:02', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(108, 43, 5, 0, 0, 0, 'valid', '2026-01-06 22:19:02', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(109, 43, 7, 0, 0, 0, 'valid', '2026-01-06 22:19:05', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(110, 43, 8, 0, 0, 0, 'valid', '2026-01-06 22:19:05', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(111, NULL, 3, 0, 0, 0, 'valid', '2026-01-08 21:35:06', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(112, NULL, 5, 0, 0, 0, 'valid', '2026-01-08 21:35:06', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(113, NULL, 7, 0, 0, 0, 'valid', '2026-01-08 21:35:09', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(114, NULL, 8, 0, 0, 0, 'valid', '2026-01-08 21:35:09', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(115, 45, 3, 0, 0, 0, 'valid', '2026-01-08 21:40:30', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(116, 45, 5, 0, 0, 0, 'valid', '2026-01-08 21:40:30', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(117, 45, 7, 0, 0, 0, 'valid', '2026-01-08 21:40:35', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(118, 45, 8, 0, 0, 0, 'valid', '2026-01-08 21:40:35', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(119, 46, 3, 0, 0, 0, 'valid', '2026-01-09 00:51:05', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(120, 46, 5, 0, 0, 0, 'valid', '2026-01-09 00:51:05', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(121, 46, 7, 0, 0, 0, 'valid', '2026-01-09 00:51:09', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(122, 46, 8, 0, 0, 0, 'valid', '2026-01-09 00:51:09', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(123, 47, 3, 0, 0, 0, 'valid', '2026-01-09 13:26:38', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(124, 47, 5, 0, 0, 0, 'valid', '2026-01-09 13:26:38', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(125, 47, 7, 0, 0, 0, 'valid', '2026-01-09 13:26:43', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(126, 47, 8, 0, 0, 0, 'valid', '2026-01-09 13:26:43', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(127, 48, 3, 0, 0, 0, 'valid', '2026-01-11 21:03:03', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(128, 48, 5, 0, 0, 0, 'valid', '2026-01-11 21:03:03', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(129, 48, 7, 0, 0, 0, 'valid', '2026-01-11 21:03:07', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(130, 48, 8, 0, 0, 0, 'valid', '2026-01-11 21:03:07', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(131, 49, 3, 0, 0, 0, 'valid', '2026-01-12 11:55:42', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(132, 49, 5, 0, 0, 0, 'valid', '2026-01-12 11:55:42', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(133, 49, 7, 0, 0, 0, 'valid', '2026-01-12 11:55:45', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(134, 49, 8, 0, 0, 0, 'valid', '2026-01-12 11:55:45', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(135, 50, 3, 0, 0, 0, 'valid', '2026-01-12 11:57:18', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(136, 50, 5, 0, 0, 0, 'valid', '2026-01-12 11:57:18', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(137, 50, 7, 0, 0, 0, 'valid', '2026-01-12 11:57:21', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(138, 50, 8, 0, 0, 0, 'valid', '2026-01-12 11:57:21', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(139, 51, 3, 0, 0, 0, 'valid', '2026-01-12 11:58:12', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(140, 51, 5, 0, 0, 0, 'valid', '2026-01-12 11:58:12', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(141, 51, 7, 0, 0, 0, 'valid', '2026-01-12 11:58:14', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(142, 51, 8, 0, 0, 0, 'valid', '2026-01-12 11:58:14', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(143, 52, 3, 0, 0, 0, 'valid', '2026-01-12 12:02:55', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(144, 52, 5, 0, 0, 0, 'valid', '2026-01-12 12:02:55', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(145, 52, 7, 0, 0, 0, 'valid', '2026-01-12 12:02:58', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(146, 52, 8, 0, 0, 0, 'valid', '2026-01-12 12:02:58', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(147, 53, 3, 0, 0, 0, 'valid', '2026-01-12 12:04:53', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(148, 53, 5, 0, 0, 0, 'valid', '2026-01-12 12:04:53', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(149, 53, 7, 0, 0, 0, 'valid', '2026-01-12 12:04:56', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(150, 53, 8, 0, 0, 0, 'valid', '2026-01-12 12:04:56', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(151, 54, 3, 0, 0, 0, 'valid', '2026-01-12 12:06:50', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(152, 54, 5, 0, 0, 0, 'valid', '2026-01-12 12:06:50', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(153, 54, 7, 0, 0, 0, 'valid', '2026-01-12 12:06:53', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(154, 54, 8, 0, 0, 0, 'valid', '2026-01-12 12:06:53', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(155, 55, 3, 0, 0, 0, 'valid', '2026-01-12 12:14:20', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(156, 55, 5, 0, 0, 0, 'valid', '2026-01-12 12:14:20', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(157, 55, 7, 0, 0, 0, 'valid', '2026-01-12 12:14:23', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(158, 55, 8, 0, 0, 0, 'valid', '2026-01-12 12:14:23', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(159, 56, 3, 0, 0, 0, 'valid', '2026-01-12 12:15:59', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(160, 56, 5, 0, 0, 0, 'valid', '2026-01-12 12:15:59', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(161, 56, 7, 0, 0, 0, 'valid', '2026-01-12 12:16:02', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(162, 56, 8, 0, 0, 0, 'valid', '2026-01-12 12:16:02', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(163, 57, 3, 0, 0, 0, 'valid', '2026-01-13 13:33:21', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(164, 57, 5, 0, 0, 0, 'valid', '2026-01-13 13:33:21', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(165, 57, 7, 0, 0, 0, 'valid', '2026-01-13 13:33:24', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(166, 57, 8, 0, 0, 0, 'valid', '2026-01-13 13:33:24', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(167, 58, 3, 0, 0, 0, 'valid', '2026-01-13 13:36:44', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(168, 58, 5, 0, 0, 0, 'valid', '2026-01-13 13:36:44', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(169, 58, 7, 0, 0, 0, 'valid', '2026-01-13 13:36:47', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(170, 58, 8, 0, 0, 0, 'valid', '2026-01-13 13:36:47', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(171, 59, 3, 0, 0, 0, 'valid', '2026-01-13 13:38:44', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(172, 59, 5, 0, 0, 0, 'valid', '2026-01-13 13:38:44', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(173, 59, 7, 0, 0, 0, 'valid', '2026-01-13 13:38:47', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(174, 59, 8, 0, 0, 0, 'valid', '2026-01-13 13:38:47', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(175, 60, 3, 0, 0, 0, 'valid', '2026-01-20 00:51:56', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(176, 60, 5, 0, 0, 0, 'valid', '2026-01-20 00:51:56', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(177, 60, 7, 0, 0, 0, 'valid', '2026-01-20 00:52:00', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(178, 60, 8, 0, 0, 0, 'valid', '2026-01-20 00:52:00', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(179, 61, 8, 0, 0, 0, 'valid', '2026-01-25 18:59:22', 'izin', 'sad', NULL),
(180, 61, 7, 0, 0, 0, 'valid', '2026-01-25 18:59:41', 'izin', 'dsa', NULL),
(181, 61, 3, 0, 0, 0, 'valid', '2026-01-28 15:25:50', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(182, 61, 5, 0, 0, 0, 'valid', '2026-01-28 15:25:50', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(183, 62, 3, 0, 0, 0, 'valid', '2026-01-30 21:11:19', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(184, 62, 5, 0, 0, 0, 'valid', '2026-01-30 21:11:19', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(185, 62, 7, 0, 0, 0, 'valid', '2026-01-30 21:11:23', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(186, 62, 8, 0, 0, 0, 'valid', '2026-01-30 21:11:23', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(187, 63, 3, 0, 0, 0, 'valid', '2026-01-31 22:42:03', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(188, 63, 5, 0, 0, 0, 'valid', '2026-01-31 22:42:03', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(189, 63, 7, 0, 0, 0, 'valid', '2026-01-31 22:42:08', 'alpha', 'Tidak hadir saat sesi ditutup', ''),
(190, 63, 8, 0, 0, 0, 'valid', '2026-01-31 22:42:08', 'alpha', 'Tidak hadir saat sesi ditutup', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `presensi_sekolah_sesi`
--

CREATE TABLE `presensi_sekolah_sesi` (
  `id` int NOT NULL,
  `waktu_buka` datetime NOT NULL,
  `waktu_tutup` datetime NOT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  `created_by` int DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `presensi_sekolah_sesi`
--

INSERT INTO `presensi_sekolah_sesi` (`id`, `waktu_buka`, `waktu_tutup`, `status`, `created_by`, `note`, `created_at`) VALUES
(1, '2025-11-07 22:46:00', '2025-11-07 22:49:00', 'closed', 1, '', '2025-11-07 22:46:46'),
(2, '2025-11-07 22:53:00', '2025-11-07 23:10:50', 'closed', 1, '', '2025-11-07 22:53:12'),
(3, '2025-11-07 23:03:00', '2025-11-07 23:21:00', 'closed', 1, '', '2025-11-07 23:03:12'),
(4, '2025-12-20 00:17:00', '2025-12-20 00:20:00', 'closed', 1, 'nlknlk', '2025-12-20 00:17:24'),
(5, '2025-12-22 01:14:00', '2025-12-22 01:30:00', 'closed', 1, '', '2025-12-22 01:17:33'),
(6, '2025-12-22 08:53:00', '2025-12-22 09:05:00', 'closed', 1, '', '2025-12-22 08:53:47'),
(7, '2025-12-22 09:03:00', '2025-12-22 09:10:00', 'closed', 1, '', '2025-12-22 09:03:29'),
(8, '2025-12-22 09:04:00', '2025-12-22 09:10:00', 'closed', 1, '', '2025-12-22 09:04:42'),
(9, '2025-12-22 09:08:00', '2025-12-22 09:24:00', 'closed', 1, '', '2025-12-22 09:08:08'),
(10, '2025-12-22 09:23:00', '2025-12-22 09:36:00', 'closed', 1, '', '2025-12-22 09:23:56'),
(11, '2025-12-22 09:44:00', '2025-12-22 09:56:00', 'closed', 1, '', '2025-12-22 09:44:16'),
(12, '2025-12-22 10:32:00', '2025-12-22 10:43:00', 'closed', 1, '', '2025-12-22 10:32:25'),
(13, '2025-12-22 10:39:00', '2025-12-22 10:46:00', 'closed', 1, '', '2025-12-22 10:39:17'),
(14, '2025-12-22 11:00:00', '2025-12-22 11:13:00', 'closed', 1, '', '2025-12-22 11:00:23'),
(15, '2025-12-22 11:06:00', '2025-12-22 11:16:00', 'closed', 1, '', '2025-12-22 11:07:01'),
(16, '2025-12-22 11:40:00', '2025-12-22 11:44:00', 'closed', 1, '', '2025-12-22 11:40:35'),
(17, '2025-12-22 11:49:00', '2025-12-22 11:53:00', 'closed', 1, '', '2025-12-22 11:49:57'),
(18, '2025-12-23 15:54:00', '2025-12-23 15:59:00', 'closed', 1, '', '2025-12-23 15:55:09'),
(19, '2025-12-24 00:07:00', '2025-12-24 00:09:00', 'closed', 1, '', '2025-12-24 00:07:11'),
(20, '2025-12-24 00:08:00', '2025-12-24 00:12:00', 'closed', 1, '', '2025-12-24 00:08:38'),
(21, '2025-12-24 23:08:00', '2025-12-24 23:11:00', 'closed', 1, '', '2025-12-24 23:08:20'),
(22, '2025-12-24 23:19:00', '2025-12-24 23:21:00', 'closed', 1, '', '2025-12-24 23:19:13'),
(23, '2025-12-24 23:49:00', '2025-12-24 23:52:00', 'closed', 9, '', '2025-12-24 23:50:05'),
(24, '2025-12-24 23:53:00', '2025-12-24 23:57:00', 'closed', 9, '', '2025-12-24 23:54:04'),
(25, '2025-12-25 00:05:00', '2025-12-25 00:07:00', 'closed', 9, '', '2025-12-25 00:05:38'),
(26, '2025-12-25 00:07:00', '2025-12-25 00:09:00', 'closed', 9, '', '2025-12-25 00:07:54'),
(27, '2025-12-25 00:07:00', '2025-12-25 00:09:00', 'closed', 9, '', '2025-12-25 00:07:54'),
(28, '2025-12-25 00:07:00', '2025-12-25 00:09:00', 'closed', 9, '', '2025-12-25 00:07:54'),
(29, '2025-12-25 00:22:00', '2025-12-25 00:26:00', 'closed', 9, '', '2025-12-25 00:22:54'),
(30, '2025-12-25 12:23:00', '2025-12-25 12:26:00', 'closed', 9, '', '2025-12-25 12:23:39'),
(31, '2025-12-25 12:48:00', '2025-12-25 12:51:00', 'closed', 9, '', '2025-12-25 12:48:25'),
(32, '2025-12-25 12:56:00', '2025-12-25 12:58:00', 'closed', 9, '', '2025-12-25 12:56:09'),
(33, '2025-12-25 13:02:00', '2025-12-25 13:04:00', 'closed', 9, '', '2025-12-25 13:02:22'),
(34, '2025-12-25 13:04:00', '2025-12-25 13:07:00', 'closed', 9, '', '2025-12-25 13:04:51'),
(35, '2025-12-25 13:09:00', '2025-12-25 13:12:00', 'closed', 9, '', '2025-12-25 13:10:03'),
(36, '2025-12-25 13:15:00', '2025-12-25 13:30:00', 'closed', 9, '', '2025-12-25 13:15:17'),
(37, '2025-12-25 13:25:00', '2025-12-25 13:30:00', 'closed', 9, '', '2025-12-25 13:25:44'),
(38, '2025-12-25 14:03:00', '2025-12-25 14:05:00', 'closed', 9, '', '2025-12-25 14:03:24'),
(39, '2025-12-25 14:11:00', '2025-12-25 14:14:00', 'closed', 9, '', '2025-12-25 14:11:06'),
(40, '2026-01-02 15:23:00', '2026-01-02 15:25:00', 'closed', 1, '', '2026-01-02 15:23:32'),
(41, '2026-01-03 11:02:00', '2026-01-03 11:03:00', 'closed', 1, '', '2026-01-03 11:02:57'),
(42, '2026-01-03 11:03:00', '2026-01-03 11:07:00', 'closed', 1, '', '2026-01-03 11:04:06'),
(43, '2026-01-06 22:17:00', '2026-01-06 22:19:00', 'closed', 1, '', '2026-01-06 22:17:42'),
(45, '2026-01-08 21:40:00', '2026-01-08 23:59:59', 'closed', NULL, 'Sesi otomatis - Kamis', '2026-01-08 21:40:07'),
(46, '2026-01-09 00:50:00', '2026-01-09 00:53:00', 'closed', 9, '', '2026-01-09 00:51:01'),
(47, '2026-01-09 13:23:00', '2026-01-09 13:26:00', 'closed', 1, '', '2026-01-09 13:23:38'),
(48, '2026-01-11 21:00:00', '2026-01-11 21:03:00', 'closed', 1, '', '2026-01-11 21:01:04'),
(49, '2026-01-12 07:00:00', '2026-01-12 23:59:59', 'closed', NULL, 'Sesi otomatis - Senin', '2026-01-12 11:45:26'),
(50, '2026-01-12 11:56:00', '2026-01-13 11:57:00', 'closed', 1, '', '2026-01-12 11:57:12'),
(51, '2026-01-12 11:57:00', '2026-01-20 11:58:00', 'closed', 1, '', '2026-01-12 11:58:08'),
(52, '2026-01-11 12:02:00', '2026-01-13 12:02:00', 'closed', 1, '', '2026-01-12 12:02:41'),
(53, '2026-01-12 12:04:00', '2026-01-13 12:04:00', 'closed', 1, '', '2026-01-12 12:04:49'),
(54, '2026-01-12 12:06:00', '2026-01-12 12:08:00', 'closed', 1, '', '2026-01-12 12:06:48'),
(55, '2026-01-12 12:14:00', '2026-01-12 12:18:00', 'closed', 1, '', '2026-01-12 12:14:16'),
(56, '2026-01-12 12:15:00', '2026-01-12 12:18:00', 'closed', 9, '', '2026-01-12 12:15:55'),
(57, '2026-01-13 07:00:00', '2026-01-13 23:59:59', 'closed', NULL, 'Sesi otomatis - Selasa', '2026-01-13 13:29:23'),
(58, '2026-01-13 13:36:00', '2026-01-13 13:38:00', 'closed', 1, '', '2026-01-13 13:36:41'),
(59, '2026-01-13 13:38:00', '2026-01-13 13:40:00', 'closed', 1, '', '2026-01-13 13:38:42'),
(60, '2026-01-19 07:00:00', '2026-01-19 23:59:59', 'closed', NULL, 'Sesi otomatis - Senin', '2026-01-19 10:30:57'),
(61, '2026-01-25 18:59:00', '2026-01-25 20:01:00', 'closed', 1, '', '2026-01-25 18:59:11'),
(62, '2026-01-28 07:00:00', '2026-01-28 23:59:59', 'closed', NULL, 'Sesi otomatis - Rabu', '2026-01-28 15:25:49'),
(63, '2026-01-30 07:00:00', '2026-01-30 23:59:59', 'closed', NULL, 'Sesi otomatis - Jumat', '2026-01-30 20:51:46');

-- --------------------------------------------------------

--
-- Table structure for table `presensi_sesi`
--

CREATE TABLE `presensi_sesi` (
  `id` int NOT NULL,
  `kelas_id` int NOT NULL,
  `guru_id` int NOT NULL,
  `waktu_buka` datetime NOT NULL,
  `waktu_tutup` datetime DEFAULT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `presensi_sesi`
--

INSERT INTO `presensi_sesi` (`id`, `kelas_id`, `guru_id`, `waktu_buka`, `waktu_tutup`, `status`) VALUES
(1, 1, 2, '2025-11-02 22:21:03', '2025-11-02 22:21:38', 'closed'),
(3, 1, 2, '2025-11-02 22:25:07', '2025-11-02 22:25:14', 'closed'),
(4, 1, 2, '2025-11-02 22:33:10', '2025-11-03 13:58:27', 'closed'),
(5, 3, 2, '2025-12-22 09:26:48', '2025-12-22 09:27:07', 'closed'),
(6, 3, 2, '2025-12-22 09:36:01', '2025-12-22 09:36:13', 'closed'),
(7, 1, 2, '2025-12-23 22:35:03', '2025-12-24 00:02:57', 'closed'),
(8, 1, 2, '2025-12-24 00:03:45', '2025-12-24 00:04:03', 'closed'),
(9, 1, 2, '2025-12-25 12:07:48', '2025-12-25 12:07:53', 'closed'),
(10, 1, 2, '2025-12-25 12:21:54', '2025-12-25 12:22:00', 'closed'),
(11, 1, 2, '2026-01-02 22:41:07', '2026-01-02 22:41:15', 'closed'),
(12, 1, 2, '2026-01-06 22:14:28', '2026-01-06 22:15:05', 'closed'),
(13, 1, 2, '2026-01-08 13:12:45', '2026-01-08 13:12:59', 'closed');

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
(1, 3, 1),
(6, 5, 1),
(4, 7, 3),
(5, 8, 1),
(7, 8, 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','admin_kesiswaan','guru','siswa','orangtua') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin Utama', 'admin@smk7.sch.id', 'admin123', 'admin', '2025-10-31 07:43:18'),
(2, 'Guru Informatika', 'guru@smk7.sch.id', 'guru123', 'guru', '2025-10-31 07:43:18'),
(3, 'Luthfi', 'siswa@smk7.sch.id', 'siswa123', 'siswa', '2025-10-31 07:43:18'),
(5, 'Fakhri', 'fakhri@gmail.com', '123', 'siswa', '2025-11-02 02:43:43'),
(6, 'Bagas', 'bagas@gmail.com', 'bagas123', 'guru', '2025-11-06 09:01:04'),
(7, 'Habib', 'habib@gmail.com', 'admin123', 'siswa', '2025-11-07 07:14:24'),
(8, 'Zola', 'zola@gmail.com', '123', 'siswa', '2025-12-01 04:07:59'),
(9, 'Yoga', 'yoga@gmail.com', 'yoga123', 'admin_kesiswaan', '2025-12-17 04:56:50'),
(10, 'admin k2', 'admink2@smk7.sch.id', 'admink2', 'admin_kesiswaan', '2026-01-30 14:10:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buku_induk`
--
ALTER TABLE `buku_induk`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_buku_induk` (`user_id`);

--
-- Indexes for table `buku_induk_dokumen`
--
ALTER TABLE `buku_induk_dokumen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buku_induk_id` (`buku_induk_id`);

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
-- Indexes for table `lokasi_sekolah`
--
ALTER TABLE `lokasi_sekolah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `presensi_kelas`
--
ALTER TABLE `presensi_kelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_presensi_kelas_user` (`user_id`),
  ADD KEY `fk_presensi_kelas_kelas` (`kelas_id`),
  ADD KEY `presensi_sesi_id` (`presensi_sesi_id`);

--
-- Indexes for table `presensi_sekolah`
--
ALTER TABLE `presensi_sekolah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_presensi_sekolah_user` (`user_id`),
  ADD KEY `fk_presensi_sekolah_sesi` (`presensi_sekolah_sesi_id`);

--
-- Indexes for table `presensi_sekolah_sesi`
--
ALTER TABLE `presensi_sekolah_sesi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `presensi_sesi`
--
ALTER TABLE `presensi_sesi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `guru_id` (`guru_id`);

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
-- AUTO_INCREMENT for table `buku_induk`
--
ALTER TABLE `buku_induk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `buku_induk_dokumen`
--
ALTER TABLE `buku_induk_dokumen`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `laporan_kemajuan`
--
ALTER TABLE `laporan_kemajuan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `lokasi_sekolah`
--
ALTER TABLE `lokasi_sekolah`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `presensi_kelas`
--
ALTER TABLE `presensi_kelas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `presensi_sekolah`
--
ALTER TABLE `presensi_sekolah`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;

--
-- AUTO_INCREMENT for table `presensi_sekolah_sesi`
--
ALTER TABLE `presensi_sekolah_sesi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `presensi_sesi`
--
ALTER TABLE `presensi_sesi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `siswa_kelas`
--
ALTER TABLE `siswa_kelas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `buku_induk`
--
ALTER TABLE `buku_induk`
  ADD CONSTRAINT `fk_buku_induk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `buku_induk_dokumen`
--
ALTER TABLE `buku_induk_dokumen`
  ADD CONSTRAINT `fk_buku_induk_dokumen` FOREIGN KEY (`buku_induk_id`) REFERENCES `buku_induk` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `lokasi_sekolah`
--
ALTER TABLE `lokasi_sekolah`
  ADD CONSTRAINT `lokasi_sekolah_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `presensi_kelas`
--
ALTER TABLE `presensi_kelas`
  ADD CONSTRAINT `fk_presensi_kelas_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_presensi_kelas_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `presensi_kelas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `presensi_kelas_ibfk_2` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `presensi_kelas_ibfk_3` FOREIGN KEY (`presensi_sesi_id`) REFERENCES `presensi_sesi` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `presensi_sekolah`
--
ALTER TABLE `presensi_sekolah`
  ADD CONSTRAINT `fk_presensi_sekolah_sesi` FOREIGN KEY (`presensi_sekolah_sesi_id`) REFERENCES `presensi_sekolah_sesi` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_presensi_sekolah_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `presensi_sekolah_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `presensi_sesi`
--
ALTER TABLE `presensi_sesi`
  ADD CONSTRAINT `presensi_sesi_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `presensi_sesi_ibfk_2` FOREIGN KEY (`guru_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
