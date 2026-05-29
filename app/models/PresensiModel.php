<?php

require_once 'Database.php';
require_once __DIR__ . '/BukuIndukModel.php';
require_once __DIR__ . '/../services/EmailService.php';
require_once __DIR__ . '/../services/WhatsAppService.php';

class PresensiModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function recordPresensiSekolah($data) {
        // Support optional reference to presensi_sekolah_sesi (nullable)
        $this->db->query('INSERT INTO presensi_sekolah (presensi_sekolah_sesi_id, user_id, latitude, longitude, jarak, status, jenis, alasan, foto_bukti) 
                         VALUES (:sesi_id, :user_id, :latitude, :longitude, :jarak, :status, :jenis, :alasan, :foto_bukti)');
        $this->db->bind(':sesi_id', $data['presensi_sekolah_sesi_id'] ?? null);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':latitude', $data['latitude']);
        $this->db->bind(':longitude', $data['longitude']);
        $this->db->bind(':jarak', $data['jarak']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':jenis', $data['jenis']);
        $this->db->bind(':alasan', $data['alasan'] ?? null);
        $this->db->bind(':foto_bukti', $data['foto_bukti'] ?? null);

        return $this->db->execute();
    }
    
    public function recordPresensiKelas($data) {
        $this->db->query('INSERT INTO presensi_mapel
                         (presensi_sesi_id, user_id, jadwal_mata_pelajaran_id, latitude, longitude, jarak, status, jenis, alasan, foto_bukti)
                         VALUES
                         (:sesi_id, :user_id, :jadwal_id, :latitude, :longitude, :jarak, :status, :jenis, :alasan, :foto_bukti)');
        $this->db->bind(':sesi_id', $data['presensi_sesi_id'] ?? null);
        $this->db->bind(':user_id', (int) $data['user_id']);
        $this->db->bind(':jadwal_id', (int) ($data['jadwal_mata_pelajaran_id'] ?? $data['mata_pelajaran_id']));
        $this->db->bind(':latitude', $data['latitude'] ?? 0);
        $this->db->bind(':longitude', $data['longitude'] ?? 0);
        $this->db->bind(':jarak', $data['jarak'] ?? 0);
        $this->db->bind(':status', $data['status'] ?? 'valid');
        $this->db->bind(':jenis', $data['jenis'] ?? 'hadir');
        $this->db->bind(':alasan', $data['alasan'] ?? null);
        $this->db->bind(':foto_bukti', $data['foto_bukti'] ?? null);
        return $this->db->execute();
    }

    /**
     * Check whether a user already has a presensi_mapel record for a given presensi_sesi
     * Returns true if exists, false otherwise
     */
    public function hasPresensiInSession($user_id, $presensi_sesi_id) {
        $this->db->query('SELECT id FROM presensi_mapel
                         WHERE user_id = :user_id AND presensi_sesi_id = :sesi_id
                         LIMIT 1');
        $this->db->bind(':user_id', (int) $user_id);
        $this->db->bind(':sesi_id', (int) $presensi_sesi_id);
        return (bool) $this->db->single();
    }
    
    public function getPresensiSekolahByUser($user_id, $limit = null) {
        $sql = 'SELECT * FROM presensi_sekolah WHERE user_id = :user_id ORDER BY waktu DESC';
        if ($limit) {
            $sql .= ' LIMIT ' . $limit;
        }
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }
    
    public function getPresensiSekolahByUserPeriode($user_id, $startDate, $endDate = null) {
        if ($endDate) {
            $sql = 'SELECT * FROM presensi_sekolah WHERE user_id = :user_id AND DATE(waktu) BETWEEN :start_date AND :end_date ORDER BY waktu DESC';
            $this->db->query($sql);
            $this->db->bind(':user_id', $user_id);
            $this->db->bind(':start_date', $startDate);
            $this->db->bind(':end_date', $endDate);
        } else {
            $sql = 'SELECT * FROM presensi_sekolah WHERE user_id = :user_id AND DATE(waktu) = :tanggal ORDER BY waktu DESC';
            $this->db->query($sql);
            $this->db->bind(':user_id', $user_id);
            $this->db->bind(':tanggal', $startDate);
        }
        return $this->db->resultSet();
    }
    
    public function getPresensiKelasByUser($user_id, $limit = null, $filters = []) {
        $filters = is_array($filters) ? $filters : ['kelas_jadwal_id' => $filters];
        $sql = 'SELECT pm.*, j.nama_mata_pelajaran,
                CONCAT(k.nama_kelas, IF(k.jurusan IS NULL OR k.jurusan = "", "", CONCAT(" ", k.jurusan))) as nama_kelas,
                k.nama_kelas as tingkat,
                k.jurusan,
                j.kelas_jadwal_id, pkel.tahun_ajaran, pkel.semester, j.hari, j.jam_mulai, j.jam_selesai
                FROM presensi_mapel pm
                INNER JOIN jadwal_mata_pelajaran j ON pm.jadwal_mata_pelajaran_id = j.id
                INNER JOIN periode_kelas pkel ON j.kelas_jadwal_id = pkel.id
                INNER JOIN kelas k ON pkel.kelas_id = k.id
                WHERE pm.user_id = :user_id';
        $sql .= $this->buildMapelFilterSql($filters);
        $sql .= ' ORDER BY pm.waktu DESC';
        if ($limit) $sql .= ' LIMIT ' . (int) $limit;
        $this->db->query($sql);
        $this->db->bind(':user_id', (int) $user_id);
        $this->bindMapelFilters($filters);
        return $this->db->resultSet();
    }
    
    public function getPresensiKelasByUserPeriode($user_id, $startDate, $endDate = null, $filters = []) {
        $filters = is_array($filters) ? $filters : ['kelas_jadwal_id' => $filters];
        if ($endDate) {
            $sql = 'SELECT pm.*, j.nama_mata_pelajaran,
                    CONCAT(k.nama_kelas, IF(k.jurusan IS NULL OR k.jurusan = "", "", CONCAT(" ", k.jurusan))) as nama_kelas,
                    k.nama_kelas as tingkat,
                    k.jurusan,
                    j.kelas_jadwal_id, pkel.tahun_ajaran, pkel.semester
                    FROM presensi_mapel pm
                    INNER JOIN jadwal_mata_pelajaran j ON pm.jadwal_mata_pelajaran_id = j.id
                    INNER JOIN periode_kelas pkel ON j.kelas_jadwal_id = pkel.id
                INNER JOIN kelas k ON pkel.kelas_id = k.id
                    WHERE pm.user_id = :user_id AND DATE(pm.waktu) BETWEEN :start_date AND :end_date';
            $sql .= $this->buildMapelFilterSql($filters);
            $sql .= ' ORDER BY pm.waktu DESC';
            $this->db->query($sql);
            $this->db->bind(':start_date', $startDate);
            $this->db->bind(':end_date', $endDate);
        } else {
            $sql = 'SELECT pm.*, j.nama_mata_pelajaran,
                    CONCAT(k.nama_kelas, IF(k.jurusan IS NULL OR k.jurusan = "", "", CONCAT(" ", k.jurusan))) as nama_kelas,
                    k.nama_kelas as tingkat,
                    k.jurusan,
                    j.kelas_jadwal_id, pkel.tahun_ajaran, pkel.semester
                    FROM presensi_mapel pm
                    INNER JOIN jadwal_mata_pelajaran j ON pm.jadwal_mata_pelajaran_id = j.id
                    INNER JOIN periode_kelas pkel ON j.kelas_jadwal_id = pkel.id
                INNER JOIN kelas k ON pkel.kelas_id = k.id
                    WHERE pm.user_id = :user_id AND DATE(pm.waktu) = :tanggal';
            $sql .= $this->buildMapelFilterSql($filters);
            $sql .= ' ORDER BY pm.waktu DESC';
            $this->db->query($sql);
            $this->db->bind(':tanggal', $startDate);
        }
        $this->db->bind(':user_id', (int) $user_id);
        $this->bindMapelFilters($filters);
        return $this->db->resultSet();
    }
    
    public function getPresensiHariIni($user_id) {
        $this->db->query('SELECT * FROM presensi_sekolah 
                         WHERE user_id = :user_id AND DATE(waktu) = CURDATE()');
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }
    
    public function getStatistikKehadiran($user_id) {
        // Statistik kehadiran untuk bulan ini dari presensi sekolah.
        $this->db->query('SELECT 
                         COUNT(*) as total,
                         SUM(CASE WHEN status = "valid" AND jenis = "hadir" THEN 1 ELSE 0 END) as hadir,
                         SUM(CASE WHEN jenis = "izin" THEN 1 ELSE 0 END) as izin,
                         SUM(CASE WHEN jenis = "sakit" THEN 1 ELSE 0 END) as sakit,
                         SUM(CASE WHEN jenis = "alpha" THEN 1 ELSE 0 END) as alpha
                         FROM presensi_sekolah
                         WHERE user_id = :user_id AND MONTH(waktu) = MONTH(CURDATE()) AND YEAR(waktu) = YEAR(CURDATE())');
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }
    
   
     
    public function getLaporanPresensiKelas($mata_pelajaran_id, $tanggal = null, $sesi_id = null) {
        // Presensi mapel memakai jadwal_mata_pelajaran sebagai sumber kelas.

        // Base select — list all siswa in mata pelajaran and join any matching presensi_mapel
        if ($sesi_id) {
            $sql = 'SELECT bi.id as siswa_id, bi.nipd, bi.nama, pk.status, pk.waktu, pk.jarak, pk.latitude, pk.longitude, pk.presensi_sesi_id, pk.jenis, pk.alasan, pk.foto_bukti, "kelas" as sumber
                    FROM buku_induk bi
                    INNER JOIN jadwal_mata_pelajaran_siswa js ON bi.id = js.siswa_id
                    LEFT JOIN presensi_mapel pk ON bi.id = pk.user_id AND pk.presensi_sesi_id = :sesi_id
                    WHERE js.jadwal_mata_pelajaran_id = :mata_pelajaran_id
                    ORDER BY bi.nama';
            $this->db->query($sql);
            $this->db->bind(':sesi_id', $sesi_id);
            $this->db->bind(':mata_pelajaran_id', $mata_pelajaran_id);
            return $this->db->resultSet();
        }

        // fallback: use date filter on presensi_mapel (today by default)
        $tanggal = $tanggal ?: date('Y-m-d');
        $sql = 'SELECT bi.id as siswa_id, bi.nipd, bi.nama, pk.status, pk.waktu, pk.jarak, pk.latitude, pk.longitude, pk.presensi_sesi_id, pk.jenis, pk.alasan, pk.foto_bukti, "kelas" as sumber
                FROM buku_induk bi
                INNER JOIN jadwal_mata_pelajaran_siswa js ON bi.id = js.siswa_id
                LEFT JOIN presensi_mapel pk ON bi.id = pk.user_id AND DATE(pk.waktu) = :tanggal AND pk.jadwal_mata_pelajaran_id = :mata_pelajaran_id
                WHERE js.jadwal_mata_pelajaran_id = :mata_pelajaran_id
                ORDER BY bi.nama';

        $this->db->query($sql);
        $this->db->bind(':tanggal', $tanggal);
        $this->db->bind(':mata_pelajaran_id', $mata_pelajaran_id);
        return $this->db->resultSet();
    }
    
    
    public function getLaporanPresensiSekolah($tanggal = null, $sesi_id = null, $filter_status = null, $limit = null, $offset = 0) {
        // Base select — list all siswa and join any matching presensi_sekolah
        if ($sesi_id) {
            $sql = 'SELECT bi.id as siswa_id, bi.nama, COALESCE(bi.email_ortu, "") AS email, ps.status, ps.waktu, ps.jarak, ps.latitude, ps.longitude, ps.jenis, ps.alasan, ps.foto_bukti, ps.presensi_sekolah_sesi_id, "sekolah" as sumber
                    FROM buku_induk bi
                    LEFT JOIN presensi_sekolah ps ON bi.id = ps.user_id AND ps.presensi_sekolah_sesi_id = :sesi_id
                    ORDER BY bi.nama';
            if ($limit) {
                $sql .= ' LIMIT :limit OFFSET :offset';
            }
            $this->db->query($sql);
            $this->db->bind(':sesi_id', $sesi_id);
            if ($limit) {
                $this->db->bind(':limit', $limit);
                $this->db->bind(':offset', $offset);
            }
            return $this->db->resultSet();
        }

        // fallback: use date filter on presensi_sekolah (today by default)
        $tanggal = $tanggal ?: date('Y-m-d');
        
        // Build query dengan filter status
        if ($filter_status) {
            if ($filter_status == 'valid') {
                // Filter hanya yang hadir (status valid)
                $sql = 'SELECT bi.id as siswa_id, bi.nama, COALESCE(bi.email_ortu, "") AS email, ps.status, ps.waktu, ps.jarak, ps.latitude, ps.longitude, ps.jenis, ps.alasan, ps.foto_bukti, ps.presensi_sekolah_sesi_id, "sekolah" as sumber
                        FROM buku_induk bi
                        INNER JOIN presensi_sekolah ps ON bi.id = ps.user_id AND DATE(ps.waktu) = :tanggal AND ps.status = "valid"
                        ORDER BY bi.nama';
            } else {
                // Filter berdasarkan jenis (izin, sakit, alpha)
                $sql = 'SELECT bi.id as siswa_id, bi.nama, COALESCE(bi.email_ortu, "") AS email, ps.status, ps.waktu, ps.jarak, ps.latitude, ps.longitude, ps.jenis, ps.alasan, ps.foto_bukti, ps.presensi_sekolah_sesi_id, "sekolah" as sumber
                        FROM buku_induk bi
                        INNER JOIN presensi_sekolah ps ON bi.id = ps.user_id AND DATE(ps.waktu) = :tanggal AND ps.jenis = :jenis
                        ORDER BY bi.nama';
                if ($limit) {
                    $sql .= ' LIMIT :limit OFFSET :offset';
                }
                $this->db->query($sql);
                $this->db->bind(':tanggal', $tanggal);
                $this->db->bind(':jenis', $filter_status);
                if ($limit) {
                    $this->db->bind(':limit', $limit);
                    $this->db->bind(':offset', $offset);
                }
                return $this->db->resultSet();
            }
        } else {
            // Tampilkan semua
            $sql = 'SELECT bi.id as siswa_id, bi.nama, COALESCE(bi.email_ortu, "") AS email, ps.status, ps.waktu, ps.jarak, ps.latitude, ps.longitude, ps.jenis, ps.alasan, ps.foto_bukti, ps.presensi_sekolah_sesi_id, "sekolah" as sumber
                    FROM buku_induk bi
                    LEFT JOIN presensi_sekolah ps ON bi.id = ps.user_id AND DATE(ps.waktu) = :tanggal
                    ORDER BY bi.nama';
        }

        if ($limit) {
            $sql .= ' LIMIT :limit OFFSET :offset';
        }
        $this->db->query($sql);
        $this->db->bind(':tanggal', $tanggal);
        if ($limit) {
            $this->db->bind(':limit', $limit);
            $this->db->bind(':offset', $offset);
        }
        return $this->db->resultSet();
    }

    
    public function countLaporanPresensiSekolah($tanggal = null, $sesi_id = null, $filter_status = null) {
        if ($sesi_id) {
            $sql = 'SELECT COUNT(bi.id) as total FROM buku_induk bi';
            $this->db->query($sql);
            $result = $this->db->single();
            return $result->total ?? 0;
        }

        $tanggal = $tanggal ?: date('Y-m-d');
        
        if ($filter_status) {
            if ($filter_status == 'valid') {
                $sql = 'SELECT COUNT(bi.id) as total FROM buku_induk bi
                        INNER JOIN presensi_sekolah ps ON bi.id = ps.user_id AND DATE(ps.waktu) = :tanggal AND ps.status = "valid"';
                $this->db->query($sql);
                $this->db->bind(':tanggal', $tanggal);
            } else {
                $sql = 'SELECT COUNT(bi.id) as total FROM buku_induk bi
                        INNER JOIN presensi_sekolah ps ON bi.id = ps.user_id AND DATE(ps.waktu) = :tanggal AND ps.jenis = :jenis';
                $this->db->query($sql);
                $this->db->bind(':tanggal', $tanggal);
                $this->db->bind(':jenis', $filter_status);
            }
        } else {
            $sql = 'SELECT COUNT(bi.id) as total FROM buku_induk bi';
            $this->db->query($sql);
        }
        
        $result = $this->db->single();
        return $result->total ?? 0;
    }
    
    
    public function getStatistikPresensiSekolah($tanggal = null, $bulan = null, $tahun = null, $user_id = null) {
        if ($tanggal) {
            // Statistik untuk tanggal tertentu
            if ($user_id) {
                // Statistik untuk user tertentu
                $sql = 'SELECT 
                        1 as total_siswa,
                        SUM(CASE WHEN ps.status = "valid" AND ps.jenis = "hadir" THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN ps.jenis = "izin" THEN 1 ELSE 0 END) as izin,
                        SUM(CASE WHEN ps.jenis = "sakit" THEN 1 ELSE 0 END) as sakit,
                        SUM(CASE WHEN ps.jenis = "alpha" THEN 1 ELSE 0 END) as alpha
                        FROM presensi_sekolah ps
                        WHERE ps.user_id = :user_id AND DATE(ps.waktu) = :tanggal';
                $this->db->query($sql);
                $this->db->bind(':user_id', $user_id);
                $this->db->bind(':tanggal', $tanggal);
            } else {
                // Statistik untuk semua siswa
                $sql = 'SELECT 
                        COUNT(DISTINCT bi.id) as total_siswa,
                        COUNT(DISTINCT CASE WHEN ps.status = "valid" AND ps.jenis = "hadir" THEN ps.user_id END) as hadir,
                        COUNT(DISTINCT CASE WHEN ps.jenis = "izin" THEN ps.user_id END) as izin,
                        COUNT(DISTINCT CASE WHEN ps.jenis = "sakit" THEN ps.user_id END) as sakit,
                        COUNT(DISTINCT CASE WHEN ps.jenis = "alpha" THEN ps.user_id END) as alpha
                        FROM buku_induk bi
                        LEFT JOIN presensi_sekolah ps ON bi.id = ps.user_id AND DATE(ps.waktu) = :tanggal';
                $this->db->query($sql);
                $this->db->bind(':tanggal', $tanggal);
            }
        } elseif ($bulan && $tahun) {
            // Statistik untuk bulan tertentu
            if ($user_id) {
                // Statistik untuk user tertentu
                $sql = 'SELECT 
                        1 as total_siswa,
                        SUM(CASE WHEN ps.status = "valid" AND ps.jenis = "hadir" THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN ps.jenis = "izin" THEN 1 ELSE 0 END) as izin,
                        SUM(CASE WHEN ps.jenis = "sakit" THEN 1 ELSE 0 END) as sakit,
                        SUM(CASE WHEN ps.jenis = "alpha" THEN 1 ELSE 0 END) as alpha
                        FROM presensi_sekolah ps
                        WHERE ps.user_id = :user_id AND MONTH(ps.waktu) = :bulan AND YEAR(ps.waktu) = :tahun';
                $this->db->query($sql);
                $this->db->bind(':user_id', $user_id);
                $this->db->bind(':bulan', $bulan);
                $this->db->bind(':tahun', $tahun);
            } else {
                // Statistik untuk semua siswa
                $sql = 'SELECT 
                        COUNT(DISTINCT bi.id) as total_siswa,
                        COUNT(DISTINCT CASE WHEN ps.status = "valid" AND ps.jenis = "hadir" THEN ps.user_id END) as hadir,
                        COUNT(DISTINCT CASE WHEN ps.jenis = "izin" THEN ps.user_id END) as izin,
                        COUNT(DISTINCT CASE WHEN ps.jenis = "sakit" THEN ps.user_id END) as sakit,
                        COUNT(DISTINCT CASE WHEN ps.jenis = "alpha" THEN ps.user_id END) as alpha
                        FROM buku_induk bi
                        LEFT JOIN presensi_sekolah ps ON bi.id = ps.user_id AND MONTH(ps.waktu) = :bulan AND YEAR(ps.waktu) = :tahun';
                $this->db->query($sql);
                $this->db->bind(':bulan', $bulan);
                $this->db->bind(':tahun', $tahun);
            }
        } else {
            // Default: statistik hari ini
            if ($user_id) {
                // Statistik untuk user tertentu
                $sql = 'SELECT 
                        1 as total_siswa,
                        SUM(CASE WHEN ps.status = "valid" AND ps.jenis = "hadir" THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN ps.jenis = "izin" THEN 1 ELSE 0 END) as izin,
                        SUM(CASE WHEN ps.jenis = "sakit" THEN 1 ELSE 0 END) as sakit,
                        SUM(CASE WHEN ps.jenis = "alpha" THEN 1 ELSE 0 END) as alpha
                        FROM presensi_sekolah ps
                        WHERE ps.user_id = :user_id AND DATE(ps.waktu) = CURDATE()';
                $this->db->query($sql);
                $this->db->bind(':user_id', $user_id);
            } else {
                // Statistik untuk semua siswa
                $sql = 'SELECT 
                        COUNT(DISTINCT bi.id) as total_siswa,
                        COUNT(DISTINCT CASE WHEN ps.status = "valid" AND ps.jenis = "hadir" THEN ps.user_id END) as hadir,
                        COUNT(DISTINCT CASE WHEN ps.jenis = "izin" THEN ps.user_id END) as izin,
                        COUNT(DISTINCT CASE WHEN ps.jenis = "sakit" THEN ps.user_id END) as sakit,
                        COUNT(DISTINCT CASE WHEN ps.jenis = "alpha" THEN ps.user_id END) as alpha
                        FROM buku_induk bi
                        LEFT JOIN presensi_sekolah ps ON bi.id = ps.user_id AND DATE(ps.waktu) = CURDATE()';
                $this->db->query($sql);
            }
        }
        
        return $this->db->single();
    }
    
    public function getStatistikPresensiKelas($tanggal = null, $bulan = null, $tahun = null, $user_id = null, $filters = []) {
        $filters = is_array($filters) ? $filters : ['kelas_jadwal_id' => $filters];
        if ($tanggal) {
            // Statistik untuk tanggal tertentu
            if ($user_id) {
                // Statistik untuk user tertentu
                $sql = 'SELECT 
                        1 as total_siswa,
                        SUM(CASE WHEN pk.status = "valid" AND pk.jenis = "hadir" THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN pk.jenis = "izin" THEN 1 ELSE 0 END) as izin,
                        SUM(CASE WHEN pk.jenis = "sakit" THEN 1 ELSE 0 END) as sakit,
                        SUM(CASE WHEN pk.jenis = "alpha" THEN 1 ELSE 0 END) as alpha
                        FROM presensi_mapel pk
                        INNER JOIN jadwal_mata_pelajaran j ON pk.jadwal_mata_pelajaran_id = j.id
                        LEFT JOIN periode_kelas pkel ON j.kelas_jadwal_id = pkel.id
                        LEFT JOIN kelas k ON pkel.kelas_id = k.id
                        WHERE pk.user_id = :user_id AND DATE(pk.waktu) = :tanggal';
                $sql .= $this->buildMapelFilterSql($filters);
                $this->db->query($sql);
                $this->db->bind(':user_id', $user_id);
                $this->db->bind(':tanggal', $tanggal);
                $this->bindMapelFilters($filters);
            } else {
                // Statistik untuk semua siswa
                $sql = 'SELECT 
                        COUNT(DISTINCT bi.id) as total_siswa,
                        COUNT(DISTINCT CASE WHEN pk.status = "valid" AND pk.jenis = "hadir" THEN pk.user_id END) as hadir,
                        COUNT(DISTINCT CASE WHEN pk.jenis = "izin" THEN pk.user_id END) as izin,
                        COUNT(DISTINCT CASE WHEN pk.jenis = "sakit" THEN pk.user_id END) as sakit,
                        COUNT(DISTINCT CASE WHEN pk.jenis = "alpha" THEN pk.user_id END) as alpha
                        FROM buku_induk bi
                        LEFT JOIN presensi_mapel pk ON bi.id = pk.user_id AND DATE(pk.waktu) = :tanggal';
                $this->db->query($sql);
                $this->db->bind(':tanggal', $tanggal);
            }
        } elseif ($bulan && $tahun) {
            // Statistik untuk bulan tertentu
            if ($user_id) {
                // Statistik untuk user tertentu
                $sql = 'SELECT 
                        1 as total_siswa,
                        SUM(CASE WHEN pk.status = "valid" AND pk.jenis = "hadir" THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN pk.jenis = "izin" THEN 1 ELSE 0 END) as izin,
                        SUM(CASE WHEN pk.jenis = "sakit" THEN 1 ELSE 0 END) as sakit,
                        SUM(CASE WHEN pk.jenis = "alpha" THEN 1 ELSE 0 END) as alpha
                        FROM presensi_mapel pk
                        INNER JOIN jadwal_mata_pelajaran j ON pk.jadwal_mata_pelajaran_id = j.id
                        LEFT JOIN periode_kelas pkel ON j.kelas_jadwal_id = pkel.id
                        LEFT JOIN kelas k ON pkel.kelas_id = k.id
                        WHERE pk.user_id = :user_id AND MONTH(pk.waktu) = :bulan AND YEAR(pk.waktu) = :tahun';
                $sql .= $this->buildMapelFilterSql($filters);
                $this->db->query($sql);
                $this->db->bind(':user_id', $user_id);
                $this->db->bind(':bulan', $bulan);
                $this->db->bind(':tahun', $tahun);
                $this->bindMapelFilters($filters);
            } else {
                // Statistik untuk semua siswa
                $sql = 'SELECT 
                        COUNT(DISTINCT bi.id) as total_siswa,
                        COUNT(DISTINCT CASE WHEN pk.status = "valid" AND pk.jenis = "hadir" THEN pk.user_id END) as hadir,
                        COUNT(DISTINCT CASE WHEN pk.jenis = "izin" THEN pk.user_id END) as izin,
                        COUNT(DISTINCT CASE WHEN pk.jenis = "sakit" THEN pk.user_id END) as sakit,
                        COUNT(DISTINCT CASE WHEN pk.jenis = "alpha" THEN pk.user_id END) as alpha
                        FROM buku_induk bi
                        LEFT JOIN presensi_mapel pk ON bi.id = pk.user_id AND MONTH(pk.waktu) = :bulan AND YEAR(pk.waktu) = :tahun';
                $this->db->query($sql);
                $this->db->bind(':bulan', $bulan);
                $this->db->bind(':tahun', $tahun);
            }
        } else {
            // Default: statistik hari ini
            if ($user_id) {
                // Statistik untuk user tertentu
                $sql = 'SELECT 
                        1 as total_siswa,
                        SUM(CASE WHEN pk.status = "valid" AND pk.jenis = "hadir" THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN pk.jenis = "izin" THEN 1 ELSE 0 END) as izin,
                        SUM(CASE WHEN pk.jenis = "sakit" THEN 1 ELSE 0 END) as sakit,
                        SUM(CASE WHEN pk.jenis = "alpha" THEN 1 ELSE 0 END) as alpha
                        FROM presensi_mapel pk
                        INNER JOIN jadwal_mata_pelajaran j ON pk.jadwal_mata_pelajaran_id = j.id
                        LEFT JOIN periode_kelas pkel ON j.kelas_jadwal_id = pkel.id
                        LEFT JOIN kelas k ON pkel.kelas_id = k.id
                        WHERE pk.user_id = :user_id AND DATE(pk.waktu) = CURDATE()';
                $sql .= $this->buildMapelFilterSql($filters);
                $this->db->query($sql);
                $this->db->bind(':user_id', $user_id);
                $this->bindMapelFilters($filters);
            } else {
                // Statistik untuk semua siswa
                $sql = 'SELECT 
                        COUNT(DISTINCT bi.id) as total_siswa,
                        COUNT(DISTINCT CASE WHEN pk.status = "valid" AND pk.jenis = "hadir" THEN pk.user_id END) as hadir,
                        COUNT(DISTINCT CASE WHEN pk.jenis = "izin" THEN pk.user_id END) as izin,
                        COUNT(DISTINCT CASE WHEN pk.jenis = "sakit" THEN pk.user_id END) as sakit,
                        COUNT(DISTINCT CASE WHEN pk.jenis = "alpha" THEN pk.user_id END) as alpha
                        FROM buku_induk bi
                        LEFT JOIN presensi_mapel pk ON bi.id = pk.user_id AND DATE(pk.waktu) = CURDATE()';
                $this->db->query($sql);
            }
        }
        
        return $this->db->single();
    }

    private function buildMapelFilterSql($filters) {
        $sql = '';
        if (!empty($filters['mapel'])) {
            $sql .= ' AND j.nama_mata_pelajaran = :filter_mapel';
        }
        if (!empty($filters['semester'])) {
            $sql .= ' AND pkel.semester = :filter_semester';
        }
        if (!empty($filters['tahun_ajaran'])) {
            $sql .= ' AND pkel.tahun_ajaran = :filter_tahun_ajaran';
        }
        if (!empty($filters['kelas_jadwal_id'])) {
            $sql .= ' AND j.kelas_jadwal_id = :filter_kelas_jadwal_id';
        }
        return $sql;
    }

    private function bindMapelFilters($filters) {
        if (!empty($filters['mapel'])) {
            $this->db->bind(':filter_mapel', $filters['mapel']);
        }
        if (!empty($filters['semester'])) {
            $this->db->bind(':filter_semester', $filters['semester']);
        }
        if (!empty($filters['tahun_ajaran'])) {
            $this->db->bind(':filter_tahun_ajaran', $filters['tahun_ajaran']);
        }
        if (!empty($filters['kelas_jadwal_id'])) {
            $this->db->bind(':filter_kelas_jadwal_id', (int) $filters['kelas_jadwal_id']);
        }
    }
    
    public function ajukanIzin($data) {
        $this->db->query('INSERT INTO izin_siswa (siswa_id, tanggal, jenis_izin, alasan, foto_bukti,  waktu_pengajuan) 
                         VALUES (:siswa_id, :tanggal, :jenis_izin, :alasan, :foto_bukti,  :waktu_pengajuan)');
        $this->db->bind(':siswa_id', $data['siswa_id']);
        $this->db->bind(':tanggal', $data['tanggal']);
        $this->db->bind(':jenis_izin', $data['jenis_izin'] ?? 'izin');
        $this->db->bind(':alasan', $data['alasan']);
        $this->db->bind(':foto_bukti', $data['foto_bukti'] ?? null);
        $this->db->bind(':waktu_pengajuan', $data['waktu_pengajuan'] ?? date('Y-m-d H:i:s'));
        
        return $this->db->execute();
    }
    
    public function getIzinBySiswa($siswa_id) {
        $this->db->query('SELECT * FROM izin_siswa WHERE siswa_id = :siswa_id ORDER BY tanggal DESC');
        $this->db->bind(':siswa_id', $siswa_id);
        return $this->db->resultSet();
    }


    public function hasPresensiInSchoolSession($user_id, $presensi_sekolah_sesi_id) {
        if (!$presensi_sekolah_sesi_id) return false;
        $this->db->query('SELECT id FROM presensi_sekolah WHERE user_id = :user_id AND presensi_sekolah_sesi_id = :sesi_id LIMIT 1');
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':sesi_id', $presensi_sekolah_sesi_id);
        $row = $this->db->single();
        return $row ? true : false;
    }

    
    public function getPresensiInSchoolSession($user_id, $presensi_sekolah_sesi_id) {
        if (!$presensi_sekolah_sesi_id) return null;
        $this->db->query('SELECT * FROM presensi_sekolah WHERE user_id = :user_id AND presensi_sekolah_sesi_id = :sesi_id LIMIT 1');
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':sesi_id', $presensi_sekolah_sesi_id);
        return $this->db->single();
    }

    
    public function updatePresensiSekolahById($id, $data) {
        $this->db->query('UPDATE presensi_sekolah SET 
                         latitude = :latitude, 
                         longitude = :longitude, 
                         jarak = :jarak, 
                         status = :status, 
                         jenis = :jenis, 
                         alasan = :alasan, 
                         foto_bukti = :foto_bukti,
                         waktu = NOW()
                         WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':latitude', $data['latitude']);
        $this->db->bind(':longitude', $data['longitude']);
        $this->db->bind(':jarak', $data['jarak']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':jenis', $data['jenis']);
        $this->db->bind(':alasan', $data['alasan'] ?? null);
        $this->db->bind(':foto_bukti', $data['foto_bukti'] ?? null);
        return $this->db->execute();
    }

    
    public function getTanggalPresensiSekolah() {
        $this->db->query('SELECT DISTINCT DATE(waktu) as tanggal FROM presensi_sekolah ORDER BY tanggal DESC');
        return $this->db->resultSet();
    }

   
    public function hasIzinOnDate($siswa_id, $tanggal) {
        $this->db->query('SELECT * FROM izin_siswa WHERE siswa_id = :siswa_id AND tanggal = :tanggal LIMIT 1');
        $this->db->bind(':siswa_id', $siswa_id);
        $this->db->bind(':tanggal', $tanggal);
        $row = $this->db->single();
        return $row ? $row : false;
    }

   
    public function createPresensiSekolahIzin($siswa_id, $tanggal, $jenis_izin, $alasan = null, $foto_bukti = null, $waktu_pengajuan = null, $sesi_sekolah_id = null) {
        // Check if presensi already exists for this date
        $this->db->query('SELECT id FROM presensi_sekolah WHERE user_id = :user_id AND DATE(waktu) = :tanggal LIMIT 1');
        $this->db->bind(':user_id', $siswa_id);
        $this->db->bind(':tanggal', $tanggal);
        $exists = $this->db->single();
        
        if ($exists) {
            return true; // Already exists
        }

        // Use waktu_pengajuan if provided, otherwise use tanggal + 00:00:00
        $waktu_insert = $waktu_pengajuan ?: ($tanggal . ' 00:00:00');

        // Insert izin record to presensi_sekolah
        $this->db->query('INSERT INTO presensi_sekolah (presensi_sekolah_sesi_id, user_id, latitude, longitude, jarak, status, jenis, alasan, foto_bukti, waktu) 
                         VALUES (:sesi_id, :user_id, 0, 0, 0, "valid", :jenis, :alasan, :foto_bukti, :waktu)');
        $this->db->bind(':sesi_id', $sesi_sekolah_id);
        $this->db->bind(':user_id', $siswa_id);
        $this->db->bind(':jenis', $jenis_izin);
        $this->db->bind(':alasan', $alasan);
        $this->db->bind(':foto_bukti', $foto_bukti);
        $this->db->bind(':waktu', $waktu_insert);
        return $this->db->execute();
    }

   
    public function createPresensiKelasIzin($siswa_id, $tanggal, $jenis_izin, $alasan = null, $foto_bukti = null, $waktu_pengajuan = null, $sesi_kelas_id = null) {
        return true;

        // Get all classes the student is enrolled in
        $this->db->query('SELECT mata_pelajaran_id FROM siswa_mata_pelajaran WHERE siswa_id = :siswa_id');
        $this->db->bind(':siswa_id', $siswa_id);
        $kelas_list = $this->db->resultSet();

        // Use waktu_pengajuan if provided, otherwise use tanggal + 00:00:00
        $waktu_insert = $waktu_pengajuan ?: ($tanggal . ' 00:00:00');

        foreach ($kelas_list as $kelas) {
            // Check if presensi already exists for this class and date
            $this->db->query('SELECT id FROM presensi_mapel WHERE user_id = :user_id AND mata_pelajaran_id = :mata_pelajaran_id AND DATE(waktu) = :tanggal LIMIT 1');
            $this->db->bind(':user_id', $siswa_id);
            $this->db->bind(':mata_pelajaran_id', $kelas->mata_pelajaran_id);
            $this->db->bind(':tanggal', $tanggal);
            $exists = $this->db->single();
            
            if (!$exists) {
                // Insert izin record to presensi_mapel
                $this->db->query('INSERT INTO presensi_mapel (presensi_sesi_id, user_id, mata_pelajaran_id, latitude, longitude, jarak, status, jenis, alasan, foto_bukti, waktu) 
                                 VALUES (:sesi_id, :user_id, :mata_pelajaran_id, 0, 0, 0, "valid", :jenis, :alasan, :foto_bukti, :waktu)');
                $this->db->bind(':sesi_id', $sesi_kelas_id);
                $this->db->bind(':user_id', $siswa_id);
                $this->db->bind(':mata_pelajaran_id', $kelas->mata_pelajaran_id);
                $this->db->bind(':jenis', $jenis_izin); // Store jenis_izin in jenis field (izin, sakit, etc)
                $this->db->bind(':alasan', $alasan);
                $this->db->bind(':foto_bukti', $foto_bukti);
                $this->db->bind(':waktu', $waktu_insert);
                $this->db->execute();
            }
        }
        return true;
    }

   
    public function createPresensiKelasSingleIzin($siswa_id, $mata_pelajaran_id, $tanggal, $jenis_izin, $alasan = null, $foto_bukti = null, $waktu_pengajuan = null, $sesi_kelas_id = null) {
        return true;

        // Check if presensi already exists for this class and date
        $this->db->query('SELECT id FROM presensi_mapel WHERE user_id = :user_id AND mata_pelajaran_id = :mata_pelajaran_id AND DATE(waktu) = :tanggal LIMIT 1');
        $this->db->bind(':user_id', $siswa_id);
        $this->db->bind(':mata_pelajaran_id', $mata_pelajaran_id);
        $this->db->bind(':tanggal', $tanggal);
        $exists = $this->db->single();
        
        if ($exists) {
            return true; // Already exists
        }

        // Use waktu_pengajuan if provided, otherwise use tanggal + 00:00:00
        $waktu_insert = $waktu_pengajuan ?: ($tanggal . ' 00:00:00');

        // Insert izin record to presensi_mapel
        $this->db->query('INSERT INTO presensi_mapel (presensi_sesi_id, user_id, mata_pelajaran_id, latitude, longitude, jarak, status, jenis, alasan, foto_bukti, waktu) 
                         VALUES (:sesi_id, :user_id, :mata_pelajaran_id, 0, 0, 0, "valid", :jenis, :alasan, :foto_bukti, :waktu)');
        $this->db->bind(':sesi_id', $sesi_kelas_id);
        $this->db->bind(':user_id', $siswa_id);
        $this->db->bind(':mata_pelajaran_id', $mata_pelajaran_id);
        $this->db->bind(':jenis', $jenis_izin);
        $this->db->bind(':alasan', $alasan);
        $this->db->bind(':foto_bukti', $foto_bukti);
        $this->db->bind(':waktu', $waktu_insert);
        return $this->db->execute();
    }

   
    public function updatePresensiKelas($siswa_id, $mata_pelajaran_id, $jenis, $alasan = null, $foto_bukti = null, $sesi_id = null) {
        // Build WHERE clause based on whether sesi_id is provided
        if ($sesi_id) {
            // Update specific session
            $sql = 'UPDATE presensi_mapel 
                    SET jenis = :jenis, alasan = :alasan, foto_bukti = :foto_bukti, status = :status,
                        latitude = :latitude, longitude = :longitude, jarak = :jarak
                    WHERE user_id = :user_id AND jadwal_mata_pelajaran_id = :mata_pelajaran_id AND presensi_sesi_id = :sesi_id';
        } else {
            // Update today's attendance
            $sql = 'UPDATE presensi_mapel 
                    SET jenis = :jenis, alasan = :alasan, foto_bukti = :foto_bukti, status = :status,
                        latitude = :latitude, longitude = :longitude, jarak = :jarak
                    WHERE user_id = :user_id AND jadwal_mata_pelajaran_id = :mata_pelajaran_id AND DATE(waktu) = CURDATE()';
        }
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $siswa_id);
        $this->db->bind(':mata_pelajaran_id', $mata_pelajaran_id);
        $this->db->bind(':jenis', $jenis);
        $this->db->bind(':alasan', $alasan);
        $this->db->bind(':foto_bukti', $foto_bukti);
        
        // Set status and coordinates based on jenis
        if ($jenis === 'izin' || $jenis === 'sakit') {
            $this->db->bind(':status', 'valid');
            $this->db->bind(':latitude', 0);
            $this->db->bind(':longitude', 0);
            $this->db->bind(':jarak', 0);
        } else {
            $this->db->bind(':status', 'valid');
            $this->db->bind(':latitude', 0);
            $this->db->bind(':longitude', 0);
            $this->db->bind(':jarak', 0);
        }
        
        if ($sesi_id) {
            $this->db->bind(':sesi_id', $sesi_id);
        }
        
        return $this->db->execute();
    }

  
    public function createOrUpdatePresensiKelas($siswa_id, $mata_pelajaran_id, $jenis, $alasan = null, $foto_bukti = null, $sesi_id = null) {
        // Check if record exists
        if ($sesi_id) {
            $this->db->query('SELECT id FROM presensi_mapel WHERE user_id = :user_id AND jadwal_mata_pelajaran_id = :mata_pelajaran_id AND presensi_sesi_id = :sesi_id LIMIT 1');
            $this->db->bind(':user_id', $siswa_id);
            $this->db->bind(':mata_pelajaran_id', $mata_pelajaran_id);
            $this->db->bind(':sesi_id', $sesi_id);
        } else {
            $this->db->query('SELECT id FROM presensi_mapel WHERE user_id = :user_id AND jadwal_mata_pelajaran_id = :mata_pelajaran_id AND DATE(waktu) = CURDATE() LIMIT 1');
            $this->db->bind(':user_id', $siswa_id);
            $this->db->bind(':mata_pelajaran_id', $mata_pelajaran_id);
        }
        
        $exists = $this->db->single();
        
        if ($exists) {
            // Update existing record
            return $this->updatePresensiKelas($siswa_id, $mata_pelajaran_id, $jenis, $alasan, $foto_bukti, $sesi_id);
        } else {
            // Create new record
            $data = [
                'presensi_sesi_id' => $sesi_id,
                'user_id' => $siswa_id,
                'jadwal_mata_pelajaran_id' => $mata_pelajaran_id,
                'latitude' => 0,
                'longitude' => 0,
                'jarak' => 0,
                'status' => 'valid',
                'jenis' => $jenis,
                'alasan' => $alasan,
                'foto_bukti' => $foto_bukti
            ];
            return $this->recordPresensiKelas($data);
        }
    }

   
    public function updatePresensiSekolah($siswa_id, $tanggal, $jenis, $alasan = null, $foto_bukti = null, $sesi_id = null) {
        // Build WHERE clause based on whether sesi_id is provided
        if ($sesi_id) {
            // Update specific session
            $sql = 'UPDATE presensi_sekolah 
                    SET jenis = :jenis, alasan = :alasan, foto_bukti = :foto_bukti, status = :status,
                        latitude = :latitude, longitude = :longitude, jarak = :jarak
                    WHERE user_id = :user_id AND presensi_sekolah_sesi_id = :sesi_id';
        } else {
            // Update by date
            $sql = 'UPDATE presensi_sekolah 
                    SET jenis = :jenis, alasan = :alasan, foto_bukti = :foto_bukti, status = :status,
                        latitude = :latitude, longitude = :longitude, jarak = :jarak
                    WHERE user_id = :user_id AND DATE(waktu) = :tanggal';
        }
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $siswa_id);
        $this->db->bind(':jenis', $jenis);
        $this->db->bind(':alasan', $alasan);
        $this->db->bind(':foto_bukti', $foto_bukti);
        
        // Set status and coordinates based on jenis
        if ($jenis === 'izin' || $jenis === 'sakit') {
            $this->db->bind(':status', 'valid');
            $this->db->bind(':latitude', 0);
            $this->db->bind(':longitude', 0);
            $this->db->bind(':jarak', 0);
        } else {
            $this->db->bind(':status', 'valid');
            $this->db->bind(':latitude', 0);
            $this->db->bind(':longitude', 0);
            $this->db->bind(':jarak', 0);
        }
        
        if ($sesi_id) {
            $this->db->bind(':sesi_id', $sesi_id);
        } else {
            $this->db->bind(':tanggal', $tanggal);
        }
        
        return $this->db->execute();
    }

 
    public function createOrUpdatePresensiSekolah($siswa_id, $tanggal, $jenis, $alasan = null, $foto_bukti = null, $sesi_id = null) {
        // Check if record exists
        if ($sesi_id) {
            $this->db->query('SELECT id FROM presensi_sekolah WHERE user_id = :user_id AND presensi_sekolah_sesi_id = :sesi_id LIMIT 1');
            $this->db->bind(':user_id', $siswa_id);
            $this->db->bind(':sesi_id', $sesi_id);
        } else {
            $this->db->query('SELECT id FROM presensi_sekolah WHERE user_id = :user_id AND DATE(waktu) = :tanggal LIMIT 1');
            $this->db->bind(':user_id', $siswa_id);
            $this->db->bind(':tanggal', $tanggal);
        }
        
        $exists = $this->db->single();
        
        if ($exists) {
            // Update existing record
            return $this->updatePresensiSekolah($siswa_id, $tanggal, $jenis, $alasan, $foto_bukti, $sesi_id);
        } else {
            // Create new record
            $data = [
                'presensi_sekolah_sesi_id' => $sesi_id,
                'user_id' => $siswa_id,
                'latitude' => 0,
                'longitude' => 0,
                'jarak' => 0,
                'status' => 'valid',
                'jenis' => $jenis,
                'alasan' => $alasan,
                'foto_bukti' => $foto_bukti
            ];
            return $this->recordPresensiSekolah($data);
        }
    }

 
    public function markAbsentStudentsAsAlphaSekolah($sesi_id) {
        // Get session info for notification
        $this->db->query('SELECT * FROM presensi_sekolah_sesi WHERE id = :sesi_id');
        $this->db->bind(':sesi_id', $sesi_id);
        $sesiInfo = $this->db->single();
        
        // Get all students who haven't checked in for this session
        $this->db->query('
            SELECT bi.id, bi.nama
            FROM buku_induk bi
            WHERE bi.id NOT IN (
                SELECT user_id 
                FROM presensi_sekolah 
                WHERE presensi_sekolah_sesi_id = :sesi_id
            )
        ');
        $this->db->bind(':sesi_id', $sesi_id);
        $absentStudents = $this->db->resultSet();
        
        // Initialize email service, WhatsApp service, and buku induk model
        $emailService = new EmailService();
        $whatsappService = new WhatsAppService();
        $bukuIndukModel = new BukuIndukModel();
        
        $count = 0;
        $notificationStats = [
            'email_sent' => 0,
            'email_failed' => 0,
            'whatsapp_sent' => 0,
            'whatsapp_failed' => 0,
            'no_contact' => 0
        ];
        
        foreach ($absentStudents as $student) {
            $data = [
                'presensi_sekolah_sesi_id' => $sesi_id,
                'user_id' => $student->id,
                'latitude' => 0,
                'longitude' => 0,
                'jarak' => 0,
                'status' => 'valid',
                'jenis' => 'alpha',
                'alasan' => 'Tidak hadir saat sesi ditutup',
                'foto_bukti' => null
            ];
            if ($this->recordPresensiSekolah($data)) {
                $count++;
                
                $tanggal = date('Y-m-d');
                $waktu = $sesiInfo->waktu_tutup ?? date('Y-m-d H:i:s');
                $parentContact = $bukuIndukModel->getParentContact($student->id);
                
                // Log untuk debugging
                error_log("===== ALPHA NOTIFICATION - SEKOLAH =====");
                error_log("Student: " . $student->nama . " (ID: " . $student->id . ")");
                error_log("Parent Contact: " . json_encode($parentContact));
                
                if (!$parentContact || (empty($parentContact['email']) && empty($parentContact['phone']))) {
                    $notificationStats['no_contact']++;
                    error_log("WARNING: No parent contact info for student " . $student->nama);
                    continue;
                }
                
                // Kirim email notifikasi ke orang tua
                if (!empty($parentContact['email'])) {
                    try {
                        $emailResult = $emailService->sendAlphaNotificationSekolah(
                            $parentContact['email'],
                            $student->nama,
                            $tanggal,
                            $waktu
                        );
                        if ($emailResult) {
                            $notificationStats['email_sent']++;
                            error_log("SUCCESS: Email sent to " . $parentContact['email']);
                        } else {
                            $notificationStats['email_failed']++;
                            error_log("FAILED: Email not sent to " . $parentContact['email']);
                        }
                    } catch (Exception $e) {
                        $notificationStats['email_failed']++;
                        error_log('EXCEPTION: Failed to send alpha notification email: ' . $e->getMessage());
                    }
                } else {
                    error_log("SKIPPED: No email address for student " . $student->nama);
                }
                
                // Kirim WhatsApp notifikasi ke orang tua
                if (!empty($parentContact['phone'])) {
                    try {
                        $whatsappResult = $whatsappService->sendAlphaNotificationSekolah(
                            $parentContact['phone'],
                            $student->nama,
                            $tanggal,
                            $waktu
                        );
                        if ($whatsappResult) {
                            $notificationStats['whatsapp_sent']++;
                            error_log("SUCCESS: WhatsApp sent to " . $parentContact['phone']);
                        } else {
                            $notificationStats['whatsapp_failed']++;
                            error_log("FAILED: WhatsApp not sent to " . $parentContact['phone']);
                        }
                    } catch (Exception $e) {
                        $notificationStats['whatsapp_failed']++;
                        error_log('EXCEPTION: Failed to send alpha notification WhatsApp: ' . $e->getMessage());
                    }
                } else {
                    error_log("SKIPPED: No phone number for student " . $student->nama);
                }
                
                error_log("=========================================");
            }
        }
        
        // Log final statistics
        error_log("ALPHA NOTIFICATION STATS - SEKOLAH:");
        error_log("  Students marked alpha: $count");
        error_log("  Emails sent: " . $notificationStats['email_sent']);
        error_log("  Emails failed: " . $notificationStats['email_failed']);
        error_log("  WhatsApp sent: " . $notificationStats['whatsapp_sent']);
        error_log("  WhatsApp failed: " . $notificationStats['whatsapp_failed']);
        error_log("  No contact info: " . $notificationStats['no_contact']);
        
        return $count;
    }

    public function closeExpiredSekolahSessions() {
        $now = date('Y-m-d H:i:s');
        $this->db->query('SELECT id FROM presensi_sekolah_sesi WHERE status = "open" AND waktu_tutup <= :now ORDER BY waktu_tutup ASC');
        $this->db->bind(':now', $now);
        $expiredSessions = $this->db->resultSet();

        $closedCount = 0;
        $alphaCount = 0;

        foreach ($expiredSessions as $session) {
            $alphaCount += $this->markAbsentStudentsAsAlphaSekolah($session->id);

            $this->db->query('UPDATE presensi_sekolah_sesi SET status = "closed" WHERE id = :id AND status = "open"');
            $this->db->bind(':id', $session->id);
            if ($this->db->execute()) {
                $closedCount++;
            }
        }

        return [
            'closed_count' => $closedCount,
            'alpha_count' => $alphaCount
        ];
    }

    
    public function markAbsentStudentsAsAlphaKelas($mata_pelajaran_id, $sesi_id) {
        // Get session and class info for notification
        $this->db->query('SELECT ps.*, k.nama_mata_pelajaran FROM presensi_mapel_sesi ps
                 LEFT JOIN jadwal_mata_pelajaran k ON ps.jadwal_mata_pelajaran_id = k.id
                 WHERE ps.id = :sesi_id');
        $this->db->bind(':sesi_id', $sesi_id);
        $sesiInfo = $this->db->single();
        
        // Log session info
        error_log("===== SESSION INFO - KELAS =====");
        error_log("Sesi ID: " . $sesi_id);
        error_log("Mata Pelajaran ID: " . $mata_pelajaran_id);
        if ($sesiInfo) {
            error_log("Nama Mata Pelajaran: " . ($sesiInfo->nama_mata_pelajaran ?? 'N/A'));
            error_log("Waktu Tutup: " . ($sesiInfo->waktu_tutup ?? 'N/A'));
        } else {
            error_log("WARNING: Session info not found!");
        }
        error_log("================================");
        
        // Get all students in this class who haven't checked in for this session
        // Use mata_pelajaran_id from PHP variable to avoid double binding issue
        $this->db->query('
            SELECT js.siswa_id, bi.nama
            FROM jadwal_mata_pelajaran_siswa js
            LEFT JOIN buku_induk bi ON js.siswa_id = bi.id
            WHERE js.jadwal_mata_pelajaran_id = :mata_pelajaran_id
            AND js.siswa_id NOT IN (
                SELECT user_id 
                FROM presensi_mapel 
                WHERE jadwal_mata_pelajaran_id = :mata_pelajaran_id2
                AND presensi_sesi_id = :sesi_id
            )
        ');
        $this->db->bind(':mata_pelajaran_id', $mata_pelajaran_id);
        $this->db->bind(':mata_pelajaran_id2', $mata_pelajaran_id);
        $this->db->bind(':sesi_id', $sesi_id);
        $absentStudents = $this->db->resultSet();
        
        // Log query results for debugging
        error_log("===== CHECKING ABSENT STUDENTS - KELAS =====");
        error_log("Mata Pelajaran ID: " . $mata_pelajaran_id);
        error_log("Sesi ID: " . $sesi_id);
        error_log("Absent Students Found: " . count($absentStudents));
        if (count($absentStudents) > 0) {
            error_log("Students: " . json_encode(array_map(function($s) {
                return ['siswa_id' => $s->siswa_id, 'nama' => $s->nama];
            }, $absentStudents)));
        }
        error_log("============================================");
        
        // Initialize email service, WhatsApp service, and buku induk model
        $emailService = new EmailService();
        $whatsappService = new WhatsAppService();
        $bukuIndukModel = new BukuIndukModel();
        
        $count = 0;
        $notificationStats = [
            'email_sent' => 0,
            'email_failed' => 0,
            'whatsapp_sent' => 0,
            'whatsapp_failed' => 0,
            'no_contact' => 0
        ];
        
        foreach ($absentStudents as $student) {
            $data = [
                'presensi_sesi_id' => $sesi_id,
                'user_id' => $student->siswa_id,
                'jadwal_mata_pelajaran_id' => $mata_pelajaran_id,
                'latitude' => 0,
                'longitude' => 0,
                'jarak' => 0,
                'status' => 'valid',
                'jenis' => 'alpha',
                'alasan' => 'Tidak hadir saat sesi ditutup',
                'foto_bukti' => null
            ];
            if ($this->recordPresensiKelas($data)) {
                $count++;
                
                $tanggal = date('Y-m-d');
                $waktu = $sesiInfo->waktu_tutup ?? date('Y-m-d H:i:s');
                $namaKelas = $sesiInfo->nama_mata_pelajaran ?? 'Mata Pelajaran';
                $parentContact = $bukuIndukModel->getParentContact($student->siswa_id);
                
                // Log untuk debugging
                error_log("===== ALPHA NOTIFICATION - KELAS =====");
                error_log("Student: " . $student->nama . " (ID: " . $student->siswa_id . ")");
                error_log("Mata Pelajaran: " . $namaKelas);
                error_log("Parent Contact: " . json_encode($parentContact));
                
                if (!$parentContact || (empty($parentContact['email']) && empty($parentContact['phone']))) {
                    $notificationStats['no_contact']++;
                    error_log("WARNING: No parent contact info for student " . $student->nama);
                    continue;
                }
                
                // Kirim email notifikasi ke orang tua
                if (!empty($parentContact['email'])) {
                    try {
                        $emailResult = $emailService->sendAlphaNotificationKelas(
                            $parentContact['email'],
                            $student->nama,
                            $namaKelas,
                            $tanggal,
                            $waktu
                        );
                        if ($emailResult) {
                            $notificationStats['email_sent']++;
                            error_log("SUCCESS: Email sent to " . $parentContact['email']);
                        } else {
                            $notificationStats['email_failed']++;
                            error_log("FAILED: Email not sent to " . $parentContact['email']);
                        }
                    } catch (Exception $e) {
                        $notificationStats['email_failed']++;
                        error_log('EXCEPTION: Failed to send alpha notification email: ' . $e->getMessage());
                    }
                } else {
                    error_log("SKIPPED: No email address for student " . $student->nama);
                }
                
                // Kirim WhatsApp notifikasi ke orang tua
                if (!empty($parentContact['phone'])) {
                    try {
                        $whatsappResult = $whatsappService->sendAlphaNotificationKelas(
                            $parentContact['phone'],
                            $student->nama,
                            $namaKelas,
                            $tanggal,
                            $waktu
                        );
                        if ($whatsappResult) {
                            $notificationStats['whatsapp_sent']++;
                            error_log("SUCCESS: WhatsApp sent to " . $parentContact['phone']);
                        } else {
                            $notificationStats['whatsapp_failed']++;
                            error_log("FAILED: WhatsApp not sent to " . $parentContact['phone']);
                        }
                    } catch (Exception $e) {
                        $notificationStats['whatsapp_failed']++;
                        error_log('EXCEPTION: Failed to send alpha notification WhatsApp: ' . $e->getMessage());
                    }
                } else {
                    error_log("SKIPPED: No phone number for student " . $student->nama);
                }
                
                error_log("=======================================");
            } else {
                // Failed to record presensi
                error_log("ERROR: Failed to record presensi for student " . $student->nama . " (ID: " . $student->siswa_id . ")");
            }
        }
        
        // Log final statistics
        error_log("ALPHA NOTIFICATION STATS - KELAS:");
        error_log("  Students marked alpha: $count");
        error_log("  Emails sent: " . $notificationStats['email_sent']);
        error_log("  Emails failed: " . $notificationStats['email_failed']);
        error_log("  WhatsApp sent: " . $notificationStats['whatsapp_sent']);
        error_log("  WhatsApp failed: " . $notificationStats['whatsapp_failed']);
        error_log("  No contact info: " . $notificationStats['no_contact']);
        
        return $count;
    }


}
?>

