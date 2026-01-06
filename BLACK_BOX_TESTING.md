# Black Box Testing - Aplikasi Presensi SMK

## Deskripsi
Dokumen ini berisi kasus uji black box testing untuk aplikasi Presensi SMK yang mencakup pengujian fungsi-fungsi untuk peran Admin, Admin Kesiswaan, Guru, dan Siswa.

---

## 1. MODUL AUTENTIKASI

### 1.1 Login dengan Kredensial Valid
| **Kasus Uji** | Login dengan email dan password yang valid |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Buka halaman login<br>2. Masukkan email yang terdaftar<br>3. Masukkan password yang benar<br>4. Klik tombol Login |
| **Hasil yang Diharapkan** | - Sistem memvalidasi kredensial<br>- User berhasil login<br>- Redirect ke dashboard sesuai role (Admin/Admin Kesiswaan/Guru/Siswa) |

### 1.2 Login dengan Kredensial Tidak Valid
| **Kasus Uji** | Login dengan email atau password yang salah |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Buka halaman login<br>2. Masukkan email yang tidak terdaftar atau password salah<br>3. Klik tombol Login |
| **Hasil yang Diharapkan** | - Sistem menampilkan pesan error "Email atau password salah!"<br>- User tetap di halaman login<br>- Tidak ada redirect |

### 1.3 Login dengan Field Kosong
| **Kasus Uji** | Login tanpa mengisi email atau password |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Buka halaman login<br>2. Biarkan field email atau password kosong<br>3. Klik tombol Login |
| **Hasil yang Diharapkan** | - Browser menampilkan validasi "Field tidak boleh kosong"<br>- Form tidak tersubmit |

### 1.4 Logout
| **Kasus Uji** | Keluar dari sistem |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. User sudah login<br>2. Klik tombol/link Logout |
| **Hasil yang Diharapkan** | - Session dihapus<br>- Redirect ke halaman login<br>- User tidak bisa akses halaman yang memerlukan autentikasi |

---

## 2. MODUL ADMIN

### 2.1 Dashboard Admin
| **Kasus Uji** | Melihat dashboard admin |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin<br>2. Akses halaman dashboard |
| **Hasil yang Diharapkan** | - Menampilkan statistik sistem<br>- Menampilkan total user, kelas, dan data presensi<br>- Dashboard ter-render dengan benar |

### 2.2 Manajemen User - Tambah User Baru
| **Kasus Uji** | Menambah user baru (Admin/Guru/Siswa) |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin<br>2. Akses halaman Users<br>3. Klik tombol Tambah User<br>4. Isi form dengan data valid (nama, email, password, role)<br>5. Submit form |
| **Hasil yang Diharapkan** | - Data user tersimpan ke database<br>- Muncul pesan sukses<br>- User baru muncul di daftar user<br>- Password ter-hash dengan benar |

### 2.3 Manajemen User - Edit User
| **Kasus Uji** | Mengubah data user yang sudah ada |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin<br>2. Akses halaman Users<br>3. Klik tombol Edit pada salah satu user<br>4. Ubah data (nama, email, atau password)<br>5. Submit form |
| **Hasil yang Diharapkan** | - Data user ter-update di database<br>- Muncul pesan sukses<br>- Perubahan data terlihat di daftar user |

### 2.4 Manajemen User - Hapus User
| **Kasus Uji** | Menghapus user dari sistem |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin<br>2. Akses halaman Users<br>3. Klik tombol Hapus pada salah satu user<br>4. Konfirmasi penghapusan |
| **Hasil yang Diharapkan** | - User dihapus dari database<br>- Muncul pesan sukses<br>- User tidak lagi muncul di daftar |

### 2.5 Manajemen Kelas - Tambah Kelas
| **Kasus Uji** | Menambah kelas baru |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin<br>2. Akses halaman Kelas<br>3. Klik tombol Tambah Kelas<br>4. Isi nama kelas dan pilih wali kelas (guru)<br>5. Submit form |
| **Hasil yang Diharapkan** | - Kelas baru tersimpan di database<br>- Muncul pesan sukses<br>- Kelas baru muncul di daftar kelas<br>- Guru terpilih sebagai wali kelas |

### 2.6 Manajemen Kelas - Edit Kelas
| **Kasus Uji** | Mengubah data kelas |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin<br>2. Akses halaman Kelas<br>3. Klik tombol Edit pada salah satu kelas<br>4. Ubah nama kelas atau wali kelas<br>5. Submit form |
| **Hasil yang Diharapkan** | - Data kelas ter-update di database<br>- Muncul pesan sukses<br>- Perubahan terlihat di daftar kelas |

### 2.7 Manajemen Kelas - Hapus Kelas
| **Kasus Uji** | Menghapus kelas |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin<br>2. Akses halaman Kelas<br>3. Klik tombol Hapus pada salah satu kelas<br>4. Konfirmasi penghapusan |
| **Hasil yang Diharapkan** | - Kelas dihapus dari database<br>- Muncul pesan sukses<br>- Kelas tidak lagi muncul di daftar |

