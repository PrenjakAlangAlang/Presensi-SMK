<?php

require_once 'Database.php';

class PresensiSesiModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function createSession($mata_pelajaran_id, $guru_id, $tanggal = null) {
        $tanggal = $tanggal ?: date('Y-m-d');
        $jadwal = $this->getJadwalForDateInGroup($mata_pelajaran_id, $guru_id, $tanggal);
        if (!$jadwal) return false;
        if (($jadwal->kelas_status ?? 'active') === 'archived') return false;

        $waktuBuka = $tanggal . ' ' . $jadwal->jam_mulai;
        $waktuTutup = $tanggal . ' ' . $jadwal->jam_selesai;

        $this->db->query('INSERT INTO presensi_mapel_sesi (jadwal_mata_pelajaran_id, guru_id, waktu_buka, waktu_tutup, status)
                         VALUES (:jadwal_id, :guru_id, :waktu_buka, :waktu_tutup, "open")
                         ON DUPLICATE KEY UPDATE status = "open", waktu_tutup = VALUES(waktu_tutup)');
        $this->db->bind(':jadwal_id', (int) $jadwal->id);
        $this->db->bind(':guru_id', (int) $guru_id);
        $this->db->bind(':waktu_buka', $waktuBuka);
        $this->db->bind(':waktu_tutup', $waktuTutup);
        return $this->db->execute();
    }

    public function createMultipleSessions($mata_pelajaran_id, $guru_id, $tanggal_mulai, $tanggal_selesai, $repeatEveryWeeks = 1) {
        $jadwalList = $this->getRelatedJadwalForGuru($mata_pelajaran_id, $guru_id);
        if (empty($jadwalList)) return false;

        $start = new DateTime($tanggal_mulai . ' 00:00:00');
        $end = new DateTime($tanggal_selesai . ' 23:59:59');
        if ($end < $start) return false;

        $repeatEveryWeeks = max(1, (int) $repeatEveryWeeks);
        $created = 0;
        $cursor = clone $start;

        while ($cursor <= $end) {
            $daysFromStart = $start->diff($cursor)->days;
            $weekOffset = intdiv($daysFromStart, 7);
            $tanggal = $cursor->format('Y-m-d');

            if ($weekOffset % $repeatEveryWeeks === 0) {
                foreach ($jadwalList as $jadwal) {
                    if ($this->isDateMatchingJadwalDay($tanggal, $jadwal->hari)) {
                        if ($this->createSession($jadwal->id, $guru_id, $tanggal)) {
                            $created++;
                        }
                    }
                }
            }

            $cursor->modify('+1 day');
        }

        return $created;
    }

