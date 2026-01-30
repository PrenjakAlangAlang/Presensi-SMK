<?php
// app/models/LocationModel.php
// Model untuk menangani lokasi sekolah dan validasi jarak presensi
// Mengandung fungsi perhitungan jarak (Haversine) dan validasi radius
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
        // Ambil record lokasi sekolah terbaru (dipakai sebagai pusat validasi)
        $this->db->query('SELECT * FROM lokasi_sekolah ORDER BY id DESC LIMIT 1');
        return $this->db->single();
    }
    
    public function updateLokasiSekolah($data) {
        // Simpan lokasi baru (disimpan sebagai insert sehingga menjaga riwayat perubahan)
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
        // Ambil lokasi sekolah terbaru, lalu bandingkan jarak user terhadap radius_presensi
        $lokasiSekolah = $this->getLokasiSekolah();
        
        if(!$lokasiSekolah) {
            return false; // tidak tersedia lokasi -> tidak valid
        }
        
        $distance = $this->calculateDistance(
            $userLat, 
            $userLon, 
            $lokasiSekolah->latitude, 
            $lokasiSekolah->longitude
        );
        
        // true jika berada di dalam radius presensi
        return $distance <= $lokasiSekolah->radius_presensi;
    }
    
    public function getDistance($userLat, $userLon) {
        // Kembalikan jarak (meter) user ke titik pusat lokasi sekolah
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
    
    public function getRiwayatLokasi() {
        // Ambil semua riwayat perubahan lokasi, urutkan dari yang terbaru
        $this->db->query('SELECT ls.*, u.nama as updated_by_nama 
                         FROM lokasi_sekolah ls 
                         LEFT JOIN users u ON ls.updated_by = u.id 
                         ORDER BY ls.id DESC');
        return $this->db->resultSet();
    }
}
?>