# Update: Nonaktifkan Validasi GPS untuk Izin/Sakit

## Tanggal: 22 Desember 2025

## Ringkasan Perubahan
Algoritma Haversine dan validasi geotagging (GPS) telah dinonaktifkan untuk jenis presensi "Izin" dan "Sakit". Siswa yang mengajukan izin atau sakit tidak perlu berada di lokasi sekolah.

---

## Perubahan Backend

### File: `app/controllers/SiswaController.php`

#### Method: `submitPresensiSekolah()`
**Sebelum:**
- Selalu melakukan validasi lokasi GPS dengan algoritma Haversine
- Memerlukan siswa berada dalam radius 100m dari sekolah

**Sesudah:**
```php
// Jika izin atau sakit, nonaktifkan validasi GPS (set koordinat ke 0)
if ($jenis === 'izin' || $jenis === 'sakit') {
    $latitude = 0;
    $longitude = 0;
    $distance = 0;
    $isValid = true; // Otomatis valid untuk izin/sakit
} else {
    // Untuk hadir, validasi lokasi GPS dengan algoritma Haversine
    $distance = $this->locationModel->getDistance($latitude, $longitude);
    $isValid = $this->locationModel->validateLocation($latitude, $longitude);
}
```

**Logika:**
- ✅ **Izin/Sakit**: Koordinat diset 0, jarak 0, status otomatis "valid"
- ✅ **Hadir**: Tetap menggunakan validasi GPS normal dengan algoritma Haversine

#### Method: `submitPresensiKelas()`
Perubahan yang sama diterapkan pada presensi kelas.

---

## Perubahan Frontend

### File: `app/views/siswa/presensi.php`

#### JavaScript: `submitPresensiSekolah()`
**Logika baru:**
```javascript
// Untuk izin/sakit, tidak perlu validasi lokasi GPS
if (jenis === 'izin' || jenis === 'sakit') {
    // Langsung submit tanpa cek lokasi
    fd.append('latitude', 0);
    fd.append('longitude', 0);
    // ... submit ke server
    return;
}

// Untuk hadir, perlu validasi lokasi GPS
if (!userLocation) {
    showNotification('error', 'Lokasi belum tersedia. Pastikan GPS aktif.');
    return;
}
```

#### JavaScript: `submitPresensiKelas()`
Implementasi yang sama untuk presensi kelas.

#### Event Listener: Jenis Presensi
**Fitur baru:**
- Saat memilih "Izin" atau "Sakit": Button presensi aktif tanpa perlu validasi lokasi
- Saat memilih "Hadir": Button hanya aktif jika lokasi valid (dalam radius 100m)

```javascript
document.getElementById('jenisPresensiSekolah').addEventListener('change', function() {
    if (this.value === 'izin' || this.value === 'sakit') {
        // Enable button untuk izin/sakit tanpa cek lokasi
        if (sessionActive && !sessionAlreadyPresenced) {
            presensiSekolahBtn.disabled = false;
        }
    } else {
        // Untuk hadir, perlu validasi lokasi
        presensiSekolahBtn.disabled = distance > 100;
    }
});
```

#### UI Improvements
**Dropdown Options:**
- "Hadir (Perlu validasi lokasi GPS)" - Jelas bahwa perlu GPS
- "Izin (Tanpa validasi lokasi)" - Jelas tidak perlu GPS
- "Sakit (Tanpa validasi lokasi)" - Jelas tidak perlu GPS

**Info Text:**
- Menampilkan informasi bahwa validasi GPS hanya untuk "Hadir"
- Label "Alasan" ditandai dengan asterisk (*) sebagai field wajib

---

## Manfaat Perubahan

### 1. **User Experience Lebih Baik**
- ✅ Siswa tidak perlu pergi ke sekolah untuk mengajukan izin/sakit
- ✅ Bisa submit izin/sakit dari rumah atau rumah sakit
- ✅ Lebih sesuai dengan kondisi real siswa yang sakit/berhalangan

### 2. **Logika Bisnis Lebih Masuk Akal**
- ✅ Izin = Tidak bisa datang ke sekolah → Tidak masuk akal minta validasi GPS
- ✅ Sakit = Sedang tidak sehat → Tidak mungkin ke sekolah
- ✅ Hadir = Benar-benar datang → Tetap perlu validasi GPS

### 3. **Fleksibilitas Sistem**
- ✅ Mendukung berbagai skenario kehadiran
- ✅ Data tetap tercatat di database dengan koordinat 0,0 untuk izin/sakit
- ✅ Mudah membedakan data: koordinat 0,0 = izin/sakit, koordinat real = hadir

