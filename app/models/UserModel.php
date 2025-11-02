<?php
// app/models/UserModel.php
require_once __DIR__ . '/Database.php';

class UserModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function login($email, $password) {
        $this->db->query('SELECT * FROM users WHERE email = :email AND password = :password');
        $this->db->bind(':email', $email);
        $this->db->bind(':password', $password);
        
        $user = $this->db->single();
        
        if($user) {
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
        $this->db->query('INSERT INTO users (nama, email, password, role) VALUES (:nama, :email, :password, :role)');
        $this->db->bind(':nama', $data['nama']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role', $data['role']);
        
        return $this->db->execute();
    }
    
    public function updateUser($data) {
        $this->db->query('UPDATE users SET nama = :nama, email = :email, role = :role WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':nama', $data['nama']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':role', $data['role']);
        
        return $this->db->execute();
    }
    
    public function deleteUser($id) {
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    public function getUsersByRole($role) {
        $this->db->query('SELECT * FROM users WHERE role = :role ORDER BY nama');
        $this->db->bind(':role', $role);
        return $this->db->resultSet();
    }
    
    public function getSiswaByOrangTua($orangtua_id) {
        $this->db->query('SELECT u.* FROM users u 
                         INNER JOIN orangtua_siswa os ON u.id = os.siswa_id 
                         WHERE os.orangtua_id = :orangtua_id');
        $this->db->bind(':orangtua_id', $orangtua_id);
        return $this->db->resultSet();
    }
    
    public function getGuruWithKelas() {
        $this->db->query('SELECT u.*, k.nama_kelas 
                         FROM users u 
                         LEFT JOIN kelas k ON u.id = k.wali_kelas 
                         WHERE u.role = "guru"');
        return $this->db->resultSet();
    }

    // Get all siswa (students)
    public function getAllSiswa() {
        $this->db->query('SELECT * FROM users WHERE role = "siswa" ORDER BY nama');
        return $this->db->resultSet();
    }

    // Get siswa who are not yet assigned to any orangtua
    public function getSiswaWithoutOrangtua() {
        $this->db->query('SELECT * FROM users WHERE role = "siswa" AND id NOT IN (SELECT siswa_id FROM orangtua_siswa) ORDER BY nama');
        return $this->db->resultSet();
    }

    public function addSiswaToOrangtua($siswa_id, $orangtua_id) {
        // avoid duplicates
        $this->db->query('SELECT COUNT(*) as cnt FROM orangtua_siswa WHERE orangtua_id = :orangtua_id AND siswa_id = :siswa_id');
        $this->db->bind(':orangtua_id', $orangtua_id);
        $this->db->bind(':siswa_id', $siswa_id);
        $row = $this->db->single();
        if($row && $row->cnt > 0) return true;

        $this->db->query('INSERT INTO orangtua_siswa (orangtua_id, siswa_id) VALUES (:orangtua_id, :siswa_id)');
        $this->db->bind(':orangtua_id', $orangtua_id);
        $this->db->bind(':siswa_id', $siswa_id);
        return $this->db->execute();
    }

    public function removeSiswaFromOrangtua($siswa_id, $orangtua_id) {
        $this->db->query('DELETE FROM orangtua_siswa WHERE orangtua_id = :orangtua_id AND siswa_id = :siswa_id');
        $this->db->bind(':orangtua_id', $orangtua_id);
        $this->db->bind(':siswa_id', $siswa_id);
        return $this->db->execute();
    }
}
?>