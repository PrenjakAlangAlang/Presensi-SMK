# Fitur Presensi Izin/Sakit Tanpa GPS

## Deskripsi
Fitur ini memungkinkan siswa untuk melakukan presensi dengan status **Izin** atau **Sakit** tanpa memerlukan validasi GPS (algoritma Haversine) dan geotagging.

## Cara Kerja

### Backend (SiswaController.php)

Ketika siswa memilih jenis presensi **izin** atau **sakit**:

1. **Koordinat GPS diset ke 0**
   - `latitude = 0`
   - `longitude = 0`
   - `distance = 0`

2. **Status otomatis valid**
   - `status = 'valid'`
   - Tidak ada pengecekan jarak dengan algoritma Haversine
   - Tidak ada validasi lokasi GPS

3. **Data yang disimpan**
   - `jenis`: 'izin' atau 'sakit'
   - `alasan`: Wajib diisi
   - `foto_bukti`: Opsional (JPG, PNG, atau PDF maks 2MB)

### Frontend (presensi.php)

#### Presensi Sekolah
- Ketika memilih "Izin" atau "Sakit":
  - Form alasan dan bukti muncul
  - Tombol berubah dari "Presensi Sekolah" → "Submit"
  - Info GPS berubah: "Untuk izin/sakit, tidak perlu validasi GPS"
  - Tombol aktif tanpa perlu validasi lokasi
  - Submit langsung dengan koordinat 0,0

#### Presensi Kelas
- Ketika memilih "Izin" atau "Sakit":
  - Form alasan dan bukti muncul
  - Tombol berubah dari "Presensi Kelas" → "Submit"
  - Info GPS berubah: "Untuk izin/sakit, tidak perlu validasi GPS"
  - Status kelas: "Siap submit izin/sakit"
  - Tombol aktif jika ada sesi kelas aktif (tanpa perlu GPS)

#### Presensi Hadir (Normal)
- Tetap memerlukan validasi GPS
- Menggunakan algoritma Haversine
- Harus dalam radius 100m dari sekolah
- Koordinat GPS real dari device

## Validasi

### Wajib Diisi
- **Alasan**: Harus diisi jika memilih izin/sakit
- **Bukti**: Opsional (tapi direkomendasikan)

### Format Bukti
- **Gambar**: JPG, PNG
- **Dokumen**: PDF
- **Ukuran maksimal**: 2MB

## Keamanan
- Validasi di backend tetap dilakukan
- Data alasan dan bukti disimpan untuk audit
- Guru/Admin dapat melihat keterangan lengkap di laporan

## Laporan
Guru dan admin dapat melihat:
- Alasan izin/sakit di kolom "Keterangan"
- Link untuk melihat bukti yang diupload
- Status presensi dengan badge warna berbeda:
  - Izin: Badge kuning
  - Sakit: Badge oranye
