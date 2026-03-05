<?php
// app/models/MataPelajaranModel.php
// Model untuk operasi terkait mata pelajaran dan relasi siswa-mata pelajaran
// Menyediakan fungsi CRUD untuk mata pelajaran serta manajemen siswa dalam mata pelajaran
require_once 'Database.php';

class MataPelajaranModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function getAllMataPelajaran() {
        // Ambil semua mata pelajaran beserta nama guru pengampu (jika ada)
        $this->db->query('SELECT mp.*, u.nama as guru_pengampu_nama 
                         FROM mata_pelajaran mp 
                         LEFT JOIN users u ON mp.guru_pengampu = u.id 
                         ORDER BY mp.nama_mata_pelajaran');
        return $this->db->resultSet();
    }
    
    public function getMataPelajaranById($id) {
        // Ambil detail satu mata pelajaran berdasarkan id
        $this->db->query('SELECT mp.*, u.nama as guru_pengampu_nama 
                         FROM mata_pelajaran mp 
                         LEFT JOIN users u ON mp.guru_pengampu = u.id 
                         WHERE mp.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    public function createMataPelajaran($data) {
        // Buat mata pelajaran baru
        $this->db->query('INSERT INTO mata_pelajaran (nama_mata_pelajaran, guru_pengampu, jadwal) 
                         VALUES (:nama_mata_pelajaran, :guru_pengampu, :jadwal)');
        $this->db->bind(':nama_mata_pelajaran', $data['nama_mata_pelajaran']);
        $this->db->bind(':guru_pengampu', $data['guru_pengampu']);
        $this->db->bind(':jadwal', $data['jadwal'] ?? null);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    public function updateMataPelajaran($data) {
        // Perbarui data mata pelajaran
        $this->db->query('UPDATE mata_pelajaran 
                         SET nama_mata_pelajaran = :nama_mata_pelajaran, 
                             guru_pengampu = :guru_pengampu, 
                             jadwal = :jadwal 
                         WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':nama_mata_pelajaran', $data['nama_mata_pelajaran']);
        $this->db->bind(':guru_pengampu', $data['guru_pengampu']);
        $this->db->bind(':jadwal', $data['jadwal'] ?? null);
        
        return $this->db->execute();
    }
    
    public function deleteMataPelajaran($id) {
        // Hapus mata pelajaran
        $this->db->query('DELETE FROM mata_pelajaran WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    public function getMataPelajaranByGuru($guru_id) {
        // Ambil semua mata pelajaran yang guru_pengampu-nya adalah guru tertentu
        // dengan informasi kelas yang terkait
        // Setiap kombinasi mata_pelajaran-kelas akan menjadi row terpisah
        $this->db->query('SELECT mp.*, 
                         kmp.kelas_id, 
                         kmp.id as kelas_mapel_id,
                         k.nama_kelas, 
                         k.tahun_ajaran,
                         mp.id as mata_pelajaran_id
                         FROM mata_pelajaran mp
                         LEFT JOIN kelas_mata_pelajaran kmp ON mp.id = kmp.mata_pelajaran_id
                         LEFT JOIN kelas k ON kmp.kelas_id = k.id
                         WHERE mp.guru_pengampu = :guru_id
                         ORDER BY k.nama_kelas, mp.nama_mata_pelajaran');
        $this->db->bind(':guru_id', $guru_id);
        return $this->db->resultSet();
    }
    
    public function getSiswaInMataPelajaran($mata_pelajaran_id) {
        // Ambil daftar siswa untuk mata pelajaran tertentu
        $this->db->query('SELECT u.* FROM users u 
                         INNER JOIN siswa_mata_pelajaran smp ON u.id = smp.siswa_id 
                         WHERE smp.mata_pelajaran_id = :mata_pelajaran_id AND u.role = "siswa"');
        $this->db->bind(':mata_pelajaran_id', $mata_pelajaran_id);
        return $this->db->resultSet();
    }

    /**
     * Get total number of siswa in a mata pelajaran
     * Returns integer count
     */
    public function getTotalSiswaByMataPelajaran($mata_pelajaran_id) {
        // Hitung total siswa di suatu mata pelajaran
        $this->db->query('SELECT COUNT(*) as total FROM siswa_mata_pelajaran WHERE mata_pelajaran_id = :mata_pelajaran_id');
        $this->db->bind(':mata_pelajaran_id', $mata_pelajaran_id);
        $row = $this->db->single();
        return $row ? (int)$row->total : 0;
    }
    
    public function addSiswaToMataPelajaran($siswa_id, $mata_pelajaran_id) {
        // Tambahkan siswa ke mata pelajaran (relasi many-to-many sederhana)
        $this->db->query('INSERT INTO siswa_mata_pelajaran (siswa_id, mata_pelajaran_id) VALUES (:siswa_id, :mata_pelajaran_id)');
        $this->db->bind(':siswa_id', $siswa_id);
        $this->db->bind(':mata_pelajaran_id', $mata_pelajaran_id);
        return $this->db->execute();
    }
    
    public function removeSiswaFromMataPelajaran($siswa_id, $mata_pelajaran_id) {
        // Hapus relasi siswa-mata pelajaran
        $this->db->query('DELETE FROM siswa_mata_pelajaran WHERE siswa_id = :siswa_id AND mata_pelajaran_id = :mata_pelajaran_id');
        $this->db->bind(':siswa_id', $siswa_id);
        $this->db->bind(':mata_pelajaran_id', $mata_pelajaran_id);
        return $this->db->execute();
    }

    public function getAvailableSiswa($mata_pelajaran_id = null) {
        // Ambil semua siswa, kecuali yang sudah ada di mata pelajaran tertentu
        if ($mata_pelajaran_id) {
            $this->db->query('SELECT * FROM users u 
                             WHERE u.role = "siswa" 
                             AND u.id NOT IN (SELECT siswa_id FROM siswa_mata_pelajaran WHERE mata_pelajaran_id = :mata_pelajaran_id)
                             ORDER BY u.nama');
            $this->db->bind(':mata_pelajaran_id', $mata_pelajaran_id);
        } else {
            $this->db->query('SELECT * FROM users u WHERE u.role = "siswa" ORDER BY u.nama');
        }
        return $this->db->resultSet();
    }
    
    public function getMataPelajaranBySiswa($siswa_id) {
        // Ambil semua mata pelajaran yang diikuti oleh siswa tertentu
        // Siswa terhubung langsung ke mata pelajaran melalui siswa_mata_pelajaran
        $this->db->query('SELECT mp.*, 
                         u.nama as guru_pengampu_nama, 
                         k.nama_kelas, 
                         k.tahun_ajaran, 
                         wali.nama as wali_kelas_nama,
                         kmp.id as kelas_mapel_id,
                         mp.id as mata_pelajaran_id
                         FROM mata_pelajaran mp
                         INNER JOIN siswa_mata_pelajaran smp ON mp.id = smp.mata_pelajaran_id
                         LEFT JOIN kelas_mata_pelajaran kmp ON mp.id = kmp.mata_pelajaran_id
                         LEFT JOIN kelas k ON kmp.kelas_id = k.id
                         LEFT JOIN users u ON mp.guru_pengampu = u.id
                         LEFT JOIN users wali ON k.wali_kelas_id = wali.id
                         WHERE smp.siswa_id = :siswa_id
                         ORDER BY k.nama_kelas, mp.nama_mata_pelajaran');
        $this->db->bind(':siswa_id', $siswa_id);
        return $this->db->resultSet();
    }
    
    public function getAllMataPelajaranWithKelas() {
        // Ambil semua mata pelajaran beserta informasi kelas untuk filter laporan
        // Setiap kombinasi mata_pelajaran-kelas akan menjadi row terpisah
        $this->db->query('SELECT mp.*, 
                         u.nama as guru_pengampu_nama, 
                         k.nama_kelas, 
                         k.tahun_ajaran,
                         kmp.kelas_id,
                         kmp.id as kelas_mapel_id,
                         mp.id as mata_pelajaran_id
                         FROM mata_pelajaran mp
                         LEFT JOIN users u ON mp.guru_pengampu = u.id
                         LEFT JOIN kelas_mata_pelajaran kmp ON mp.id = kmp.mata_pelajaran_id
                         LEFT JOIN kelas k ON kmp.kelas_id = k.id
                         ORDER BY k.nama_kelas, mp.nama_mata_pelajaran');
        return $this->db->resultSet();
    }
}
?>