### 2.8 Manajemen Siswa dalam Kelas - Tambah Siswa ke Kelas
| **Kasus Uji** | Menambahkan siswa ke kelas tertentu |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin<br>2. Akses halaman Kelas<br>3. Pilih kelas dan lihat detail siswa<br>4. Klik tombol Tambah Siswa<br>5. Pilih siswa dari daftar siswa tersedia<br>6. Submit |
| **Hasil yang Diharapkan** | - Siswa masuk ke dalam kelas<br>- Relasi tersimpan di database<br>- Siswa muncul di daftar siswa kelas tersebut<br>- Total siswa kelas bertambah |

### 2.9 Manajemen Siswa dalam Kelas - Hapus Siswa dari Kelas
| **Kasus Uji** | Mengeluarkan siswa dari kelas |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin<br>2. Akses halaman Kelas<br>3. Pilih kelas dan lihat detail siswa<br>4. Klik tombol Hapus pada salah satu siswa<br>5. Konfirmasi penghapusan |
| **Hasil yang Diharapkan** | - Siswa dikeluarkan dari kelas<br>- Relasi dihapus dari database<br>- Siswa tidak lagi muncul di daftar siswa kelas<br>- Total siswa kelas berkurang |

### 2.10 Manajemen Lokasi - Update Lokasi Sekolah
| **Kasus Uji** | Mengubah koordinat lokasi sekolah |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin<br>2. Akses halaman Lokasi<br>3. Ubah latitude dan longitude<br>4. Ubah radius toleransi (meter)<br>5. Submit form |
| **Hasil yang Diharapkan** | - Koordinat lokasi ter-update di database<br>- Muncul pesan sukses<br>- Lokasi baru akan digunakan untuk validasi presensi siswa |

### 2.11 Sesi Presensi Sekolah - Buat Sesi Manual
| **Kasus Uji** | Membuat sesi presensi sekolah secara manual |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin<br>2. Akses halaman Presensi Sekolah<br>3. Klik tombol Buat Sesi<br>4. Isi waktu buka dan waktu tutup<br>5. Isi note/keterangan (opsional)<br>6. Submit form |
| **Hasil yang Diharapkan** | - Sesi presensi sekolah dibuat<br>- Data tersimpan di database dengan status 'aktif'<br>- Siswa dapat melakukan presensi sekolah<br>- Sesi muncul di daftar sesi |

### 2.12 Sesi Presensi Sekolah - Perpanjang Sesi
| **Kasus Uji** | Memperpanjang waktu tutup sesi presensi sekolah |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin<br>2. Akses halaman Presensi Sekolah<br>3. Pilih sesi aktif<br>4. Klik tombol Perpanjang<br>5. Ubah waktu tutup menjadi lebih lama<br>6. Submit |
| **Hasil yang Diharapkan** | - Waktu tutup sesi ter-update<br>- Sesi tetap aktif sampai waktu tutup baru<br>- Muncul pesan sukses |

### 2.13 Sesi Presensi Sekolah - Tutup Sesi Manual
| **Kasus Uji** | Menutup sesi presensi sekolah sebelum waktu tutup |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin<br>2. Akses halaman Presensi Sekolah<br>3. Pilih sesi aktif<br>4. Klik tombol Tutup Sesi<br>5. Konfirmasi penutupan |
| **Hasil yang Diharapkan** | - Status sesi berubah menjadi 'tertutup'<br>- Siswa yang belum presensi otomatis ditandai alpha<br>- Muncul notifikasi jumlah siswa yang ditandai alpha<br>- Sesi tidak bisa digunakan untuk presensi lagi |

### 2.14 Laporan - View Laporan Presensi
| **Kasus Uji** | Melihat laporan presensi |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin<br>2. Akses halaman Laporan<br>3. Pilih filter (tanggal, bulan, kelas, siswa)<br>4. Klik tombol Tampilkan |
| **Hasil yang Diharapkan** | - Data presensi ditampilkan sesuai filter<br>- Menampilkan statistik kehadiran (hadir, izin, sakit, alpha)<br>- Data akurat dan sesuai dengan database |

### 2.15 Laporan - Export Laporan
| **Kasus Uji** | Export laporan ke format file |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin<br>2. Akses halaman Laporan<br>3. Pilih filter laporan<br>4. Klik tombol Export (CSV/Excel/PDF) |
| **Hasil yang Diharapkan** | - File laporan ter-download<br>- Format file sesuai pilihan<br>- Data di file sesuai dengan yang ditampilkan di layar |

---

## 3. MODUL ADMIN KESISWAAN

### 3.1 Dashboard Admin Kesiswaan
| **Kasus Uji** | Melihat dashboard admin kesiswaan |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin Kesiswaan<br>2. Akses halaman dashboard |
| **Hasil yang Diharapkan** | - Menampilkan statistik total siswa dan guru<br>- Menampilkan daftar sesi presensi sekolah<br>- Dashboard ter-render dengan benar |