---

## Cara Kerja Sistem Baru

### Untuk Siswa yang Izin/Sakit:
1. Buka halaman **Presensi**
2. Pilih jenis: **Izin** atau **Sakit**
3. Form alasan muncul otomatis (wajib diisi)
4. Opsional upload bukti (surat keterangan, foto, dll)
5. Klik tombol **Presensi Sekolah/Kelas**
6. ✅ Sukses - Tanpa perlu validasi GPS!

### Untuk Siswa yang Hadir:
1. Buka halaman **Presensi**
2. Pilih jenis: **Hadir** (default)
3. Pastikan GPS aktif dan berada dalam radius 100m
4. Klik tombol **Presensi Sekolah/Kelas**
5. ✅ Validasi GPS dengan algoritma Haversine
6. ✅ Sukses jika lokasi valid

---

## Data di Database

### Contoh Record Presensi Sekolah:

**Izin/Sakit:**
```
id: 123
user_id: 8
latitude: 0
longitude: 0
jarak: 0
status: "valid"
jenis: "izin" / "sakit"
alasan: "Sakit demam"
foto_bukti: "uploads/izin/bukti-xxx.jpg"
```

**Hadir:**
```
id: 124
user_id: 8
latitude: -7.649859
longitude: 110.413132
jarak: 29.80
status: "valid"
jenis: "hadir"
alasan: NULL
foto_bukti: NULL
```

**Cara Identifikasi:**
- Jika `latitude = 0` dan `longitude = 0` → Izin/Sakit (no GPS)
- Jika `latitude ≠ 0` dan `longitude ≠ 0` → Hadir (dengan GPS)

---

## Testing Checklist

### Test Presensi Izin:
- [x] Pilih jenis "Izin"
- [x] Form alasan muncul
- [x] Button aktif tanpa perlu GPS
- [x] Submit berhasil dengan koordinat 0,0
- [x] Data tersimpan dengan status "valid"

### Test Presensi Sakit:
- [x] Pilih jenis "Sakit"
- [x] Form alasan muncul
- [x] Upload bukti berhasil
- [x] Button aktif tanpa perlu GPS
- [x] Submit berhasil dengan koordinat 0,0

### Test Presensi Hadir:
- [x] Pilih jenis "Hadir"
- [x] Form alasan tersembunyi
- [x] Button hanya aktif jika GPS valid
- [x] Validasi Haversine berjalan normal
- [x] Submit ditolak jika di luar radius

### Test UI/UX:
- [x] Info GPS tampil dengan jelas
- [x] Label "wajib" ditampilkan untuk alasan
- [x] Button enable/disable sesuai kondisi
- [x] Notifikasi sukses menampilkan jenis yang benar

---

## Catatan Teknis

### Validasi Lokasi (LocationModel):
```php
// Method ini TIDAK dipanggil untuk izin/sakit
public function getDistance($latitude, $longitude) {
    // Algoritma Haversine untuk hitung jarak
    // Hanya dipanggil untuk jenis "hadir"
}

public function validateLocation($latitude, $longitude) {
    // Validasi apakah dalam radius 100m
    // Hanya dipanggil untuk jenis "hadir"
}
```

### Security:
- ✅ Server-side validation tetap ada
- ✅ Tidak bisa bypass dengan manipulasi frontend
- ✅ Session management tetap berjalan normal
- ✅ Duplicate prevention tetap aktif

---

## Rollback (Jika Diperlukan)

Jika ingin kembali ke sistem lama (semua jenis harus validasi GPS):

1. Restore `SiswaController.php`:
   - Hapus kondisi `if ($jenis === 'izin' || $jenis === 'sakit')`
   - Kembali ke validasi GPS untuk semua jenis

2. Restore `presensi.php` (JavaScript):
   - Hapus kondisi skip validasi untuk izin/sakit
   - Restore requirement `!userLocation` di awal fungsi

3. Update UI text:
   - Hapus "(Tanpa validasi lokasi)" dari dropdown options
   - Restore info text original

---

## Kesimpulan

Perubahan ini membuat sistem lebih realistis dan user-friendly:
- ✅ **Izin/Sakit**: Tidak perlu GPS → Masuk akal
- ✅ **Hadir**: Perlu GPS → Tetap terkontrol
- ✅ **Data tetap terstruktur** dengan koordinat 0,0 sebagai penanda
- ✅ **Fleksibel** untuk berbagai skenario kehadiran

---

**Last Updated:** 22 Desember 2025  
**Version:** 2.1
