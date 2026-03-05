# Petunjuk Mengelola Kelas dan Mata Pelajaran

## Cara Menampilkan Nama Kelas dan Tahun Ajaran di Halaman Guru

### Langkah-langkah untuk Admin:

1. **Buat Kelas Terlebih Dahulu**
   - Login sebagai Admin
   - Buka menu "Manajemen Kelas"
   - Klik "Tambah Kelas"
   - Isi nama kelas (contoh: X-A, XI IPA 1) dan tahun ajaran (contoh: 2025/2026)

2. **Buat Mata Pelajaran dan Tentukan Guru Pengampu**
   - Buka menu "Mata Pelajaran"
   - Klik "Tambah Mata Pelajaran"
   - Isi nama mata pelajaran dan pilih guru pengampu
   - Simpan

3. **Hubungkan Mata Pelajaran ke Kelas** (PENTING!)
   - Kembali ke menu "Manajemen Kelas"
   - Pilih kelas yang ingin dikelola
   - Klik tombol untuk mengelola mata pelajaran di kelas tersebut
   - Tambahkan mata pelajaran yang sesuai ke kelas

4. **Tambahkan Siswa ke Mata Pelajaran**
   - Buka menu "Mata Pelajaran"
   - Pilih mata pelajaran
   - Tambahkan siswa yang mengikuti mata pelajaran tersebut

### Hasil di Halaman Guru:

Setelah langkah-langkah di atas dilakukan, guru akan melihat:
- Nama mata pelajaran yang diampu
- Nama kelas dan tahun ajaran (contoh: X-A - 2025/2026)
- Total siswa di mata pelajaran tersebut
- Status presensi (Aktif/Tutup)
- Tombol untuk membuka/tutup sesi presensi dan melihat laporan

### Catatan Penting:

- Jika mata pelajaran belum dihubungkan ke kelas, akan muncul pesan "Belum ditugaskan ke kelas"
- Satu mata pelajaran bisa diajarkan di beberapa kelas berbeda (misal: Matematika di X-A dan X-B)
- Setiap kombinasi mata_pelajaran-kelas akan muncul sebagai card terpisah di halaman guru

## Struktur Database Terkait:

- `kelas` - Menyimpan data kelas (nama_kelas, tahun_ajaran)
- `mata_pelajaran` - Menyimpan data mata pelajaran dan guru pengampu
- `kelas_mata_pelajaran` - Tabel relasi antara kelas dan mata pelajaran (PENTING!)
- `siswa_mata_pelajaran` - Tabel relasi antara siswa dan mata pelajaran
- `presensi_sesi` - Menyimpan sesi presensi yang dibuka guru

## Troubleshooting:

### Nama kelas tidak muncul di halaman guru?
- Pastikan relasi di tabel `kelas_mata_pelajaran` sudah dibuat
- Admin harus menghubungkan mata pelajaran ke kelas melalui menu "Manajemen Kelas"

### Mata pelajaran muncul duplikat?
- Ini normal jika mata pelajaran diajarkan di beberapa kelas berbeda
- Setiap card mewakili kombinasi mata_pelajaran dan kelas yang unik

### Siswa tidak muncul?
- Pastikan siswa sudah ditambahkan ke mata pelajaran melalui menu "Mata Pelajaran" di admin
