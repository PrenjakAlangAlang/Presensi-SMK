# DFD Sistem Presensi SMK

Dokumen ini berisi DFD Level 0, Level 1, dan Level 2 dalam format Mermaid. Diagram disusun berdasarkan modul yang ada di repository: login, user, buku induk, kelas, jadwal mata pelajaran, lokasi sekolah, presensi sekolah, presensi mata pelajaran, laporan, dan notifikasi alpha.

## File Gambar

- [Gambar DFD Level 0](dfd-level-0.svg)
- [Gambar DFD Level 1](dfd-level-1.svg)
- [Gambar DFD Level 2 Presensi Mapel](dfd-level-2-presensi-mapel.svg)

![DFD Level 0](dfd-level-0.svg)

![DFD Level 1](dfd-level-1.svg)

![DFD Level 2 Presensi Mapel](dfd-level-2-presensi-mapel.svg)

## DFD Level 0

```mermaid
flowchart LR
    Admin[Admin]
    AK[Admin Kesiswaan]
    Guru[Guru]
    Siswa[Siswa]
    Ortu[Orang Tua atau Wali]
    Email[Email Service]
    WA[WhatsApp atau Fonnte]

    S((Sistem Presensi SMK))

    Admin -->|Data user, kelas, jadwal, lokasi, sesi, laporan| S
    AK -->|Data buku induk, sesi sekolah, laporan| S
    Guru -->|Data sesi mapel, laporan kemajuan, status presensi| S
    Siswa -->|Data login, biodata, GPS, presensi, alasan, bukti| S

    S -->|Dashboard, status sesi, riwayat, laporan, hasil ekspor| Admin
    S -->|Dashboard, data siswa, laporan presensi| AK
    S -->|Jadwal mengajar, data siswa, rekap presensi mapel| Guru
    S -->|Jadwal, status presensi, riwayat, data buku induk| Siswa

    S -->|Permintaan kirim notifikasi alpha| Email
    S -->|Permintaan kirim notifikasi alpha| WA
    Email -->|Email ketidakhadiran| Ortu
    WA -->|Pesan ketidakhadiran| Ortu
```

## DFD Level 1

