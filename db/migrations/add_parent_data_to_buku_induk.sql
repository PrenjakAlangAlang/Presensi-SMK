-- Migration: Add parent data fields to buku_induk table
-- Created: 2025-12-24
-- Purpose: Add nama_ayah, nama_ibu, and no_telp_ortu fields to store parent information

ALTER TABLE `buku_induk` 
ADD COLUMN `nama_ayah` VARCHAR(150) NULL AFTER `alamat`,
ADD COLUMN `nama_ibu` VARCHAR(150) NULL AFTER `nama_ayah`,
ADD COLUMN `no_telp_ortu` VARCHAR(20) NULL AFTER `nama_ibu`;

-- Note: Run this migration to add parent data fields to buku_induk table
