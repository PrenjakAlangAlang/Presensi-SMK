<?php
$page_title = "Dashboard Admin Kesiswaan";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Ringkasan Kesiswaan</h2>
    <p class="text-gray-600">Pantau data siswa dan sesi presensi sekolah</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <p class="text-gray-500 text-sm">Total Siswa</p>
        <h3 class="text-3xl font-bold text-gray-800 mt-2"><?php echo $totalSiswa ?? 0; ?></h3>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <p class="text-gray-500 text-sm">Total Guru</p>
        <h3 class="text-3xl font-bold text-gray-800 mt-2"><?php echo $totalGuru ?? 0; ?></h3>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <p class="text-gray-500 text-sm">Jumlah Sesi Presensi</p>
        <h3 class="text-3xl font-bold text-gray-800 mt-2"><?php echo isset($sessions) ? count($sessions) : 0; ?></h3>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Sesi Presensi Terbaru</h3>
            <p class="text-sm text-gray-500">Riwayat 5 sesi terakhir</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/public/index.php?action=admin_kesiswaan_presensi_sekolah" class="text-blue-600 hover:text-blue-700 text-sm">Kelola</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-gray-600">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Waktu Buka</th>
                    <th class="px-4 py-2">Waktu Tutup</th>
                    <th class="px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if(!empty($sessions)): foreach(array_slice($sessions,0,5) as $s): ?>
                    <tr>
                        <td class="px-4 py-2"><?php echo $s->id; ?></td>
                        <td class="px-4 py-2"><?php echo $s->waktu_buka; ?></td>
                        <td class="px-4 py-2"><?php echo $s->waktu_tutup; ?></td>
                        <td class="px-4 py-2 capitalize"><?php echo $s->status; ?></td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="4" class="px-4 py-3 text-gray-500">Belum ada sesi presensi.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
