# Routing Update untuk Struktur Kelas dan Mata Pelajaran Baru

## Routes Baru yang Perlu Ditambahkan ke index.php

Tambahkan routes berikut ke file `index.php` Anda di bagian admin actions:

```php
// ========================================
// ADMIN: Kelas Management Routes
// ========================================

// Get mata pelajaran in kelas
case 'admin_get_matapelajaran_dalam_kelas':
    $adminController->getMataPelajaranDalamKelas();
    break;

// Get available mata pelajaran for kelas
case 'admin_get_matapelajaran_tersedia':
    $adminController->getMataPelajaranTersedia();
    break;

// Add mata pelajaran to kelas
case 'admin_add_matapelajaran_to_kelas':
    $adminController->addMataPelajaranToKelas();
    break;

// Remove mata pelajaran from kelas
case 'admin_remove_matapelajaran_from_kelas':
    $adminController->removeMataPelajaranFromKelas();
    break;

// Get siswa in kelas entity (not mata pelajaran)
case 'admin_get_siswa_in_kelas_entity':
    $adminController->getSiswaInKelasEntity();
    break;

// Add siswa to kelas entity
case 'admin_add_siswa_to_kelas_entity':
    $adminController->addSiswaToKelasEntity();
    break;

// Remove siswa from kelas entity
case 'admin_remove_siswa_from_kelas_entity':
    $adminController->removeSiswaFromKelasEntity();
    break;
```

## Routes yang Sudah Ada (Perlu Dimodifikasi)

Routes berikut sudah ada tetapi sekarang support parameter `type` untuk membedakan antara kelas dan mata_pelajaran:

```php
// Create - Sekarang support type=kelas atau type=mata_pelajaran
case 'admin_create_kelas':
    $adminController->createKelas();
    break;

// Update - Sekarang support type=kelas atau type=mata_pelajaran
case 'admin_update_kelas':
    $adminController->updateKelas();
    break;

// Delete - Sekarang support type=kelas atau type=mata_pelajaran
case 'admin_delete_kelas':
    $adminController->deleteKelas();
    break;

// Get siswa dalam mata pelajaran (unchanged)
case 'admin_siswa_dalam_kelas':
    $adminController->getSiswaDalamKelas();
    break;

// Get siswa tersedia untuk mata pelajaran (unchanged)
case 'admin_siswa_tersedia':
    $adminController->getSiswaTersedia();
    break;

// Add siswa to mata pelajaran (unchanged)
case 'admin_add_siswa':
    $adminController->addSiswaToKelas();
    break;

// Remove siswa from mata pelajaran (unchanged)
case 'admin_remove_siswa':
    $adminController->removeSiswaFromKelas();
    break;
```

## Contoh Lengkap untuk Section Admin di index.php

```php
<?php
// ... kode sebelumnya ...

// Admin routes
if(isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin') {
    switch($action) {
        // Dashboard
        case 'admin_dashboard':
            $adminController->dashboard();
            break;
            
        // Users management
        case 'admin_users':
            $adminController->users();
            break;
        case 'admin_create_user':
            $adminController->createUser();
            break;
        case 'admin_update_user':
            $adminController->updateUser();
            break;
        case 'admin_delete_user':
            $adminController->deleteUser();
            break;
            
        // Kelas & Mata Pelajaran Management
        case 'admin_kelas':
            $adminController->kelas();
            break;
        case 'admin_create_kelas':
            $adminController->createKelas(); // Supports type=kelas or type=mata_pelajaran
            break;
        case 'admin_update_kelas':
            $adminController->updateKelas(); // Supports type=kelas or type=mata_pelajaran
            break;
        case 'admin_delete_kelas':
            $adminController->deleteKelas(); // Supports type=kelas or type=mata_pelajaran
            break;
            
        // Siswa-Mata Pelajaran Management (existing)
        case 'admin_siswa_dalam_kelas':
            $adminController->getSiswaDalamKelas();
            break;
        case 'admin_siswa_tersedia':
            $adminController->getSiswaTersedia();
            break;
        case 'admin_add_siswa':
            $adminController->addSiswaToKelas();
            break;
        case 'admin_remove_siswa':
            $adminController->removeSiswaFromKelas();
            break;
            
        // ===== NEW ROUTES FOR KELAS-MATA PELAJARAN RELATIONSHIP =====
        case 'admin_get_matapelajaran_dalam_kelas':
            $adminController->getMataPelajaranDalamKelas();
            break;
        case 'admin_get_matapelajaran_tersedia':
            $adminController->getMataPelajaranTersedia();
            break;
        case 'admin_add_matapelajaran_to_kelas':
            $adminController->addMataPelajaranToKelas();
            break;
        case 'admin_remove_matapelajaran_from_kelas':
            $adminController->removeMataPelajaranFromKelas();
            break;
            
        // ===== NEW ROUTES FOR SISWA-KELAS ENTITY RELATIONSHIP =====
        case 'admin_get_siswa_in_kelas_entity':
            $adminController->getSiswaInKelasEntity();
            break;
        case 'admin_add_siswa_to_kelas_entity':
            $adminController->addSiswaToKelasEntity();
            break;
        case 'admin_remove_siswa_from_kelas_entity':
            $adminController->removeSiswaFromKelasEntity();
            break;
            
        // Lokasi management
        case 'admin_lokasi':
            $adminController->lokasi();
            break;
        case 'admin_update_lokasi':
            $adminController->updateLokasi();
            break;
            
        // Laporan
        case 'admin_laporan':
            $adminController->laporan();
            break;
        case 'admin_export_excel':
            $adminController->exportExcel();
            break;
        case 'admin_export_pdf':
            $adminController->exportPDF();
            break;
            
        // Presensi Sekolah
        case 'admin_presensi_sekolah':
            $adminController->presensiSekolah();
            break;
        case 'admin_create_presensi_sekolah':
            $adminController->createPresensiSekolah();
            break;
        case 'admin_extend_presensi_sekolah':
            $adminController->extendPresensiSekolah();
            break;
        case 'admin_close_presensi_sekolah':
            $adminController->closePresensiSekolah();
            break;
        case 'admin_delete_presensi_sekolah':
            $adminController->deletePresensiSekolah();
            break;
        case 'admin_delete_multiple_presensi_sekolah':
            $adminController->deleteMultiplePresensiSekolah();
            break;
        case 'admin_ubah_status_presensi_sekolah':
            $adminController->ubahStatusPresensiSekolah();
            break;
        case 'admin_ubah_status_presensi_kelas':
            $adminController->ubahStatusPresensiKelas();
            break;
            
        // Buku Induk
        case 'admin_buku_induk':
            $adminController->bukuInduk();
            break;
        case 'admin_save_buku_induk':
            $adminController->saveBukuInduk();
            break;
        case 'admin_delete_dokumen':
            $adminController->deleteDokumen();
            break;
            
        default:
            $adminController->dashboard();
            break;
    }
}

// ... kode selanjutnya ...
```

