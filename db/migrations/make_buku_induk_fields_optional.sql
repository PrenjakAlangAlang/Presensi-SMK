-- Migration: longgarkan field buku_induk agar data bisa disimpan bertahap.

ALTER TABLE `buku_induk`
  MODIFY `nisn` varchar(10) NULL,
  MODIFY `tempat_lahir` varchar(25) NULL,
  MODIFY `tanggal_lahir` date NULL,
  MODIFY `alamat` text NULL;
