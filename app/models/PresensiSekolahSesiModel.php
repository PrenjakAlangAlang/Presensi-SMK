<?php

require_once 'Database.php';

class PresensiSekolahSesiModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    
    public function getActiveSession() {
        $now = date('Y-m-d H:i:s');
        $this->db->query('SELECT * FROM presensi_sekolah_sesi WHERE status = "open" AND waktu_buka <= :now AND waktu_tutup > :now ORDER BY waktu_buka DESC LIMIT 1');
        $this->db->bind(':now', $now);
        return $this->db->single();
    }

    
    public function createSession($waktu_buka, $waktu_tutup, $created_by = null) {
        $this->db->query('INSERT INTO presensi_sekolah_sesi (waktu_buka, waktu_tutup, status, created_by) VALUES (:wb, :wt, "open", :created_by)');
        $this->db->bind(':wb', $waktu_buka);
        $this->db->bind(':wt', $waktu_tutup);
        $this->db->bind(':created_by', $created_by);
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function createMultipleSessions($waktu_buka, $waktu_tutup, $repeatDays, $repeatEveryWeeks, $repeatUntil, $created_by = null) {
        $start = new DateTime($waktu_buka);
        $end = new DateTime($waktu_tutup);
        $until = new DateTime($repeatUntil . ' 23:59:59');
        $repeatEveryWeeks = max(1, (int) $repeatEveryWeeks);
        $repeatDays = array_map('intval', (array) $repeatDays);
        $durationSeconds = $end->getTimestamp() - $start->getTimestamp();

        if ($durationSeconds <= 0 || empty($repeatDays) || $until < $start) {
            return false;
        }

        $created = 0;
        $cursor = new DateTime($start->format('Y-m-d 00:00:00'));
        while ($cursor <= $until) {
            $dayNumber = (int) $cursor->format('w');
            $daysFromStart = $start->diff($cursor)->days;
            $weekOffset = intdiv($daysFromStart, 7);

            if (in_array($dayNumber, $repeatDays, true) && $weekOffset % $repeatEveryWeeks === 0) {
                $sessionStart = new DateTime($cursor->format('Y-m-d') . ' ' . $start->format('H:i:s'));
                if ($sessionStart >= $start && $sessionStart <= $until) {
                    $sessionEnd = clone $sessionStart;
                    $sessionEnd->modify('+' . $durationSeconds . ' seconds');
                    if ($this->createSession($sessionStart->format('Y-m-d H:i:s'), $sessionEnd->format('Y-m-d H:i:s'), $created_by)) {
                        $created++;
                    }
                }
            }

            $cursor->modify('+1 day');
        }

        return $created;
    }

    
    public function closeSession($id) {
        $this->db->query('UPDATE presensi_sekolah_sesi SET status = "closed" WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    
    public function extendSession($id, $new_waktu_tutup) {
        $this->db->query('UPDATE presensi_sekolah_sesi SET waktu_tutup = :wt, status = "open" WHERE id = :id');
        $this->db->bind(':wt', $new_waktu_tutup);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

   
    public function getSessions($date = null) {
        if ($date) {
            $this->db->query('SELECT * FROM presensi_sekolah_sesi WHERE DATE(waktu_buka) = :d ORDER BY waktu_buka DESC');
            $this->db->bind(':d', $date);
        } else {
            $this->db->query('SELECT * FROM presensi_sekolah_sesi ORDER BY waktu_buka DESC');
        }
        return $this->db->resultSet();
    }

    
    public function getSesiByTanggal($tanggal) {
        $this->db->query('SELECT * FROM presensi_sekolah_sesi WHERE DATE(waktu_buka) = :tanggal ORDER BY waktu_buka DESC LIMIT 1');
        $this->db->bind(':tanggal', $tanggal);
        return $this->db->single();
    }

    
    public function getSessionById($id) {
        $this->db->query('SELECT * FROM presensi_sekolah_sesi WHERE id = :id LIMIT 1');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    private function getNamaHari($dayNumber) {
        $namaHari = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu'
        ];
        return $namaHari[$dayNumber] ?? '';
    }

   
    public function deleteSesi($id) {
        // Delete related presensi records first
        $this->db->query('DELETE FROM presensi_sekolah WHERE presensi_sekolah_sesi_id = :id');
        $this->db->bind(':id', $id);
        $this->db->execute();
        
        // Then delete the session
        $this->db->query('DELETE FROM presensi_sekolah_sesi WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

   
    public function deleteMultipleSesi($ids) {
        if (empty($ids) || !is_array($ids)) {
            return false;
        }
        
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        // Delete related presensi records first
        $this->db->query("DELETE FROM presensi_sekolah WHERE presensi_sekolah_sesi_id IN ($placeholders)");
        foreach ($ids as $index => $id) {
            $this->db->bind($index + 1, $id);
        }
        $this->db->execute();
        
        // Then delete the sessions
        $this->db->query("DELETE FROM presensi_sekolah_sesi WHERE id IN ($placeholders)");
        foreach ($ids as $index => $id) {
            $this->db->bind($index + 1, $id);
        }
        return $this->db->execute();
    }
}

?>
