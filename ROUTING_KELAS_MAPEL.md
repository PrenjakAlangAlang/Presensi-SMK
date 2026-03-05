# Routing & Endpoint untuk Struktur Kelas & Mata Pelajaran

## Admin - Manajemen Kelas

### Halaman Utama Kelas
```
URL: index.php?action=admin_kelas
Method: GET
Controller: AdminController->kelas()
Deskripsi: Menampilkan daftar semua kelas dengan statistik
```

### CRUD Kelas

#### Create Kelas
```
URL: index.php?action=admin_create_kelas
Method: POST
Controller: AdminController->createKelas()
Parameters:
  - nama_kelas (required)
  - tahun_ajaran (required)
Response: Redirect dengan flash message
```

#### Update Kelas
```
URL: index.php?action=admin_update_kelas
Method: POST
Controller: AdminController->updateKelas()
Parameters:
  - id (required)
  - nama_kelas (required)
  - tahun_ajaran (required)
Response: Redirect dengan flash message
```

#### Delete Kelas
```
URL: index.php?action=admin_delete_kelas
Method: POST
Controller: AdminController->deleteKelas()
Parameters:
  - id (required)
Response: Redirect dengan flash message
```

### Manajemen Siswa dalam Kelas

#### Get Siswa dalam Kelas (API)
```
URL: index.php?action=admin_get_siswa_kelas&kelas_id={id}
Method: GET
Controller: AdminController->getSiswaDalamKelas()
Response: JSON array of siswa
```

#### Get Siswa Tersedia (API)
```
URL: index.php?action=admin_get_siswa_tersedia_kelas&kelas_id={id}
Method: GET
Controller: AdminController->getSiswaTersediaKelas()
Response: JSON array of available siswa
```

#### Add Siswa ke Kelas (API)
```
URL: index.php?action=admin_add_siswa_kelas
Method: POST
Controller: AdminController->addSiswaToKelas()
Parameters:
  - siswa_id (required)
  - kelas_id (required)
Response: JSON {success: true/false, message: "..."}
```

#### Remove Siswa dari Kelas (API)
```
URL: index.php?action=admin_remove_siswa_kelas
Method: POST
Controller: AdminController->removeSiswaFromKelas()
Parameters:
  - siswa_id (required)
  - kelas_id (required)
Response: JSON {success: true/false, message: "..."}
```

### Manajemen Mata Pelajaran dalam Kelas

#### Get Mata Pelajaran dalam Kelas (API)
```
URL: index.php?action=admin_get_mapel_kelas&kelas_id={id}
Method: GET
Controller: AdminController->getMataPelajaranDalamKelas()
Response: JSON array of mata pelajaran
```

#### Get Mata Pelajaran Tersedia (API)
```
URL: index.php?action=admin_get_mapel_tersedia_kelas&kelas_id={id}
Method: GET
Controller: AdminController->getMataPelajaranTersediaKelas()
Response: JSON array of available mata pelajaran
```

#### Add Mata Pelajaran ke Kelas (API)
```
URL: index.php?action=admin_add_mapel_kelas
Method: POST
Controller: AdminController->addMataPelajaranToKelas()
Parameters:
  - mata_pelajaran_id (required)
  - kelas_id (required)
Response: JSON {success: true/false, message: "..."}
```

#### Remove Mata Pelajaran dari Kelas (API)
```
URL: index.php?action=admin_remove_mapel_kelas
Method: POST
Controller: AdminController->removeMataPelajaranFromKelas()
Parameters:
  - mata_pelajaran_id (required)
  - kelas_id (required)
Response: JSON {success: true/false, message: "..."}
```

## Admin - Manajemen Mata Pelajaran

### Halaman Mata Pelajaran
```
URL: index.php?action=admin_mata_pelajaran
Method: GET
Controller: AdminController->mataPelajaran()
Deskripsi: Menampilkan daftar semua mata pelajaran
```

### CRUD Mata Pelajaran

#### Create Mata Pelajaran
```
URL: index.php?action=admin_create_mata_pelajaran
Method: POST
Controller: AdminController->createMataPelajaran()
Parameters:
  - nama_mata_pelajaran (required)
  - guru_pengampu (required)
  - jadwal (optional)
Response: Redirect dengan flash message
```

#### Update Mata Pelajaran
```
URL: index.php?action=admin_update_mata_pelajaran
Method: POST
Controller: AdminController->updateMataPelajaran()
Parameters:
  - id (required)
  - nama_mata_pelajaran (required)
  - guru_pengampu (required)
  - jadwal (optional)
Response: Redirect dengan flash message
```

#### Delete Mata Pelajaran
```
URL: index.php?action=admin_delete_mata_pelajaran
Method: POST
Controller: AdminController->deleteMataPelajaran()
Parameters:
  - id (required)
Response: Redirect dengan flash message
```

## Guru - Mata Pelajaran

### Dashboard Guru
```
URL: index.php?action=guru_dashboard
Method: GET
Controller: GuruController->dashboard()
Deskripsi: Menampilkan mata pelajaran yang diampu dengan info kelas
```

### Halaman Mata Pelajaran Guru
```
URL: index.php?action=guru_kelas (tetap sama, tapi kontennya berbeda)
Method: GET
Controller: GuruController->kelas()
Deskripsi: Menampilkan mata pelajaran per kelas yang diampu
```

