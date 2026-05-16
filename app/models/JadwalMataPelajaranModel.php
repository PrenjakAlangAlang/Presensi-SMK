<?php

require_once 'Database.php';

class JadwalMataPelajaranModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllJadwal() {
        $this->db->query('SELECT j.*, k.nama_kelas, k.tahun_ajaran, k.semester, u.nama as guru_pengampu_nama,
                         CONCAT(j.hari, ", ", TIME_FORMAT(j.jam_mulai, "%H:%i"), "-", TIME_FORMAT(j.jam_selesai, "%H:%i")) as jadwal,
                         (SELECT COUNT(DISTINCT js.siswa_id)
                          FROM jadwal_mata_pelajaran rel
                          INNER JOIN jadwal_mata_pelajaran_siswa js ON rel.id = js.jadwal_mata_pelajaran_id
                          WHERE rel.kelas_jadwal_id = j.kelas_jadwal_id
                            AND rel.nama_mata_pelajaran = j.nama_mata_pelajaran
                            AND (rel.guru_pengampu <=> j.guru_pengampu)) as jumlah_siswa
                         FROM jadwal_mata_pelajaran j
                         INNER JOIN kelas_jadwal k ON j.kelas_jadwal_id = k.id
                         LEFT JOIN users u ON j.guru_pengampu = u.id
                         ORDER BY FIELD(j.hari, "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"),
                                  j.jam_mulai, k.nama_kelas, j.nama_mata_pelajaran');
        return $this->db->resultSet();
    }

