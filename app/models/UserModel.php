<?php

require_once __DIR__ . '/Database.php';

class UserModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function login($identifier, $password) {
        $identifier = trim($identifier);
        $isNisLogin = !filter_var($identifier, FILTER_VALIDATE_EMAIL);

        if (!$isNisLogin) {
            $this->db->query('SELECT * FROM users WHERE email = :email LIMIT 1');
            $this->db->bind(':email', $identifier);
        } else {
            $this->db->query('SELECT bi.id, bi.nama, bi.email_ortu AS email, bi.password, "siswa" AS role
                              FROM buku_induk bi
                              WHERE TRIM(bi.nis) = :nis
                              ORDER BY bi.id DESC
                              LIMIT 1');
            $this->db->bind(':nis', $identifier);
        }
        
        $user = $this->db->single();
        
        $passwordHash = $user->password ?? null;

        $passwordValid = $user && $passwordHash && password_verify($password, $passwordHash);
        if (!$passwordValid && $user && $isNisLogin && $passwordHash && hash_equals((string) $passwordHash, (string) $password)) {
            $passwordValid = $this->updateBukuIndukPasswordHash($user->id, password_hash($password, PASSWORD_DEFAULT));
        }

        if($user && $passwordValid) {
            // Simpan data penting ke session untuk penggunaan di controller/view
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_email'] = $user->email;
            $_SESSION['user_role'] = $user->role;
            $_SESSION['user_nama'] = $user->nama;
            return true;
        }
        return false;
    }
    
    public function getAllUsers() {
        
        $this->db->query('SELECT * FROM users ORDER BY role, nama');
        return $this->db->resultSet();
    }
    
    public function getUserById($id) {
        
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    public function createUser($data) {
        if (($data['role'] ?? '') === 'siswa') {
            return false;
        }

        // Tambah user baru dengan password ter-hash
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $this->db->query('INSERT INTO users (nama, email, password, role) VALUES (:nama, :email, :password, :role)');
        $this->db->bind(':nama', $data['nama']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $hashedPassword);
        $this->db->bind(':role', $data['role']);
        
        return $this->db->execute();
    }
    
    public function updateUser($data) {
        if (($data['role'] ?? '') === 'siswa') {
            return false;
        }
      
        if (isset($data['password']) && !empty($data['password'])) {
            // Update dengan password ter-hash
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $this->db->query('UPDATE users SET nama = :nama, email = :email, role = :role, password = :password WHERE id = :id');
            $this->db->bind(':id', $data['id']);
            $this->db->bind(':nama', $data['nama']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':role', $data['role']);
            $this->db->bind(':password', $hashedPassword);
        } else {
            // Update tanpa password
            $this->db->query('UPDATE users SET nama = :nama, email = :email, role = :role WHERE id = :id');
            $this->db->bind(':id', $data['id']);
            $this->db->bind(':nama', $data['nama']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':role', $data['role']);
        }
        
        return $this->db->execute();
    }
    
    public function deleteUser($id) {
        
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    public function getUsersByRole($role) {
        if ($role === 'siswa') {
            $this->db->query('SELECT id, nama, COALESCE(email_ortu, "") AS email, "siswa" AS role FROM buku_induk ORDER BY nama');
            return $this->db->resultSet();
        }

        // Ambil semua user dengan role tertentu (mis. siswa, guru, admin)
        $this->db->query('SELECT * FROM users WHERE role = :role ORDER BY nama');
        $this->db->bind(':role', $role);
        return $this->db->resultSet();
    }
    
    public function getGuruWithKelas() {
        // Ambil guru beserta info mata pelajaran jika ada (left join)
        $this->db->query('SELECT u.*, k.nama_mata_pelajaran 
                         FROM users u 
                         LEFT JOIN jadwal_mata_pelajaran k ON u.id = k.guru_pengampu 
                         WHERE u.role = "guru"');
        return $this->db->resultSet();
    }


    public function getAllSiswa() {
        $this->db->query('SELECT id, nama, COALESCE(email_ortu, "") AS email, "siswa" AS role FROM buku_induk ORDER BY nama');
        return $this->db->resultSet();
    }

    public function createSiswaFromBukuInduk($nama, $nis, $passwordHash) {
        return false;
    }

    public function updateSiswaProfile($userId, $nama) {
        $this->db->query('UPDATE buku_induk SET nama = :nama WHERE id = :id');
        $this->db->bind(':id', $userId);
        $this->db->bind(':nama', $nama);
        return $this->db->execute();
    }

    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->updatePasswordHash($userId, $hashedPassword);
    }

    public function updatePasswordHash($userId, $passwordHash) {
        $this->db->query('UPDATE users SET password = :password WHERE id = :id');
        $this->db->bind(':id', $userId);
        $this->db->bind(':password', $passwordHash);
        return $this->db->execute();
    }

    private function updateBukuIndukPasswordHash($id, $passwordHash) {
        $this->db->query('UPDATE buku_induk SET password = :password WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':password', $passwordHash);
        return $this->db->execute();
    }

    private function generateSiswaEmail($nis) {
        $safeNis = preg_replace('/[^a-zA-Z0-9._-]/', '', (string) $nis);
        if ($safeNis === '') {
            $safeNis = uniqid('siswa');
        }
        return 'siswa_' . $safeNis . '@buku-induk.local';
    }
}
?>
