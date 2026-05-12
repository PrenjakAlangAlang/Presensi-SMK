<?php

require_once 'Database.php';

class PresensiSesiModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function createSession($mata_pelajaran_id, $guru_id) {
        return false;
    }

    public function closeSession($mata_pelajaran_id, $guru_id) {
        return false;
    }

    public function getActiveSessionByKelas($mata_pelajaran_id) {
        return null;
    }

    public function isSessionActive($mata_pelajaran_id) {
        
        $s = $this->getActiveSessionByKelas($mata_pelajaran_id);
        return $s ? true : false;
    }

    
    public function getSessionsByKelas($mata_pelajaran_id) {
        return [];
    }

   
    public function getSessionById($id) {
        return null;
    }
}

?>
