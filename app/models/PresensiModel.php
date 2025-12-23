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
        // support presensi_sesi_id if provided (nullable)
        $this->db->query('INSERT INTO presensi_kelas (presensi_sesi_id, user_id, kelas_id, latitude, longitude, jarak, status, jenis, alasan, foto_bukti, waktu) 
                         VALUES (:presensi_sesi_id, :user_id, :kelas_id, :latitude, :longitude, :jarak, :status, :jenis, :alasan, :foto_bukti, NOW())');
        $this->db->bind(':presensi_sesi_id', $data['presensi_sesi_id'] ?? null);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':kelas_id', $data['kelas_id']);
        $this->db->bind(':latitude', $data['latitude']);
        $this->db->bind(':longitude', $data['longitude']);
        $this->db->bind(':jarak', $data['jarak']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':jenis', $data['jenis'] ?? 'hadir');
        $this->db->bind(':alasan', $data['alasan'] ?? null);
        $this->db->bind(':foto_bukti', $data['foto_bukti'] ?? null);

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
            $sql = 'SELECT u.id as siswa_id, u.nama, pk.status, pk.waktu, pk.jarak, pk.latitude, pk.longitude, pk.presensi_sesi_id, pk.jenis, pk.alasan, pk.foto_bukti, "kelas" as sumber
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
        $sql = 'SELECT u.id as siswa_id, u.nama, pk.status, pk.waktu, pk.jarak, pk.latitude, pk.longitude, pk.presensi_sesi_id, pk.jenis, pk.alasan, pk.foto_bukti, "kelas" as sumber
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
            $sql = 'SELECT u.id as siswa_id, u.nama, u.email, ps.status, ps.waktu, ps.jarak, ps.latitude, ps.longitude, ps.jenis, ps.alasan, ps.foto_bukti, ps.presensi_sekolah_sesi_id, "sekolah" as sumber
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
                $sql = 'SELECT u.id as siswa_id, u.nama, u.email, ps.status, ps.waktu, ps.jarak, ps.latitude, ps.longitude, ps.jenis, ps.alasan, ps.foto_bukti, ps.presensi_sekolah_sesi_id, "sekolah" as sumber
                        FROM users u
                        INNER JOIN presensi_sekolah ps ON u.id = ps.user_id AND DATE(ps.waktu) = :tanggal AND ps.status = "valid"
                        WHERE u.role = "siswa"
                        ORDER BY u.nama';
            } else {
                // Filter berdasarkan jenis (izin, sakit, alpha)
                $sql = 'SELECT u.id as siswa_id, u.nama, u.email, ps.status, ps.waktu, ps.jarak, ps.latitude, ps.longitude, ps.jenis, ps.alasan, ps.foto_bukti, ps.presensi_sekolah_sesi_id, "sekolah" as sumber
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
            $sql = 'SELECT u.id as siswa_id, u.nama, u.email, ps.status, ps.waktu, ps.jarak, ps.latitude, ps.longitude, ps.jenis, ps.alasan, ps.foto_bukti, ps.presensi_sekolah_sesi_id, "sekolah" as sumber
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

    /**
     * Check if a student has izin on a specific date
     * Returns the izin record if exists, false otherwise
     */
    public function hasIzinOnDate($siswa_id, $tanggal) {
        $this->db->query('SELECT * FROM izin_siswa WHERE siswa_id = :siswa_id AND tanggal = :tanggal LIMIT 1');
        $this->db->bind(':siswa_id', $siswa_id);
        $this->db->bind(':tanggal', $tanggal);
        $row = $this->db->single();
        return $row ? $row : false;
    }

    /**
     * Create presensi_sekolah record for izin
     * Automatically called when izin is approved/submitted
     */
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

    /**
     * Create presensi_kelas records for izin for all classes the student is enrolled in
     * If sesi_kelas_id is null, will not link to any session
     */
    public function createPresensiKelasIzin($siswa_id, $tanggal, $jenis_izin, $alasan = null, $foto_bukti = null, $waktu_pengajuan = null, $sesi_kelas_id = null) {
        // Get all classes the student is enrolled in
        $this->db->query('SELECT kelas_id FROM siswa_kelas WHERE siswa_id = :siswa_id');
        $this->db->bind(':siswa_id', $siswa_id);
        $kelas_list = $this->db->resultSet();

        // Use waktu_pengajuan if provided, otherwise use tanggal + 00:00:00
        $waktu_insert = $waktu_pengajuan ?: ($tanggal . ' 00:00:00');

        foreach ($kelas_list as $kelas) {
            // Check if presensi already exists for this class and date
            $this->db->query('SELECT id FROM presensi_kelas WHERE user_id = :user_id AND kelas_id = :kelas_id AND DATE(waktu) = :tanggal LIMIT 1');
            $this->db->bind(':user_id', $siswa_id);
            $this->db->bind(':kelas_id', $kelas->kelas_id);
            $this->db->bind(':tanggal', $tanggal);
            $exists = $this->db->single();
            
            if (!$exists) {
                // Insert izin record to presensi_kelas
                $this->db->query('INSERT INTO presensi_kelas (presensi_sesi_id, user_id, kelas_id, latitude, longitude, jarak, status, jenis, alasan, foto_bukti, waktu) 
                                 VALUES (:sesi_id, :user_id, :kelas_id, 0, 0, 0, "valid", :jenis, :alasan, :foto_bukti, :waktu)');
                $this->db->bind(':sesi_id', $sesi_kelas_id);
                $this->db->bind(':user_id', $siswa_id);
                $this->db->bind(':kelas_id', $kelas->kelas_id);
                $this->db->bind(':jenis', $jenis_izin); // Store jenis_izin in jenis field (izin, sakit, etc)
                $this->db->bind(':alasan', $alasan);
                $this->db->bind(':foto_bukti', $foto_bukti);
                $this->db->bind(':waktu', $waktu_insert);
                $this->db->execute();
            }
        }
        return true;
    }

    /**
     * Create presensi_kelas record for izin for a single specific class
     */
    public function createPresensiKelasSingleIzin($siswa_id, $kelas_id, $tanggal, $jenis_izin, $alasan = null, $foto_bukti = null, $waktu_pengajuan = null, $sesi_kelas_id = null) {
        // Check if presensi already exists for this class and date
        $this->db->query('SELECT id FROM presensi_kelas WHERE user_id = :user_id AND kelas_id = :kelas_id AND DATE(waktu) = :tanggal LIMIT 1');
        $this->db->bind(':user_id', $siswa_id);
        $this->db->bind(':kelas_id', $kelas_id);
        $this->db->bind(':tanggal', $tanggal);
        $exists = $this->db->single();
        
        if ($exists) {
            return true; // Already exists
        }

        // Use waktu_pengajuan if provided, otherwise use tanggal + 00:00:00
        $waktu_insert = $waktu_pengajuan ?: ($tanggal . ' 00:00:00');

        // Insert izin record to presensi_kelas
        $this->db->query('INSERT INTO presensi_kelas (presensi_sesi_id, user_id, kelas_id, latitude, longitude, jarak, status, jenis, alasan, foto_bukti, waktu) 
                         VALUES (:sesi_id, :user_id, :kelas_id, 0, 0, 0, "valid", :jenis, :alasan, :foto_bukti, :waktu)');
        $this->db->bind(':sesi_id', $sesi_kelas_id);
        $this->db->bind(':user_id', $siswa_id);
        $this->db->bind(':kelas_id', $kelas_id);
        $this->db->bind(':jenis', $jenis_izin);
        $this->db->bind(':alasan', $alasan);
        $this->db->bind(':foto_bukti', $foto_bukti);
        $this->db->bind(':waktu', $waktu_insert);
        return $this->db->execute();
    }

    /**
     * Update presensi kelas status
     * Used by teachers to modify student attendance status
     */
    public function updatePresensiKelas($siswa_id, $kelas_id, $jenis, $alasan = null, $foto_bukti = null, $sesi_id = null) {
        // Build WHERE clause based on whether sesi_id is provided
        if ($sesi_id) {
            // Update specific session
            $sql = 'UPDATE presensi_kelas 
                    SET jenis = :jenis, alasan = :alasan, foto_bukti = :foto_bukti, status = :status,
                        latitude = :latitude, longitude = :longitude, jarak = :jarak
                    WHERE user_id = :user_id AND kelas_id = :kelas_id AND presensi_sesi_id = :sesi_id';
        } else {
            // Update today's attendance
            $sql = 'UPDATE presensi_kelas 
                    SET jenis = :jenis, alasan = :alasan, foto_bukti = :foto_bukti, status = :status,
                        latitude = :latitude, longitude = :longitude, jarak = :jarak
                    WHERE user_id = :user_id AND kelas_id = :kelas_id AND DATE(waktu) = CURDATE()';
        }
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $siswa_id);
        $this->db->bind(':kelas_id', $kelas_id);
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

    /**
     * Create presensi kelas record if it doesn't exist
     * Used when teacher creates attendance record for student who hasn't checked in
     */
    public function createOrUpdatePresensiKelas($siswa_id, $kelas_id, $jenis, $alasan = null, $foto_bukti = null, $sesi_id = null) {
        // Check if record exists
        if ($sesi_id) {
            $this->db->query('SELECT id FROM presensi_kelas WHERE user_id = :user_id AND kelas_id = :kelas_id AND presensi_sesi_id = :sesi_id LIMIT 1');
            $this->db->bind(':user_id', $siswa_id);
            $this->db->bind(':kelas_id', $kelas_id);
            $this->db->bind(':sesi_id', $sesi_id);
        } else {
            $this->db->query('SELECT id FROM presensi_kelas WHERE user_id = :user_id AND kelas_id = :kelas_id AND DATE(waktu) = CURDATE() LIMIT 1');
            $this->db->bind(':user_id', $siswa_id);
            $this->db->bind(':kelas_id', $kelas_id);
        }
        
        $exists = $this->db->single();
        
        if ($exists) {
            // Update existing record
            return $this->updatePresensiKelas($siswa_id, $kelas_id, $jenis, $alasan, $foto_bukti, $sesi_id);
        } else {
            // Create new record
            $data = [
                'presensi_sesi_id' => $sesi_id,
                'user_id' => $siswa_id,
                'kelas_id' => $kelas_id,
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

    /**
     * Update presensi sekolah status
     * Used by admin to modify student attendance status
     */
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

    /**
     * Create or update presensi sekolah record
     * Used when admin creates attendance record for student who hasn't checked in
     */
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

    /**
     * Mark all students who haven't checked in for a school session as alpha
     * Called when school session is closed
     */
    public function markAbsentStudentsAsAlphaSekolah($sesi_id) {
        // Get all students who haven't checked in for this session
        $this->db->query('
            SELECT u.id 
            FROM users u 
            WHERE u.role = "siswa" 
            AND u.id NOT IN (
                SELECT user_id 
                FROM presensi_sekolah 
                WHERE presensi_sekolah_sesi_id = :sesi_id
            )
        ');
        $this->db->bind(':sesi_id', $sesi_id);
        $absentStudents = $this->db->resultSet();
        
        $count = 0;
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
            }
        }
        
        return $count;
    }

    /**
     * Mark all students in a class who haven't checked in for a session as alpha
     * Called when class session is closed
     */
    public function markAbsentStudentsAsAlphaKelas($kelas_id, $sesi_id) {
        // Get all students in this class who haven't checked in for this session
        $this->db->query('
            SELECT sk.siswa_id 
            FROM siswa_kelas sk
            WHERE sk.kelas_id = :kelas_id
            AND sk.siswa_id NOT IN (
                SELECT user_id 
                FROM presensi_kelas 
                WHERE kelas_id = :kelas_id 
                AND presensi_sesi_id = :sesi_id
            )
        ');
        $this->db->bind(':kelas_id', $kelas_id);
        $this->db->bind(':sesi_id', $sesi_id);
        $absentStudents = $this->db->resultSet();
        
        $count = 0;
        foreach ($absentStudents as $student) {
            $data = [
                'presensi_sesi_id' => $sesi_id,
                'user_id' => $student->siswa_id,
                'kelas_id' => $kelas_id,
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
            }
        }
        
        return $count;
    }
}
?>
