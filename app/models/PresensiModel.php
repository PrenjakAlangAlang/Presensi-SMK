<?php
// app/models/PresensiModel.php
// Model untuk mencatat dan mengambil data presensi
// - recordPresensiSekolah: mencatat presensi umum
// - recordPresensiKelas: mencatat presensi per kelas (mendukung sesi)
// - hasPresensiInSession: mencegah duplikat presensi per sesi
// - fungsi laporan dan statistik terkait presensi
require_once 'Database.php';

class PresensiModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function recordPresensiSekolah($data) {
        // Support optional reference to presensi_sekolah_sesi (nullable)
        $this->db->query('INSERT INTO presensi_sekolah (presensi_sekolah_sesi_id, user_id, latitude, longitude, jarak, status, jenis) 
                         VALUES (:sesi_id, :user_id, :latitude, :longitude, :jarak, :status, :jenis)');
        $this->db->bind(':sesi_id', $data['presensi_sekolah_sesi_id'] ?? null);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':latitude', $data['latitude']);
        $this->db->bind(':longitude', $data['longitude']);
        $this->db->bind(':jarak', $data['jarak']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':jenis', $data['jenis']);

        return $this->db->execute();
    }
    
    public function recordPresensiKelas($data) {
        // support presensi_sesi_id if provided (nullable)
        $this->db->query('INSERT INTO presensi_kelas (presensi_sesi_id, user_id, kelas_id, latitude, longitude, jarak, status, waktu) 
                         VALUES (:presensi_sesi_id, :user_id, :kelas_id, :latitude, :longitude, :jarak, :status, NOW())');
        $this->db->bind(':presensi_sesi_id', $data['presensi_sesi_id'] ?? null);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':kelas_id', $data['kelas_id']);
        $this->db->bind(':latitude', $data['latitude']);
        $this->db->bind(':longitude', $data['longitude']);
        $this->db->bind(':jarak', $data['jarak']);
        $this->db->bind(':status', $data['status']);

        return $this->db->execute();
    }

    /**
     * Check whether a user already has a presensi_kelas record for a given presensi_sesi
     * Returns true if exists, false otherwise
     */
    public function hasPresensiInSession($user_id, $presensi_sesi_id) {
        if (!$presensi_sesi_id) return false;
        $this->db->query('SELECT id FROM presensi_kelas WHERE user_id = :user_id AND presensi_sesi_id = :sesi_id LIMIT 1');
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':sesi_id', $presensi_sesi_id);
        $row = $this->db->single();
        return $row ? true : false;
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
    
    public function getPresensiKelasByUser($user_id, $limit = null) {
        $sql = 'SELECT pk.*, k.nama_kelas 
                FROM presensi_kelas pk 
                INNER JOIN kelas k ON pk.kelas_id = k.id 
                WHERE pk.user_id = :user_id 
                ORDER BY pk.waktu DESC';
        
        if ($limit) {
            $sql .= ' LIMIT ' . $limit;
        }
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }
    
    public function getPresensiHariIni($user_id) {
        $this->db->query('SELECT * FROM presensi_sekolah 
                         WHERE user_id = :user_id AND DATE(waktu) = CURDATE()');
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }
    
    public function getStatistikKehadiran($user_id) {
        // Statistik kehadiran untuk bulan ini (jumlah hadir, izin, sakit, alpha)
        $this->db->query('SELECT 
                         COUNT(*) as total,
                         SUM(CASE WHEN status = "valid" THEN 1 ELSE 0 END) as hadir,
                         SUM(CASE WHEN jenis = "izin" THEN 1 ELSE 0 END) as izin,
                         SUM(CASE WHEN jenis = "sakit" THEN 1 ELSE 0 END) as sakit,
                         SUM(CASE WHEN jenis = "alpha" THEN 1 ELSE 0 END) as alpha
                         FROM presensi_sekolah 
                         WHERE user_id = :user_id AND MONTH(waktu) = MONTH(CURDATE())');
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }
    
    /**
     * Get presensi laporan for a kelas.
     * If $sesi_id provided, returns attendance for that session (left join so students without presensi are included).
     * Otherwise if $tanggal provided (or default today) returns presensi_kelas for that date.
     * Returns array of objects with siswa data and presensi fields (status, waktu, jarak, latitude, longitude, presensi_sesi_id)
     */
    public function getLaporanPresensiKelas($kelas_id, $tanggal = null, $sesi_id = null) {
        // Base select — list all siswa in kelas and join any matching presensi_kelas
        if ($sesi_id) {
            $sql = 'SELECT u.id as siswa_id, u.nama, pk.status, pk.waktu, pk.jarak, pk.latitude, pk.longitude, pk.presensi_sesi_id, "kelas" as sumber
                    FROM users u
                    LEFT JOIN presensi_kelas pk ON u.id = pk.user_id AND pk.presensi_sesi_id = :sesi_id
                    WHERE u.id IN (SELECT siswa_id FROM siswa_kelas WHERE kelas_id = :kelas_id)
                    AND u.role = "siswa"
                    ORDER BY u.nama';
            $this->db->query($sql);
            $this->db->bind(':sesi_id', $sesi_id);
            $this->db->bind(':kelas_id', $kelas_id);
            return $this->db->resultSet();
        }

        // fallback: use date filter on presensi_kelas (today by default)
        $tanggal = $tanggal ?: date('Y-m-d');
        $sql = 'SELECT u.id as siswa_id, u.nama, pk.status, pk.waktu, pk.jarak, pk.latitude, pk.longitude, pk.presensi_sesi_id, "kelas" as sumber
                FROM users u
                LEFT JOIN presensi_kelas pk ON u.id = pk.user_id AND DATE(pk.waktu) = :tanggal AND pk.kelas_id = :kelas_id
                WHERE u.id IN (SELECT siswa_id FROM siswa_kelas WHERE kelas_id = :kelas_id)
                AND u.role = "siswa"
                ORDER BY u.nama';

        $this->db->query($sql);
        $this->db->bind(':tanggal', $tanggal);
        $this->db->bind(':kelas_id', $kelas_id);
        return $this->db->resultSet();
    }
    
    /**
     * Get laporan presensi sekolah (umum)
     * If $sesi_id provided, returns attendance for that session (left join so students without presensi are included).
     * Otherwise if $tanggal provided (or default today) returns presensi_sekolah for that date.
     * Returns array of objects with siswa data and presensi fields (status, waktu, jarak, latitude, longitude, jenis, presensi_sekolah_sesi_id)
     */
    public function getLaporanPresensiSekolah($tanggal = null, $sesi_id = null, $filter_status = null, $limit = null, $offset = 0) {
        // Base select — list all siswa and join any matching presensi_sekolah
        if ($sesi_id) {
            $sql = 'SELECT u.id as siswa_id, u.nama, u.email, ps.status, ps.waktu, ps.jarak, ps.latitude, ps.longitude, ps.jenis, ps.presensi_sekolah_sesi_id, "sekolah" as sumber
                    FROM users u
                    LEFT JOIN presensi_sekolah ps ON u.id = ps.user_id AND ps.presensi_sekolah_sesi_id = :sesi_id
                    WHERE u.role = "siswa"
                    ORDER BY u.nama';
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
                $sql = 'SELECT u.id as siswa_id, u.nama, u.email, ps.status, ps.waktu, ps.jarak, ps.latitude, ps.longitude, ps.jenis, ps.presensi_sekolah_sesi_id, "sekolah" as sumber
                        FROM users u
                        INNER JOIN presensi_sekolah ps ON u.id = ps.user_id AND DATE(ps.waktu) = :tanggal AND ps.status = "valid"
                        WHERE u.role = "siswa"
                        ORDER BY u.nama';
            } else {
                // Filter berdasarkan jenis (izin, sakit, alpha)
                $sql = 'SELECT u.id as siswa_id, u.nama, u.email, ps.status, ps.waktu, ps.jarak, ps.latitude, ps.longitude, ps.jenis, ps.presensi_sekolah_sesi_id, "sekolah" as sumber
                        FROM users u
                        INNER JOIN presensi_sekolah ps ON u.id = ps.user_id AND DATE(ps.waktu) = :tanggal AND ps.jenis = :jenis
                        WHERE u.role = "siswa"
                        ORDER BY u.nama';
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
            $sql = 'SELECT u.id as siswa_id, u.nama, u.email, ps.status, ps.waktu, ps.jarak, ps.latitude, ps.longitude, ps.jenis, ps.presensi_sekolah_sesi_id, "sekolah" as sumber
                    FROM users u
                    LEFT JOIN presensi_sekolah ps ON u.id = ps.user_id AND DATE(ps.waktu) = :tanggal
                    WHERE u.role = "siswa"
                    ORDER BY u.nama';
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

    /**
     * Count total laporan presensi sekolah
     */
    public function countLaporanPresensiSekolah($tanggal = null, $sesi_id = null, $filter_status = null) {
        if ($sesi_id) {
            $sql = 'SELECT COUNT(u.id) as total FROM users u WHERE u.role = "siswa"';
            $this->db->query($sql);
            $result = $this->db->single();
            return $result->total ?? 0;
        }

        $tanggal = $tanggal ?: date('Y-m-d');
        
        if ($filter_status) {
            if ($filter_status == 'valid') {
                $sql = 'SELECT COUNT(u.id) as total FROM users u
                        INNER JOIN presensi_sekolah ps ON u.id = ps.user_id AND DATE(ps.waktu) = :tanggal AND ps.status = "valid"
                        WHERE u.role = "siswa"';
                $this->db->query($sql);
                $this->db->bind(':tanggal', $tanggal);
            } else {
                $sql = 'SELECT COUNT(u.id) as total FROM users u
                        INNER JOIN presensi_sekolah ps ON u.id = ps.user_id AND DATE(ps.waktu) = :tanggal AND ps.jenis = :jenis
                        WHERE u.role = "siswa"';
                $this->db->query($sql);
                $this->db->bind(':tanggal', $tanggal);
                $this->db->bind(':jenis', $filter_status);
            }
        } else {
            $sql = 'SELECT COUNT(u.id) as total FROM users u WHERE u.role = "siswa"';
            $this->db->query($sql);
        }
        
        $result = $this->db->single();
        return $result->total ?? 0;
    }
    
    /**
     * Get statistik presensi sekolah untuk periode tertentu
     * Returns count of hadir, izin, sakit, alpha, and total siswa
     */
    public function getStatistikPresensiSekolah($tanggal = null, $bulan = null, $tahun = null) {
        if ($tanggal) {
            // Statistik untuk tanggal tertentu
            $sql = 'SELECT 
                    COUNT(DISTINCT u.id) as total_siswa,
                    COUNT(DISTINCT CASE WHEN ps.status = "valid" AND ps.jenis = "hadir" THEN ps.user_id END) as hadir,
                    COUNT(DISTINCT CASE WHEN ps.jenis = "izin" THEN ps.user_id END) as izin,
                    COUNT(DISTINCT CASE WHEN ps.jenis = "sakit" THEN ps.user_id END) as sakit,
                    COUNT(DISTINCT CASE WHEN ps.jenis = "alpha" THEN ps.user_id END) as alpha
                    FROM users u
                    LEFT JOIN presensi_sekolah ps ON u.id = ps.user_id AND DATE(ps.waktu) = :tanggal
                    WHERE u.role = "siswa"';
            $this->db->query($sql);
            $this->db->bind(':tanggal', $tanggal);
        } elseif ($bulan && $tahun) {
            // Statistik untuk bulan tertentu
            $sql = 'SELECT 
                    COUNT(DISTINCT u.id) as total_siswa,
                    COUNT(DISTINCT CASE WHEN ps.status = "valid" AND ps.jenis = "hadir" THEN ps.user_id END) as hadir,
                    COUNT(DISTINCT CASE WHEN ps.jenis = "izin" THEN ps.user_id END) as izin,
                    COUNT(DISTINCT CASE WHEN ps.jenis = "sakit" THEN ps.user_id END) as sakit,
                    COUNT(DISTINCT CASE WHEN ps.jenis = "alpha" THEN ps.user_id END) as alpha
                    FROM users u
                    LEFT JOIN presensi_sekolah ps ON u.id = ps.user_id AND MONTH(ps.waktu) = :bulan AND YEAR(ps.waktu) = :tahun
                    WHERE u.role = "siswa"';
            $this->db->query($sql);
            $this->db->bind(':bulan', $bulan);
            $this->db->bind(':tahun', $tahun);
        } else {
            // Default: statistik hari ini
            $sql = 'SELECT 
                    COUNT(DISTINCT u.id) as total_siswa,
                    COUNT(DISTINCT CASE WHEN ps.status = "valid" AND ps.jenis = "hadir" THEN ps.user_id END) as hadir,
                    COUNT(DISTINCT CASE WHEN ps.jenis = "izin" THEN ps.user_id END) as izin,
                    COUNT(DISTINCT CASE WHEN ps.jenis = "sakit" THEN ps.user_id END) as sakit,
                    COUNT(DISTINCT CASE WHEN ps.jenis = "alpha" THEN ps.user_id END) as alpha
                    FROM users u
                    LEFT JOIN presensi_sekolah ps ON u.id = ps.user_id AND DATE(ps.waktu) = CURDATE()
                    WHERE u.role = "siswa"';
            $this->db->query($sql);
        }
        
        return $this->db->single();
    }
    
    public function ajukanIzin($data) {
        $this->db->query('INSERT INTO izin_siswa (siswa_id, tanggal, jenis_izin, alasan, foto_bukti,  waktu_pengajuan) 
                         VALUES (:siswa_id, :tanggal, :jenis_izin, :alasan, :foto_bukti,  NOW())');
        $this->db->bind(':siswa_id', $data['siswa_id']);
        $this->db->bind(':tanggal', $data['tanggal']);
        $this->db->bind(':jenis_izin', $data['jenis_izin'] ?? 'izin');
        $this->db->bind(':alasan', $data['alasan']);
        $this->db->bind(':foto_bukti', $data['foto_bukti'] ?? null);
        
        return $this->db->execute();
    }
    
    public function getIzinBySiswa($siswa_id) {
        $this->db->query('SELECT * FROM izin_siswa WHERE siswa_id = :siswa_id ORDER BY tanggal DESC');
        $this->db->bind(':siswa_id', $siswa_id);
        return $this->db->resultSet();
    }

    /**
     * Check whether a user already has a presensi_sekolah record for a given presensi_sekolah_sesi
     * Returns true if exists, false otherwise
     */
    public function hasPresensiInSchoolSession($user_id, $presensi_sekolah_sesi_id) {
        if (!$presensi_sekolah_sesi_id) return false;
        $this->db->query('SELECT id FROM presensi_sekolah WHERE user_id = :user_id AND presensi_sekolah_sesi_id = :sesi_id LIMIT 1');
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':sesi_id', $presensi_sekolah_sesi_id);
        $row = $this->db->single();
        return $row ? true : false;
    }

    /**
     * Get list of dates that have presensi sekolah records
     * Returns array of unique dates in DESC order
     */
    public function getTanggalPresensiSekolah() {
        $this->db->query('SELECT DISTINCT DATE(waktu) as tanggal FROM presensi_sekolah ORDER BY tanggal DESC');
        return $this->db->resultSet();
    }
}
?>