### 3.2 Buku Induk - Tambah Data Buku Induk
| **Kasus Uji** | Menambah data buku induk siswa |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin Kesiswaan<br>2. Akses halaman Buku Induk<br>3. Klik tombol Tambah<br>4. Pilih siswa<br>5. Isi data lengkap (NIS, NISN, tempat/tanggal lahir, alamat, data ortu, dll)<br>6. Upload dokumen PDF (opsional)<br>7. Submit form |
| **Hasil yang Diharapkan** | - Data buku induk tersimpan di database<br>- File PDF (jika ada) ter-upload ke server<br>- Muncul pesan sukses<br>- Data muncul di daftar buku induk |

### 3.3 Buku Induk - Edit Data Buku Induk
| **Kasus Uji** | Mengubah data buku induk siswa |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin Kesiswaan<br>2. Akses halaman Buku Induk<br>3. Klik tombol Edit pada salah satu siswa<br>4. Ubah data yang diperlukan<br>5. Upload dokumen baru (opsional)<br>6. Submit form |
| **Hasil yang Diharapkan** | - Data buku induk ter-update di database<br>- File PDF baru menggantikan yang lama (jika diupload)<br>- Muncul pesan sukses<br>- Perubahan terlihat di daftar |

### 3.4 Buku Induk - Upload Dokumen Tambahan
| **Kasus Uji** | Menambah dokumen PDF tambahan ke buku induk |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin Kesiswaan<br>2. Akses halaman Buku Induk<br>3. Pilih siswa<br>4. Upload file PDF tambahan<br>5. Isi keterangan dokumen<br>6. Submit |
| **Hasil yang Diharapkan** | - File PDF ter-upload ke server<br>- Data dokumen tersimpan dengan relasi ke buku induk<br>- Dokumen muncul di daftar dokumen siswa<br>- File bisa didownload |

### 3.5 Buku Induk - Hapus Dokumen
| **Kasus Uji** | Menghapus dokumen dari buku induk |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin Kesiswaan<br>2. Akses halaman Buku Induk<br>3. Pilih siswa dan lihat dokumen<br>4. Klik tombol Hapus pada dokumen<br>5. Konfirmasi penghapusan |
| **Hasil yang Diharapkan** | - File dihapus dari server<br>- Data dokumen dihapus dari database<br>- Muncul pesan sukses<br>- Dokumen tidak lagi muncul di daftar |

### 3.6 Buku Induk - Validasi Upload File PDF
| **Kasus Uji** | Upload file non-PDF atau file terlalu besar |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin Kesiswaan<br>2. Akses halaman Buku Induk<br>3. Coba upload file selain PDF (misal .jpg, .docx)<br>4. Atau upload file PDF > 5MB<br>5. Submit |
| **Hasil yang Diharapkan** | - Sistem menolak upload<br>- Muncul pesan error "Hanya file PDF yang diperbolehkan" atau "Ukuran file terlalu besar"<br>- Data tidak tersimpan |

### 3.7 Sesi Presensi Sekolah (Admin Kesiswaan)
| **Kasus Uji** | Mengelola sesi presensi sekolah |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin Kesiswaan<br>2. Akses halaman Presensi Sekolah<br>3. Lakukan operasi CRUD sesi (buat, perpanjang, tutup) |
| **Hasil yang Diharapkan** | - Fitur sama dengan Admin<br>- Admin Kesiswaan dapat membuat, memperpanjang, dan menutup sesi<br>- Siswa yang belum presensi saat tutup ditandai alpha |

### 3.8 Laporan (Admin Kesiswaan)
| **Kasus Uji** | Melihat dan export laporan presensi |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin Kesiswaan<br>2. Akses halaman Laporan<br>3. Pilih filter dan tampilkan data<br>4. Export laporan |
| **Hasil yang Diharapkan** | - Data presensi ditampilkan sesuai filter<br>- Statistik akurat<br>- File export ter-download dengan benar |

---

## 4. MODUL GURU

### 4.1 Dashboard Guru
| **Kasus Uji** | Melihat dashboard guru |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Guru<br>2. Akses halaman dashboard |
| **Hasil yang Diharapkan** | - Menampilkan kelas yang diajar guru<br>- Menampilkan total siswa di semua kelas<br>- Menampilkan jumlah kelas dengan presensi aktif<br>- Menampilkan aktivitas presensi terbaru |

### 4.2 Manajemen Kelas - Lihat Daftar Kelas
| **Kasus Uji** | Melihat daftar kelas yang diajar |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Guru<br>2. Akses halaman Kelas |
| **Hasil yang Diharapkan** | - Menampilkan semua kelas yang diajar guru tersebut<br>- Menampilkan total siswa per kelas<br>- Menampilkan status sesi presensi (aktif/tidak aktif)<br>- Menampilkan presensi hari ini per kelas |

### 4.3 Manajemen Kelas - Lihat Detail Siswa
| **Kasus Uji** | Melihat daftar siswa dalam kelas |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Guru<br>2. Akses halaman Kelas<br>3. Klik detail pada salah satu kelas |
| **Hasil yang Diharapkan** | - Menampilkan daftar siswa di kelas tersebut<br>- Menampilkan informasi siswa (nama, email)<br>- Menampilkan status kehadiran siswa |

