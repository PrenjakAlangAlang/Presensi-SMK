# PENTING: Jalankan SQL Migration

## ⚠️ SEBELUM MENJALANKAN

1. **BACKUP DATABASE TERLEBIH DAHULU!**
   ```sql
   -- Backup via phpMyAdmin atau command line
   mysqldump -u root presensi_smk > backup_presensi_smk_$(date +%Y%m%d).sql
   ```

2. **Tutup semua koneksi aplikasi ke database**

## 📋 Langkah Migrasi

### Cara 1: Via phpMyAdmin (MUDAH)
1. Buka phpMyAdmin di http://localhost/phpmyadmin
2. Pilih database `presensi_smk`
3. Klik tab "SQL"
4. Copy-paste isi file `db/migrations/migration_kelas_to_mata_pelajaran.sql`
5. Klik "Go"

### Cara 2: Via MySQL Command Line
```bash
# Masuk ke direktori project
cd C:\laragon\www\Presensi-SMK

# Jalankan migration
mysql -u root presensi_smk < db/migrations/migration_kelas_to_mata_pelajaran.sql
```

### Cara 3: Via Laragon Terminal
1. Buka Laragon
2. Klik "Terminal"
3. Jalankan:
```bash
cd C:\laragon\www\Presensi-SMK
mysql -u root presensi_smk < db/migrations/migration_kelas_to_mata_pelajaran.sql
```

## ✅ Verifikasi Migrasi Berhasil

Setelah menjalankan migration, cek di database:

```sql
-- Cek tabel mata_pelajaran sudah dibuat
SHOW TABLES LIKE 'mata_pelajaran';

-- Cek struktur tabel
DESC mata_pelajaran;

-- Cek data sudah dimigrasikan
SELECT COUNT(*) FROM mata_pelajaran;

-- Cek tabel siswa_mata_pelajaran
DESC siswa_mata_pelajaran;
SELECT COUNT(*) FROM siswa_mata_pelajaran;

-- Pastikan tabel lama sudah tidak ada (OPSIONAL - lihat catatan di bawah)
-- SHOW TABLES LIKE 'kelas';
-- SHOW TABLES LIKE 'siswa_kelas';
```

## 📝 CATATAN PENTING

1. **File migration sudah mencakup**:
   - ✅ RENAME tabel `kelas` → `mata_pelajaran`
   - ✅ RENAME tabel `siswa_kelas` → `siswa_mata_pelajaran`
   - ✅ ALTER kolom `nama_kelas` → `nama_mata_pelajaran`
   - ✅ ALTER kolom `wali_kelas` → `guru_pengampu`
   - ✅ UPDATE foreign key references

2. **Jika ada error**:
   - Restore dari backup
   - Cek error message
   - Pastikan tidak ada typo di nama tabel/kolom

3. **Setelah berhasil migrasi**:
   - Test semua fitur aplikasi
   - Login sebagai Admin, Guru, Siswa
   - Test CRUD mata pelajaran
   - Test presensi
   - Test laporan

4. **Hapus file lama (OPSIONAL)**:
   ```bash
   # Setelah yakin semua berjalan normal (3-7 hari testing)
   # Hapus file model lama:
   rm app/models/KelasModel.php
   ```

## 🚀 Setelah Migration

Refresh aplikasi dan coba:
- ✅ Menu "Data Mata Pelajaran" (Admin)
- ✅ Menu "Mata Pelajaran Saya" (Guru)
- ✅ Dashboard semua role
- ✅ Laporan presensi
- ✅ Export Excel/PDF

---

**Jika ada masalah, restore backup dan hubungi developer!**