    public function closeSession($mata_pelajaran_id, $guru_id) {
        $session = $this->getActiveSessionByKelas($mata_pelajaran_id);
        if (!$session || (int) $session->guru_id !== (int) $guru_id) return false;

        require_once __DIR__ . '/PresensiModel.php';
        $presensiModel = new PresensiModel();
        $presensiModel->markAbsentStudentsAsAlphaKelas($mata_pelajaran_id, $session->id);

        $this->db->query('UPDATE presensi_mapel_sesi SET status = "closed", waktu_tutup = NOW()
                         WHERE id = :id AND status = "open"');
        $this->db->bind(':id', (int) $session->id);
        return $this->db->execute();
    }

    public function closeSessionByIdForGuru($sesi_id, $guru_id) {
        $session = $this->getSessionForGuru($sesi_id, $guru_id);
        if (!$session || $session->status !== 'open') return false;
        if (strtotime($session->waktu_buka) > time()) return false;

        require_once __DIR__ . '/PresensiModel.php';
        $presensiModel = new PresensiModel();
        $presensiModel->markAbsentStudentsAsAlphaKelas($session->jadwal_mata_pelajaran_id, $session->id);

        $this->db->query('UPDATE presensi_mapel_sesi SET status = "closed" WHERE id = :id');
        $this->db->bind(':id', (int) $sesi_id);
        return $this->db->execute();
    }

    public function getActiveSessionByKelas($mata_pelajaran_id) {
        $this->db->query('SELECT * FROM presensi_mapel_sesi
                         WHERE jadwal_mata_pelajaran_id = :jadwal_id
                           AND status = "open"
                           AND NOW() BETWEEN waktu_buka AND waktu_tutup
                         ORDER BY id DESC
                         LIMIT 1');
        $this->db->bind(':jadwal_id', (int) $mata_pelajaran_id);
        return $this->db->single();
    }

    public function isSessionActive($mata_pelajaran_id) {
        
        $s = $this->getActiveSessionByKelas($mata_pelajaran_id);
        return $s ? true : false;
    }

    
    public function getSessionsByKelas($mata_pelajaran_id) {
        $this->db->query('SELECT * FROM presensi_mapel_sesi
                         WHERE jadwal_mata_pelajaran_id = :jadwal_id
                         ORDER BY waktu_buka DESC');
        $this->db->bind(':jadwal_id', (int) $mata_pelajaran_id);
        return $this->db->resultSet();
    }

    public function getSessionsWithStatsByKelas($mata_pelajaran_id, $guru_id) {
        $jadwalIds = $this->getRelatedJadwalIdsForGuru($mata_pelajaran_id, $guru_id);
        if (empty($jadwalIds)) {
            return [];
        }
        $placeholders = $this->buildInPlaceholders($jadwalIds, 'jadwal_id');

        $this->db->query('SELECT s.*, j.hari, j.jam_mulai, j.jam_selesai, j.ruang,
                         (SELECT COUNT(*) FROM jadwal_mata_pelajaran_siswa js WHERE js.jadwal_mata_pelajaran_id = s.jadwal_mata_pelajaran_id) as total_siswa,
                         (SELECT COUNT(*) FROM presensi_mapel pm WHERE pm.presensi_sesi_id = s.id AND pm.jenis = "hadir") as hadir,
                         (SELECT COUNT(*) FROM presensi_mapel pm WHERE pm.presensi_sesi_id = s.id AND pm.jenis = "izin") as izin,
                         (SELECT COUNT(*) FROM presensi_mapel pm WHERE pm.presensi_sesi_id = s.id AND pm.jenis = "sakit") as sakit,
                         (SELECT COUNT(*) FROM presensi_mapel pm WHERE pm.presensi_sesi_id = s.id AND pm.jenis = "alpha") as alpha
                         FROM presensi_mapel_sesi s
                         INNER JOIN jadwal_mata_pelajaran j ON s.jadwal_mata_pelajaran_id = j.id
                         WHERE s.jadwal_mata_pelajaran_id IN (' . $placeholders . ')
                         ORDER BY s.waktu_buka DESC');
        $this->bindInValues($jadwalIds, 'jadwal_id');
        return $this->db->resultSet();
    }

   
    public function getSessionById($id) {
        $this->db->query('SELECT ps.*, j.nama_mata_pelajaran, k.nama_kelas
                         FROM presensi_mapel_sesi ps
                         INNER JOIN jadwal_mata_pelajaran j ON ps.jadwal_mata_pelajaran_id = j.id
                         INNER JOIN kelas k ON j.kelas_jadwal_id = k.id
                         WHERE ps.id = :id');
        $this->db->bind(':id', (int) $id);
        return $this->db->single();
    }

    public function getSessionForGuru($sesi_id, $guru_id) {
        $this->db->query('SELECT ps.*, j.nama_mata_pelajaran, k.nama_kelas, j.guru_pengampu
                         FROM presensi_mapel_sesi ps
                         INNER JOIN jadwal_mata_pelajaran j ON ps.jadwal_mata_pelajaran_id = j.id
                         INNER JOIN kelas k ON j.kelas_jadwal_id = k.id
                         WHERE ps.id = :id AND j.guru_pengampu = :guru_id
                         LIMIT 1');
        $this->db->bind(':id', (int) $sesi_id);
        $this->db->bind(':guru_id', (int) $guru_id);
        return $this->db->single();
    }

    public function deleteSessionForGuru($sesi_id, $guru_id) {
        $session = $this->getSessionForGuru($sesi_id, $guru_id);
        if (!$session) return false;

        $this->db->query('DELETE FROM presensi_mapel WHERE presensi_sesi_id = :sesi_id');
        $this->db->bind(':sesi_id', (int) $sesi_id);
        $this->db->execute();

        $this->db->query('DELETE FROM presensi_mapel_sesi WHERE id = :sesi_id');
        $this->db->bind(':sesi_id', (int) $sesi_id);
        return $this->db->execute();
    }

    public function updateLaporanKemajuanForGuru($sesi_id, $guru_id, $laporan_kemajuan) {
        $session = $this->getSessionForGuru($sesi_id, $guru_id);
        if (!$session) return false;

        $this->db->query('UPDATE presensi_mapel_sesi
                         SET laporan_kemajuan = :laporan_kemajuan
                         WHERE id = :sesi_id AND guru_id = :guru_id');
        $this->db->bind(':laporan_kemajuan', trim((string) $laporan_kemajuan));
        $this->db->bind(':sesi_id', (int) $sesi_id);
        $this->db->bind(':guru_id', (int) $guru_id);
        return $this->db->execute();
    }

    public function syncScheduledSessionsForToday($guru_id = null) {
        return 0;
    }

    public function closeExpiredSessions() {
        $this->db->query('SELECT id, jadwal_mata_pelajaran_id FROM presensi_mapel_sesi
                         WHERE status = "open" AND waktu_tutup <= NOW()');
        $expired = $this->db->resultSet();

        require_once __DIR__ . '/PresensiModel.php';
        $presensiModel = new PresensiModel();
        foreach ($expired as $session) {
            $presensiModel->markAbsentStudentsAsAlphaKelas($session->jadwal_mata_pelajaran_id, $session->id);
            $this->db->query('UPDATE presensi_mapel_sesi SET status = "closed" WHERE id = :id');
            $this->db->bind(':id', (int) $session->id);
            $this->db->execute();
        }

        return count($expired);
    }

    public function getTodayForSiswa($siswa_id) {
        $this->db->query('SELECT j.*, k.nama_kelas, k.status as kelas_status, u.nama as guru_pengampu_nama,
                         s.id as sesi_id, s.status as sesi_status, s.waktu_buka, s.waktu_tutup,
                         pm.id as presensi_id, pm.jenis as presensi_jenis, pm.waktu as waktu_presensi
                         FROM jadwal_mata_pelajaran j
                         INNER JOIN kelas k ON j.kelas_jadwal_id = k.id
                         INNER JOIN jadwal_mata_pelajaran_siswa js ON j.id = js.jadwal_mata_pelajaran_id
                         LEFT JOIN users u ON j.guru_pengampu = u.id
                         LEFT JOIN presensi_mapel_sesi s ON s.jadwal_mata_pelajaran_id = j.id AND DATE(s.waktu_buka) = CURDATE()
                         LEFT JOIN presensi_mapel pm ON pm.presensi_sesi_id = s.id AND pm.user_id = :siswa_id
                         WHERE js.siswa_id = :siswa_id2 AND j.hari = :hari AND k.status = "active"
                         ORDER BY j.jam_mulai, j.nama_mata_pelajaran');
        $this->db->bind(':siswa_id', (int) $siswa_id);
        $this->db->bind(':siswa_id2', (int) $siswa_id);
        $this->db->bind(':hari', $this->currentHari());
        return $this->db->resultSet();
    }

    public function getTodayForGuru($guru_id) {
        $this->db->query('SELECT j.*, k.nama_kelas, k.status as kelas_status,
                         s.id as sesi_id, s.status as sesi_status, s.waktu_buka, s.waktu_tutup,
                         (SELECT COUNT(*) FROM jadwal_mata_pelajaran_siswa js WHERE js.jadwal_mata_pelajaran_id = j.id) as total_siswa,
                         (SELECT COUNT(*) FROM presensi_mapel pm WHERE pm.presensi_sesi_id = s.id AND pm.jenis = "hadir") as hadir,
                         (SELECT COUNT(*) FROM presensi_mapel pm WHERE pm.presensi_sesi_id = s.id AND pm.jenis = "izin") as izin,
                         (SELECT COUNT(*) FROM presensi_mapel pm WHERE pm.presensi_sesi_id = s.id AND pm.jenis = "sakit") as sakit,
                         (SELECT COUNT(*) FROM presensi_mapel pm WHERE pm.presensi_sesi_id = s.id AND pm.jenis = "alpha") as alpha
                         FROM jadwal_mata_pelajaran j
                         INNER JOIN kelas k ON j.kelas_jadwal_id = k.id
                         LEFT JOIN presensi_mapel_sesi s ON s.jadwal_mata_pelajaran_id = j.id AND DATE(s.waktu_buka) = CURDATE()
                         WHERE j.guru_pengampu = :guru_id AND j.hari = :hari AND k.status = "active"
                         ORDER BY j.jam_mulai, j.nama_mata_pelajaran');
        $this->db->bind(':guru_id', (int) $guru_id);
        $this->db->bind(':hari', $this->currentHari());
        return $this->db->resultSet();
    }

    public function getManageForGuru($guru_id, $includeArchived = false) {
        $statusFilter = $includeArchived ? '' : ' WHERE k.status = "active"';
        $this->db->query('SELECT base.*, k.nama_kelas,
                         k.status as kelas_status,
                         grouped.jumlah_pertemuan,
                         grouped.jadwal_ringkas
                         FROM (
                            SELECT MIN(id) as id,
                                   kelas_jadwal_id,
                                   nama_mata_pelajaran,
                                   guru_pengampu,
                                   COUNT(*) as jumlah_pertemuan,
                                   GROUP_CONCAT(
                                      CONCAT(hari, ", ", TIME_FORMAT(jam_mulai, "%H:%i"), "-", TIME_FORMAT(jam_selesai, "%H:%i"))
                                      ORDER BY FIELD(hari, "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"), jam_mulai
                                      SEPARATOR " | "
                                   ) as jadwal_ringkas
                            FROM jadwal_mata_pelajaran
                            WHERE guru_pengampu = :guru_id
                            GROUP BY kelas_jadwal_id, nama_mata_pelajaran, guru_pengampu
                         ) grouped
                         INNER JOIN jadwal_mata_pelajaran base ON grouped.id = base.id
                         INNER JOIN kelas k ON base.kelas_jadwal_id = k.id
                         ' . $statusFilter . '
                         ORDER BY k.nama_kelas, base.nama_mata_pelajaran');
        $this->db->bind(':guru_id', (int) $guru_id);
        return $this->db->resultSet();
    }

    private function getJadwalForGuru($jadwal_id, $guru_id) {
        $this->db->query('SELECT j.*, k.status as kelas_status
                         FROM jadwal_mata_pelajaran j
                         INNER JOIN kelas k ON j.kelas_jadwal_id = k.id
                         WHERE j.id = :id AND j.guru_pengampu = :guru_id
                         LIMIT 1');
        $this->db->bind(':id', (int) $jadwal_id);
        $this->db->bind(':guru_id', (int) $guru_id);
        return $this->db->single();
    }

    private function getRelatedJadwalForGuru($jadwal_id, $guru_id) {
        $jadwal = $this->getJadwalForGuru($jadwal_id, $guru_id);
        if (!$jadwal) return [];

        $this->db->query('SELECT j.*, k.status as kelas_status
                         FROM jadwal_mata_pelajaran j
                         INNER JOIN kelas k ON j.kelas_jadwal_id = k.id
                         WHERE j.kelas_jadwal_id = :kelas_jadwal_id
                           AND j.nama_mata_pelajaran = :nama_mata_pelajaran
                           AND j.guru_pengampu = :guru_id
                         ORDER BY FIELD(hari, "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"), jam_mulai');
        $this->db->bind(':kelas_jadwal_id', (int) $jadwal->kelas_jadwal_id);
        $this->db->bind(':nama_mata_pelajaran', $jadwal->nama_mata_pelajaran);
        $this->db->bind(':guru_id', (int) $guru_id);
        return $this->db->resultSet();
    }

    private function getRelatedJadwalIdsForGuru($jadwal_id, $guru_id) {
        return array_map(function($jadwal) {
            return (int) $jadwal->id;
        }, $this->getRelatedJadwalForGuru($jadwal_id, $guru_id));
    }

    private function getJadwalForDateInGroup($jadwal_id, $guru_id, $tanggal) {
        foreach ($this->getRelatedJadwalForGuru($jadwal_id, $guru_id) as $jadwal) {
            if ($this->isDateMatchingJadwalDay($tanggal, $jadwal->hari)) {
                return $jadwal;
            }
        }
        return null;
    }

    private function buildInPlaceholders($values, $prefix) {
        return implode(', ', array_map(function($index) use ($prefix) {
            return ':' . $prefix . '_' . $index;
        }, array_keys($values)));
    }

    private function bindInValues($values, $prefix) {
        foreach (array_values($values) as $index => $value) {
            $this->db->bind(':' . $prefix . '_' . $index, (int) $value);
        }
    }

    private function currentHari() {
        $hariMap = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu'
        ];
        return $hariMap[(int) date('N')];
    }

    private function isDateMatchingJadwalDay($tanggal, $hariJadwal) {
        $dayMap = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu'
        ];
        $dayName = $dayMap[(int) date('N', strtotime($tanggal))] ?? null;
        return $dayName === $hariJadwal;
    }
}

?>
