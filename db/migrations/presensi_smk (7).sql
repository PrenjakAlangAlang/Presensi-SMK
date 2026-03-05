-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 04, 2026 at 01:56 PM
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
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nis` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nisn` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tempat_lahir` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text,
  `nama_ayah` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `nama_ibu` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `nama_wali` varchar(100) DEFAULT NULL,
  `no_telp_ortu` varchar(20) DEFAULT NULL,
  `email_ortu` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Email orang tua untuk notifikasi',
  `dokumen_pdf` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `buku_induk`
--

INSERT INTO `buku_induk` (`id`, `user_id`, `nama`, `nis`, `nisn`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `nama_ayah`, `nama_ibu`, `nama_wali`, `no_telp_ortu`, `email_ortu`, `dokumen_pdf`) VALUES
(1, 5, 'Fakhri Tajuddin Hidayat', '7627', '11134567', 'Sleman', '2025-12-01', 'Ngampel', '', '', '', '', '', 'http://localhost/Presensi-SMK/public/uploads/buku_induk/buku-induk-694239e508e54.pdf'),
(2, 8, 'Zola', '7627', '11134567', 'Yogyakarta', '2025-12-01', 'fgsdfs', 'dsfsdf', 'sdfdsfsd', 'vzxv', '', '', 'http://localhost/Presensi-SMK/public/uploads/buku_induk/buku-induk-697edf25009be.pdf'),
(3, 7, 'Habib Maulana', 'dsada', 'sdaddd', 'sdadas', '2026-02-01', 'dasdas', 'dsa', 'dasd', 'asdas', '', '', 'http://localhost/Presensi-SMK/public/uploads/buku_induk/buku-induk-697ede11e77e7.pdf'),
(4, 3, 'Luthfi Nurafiq Asshiddiqi', '7627', '12345566', 'Sleman', '2026-02-04', 'Pakem', 'Atun Budi', 'Adik Kristien', '', '', 'luthfinurafiq76@gmail.com', '/uploads/buku_induk/buku-induk-698dff45b9fd6.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `buku_induk_dokumen`
--

CREATE TABLE `buku_induk_dokumen` (
  `id` int NOT NULL,
  `buku_induk_id` int NOT NULL,
  `nama_file` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `dokumen_pdf` varchar(255) NOT NULL,
  `keterangan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `buku_induk_dokumen`
--

