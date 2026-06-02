START TRANSACTION;

--
-- 1. Buat tabel profil admin dan guru.
--    id dibuat sama dengan users.id agar kode lama yang memakai session user_id tetap aman.
--
CREATE TABLE `admin` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `nama` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_admin_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `guru` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `nama` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_guru_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 2. Pindahkan nama dari users ke tabel profil sesuai role.
--
INSERT INTO `admin` (`id`, `user_id`, `nama`)
SELECT `id`, `id`, `nama`
FROM `users`
WHERE `role` IN ('admin', 'admin_kesiswaan');

INSERT INTO `guru` (`id`, `user_id`, `nama`)
SELECT `id`, `id`, `nama`
FROM `users`
WHERE `role` = 'guru';

--
-- 3. Lepas foreign key lama yang masih mengarah ke users.
--
ALTER TABLE `jadwal_mata_pelajaran`
  DROP FOREIGN KEY `fk_jadwal_guru`;

ALTER TABLE `lokasi_sekolah`
  DROP FOREIGN KEY `lokasi_sekolah_ibfk_1`;

ALTER TABLE `presensi_mapel_sesi`
  DROP FOREIGN KEY `fk_presensi_mapel_sesi_guru`;

ALTER TABLE `presensi_sekolah_sesi`
  DROP FOREIGN KEY `fk_presensi_sekolah_sesi_created_by`;

--
-- 4. Tambahkan foreign key baru sesuai proses bisnis.
--
ALTER TABLE `admin`
  ADD CONSTRAINT `fk_admin_user`
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
  ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `guru`
  ADD CONSTRAINT `fk_guru_user`
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
  ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `jadwal_mata_pelajaran`
  ADD CONSTRAINT `fk_jadwal_guru`
  FOREIGN KEY (`guru_pengampu`) REFERENCES `guru` (`id`)
  ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `lokasi_sekolah`
  ADD CONSTRAINT `lokasi_sekolah_ibfk_1`
  FOREIGN KEY (`updated_by`) REFERENCES `admin` (`id`)
  ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `presensi_mapel_sesi`
  ADD CONSTRAINT `fk_presensi_mapel_sesi_guru`
  FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`)
  ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `presensi_sekolah_sesi`
  ADD CONSTRAINT `fk_presensi_sekolah_sesi_created_by`
  FOREIGN KEY (`created_by`) REFERENCES `admin` (`id`)
  ON DELETE SET NULL ON UPDATE CASCADE;

--
-- 5. Hapus nama dari users setelah berhasil dipindahkan.
--
ALTER TABLE `users`
  DROP COLUMN `nama`;

COMMIT;