```mermaid
flowchart LR
    Admin[Admin]
    AK[Admin Kesiswaan]
    Guru[Guru]
    Siswa[Siswa]
    Ortu[Orang Tua atau Wali]
    Email[Email Service]
    WA[WhatsApp atau Fonnte]

    P1((1. Login))
    P2((2. Kelola User))
    P3((3. Kelola Buku Induk Siswa))
    P4((4. Kelola Kelas))
    P5((5. Kelola Jadwal Mata Pelajaran))
    P6((6. Kelola Lokasi Sekolah))
    P7((7. Kelola Sesi Presensi Sekolah))
    P8((8. Melakukan Presensi Sekolah))
    P9((9. Kelola Sesi Presensi Mapel))
    P10((10. Melakukan Presensi Mapel))
    P11((11. Melihat dan Ekspor Laporan))
    P12((12. Kirim Notifikasi Alpha))

    D1[(D1 Users)]
    D2[(D2 Buku Induk)]
    D3[(D3 Kelas)]
    D4[(D4 Jadwal Mata Pelajaran)]
    D5[(D5 Jadwal Mapel Siswa)]
    D6[(D6 Lokasi Sekolah)]
    D7[(D7 Presensi Sekolah Sesi)]
    D8[(D8 Presensi Sekolah)]
    D9[(D9 Presensi Mapel Sesi)]
    D10[(D10 Presensi Mapel)]
    D11[(D11 File Upload Bukti)]

    Admin -->|Username dan password| P1
    AK -->|Username dan password| P1
    Guru -->|Username dan password| P1
    Siswa -->|NIS atau NISN dan password| P1
    P1 -->|Validasi akun pegawai| D1
    P1 -->|Validasi akun siswa| D2
    P1 -->|Hak akses| Admin
    P1 -->|Hak akses| AK
    P1 -->|Hak akses| Guru
    P1 -->|Hak akses| Siswa

    Admin -->|Data admin, admin kesiswaan, guru| P2
    P2 -->|Simpan, ubah, hapus user| D1
    D1 -->|Informasi user| P2
    P2 -->|Daftar user| Admin

    Admin -->|Data siswa dan dokumen| P3
    AK -->|Data siswa dan dokumen| P3
    Siswa -->|Biodata dan dokumen pribadi| P3
    P3 -->|Simpan biodata dan kontak orang tua| D2
    P3 -->|Simpan dokumen buku induk| D11
    D2 -->|Informasi buku induk| P3
    D11 -->|File dokumen| P3
    P3 -->|Data buku induk| Admin
    P3 -->|Data buku induk| AK
    P3 -->|Data buku induk pribadi| Siswa

    Admin -->|Nama kelas, tahun ajaran, semester| P4
    P4 -->|Simpan, ubah, hapus kelas| D3
    D3 -->|Informasi kelas| P4
    P4 -->|Daftar kelas| Admin

    Admin -->|Data mapel, guru, hari, jam, ruang| P5
    Admin -->|Data peserta mapel| P5
    P5 -->|Ambil data guru| D1
    P5 -->|Ambil data kelas| D3
    P5 -->|Simpan jadwal mapel| D4
    P5 -->|Simpan relasi siswa-mapel| D5
    D4 -->|Informasi jadwal| P5
    D5 -->|Daftar peserta mapel| P5
    P5 -->|Jadwal dan peserta mapel| Admin
    P5 -->|Jadwal mengajar| Guru
    P5 -->|Jadwal belajar| Siswa

    Admin -->|Koordinat dan radius sekolah| P6
    P6 -->|Simpan lokasi sekolah| D6
    D6 -->|Pusat lokasi dan radius| P6
    P6 -->|Informasi lokasi sekolah| Admin

    Admin -->|Waktu buka, tutup, pengulangan| P7
    AK -->|Waktu buka, tutup, pengulangan| P7
    P7 -->|Buat, perpanjang, tutup, hapus sesi| D7
    P7 -->|Tandai siswa belum presensi sebagai alpha| D8
    D7 -->|Status sesi sekolah| P7
    P7 -->|Informasi sesi sekolah| Admin
    P7 -->|Informasi sesi sekolah| AK
    P7 -->|Status sesi aktif| Siswa
    P7 -->|Data alpha sekolah| P12

    Siswa -->|GPS, jenis presensi, alasan, bukti| P8
    P8 -->|Cek sesi aktif| D7
    P8 -->|Cek lokasi dan radius| D6
    P8 -->|Simpan bukti izin atau sakit| D11
    P8 -->|Simpan presensi sekolah| D8
    D8 -->|Riwayat presensi sekolah| P8
    P8 -->|Status presensi sekolah| Siswa

    Guru -->|Pilih jadwal, tanggal, pengulangan| P9
    Guru -->|Laporan kemajuan| P9
    P9 -->|Cek jadwal guru| D4
    P9 -->|Buat, tutup, hapus sesi mapel| D9
    P9 -->|Simpan laporan kemajuan mapel| D9
    P9 -->|Tandai siswa belum presensi sebagai alpha| D10
    D9 -->|Status sesi mapel| P9
    P9 -->|Informasi sesi presensi mapel| Guru
    P9 -->|Status sesi mapel aktif| Siswa
    P9 -->|Data alpha mapel| P12

    Siswa -->|GPS, jenis presensi, alasan, bukti| P10
    P10 -->|Cek peserta jadwal| D5
    P10 -->|Cek sesi mapel aktif| D9
    P10 -->|Cek lokasi dan radius| D6
    P10 -->|Simpan bukti izin atau sakit| D11
    P10 -->|Simpan presensi mapel| D10
    D10 -->|Riwayat presensi mapel| P10
    P10 -->|Status presensi mapel| Siswa

    Admin -->|Filter laporan sekolah dan mapel| P11
    AK -->|Filter laporan sekolah dan mapel| P11
    Guru -->|Filter laporan mapel miliknya| P11
    Siswa -->|Filter riwayat pribadi| P11
    P11 -->|Ambil data siswa| D2
    P11 -->|Ambil kelas| D3
    P11 -->|Ambil jadwal mapel| D4
    P11 -->|Ambil presensi sekolah| D8
    P11 -->|Ambil sesi mapel dan laporan kemajuan| D9
    P11 -->|Ambil presensi mapel| D10
    P11 -->|Laporan dan ekspor Excel atau PDF| Admin
    P11 -->|Laporan dan ekspor Excel atau PDF| AK
    P11 -->|Laporan mapel dan ekspor| Guru
    P11 -->|Riwayat presensi| Siswa

    P12 -->|Ambil kontak orang tua| D2
    P12 -->|Notifikasi alpha| Email
    P12 -->|Notifikasi alpha| WA
    Email -->|Email alpha| Ortu
    WA -->|Pesan WhatsApp alpha| Ortu
```

## DFD Level 2 - Proses Presensi Mata Pelajaran

Diagram ini memecah proses Level 1 nomor 9 dan 10, yaitu pengelolaan sesi presensi mapel oleh guru dan pengisian presensi mapel oleh siswa.

