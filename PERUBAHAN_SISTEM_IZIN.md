# Dokumentasi Perubahan Sistem Izin

## Ringkasan Perubahan
Sistem izin telah diintegrasikan langsung ke dalam halaman presensi siswa. Sekarang siswa dapat memilih jenis presensi (Hadir, Izin, Sakit) beserta alasan dan bukti pendukung langsung dari halaman presensi, tanpa perlu mengakses halaman izin terpisah.

## Perubahan Database

### File: `db/migrations/update_presensi_izin.sql`
Script SQL untuk update database schema:

1. **Tambah kolom ke tabel `presensi_sekolah`:**
   - `alasan` (TEXT, nullable) - Alasan izin/sakit
   - `foto_bukti` (VARCHAR 255, nullable) - Path file bukti

2. **Tambah kolom ke tabel `presensi_kelas`:**
   - `alasan` (TEXT, nullable) - Alasan izin/sakit
   - `foto_bukti` (VARCHAR 255, nullable) - Path file bukti

3. **Hapus tabel `izin_siswa`** - Tidak diperlukan lagi

### Cara Menjalankan Migrasi:
```bash
# Via phpMyAdmin atau MySQL CLI
mysql -u root -p presensi_smk < db/migrations/update_presensi_izin.sql
```

## Perubahan Backend

### 1. PresensiModel.php
- ✅ Update `recordPresensiSekolah()` untuk menyimpan `alasan` dan `foto_bukti`
- ✅ Update `recordPresensiKelas()` untuk menyimpan `alasan` dan `foto_bukti`

### 2. SiswaController.php
- ✅ Update `submitPresensiSekolah()` untuk handle:
  - Parameter `jenis` (hadir/izin/sakit)
  - Parameter `alasan` (text)
  - File upload `bukti` (image/pdf)
  
- ✅ Update `submitPresensiKelas()` untuk handle:
  - Parameter `jenis` (hadir/izin/sakit)
  - Parameter `alasan` (text)
  - File upload `bukti` (image/pdf)

- ✅ Hapus method `izin()` - tidak diperlukan lagi
- ✅ Hapus method `ajukanIzin()` - tidak diperlukan lagi
- ✅ Rename `handleIzinUpload()` menjadi `handleBuktiUpload()` untuk konsistensi

## Perubahan Frontend

### 1. app/views/siswa/presensi.php
**Penambahan UI untuk Presensi Sekolah:**
- ✅ Dropdown pemilihan jenis presensi (Hadir/Izin/Sakit)
- ✅ Form alasan (tampil otomatis jika pilih Izin/Sakit)
- ✅ Input upload bukti (opsional, format: JPG/PNG/PDF, max 2MB)

**Penambahan UI untuk Presensi Kelas:**
- ✅ Dropdown pemilihan jenis presensi (Hadir/Izin/Sakit)
- ✅ Form alasan (tampil otomatis jika pilih Izin/Sakit)
- ✅ Input upload bukti (opsional, format: JPG/PNG/PDF, max 2MB)

**Update JavaScript:**
- ✅ Event listener untuk show/hide form alasan berdasarkan jenis presensi
- ✅ Validasi alasan wajib diisi untuk Izin/Sakit
- ✅ Submit form dengan FormData (mendukung file upload)
- ✅ Notifikasi sukses menampilkan jenis presensi yang dipilih

### 2. File yang Dihapus
- ✅ `app/views/siswa/izin.php` - Tidak diperlukan lagi

### 3. Routing (public/index.php)
- ✅ Hapus route `siswa_izin`
- ✅ Hapus route `ajukan_izin`
- ✅ Hapus dari allowed actions array

### 4. Navigation (app/views/layouts/sidebar.php)
- ✅ Hapus menu "Ajukan Izin" dari sidebar siswa

### 5. Dashboard (app/views/siswa/dashboard.php)
- ✅ Ubah shortcut "Ajukan Izin" menjadi "Buku Induk"

## Cara Penggunaan Baru

### Untuk Siswa:
1. Buka halaman **Presensi** (bukan Izin lagi)
2. Pilih jenis presensi dari dropdown:
   - **Hadir** - Presensi normal berbasis GPS
   - **Izin** - Tidak bisa hadir karena keperluan tertentu
   - **Sakit** - Tidak bisa hadir karena sakit

