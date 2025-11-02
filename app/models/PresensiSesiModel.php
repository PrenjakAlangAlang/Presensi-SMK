<?php
// app/models/PresensiSesiModel.php
require_once 'Database.php';

class PresensiSesiModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function createSession($kelas_id, $guru_id) {
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
        $this->db->query('UPDATE presensi_sesi SET waktu_tutup = NOW(), status = "closed" WHERE kelas_id = :kelas_id AND status = "open"');
        $this->db->bind(':kelas_id', $kelas_id);
        return $this->db->execute();
    }

    public function getActiveSessionByKelas($kelas_id) {
        $this->db->query('SELECT * FROM presensi_sesi WHERE kelas_id = :kelas_id AND status = "open" LIMIT 1');
        $this->db->bind(':kelas_id', $kelas_id);
        return $this->db->single();
    }

    public function isSessionActive($kelas_id) {
        $s = $this->getActiveSessionByKelas($kelas_id);
        return $s ? true : false;
    }
}

?>
