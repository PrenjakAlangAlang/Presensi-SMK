<?php
// app/models/LocationModel.php
require_once 'Database.php';

class LocationModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Algoritma Haversine untuk menghitung jarak antara dua koordinat
    public function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earth_radius = 6371000; // Radius bumi dalam meter
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) + 
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
             sin($dLon/2) * sin($dLon/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        $distance = $earth_radius * $c;
        
        return $distance;
    }
    
    public function getLokasiSekolah() {
        $this->db->query('SELECT * FROM lokasi_sekolah ORDER BY id DESC LIMIT 1');
        return $this->db->single();
    }
    
    public function updateLokasiSekolah($data) {
        $this->db->query('INSERT INTO lokasi_sekolah (nama_sekolah, latitude, longitude, radius_presensi, updated_by) 
                         VALUES (:nama_sekolah, :latitude, :longitude, :radius_presensi, :updated_by)');
        $this->db->bind(':nama_sekolah', $data['nama_sekolah']);
        $this->db->bind(':latitude', $data['latitude']);
        $this->db->bind(':longitude', $data['longitude']);
        $this->db->bind(':radius_presensi', $data['radius_presensi']);
        $this->db->bind(':updated_by', $data['updated_by']);
        
        return $this->db->execute();
    }
    
    public function validateLocation($userLat, $userLon) {
        $lokasiSekolah = $this->getLokasiSekolah();
        
        if(!$lokasiSekolah) {
            return false;
        }
        
        $distance = $this->calculateDistance(
            $userLat, 
            $userLon, 
            $lokasiSekolah->latitude, 
            $lokasiSekolah->longitude
        );
        
        return $distance <= $lokasiSekolah->radius_presensi;
    }
    
    public function getDistance($userLat, $userLon) {
        $lokasiSekolah = $this->getLokasiSekolah();
        
        if(!$lokasiSekolah) {
            return null;
        }
        
        return $this->calculateDistance(
            $userLat, 
            $userLon, 
            $lokasiSekolah->latitude, 
            $lokasiSekolah->longitude
        );
    }
}
?>