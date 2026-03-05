# Quick Setup Guide: Implementasi Struktur Kelas & Mata Pelajaran Baru

## Langkah-langkah Implementasi

### Step 1: Backup Database ✅
**PENTING!** Backup database sebelum melakukan perubahan.

```bash
# Di command line (Windows - Laragon)
cd c:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\
mysqldump.exe -u root presensi_smk > c:\backup_presensi_smk.sql

# Atau lewat phpMyAdmin:
# 1. Buka http://localhost/phpmyadmin
# 2. Pilih database presensi_smk
# 3. Tab "Export" > Go
```

### Step 2: Jalankan Migration SQL ⚙️

```bash
# Di command line (Windows - Laragon)
cd c:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\
mysql.exe -u root presensi_smk < c:\laragon\www\Presensi-SMK\db\migrations\migration_kelas_matapelajaran_restructure.sql

# Atau lewat phpMyAdmin:
# 1. Pilih database presensi_smk
# 2. Tab "SQL"
# 3. Copy-paste isi file migration_kelas_matapelajaran_restructure.sql
# 4. Klik "Go"
```

### Step 3: Verifikasi Database Structure ✔️

Cek bahwa perubahan berhasil:

```sql
-- Cek table kelas sudah ada
SHOW TABLES LIKE 'kelas';

-- Cek table kelas_mata_pelajaran sudah ada
SHOW TABLES LIKE 'kelas_mata_pelajaran';

-- Cek kolom tahun_ajaran sudah dihapus dari mata_pelajaran
DESCRIBE mata_pelajaran;

-- Cek presensi_sesi sekarang punya mata_pelajaran_id (bukan kelas_id)
DESCRIBE presensi_sesi;

-- Cek view helper sudah ada
SHOW FULL TABLES WHERE Table_type = 'VIEW';
```

### Step 4: Sesuaikan Data Sample 📝

Edit file migration dan sesuaikan data sample kelas dan relasi:

```sql
-- Sesuaikan data kelas dengan kelas di sekolah Anda
INSERT INTO `kelas` (`id`, `nama_kelas`, `tahun_ajaran`) VALUES
(1, 'X RPL 1', '2025/2026'),
(2, 'X RPL 2', '2025/2026'),
(3, 'XI RPL 1', '2025/2026');
-- dst...

-- Sesuaikan relasi kelas-mata pelajaran
INSERT INTO `kelas_mata_pelajaran` (`kelas_id`, `mata_pelajaran_id`) VALUES
(1, 1), -- X RPL 1 memiliki mata pelajaran id 1
(1, 3), -- X RPL 1 memiliki mata pelajaran id 3
-- dst...
```

### Step 5: Update Routing di index.php 🔧

Buka file `index.php` dan tambahkan routes baru untuk admin. Lihat file `ROUTING_UPDATE_KELAS_MATAPELAJARAN.md` untuk detail lengkap.

**Minimal routes yang perlu ditambahkan:**

```php
// Section admin routes
case 'admin_get_matapelajaran_dalam_kelas':
    $adminController->getMataPelajaranDalamKelas();
    break;
case 'admin_add_matapelajaran_to_kelas':
    $adminController->addMataPelajaranToKelas();
    break;
case 'admin_remove_matapelajaran_from_kelas':
    $adminController->removeMataPelajaranFromKelas();
    break;
// ... tambahkan routes lainnya
```

### Step 6: Update View Admin Kelas 🎨

File yang perlu diupdate: `app/views/admin/kelas.php`

**Perubahan yang diperlukan:**

1. **Pisahkan tampilan menjadi 2 tab:**
   - Tab "Daftar Kelas" untuk manage kelas
   - Tab "Daftar Mata Pelajaran" untuk manage mata pelajaran

2. **Form Tambah Kelas:**
```html
<form method="POST" action="index.php?action=admin_create_kelas">
    <input type="hidden" name="type" value="kelas">
    <input type="text" name="nama_kelas" placeholder="Nama Kelas" required>
    <input type="text" name="tahun_ajaran" placeholder="2025/2026" required>
    <button type="submit">Tambah Kelas</button>
</form>
```

3. **Form Tambah Mata Pelajaran:**
```html
<form method="POST" action="index.php?action=admin_create_kelas">
    <input type="hidden" name="type" value="mata_pelajaran">
    <input type="text" name="nama_mata_pelajaran" placeholder="Nama Mata Pelajaran" required>
    <select name="guru_pengampu" required>
        <option value="">Pilih Guru</option>
        <?php foreach($guru as $g): ?>
        <option value="<?= $g->id ?>"><?= htmlspecialchars($g->nama) ?></option>
        <?php endforeach; ?>
    </select>
    <textarea name="jadwal" placeholder="Jadwal (opsional)"></textarea>
    <button type="submit">Tambah Mata Pelajaran</button>
</form>
```

