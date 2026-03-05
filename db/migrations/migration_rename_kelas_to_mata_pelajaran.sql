-- Migration: Rename kelas_id to mata_pelajaran_id in presensi_sesi and presensi_kelas tables
-- Date: 2026-03-04
-- Description: Mengubah nama kolom kelas_id menjadi mata_pelajaran_id untuk konsistensi dengan struktur data

-- Step 1: Rename column in presensi_sesi table
ALTER TABLE `presensi_sesi` 
CHANGE COLUMN `kelas_id` `mata_pelajaran_id` INT NOT NULL;

-- Step 2: Rename column in presensi_kelas table  
ALTER TABLE `presensi_kelas` 
CHANGE COLUMN `kelas_id` `mata_pelajaran_id` INT NOT NULL;

-- Step 3: Update foreign key constraint names if needed
-- Note: Check existing constraints first before adding

-- Verify the changes
-- SELECT * FROM information_schema.COLUMNS 
-- WHERE TABLE_SCHEMA = 'presensi_smk' 
-- AND COLUMN_NAME IN ('mata_pelajaran_id', 'kelas_id')
-- AND TABLE_NAME IN ('presensi_sesi', 'presensi_kelas');
