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

    /**
     * Get kontak orang tua siswa berdasarkan user_id
     * @param int $userId ID siswa
     * @return array|null Data kontak orang tua atau null jika tidak ada
     */
    public function getParentContact($userId) {
        $this->db->query('SELECT no_telp_ortu, email_ortu, nama_ayah, nama_ibu FROM buku_induk WHERE user_id = :uid');
        $this->db->bind(':uid', $userId);
        $result = $this->db->single();
        
        if ($result) {
            return [
                'phone' => $result->no_telp_ortu ?? null,
                'email' => $result->email_ortu ?? null,
                'nama_ayah' => $result->nama_ayah ?? null,
                'nama_ibu' => $result->nama_ibu ?? null
            ];
        }
        
        return null;
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
        $this->db->query('INSERT INTO buku_induk (user_id, nama, nis, nisn, tempat_lahir, tanggal_lahir, alamat, nama_ayah, nama_ibu, no_telp_ortu, email_ortu, dokumen_pdf)
                          VALUES (:user_id, :nama, :nis, :nisn, :tempat_lahir, :tanggal_lahir, :alamat, :nama_ayah, :nama_ibu, :no_telp_ortu, :email_ortu, :dokumen_pdf)');
        $this->db->bind(':user_id', $data['user_id']);
        $this->bindCommon($data);
        return $this->db->execute();
    }

    public function update($id, $data) {
        $this->db->query('UPDATE buku_induk SET nama = :nama, nis = :nis, nisn = :nisn, tempat_lahir = :tempat_lahir,
                          tanggal_lahir = :tanggal_lahir, alamat = :alamat, nama_ayah = :nama_ayah, nama_ibu = :nama_ibu,
                          no_telp_ortu = :no_telp_ortu, email_ortu = :email_ortu, dokumen_pdf = :dokumen_pdf
                          WHERE id = :id');
        $this->bindCommon($data);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    private function bindCommon($data) {
        // Bind hanya field yang ada di kedua query (create dan update)
        $this->db->bind(':nama', $data['nama']);
        $this->db->bind(':nis', $data['nis']);
        $this->db->bind(':nisn', $data['nisn']);
        $this->db->bind(':tempat_lahir', $data['tempat_lahir']);
        $this->db->bind(':tanggal_lahir', $data['tanggal_lahir']);
        $this->db->bind(':alamat', $data['alamat']);
        $this->db->bind(':nama_ayah', $data['nama_ayah'] ?? null);
        $this->db->bind(':nama_ibu', $data['nama_ibu'] ?? null);
        $this->db->bind(':no_telp_ortu', $data['no_telp_ortu'] ?? null);
        $this->db->bind(':email_ortu', $data['email_ortu'] ?? null);
        $this->db->bind(':dokumen_pdf', $data['dokumen_pdf'] ?? null);
    }
}
?>
