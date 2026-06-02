<?php

require_once __DIR__ . '/Database.php';

class UserModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function login($identifier, $password) {
        $identifier = trim($identifier);
        $isEmailLogin = (bool) filter_var($identifier, FILTER_VALIDATE_EMAIL);

        if ($isEmailLogin) {
            $this->db->query('SELECT u.*, COALESCE(g.nama, a.nama) AS nama
                              FROM users u
                              LEFT JOIN guru g ON u.id = g.user_id
                              LEFT JOIN admin a ON u.id = a.user_id
                              WHERE u.email = :email
                              LIMIT 1');
            $this->db->bind(':email', $identifier);
        } else {
            $this->db->query('SELECT bi.id, bi.nama, bi.email, bi.password, "siswa" AS role
                              FROM buku_induk bi
                              WHERE TRIM(bi.nipd) = :nipd
                              ORDER BY bi.id DESC
                              LIMIT 1');
            $this->db->bind(':nipd', $identifier);
        }
        
        $user = $this->db->single();

        if (!$user && $isEmailLogin) {
            $this->db->query('SELECT bi.id, bi.nama, bi.email, bi.password, "siswa" AS role
                              FROM buku_induk bi
                              WHERE bi.email = :email
                              ORDER BY bi.id DESC
                              LIMIT 1');
            $this->db->bind(':email', $identifier);
            $user = $this->db->single();
        }
        
        $passwordHash = $user->password ?? null;
        $isSiswaLogin = $user && ($user->role ?? '') === 'siswa';

        $passwordValid = $user && $passwordHash && password_verify($password, $passwordHash);
        if (!$passwordValid && $isSiswaLogin && $passwordHash && hash_equals((string) $passwordHash, (string) $password)) {
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
        
        $this->db->query('SELECT u.*, COALESCE(g.nama, a.nama) AS nama
                         FROM users u
                         LEFT JOIN guru g ON u.id = g.user_id
                         LEFT JOIN admin a ON u.id = a.user_id
                         ORDER BY u.role, nama');
        return $this->db->resultSet();
    }
    
    public function getUserById($id) {
        
        $this->db->query('SELECT u.*, COALESCE(g.nama, a.nama) AS nama
                         FROM users u
                         LEFT JOIN guru g ON u.id = g.user_id
                         LEFT JOIN admin a ON u.id = a.user_id
                         WHERE u.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    public function createUser($data) {
        if (($data['role'] ?? '') === 'siswa') {
            return false;
        }

        // Tambah user baru dengan password ter-hash
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $this->db->query('INSERT INTO users (email, password, role) VALUES (:email, :password, :role)');
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $hashedPassword);
        $this->db->bind(':role', $data['role']);
        
        if (!$this->db->execute()) {
            return false;
        }

        return $this->syncUserProfile((int) $this->db->lastInsertId(), $data['role'], $data['nama']);
    }
    
    public function updateUser($data) {
        if (($data['role'] ?? '') === 'siswa') {
            return false;
        }
      
        if (isset($data['password']) && !empty($data['password'])) {
            // Update dengan password ter-hash
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $this->db->query('UPDATE users SET email = :email, role = :role, password = :password WHERE id = :id');
            $this->db->bind(':id', $data['id']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':role', $data['role']);
            $this->db->bind(':password', $hashedPassword);
        } else {
            // Update tanpa password
            $this->db->query('UPDATE users SET email = :email, role = :role WHERE id = :id');
            $this->db->bind(':id', $data['id']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':role', $data['role']);
        }
        
        return $this->db->execute() && $this->syncUserProfile((int) $data['id'], $data['role'], $data['nama']);
    }
    
    public function deleteUser($id) {
        
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    public function getUsersByRole($role) {
        if ($role === 'siswa') {
            $this->db->query('SELECT id, nama, COALESCE(email, "") AS email, "siswa" AS role FROM buku_induk ORDER BY nama');
            return $this->db->resultSet();
        }

        if ($role === 'guru') {
            $this->db->query('SELECT u.*, g.nama
                             FROM users u
                             INNER JOIN guru g ON u.id = g.user_id
                             WHERE u.role = :role
                             ORDER BY g.nama');
            $this->db->bind(':role', $role);
            return $this->db->resultSet();
        }

        $this->db->query('SELECT u.*, a.nama
                         FROM users u
                         INNER JOIN admin a ON u.id = a.user_id
                         WHERE u.role = :role
                         ORDER BY a.nama');
        $this->db->bind(':role', $role);
        return $this->db->resultSet();
    }
    
    public function getGuruWithKelas() {
        // Ambil guru beserta info mata pelajaran jika ada (left join)
        $this->db->query('SELECT u.*, g.id AS guru_id, g.nama, k.nama_mata_pelajaran
                         FROM guru g
                         INNER JOIN users u ON g.user_id = u.id
                         LEFT JOIN jadwal_mata_pelajaran k ON g.id = k.guru_pengampu
                         WHERE u.role = "guru"');
        return $this->db->resultSet();
    }


    public function getAllSiswa() {
        $this->db->query('SELECT id, nama, COALESCE(email, "") AS email, "siswa" AS role FROM buku_induk ORDER BY nama');
        return $this->db->resultSet();
    }

    public function createSiswaFromBukuInduk($nama, $nipd, $passwordHash) {
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

    private function syncUserProfile($userId, $role, $nama) {
        if ($role === 'guru') {
            $this->db->query('DELETE FROM admin WHERE user_id = :user_id');
            $this->db->bind(':user_id', $userId);
            if (!$this->db->execute()) return false;

            $this->db->query('INSERT INTO guru (id, user_id, nama)
                             VALUES (:id, :user_id, :nama)
                             ON DUPLICATE KEY UPDATE nama = VALUES(nama)');
        } else {
            $this->db->query('DELETE FROM guru WHERE user_id = :user_id');
            $this->db->bind(':user_id', $userId);
            if (!$this->db->execute()) return false;

            $this->db->query('INSERT INTO admin (id, user_id, nama)
                             VALUES (:id, :user_id, :nama)
                             ON DUPLICATE KEY UPDATE nama = VALUES(nama)');
        }

        $this->db->bind(':id', $userId);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':nama', $nama);
        return $this->db->execute();
    }

    private function generateSiswaEmail($nipd) {
        $safeNipd = preg_replace('/[^a-zA-Z0-9._-]/', '', (string) $nipd);
        if ($safeNipd === '') {
            $safeNipd = uniqid('siswa');
        }
        return 'siswa_' . $safeNipd . '@buku-induk.local';
    }
}
?>