### Buka Presensi (per mata pelajaran dalam kelas)
```
URL: index.php?action=buka_presensi_kelas
Method: POST
Controller: GuruController->bukaPresensiKelas()
Parameters:
  - kelas_mapel_id (required) -- ID dari kelas_mata_pelajaran
Response: JSON {success: true/false}
```

### Tutup Presensi
```
URL: index.php?action=tutup_presensi_kelas
Method: POST
Controller: GuruController->tutupPresensiKelas()
Parameters:
  - kelas_mapel_id (required)
  - catatan (optional)
Response: JSON {success: true/false}
```

## Siswa - Mata Pelajaran

### Dashboard Siswa
```
URL: index.php?action=siswa_dashboard
Method: GET
Controller: SiswaController->dashboard()
Deskripsi: Menampilkan kelas siswa dan mata pelajaran yang diikuti
```

### Presensi Mata Pelajaran
```
URL: index.php?action=siswa_presensi_mapel
Method: POST
Controller: SiswaController->presensiMataPelajaran()
Parameters:
  - kelas_mapel_id (required)
  - status (hadir/izin/sakit)
  - keterangan (optional, required for izin/sakit)
  - bukti_file (optional, for izin/sakit)
Response: JSON {success: true/false}
```

## Struktur URL yang Direkomendasikan

Untuk konsistensi, pertimbangkan struktur berikut:

### Admin
- `/admin/kelas` - Manajemen kelas
- `/admin/mata-pelajaran` - Manajemen mata pelajaran
- `/admin/kelas/{id}/siswa` - Kelola siswa dalam kelas
- `/admin/kelas/{id}/mata-pelajaran` - Kelola mata pelajaran dalam kelas

### Guru
- `/guru/mata-pelajaran` - List mata pelajaran yang diampu
- `/guru/mata-pelajaran/{kelas_mapel_id}` - Detail mata pelajaran per kelas
- `/guru/mata-pelajaran/{kelas_mapel_id}/presensi` - Buka/tutup presensi

### Siswa
- `/siswa/kelas` - Info kelas siswa
- `/siswa/mata-pelajaran` - List mata pelajaran yang diikuti
- `/siswa/mata-pelajaran/{kelas_mapel_id}/presensi` - Presensi mata pelajaran

## Perubahan di index.php (Router)

Tambahkan routing baru di `index.php`:

```php
// Admin - Kelas Management
case 'admin_kelas':
    $controller->kelas();
    break;
case 'admin_create_kelas':
    $controller->createKelas();
    break;
case 'admin_update_kelas':
    $controller->updateKelas();
    break;
case 'admin_delete_kelas':
    $controller->deleteKelas();
    break;

// Admin - Kelas API
case 'admin_get_siswa_kelas':
    $controller->getSiswaDalamKelas();
    break;
case 'admin_get_siswa_tersedia_kelas':
    $controller->getSiswaTersediaKelas();
    break;
case 'admin_add_siswa_kelas':
    $controller->addSiswaToKelas();
    break;
case 'admin_remove_siswa_kelas':
    $controller->removeSiswaFromKelas();
    break;
case 'admin_get_mapel_kelas':
    $controller->getMataPelajaranDalamKelas();
    break;
case 'admin_get_mapel_tersedia_kelas':
    $controller->getMataPelajaranTersediaKelas();
    break;
case 'admin_add_mapel_kelas':
    $controller->addMataPelajaranToKelas();
    break;
case 'admin_remove_mapel_kelas':
    $controller->removeMataPelajaranFromKelas();
    break;

// Admin - Mata Pelajaran Management
case 'admin_mata_pelajaran':
    $controller->mataPelajaran();
    break;
case 'admin_create_mata_pelajaran':
    $controller->createMataPelajaran();
    break;
case 'admin_update_mata_pelajaran':
    $controller->updateMataPelajaran();
    break;
case 'admin_delete_mata_pelajaran':
    $controller->deleteMataPelajaran();
    break;
```

## Migrasi URL Lama ke Baru

### Yang Tetap:
- `admin_users` - Tidak berubah
- `admin_lokasi` - Tidak berubah
- `admin_laporan` - Tetap, tapi perlu update query
- `guru_dashboard` - Tetap, tapi perlu update konten
- `siswa_dashboard` - Tetap, tapi perlu update konten

### Yang Berubah:
- `admin_kelas` → Sekarang untuk manajemen **kelas**, bukan mata pelajaran
- `admin_create_kelas` → Create **kelas**, bukan mata pelajaran
- `admin_update_kelas` → Update **kelas**, bukan mata pelajaran
- `admin_delete_kelas` → Delete **kelas**, bukan mata pelajaran

### Yang Baru:
- `admin_mata_pelajaran` → Manajemen mata pelajaran (terpisah dari kelas)
- `admin_create_mata_pelajaran` → Create mata pelajaran
- `admin_*_mapel_kelas` → API untuk mengelola mata pelajaran dalam kelas
- `admin_*_siswa_kelas` → API untuk mengelola siswa dalam kelas

## Catatan Implementasi

1. **kelas_mapel_id**: Ini adalah ID dari tabel `kelas_mata_pelajaran`, bukan `mata_pelajaran_id`
2. **Backward Compatibility**: Beberapa endpoint lama (`admin_get_siswa_kelas`) masih bisa digunakan
3. **Presensi**: Sekarang menggunakan `kelas_mapel_id` untuk identifikasi unik mata pelajaran dalam kelas
4. **Session**: `presensi_sesi` perlu update untuk menyimpan `kelas_mata_pelajaran_id`