### 4.4 Sesi Presensi Kelas - Buka Sesi
| **Kasus Uji** | Membuka sesi presensi kelas |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Guru<br>2. Akses halaman Kelas<br>3. Pilih kelas<br>4. Klik tombol Buka Presensi<br>5. Isi waktu tutup dan catatan<br>6. Submit |
| **Hasil yang Diharapkan** | - Sesi presensi kelas dibuat dengan status 'aktif'<br>- Waktu buka adalah waktu submit<br>- Siswa di kelas tersebut dapat melakukan presensi<br>- Tombol berubah menjadi "Tutup Presensi" |

### 4.5 Sesi Presensi Kelas - Tutup Sesi
| **Kasus Uji** | Menutup sesi presensi kelas |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Guru<br>2. Akses halaman Kelas<br>3. Pilih kelas dengan sesi aktif<br>4. Klik tombol Tutup Presensi<br>5. Konfirmasi penutupan |
| **Hasil yang Diharapkan** | - Status sesi berubah menjadi 'tertutup'<br>- Siswa yang belum presensi ditandai alpha<br>- Muncul notifikasi jumlah siswa alpha<br>- Tombol kembali menjadi "Buka Presensi" |

### 4.6 Sesi Presensi Kelas - Tutup Otomatis
| **Kasus Uji** | Sesi presensi tertutup otomatis setelah waktu habis |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Guru membuka sesi dengan waktu tutup tertentu<br>2. Tunggu hingga waktu tutup terlewati<br>3. Refresh halaman atau akses data presensi |
| **Hasil yang Diharapkan** | - Sistem otomatis menutup sesi<br>- Siswa yang belum presensi ditandai alpha<br>- Status sesi menjadi 'tertutup' |

### 4.7 Laporan - Lihat Laporan Kelas
| **Kasus Uji** | Melihat laporan presensi per kelas |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Guru<br>2. Akses halaman Laporan<br>3. Pilih kelas<br>4. Pilih sesi presensi<br>5. Klik Tampilkan |
| **Hasil yang Diharapkan** | - Menampilkan data presensi siswa di kelas tersebut<br>- Menampilkan statistik per sesi (hadir, izin, sakit, alpha)<br>- Data akurat per sesi yang dipilih |

### 4.8 Laporan - Ubah Status Presensi
| **Kasus Uji** | Mengubah status presensi siswa secara manual |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Guru<br>2. Akses halaman Laporan<br>3. Pilih kelas dan sesi<br>4. Klik Edit pada status siswa tertentu<br>5. Ubah status (Hadir/Izin/Sakit/Alpha)<br>6. Submit |
| **Hasil yang Diharapkan** | - Status presensi siswa ter-update<br>- Perubahan tersimpan di database<br>- Muncul pesan sukses<br>- Statistik laporan ter-update |

### 4.9 Laporan - Export Laporan Kelas
| **Kasus Uji** | Export laporan presensi kelas |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Guru<br>2. Akses halaman Laporan<br>3. Pilih kelas dan filter<br>4. Klik tombol Export |
| **Hasil yang Diharapkan** | - File laporan ter-download<br>- Format file sesuai (CSV/Excel/PDF)<br>- Data di file lengkap dan akurat |

---

## 5. MODUL SISWA

### 5.1 Dashboard Siswa
| **Kasus Uji** | Melihat dashboard siswa |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Siswa<br>2. Akses halaman dashboard |
| **Hasil yang Diharapkan** | - Menampilkan statistik kehadiran siswa (total hadir, izin, sakit, alpha)<br>- Menampilkan presensi terakhir (5 data)<br>- Menampilkan presensi hari ini<br>- Menampilkan kelas yang diikuti |

### 5.2 Presensi Sekolah - Cek Status Sesi
| **Kasus Uji** | Mengecek apakah ada sesi presensi sekolah aktif |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Siswa<br>2. Akses halaman Presensi<br>3. Tab Presensi Sekolah |
| **Hasil yang Diharapkan** | - Jika ada sesi aktif, menampilkan tombol "Presensi Sekarang"<br>- Jika tidak ada sesi, menampilkan pesan "Tidak ada sesi presensi aktif"<br>- Menampilkan waktu buka dan tutup sesi |

### 5.3 Presensi Sekolah - Submit Presensi Berhasil
| **Kasus Uji** | Melakukan presensi sekolah dengan lokasi valid |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Siswa<br>2. Akses halaman Presensi<br>3. Pastikan ada sesi aktif<br>4. Izinkan akses lokasi browser<br>5. Berada di lokasi sekolah (dalam radius toleransi)<br>6. Klik tombol Presensi Sekarang |
| **Hasil yang Diharapkan** | - Sistem mengambil koordinat GPS siswa<br>- Sistem menghitung jarak dari lokasi sekolah<br>- Jika dalam radius, presensi tersimpan dengan status "Hadir"<br>- Muncul pesan sukses "Presensi berhasil!"<br>- Tombol Presensi tidak bisa diklik lagi untuk sesi yang sama |

