<?php

require_once __DIR__ . '/Database.php';

class BukuIndukModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAll() {
        $this->db->query('SELECT bi.*, bi.nama as user_nama, COALESCE(bi.email, "") AS email,
                          COALESCE(k.nama_kelas, bi.kelas) as kelas,
                          COALESCE(k.jurusan, bi.jurusan) as jurusan,
                          bi.kelas as kelas_lama, bi.jurusan as jurusan_lama
                          FROM buku_induk bi
                          LEFT JOIN kelas k ON bi.kelas_id = k.id
                          ORDER BY bi.nama');
        return $this->db->resultSet();
    }

    public function getById($id) {
        $this->db->query('SELECT bi.*, COALESCE(k.nama_kelas, bi.kelas) as kelas,
                          COALESCE(k.jurusan, bi.jurusan) as jurusan
                          FROM buku_induk bi
                          LEFT JOIN kelas k ON bi.kelas_id = k.id
                          WHERE bi.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getByUserId($userId) {
        $this->db->query('SELECT bi.*, COALESCE(k.nama_kelas, bi.kelas) as kelas,
                          COALESCE(k.jurusan, bi.jurusan) as jurusan
                          FROM buku_induk bi
                          LEFT JOIN kelas k ON bi.kelas_id = k.id
                          WHERE bi.id = :uid ORDER BY bi.id DESC LIMIT 1');
        $this->db->bind(':uid', $userId);
        return $this->db->single();
    }

    public function getByNipd($nipd) {
        $this->db->query('SELECT * FROM buku_induk WHERE nipd = :nipd ORDER BY id DESC LIMIT 1');
        $this->db->bind(':nipd', $nipd);
        return $this->db->single();
    }

    public function getByEmail($email) {
        $this->db->query('SELECT * FROM buku_induk WHERE email = :email ORDER BY id DESC LIMIT 1');
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    /**
     * Get kontak orang tua siswa berdasarkan user_id
     * @param int $userId ID siswa
     * @return array|null Data kontak orang tua atau null jika tidak ada
     */
    public function getParentContact($userId) {
        $this->db->query('SELECT no_telp_ortu, email_ortu, nama_ayah, nama_ibu FROM buku_induk WHERE id = :uid ORDER BY id DESC LIMIT 1');
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

    public function getDokumen($bukuIndukId) {
        return [];
    }

    public function addDokumen($data) {
        return false;
    }

    public function deleteDokumen($id) {
        return false;
    }

    public function getDokumenById($id) {
        return null;
    }

    public function upsert($data) {
        $existing = null;
        if (!empty($data['id'])) {
            $existing = $this->getById($data['id']);
        }
        if (!$existing && !empty($data['nipd'])) {
            $existing = $this->getByNipd($data['nipd']);
        }

        if ($existing) {
            return $this->update($existing->id, $data);
        }
        return $this->create($data);
    }

    public function create($data) {
        $data['password_hash'] = $data['password_hash'] ?? $this->makePasswordHash($data['password'] ?? null);
        $data['email'] = $data['email'] ?? $this->generateStudentEmail($data['nipd'] ?? '');
        $this->db->query('INSERT INTO buku_induk (nama, nipd, email, nisn, kelas_id, kelas, jurusan, tanggal_diterima, agama, tempat_lahir, tanggal_lahir, alamat, nama_ayah, nama_ibu, nama_wali, no_telp_ortu, email_ortu, dokumen_ijasah, dokumen_pas_foto, dokumen_akta_kelahiran, dokumen_kk, password)
                          VALUES (:nama, :nipd, :email, :nisn, :kelas_id, :kelas, :jurusan, :tanggal_diterima, :agama, :tempat_lahir, :tanggal_lahir, :alamat, :nama_ayah, :nama_ibu, :nama_wali, :no_telp_ortu, :email_ortu, :dokumen_ijasah, :dokumen_pas_foto, :dokumen_akta_kelahiran, :dokumen_kk, :password)');
        $this->bindCommon($data);
        $this->db->bind(':password', $data['password_hash']);
        return $this->db->execute();
    }

    public function update($id, $data) {
        $passwordHash = $data['password_hash'] ?? $this->makePasswordHash($data['password'] ?? null);
        if ($passwordHash) {
            $this->db->query('UPDATE buku_induk SET nama = :nama, nipd = :nipd, email = :email, nisn = :nisn,
                              kelas_id = :kelas_id, kelas = :kelas, jurusan = :jurusan, tanggal_diterima = :tanggal_diterima, agama = :agama,
                              tempat_lahir = :tempat_lahir,
                              tanggal_lahir = :tanggal_lahir, alamat = :alamat, nama_ayah = :nama_ayah, nama_ibu = :nama_ibu,
                              nama_wali = :nama_wali, no_telp_ortu = :no_telp_ortu, email_ortu = :email_ortu,
                              dokumen_ijasah = :dokumen_ijasah, dokumen_pas_foto = :dokumen_pas_foto,
                              dokumen_akta_kelahiran = :dokumen_akta_kelahiran, dokumen_kk = :dokumen_kk,
                              password = :password
                              WHERE id = :id');
            $this->db->bind(':password', $passwordHash);
        } else {
            $this->db->query('UPDATE buku_induk SET nama = :nama, nipd = :nipd, email = :email, nisn = :nisn,
                              kelas_id = :kelas_id, kelas = :kelas, jurusan = :jurusan, tanggal_diterima = :tanggal_diterima, agama = :agama,
                              tempat_lahir = :tempat_lahir,
                              tanggal_lahir = :tanggal_lahir, alamat = :alamat, nama_ayah = :nama_ayah, nama_ibu = :nama_ibu,
                              nama_wali = :nama_wali, no_telp_ortu = :no_telp_ortu, email_ortu = :email_ortu,
                              dokumen_ijasah = :dokumen_ijasah, dokumen_pas_foto = :dokumen_pas_foto,
                              dokumen_akta_kelahiran = :dokumen_akta_kelahiran, dokumen_kk = :dokumen_kk
                              WHERE id = :id');
        }
        $this->bindCommon($data);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function updatePasswordByUserId($userId, $newPassword) {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        $this->db->query('UPDATE buku_induk SET password = :password WHERE id = :user_id');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':password', $passwordHash);
        return $this->db->execute();
    }

    private function makePasswordHash($password) {
        if (empty($password)) {
            return null;
        }
        return password_hash($password, PASSWORD_DEFAULT);
    }

    private function bindCommon($data) {
        // Bind hanya field yang ada di kedua query (create dan update)
        $this->db->bind(':nama', $data['nama']);
        $this->db->bind(':nipd', $data['nipd']);
        $this->db->bind(':email', $data['email'] ?? $this->generateStudentEmail($data['nipd'] ?? ''));
        $this->db->bind(':nisn', $data['nisn'] ?? null);
        $this->db->bind(':kelas_id', !empty($data['kelas_id']) ? (int) $data['kelas_id'] : null);
        $this->db->bind(':kelas', $data['kelas'] ?? null);
        $this->db->bind(':jurusan', $data['jurusan'] ?? null);
        $this->db->bind(':tanggal_diterima', $data['tanggal_diterima'] ?? null);
        $this->db->bind(':agama', $data['agama'] ?? null);
        $this->db->bind(':tempat_lahir', $data['tempat_lahir'] ?? null);
        $this->db->bind(':tanggal_lahir', $data['tanggal_lahir'] ?? null);
        $this->db->bind(':alamat', $data['alamat'] ?? null);
        $this->db->bind(':nama_ayah', $data['nama_ayah'] ?? null);
        $this->db->bind(':nama_ibu', $data['nama_ibu'] ?? null);
        $this->db->bind(':nama_wali', $data['nama_wali'] ?? null);
        $this->db->bind(':no_telp_ortu', $data['no_telp_ortu'] ?? null);
        $this->db->bind(':email_ortu', $data['email_ortu'] ?? null);
        $this->db->bind(':dokumen_ijasah', $data['dokumen_ijasah'] ?? null);
        $this->db->bind(':dokumen_pas_foto', $data['dokumen_pas_foto'] ?? null);
        $this->db->bind(':dokumen_akta_kelahiran', $data['dokumen_akta_kelahiran'] ?? null);
        $this->db->bind(':dokumen_kk', $data['dokumen_kk'] ?? null);
    }

    private function generateStudentEmail($nipd) {
        $safeNipd = preg_replace('/[^a-zA-Z0-9._-]/', '', (string) $nipd);
        if ($safeNipd === '') {
            $safeNipd = uniqid('siswa');
        }
        return strtolower($safeNipd) . '@smk7.sch.id';
    }
}
?>
