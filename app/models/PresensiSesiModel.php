<?php
// app/models/PresensiSesiModel.php
// Model untuk mengelola sesi presensi per kelas
// Fungsi: buat sesi, tutup sesi, cek sesi aktif, ambil daftar sesi
require_once 'Database.php';

class PresensiSesiModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function createSession($kelas_id, $guru_id) {
        // Buat sesi presensi baru dan kembalikan id jika sukses
        $this->db->query('INSERT INTO presensi_sesi (kelas_id, guru_id, waktu_buka, status) VALUES (:kelas_id, :guru_id, NOW(), "open")');
        $this->db->bind(':kelas_id', $kelas_id);
        $this->db->bind(':guru_id', $guru_id);
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function closeSession($kelas_id, $guru_id) {
        // Close the active session for the kelas (if exists)
        // Tutup sesi yang sedang open untuk kelas tersebut
        $this->db->query('UPDATE presensi_sesi SET waktu_tutup = NOW(), status = "closed" WHERE kelas_id = :kelas_id AND status = "open"');
        $this->db->bind(':kelas_id', $kelas_id);
        return $this->db->execute();
    }

    public function getActiveSessionByKelas($kelas_id) {
        // Ambil sesi aktif (status open) untuk kelas tertentu
        $this->db->query('SELECT * FROM presensi_sesi WHERE kelas_id = :kelas_id AND status = "open" LIMIT 1');
        $this->db->bind(':kelas_id', $kelas_id);
        return $this->db->single();
    }

    public function isSessionActive($kelas_id) {
        // Cek apakah ada sesi aktif
        $s = $this->getActiveSessionByKelas($kelas_id);
        return $s ? true : false;
    }

    /**
     * Get all sessions for a kelas ordered by waktu_buka desc
     */
    public function getSessionsByKelas($kelas_id) {
        // Ambil semua sesi untuk kelas, urut berdasarkan waktu buka desc
        $this->db->query('SELECT * FROM presensi_sesi WHERE kelas_id = :kelas_id ORDER BY waktu_buka DESC');
        $this->db->bind(':kelas_id', $kelas_id);
        return $this->db->resultSet();
    }

    /**
     * Get single session by id
     */
    public function getSessionById($id) {
        // Ambil satu sesi berdasarkan id
        $this->db->query('SELECT * FROM presensi_sesi WHERE id = :id LIMIT 1');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
}

?>