### 5.4 Presensi Sekolah - Submit Presensi Gagal (Lokasi Tidak Valid)
| **Kasus Uji** | Presensi sekolah dengan lokasi di luar radius |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Siswa<br>2. Akses halaman Presensi<br>3. Pastikan ada sesi aktif<br>4. Berada di luar radius lokasi sekolah<br>5. Klik tombol Presensi Sekarang |
| **Hasil yang Diharapkan** | - Sistem mengambil koordinat GPS siswa<br>- Sistem menghitung jarak dari lokasi sekolah<br>- Jika di luar radius, presensi ditolak<br>- Muncul pesan error "Anda berada di luar jangkauan lokasi sekolah" |

### 5.5 Presensi Sekolah - Akses Lokasi Ditolak
| **Kasus Uji** | Siswa menolak akses lokasi browser |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Siswa<br>2. Akses halaman Presensi<br>3. Pastikan ada sesi aktif<br>4. Tolak akses lokasi saat browser meminta<br>5. Klik tombol Presensi |
| **Hasil yang Diharapkan** | - Browser menampilkan pesan error lokasi<br>- Sistem menampilkan pesan "Akses lokasi diperlukan untuk presensi"<br>- Presensi tidak bisa dilakukan |

### 5.6 Presensi Sekolah - Presensi Ganda
| **Kasus Uji** | Mencoba presensi sekolah dua kali dalam sesi yang sama |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Siswa<br>2. Lakukan presensi sekolah dengan sukses<br>3. Coba klik tombol Presensi lagi di sesi yang sama |
| **Hasil yang Diharapkan** | - Sistem mendeteksi siswa sudah presensi<br>- Tombol Presensi disabled atau menampilkan pesan "Anda sudah melakukan presensi"<br>- Tidak ada data presensi ganda tersimpan |

### 5.7 Presensi Kelas - Cek Status Sesi Kelas
| **Kasus Uji** | Mengecek sesi presensi kelas aktif |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Siswa<br>2. Akses halaman Presensi<br>3. Tab Presensi Kelas |
| **Hasil yang Diharapkan** | - Menampilkan daftar kelas yang diikuti siswa<br>- Menampilkan status sesi per kelas (aktif/tidak aktif)<br>- Jika ada sesi aktif, tombol "Presensi" muncul |

### 5.8 Presensi Kelas - Submit Presensi Berhasil
| **Kasus Uji** | Melakukan presensi kelas dengan sukses |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Siswa<br>2. Akses halaman Presensi<br>3. Pilih kelas dengan sesi aktif<br>4. Izinkan akses lokasi<br>5. Berada di lokasi sekolah<br>6. Klik tombol Presensi |
| **Hasil yang Diharapkan** | - Koordinat siswa divalidasi<br>- Jika dalam radius, presensi tersimpan dengan status "Hadir"<br>- Muncul pesan sukses<br>- Status presensi kelas ter-update |

### 5.9 Presensi Kelas - Presensi Gagal (Lokasi Tidak Valid)
| **Kasus Uji** | Presensi kelas di luar radius sekolah |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Siswa<br>2. Akses halaman Presensi<br>3. Pilih kelas dengan sesi aktif<br>4. Berada di luar radius lokasi sekolah<br>5. Klik tombol Presensi |
| **Hasil yang Diharapkan** | - Sistem menolak presensi<br>- Muncul pesan error "Lokasi tidak valid"<br>- Data tidak tersimpan |

### 5.10 Presensi Kelas - Presensi Ganda
| **Kasus Uji** | Mencoba presensi kelas dua kali dalam sesi yang sama |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Siswa<br>2. Lakukan presensi kelas dengan sukses<br>3. Coba presensi lagi di sesi yang sama |
| **Hasil yang Diharapkan** | - Sistem mendeteksi sudah presensi<br>- Tombol disabled atau muncul pesan "Sudah presensi"<br>- Tidak ada data ganda |

### 5.11 Riwayat - Lihat Riwayat Harian
| **Kasus Uji** | Melihat riwayat presensi per hari |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Siswa<br>2. Akses halaman Riwayat<br>3. Pilih filter "Harian"<br>4. Pilih tanggal<br>5. Klik Tampilkan |
| **Hasil yang Diharapkan** | - Menampilkan statistik presensi siswa pada tanggal tersebut<br>- Menampilkan data presensi sekolah dan kelas pada tanggal tersebut<br>- Chart/grafik ditampilkan (jika ada) |

### 5.12 Riwayat - Lihat Riwayat Mingguan
| **Kasus Uji** | Melihat riwayat presensi per minggu |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Siswa<br>2. Akses halaman Riwayat<br>3. Pilih filter "Mingguan"<br>4. Pilih minggu dan tahun<br>5. Klik Tampilkan |
| **Hasil yang Diharapkan** | - Menampilkan statistik presensi siswa dalam minggu tersebut<br>- Menampilkan data presensi sekolah dan kelas selama seminggu<br>- Chart menampilkan tren kehadiran per hari |

