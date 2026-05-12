-- Migration: nonaktifkan modul presensi mata pelajaran di database
-- Catatan: master data mata_pelajaran, kelas_mata_pelajaran, dan siswa_mata_pelajaran tetap dipertahankan.

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `presensi_mapel`;
DROP TABLE IF EXISTS `presensi_mapel_sesi`;

SET FOREIGN_KEY_CHECKS = 1;
