# 🚀 QUICK SETUP GUIDE - Sistem Kelas dan Mata Pelajaran

## ⚡ Langkah Cepat

### 1. Backup Database
```bash
# Di phpMyAdmin: Export database Anda
# Atau gunakan mysqldump:
mysqldump -u root -p presensi_smk > backup_$(date +%Y%m%d).sql
```

### 2. Jalankan Migration
```bash
# Di MySQL atau phpMyAdmin, jalankan file:
db/migrations/migration_restructure_kelas_mata_pelajaran.sql
```

### 3. Verifikasi Struktur Database
Pastikan tabel berikut sudah dibuat:
- ✅ `kelas`
- ✅ `mata_pelajaran`
- ✅ `kelas_mata_pelajaran`
- ✅ `siswa_kelas`

### 4. Test Sistem
1. Login sebagai Admin
2. Buka menu "Manajemen Kelas"
3. Coba buat kelas baru:
   - Nama: X Akuntansi 1
   - Tahun: 2025/2026
4. Tambah mata pelajaran ke kelas
5. Tambah siswa ke kelas

## 📋 Contoh Skenario Penggunaan

### Scenario 1: Membuat Kelas Baru
```
1. Klik "Tambah Kelas"
2. Nama Kelas: "X RPL 1"
3. Tahun Ajaran: "2025/2026"
4. Simpan
```

### Scenario 2: Menambah Mata Pelajaran
```
1. Klik "Mata Pelajaran" pada kelas "X RPL 1"
2. Pilih: "Pemrograman Web"
3. Guru: "Pak Budi"
4. Jadwal: "Senin, 08:00-10:00"
5. Tambah Mata Pelajaran
```

### Scenario 3: Daftarkan Siswa
```
1. Klik "Siswa" pada kelas "X RPL 1"
2. Pilih siswa dari dropdown
3. Klik "Tambah Siswa"
```

## 🎯 Fitur Utama

### 1. Manajemen Kelas
- ✅ CRUD Kelas (Create, Read, Update, Delete)
- ✅ List semua kelas dengan statistik
- ✅ Edit info kelas (nama, tahun ajaran)

### 2. Manajemen Mata Pelajaran di Kelas
- ✅ Tambah mata pelajaran ke kelas
- ✅ Set guru pengampu per mata pelajaran per kelas
- ✅ Set jadwal per mata pelajaran per kelas
- ✅ Hapus mata pelajaran dari kelas

### 3. Manajemen Siswa di Kelas
- ✅ Tambah siswa ke kelas
- ✅ Hapus siswa dari kelas
- ✅ View daftar siswa per kelas

## 🔧 API Endpoints

### Kelas
- `GET index.php?action=admin_kelas` - Halaman manajemen kelas
- `POST index.php?action=admin_create_kelas` - Buat kelas baru
- `POST index.php?action=admin_update_kelas` - Update kelas
- `POST index.php?action=admin_delete_kelas` - Hapus kelas

### Mata Pelajaran dalam Kelas
- `GET index.php?action=admin_get_matapelajaran_kelas&kelas_id={id}` - Get mata pelajaran (JSON)
- `GET index.php?action=admin_get_matapelajaran_tersedia&kelas_id={id}` - Get tersedia (JSON)
- `POST index.php?action=admin_add_matapelajaran_kelas` - Tambah mata pelajaran
- `POST index.php?action=admin_remove_matapelajaran_kelas` - Hapus mata pelajaran

### Siswa dalam Kelas
- `GET index.php?action=admin_get_siswa_kelas&kelas_id={id}` - Get siswa (JSON)
- `GET index.php?action=admin_get_siswa_tersedia&kelas_id={id}` - Get tersedia (JSON)
- `POST index.php?action=admin_add_siswa_kelas` - Tambah siswa
- `POST index.php?action=admin_remove_siswa_kelas` - Hapus siswa

## ⚠️ Troubleshooting

### Error: Undefined variable $kelasList
**Solusi:** Pastikan di `AdminController::kelas()` sudah ada:
```php
$kelasList = $this->kelasModel->getAllKelas();
```

### Error: Call to undefined method
**Solusi:** Pastikan sudah require KelasModel di AdminController:
```php
require_once __DIR__ . '/../models/KelasModel.php';
```

### Error: Table 'kelas' doesn't exist
**Solusi:** Jalankan migration SQL terlebih dahulu

### Modal tidak muncul
**Solusi:** 
1. Check console browser untuk error JavaScript
2. Pastikan jQuery/Bootstrap sudah loaded
3. Clear browser cache

## 📖 Dokumentasi Lengkap
Lihat file: `DOKUMENTASI_PERUBAHAN_KELAS_MAPEL.md`

## 🎓 Training Video (Coming Soon)
- Video tutorial cara menggunakan sistem baru
- Screencast demo lengkap

---

**Need Help?** Contact: Tim Developer  
**Last Updated:** 4 Maret 2026