### 5.13 Riwayat - Lihat Riwayat Bulanan
| **Kasus Uji** | Melihat riwayat presensi per bulan |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Siswa<br>2. Akses halaman Riwayat<br>3. Pilih filter "Bulanan"<br>4. Pilih bulan dan tahun<br>5. Klik Tampilkan |
| **Hasil yang Diharapkan** | - Menampilkan statistik presensi siswa dalam bulan tersebut<br>- Menampilkan semua data presensi di bulan tersebut<br>- Statistik dan chart ter-update sesuai periode |

### 5.14 Buku Induk - Lihat Data Pribadi
| **Kasus Uji** | Melihat data buku induk pribadi |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Siswa<br>2. Akses halaman Buku Induk |
| **Hasil yang Diharapkan** | - Menampilkan data pribadi siswa (NIS, NISN, tempat/tanggal lahir, alamat, data ortu)<br>- Menampilkan dokumen yang sudah diupload<br>- Siswa dapat download dokumen |

### 5.15 Buku Induk - Edit Data Pribadi (Jika Diizinkan)
| **Kasus Uji** | Siswa mengubah data buku induk sendiri |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Siswa<br>2. Akses halaman Buku Induk<br>3. Klik tombol Edit<br>4. Ubah data yang diizinkan<br>5. Submit |
| **Hasil yang Diharapkan** | - Data ter-update di database<br>- Muncul pesan sukses<br>- Perubahan terlihat di halaman buku induk |

### 5.16 Buku Induk - Upload Dokumen Tambahan (Jika Diizinkan)
| **Kasus Uji** | Siswa upload dokumen ke buku induk sendiri |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Siswa<br>2. Akses halaman Buku Induk<br>3. Upload file PDF<br>4. Isi keterangan<br>5. Submit |
| **Hasil yang Diharapkan** | - File ter-upload ke server<br>- Data dokumen tersimpan<br>- Dokumen muncul di daftar<br>- Muncul pesan sukses |

---

## 6. MODUL LAPORAN UMUM

### 6.1 Filter Laporan Berdasarkan Tanggal
| **Kasus Uji** | Filter laporan dengan rentang tanggal |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin/Guru<br>2. Akses halaman Laporan<br>3. Pilih tanggal mulai dan tanggal akhir<br>4. Klik Tampilkan |
| **Hasil yang Diharapkan** | - Data presensi ditampilkan sesuai rentang tanggal<br>- Tidak ada data di luar rentang<br>- Statistik sesuai dengan periode |

### 6.2 Filter Laporan Berdasarkan Kelas
| **Kasus Uji** | Filter laporan per kelas |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin/Guru<br>2. Akses halaman Laporan<br>3. Pilih kelas tertentu<br>4. Klik Tampilkan |
| **Hasil yang Diharapkan** | - Hanya menampilkan data presensi siswa di kelas tersebut<br>- Statistik sesuai dengan kelas yang dipilih<br>- Tidak ada data dari kelas lain |

### 6.3 Filter Laporan Berdasarkan Siswa
| **Kasus Uji** | Filter laporan per siswa |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin/Guru<br>2. Akses halaman Laporan<br>3. Pilih siswa tertentu<br>4. Klik Tampilkan |
| **Hasil yang Diharapkan** | - Menampilkan semua data presensi siswa tersebut<br>- Statistik kehadiran siswa (hadir, izin, sakit, alpha)<br>- Riwayat lengkap presensi siswa |

### 6.4 Laporan Rekap Bulanan
| **Kasus Uji** | Melihat rekap presensi per bulan |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin/Guru<br>2. Akses halaman Laporan<br>3. Pilih bulan dan tahun<br>4. Klik Tampilkan Rekap |
| **Hasil yang Diharapkan** | - Menampilkan rekap presensi seluruh siswa dalam bulan tersebut<br>- Statistik total (hadir, izin, sakit, alpha) per siswa<br>- Persentase kehadiran per siswa |

---

## 7. MODUL VALIDASI DAN ERROR HANDLING

### 7.1 Validasi Input - Email Tidak Valid
| **Kasus Uji** | Input email dengan format salah |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Akses form yang memerlukan email (Login/Tambah User)<br>2. Masukkan email dengan format salah (misal: "user@")<br>3. Submit form |
| **Hasil yang Diharapkan** | - Browser atau sistem menampilkan pesan "Format email tidak valid"<br>- Form tidak tersubmit<br>- Data tidak tersimpan |

### 7.2 Validasi Input - Password Kosong
| **Kasus Uji** | Submit form dengan password kosong |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Akses form login atau tambah user<br>2. Biarkan field password kosong<br>3. Submit form |
| **Hasil yang Diharapkan** | - Muncul validasi "Password tidak boleh kosong"<br>- Form tidak tersubmit |

### 7.3 Validasi Upload - File Terlalu Besar
| **Kasus Uji** | Upload file melebihi batas ukuran |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Akses form upload dokumen<br>2. Pilih file PDF > 5MB<br>3. Submit form |
| **Hasil yang Diharapkan** | - Sistem menolak upload<br>- Muncul pesan "Ukuran file terlalu besar, maksimal 5MB"<br>- File tidak tersimpan |