3. Jika pilih **Izin** atau **Sakit**:
   - Form alasan akan muncul otomatis
   - Wajib mengisi alasan
   - Opsional upload bukti (surat keterangan, foto, dll)

4. Klik tombol **Presensi Sekolah** atau **Presensi Kelas**

### Validasi:
- ✅ Alasan wajib diisi untuk jenis Izin/Sakit
- ✅ Bukti opsional, format: JPG, PNG, PDF
- ✅ Maksimal ukuran file: 2MB
- ✅ Lokasi GPS tetap divalidasi (untuk data jarak)
- ✅ Cegah duplikat presensi per sesi

## Keuntungan Sistem Baru

1. **User Experience Lebih Baik:**
   - Satu halaman untuk semua jenis presensi
   - Lebih intuitif dan mudah dipahami
   - Mengurangi navigasi yang tidak perlu

2. **Data Lebih Terstruktur:**
   - Semua data presensi di satu tabel
   - Konsistensi struktur data
   - Mudah untuk laporan dan statistik

3. **Kode Lebih Bersih:**
   - Menghapus duplikasi logika
   - Satu controller method untuk semua jenis presensi
   - Lebih mudah dimaintain

4. **Fleksibilitas:**
   - Mudah menambah jenis presensi baru (cukup tambah option)
   - Bukti tidak wajib (opsional)
   - Dapat digunakan untuk berbagai skenario

## Testing Checklist

### Database:
- [ ] Jalankan migration SQL
- [ ] Verifikasi kolom `alasan` dan `foto_bukti` ada di tabel `presensi_sekolah`
- [ ] Verifikasi kolom `alasan` dan `foto_bukti` ada di tabel `presensi_kelas`
- [ ] Verifikasi tabel `izin_siswa` sudah terhapus

### Presensi Sekolah:
- [ ] Test presensi dengan jenis "Hadir" (tanpa alasan)
- [ ] Test presensi dengan jenis "Izin" (dengan alasan)
- [ ] Test presensi dengan jenis "Sakit" (dengan alasan + upload bukti)
- [ ] Test validasi alasan wajib untuk Izin/Sakit
- [ ] Test upload file bukti (image & PDF)
- [ ] Test validasi ukuran file max 2MB

### Presensi Kelas:
- [ ] Test presensi kelas dengan jenis "Hadir"
- [ ] Test presensi kelas dengan jenis "Izin" (dengan alasan)
- [ ] Test presensi kelas dengan jenis "Sakit" (dengan alasan + upload bukti)
- [ ] Test validasi alasan wajib untuk Izin/Sakit
- [ ] Test upload file bukti (image & PDF)

### UI/UX:
- [ ] Form alasan muncul otomatis saat pilih Izin/Sakit
- [ ] Form alasan tersembunyi saat pilih Hadir
- [ ] Notifikasi menampilkan jenis presensi yang dipilih
- [ ] Form reset setelah submit berhasil
- [ ] Link "Ajukan Izin" tidak ada di sidebar
- [ ] Link di dashboard siswa sudah diupdate

### Laporan:
- [ ] Verifikasi data alasan dan bukti muncul di laporan guru
- [ ] Verifikasi riwayat presensi siswa menampilkan semua jenis
- [ ] Verifikasi statistik kehadiran tetap akurat

## Rollback (Jika Diperlukan)

Jika ingin kembali ke sistem lama:

1. Restore tabel `izin_siswa` dari backup
2. Restore file `app/views/siswa/izin.php`
3. Restore routing di `public/index.php`
4. Restore menu di sidebar
5. Revert perubahan di `SiswaController.php`
6. Revert perubahan di `PresensiModel.php`
7. Revert perubahan di `presensi.php`

## Catatan Penting

- ⚠️ **Backup database sebelum menjalankan migration!**
- ⚠️ Data di tabel `izin_siswa` akan hilang - export dulu jika diperlukan
- ⚠️ File upload disimpan di `/public/uploads/izin/`
- ⚠️ Pastikan folder upload writable (chmod 777)

## Support

Jika ada masalah atau pertanyaan, hubungi developer atau buat issue di repository.

---
**Last Updated:** 22 Desember 2025
**Version:** 2.0
