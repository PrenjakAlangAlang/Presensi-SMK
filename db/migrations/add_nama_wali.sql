-- Migration: Add nama_wali column to buku_induk table
-- Date: 2026-01-07

ALTER TABLE `buku_induk` 
ADD COLUMN `nama_wali` VARCHAR(100) NULL AFTER `nama_ibu`;