INSERT INTO `buku_induk_dokumen` (`id`, `buku_induk_id`, `nama_file`, `dokumen_pdf`, `keterangan`) VALUES
(4, 2, 'Jadwal Seminar Tugas Akhir Bulan Februari 2026 Prodi SI.pdf', 'http://localhost/Presensi-SMK/public/uploads/buku_induk/buku-induk-698de74ad6972.pdf', 'seminar'),
(5, 4, 'Ahmad+Ari+Gunawan+S.pdf', '/uploads/buku_induk/buku-induk-698dff45bac5e.pdf', 'tetggdfs'),
(6, 4, 'Socialmediaandgeography.pdf', '/uploads/buku_induk/buku-induk-698dff45bb2c5.pdf', '');

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` int NOT NULL,
  `nama_kelas` varchar(100) NOT NULL,
  `tahun_ajaran` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `nama_kelas`, `tahun_ajaran`) VALUES
(1, 'X TKR 1', '2025/2026');

-- --------------------------------------------------------

--
-- Table structure for table `kelas_mata_pelajaran`
--

CREATE TABLE `kelas_mata_pelajaran` (
  `id` int NOT NULL,
  `kelas_id` int NOT NULL,
  `mata_pelajaran_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kelas_mata_pelajaran`
--

INSERT INTO `kelas_mata_pelajaran` (`id`, `kelas_id`, `mata_pelajaran_id`) VALUES
(1, 1, 1);

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
(13, 1, 2, '2026-01-08', 'h8nu', '2026-01-08 06:12:59'),
(14, 1, 2, '2026-02-01', 'kgkkjh', '2026-02-01 04:08:04'),
(15, 1, 2, '2026-02-01', 'lhohi', '2026-02-01 04:54:57'),
(16, 1, 2, '2026-02-01', 'nlk;', '2026-02-01 04:57:16'),
(17, 1, 2, '2026-02-04', 'adjasd', '2026-02-04 06:55:24'),
(18, 1, 2, '2026-02-10', 'fsdf', '2026-02-10 06:52:48'),
(19, 1, 2, '2026-03-02', 'hjvjvh', '2026-03-02 06:37:18'),
(20, 1, 2, '2026-03-02', 'hjvjvh', '2026-03-02 06:37:18');

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
  `updated_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lokasi_sekolah`
--

INSERT INTO `lokasi_sekolah` (`id`, `nama_sekolah`, `latitude`, `longitude`, `radius_presensi`, `updated_by`) VALUES
(1, 'SMK Negeri 7 Yogyakarta', -7.7956, 110.3695, 100, 1),
(2, 'SMK Negeri 7 Yogyakarta', -7.64961, 110.413032, 100, 1),
(3, 'SMK Negeri 7 Yogyakarta', -7.652036, 110.412129, 300, 1),
(4, 'SMK Negeri 7 Yogyakarta', -7.652164, 110.41374, 400, 1),
(5, 'SMK Negeri 7 Yogyakarta', -7.649973, 110.412988, 400, 1),
(6, 'SMK Negeri 7 Yogyakarta', -7.649731, 110.416681, 400, 1),
(7, 'SMK Negeri 7 Yogyakarta', -7.652781, 110.408629, 400, 1),
(8, 'SMK Negeri 7 Yogyakarta', -7.649859, 110.413132, 400, 1),
(9, 'SMK Negeri 7 Yogyakarta', -7.648272, 110.41709, 400, 1),
(10, 'SMK Negeri 7 Yogyakarta', -7.649859, 110.413132, 400, 1),
(11, 'SMK Negeri 7 Yogyakarta', -7.654622, 110.42154, 400, 1),
(12, 'SMK Negeri 7 Yogyakarta', -7.649859, 110.413132, 400, 1),
(13, 'SMK Negeri 7 Yogyakarta', -7.651675, 110.419774, 400, 1),
(14, 'SMK Negeri 7 Yogyakarta', -7.649859, 110.413132, 400, 1),
(15, 'SMK Negeri 7 Yogyakarta', -7.660007, 110.422757, 400, 1),
(16, 'SMK Negeri 7 Yogyakarta', -7.660007, 110.422757, 1000, 1),
(17, 'SMK Negeri 7 Yogyakarta', -7.65754, 110.42032, 1000, 1),
(18, 'SMK Negeri 7 Yogyakarta', -7.655498, 110.418442, 1000, 1),
(19, 'SMK Negeri 7 Yogyakarta', -7.656026, 110.419771, 1000, 1),
(20, 'SMK Negeri 7 Yogyakarta', -7.650471, 110.415945, 500, 1),
(21, 'SMK Negeri 7 Yogyakarta', -7.656349, 110.423524, 500, 1),
(22, 'SMK Negeri 7 Yogyakarta', -7.649859, 110.413132, 500, 1),
(23, 'SMK Negeri 7 Yogyakarta', -7.650058, 110.414706, 500, 1),
(24, 'SMK Negeri 7 Yogyakarta', -7.761999, 110.410064, 500, 1),
(25, 'SMK Negeri 7 Yogyakarta', -7.652045, 110.416287, 200, 1),
(26, 'SMK Negeri 7 Yogyakarta', -7.652045, 110.416287, 500, 1),
(27, 'SMK Negeri 7 Yogyakarta', -7.652045, 110.416287, 500, 1),
(28, 'SMK Negeri 7 Yogyakarta', -7.652045, 110.416287, 400, 1),
(29, 'SMK Negeri 7 Yogyakarta', -7.650696, 110.413415, 400, 1),
(30, 'SMK Negeri 7 Yogyakarta', -7.651483, 110.416638, 400, 1),
(31, 'SMK Negeri 7 Yogyakarta', -7.650563, 110.415016, 400, 1),
(32, 'SMK Negeri 7 Yogyakarta', -7.649518, 110.413088, 400, 1),
(33, 'SMK Negeri 7 Yogyakarta', -7.649859, 110.413132, 400, 1),
(34, 'SMK Negeri 7 Yogyakarta', -7.649859, 110.413132, 400, 1),
(35, 'SMK Negeri 7 Yogyakarta', -7.649612, 110.415243, 400, 1),
(36, 'SMK Negeri 7 Yogyakarta', -7.782389, 110.415879, 400, 1),
(37, 'SMK Negeri 7 Yogyakarta', -7.782389, 110.415879, 400, 1),
(38, 'SMK Negeri 7 Yogyakarta', -7.781402, 110.418032, 400, 1),
(39, 'SMK Negeri 7 Yogyakarta', -7.649497, 110.413696, 400, 1);

-- --------------------------------------------------------

--
-- Table structure for table `mata_pelajaran`
--

CREATE TABLE `mata_pelajaran` (
  `id` int NOT NULL,
  `nama_mata_pelajaran` varchar(100) NOT NULL,
  `guru_pengampu` int DEFAULT NULL,
  `jadwal` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `mata_pelajaran`
--

INSERT INTO `mata_pelajaran` (`id`, `nama_mata_pelajaran`, `guru_pengampu`, `jadwal`) VALUES
(1, 'Bahasa Indonesia', 2, 'Senin, 07:30-09:00'),
(3, 'Pendidikan Agama Islam', 2, 'Senin, 09:30-11:00'),
(4, 'Bahasa Inggris', 6, 'Senin, 11.00-12.30'),
(6, 'PKN', 6, 'Senin, 11.00-12.30'),
(7, 'PKN', 2, 'Senin, 11.00-12.30');

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
(17, 3, 1, 0, 0, 0, 'valid', '2026-01-08 13:12:53', 13, 'hadir', 'Tidak hadir saat sesi ditutup', ''),
(18, 5, 1, 0, 0, 0, 'valid', '2026-01-08 13:12:53', 13, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(19, 8, 1, 0, 0, 0, 'valid', '2026-01-08 13:12:57', 13, 'alpha', 'Tidak hadir saat sesi ditutup', ''),
(20, 8, 1, -7.649859, 110.413132, 0, 'valid', '2026-02-01 11:06:16', 14, 'hadir', NULL, NULL),
(21, 3, 1, 0, 0, 0, 'valid', '2026-02-01 11:07:59', 14, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(22, 5, 1, 0, 0, 0, 'valid', '2026-02-01 11:07:59', 14, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(23, 8, 1, -7.649859, 110.413132, 0, 'valid', '2026-02-01 11:24:24', 15, 'hadir', NULL, NULL),
(24, 3, 1, -7.649859, 110.413132, 0, 'valid', '2026-02-01 11:26:47', 15, 'hadir', NULL, NULL),
(25, 5, 1, 0, 0, 0, 'valid', '2026-02-01 11:54:54', 15, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(26, 8, 1, -7.649859, 110.413132, 0, 'valid', '2026-02-01 11:55:18', 16, 'hadir', NULL, NULL),
(27, 3, 1, 0, 0, 0, 'valid', '2026-02-01 11:56:56', 16, 'izin', 'nlknknkln', NULL),
(28, 5, 1, 0, 0, 0, 'valid', '2026-02-01 11:57:13', 16, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(29, 3, 1, 0, 0, 0, 'valid', '2026-02-04 13:55:20', 17, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(30, 8, 1, 0, 0, 0, 'valid', '2026-02-04 13:55:20', 17, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(31, 5, 1, 0, 0, 0, 'valid', '2026-02-04 13:55:23', 17, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(32, 3, 1, 0, 0, 0, 'valid', '2026-02-10 13:52:43', 18, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(33, 8, 1, 0, 0, 0, 'valid', '2026-02-10 13:52:43', 18, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(34, 5, 1, 0, 0, 0, 'valid', '2026-02-10 13:52:47', 18, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(35, 3, 1, 0, 0, 0, 'valid', '2026-03-02 13:36:57', 19, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(36, 8, 1, 0, 0, 0, 'valid', '2026-03-02 13:37:18', 19, 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(37, 5, 1, 0, 0, 0, 'valid', '2026-03-02 13:37:18', 19, 'alpha', 'Tidak hadir saat sesi ditutup', NULL);

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
(177, 60, 7, 0, 0, 0, 'valid', '2026-01-20 00:52:00', 'alpha', 'Tidak hadir saat sesi ditutup', ''),
(178, 60, 8, 0, 0, 0, 'valid', '2026-01-20 00:52:00', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(231, 75, 3, 0, 0, 0, 'valid', '2026-02-02 01:05:36', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(232, 75, 5, 0, 0, 0, 'valid', '2026-02-02 01:05:36', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(233, 75, 7, 0, 0, 0, 'valid', '2026-02-02 01:05:40', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(234, 75, 8, 0, 0, 0, 'valid', '2026-02-02 01:05:40', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(235, 76, 3, 0, 0, 0, 'valid', '2026-02-02 05:00:33', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(236, 76, 5, 0, 0, 0, 'valid', '2026-02-02 05:00:33', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(237, 76, 7, 0, 0, 0, 'valid', '2026-02-02 05:00:36', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(238, 76, 8, 0, 0, 0, 'valid', '2026-02-02 05:00:36', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(247, 79, 3, 0, 0, 0, 'valid', '2026-02-02 05:57:35', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(248, 79, 5, 0, 0, 0, 'valid', '2026-02-02 05:57:35', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(249, 79, 7, 0, 0, 0, 'valid', '2026-02-02 05:57:39', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(250, 79, 8, 0, 0, 0, 'valid', '2026-02-02 05:57:39', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(251, 80, 3, 0, 0, 0, 'valid', '2026-02-03 23:21:53', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(252, 80, 5, 0, 0, 0, 'valid', '2026-02-03 23:21:53', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(253, 80, 7, 0, 0, 0, 'valid', '2026-02-03 23:21:56', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(254, 80, 8, 0, 0, 0, 'valid', '2026-02-03 23:21:56', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(255, 81, 8, 0, 0, 0, 'valid', '2026-02-04 13:57:48', 'izin', 'sdada', NULL),
(256, 81, 3, 0, 0, 0, 'valid', '2026-02-05 10:45:27', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(257, 81, 5, 0, 0, 0, 'valid', '2026-02-05 10:45:27', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(258, 81, 7, 0, 0, 0, 'valid', '2026-02-05 10:45:30', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(259, 82, 8, -7.649859, 110.413132, 317.38855790502, 'valid', '2026-02-05 22:11:13', 'hadir', NULL, NULL),
(260, 82, 3, 0, 0, 0, 'valid', '2026-02-05 22:19:59', 'izin', 'dadsa', NULL),
(261, 82, 7, -7.649859, 110.413132, 0, 'valid', '2026-02-05 22:20:19', 'hadir', NULL, NULL),
(262, 82, 5, 0, 0, 0, 'valid', '2026-02-05 22:20:42', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(263, 83, 8, -7.649859, 110.413132, 174.86877035454, 'valid', '2026-02-05 22:21:25', 'hadir', NULL, NULL),
(264, 83, 3, 0, 0, 0, 'valid', '2026-02-06 21:35:53', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(265, 83, 5, 0, 0, 0, 'valid', '2026-02-06 21:35:53', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(266, 83, 7, 0, 0, 0, 'valid', '2026-02-06 21:35:57', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(267, 84, 3, 0, 0, 0, 'valid', '2026-02-06 21:36:19', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(268, 84, 5, 0, 0, 0, 'valid', '2026-02-06 21:36:19', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(269, 84, 7, 0, 0, 0, 'valid', '2026-02-06 21:36:22', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(270, 84, 8, 0, 0, 0, 'valid', '2026-02-06 21:36:22', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(271, 85, 3, 0, 0, 0, 'valid', '2026-02-07 22:46:18', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(272, 85, 5, 0, 0, 0, 'valid', '2026-02-07 22:46:18', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(273, 85, 7, 0, 0, 0, 'valid', '2026-02-07 22:46:22', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(274, 85, 8, 0, 0, 0, 'valid', '2026-02-07 22:46:22', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(275, 86, 3, 0, 0, 0, 'valid', '2026-02-08 22:25:06', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(276, 86, 5, 0, 0, 0, 'valid', '2026-02-08 22:25:06', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(277, 86, 7, 0, 0, 0, 'valid', '2026-02-08 22:25:09', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(278, 86, 8, 0, 0, 0, 'valid', '2026-02-08 22:25:09', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(279, 87, 3, 0, 0, 0, 'valid', '2026-02-10 11:14:40', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(280, 87, 5, 0, 0, 0, 'valid', '2026-02-10 11:14:40', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(281, 87, 7, 0, 0, 0, 'valid', '2026-02-10 11:14:43', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(282, 87, 8, 0, 0, 0, 'valid', '2026-02-10 11:14:43', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(283, 88, 3, 0, 0, 0, 'valid', '2026-02-12 11:07:25', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(284, 88, 5, 0, 0, 0, 'valid', '2026-02-12 11:07:25', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(285, 88, 7, 0, 0, 0, 'valid', '2026-02-12 11:07:29', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(286, 88, 8, 0, 0, 0, 'valid', '2026-02-12 11:07:29', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(287, 89, 3, 0, 0, 0, 'valid', '2026-02-12 22:59:49', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(288, 89, 5, 0, 0, 0, 'valid', '2026-02-12 22:59:50', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(289, 89, 7, 0, 0, 0, 'valid', '2026-02-12 22:59:56', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(290, 89, 8, 0, 0, 0, 'valid', '2026-02-12 22:59:56', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(291, 90, 3, 0, 0, 0, 'valid', '2026-02-12 23:05:14', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(292, 90, 5, 0, 0, 0, 'valid', '2026-02-12 23:05:17', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(293, 90, 7, 0, 0, 0, 'valid', '2026-02-12 23:05:17', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(294, 90, 8, 0, 0, 0, 'valid', '2026-02-12 23:05:17', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(295, 91, 3, 0, 0, 0, 'valid', '2026-02-12 23:06:34', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(296, 91, 5, 0, 0, 0, 'valid', '2026-02-12 23:06:34', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(297, 91, 7, 0, 0, 0, 'valid', '2026-02-12 23:06:34', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(298, 91, 8, 0, 0, 0, 'valid', '2026-02-12 23:06:34', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(307, 94, 3, 0, 0, 0, 'valid', '2026-02-12 23:08:19', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(308, 94, 5, 0, 0, 0, 'valid', '2026-02-12 23:08:22', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(309, 94, 7, 0, 0, 0, 'valid', '2026-02-12 23:08:22', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(310, 94, 8, 0, 0, 0, 'valid', '2026-02-12 23:08:22', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(311, 95, 3, 0, 0, 0, 'valid', '2026-02-12 23:09:29', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(312, 95, 5, 0, 0, 0, 'valid', '2026-02-12 23:09:32', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(313, 95, 7, 0, 0, 0, 'valid', '2026-02-12 23:09:32', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(314, 95, 8, 0, 0, 0, 'valid', '2026-02-12 23:09:32', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(315, 97, 3, 0, 0, 0, 'valid', '2026-02-14 19:57:07', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(316, 97, 5, 0, 0, 0, 'valid', '2026-02-14 19:57:10', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(317, 97, 7, 0, 0, 0, 'valid', '2026-02-14 19:57:10', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(318, 97, 8, 0, 0, 0, 'valid', '2026-02-14 19:57:10', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(319, 98, 3, 0, 0, 0, 'valid', '2026-02-14 19:57:46', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(320, 98, 5, 0, 0, 0, 'valid', '2026-02-14 19:57:48', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(321, 98, 7, 0, 0, 0, 'valid', '2026-02-14 19:57:48', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(322, 98, 8, 0, 0, 0, 'valid', '2026-02-14 19:57:48', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(323, 99, 8, -7.649859, 110.413132, 221.89358170725, 'valid', '2026-02-15 21:43:28', 'hadir', NULL, NULL),
(324, 99, 3, 0, 0, 0, 'valid', '2026-02-15 22:00:47', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(325, 99, 5, 0, 0, 0, 'valid', '2026-02-15 22:00:50', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(326, 99, 7, 0, 0, 0, 'valid', '2026-02-15 22:00:50', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(327, 100, 3, 0, 0, 0, 'valid', '2026-02-16 11:22:32', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(328, 100, 5, 0, 0, 0, 'valid', '2026-02-16 11:22:35', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(329, 100, 7, 0, 0, 0, 'valid', '2026-02-16 11:22:35', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(330, 100, 8, 0, 0, 0, 'valid', '2026-02-16 11:22:35', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(331, 101, 3, 0, 0, 0, 'valid', '2026-02-17 21:36:37', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(332, 101, 5, 0, 0, 0, 'valid', '2026-02-17 21:36:40', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(333, 101, 7, 0, 0, 0, 'valid', '2026-02-17 21:36:40', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(334, 101, 8, 0, 0, 0, 'valid', '2026-02-17 21:36:40', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(335, 102, 3, 0, 0, 0, 'valid', '2026-02-18 11:13:50', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(336, 102, 5, 0, 0, 0, 'valid', '2026-02-18 11:13:54', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(337, 102, 7, 0, 0, 0, 'valid', '2026-02-18 11:13:54', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(338, 102, 8, 0, 0, 0, 'valid', '2026-02-18 11:13:54', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(339, 103, 3, 0, 0, 0, 'valid', '2026-02-19 07:50:09', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(340, 103, 5, 0, 0, 0, 'valid', '2026-02-19 07:50:13', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(341, 103, 7, 0, 0, 0, 'valid', '2026-02-19 07:50:13', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(342, 103, 8, 0, 0, 0, 'valid', '2026-02-19 07:50:13', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(343, 104, 3, 0, 0, 0, 'valid', '2026-03-02 13:04:00', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(344, 104, 5, 0, 0, 0, 'valid', '2026-03-02 13:04:24', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(345, 104, 7, 0, 0, 0, 'valid', '2026-03-02 13:04:24', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(346, 104, 8, 0, 0, 0, 'valid', '2026-03-02 13:04:24', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(347, 105, 3, 0, 0, 0, 'valid', '2026-03-02 13:04:54', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(348, 105, 5, 0, 0, 0, 'valid', '2026-03-02 13:05:16', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(349, 105, 7, 0, 0, 0, 'valid', '2026-03-02 13:05:16', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(350, 105, 8, 0, 0, 0, 'valid', '2026-03-02 13:05:16', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(351, 106, 3, 0, 0, 0, 'valid', '2026-03-02 13:31:36', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(352, 106, 5, 0, 0, 0, 'valid', '2026-03-02 13:31:57', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(353, 106, 7, 0, 0, 0, 'valid', '2026-03-02 13:31:57', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(354, 106, 8, 0, 0, 0, 'valid', '2026-03-02 13:31:57', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(375, 112, 3, 0, 0, 0, 'valid', '2026-03-02 14:09:15', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(376, 112, 5, 0, 0, 0, 'valid', '2026-03-02 14:09:36', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(377, 112, 7, 0, 0, 0, 'valid', '2026-03-02 14:09:36', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(378, 112, 8, 0, 0, 0, 'valid', '2026-03-02 14:09:36', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(379, 113, 3, 0, 0, 0, 'valid', '2026-03-04 13:48:39', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(380, 113, 5, 0, 0, 0, 'valid', '2026-03-04 13:49:02', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(381, 113, 7, 0, 0, 0, 'valid', '2026-03-04 13:49:02', 'alpha', 'Tidak hadir saat sesi ditutup', NULL),
(382, 113, 8, 0, 0, 0, 'valid', '2026-03-04 13:49:02', 'alpha', 'Tidak hadir saat sesi ditutup', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `presensi_sekolah_sesi`
--

CREATE TABLE `presensi_sekolah_sesi` (
  `id` int NOT NULL,
  `waktu_buka` datetime NOT NULL,
  `waktu_tutup` datetime NOT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  `created_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `presensi_sekolah_sesi`
