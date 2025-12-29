# Fitur Upload Multiple File PDF Buku Induk

## Deskripsi
Fitur ini memungkinkan upload lebih dari satu file PDF untuk dokumen buku induk siswa. Setiap dokumen dapat memiliki keterangan/deskripsi opsional.

## Perubahan yang Dilakukan

### 1. Database Schema
**File:** `db/migrations/add_buku_induk_dokumen.sql`

Menambahkan tabel baru `buku_induk_dokumen`:
- `id` - Primary key
- `buku_induk_id` - Foreign key ke tabel `buku_induk`
- `nama_file` - Nama file asli yang diupload
- `path_file` - Path lengkap file di server
- `keterangan` - Deskripsi/keterangan dokumen (opsional)
- `created_at` - Timestamp saat dokumen diupload

### 2. Model
**File:** `app/models/BukuIndukModel.php`

Menambahkan method baru:
- `getDokumen($bukuIndukId)` - Mengambil semua dokumen untuk buku induk tertentu
- `addDokumen($data)` - Menambahkan dokumen baru
- `deleteDokumen($id)` - Menghapus dokumen berdasarkan ID
- `getDokumenById($id)` - Mengambil detail dokumen berdasarkan ID

### 3. Controller Siswa
**File:** `app/controllers/SiswaController.php`

**Perubahan:**
- Method `bukuInduk()` - Ditambahkan pengambilan data dokumen
- Method `saveBukuInduk()` - Ditambahkan logic untuk handle multiple file upload
- Method `deleteDokumen()` - Method baru untuk menghapus dokumen

**Cara Kerja Upload Multiple Files:**
1. Loop melalui semua file yang diupload via `$_FILES['dokumen_files']`
2. Validasi setiap file (tipe PDF, ukuran)
3. Upload file ke folder `public/uploads/buku_induk/`
4. Simpan informasi file ke database

### 4. Controller Admin Kesiswaan
**File:** `app/controllers/AdminKesiswaanController.php`

**Perubahan:**
- Method `bukuInduk()` - Ditambahkan pengambilan data dokumen untuk setiap record
- Method `saveBukuInduk()` - Ditambahkan logic untuk handle multiple file upload
- Method `deleteDokumen()` - Method baru untuk menghapus dokumen

### 5. View Siswa
**File:** `app/views/siswa/buku_induk.php`

**Fitur Baru:**
- Form upload multiple PDF dengan tombol tambah/kurang input
- Input keterangan untuk setiap dokumen
- Daftar dokumen yang sudah diupload
- Tombol view dan delete untuk setiap dokumen
- JavaScript untuk dynamic form (tambah/kurang input file)

### 6. View Admin Kesiswaan
**File:** `app/views/admin_kesiswaan/buku_induk.php`

**Fitur Baru:**
- Form upload multiple PDF dengan tombol tambah/kurang input
- Input keterangan untuk setiap dokumen
- Indikator jumlah dokumen di tabel (ikon folder dengan counter)
- Modal untuk melihat semua dokumen
- Tombol view dan delete untuk setiap dokumen
- JavaScript untuk dynamic form dan modal

### 7. Routing
**File:** `public/index.php`

**Route Baru:**
- `siswa_delete_dokumen` - Untuk siswa menghapus dokumen
- `admin_kesiswaan_delete_dokumen` - Untuk admin kesiswaan menghapus dokumen

## Cara Penggunaan

### Untuk Siswa:
1. Login sebagai siswa
2. Masuk ke menu "Buku Induk"
3. Scroll ke bagian "Upload Dokumen Tambahan"
4. Klik tombol "+" untuk menambah input file baru
5. Pilih file PDF dan masukkan keterangan (opsional)
6. Klik "Simpan"
7. Dokumen yang sudah diupload akan tampil di bawah form
8. Klik ikon mata untuk melihat dokumen
9. Klik ikon sampah untuk menghapus dokumen

### Untuk Admin Kesiswaan:
1. Login sebagai admin kesiswaan
2. Masuk ke menu "Buku Induk"
3. Isi atau edit data siswa
4. Di bagian "Upload Dokumen Tambahan", klik "+" untuk menambah input file
5. Pilih file PDF dan masukkan keterangan (opsional)
6. Klik "Simpan"
7. Di tabel, akan muncul indikator jumlah dokumen (ikon folder dengan angka)
8. Klik ikon folder untuk melihat semua dokumen dalam modal
9. Di dalam modal, bisa view atau delete dokumen

## Instalasi

### Langkah 1: Jalankan Migration Database
Jalankan query SQL dari file `db/migrations/add_buku_induk_dokumen.sql` di database Anda:

```bash
mysql -u root -p presensi_smk < db/migrations/add_buku_induk_dokumen.sql
```

Atau import manual via phpMyAdmin.

### Langkah 2: Pastikan Folder Upload Ada
Pastikan folder `public/uploads/buku_induk/` memiliki permission write:

```bash
chmod 777 public/uploads/buku_induk/
```

### Langkah 3: Update File
Semua file sudah diupdate, tidak perlu langkah tambahan.

## Validasi File
- **Tipe File:** Hanya PDF yang diperbolehkan
- **Ukuran Maksimal:** 2MB per file (dapat disesuaikan di method `handlePdfUpload()`)
- **Nama File:** Otomatis di-rename dengan format `buku-induk-[uniqid].pdf`

## Keamanan
- File disimpan dengan nama yang di-generate (uniqid) untuk menghindari collision
- Foreign key constraint memastikan dokumen terhapus jika buku induk dihapus
- Validasi tipe file untuk mencegah upload file berbahaya
- Only authenticated users can upload/delete documents

## Troubleshooting

### File tidak terupload
- Cek permission folder `public/uploads/buku_induk/`
- Cek setting PHP `upload_max_filesize` dan `post_max_size` di php.ini
- Pastikan file tidak melebihi 2MB

### Error saat delete dokumen
- Pastikan file exists di server
- Cek permission folder upload

### Dokumen tidak muncul
- Pastikan migration database sudah dijalankan
- Cek apakah ada error di browser console
