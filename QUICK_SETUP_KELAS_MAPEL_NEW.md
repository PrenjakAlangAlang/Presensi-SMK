# Quick Setup - Struktur Kelas & Mata Pelajaran

## Langkah Cepat

### 1. Backup Database
```bash
mysqldump -u root -p presensi_smk > backup_before_migration.sql
```

### 2. Jalankan Migration
```bash
cd c:\laragon\www\Presensi-SMK
mysql -u root presensi_smk < db/migrations/migration_add_kelas_structure.sql
```

### 3. Verifikasi Tabel Baru
```sql
SHOW TABLES;
-- Harus muncul:
-- - kelas
-- - kelas_mata_pelajaran  
-- - siswa_kelas
```

### 4. Isi Data Contoh

```sql
-- 1. Buat Kelas
INSERT INTO kelas (nama_kelas, tahun_ajaran) VALUES
('X RPL 1', '2025/2026'),
('X RPL 2', '2025/2026'),
('XI TKJ 1', '2025/2026');

-- 2. Lihat ID Kelas yang dibuat
SELECT * FROM kelas;

-- 3. Buat/Update Mata Pelajaran (hapus tahun_ajaran jika ada)
INSERT INTO mata_pelajaran (nama_mata_pelajaran, guru_pengampu, jadwal) VALUES
('Matematika', 2, 'Senin 08:00-10:00'),
('Bahasa Indonesia', 3, 'Selasa 08:00-10:00'),
('Pemrograman Web', 2, 'Rabu 10:00-12:00')
ON DUPLICATE KEY UPDATE jadwal = VALUES(jadwal);

-- 4. Lihat ID Mata Pelajaran
SELECT * FROM mata_pelajaran;

-- 5. Hubungkan Mata Pelajaran ke Kelas
-- Contoh: Kelas X RPL 1 (id=1) memiliki 3 mata pelajaran
INSERT INTO kelas_mata_pelajaran (kelas_id, mata_pelajaran_id) VALUES
(1, 1), -- X RPL 1 -> Matematika
(1, 2), -- X RPL 1 -> Bahasa Indonesia  
(1, 3); -- X RPL 1 -> Pemrograman Web

-- 6. Masukkan Siswa ke Kelas
-- Ganti ID siswa sesuai database Anda
INSERT INTO siswa_kelas (siswa_id, kelas_id) 
SELECT id, 1 FROM users WHERE role = 'siswa' LIMIT 5;
-- Ini memasukkan 5 siswa pertama ke kelas X RPL 1

-- 7. Verifikasi
SELECT 
    k.nama_kelas,
    COUNT(DISTINCT sk.siswa_id) as jumlah_siswa,
    COUNT(DISTINCT kmp.mata_pelajaran_id) as jumlah_mapel
FROM kelas k
LEFT JOIN siswa_kelas sk ON k.id = sk.kelas_id
LEFT JOIN kelas_mata_pelajaran kmp ON k.id = kmp.kelas_id
GROUP BY k.id;
```

## Struktur Relasi

```
KELAS (X RPL 1)
  ├── SISWA
  │     ├── Budi
  │     ├── Ani
  │     └── Citra
  └── MATA PELAJARAN
        ├── Matematika (Guru: Pak Budi)
        ├── B. Indonesia (Guru: Bu Ani)
        └── Pemrograman Web (Guru: Pak Budi)
```

## Alur Penggunaan

### Admin
1. **Buat Kelas** → X RPL 1, XI TKJ 1, dst
2. **Buat Mata Pelajaran** → Matematika, B.Indonesia, dst (dengan guru)
3. **Assign Mapel ke Kelas** → X RPL 1 punya Matematika + B.Indo + Prog Web
4. **Assign Siswa ke Kelas** → Budi, Ani, Citra masuk X RPL 1
5. Siswa **otomatis** terdaftar di semua mapel kelasnya

### Guru
1. Login → Lihat mata pelajaran yang diampu **per kelas**
2. Contoh: Pak Budi mengajar "Matematika di X RPL 1" dan "Pemrograman Web di X RPL 1"
3. Buka presensi **per mata pelajaran per kelas**
4. Tutup presensi → Beri catatan kemajuan