```mermaid
flowchart LR
    Guru[Guru]
    Siswa[Siswa]
    Ortu[Orang Tua atau Wali]
    Email[Email Service]
    WA[WhatsApp atau Fonnte]

    P91((9.1 Pilih Jadwal Mengajar))
    P92((9.2 Buat Sesi Mapel))
    P93((9.3 Tutup atau Hapus Sesi))
    P94((9.4 Catat Alpha Otomatis))
    P95((9.5 Simpan Laporan Kemajuan))

    P101((10.1 Tampilkan Jadwal Hari Ini))
    P102((10.2 Ambil Data GPS))
    P103((10.3 Validasi Peserta dan Input))
    P104((10.4 Validasi Sesi Aktif))
    P105((10.5 Validasi Lokasi dan Fake GPS))
    P106((10.6 Simpan Bukti Izin atau Sakit))
    P107((10.7 Simpan Presensi Mapel))
    P108((10.8 Tampilkan Status dan Riwayat))

    P121((12.1 Ambil Kontak Orang Tua))
    P122((12.2 Kirim Notifikasi Alpha))

    D2[(D2 Buku Induk)]
    D4[(D4 Jadwal Mata Pelajaran)]
    D5[(D5 Jadwal Mapel Siswa)]
    D6[(D6 Lokasi Sekolah)]
    D9[(D9 Presensi Mapel Sesi)]
    D10[(D10 Presensi Mapel)]
    D11[(D11 File Upload Bukti)]

    Guru -->|Pilih mapel atau kelas| P91
    P91 -->|Cek jadwal milik guru| D4
    D4 -->|Data jadwal, hari, jam, ruang| P91
    P91 -->|Jadwal valid| P92
    Guru -->|Tanggal, opsi pengulangan| P92
    P92 -->|Buat sesi open sesuai jam jadwal| D9
    D9 -->|Status sesi| P92
    P92 -->|Informasi sesi berhasil dibuat| Guru

    Guru -->|Pilih sesi| P93
    P93 -->|Cek sesi milik guru| D9
    P93 -->|Jika ditutup, ambil daftar peserta| D5
    D5 -->|Daftar siswa peserta mapel| P94
    D10 -->|Data siswa yang sudah presensi| P94
    P94 -->|Simpan presensi alpha| D10
    P94 -->|Data siswa alpha| P121
    P93 -->|Ubah status closed atau hapus sesi| D9
    P93 -->|Status penutupan atau penghapusan| Guru

    Guru -->|Catatan laporan kemajuan| P95
    P95 -->|Simpan laporan kemajuan pada sesi| D9
    P95 -->|Status penyimpanan laporan| Guru

    Siswa -->|Membuka halaman presensi mapel| P101
    P101 -->|Ambil jadwal siswa hari ini| D4
    P101 -->|Ambil relasi siswa-mapel| D5
    P101 -->|Ambil sesi mapel hari ini| D9
    P101 -->|Ambil presensi yang sudah ada| D10
    P101 -->|Jadwal, status sesi, riwayat singkat| Siswa

    Siswa -->|Koordinat, akurasi, sampel GPS| P102
    P102 -->|Data GPS| P103
    Siswa -->|Jenis hadir, izin, sakit, alasan, bukti| P103
    P103 -->|Cek siswa terdaftar pada mapel| D5
    P103 -->|Input valid| P104

    P104 -->|Cek sesi open dan waktu aktif| D9
    P104 -->|Sesi valid| P105
    P105 -->|Ambil radius dan titik sekolah| D6
    D6 -->|Koordinat sekolah dan radius| P105
    P105 -->|Hasil validasi lokasi| P107

    P103 -->|Bukti izin atau sakit| P106
    P106 -->|Simpan file bukti| D11
    D11 -->|Path file bukti| P107

    P107 -->|Cek duplikasi presensi sesi| D10
    P107 -->|Simpan presensi hadir, izin, atau sakit| D10
    D10 -->|Data presensi tersimpan| P108
    P108 -->|Pesan berhasil atau gagal dan riwayat terbaru| Siswa

    P121 -->|Ambil email dan nomor orang tua| D2
    P121 -->|Kontak orang tua| P122
    P122 -->|Kirim email alpha| Email
    P122 -->|Kirim WhatsApp alpha| WA
    Email -->|Email ketidakhadiran mapel| Ortu
    WA -->|Pesan ketidakhadiran mapel| Ortu
```

## Keterangan Data Store

- D1 Users: akun admin, admin kesiswaan, dan guru.
- D2 Buku Induk: identitas siswa, akun siswa, kontak orang tua, dan data dokumen.
- D3 Kelas: data kelas, tahun ajaran, dan semester.
- D4 Jadwal Mata Pelajaran: jadwal mapel, guru pengampu, hari, jam, ruang.
- D5 Jadwal Mapel Siswa: relasi siswa dengan jadwal mata pelajaran.
- D6 Lokasi Sekolah: titik koordinat sekolah dan radius presensi.
- D7 Presensi Sekolah Sesi: sesi presensi sekolah.
- D8 Presensi Sekolah: catatan presensi sekolah siswa.
- D9 Presensi Mapel Sesi: sesi presensi mata pelajaran dan laporan kemajuan.
- D10 Presensi Mapel: catatan presensi mata pelajaran siswa.
- D11 File Upload Bukti: file bukti izin, sakit, dan dokumen buku induk di folder upload publik.
