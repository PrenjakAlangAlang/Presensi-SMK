-- Migration: Remove orangtua (parent) role and related data
-- Created: 2025-12-24
-- Purpose: Remove all orangtua functionality from the system

-- 1. Remove all relationships between orangtua and siswa
DROP TABLE IF EXISTS `orangtua_siswa`;

-- 2. Delete all users with orangtua role
DELETE FROM `users` WHERE `role` = 'orangtua';

-- 3. Clean up any orphaned data (optional, for safety)
-- This ensures data integrity after removing the orangtua role

-- Note: Run this migration to completely remove the orangtua role from the system
-- Make sure to backup your database before running this migration
