<?php
// app/models/KelasModel.php
// Model untuk operasi terkait kelas dan relasi siswa-kelas
// Menyediakan fungsi CRUD untuk kelas serta manajemen siswa dalam kelas
require_once 'Database.php';

class KelasModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function getAllKelas() {
        // Ambil semua kelas dengan jumlah siswa dan mata pelajaran
        // Siswa dihitung dari mata pelajaran yang ada di kelas (siswa_mata_pelajaran)
        $this->db->query('SELECT k.*, u.nama as wali_kelas_nama, 
                         (SELECT COUNT(DISTINCT smp.siswa_id) 
                          FROM kelas_mata_pelajaran kmp
                          INNER JOIN siswa_mata_pelajaran smp ON kmp.mata_pelajaran_id = smp.mata_pelajaran_id
                          WHERE kmp.kelas_id = k.id) as jumlah_siswa,
                         (SELECT COUNT(*) FROM kelas_mata_pelajaran WHERE kelas_id = k.id) as jumlah_mapel
                         FROM kelas k 
                         LEFT JOIN users u ON k.wali_kelas_id = u.id
                         ORDER BY k.tahun_ajaran DESC, k.nama_kelas ASC');
        return $this->db->resultSet();
    }
    
    public function getKelasById($id) {
        // Ambil detail satu kelas berdasarkan id dengan jumlah siswa dan mata pelajaran
        $this->db->query('SELECT k.*, u.nama as wali_kelas_nama,
                         (SELECT COUNT(DISTINCT smp.siswa_id) 
                          FROM kelas_mata_pelajaran kmp
                          INNER JOIN siswa_mata_pelajaran smp ON kmp.mata_pelajaran_id = smp.mata_pelajaran_id
                          WHERE kmp.kelas_id = k.id) as jumlah_siswa,
                         (SELECT COUNT(*) FROM kelas_mata_pelajaran WHERE kelas_id = k.id) as jumlah_mapel
                         FROM kelas k 
                         LEFT JOIN users u ON k.wali_kelas_id = u.id
                         WHERE k.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    public function createKelas($data) {
        // Buat kelas baru
        $this->db->query('INSERT INTO kelas (nama_kelas, tahun_ajaran, wali_kelas_id) 
                         VALUES (:nama_kelas, :tahun_ajaran, :wali_kelas_id)');
        $this->db->bind(':nama_kelas', $data['nama_kelas']);
        $this->db->bind(':tahun_ajaran', $data['tahun_ajaran']);
        $this->db->bind(':wali_kelas_id', $data['wali_kelas_id'] ?? null);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    public function updateKelas($data) {
        // Perbarui data kelas
        $this->db->query('UPDATE kelas 
                         SET nama_kelas = :nama_kelas, tahun_ajaran = :tahun_ajaran, wali_kelas_id = :wali_kelas_id 
                         WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':nama_kelas', $data['nama_kelas']);
        $this->db->bind(':tahun_ajaran', $data['tahun_ajaran']);
        $this->db->bind(':wali_kelas_id', $data['wali_kelas_id'] ?? null);
        
        return $this->db->execute();
    }
    
    public function deleteKelas($id) {
        // Hapus kelas
        $this->db->query('DELETE FROM kelas WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    /**
     * Get all mata pelajaran in a kelas with guru info
     */
    public function getMataPelajaranInKelas($kelas_id) {
        $this->db->query('SELECT mp.*, u.nama as guru_pengampu_nama, kmp.id as kelas_mapel_id
                         FROM mata_pelajaran mp
                         INNER JOIN kelas_mata_pelajaran kmp ON mp.id = kmp.mata_pelajaran_id
                         LEFT JOIN users u ON mp.guru_pengampu = u.id
                         WHERE kmp.kelas_id = :kelas_id
                         ORDER BY mp.nama_mata_pelajaran');
        $this->db->bind(':kelas_id', $kelas_id);
        return $this->db->resultSet();
    }
    
    /**
     * Get available mata pelajaran (not in specific kelas)
     */
    public function getAvailableMataPelajaran($kelas_id = null) {
        if ($kelas_id) {
            $this->db->query('SELECT mp.*, u.nama as guru_pengampu_nama 
                             FROM mata_pelajaran mp
                             LEFT JOIN users u ON mp.guru_pengampu = u.id
                             WHERE mp.id NOT IN (SELECT mata_pelajaran_id FROM kelas_mata_pelajaran WHERE kelas_id = :kelas_id)
                             ORDER BY mp.nama_mata_pelajaran');
            $this->db->bind(':kelas_id', $kelas_id);
        } else {
            $this->db->query('SELECT mp.*, u.nama as guru_pengampu_nama 
                             FROM mata_pelajaran mp
                             LEFT JOIN users u ON mp.guru_pengampu = u.id
                             ORDER BY mp.nama_mata_pelajaran');
        }
        return $this->db->resultSet();
    }
    
    /**
     * Add mata pelajaran to kelas
     */
    public function addMataPelajaranToKelas($mata_pelajaran_id, $kelas_id) {
        $this->db->query('INSERT INTO kelas_mata_pelajaran (kelas_id, mata_pelajaran_id) 
                         VALUES (:kelas_id, :mata_pelajaran_id)');
        $this->db->bind(':kelas_id', $kelas_id);
        $this->db->bind(':mata_pelajaran_id', $mata_pelajaran_id);
        return $this->db->execute();
    }
    
    /**
     * Remove mata pelajaran from kelas
     */
    public function removeMataPelajaranFromKelas($mata_pelajaran_id, $kelas_id) {
        $this->db->query('DELETE FROM kelas_mata_pelajaran 
                         WHERE mata_pelajaran_id = :mata_pelajaran_id AND kelas_id = :kelas_id');
        $this->db->bind(':mata_pelajaran_id', $mata_pelajaran_id);
        $this->db->bind(':kelas_id', $kelas_id);
        return $this->db->execute();
    }
    
    /**
     * Get all siswa in mata pelajaran within a kelas (DISTINCT siswa)
     * Menampilkan semua siswa unik yang terdaftar di mata pelajaran dalam kelas ini
     */
    public function getSiswaInKelas($kelas_id) {
        $this->db->query('SELECT DISTINCT u.* FROM users u 
                         INNER JOIN siswa_mata_pelajaran smp ON u.id = smp.siswa_id 
                         INNER JOIN kelas_mata_pelajaran kmp ON smp.mata_pelajaran_id = kmp.mata_pelajaran_id
                         WHERE kmp.kelas_id = :kelas_id AND u.role = "siswa"
                         ORDER BY u.nama ASC');
        $this->db->bind(':kelas_id', $kelas_id);
        return $this->db->resultSet();
    }

    /**
     * Get total number of siswa in a kelas (DISTINCT count)
     * Returns integer count
     */
    public function getTotalSiswaByKelas($kelas_id) {
        $this->db->query('SELECT COUNT(DISTINCT smp.siswa_id) as total 
                         FROM kelas_mata_pelajaran kmp
                         INNER JOIN siswa_mata_pelajaran smp ON kmp.mata_pelajaran_id = smp.mata_pelajaran_id
                         WHERE kmp.kelas_id = :kelas_id');
        $this->db->bind(':kelas_id', $kelas_id);
        $row = $this->db->single();
        return $row ? (int)$row->total : 0;
    }
}
?>