--

INSERT INTO `presensi_sekolah_sesi` (`id`, `waktu_buka`, `waktu_tutup`, `status`, `created_by`) VALUES
(1, '2025-11-07 22:46:00', '2025-11-07 22:49:00', 'closed', 1),
(2, '2025-11-07 22:53:00', '2025-11-07 23:10:50', 'closed', 1),
(3, '2025-11-07 23:03:00', '2025-11-07 23:21:00', 'closed', 1),
(4, '2025-12-20 00:17:00', '2025-12-20 00:20:00', 'closed', 1),
(5, '2025-12-22 01:14:00', '2025-12-22 01:30:00', 'closed', 1),
(6, '2025-12-22 08:53:00', '2025-12-22 09:05:00', 'closed', 1),
(7, '2025-12-22 09:03:00', '2025-12-22 09:10:00', 'closed', 1),
(8, '2025-12-22 09:04:00', '2025-12-22 09:10:00', 'closed', 1),
(9, '2025-12-22 09:08:00', '2025-12-22 09:24:00', 'closed', 1),
(10, '2025-12-22 09:23:00', '2025-12-22 09:36:00', 'closed', 1),
(11, '2025-12-22 09:44:00', '2025-12-22 09:56:00', 'closed', 1),
(12, '2025-12-22 10:32:00', '2025-12-22 10:43:00', 'closed', 1),
(13, '2025-12-22 10:39:00', '2025-12-22 10:46:00', 'closed', 1),
(14, '2025-12-22 11:00:00', '2025-12-22 11:13:00', 'closed', 1),
(15, '2025-12-22 11:06:00', '2025-12-22 11:16:00', 'closed', 1),
(16, '2025-12-22 11:40:00', '2025-12-22 11:44:00', 'closed', 1),
(17, '2025-12-22 11:49:00', '2025-12-22 11:53:00', 'closed', 1),
(18, '2025-12-23 15:54:00', '2025-12-23 15:59:00', 'closed', 1),
(19, '2025-12-24 00:07:00', '2025-12-24 00:09:00', 'closed', 1),
(20, '2025-12-24 00:08:00', '2025-12-24 00:12:00', 'closed', 1),
(21, '2025-12-24 23:08:00', '2025-12-24 23:11:00', 'closed', 1),
(22, '2025-12-24 23:19:00', '2025-12-24 23:21:00', 'closed', 1),
(23, '2025-12-24 23:49:00', '2025-12-24 23:52:00', 'closed', 9),
(24, '2025-12-24 23:53:00', '2025-12-24 23:57:00', 'closed', 9),
(25, '2025-12-25 00:05:00', '2025-12-25 00:07:00', 'closed', 9),
(26, '2025-12-25 00:07:00', '2025-12-25 00:09:00', 'closed', 9),
(27, '2025-12-25 00:07:00', '2025-12-25 00:09:00', 'closed', 9),
(28, '2025-12-25 00:07:00', '2025-12-25 00:09:00', 'closed', 9),
(29, '2025-12-25 00:22:00', '2025-12-25 00:26:00', 'closed', 9),
(30, '2025-12-25 12:23:00', '2025-12-25 12:26:00', 'closed', 9),
(31, '2025-12-25 12:48:00', '2025-12-25 12:51:00', 'closed', 9),
(32, '2025-12-25 12:56:00', '2025-12-25 12:58:00', 'closed', 9),
(33, '2025-12-25 13:02:00', '2025-12-25 13:04:00', 'closed', 9),
(34, '2025-12-25 13:04:00', '2025-12-25 13:07:00', 'closed', 9),
(35, '2025-12-25 13:09:00', '2025-12-25 13:12:00', 'closed', 9),
(36, '2025-12-25 13:15:00', '2025-12-25 13:30:00', 'closed', 9),
(37, '2025-12-25 13:25:00', '2025-12-25 13:30:00', 'closed', 9),
(38, '2025-12-25 14:03:00', '2025-12-25 14:05:00', 'closed', 9),
(39, '2025-12-25 14:11:00', '2025-12-25 14:14:00', 'closed', 9),
(40, '2026-01-02 15:23:00', '2026-01-02 15:25:00', 'closed', 1),
(41, '2026-01-03 11:02:00', '2026-01-03 11:03:00', 'closed', 1),
(42, '2026-01-03 11:03:00', '2026-01-03 11:07:00', 'closed', 1),
(43, '2026-01-06 22:17:00', '2026-01-06 22:19:00', 'closed', 1),
(45, '2026-01-08 21:40:00', '2026-01-08 23:59:59', 'closed', NULL),
(46, '2026-01-09 00:50:00', '2026-01-09 00:53:00', 'closed', 9),
(47, '2026-01-09 13:23:00', '2026-01-09 13:26:00', 'closed', 1),
(48, '2026-01-11 21:00:00', '2026-01-11 21:03:00', 'closed', 1),
(49, '2026-01-12 07:00:00', '2026-01-12 23:59:59', 'closed', NULL),
(50, '2026-01-12 11:56:00', '2026-01-13 11:57:00', 'closed', 1),
(51, '2026-01-12 11:57:00', '2026-01-20 11:58:00', 'closed', 1),
(52, '2026-01-11 12:02:00', '2026-01-13 12:02:00', 'closed', 1),
(53, '2026-01-12 12:04:00', '2026-01-13 12:04:00', 'closed', 1),
(54, '2026-01-12 12:06:00', '2026-01-12 12:08:00', 'closed', 1),
(55, '2026-01-12 12:14:00', '2026-01-12 12:18:00', 'closed', 1),
(56, '2026-01-12 12:15:00', '2026-01-12 12:18:00', 'closed', 9),
(57, '2026-01-13 07:00:00', '2026-02-01 23:20:00', 'closed', NULL),
(58, '2026-01-13 13:36:00', '2026-01-13 13:38:00', 'closed', 1),
(59, '2026-01-13 13:38:00', '2026-02-01 13:43:00', 'closed', 1),
(60, '2026-01-19 07:00:00', '2026-01-31 19:31:00', 'closed', NULL),
(75, '2026-02-02 01:05:00', '2026-02-02 01:09:00', 'closed', 1),
(76, '2026-02-02 05:00:00', '2026-02-02 06:02:00', 'closed', 1),
(79, '2026-02-02 05:57:00', '2026-02-02 06:04:00', 'closed', 1),
(80, '2026-02-03 07:00:00', '2026-02-03 23:59:59', 'closed', NULL),
(81, '2026-02-04 07:00:00', '2026-02-04 23:59:59', 'closed', NULL),
(82, '2026-02-05 07:00:00', '2026-02-05 23:59:59', 'closed', NULL),
(83, '2026-02-05 22:21:00', '2026-02-05 22:24:00', 'closed', 1),
(84, '2026-02-06 07:00:00', '2026-02-06 23:59:59', 'closed', NULL),
(85, '2026-02-07 22:46:00', '2026-02-07 22:49:00', 'closed', 1),
(86, '2026-02-08 22:24:00', '2026-02-08 22:28:00', 'closed', 1),
(87, '2026-02-09 07:00:00', '2026-02-09 23:59:59', 'closed', NULL),
(88, '2026-02-10 07:00:00', '2026-02-10 23:59:59', 'closed', NULL),
(89, '2026-02-12 07:00:00', '2026-02-12 23:59:59', 'closed', NULL),
(90, '2026-02-12 23:00:00', '2026-02-13 23:04:00', 'closed', 9),
(91, '2026-02-12 23:06:00', '2026-02-12 23:10:00', 'closed', 1),
(94, '2026-02-12 23:08:00', '2026-02-13 23:08:00', 'closed', 1),
(95, '2026-02-12 23:09:00', '2026-02-13 23:09:00', 'closed', 9),
(97, '2026-02-13 07:00:00', '2026-02-13 23:59:59', 'closed', NULL),
(98, '2026-02-14 19:57:00', '2026-02-14 19:59:00', 'closed', 1),
(99, '2026-02-15 21:43:00', '2026-02-15 21:45:00', 'closed', 1),
(100, '2026-02-15 22:00:00', '2026-02-15 23:00:00', 'closed', 1),
(101, '2026-02-16 07:00:00', '2026-02-16 23:59:59', 'closed', NULL),
(102, '2026-02-17 07:00:00', '2026-02-17 23:59:59', 'closed', NULL),
(103, '2026-02-18 07:00:00', '2026-02-18 23:59:59', 'closed', NULL),
(104, '2026-02-19 07:00:00', '2026-02-19 23:59:59', 'closed', NULL),
(105, '2026-03-02 07:00:00', '2026-03-02 23:59:59', 'closed', NULL),
(106, '2026-03-02 13:31:00', '2026-03-02 13:36:00', 'closed', 1),
(112, '2026-03-02 14:09:00', '2026-03-02 14:14:00', 'closed', 1),
(113, '2026-03-03 07:00:00', '2026-03-03 23:59:59', 'closed', NULL),
(114, '2026-03-04 07:00:00', '2026-03-04 23:59:59', 'open', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `presensi_sesi`
--

CREATE TABLE `presensi_sesi` (
  `id` int NOT NULL,
  `kelas_id` int NOT NULL,
  `kelas_mata_pelajaran_id` int DEFAULT NULL,
  `guru_id` int NOT NULL,
  `waktu_buka` datetime NOT NULL,
  `waktu_tutup` datetime DEFAULT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `presensi_sesi`
--

INSERT INTO `presensi_sesi` (`id`, `kelas_id`, `kelas_mata_pelajaran_id`, `guru_id`, `waktu_buka`, `waktu_tutup`, `status`) VALUES
(1, 1, NULL, 2, '2025-11-02 22:21:03', '2025-11-02 22:21:38', 'closed'),
(3, 1, NULL, 2, '2025-11-02 22:25:07', '2025-11-02 22:25:14', 'closed'),
(4, 1, NULL, 2, '2025-11-02 22:33:10', '2025-11-03 13:58:27', 'closed'),
(5, 3, NULL, 2, '2025-12-22 09:26:48', '2025-12-22 09:27:07', 'closed'),
(6, 3, NULL, 2, '2025-12-22 09:36:01', '2025-12-22 09:36:13', 'closed'),
(7, 1, NULL, 2, '2025-12-23 22:35:03', '2025-12-24 00:02:57', 'closed'),
(8, 1, NULL, 2, '2025-12-24 00:03:45', '2025-12-24 00:04:03', 'closed'),
(9, 1, NULL, 2, '2025-12-25 12:07:48', '2025-12-25 12:07:53', 'closed'),
(10, 1, NULL, 2, '2025-12-25 12:21:54', '2025-12-25 12:22:00', 'closed'),
(11, 1, NULL, 2, '2026-01-02 22:41:07', '2026-01-02 22:41:15', 'closed'),
(12, 1, NULL, 2, '2026-01-06 22:14:28', '2026-01-06 22:15:05', 'closed'),
(13, 1, NULL, 2, '2026-01-08 13:12:45', '2026-01-08 13:12:59', 'closed'),
(14, 1, NULL, 2, '2026-02-01 11:05:30', '2026-02-01 11:08:04', 'closed'),
(15, 1, NULL, 2, '2026-02-01 11:24:08', '2026-02-01 11:54:57', 'closed'),
(16, 1, NULL, 2, '2026-02-01 11:55:07', '2026-02-01 11:57:16', 'closed'),
(17, 1, NULL, 2, '2026-02-01 11:58:04', '2026-02-04 13:55:24', 'closed'),
(18, 1, NULL, 2, '2026-02-04 14:01:34', '2026-02-10 13:52:48', 'closed'),
(19, 1, NULL, 2, '2026-02-10 21:32:55', '2026-03-02 13:37:18', 'closed');

-- --------------------------------------------------------

--
-- Table structure for table `siswa_mata_pelajaran`
--

CREATE TABLE `siswa_mata_pelajaran` (
  `id` int NOT NULL,
  `siswa_id` int NOT NULL,
  `mata_pelajaran_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `siswa_mata_pelajaran`
--

INSERT INTO `siswa_mata_pelajaran` (`id`, `siswa_id`, `mata_pelajaran_id`) VALUES
(1, 3, 1),
(6, 5, 1),
(9, 5, 4),
(8, 5, 6),
(4, 7, 3),
(5, 8, 1),
(7, 8, 3),
(10, 8, 7);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','admin_kesiswaan','guru','siswa') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`) VALUES
(1, 'Setyo Budi Sungkowo, S.Pd., M.Pd.', 'admin@smk7.sch.id', '$2y$10$3Q6gG59W7Ewksl2IW68gPehtOCTh.aMOvMtJHn32Yq7svLwMmOqL.', 'admin'),
(2, 'Adik Kristien, S.Pd.', 'guru@smk7.sch.id', '$2y$10$6cT9rCw8UfrAHbQ89bPFHe4Gyiru4mV0buME81qpo0bwdWLmoxswy', 'guru'),
(3, 'Luthfi', 'siswa@smk7.sch.id', '$2y$10$1N4Zf0ftXXK5aiXZH/gYTehCZd1gZbVdFem2e7B6dgHEAe42P/lPC', 'siswa'),
(5, 'Fakhri', 'fakhri@gmail.com', '$2y$10$AWiHc9JLrLnmzjvEPat0deqUBIEvT9arFFzmMVLwyUqQDMItmYAP.', 'siswa'),
(6, 'Bagas Nur, S.Pd.', 'bagas@gmail.com', '$2y$10$YRyEZtmkiEjcgH3qwNtKCuH9RFLKyyOyyujZ21XnS5wrZMPixDZWG', 'guru'),
(7, 'Habib', 'habib@gmail.com', '$2y$10$pR8LKq2ueQf5fX1uCEpnX.bwvhwsihases0t27cXfz0X6J0Nwt45u', 'siswa'),
(8, 'Zola', 'zola@gmail.com', '$2y$10$pRwU1JSRQMtnPQ78fy5D.eSYowRWM2qk2jdb9jf/uJEWpaNds7Fea', 'siswa'),
(9, 'Faisal Raihan, S.Pd.', 'yoga@gmail.com', '$2y$10$4NgXv86tghb1o593rHmk5OUFt1vswplgbiEMgkPAxXW5j1BFf55Ha', 'admin_kesiswaan'),
(13, 'Agus Prihatin, A.Md.', 'admin1@smk7.sch.id', '$2y$10$22Fnakkmf9HznSEdrDV31OWnyfjOAZRMB4uqKYNLU69kfdpOXgk0a', 'admin');

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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kelas_mata_pelajaran`
--
ALTER TABLE `kelas_mata_pelajaran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_kelas_mapel` (`kelas_id`,`mata_pelajaran_id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `mata_pelajaran_id` (`mata_pelajaran_id`);

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
-- Indexes for table `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guru_pengampu` (`guru_pengampu`);

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
  ADD KEY `guru_id` (`guru_id`),
  ADD KEY `kelas_mata_pelajaran_id` (`kelas_mata_pelajaran_id`);

--
-- Indexes for table `siswa_mata_pelajaran`
--
ALTER TABLE `siswa_mata_pelajaran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `siswa_id` (`siswa_id`,`mata_pelajaran_id`),
  ADD KEY `kelas_id` (`mata_pelajaran_id`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `buku_induk_dokumen`
--
ALTER TABLE `buku_induk_dokumen`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kelas_mata_pelajaran`
--
ALTER TABLE `kelas_mata_pelajaran`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `laporan_kemajuan`
--
ALTER TABLE `laporan_kemajuan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `lokasi_sekolah`
--
ALTER TABLE `lokasi_sekolah`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `presensi_kelas`
--
ALTER TABLE `presensi_kelas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `presensi_sekolah`
--
ALTER TABLE `presensi_sekolah`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=383;

--
-- AUTO_INCREMENT for table `presensi_sekolah_sesi`
--
ALTER TABLE `presensi_sekolah_sesi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `presensi_sesi`
--
ALTER TABLE `presensi_sesi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `siswa_mata_pelajaran`
--
ALTER TABLE `siswa_mata_pelajaran`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
-- Constraints for table `kelas_mata_pelajaran`
--
ALTER TABLE `kelas_mata_pelajaran`
  ADD CONSTRAINT `fk_kelas_mapel_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kelas_mapel_mapel` FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `laporan_kemajuan`
--
ALTER TABLE `laporan_kemajuan`
  ADD CONSTRAINT `laporan_kemajuan_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `laporan_kemajuan_ibfk_2` FOREIGN KEY (`guru_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lokasi_sekolah`
--
ALTER TABLE `lokasi_sekolah`
  ADD CONSTRAINT `lokasi_sekolah_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  ADD CONSTRAINT `fk_mata_pelajaran_guru` FOREIGN KEY (`guru_pengampu`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `presensi_kelas`
--
ALTER TABLE `presensi_kelas`
  ADD CONSTRAINT `fk_presensi_kelas_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_presensi_kelas_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `presensi_kelas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `presensi_kelas_ibfk_2` FOREIGN KEY (`kelas_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE,
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
  ADD CONSTRAINT `presensi_sesi_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `presensi_sesi_ibfk_2` FOREIGN KEY (`guru_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `siswa_mata_pelajaran`
--
ALTER TABLE `siswa_mata_pelajaran`
  ADD CONSTRAINT `siswa_mata_pelajaran_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `siswa_mata_pelajaran_ibfk_2` FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
