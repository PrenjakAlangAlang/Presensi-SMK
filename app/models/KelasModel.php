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
        // Ambil semua kelas beserta nama wali kelas (jika ada)
        $this->db->query('SELECT k.*, u.nama as wali_kelas_nama 
                         FROM kelas k 
                         LEFT JOIN users u ON k.wali_kelas = u.id 
                         ORDER BY k.nama_kelas');
        return $this->db->resultSet();
    }
    
    public function getKelasById($id) {
        // Ambil detail satu kelas berdasarkan id
        $this->db->query('SELECT k.*, u.nama as wali_kelas_nama 
                         FROM kelas k 
                         LEFT JOIN users u ON k.wali_kelas = u.id 
                         WHERE k.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    public function createKelas($data) {
        // Buat kelas baru
        $this->db->query('INSERT INTO kelas (nama_kelas, tahun_ajaran, wali_kelas) 
                         VALUES (:nama_kelas, :tahun_ajaran, :wali_kelas)');
        $this->db->bind(':nama_kelas', $data['nama_kelas']);
        $this->db->bind(':tahun_ajaran', $data['tahun_ajaran']);
        $this->db->bind(':wali_kelas', $data['wali_kelas']);
        
        return $this->db->execute();
    }
    
    public function updateKelas($data) {
        // Perbarui data kelas
        $this->db->query('UPDATE kelas SET nama_kelas = :nama_kelas, tahun_ajaran = :tahun_ajaran, wali_kelas = :wali_kelas 
                         WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':nama_kelas', $data['nama_kelas']);
        $this->db->bind(':tahun_ajaran', $data['tahun_ajaran']);
        $this->db->bind(':wali_kelas', $data['wali_kelas']);
        
        return $this->db->execute();
    }
    
    public function deleteKelas($id) {
        // Hapus kelas
        $this->db->query('DELETE FROM kelas WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    public function getKelasByGuru($guru_id) {
        // Ambil semua kelas yang wali_kelas-nya adalah guru tertentu
        $this->db->query('SELECT k.* FROM kelas k WHERE k.wali_kelas = :guru_id');
        $this->db->bind(':guru_id', $guru_id);
        return $this->db->resultSet();
    }
    
    public function getSiswaInKelas($kelas_id) {
        // Ambil daftar siswa untuk kelas tertentu
        $this->db->query('SELECT u.* FROM users u 
                         INNER JOIN siswa_kelas sk ON u.id = sk.siswa_id 
                         WHERE sk.kelas_id = :kelas_id AND u.role = "siswa"');
        $this->db->bind(':kelas_id', $kelas_id);
        return $this->db->resultSet();
    }

    /**
     * Get total number of siswa in a kelas
     * Returns integer count
     */
    public function getTotalSiswaByKelas($kelas_id) {
        // Hitung total siswa di suatu kelas
        $this->db->query('SELECT COUNT(*) as total FROM siswa_kelas WHERE kelas_id = :kelas_id');
        $this->db->bind(':kelas_id', $kelas_id);
        $row = $this->db->single();
        return $row ? (int)$row->total : 0;
    }
    
    public function addSiswaToKelas($siswa_id, $kelas_id) {
        // Tambahkan siswa ke kelas (relasi many-to-many sederhana)
        $this->db->query('INSERT INTO siswa_kelas (siswa_id, kelas_id) VALUES (:siswa_id, :kelas_id)');
        $this->db->bind(':siswa_id', $siswa_id);
        $this->db->bind(':kelas_id', $kelas_id);
        return $this->db->execute();
    }
    
    public function removeSiswaFromKelas($siswa_id, $kelas_id) {
        // Hapus relasi siswa-kelas
        $this->db->query('DELETE FROM siswa_kelas WHERE siswa_id = :siswa_id AND kelas_id = :kelas_id');
        $this->db->bind(':siswa_id', $siswa_id);
        $this->db->bind(':kelas_id', $kelas_id);
        return $this->db->execute();
    }

    public function getAvailableSiswa() {
        // Ambil siswa yang belum tergabung di kelas manapun
        $this->db->query('SELECT * FROM users u WHERE u.role = "siswa" AND u.id NOT IN (SELECT siswa_id FROM siswa_kelas) ORDER BY u.nama');
        return $this->db->resultSet();
    }
}
?>