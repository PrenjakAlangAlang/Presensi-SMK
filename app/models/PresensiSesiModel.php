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

    public function createSession($mata_pelajaran_id, $guru_id) {
        // Buat sesi presensi baru dan kembalikan id jika sukses
        $this->db->query('INSERT INTO presensi_sesi (mata_pelajaran_id, guru_id, waktu_buka, status) VALUES (:mata_pelajaran_id, :guru_id, NOW(), "open")');
        $this->db->bind(':mata_pelajaran_id', $mata_pelajaran_id);
        $this->db->bind(':guru_id', $guru_id);
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function closeSession($mata_pelajaran_id, $guru_id) {
        // Close the active session for the mata pelajaran (if exists)
        // Tutup sesi yang sedang open untuk mata pelajaran tersebut
        $this->db->query('UPDATE presensi_sesi SET waktu_tutup = NOW(), status = "closed" WHERE mata_pelajaran_id = :mata_pelajaran_id AND status = "open"');
        $this->db->bind(':mata_pelajaran_id', $mata_pelajaran_id);
        return $this->db->execute();
    }

    public function getActiveSessionByKelas($mata_pelajaran_id) {
        // Ambil sesi aktif (status open) untuk mata pelajaran tertentu
        $this->db->query('SELECT * FROM presensi_sesi WHERE mata_pelajaran_id = :mata_pelajaran_id AND status = "open" LIMIT 1');
        $this->db->bind(':mata_pelajaran_id', $mata_pelajaran_id);
        return $this->db->single();
    }

    public function isSessionActive($mata_pelajaran_id) {
        // Cek apakah ada sesi aktif
        $s = $this->getActiveSessionByKelas($mata_pelajaran_id);
        return $s ? true : false;
    }

    /**
     * Get all sessions for a mata pelajaran ordered by waktu_buka desc
     */
    public function getSessionsByKelas($mata_pelajaran_id) {
        // Ambil semua sesi untuk mata pelajaran, urut berdasarkan waktu buka desc
        $this->db->query('SELECT * FROM presensi_sesi WHERE mata_pelajaran_id = :mata_pelajaran_id ORDER BY waktu_buka DESC');
        $this->db->bind(':mata_pelajaran_id', $mata_pelajaran_id);
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