### Siswa  
1. Login → Lihat kelas (X RPL 1)
2. Lihat mata pelajaran di kelasnya (Matematika, B.Indo, Prog Web)
3. Presensi per mata pelajaran saat sesi dibuka

## File yang Sudah Dibuat

✅ **Migration SQL**: `db/migrations/migration_add_kelas_structure.sql`
✅ **KelasModel**: `app/models/KelasModel.php` (Updated)
✅ **MataPelajaranModel**: `app/models/MataPelajaranModel.php` (Updated)
✅ **Dokumentasi**: `PANDUAN_IMPLEMENTASI_KELAS_MAPEL.md`
✅ **Routing**: `ROUTING_KELAS_MAPEL.md`

## File yang Perlu Dibuat/Update

⏳ **AdminController**: Update method kelas() dan tambah method baru
⏳ **GuruController**: Update untuk struktur baru
⏳ **SiswaController**: Update untuk struktur baru
⏳ **Views Admin**: `app/views/admin/kelas.php`, `app/views/admin/mata_pelajaran.php`
⏳ **Views Guru**: `app/views/guru/kelas.php`
⏳ **Views Siswa**: `app/views/siswa/dashboard.php`
⏳ **index.php**: Tambah routing baru

## Query Berguna

### Lihat Siswa per Kelas
```sql
SELECT k.nama_kelas, u.nama as siswa, u.username
FROM kelas k
JOIN siswa_kelas sk ON k.id = sk.kelas_id
JOIN users u ON sk.siswa_id = u.id
ORDER BY k.nama_kelas, u.nama;
```

### Lihat Mata Pelajaran per Kelas
```sql
SELECT k.nama_kelas, mp.nama_mata_pelajaran, ug.nama as guru
FROM kelas k
JOIN kelas_mata_pelajaran kmp ON k.id = kmp.kelas_id
JOIN mata_pelajaran mp ON kmp.mata_pelajaran_id = mp.id
LEFT JOIN users ug ON mp.guru_pengampu = ug.id
ORDER BY k.nama_kelas, mp.nama_mata_pelajaran;
```

### Lihat Semua Data Lengkap
```sql
SELECT 
    k.nama_kelas,
    k.tahun_ajaran,
    mp.nama_mata_pelajaran,
    ug.nama as guru,
    us.nama as siswa
FROM kelas k
LEFT JOIN kelas_mata_pelajaran kmp ON k.id = kmp.kelas_id
LEFT JOIN mata_pelajaran mp ON kmp.mata_pelajaran_id = mp.id
LEFT JOIN users ug ON mp.guru_pengampu = ug.id
LEFT JOIN siswa_kelas sk ON k.id = sk.kelas_id
LEFT JOIN users us ON sk.siswa_id = us.id
ORDER BY k.nama_kelas, mp.nama_mata_pelajaran, us.nama;
```

## Troubleshooting

### Error: Table 'kelas' doesn't exist
→ Migration belum dijalankan. Jalankan file SQL di `db/migrations/`

### Error: Unknown column 'tahun_ajaran' in mata_pelajaran
→ Migration sudah berhasil! Hapus tahun_ajaran dari form mata pelajaran

### Siswa tidak muncul di mata pelajaran
→ Pastikan siswa sudah dimasukkan ke kelas dulu
→ Pastikan kelas sudah punya mata pelajaran

### Guru tidak lihat mata pelajaran
→ Pastikan guru_pengampu sudah diset di mata_pelajaran
→ Pastikan mata pelajaran sudah di-assign ke kelas

## Next Implementation Priority

1. ✅ Migration SQL
2. ✅ Models (KelasModel, MataPelajaranModel)
3. ⏳ AdminController - Manajemen Kelas
4. ⏳ AdminController - Manajemen Mata Pelajaran  
5. ⏳ Views Admin - UI untuk Kelas & Mata Pelajaran
6. ⏳ GuruController - Update struktur
7. ⏳ Views Guru - Update UI
8. ⏳ Testing & Debugging
