<?php
// app/models/UserModel.php
// Model untuk operasi CRUD pada tabel users dan relasi terkait
// Menyediakan fungsi login, daftar user berdasarkan role, dan relasi orangtua/siswa
require_once __DIR__ . '/Database.php';

class UserModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function login($email, $password) {
        // Cari user dengan email & password yang diberikan
        $this->db->query('SELECT * FROM users WHERE email = :email AND password = :password');
        $this->db->bind(':email', $email);
        $this->db->bind(':password', $password);
        
        $user = $this->db->single();
        
        if($user) {
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
        // Ambil semua user, urutkan menurut role lalu nama
        $this->db->query('SELECT * FROM users ORDER BY role, nama');
        return $this->db->resultSet();
    }
    
    public function getUserById($id) {
        // Ambil satu user berdasarkan id
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    public function createUser($data) {
        // Tambah user baru (password belum di-hash di implementasi ini)
        $this->db->query('INSERT INTO users (nama, email, password, role) VALUES (:nama, :email, :password, :role)');
        $this->db->bind(':nama', $data['nama']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role', $data['role']);
        
        return $this->db->execute();
    }
    
    public function updateUser($data) {
        // Perbarui data user (nama, email, role, dan password jika ada)
        if (isset($data['password']) && !empty($data['password'])) {
            // Update dengan password
            $this->db->query('UPDATE users SET nama = :nama, email = :email, role = :role, password = :password WHERE id = :id');
            $this->db->bind(':id', $data['id']);
            $this->db->bind(':nama', $data['nama']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':role', $data['role']);
            $this->db->bind(':password', $data['password']);
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
        // Hapus user berdasarkan id
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    public function getUsersByRole($role) {
        // Ambil semua user dengan role tertentu (mis. siswa, guru, admin)
        $this->db->query('SELECT * FROM users WHERE role = :role ORDER BY nama');
        $this->db->bind(':role', $role);
        return $this->db->resultSet();
    }
    
    public function getGuruWithKelas() {
        // Ambil guru beserta info kelas jika ada (left join)
        $this->db->query('SELECT u.*, k.nama_kelas 
                         FROM users u 
                         LEFT JOIN kelas k ON u.id = k.wali_kelas 
                         WHERE u.role = "guru"');
        return $this->db->resultSet();
    }

    // Get all siswa (students)
    public function getAllSiswa() {
        // Ambil semua pengguna yang berperan sebagai siswa
        $this->db->query('SELECT * FROM users WHERE role = "siswa" ORDER BY nama');
        return $this->db->resultSet();
    }
}
?>