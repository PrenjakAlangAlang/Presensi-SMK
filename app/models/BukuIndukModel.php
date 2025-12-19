<?php
// app/models/BukuIndukModel.php
// Menangani data buku induk siswa: biodata dan dokumen pendukung
require_once __DIR__ . '/Database.php';

class BukuIndukModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAll() {
        $this->db->query('SELECT bi.*, u.nama, u.email FROM buku_induk bi INNER JOIN users u ON bi.user_id = u.id ORDER BY u.nama');
        return $this->db->resultSet();
    }

    public function getById($id) {
        $this->db->query('SELECT * FROM buku_induk WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getByUserId($userId) {
        $this->db->query('SELECT * FROM buku_induk WHERE user_id = :uid');
        $this->db->bind(':uid', $userId);
        return $this->db->single();
    }

    public function upsert($data) {
        // Jika record sudah ada untuk user_id -> update, jika belum -> insert
        $existing = $this->getByUserId($data['user_id']);
        if ($existing) {
            return $this->update($existing->id, $data);
        }
        return $this->create($data);
    }

    public function create($data) {
        $this->db->query('INSERT INTO buku_induk (user_id, nama, nis, nisn, tempat_lahir, tanggal_lahir, alamat, dokumen_pdf)
                          VALUES (:user_id, :nama, :nis, :nisn, :tempat_lahir, :tanggal_lahir, :alamat, :dokumen_pdf)');
        $this->bindCommon($data);
        return $this->db->execute();
    }

    public function update($id, $data) {
        $this->db->query('UPDATE buku_induk SET nama = :nama, nis = :nis, nisn = :nisn, tempat_lahir = :tempat_lahir,
                          tanggal_lahir = :tanggal_lahir, alamat = :alamat, dokumen_pdf = :dokumen_pdf
                          WHERE id = :id');
        $this->bindCommon($data);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    private function bindCommon($data) {
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':nama', $data['nama']);
        $this->db->bind(':nis', $data['nis']);
        $this->db->bind(':nisn', $data['nisn']);
        $this->db->bind(':tempat_lahir', $data['tempat_lahir']);
        $this->db->bind(':tanggal_lahir', $data['tanggal_lahir']);
        $this->db->bind(':alamat', $data['alamat']);
        $this->db->bind(':dokumen_pdf', $data['dokumen_pdf'] ?? null);
    }
}
?>
