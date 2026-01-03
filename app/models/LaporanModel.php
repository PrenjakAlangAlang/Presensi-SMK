<?php
// app/models/LaporanModel.php
// Model untuk menyimpan dan mengambil laporan kemajuan kelas
// Menyimpan catatan guru per tanggal dan mengambil riwayatnya
require_once 'Database.php';

class LaporanModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Save laporan kemajuan for a class
     * Expected data: ['kelas_id' => int, 'guru_id' => int, 'catatan' => string]
     */
    public function saveLaporanKemajuan($data) {
        // Simpan laporan kemajuan untuk kelas (tanggal bisa diberikan atau default hari ini)
    $this->db->query('INSERT INTO laporan_kemajuan (kelas_id, guru_id, tanggal, catatan, created_at) VALUES (:kelas_id, :guru_id, :tanggal, :catatan, NOW())');
    $this->db->bind(':kelas_id', $data['kelas_id']);
    $this->db->bind(':guru_id', $data['guru_id']);
    $this->db->bind(':tanggal', $data['tanggal'] ?? date('Y-m-d'));
    $this->db->bind(':catatan', $data['catatan']);

        if ($this->db->execute()) {
            // kembalikan id record baru jika sukses
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Get laporan kemajuan for a specific class (most recent first)
     */
    public function getLaporanByKelas($kelas_id) {
        // Ambil daftar laporan kemajuan untuk satu kelas, terbaru terlebih dahulu
        $this->db->query('SELECT lk.*, u.nama as guru_nama 
                         FROM laporan_kemajuan lk
                         LEFT JOIN users u ON lk.guru_id = u.id
                         WHERE lk.kelas_id = :kelas_id 
                         ORDER BY lk.created_at DESC');
        $this->db->bind(':kelas_id', $kelas_id);
        return $this->db->resultSet();
    }

    /**
     * Get laporan kemajuan for a specific class with date range filter
     */
    public function getLaporanByKelasWithDateRange($kelas_id, $startDate, $endDate) {
        $this->db->query('SELECT lk.*, u.nama as guru_nama 
                         FROM laporan_kemajuan lk
                         LEFT JOIN users u ON lk.guru_id = u.id
                         WHERE lk.kelas_id = :kelas_id 
                         AND lk.tanggal BETWEEN :start_date AND :end_date
                         ORDER BY lk.tanggal DESC, lk.created_at DESC');
        $this->db->bind(':kelas_id', $kelas_id);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        return $this->db->resultSet();
    }
}

?>