4. **Tabel Daftar Kelas:**
```html
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Kelas</th>
            <th>Tahun Ajaran</th>
            <th>Jumlah Siswa</th>
            <th>Jumlah Mata Pelajaran</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($kelas as $k): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($k->nama_kelas) ?></td>
            <td><?= htmlspecialchars($k->tahun_ajaran) ?></td>
            <td><?= $kelasModel->getTotalSiswaByKelas($k->id) ?></td>
            <td><?= count($kelasModel->getMataPelajaranInKelas($k->id)) ?></td>
            <td>
                <button onclick="manageMataPelajaran(<?= $k->id ?>)">Manage Mata Pelajaran</button>
                <button onclick="manageSiswa(<?= $k->id ?>)">Manage Siswa</button>
                <button onclick="editKelas(<?= $k->id ?>)">Edit</button>
                <button onclick="deleteKelas(<?= $k->id ?>)">Hapus</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
```

5. **JavaScript untuk Manage Mata Pelajaran:**
```javascript
function manageMataPelajaran(kelasId) {
    // Modal untuk tambah/hapus mata pelajaran dari kelas
    fetch('index.php?action=admin_get_matapelajaran_dalam_kelas&kelas_id=' + kelasId)
        .then(response => response.json())
        .then(data => {
            // Tampilkan modal dengan list mata pelajaran
            showMataPelajaranModal(kelasId, data);
        });
}

function addMataPelajaranToKelas(kelasId, mapelId) {
    fetch('index.php?action=admin_add_matapelajaran_to_kelas', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'kelas_id=' + kelasId + '&mata_pelajaran_id=' + mapelId
    })
    .then(response => response.json())
    .then(result => {
        if(result.success) {
            alert('Mata pelajaran berhasil ditambahkan!');
            location.reload();
        }
    });
}
```

### Step 7: Testing Fungsionalitas ✅

**Test Checklist:**

#### Admin Panel:
- [ ] Buat kelas baru (X RPL 1, tahun 2025/2026)
- [ ] Edit kelas yang sudah ada
- [ ] Hapus kelas
- [ ] Buat mata pelajaran baru (Matematika, guru: Pak Budi)
- [ ] Edit mata pelajaran
- [ ] Hapus mata pelajaran
- [ ] Tambah mata pelajaran ke kelas (Matematika → X RPL 1)
- [ ] Hapus mata pelajaran dari kelas
- [ ] Tambah siswa ke kelas
- [ ] Tambah siswa ke mata pelajaran

#### Guru:
- [ ] Login sebagai guru
- [ ] Lihat dashboard (cek tidak ada error)
- [ ] Lihat halaman "Kelas Saya" (cek mata pelajaran muncul)
- [ ] Buka presensi untuk mata pelajaran
- [ ] Tutup presensi untuk mata pelajaran
- [ ] Lihat laporan presensi per mata pelajaran
- [ ] Export laporan Excel
- [ ] Export laporan PDF

#### Siswa:
- [ ] Login sebagai siswa
- [ ] Lihat dashboard (cek tidak ada error)
- [ ] Lihat halaman "Presensi"
- [ ] Lakukan presensi ketika sesi dibuka
- [ ] Lihat riwayat presensi
- [ ] Ajukan izin

### Step 8: Troubleshooting Common Issues 🔍

#### Error: "Call to undefined method"
**Penyebab:** Method baru di model belum ada atau typo.
**Solusi:** Cek kembali file model yang sudah diupdate.

#### Error: "Unknown column 'tahun_ajaran' in mata_pelajaran"
**Penyebab:** Migration belum dijalankan atau gagal.
**Solusi:** Jalankan ulang migration SQL.

#### Error: "Unknown column 'kelas_id' in presensi_sesi"
**Penyebab:** Migration untuk presensi_sesi belum dijalankan.
**Solusi:** Jalankan ulang migration atau manual ALTER TABLE.

#### Presensi tidak bisa dibuka
**Penyebab:** Relasi kelas-mata pelajaran belum diset.
**Solusi:** Pastikan mata pelajaran sudah dihubungkan dengan kelas melalui table `kelas_mata_pelajaran`.

#### Siswa tidak muncul saat presensi
**Penyebab:** Siswa belum ditambahkan ke mata pelajaran atau kelas.
**Solusi:** 
1. Tambahkan siswa ke kelas (table `siswa_kelas`)
2. Tambahkan siswa ke mata pelajaran (table `siswa_mata_pelajaran`)

### Step 9: Populate Data Awal (Optional) 📊

Jika Anda memulai dari fresh install, populate data berikut:

