<?php
// app/models/PresensiModel.php
require_once 'Database.php';

class PresensiModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function recordPresensiSekolah($data) {
        $this->db->query('INSERT INTO presensi_sekolah (user_id, latitude, longitude, jarak, status, jenis) 
                         VALUES (:user_id, :latitude, :longitude, :jarak, :status, :jenis)');
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
    
    public function getLaporanPresensiKelas($kelas_id, $tanggal = null) {
        $sql = 'SELECT u.nama, ps.waktu, ps.status, ps.jenis 
                FROM users u 
                LEFT JOIN presensi_sekolah ps ON u.id = ps.user_id AND DATE(ps.waktu) = :tanggal 
                WHERE u.id IN (SELECT siswa_id FROM siswa_kelas WHERE kelas_id = :kelas_id) 
                AND u.role = "siswa"';
        
        $this->db->query($sql);
        $this->db->bind(':kelas_id', $kelas_id);
        $this->db->bind(':tanggal', $tanggal ?: date('Y-m-d'));
        return $this->db->resultSet();
    }
    
    public function ajukanIzin($data) {
        $this->db->query('INSERT INTO izin_siswa (siswa_id, tanggal, alasan, status) 
                         VALUES (:siswa_id, :tanggal, :alasan, "pending")');
        $this->db->bind(':siswa_id', $data['siswa_id']);
        $this->db->bind(':tanggal', $data['tanggal']);
        $this->db->bind(':alasan', $data['alasan']);
        
        return $this->db->execute();
    }
    
    public function getIzinBySiswa($siswa_id) {
        $this->db->query('SELECT * FROM izin_siswa WHERE siswa_id = :siswa_id ORDER BY tanggal DESC');
        $this->db->bind(':siswa_id', $siswa_id);
        return $this->db->resultSet();
    }
}
?>
