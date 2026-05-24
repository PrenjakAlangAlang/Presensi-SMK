<?php

require_once __DIR__ . '/../../config/config.php';

$pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER,
    DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$dates = [
    '2026-05-18' => 'Senin',
    '2026-05-19' => 'Selasa',
    '2026-05-20' => 'Rabu',
];

$targetClassNames = [
    'X PRODUKSI FILM',
    'X DESAIN KOMUNIKASI VISUAL',
    'XI PRODUKSI FILM',
    'XI DESAIN KOMUNIKASI VISUAL',
];

$targetClassPairs = [
    ['X', 'PRODUKSI FILM'],
    ['X', 'DESAIN KOMUNIKASI VISUAL'],
    ['XI', 'PRODUKSI FILM'],
    ['XI', 'DESAIN KOMUNIKASI VISUAL'],
];

$summary = [
    'school_sessions_created' => 0,
    'school_presensi_created' => 0,
    'school_presensi_updated' => 0,
    'mapel_sessions_created' => 0,
    'mapel_presensi_created' => 0,
    'mapel_presensi_updated' => 0,
    'school_presensi_skipped' => 0,
    'mapel_presensi_skipped' => 0,
];

function qMarks($items) {
    return implode(',', array_fill(0, count($items), '?'));
}

function targetStudentWhere($pairs) {
    return implode(' OR ', array_fill(0, count($pairs), '(kelas = ? AND jurusan = ?)'));
}

function targetStudentParams($pairs) {
    $params = [];
    foreach ($pairs as $pair) {
        $params[] = $pair[0];
        $params[] = $pair[1];
    }
    return $params;
}

function dummyJenis($studentId, $salt) {
    $value = ((int) $studentId + (int) $salt) % 29;
    if ($value === 0) return 'alpha';
    if ($value === 5) return 'izin';
    if ($value === 11) return 'sakit';
    return 'hadir';
}

function alasanFor($jenis) {
    if ($jenis === 'izin') return 'Izin keperluan keluarga';
    if ($jenis === 'sakit') return 'Sakit';
    if ($jenis === 'alpha') return 'Tidak hadir saat sesi ditutup';
    return null;
}

function locationFor($studentId, $salt) {
    $offsetA = (((int) $studentId + (int) $salt) % 19) / 100000;
    $offsetB = (((int) $studentId + (int) $salt) % 17) / 100000;

    return [
        -7.7956 + $offsetA,
        110.3695 + $offsetB,
        5 + (((int) $studentId + (int) $salt) % 60),
    ];
}

function attendanceTime($date, $startTime, $studentId, $salt, $lateForAlpha = false) {
    $start = new DateTime($date . ' ' . $startTime);
    $minutes = 5 + (((int) $studentId + (int) $salt) % 45);
    if ($lateForAlpha) {
        $minutes = 55 + (((int) $studentId + (int) $salt) % 20);
    }
    $start->modify('+' . $minutes . ' minutes');
    return $start->format('Y-m-d H:i:s');
}

function getOrCreateSchoolSession(PDO $pdo, $date, array &$summary) {
    $open = $date . ' 07:00:00';
    $close = $date . ' 08:00:00';

    $stmt = $pdo->prepare('SELECT id FROM presensi_sekolah_sesi WHERE waktu_buka = ? LIMIT 1');
    $stmt->execute([$open]);
    $id = $stmt->fetchColumn();
    if ($id) return (int) $id;

    $stmt = $pdo->prepare('INSERT INTO presensi_sekolah_sesi (waktu_buka, waktu_tutup, status, created_by) VALUES (?, ?, "closed", NULL)');
    $stmt->execute([$open, $close]);
    $summary['school_sessions_created']++;
    return (int) $pdo->lastInsertId();
}

function getOrCreateMapelSession(PDO $pdo, $jadwal, $date, array &$summary) {
    $open = $date . ' ' . $jadwal['jam_mulai'];
    $close = $date . ' ' . $jadwal['jam_selesai'];

    $stmt = $pdo->prepare('SELECT id FROM presensi_mapel_sesi WHERE jadwal_mata_pelajaran_id = ? AND waktu_buka = ? LIMIT 1');
    $stmt->execute([(int) $jadwal['id'], $open]);
    $id = $stmt->fetchColumn();
    if ($id) return (int) $id;

    $stmt = $pdo->prepare('INSERT INTO presensi_mapel_sesi (jadwal_mata_pelajaran_id, guru_id, waktu_buka, waktu_tutup, status, laporan_kemajuan) VALUES (?, ?, ?, ?, "closed", ?)');
    $stmt->execute([
        (int) $jadwal['id'],
        $jadwal['guru_pengampu'] !== null ? (int) $jadwal['guru_pengampu'] : null,
        $open,
        $close,
        'Pembelajaran berjalan sesuai jadwal.',
    ]);
    $summary['mapel_sessions_created']++;
    return (int) $pdo->lastInsertId();
}

$pdo->beginTransaction();

