<?php
// Scheduler presensi mata pelajaran.
// Jalankan via CLI, misalnya dari Windows Task Scheduler setiap 1 menit.

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/PresensiSesiModel.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "Forbidden\n";
    exit(1);
}

$model = new PresensiSesiModel();
$closedCount = $model->closeExpiredSessions();

echo sprintf(
    "[%s] Menutup sesi presensi mapel kedaluwarsa: %d sesi.\n",
    date('Y-m-d H:i:s T'),
    $closedCount
);
