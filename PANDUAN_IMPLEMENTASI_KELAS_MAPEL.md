# Panduan Implementasi Struktur Kelas & Mata Pelajaran

## Ringkasan Perubahan

Sistem presensi telah direstrukturisasi untuk memisahkan konsep **Kelas** dan **Mata Pelajaran**:

### Struktur Lama:
```
mata_pelajaran (berisi: nama, tahun_ajaran, guru, jadwal)
   └── siswa_mata_pelajaran (many-to-many)
```

### Struktur Baru:
```
kelas (berisi: nama_kelas, tahun_ajaran)
   ├── siswa_kelas (siswa tergabung dalam kelas)
   └── kelas_mata_pelajaran
          └── mata_pelajaran (berisi: nama, guru, jadwal)
```

## Alur Kerja Baru

1. **Admin membuat Kelas** (contoh: X RPL 1, XI TKJ 2)
2. **Admin menambahkan Mata Pelajaran** ke database (Matematika, Bahasa Indonesia, dll)
3. **Admin menambahkan Mata Pelajaran ke Kelas** (X RPL 1 memiliki: Matematika, B. Indonesia, dll)
4. **Admin memasukkan Siswa ke dalam Kelas**
5. **Siswa otomatis terdaftar di semua Mata Pelajaran dalam Kelasnya**

## Struktur Database Baru

### Tabel: `kelas`
```sql
- id (PK)
- nama_kelas (VARCHAR 100) -- contoh: "X RPL 1"
- tahun_ajaran (VARCHAR 20) -- contoh: "2025/2026"
- created_at
- updated_at
```

### Tabel: `mata_pelajaran` (Dimodifikasi)
```sql
- id (PK)
- nama_mata_pelajaran (VARCHAR 100) -- contoh: "Matematika"
- guru_pengampu (FK ke users)
- jadwal (TEXT)
- created_at
- updated_at

DIHAPUS: tahun_ajaran (pindah ke tabel kelas)
```

### Tabel: `kelas_mata_pelajaran` (Junction Table BARU)
```sql
- id (PK)
- kelas_id (FK ke kelas)
- mata_pelajaran_id (FK ke mata_pelajaran)
- created_at

UNIQUE KEY: (kelas_id, mata_pelajaran_id)
```

### Tabel: `siswa_kelas` (BARU, menggantikan siswa_mata_pelajaran)
```sql
- id (PK)
- siswa_id (FK ke users)
- kelas_id (FK ke kelas)
- created_at

UNIQUE KEY: (siswa_id, kelas_id)
```

## Langkah Migrasi

### 1. Jalankan Migration SQL
```bash
# Jalankan file migration
mysql -u root -p presensi_smk < db/migrations/migration_add_kelas_structure.sql
```

### 2. Isi Data Awal

#### Buat Kelas-kelas
```sql
INSERT INTO kelas (nama_kelas, tahun_ajaran) VALUES
('X RPL 1', '2025/2026'),
('X RPL 2', '2025/2026'),
('XI TKJ 1', '2025/2026'),
('XII MM 1', '2025/2026');
```

#### Buat Mata Pelajaran (jika belum ada)
```sql
INSERT INTO mata_pelajaran (nama_mata_pelajaran, guru_pengampu, jadwal) VALUES
('Matematika', 2, 'Senin 08:00-10:00'),
('Bahasa Indonesia', 3, 'Selasa 08:00-10:00'),
('Pemrograman Web', 2, 'Rabu 10:00-12:00');
```

#### Hubungkan Mata Pelajaran ke Kelas
```sql
-- X RPL 1 memiliki Matematika dan Pemrograman Web
INSERT INTO kelas_mata_pelajaran (kelas_id, mata_pelajaran_id) VALUES
(1, 1), -- X RPL 1 -> Matematika
(1, 3); -- X RPL 1 -> Pemrograman Web
```

#### Masukkan Siswa ke Kelas
```sql
-- Siswa dengan id 10, 11, 12 masuk ke X RPL 1
INSERT INTO siswa_kelas (siswa_id, kelas_id) VALUES
(10, 1),
(11, 1),
(12, 1);
```

### 3. Update Controller & View (Akan dilakukan di step berikutnya)

## Keuntungan Struktur Baru

1. **Lebih Realistis**: Sesuai dengan struktur sekolah sesungguhnya
2. **Manajemen Lebih Mudah**: Admin mengelola kelas, bukan individual siswa-mapel
3. **Skalabilitas**: Mudah menambah/menghapus mata pelajaran per kelas
4. **Konsistensi Data**: Siswa otomatis terdaftar di semua mapel kelasnya
5. **Laporan Lebih Baik**: Bisa membuat laporan per kelas atau per mata pelajaran

## Model yang Telah Diupdate

- ✅ `KelasModel.php` - CRUD kelas, manajemen siswa & mata pelajaran
- ✅ `MataPelajaranModel.php` - Disesuaikan dengan struktur baru
- ⏳ `AdminController.php` - Perlu update untuk handle kelas & mapel
- ⏳ `GuruController.php` - Perlu update untuk struktur baru
- ⏳ View files - Perlu update UI

## Next Steps

1. Update AdminController untuk menambah endpoint kelas
2. Update view admin untuk manajemen kelas
3. Update GuruController untuk bekerja dengan struktur baru
4. Update view guru untuk menampilkan mata pelajaran per kelas
5. Update SiswaController jika diperlukan
6. Testing menyeluruh

## Catatan Penting

- **Backup database** sebelum menjalankan migration
- Data lama akan di-backup ke tabel `mata_pelajaran_backup` dan `presensi_sesi_backup`
- Tabel `siswa_mata_pelajaran` lama akan digantikan dengan `siswa_kelas`
- Perlu re-entry data siswa dan assignment mata pelajaran setelah migration