try {
    $studentWhere = targetStudentWhere($targetClassPairs);
    $studentParams = targetStudentParams($targetClassPairs);

    $stmt = $pdo->prepare('SELECT id, nama, kelas, jurusan FROM buku_induk WHERE ' . $studentWhere . ' ORDER BY kelas, jurusan, nama');
    $stmt->execute($studentParams);
    $targetStudents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach (array_keys($dates) as $dateIndex => $date) {
        $schoolSessionId = getOrCreateSchoolSession($pdo, $date, $summary);

        foreach ($targetStudents as $student) {
            $exists = $pdo->prepare('SELECT id FROM presensi_sekolah WHERE presensi_sekolah_sesi_id = ? AND user_id = ? LIMIT 1');
            $exists->execute([$schoolSessionId, (int) $student['id']]);
            $existingPresensiId = $exists->fetchColumn();

            $jenis = dummyJenis($student['id'], $dateIndex + 10);
            [$lat, $lng, $jarak] = locationFor($student['id'], $dateIndex + 10);
            $isAlpha = $jenis === 'alpha';
            $isNonLocation = $jenis !== 'hadir';
            $waktu = attendanceTime($date, '07:00:00', $student['id'], $dateIndex + 10, $isAlpha);

            $presensiValues = [
                $isNonLocation ? 0 : $lat,
                $isNonLocation ? 0 : $lng,
                $isNonLocation ? 0 : $jarak,
                'valid',
                $waktu,
                $jenis,
                alasanFor($jenis),
            ];

            if ($existingPresensiId) {
                $update = $pdo->prepare('UPDATE presensi_sekolah SET latitude = ?, longitude = ?, jarak = ?, status = ?, waktu = ?, jenis = ?, alasan = ?, foto_bukti = NULL WHERE id = ?');
                $update->execute(array_merge($presensiValues, [(int) $existingPresensiId]));
                $summary['school_presensi_updated']++;
                continue;
            }

            $insert = $pdo->prepare('INSERT INTO presensi_sekolah (presensi_sekolah_sesi_id, user_id, latitude, longitude, jarak, status, waktu, jenis, alasan, foto_bukti) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)');
            $insert->execute(array_merge([
                $schoolSessionId,
                (int) $student['id'],
            ], $presensiValues));
            $summary['school_presensi_created']++;
        }

        $classPlaceholders = qMarks($targetClassNames);
        $scheduleStmt = $pdo->prepare(
            'SELECT j.id, j.kelas_jadwal_id, j.nama_mata_pelajaran, j.guru_pengampu, j.hari, j.jam_mulai, j.jam_selesai, k.nama_kelas
             FROM jadwal_mata_pelajaran j
             INNER JOIN kelas k ON j.kelas_jadwal_id = k.id
             WHERE k.nama_kelas IN (' . $classPlaceholders . ')
               AND j.hari = ?
             ORDER BY k.nama_kelas, j.jam_mulai, j.id'
        );
        $scheduleStmt->execute(array_merge($targetClassNames, [$dates[$date]]));
        $schedules = $scheduleStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($schedules as $schedule) {
            $mapelSessionId = getOrCreateMapelSession($pdo, $schedule, $date, $summary);

            $enrolledStmt = $pdo->prepare(
                'SELECT bi.id
                 FROM jadwal_mata_pelajaran_siswa js
                 INNER JOIN buku_induk bi ON js.siswa_id = bi.id
                 WHERE js.jadwal_mata_pelajaran_id = ?
                   AND (' . $studentWhere . ')
                 ORDER BY bi.nama'
            );
            $enrolledStmt->execute(array_merge([(int) $schedule['id']], $studentParams));
            $students = $enrolledStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($students as $student) {
                $exists = $pdo->prepare('SELECT id FROM presensi_mapel WHERE presensi_sesi_id = ? AND user_id = ? LIMIT 1');
                $exists->execute([$mapelSessionId, (int) $student['id']]);
                $existingPresensiId = $exists->fetchColumn();

                $salt = (int) $schedule['id'] + $dateIndex;
                $jenis = dummyJenis($student['id'], $salt);
                [$lat, $lng, $jarak] = locationFor($student['id'], $salt);
                $isAlpha = $jenis === 'alpha';
                $isNonLocation = $jenis !== 'hadir';
                $waktu = attendanceTime($date, $schedule['jam_mulai'], $student['id'], $salt, $isAlpha);

                $presensiValues = [
                    $isNonLocation ? 0 : $lat,
                    $isNonLocation ? 0 : $lng,
                    $isNonLocation ? 0 : $jarak,
                    $isAlpha ? 'invalid' : 'valid',
                    $waktu,
                    $jenis,
                    alasanFor($jenis),
                ];

                if ($existingPresensiId) {
                    $update = $pdo->prepare('UPDATE presensi_mapel SET latitude = ?, longitude = ?, jarak = ?, status = ?, waktu = ?, jenis = ?, alasan = ?, foto_bukti = NULL WHERE id = ?');
                    $update->execute(array_merge($presensiValues, [(int) $existingPresensiId]));
                    $summary['mapel_presensi_updated']++;
                    continue;
                }

                $insert = $pdo->prepare('INSERT INTO presensi_mapel (presensi_sesi_id, user_id, jadwal_mata_pelajaran_id, latitude, longitude, jarak, status, waktu, jenis, alasan, foto_bukti) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)');
                $insert->execute(array_merge([
                    $mapelSessionId,
                    (int) $student['id'],
                    (int) $schedule['id'],
                ], $presensiValues));
                $summary['mapel_presensi_created']++;
            }
        }
    }

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
}

echo "Seed dummy presensi 18-20 Mei 2026 selesai.\n";
foreach ($summary as $key => $value) {
    echo "- {$key}: {$value}\n";
}