### 7.4 Validasi Upload - Format File Salah
| **Kasus Uji** | Upload file dengan format tidak diperbolehkan |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Akses form upload dokumen PDF<br>2. Pilih file .jpg atau .docx<br>3. Submit form |
| **Hasil yang Diharapkan** | - Sistem menolak upload<br>- Muncul pesan "Hanya file PDF yang diperbolehkan"<br>- File tidak tersimpan |

### 7.5 Error Handling - Database Connection Error
| **Kasus Uji** | Koneksi database gagal |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Matikan database server<br>2. Coba akses aplikasi atau submit form |
| **Hasil yang Diharapkan** | - Muncul pesan error yang user-friendly<br>- Tidak menampilkan detail teknis error<br>- Aplikasi tidak crash |

### 7.6 Error Handling - Session Timeout
| **Kasus Uji** | Session user habis/expired |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login ke sistem<br>2. Biarkan idle hingga session timeout<br>3. Coba akses halaman atau submit form |
| **Hasil yang Diharapkan** | - User di-redirect ke halaman login<br>- Muncul pesan "Session expired, silakan login kembali"<br>- Data form tidak hilang (jika ada) |

---

## 8. MODUL KEAMANAN

### 8.1 Authorization - Akses Halaman Admin oleh Non-Admin
| **Kasus Uji** | User non-admin mencoba akses halaman admin |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Guru/Siswa<br>2. Coba akses URL halaman admin (misal: ?action=admin_users)<br>3. Submit request |
| **Hasil yang Diharapkan** | - Akses ditolak<br>- User di-redirect atau muncul error 403 Forbidden<br>- Halaman admin tidak tampil |

### 8.2 Authorization - Akses Data Siswa Lain
| **Kasus Uji** | Siswa mencoba akses data siswa lain |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Siswa A<br>2. Coba akses riwayat atau buku induk Siswa B dengan mengubah parameter URL<br>3. Submit request |
| **Hasil yang Diharapkan** | - Akses ditolak<br>- Hanya menampilkan data siswa yang login<br>- Tidak ada kebocoran data |

### 8.3 SQL Injection - Input Berbahaya
| **Kasus Uji** | Input SQL injection pada form |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Akses form login atau search<br>2. Masukkan string SQL injection (misal: `' OR '1'='1`)<br>3. Submit form |
| **Hasil yang Diharapkan** | - Query di-escape dengan benar<br>- Tidak ada eksekusi SQL berbahaya<br>- Sistem tetap aman dan tidak ada data bocor |

### 8.4 XSS - Input Script Berbahaya
| **Kasus Uji** | Input JavaScript pada form |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Akses form input (misal: tambah user, komentar)<br>2. Masukkan script `<script>alert('XSS')</script>`<br>3. Submit dan lihat output |
| **Hasil yang Diharapkan** | - Script tidak dieksekusi<br>- Output di-escape/sanitize<br>- Tidak ada pop-up alert muncul<br>- Sistem aman dari XSS |

### 8.5 Password Hashing
| **Kasus Uji** | Password tersimpan dengan hash |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Tambah user baru dengan password<br>2. Cek database langsung |
| **Hasil yang Diharapkan** | - Password tersimpan dalam bentuk hash (bcrypt/password_hash)<br>- Password plain text tidak terlihat di database<br>- Hash berbeda untuk password yang sama |

---

## 9. MODUL NOTIFIKASI DAN FEEDBACK

### 9.1 Notifikasi Sukses - Tambah Data
| **Kasus Uji** | Notifikasi setelah berhasil menambah data |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin<br>2. Tambah user/kelas/data baru<br>3. Submit form dengan data valid |
| **Hasil yang Diharapkan** | - Muncul notifikasi sukses (toast/alert)<br>- Pesan jelas: "Data berhasil ditambahkan"<br>- Notifikasi menghilang setelah beberapa detik atau dapat ditutup |

### 9.2 Notifikasi Error - Gagal Menyimpan
| **Kasus Uji** | Notifikasi saat gagal menyimpan data |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Submit form dengan data yang menyebabkan error (misal: duplicate email)<br>2. Lihat response |
| **Hasil yang Diharapkan** | - Muncul notifikasi error<br>- Pesan jelas dan informatif<br>- User diberikan petunjuk cara memperbaiki |

### 9.3 Konfirmasi - Hapus Data
| **Kasus Uji** | Konfirmasi sebelum menghapus data |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Klik tombol Hapus pada data user/kelas<br>2. Lihat response |
| **Hasil yang Diharapkan** | - Muncul dialog konfirmasi "Apakah Anda yakin ingin menghapus?"<br>- Ada tombol Batal dan OK<br>- Data baru terhapus setelah konfirmasi OK |

---

## 10. MODUL RESPONSIF DAN UI/UX