```sql
-- 1. Buat beberapa kelas
INSERT INTO kelas (nama_kelas, tahun_ajaran) VALUES
('X RPL 1', '2025/2026'),
('X RPL 2', '2025/2026'),
('XI RPL 1', '2025/2026'),
('XI RPL 2', '2025/2026'),
('XII RPL 1', '2025/2026'),
('XII RPL 2', '2025/2026');

-- 2. Buat beberapa mata pelajaran (sesuaikan guru_pengampu dengan id guru Anda)
INSERT INTO mata_pelajaran (nama_mata_pelajaran, guru_pengampu, jadwal) VALUES
('Matematika', 2, 'Senin 07:00-09:00'),
('Bahasa Indonesia', 2, 'Senin 09:00-11:00'),
('Pemrograman Web', 2, 'Selasa 07:00-10:00'),
('Basis Data', 2, 'Rabu 07:00-09:00'),
('Pemrograman Mobile', 2, 'Kamis 07:00-10:00');

-- 3. Hubungkan kelas dengan mata pelajaran
-- Contoh: X RPL 1 memiliki semua mata pelajaran
INSERT INTO kelas_mata_pelajaran (kelas_id, mata_pelajaran_id) 
SELECT 1, id FROM mata_pelajaran;

-- X RPL 2 juga memiliki semua mata pelajaran
INSERT INTO kelas_mata_pelajaran (kelas_id, mata_pelajaran_id) 
SELECT 2, id FROM mata_pelajaran;

-- 4. Tambahkan siswa ke kelas (contoh siswa id 3, 5, 7, 8 ke kelas 1)
INSERT INTO siswa_kelas (siswa_id, kelas_id) VALUES
(3, 1),
(5, 1),
(7, 1),
(8, 1);

-- 5. Tambahkan siswa ke mata pelajaran (otomatis ke semua mata pelajaran di kelas mereka)
INSERT INTO siswa_mata_pelajaran (siswa_id, mata_pelajaran_id)
SELECT sk.siswa_id, kmp.mata_pelajaran_id
FROM siswa_kelas sk
INNER JOIN kelas_mata_pelajaran kmp ON sk.kelas_id = kmp.kelas_id
WHERE NOT EXISTS (
    SELECT 1 FROM siswa_mata_pelajaran smp 
    WHERE smp.siswa_id = sk.siswa_id 
    AND smp.mata_pelajaran_id = kmp.mata_pelajaran_id
);
```

### Step 10: Dokumentasi dan Deployment 📚

1. **Update README.md** dengan informasi struktur baru
2. **Backup kode lama** sebelum deploy
3. **Test di environment staging** dulu jika ada
4. **Deploy ke production** dengan hati-hati
5. **Monitor error logs** setelah deployment

## Summary of Files Changed

### Model Files (Updated):
- ✅ `app/models/KelasModel.php` - Full rewrite
- ✅ `app/models/MataPelajaranModel.php` - Removed tahun_ajaran
- ✅ `app/models/PresensiSesiModel.php` - Changed kelas_id to mata_pelajaran_id

### Controller Files (Updated):
- ✅ `app/controllers/AdminController.php` - Added KelasModel, new methods
- ✅ `app/controllers/GuruController.php` - Updated to use mata_pelajaran_id
- ✅ `app/controllers/SiswaController.php` - Updated session checks

### Database Files (New):
- ✅ `db/migrations/migration_kelas_matapelajaran_restructure.sql`

### Documentation Files (New):
- ✅ `PANDUAN_STRUKTUR_KELAS_MATAPELAJARAN.md`
- ✅ `ROUTING_UPDATE_KELAS_MATAPELAJARAN.md`
- ✅ `QUICK_SETUP_KELAS_MATAPELAJARAN.md` (this file)

### View Files (Need Manual Update):
- ⚠️ `app/views/admin/kelas.php` - **NEEDS MAJOR UPDATE**
- ⚠️ `app/views/guru/kelas.php` - Minor updates if needed
- ⚠️ `app/views/siswa/presensi.php` - Minor updates if needed

### Routing File (Need Manual Update):
- ⚠️ `index.php` - **ADD NEW ROUTES**

## Next Steps

Setelah setup selesai, Anda bisa:

1. **Customize UI** - Update views untuk tampilan yang lebih baik
2. **Add Features** - Tambah fitur seperti jadwal otomatis, notifikasi, dll
3. **Optimize** - Optimize query dan add caching jika diperlukan
4. **Security** - Review dan improve security measures

## Support & Help

Jika mengalami masalah:
1. Cek error log di `logs/` folder (jika ada)
2. Cek browser console untuk JavaScript errors
3. Review dokumentasi: `PANDUAN_STRUKTUR_KELAS_MATAPELAJARAN.md`
4. Check database structure dengan DESCRIBE dan SHOW queries

---
**Happy Coding! 🚀**
**Date:** 4 Maret 2026
**Version:** 2.0