    public function getJadwalByKelas($kelas_jadwal_id) {
        $this->db->query('SELECT j.*, k.nama_kelas, k.tahun_ajaran, k.semester, u.nama as guru_pengampu_nama,
                         CONCAT(j.hari, ", ", TIME_FORMAT(j.jam_mulai, "%H:%i"), "-", TIME_FORMAT(j.jam_selesai, "%H:%i")) as jadwal,
                         (SELECT COUNT(DISTINCT js.siswa_id)
                          FROM jadwal_mata_pelajaran rel
                          INNER JOIN jadwal_mata_pelajaran_siswa js ON rel.id = js.jadwal_mata_pelajaran_id
                          WHERE rel.kelas_jadwal_id = j.kelas_jadwal_id
                            AND rel.nama_mata_pelajaran = j.nama_mata_pelajaran
                            AND (rel.guru_pengampu <=> j.guru_pengampu)) as jumlah_siswa
                         FROM jadwal_mata_pelajaran j
                         INNER JOIN kelas_jadwal k ON j.kelas_jadwal_id = k.id
                         LEFT JOIN users u ON j.guru_pengampu = u.id
                         WHERE j.kelas_jadwal_id = :kelas_jadwal_id
                         ORDER BY FIELD(j.hari, "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"),
                                  j.jam_mulai, j.nama_mata_pelajaran');
        $this->db->bind(':kelas_jadwal_id', (int) $kelas_jadwal_id);
        return $this->db->resultSet();
    }

    public function getAllKelasJadwal() {
        $this->db->query('SELECT k.*,
                         (SELECT COUNT(*)
                          FROM jadwal_mata_pelajaran j
                          WHERE j.kelas_jadwal_id = k.id) as jumlah_jadwal,
                         (SELECT COUNT(DISTINCT j.nama_mata_pelajaran)
                          FROM jadwal_mata_pelajaran j
                          WHERE j.kelas_jadwal_id = k.id) as jumlah_mapel
                         FROM kelas_jadwal k
                         ORDER BY k.tahun_ajaran DESC, k.nama_kelas ASC');
        return $this->db->resultSet();
    }

    public function getKelasJadwalById($id) {
        $this->db->query('SELECT * FROM kelas_jadwal WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function createKelasJadwal($nama_kelas, $tahun_ajaran = null, $semester = null) {
        if ($this->kelasJadwalExists($nama_kelas, $tahun_ajaran, $semester)) {
            return false;
        }

        $this->db->query('INSERT INTO kelas_jadwal (nama_kelas, tahun_ajaran, semester)
                         VALUES (:nama_kelas, :tahun_ajaran, :semester)');
        $this->db->bind(':nama_kelas', $nama_kelas);
        $this->db->bind(':tahun_ajaran', $tahun_ajaran);
        $this->db->bind(':semester', $semester);
        return $this->db->execute();
    }

    public function updateKelasJadwal($id, $nama_kelas, $tahun_ajaran = null, $semester = null) {
        $kelas = $this->getKelasJadwalById($id);
        if (!$kelas) return false;
        if ($this->kelasJadwalExists($nama_kelas, $tahun_ajaran, $semester, $id)) return false;

        $this->db->query('UPDATE kelas_jadwal
                         SET nama_kelas = :nama_kelas, tahun_ajaran = :tahun_ajaran, semester = :semester
                         WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':nama_kelas', $nama_kelas);
        $this->db->bind(':tahun_ajaran', $tahun_ajaran);
        $this->db->bind(':semester', $semester);
        $ok = $this->db->execute();

        $this->db->query('UPDATE jadwal_mata_pelajaran
                         SET nama_kelas = :nama_kelas
                         WHERE kelas_jadwal_id = :kelas_jadwal_id');
        $this->db->bind(':nama_kelas', $nama_kelas);
        $this->db->bind(':kelas_jadwal_id', (int) $id);
        return $this->db->execute() && $ok;
    }

    public function deleteKelasJadwal($id) {
        $kelas = $this->getKelasJadwalById($id);
        if (!$kelas) return false;

        $this->db->query('DELETE FROM jadwal_mata_pelajaran WHERE kelas_jadwal_id = :kelas_jadwal_id');
        $this->db->bind(':kelas_jadwal_id', (int) $id);
        $this->db->execute();

        $this->db->query('DELETE FROM kelas_jadwal WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    private function kelasJadwalExists($nama_kelas, $tahun_ajaran = null, $semester = null, $excludeId = null) {
        $sql = 'SELECT id
                FROM kelas_jadwal
                WHERE nama_kelas = :nama_kelas
                  AND (tahun_ajaran <=> :tahun_ajaran)
                  AND (semester <=> :semester)';
        if ($excludeId) {
            $sql .= ' AND id <> :exclude_id';
        }
        $sql .= ' LIMIT 1';

        $this->db->query($sql);
        $this->db->bind(':nama_kelas', $nama_kelas);
        $this->db->bind(':tahun_ajaran', $tahun_ajaran);
        $this->db->bind(':semester', $semester);
        if ($excludeId) {
            $this->db->bind(':exclude_id', (int) $excludeId);
        }
        return (bool) $this->db->single();
    }

    public function getJadwalById($id) {
        $this->db->query('SELECT j.*, k.nama_kelas, k.tahun_ajaran, k.semester, u.nama as guru_pengampu_nama,
                         CONCAT(j.hari, ", ", TIME_FORMAT(j.jam_mulai, "%H:%i"), "-", TIME_FORMAT(j.jam_selesai, "%H:%i")) as jadwal
                         FROM jadwal_mata_pelajaran j
                         INNER JOIN kelas_jadwal k ON j.kelas_jadwal_id = k.id
                         LEFT JOIN users u ON j.guru_pengampu = u.id
                         WHERE j.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function createJadwal($data) {
        $this->db->query('INSERT INTO jadwal_mata_pelajaran
                         (kelas_jadwal_id, nama_kelas, nama_mata_pelajaran, guru_pengampu, hari, jam_mulai, jam_selesai, ruang)
                         VALUES
                         (:kelas_jadwal_id, :nama_kelas, :nama_mata_pelajaran, :guru_pengampu, :hari, :jam_mulai, :jam_selesai, :ruang)');
        $this->bindJadwalData($data);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function updateJadwal($data) {
        $this->db->query('UPDATE jadwal_mata_pelajaran
                         SET kelas_jadwal_id = :kelas_jadwal_id,
                             nama_kelas = :nama_kelas,
                             nama_mata_pelajaran = :nama_mata_pelajaran,
                             guru_pengampu = :guru_pengampu,
                             hari = :hari,
                             jam_mulai = :jam_mulai,
                             jam_selesai = :jam_selesai,
                             ruang = :ruang
                         WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->bindJadwalData($data);
        return $this->db->execute();
    }

    public function updateJadwalGroup($baseData, $slots, $groupId) {
        $existingIds = $this->getRelatedJadwalIds($groupId);
        $keptIds = [];
        $groupSiswaIds = $this->getSiswaIdsForJadwalIds($existingIds);
        $ok = true;

        foreach ($slots as $slot) {
            if (empty($slot['hari']) || empty($slot['jam_mulai']) || empty($slot['jam_selesai'])) {
                continue;
            }

            $data = $baseData + [
                'id' => $slot['id'] ?? null,
                'hari' => $slot['hari'],
                'jam_mulai' => $slot['jam_mulai'],
                'jam_selesai' => $slot['jam_selesai'],
                'ruang' => $slot['ruang'] ?? null
            ];

            if (!empty($data['id']) && in_array((int) $data['id'], $existingIds, true)) {
                $ok = $this->updateJadwal($data) && $ok;
                $keptIds[] = (int) $data['id'];
            } else {
                $newId = $this->createJadwal($data);
                if ($newId) {
                    $keptIds[] = (int) $newId;
                    $this->syncSiswaToJadwal($newId, $groupSiswaIds);
                } else {
                    $ok = false;
                }
            }
        }

        $deleteIds = array_values(array_diff($existingIds, $keptIds));
        foreach ($deleteIds as $deleteId) {
            $ok = $this->deleteJadwal($deleteId) && $ok;
        }

        return $ok && count($keptIds) > 0;
    }

    private function bindJadwalData($data) {
        $this->db->bind(':kelas_jadwal_id', (int) $data['kelas_jadwal_id']);
        $this->db->bind(':nama_kelas', $data['nama_kelas']);
        $this->db->bind(':nama_mata_pelajaran', $data['nama_mata_pelajaran']);
        $this->db->bind(':guru_pengampu', !empty($data['guru_pengampu']) ? $data['guru_pengampu'] : null);
        $this->db->bind(':hari', $data['hari']);
        $this->db->bind(':jam_mulai', $data['jam_mulai']);
        $this->db->bind(':jam_selesai', $data['jam_selesai']);
        $this->db->bind(':ruang', $data['ruang'] ?? null);
    }

    public function deleteJadwal($id) {
        $this->db->query('DELETE FROM jadwal_mata_pelajaran WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getJadwalByGuru($guru_id) {
        $this->db->query('SELECT j.*, k.nama_kelas, k.tahun_ajaran, k.semester,
                         j.id as mata_pelajaran_id,
                         CONCAT(j.hari, ", ", TIME_FORMAT(j.jam_mulai, "%H:%i"), "-", TIME_FORMAT(j.jam_selesai, "%H:%i")) as jadwal
                         FROM jadwal_mata_pelajaran j
                         INNER JOIN kelas_jadwal k ON j.kelas_jadwal_id = k.id
                         WHERE j.guru_pengampu = :guru_id
                         ORDER BY FIELD(j.hari, "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"),
                                  j.jam_mulai, k.nama_kelas');
        $this->db->bind(':guru_id', $guru_id);
        return $this->db->resultSet();
    }

    public function getJadwalBySiswa($siswa_id) {
        $this->db->query('SELECT j.*, k.nama_kelas, k.tahun_ajaran, k.semester, u.nama as guru_pengampu_nama,
                         j.id as mata_pelajaran_id,
                         CONCAT(j.hari, ", ", TIME_FORMAT(j.jam_mulai, "%H:%i"), "-", TIME_FORMAT(j.jam_selesai, "%H:%i")) as jadwal
                         FROM jadwal_mata_pelajaran j
                         INNER JOIN kelas_jadwal k ON j.kelas_jadwal_id = k.id
                         INNER JOIN jadwal_mata_pelajaran_siswa js ON j.id = js.jadwal_mata_pelajaran_id
                         LEFT JOIN users u ON j.guru_pengampu = u.id
                         WHERE js.siswa_id = :siswa_id
                         ORDER BY FIELD(j.hari, "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"),
                                  j.jam_mulai, j.nama_mata_pelajaran');
        $this->db->bind(':siswa_id', $siswa_id);
        return $this->db->resultSet();
    }

    public function getSiswaInJadwal($jadwal_id) {
        $jadwalIds = $this->getRelatedJadwalIds($jadwal_id);
        $placeholders = $this->buildInPlaceholders($jadwalIds, 'jadwal_id');

        $this->db->query('SELECT DISTINCT bi.id, bi.nama, bi.nis, bi.nisn, bi.kelas, bi.jurusan, bi.tanggal_diterima, bi.agama,
                         COALESCE(bi.email_ortu, "") AS email, "siswa" AS role
                         FROM buku_induk bi
                         INNER JOIN jadwal_mata_pelajaran_siswa js ON bi.id = js.siswa_id
                         WHERE js.jadwal_mata_pelajaran_id IN (' . $placeholders . ')
                         ORDER BY bi.nama');
        $this->bindInValues($jadwalIds, 'jadwal_id');
        return $this->db->resultSet();
    }

    public function getAvailableSiswa($jadwal_id = null, $filters = []) {
        $conditions = [];
        if ($jadwal_id) {
            $jadwalIds = $this->getRelatedJadwalIds($jadwal_id);
            $placeholders = $this->buildInPlaceholders($jadwalIds, 'jadwal_id');
            $conditions[] = 'id NOT IN (
                SELECT siswa_id FROM jadwal_mata_pelajaran_siswa WHERE jadwal_mata_pelajaran_id IN (' . $placeholders . ')
            )';
        }

        if (!empty($filters['kelas'])) {
            $conditions[] = 'kelas = :filter_kelas';
        }
        if (!empty($filters['jurusan'])) {
            $conditions[] = 'jurusan = :filter_jurusan';
        }
        if (!empty($filters['agama'])) {
            $conditions[] = 'agama = :filter_agama';
        }
        if (!empty($filters['search'])) {
            $conditions[] = '(nama LIKE :filter_search OR nis LIKE :filter_search OR nisn LIKE :filter_search OR kelas LIKE :filter_search OR jurusan LIKE :filter_search OR agama LIKE :filter_search)';
        }

        $where = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';

        $this->db->query('SELECT id, nama, nis, nisn, kelas, jurusan, tanggal_diterima, agama,
                         COALESCE(email_ortu, "") AS email, "siswa" AS role
                         FROM buku_induk' . $where . '
                         ORDER BY kelas IS NULL, kelas, jurusan IS NULL, jurusan, agama IS NULL, agama, nama');

        if ($jadwal_id) {
            $this->bindInValues($jadwalIds, 'jadwal_id');
        }
        if (!empty($filters['kelas'])) {
            $this->db->bind(':filter_kelas', $filters['kelas']);
        }
        if (!empty($filters['jurusan'])) {
            $this->db->bind(':filter_jurusan', $filters['jurusan']);
        }
        if (!empty($filters['agama'])) {
            $this->db->bind(':filter_agama', $filters['agama']);
        }
        if (!empty($filters['search'])) {
            $this->db->bind(':filter_search', '%' . $filters['search'] . '%');
        }

        return $this->db->resultSet();
    }

    public function addSiswaToJadwal($siswa_id, $jadwal_id) {
        $ok = true;
        foreach ($this->getRelatedJadwalIds($jadwal_id) as $relatedJadwalId) {
            $this->db->query('INSERT IGNORE INTO jadwal_mata_pelajaran_siswa (jadwal_mata_pelajaran_id, siswa_id)
                             VALUES (:jadwal_id, :siswa_id)');
            $this->db->bind(':jadwal_id', $relatedJadwalId);
            $this->db->bind(':siswa_id', $siswa_id);
            $ok = $this->db->execute() && $ok;
        }
        return $ok;
    }

    public function removeSiswaFromJadwal($siswa_id, $jadwal_id) {
        $jadwalIds = $this->getRelatedJadwalIds($jadwal_id);
        $placeholders = $this->buildInPlaceholders($jadwalIds, 'jadwal_id');

        $this->db->query('DELETE FROM jadwal_mata_pelajaran_siswa
                         WHERE jadwal_mata_pelajaran_id IN (' . $placeholders . ') AND siswa_id = :siswa_id');
        $this->bindInValues($jadwalIds, 'jadwal_id');
        $this->db->bind(':siswa_id', $siswa_id);
        return $this->db->execute();
    }

    public function getTotalSiswaByJadwal($jadwal_id) {
        $jadwalIds = $this->getRelatedJadwalIds($jadwal_id);
        $placeholders = $this->buildInPlaceholders($jadwalIds, 'jadwal_id');

        $this->db->query('SELECT COUNT(DISTINCT siswa_id) as total
                         FROM jadwal_mata_pelajaran_siswa
                         WHERE jadwal_mata_pelajaran_id IN (' . $placeholders . ')');
        $this->bindInValues($jadwalIds, 'jadwal_id');
        $row = $this->db->single();
        return $row ? (int) $row->total : 0;
    }

    private function getRelatedJadwalIds($jadwal_id) {
        $jadwal = $this->getJadwalById($jadwal_id);
        if (!$jadwal) {
            return [(int) $jadwal_id];
        }

        $this->db->query('SELECT id
                         FROM jadwal_mata_pelajaran
                         WHERE kelas_jadwal_id = :kelas_jadwal_id
                           AND nama_mata_pelajaran = :nama_mata_pelajaran
                           AND (guru_pengampu <=> :guru_pengampu)');
        $this->db->bind(':kelas_jadwal_id', (int) $jadwal->kelas_jadwal_id);
        $this->db->bind(':nama_mata_pelajaran', $jadwal->nama_mata_pelajaran);
        $this->db->bind(':guru_pengampu', $jadwal->guru_pengampu ?? null);

        $rows = $this->db->resultSet();
        $ids = array_map(function($row) {
            return (int) $row->id;
        }, $rows);

        return $ids ?: [(int) $jadwal_id];
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

    private function getSiswaIdsForJadwalIds($jadwalIds) {
        $placeholders = $this->buildInPlaceholders($jadwalIds, 'jadwal_id');
        $this->db->query('SELECT DISTINCT siswa_id
                         FROM jadwal_mata_pelajaran_siswa
                         WHERE jadwal_mata_pelajaran_id IN (' . $placeholders . ')');
        $this->bindInValues($jadwalIds, 'jadwal_id');

        return array_map(function($row) {
            return (int) $row->siswa_id;
        }, $this->db->resultSet());
    }

    private function syncSiswaToJadwal($jadwalId, $siswaIds) {
        foreach ($siswaIds as $siswaId) {
            $this->db->query('INSERT IGNORE INTO jadwal_mata_pelajaran_siswa (jadwal_mata_pelajaran_id, siswa_id)
                             VALUES (:jadwal_id, :siswa_id)');
            $this->db->bind(':jadwal_id', (int) $jadwalId);
            $this->db->bind(':siswa_id', (int) $siswaId);
            $this->db->execute();
        }
    }
}
?>