### 10.1 Responsive - Tampilan Mobile
| **Kasus Uji** | Aplikasi diakses dari perangkat mobile |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Akses aplikasi dari smartphone<br>2. Login dan navigasi ke berbagai halaman |
| **Hasil yang Diharapkan** | - Layout menyesuaikan ukuran layar<br>- Semua elemen dapat di-scroll dan diklik<br>- Tidak ada elemen terpotong atau overflow<br>- Navigasi tetap mudah digunakan |

### 10.2 Responsive - Tampilan Tablet
| **Kasus Uji** | Aplikasi diakses dari tablet |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Akses aplikasi dari tablet<br>2. Login dan navigasi ke berbagai halaman |
| **Hasil yang Diharapkan** | - Layout optimal untuk layar tablet<br>- Semua fitur berfungsi normal<br>- Tampilan tidak terlalu renggang atau sempit |

### 10.3 Loading State - Fetch Data
| **Kasus Uji** | Tampilan loading saat mengambil data |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Akses halaman dengan data banyak (laporan, user list)<br>2. Perhatikan proses loading |
| **Hasil yang Diharapkan** | - Muncul indikator loading (spinner/skeleton)<br>- User tahu bahwa data sedang diproses<br>- Setelah selesai, data ditampilkan dengan benar |

---

## 11. EDGE CASES DAN SKENARIO KHUSUS

### 11.1 Presensi - Midnight Edge Case
| **Kasus Uji** | Presensi dilakukan tepat pada pergantian hari (00:00) |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Buka sesi presensi menjelang tengah malam<br>2. Submit presensi tepat saat jam berubah ke 00:00 |
| **Hasil yang Diharapkan** | - Presensi tersimpan dengan tanggal yang benar<br>- Tidak ada error timezone<br>- Data masuk ke statistik yang tepat |

### 11.2 Sesi Presensi - Overlap Session
| **Kasus Uji** | Membuat sesi presensi yang waktunya overlap |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login sebagai Admin/Guru<br>2. Buat sesi presensi 08:00 - 10:00<br>3. Coba buat sesi lagi 09:00 - 11:00 untuk kelas yang sama |
| **Hasil yang Diharapkan** | - Sistem mendeteksi overlap (jika ada validasi)<br>- Atau sistem mengizinkan multiple session (sesuai business logic)<br>- Tidak ada conflict dalam data |

### 11.3 Koneksi Internet - Offline Mode
| **Kasus Uji** | User kehilangan koneksi internet saat submit |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Isi form presensi/data<br>2. Matikan koneksi internet<br>3. Submit form |
| **Hasil yang Diharapkan** | - Muncul notifikasi error koneksi<br>- Form data tidak hilang<br>- User bisa retry setelah koneksi kembali |

### 11.4 Browser Compatibility
| **Kasus Uji** | Aplikasi diakses dari browser berbeda |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Akses aplikasi dari Chrome, Firefox, Safari, Edge<br>2. Test semua fitur utama |
| **Hasil yang Diharapkan** | - Semua fitur berfungsi normal di semua browser modern<br>- Tampilan konsisten<br>- Tidak ada error JavaScript |

### 11.5 Concurrent Users - Multiple Login
| **Kasus Uji** | User login dari dua device berbeda |
|---------------|-------------------------------------------|
| **Skenario Uji** | 1. Login dari komputer<br>2. Login lagi dari smartphone dengan akun yang sama |
| **Hasil yang Diharapkan** | - Sistem mengizinkan multiple login (atau membatasi sesuai policy)<br>- Session tidak conflict<br>- Data presensi tetap akurat |

---

## RINGKASAN COVERAGE TESTING

| **Modul** | **Total Kasus Uji** |
|-----------|---------------------|
| Autentikasi | 4 |
| Admin | 15 |
| Admin Kesiswaan | 8 |
| Guru | 9 |
| Siswa | 16 |
| Laporan Umum | 4 |
| Validasi & Error Handling | 6 |
| Keamanan | 5 |
| Notifikasi & Feedback | 3 |
| Responsif & UI/UX | 3 |
| Edge Cases | 5 |
| **TOTAL** | **78 Kasus Uji** |

---

## CATATAN PENTING

1. **Prioritas Testing**: Lakukan testing dengan prioritas tinggi pada fitur-fitur kritis seperti:
   - Autentikasi dan authorization
   - Presensi (validasi lokasi GPS)
   - Manajemen sesi presensi
   - Laporan dan statistik

2. **Testing Environment**: 
   - Gunakan data dummy untuk testing
   - Siapkan user testing untuk setiap role (Admin, Admin Kesiswaan, Guru, Siswa)
   - Test di berbagai device (desktop, tablet, mobile)

3. **Bug Tracking**:
   - Catat setiap bug yang ditemukan
   - Berikan severity level (Critical, High, Medium, Low)
   - Track status perbaikan

4. **Regression Testing**:
   - Setelah bug fix, lakukan re-test
   - Test juga fitur-fitur terkait yang mungkin terpengaruh

5. **Performance Testing**:
   - Test dengan data dalam jumlah besar
   - Monitor response time
   - Check memory usage

---

**Dokumen ini dapat di-update sesuai dengan perkembangan fitur aplikasi.**

*Tanggal Pembuatan: 6 Januari 2026*