## AJAX Calls dari Frontend

Contoh pemanggilan AJAX untuk routes baru:

### 1. Get Mata Pelajaran dalam Kelas
```javascript
fetch('index.php?action=admin_get_matapelajaran_dalam_kelas&kelas_id=' + kelasId)
    .then(response => response.json())
    .then(data => {
        // data adalah array mata pelajaran
        console.log(data);
    });
```

### 2. Add Mata Pelajaran to Kelas
```javascript
fetch('index.php?action=admin_add_matapelajaran_to_kelas', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'kelas_id=' + kelasId + '&mata_pelajaran_id=' + mapelId
})
.then(response => response.json())
.then(result => {
    if(result.success) {
        alert('Mata pelajaran berhasil ditambahkan ke kelas!');
    }
});
```

### 3. Remove Mata Pelajaran from Kelas
```javascript
fetch('index.php?action=admin_remove_matapelajaran_from_kelas', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'kelas_id=' + kelasId + '&mata_pelajaran_id=' + mapelId
})
.then(response => response.json())
.then(result => {
    if(result.success) {
        alert('Mata pelajaran berhasil dihapus dari kelas!');
    }
});
```

### 4. Create Kelas (bukan Mata Pelajaran)
```javascript
fetch('index.php?action=admin_create_kelas', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'type=kelas&nama_kelas=' + encodeURIComponent(namaKelas) + '&tahun_ajaran=' + tahunAjaran
})
.then(response => {
    window.location.reload(); // Redirect after success
});
```

### 5. Create Mata Pelajaran (bukan Kelas)
```javascript
fetch('index.php?action=admin_create_kelas', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'type=mata_pelajaran&nama_mata_pelajaran=' + encodeURIComponent(namaMapel) + 
          '&guru_pengampu=' + guruId + '&jadwal=' + encodeURIComponent(jadwal)
})
.then(response => {
    window.location.reload();
});
```

## Form Examples

### Form Tambah Kelas
```html
<form method="POST" action="index.php?action=admin_create_kelas">
    <input type="hidden" name="type" value="kelas">
    <input type="text" name="nama_kelas" placeholder="Nama Kelas" required>
    <input type="text" name="tahun_ajaran" placeholder="Tahun Ajaran" required>
    <button type="submit">Simpan Kelas</button>
</form>
```

### Form Tambah Mata Pelajaran
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
    <textarea name="jadwal" placeholder="Jadwal"></textarea>
    <button type="submit">Simpan Mata Pelajaran</button>
</form>
```

## Testing Checklist

Setelah menambahkan routes, test:

- [ ] GET admin_get_matapelajaran_dalam_kelas?kelas_id=1
- [ ] GET admin_get_matapelajaran_tersedia?kelas_id=1
- [ ] POST admin_add_matapelajaran_to_kelas (dengan kelas_id & mata_pelajaran_id)
- [ ] POST admin_remove_matapelajaran_from_kelas (dengan kelas_id & mata_pelajaran_id)
- [ ] GET admin_get_siswa_in_kelas_entity?kelas_id=1
- [ ] POST admin_add_siswa_to_kelas_entity (dengan kelas_id & siswa_id)
- [ ] POST admin_remove_siswa_from_kelas_entity (dengan kelas_id & siswa_id)
- [ ] POST admin_create_kelas (dengan type=kelas)
- [ ] POST admin_create_kelas (dengan type=mata_pelajaran)
- [ ] POST admin_update_kelas (dengan type=kelas)
- [ ] POST admin_update_kelas (dengan type=mata_pelajaran)
- [ ] POST admin_delete_kelas (dengan type=kelas)
- [ ] POST admin_delete_kelas (dengan type=mata_pelajaran)

---
**Update:** 4 Maret 2026
