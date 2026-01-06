<?php
$page_title = "Dashboard Admin Kesiswaan";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Ringkasan Kesiswaan</h2>
    <p class="text-gray-600">Pantau data siswa dan sesi presensi sekolah</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Siswa</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $totalSiswa ?? '0'; ?></h3>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    <!--
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Guru</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $totalGuru ?? '0'; ?></h3>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chalkboard-teacher text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
-->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Jumlah Sesi Presensi</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo isset($sessions) ? count($sessions) : 0; ?></h3>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chalkboard text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">Sesi Presensi Terbaru</h3>
        <div class="flex space-x-2">
            <button onclick="kelola()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-gear"></i>
                <span>Kelola</span>
            </button>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">ID</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Waktu Buka</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Waktu Tutup</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($sessions)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Tidak ada data sesi presensi terbaru</p>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach(array_slice($sessions, $offset, 5) as $s): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div>
                                    <span class="font-medium text-gray-800"><?php echo $s->id; ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            <?php echo $s->waktu_buka; ?>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            <?php echo $s->waktu_tutup; ?>
                        </td>
                        <td class="px-6 py-4 text-gray-600-700 capitalize">
                            <?php echo $s->status; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="p-6 border-t border-gray-200 flex justify-between items-center">
        <div class="text-sm text-gray-600">
            <?php 
            $start = min($offset + 1, $total_records);
            $end = min($offset + 5, $total_records);
            ?>
            Menampilkan <?php echo $start; ?>-<?php echo $end; ?> dari <?php echo $total_records; ?> data
        </div>
        <div class="flex space-x-2">
            <?php if ($total_pages > 1): ?>
                <!-- Previous Button -->
                <?php if ($page > 1): ?>
                    <a href="?action=admin_kesiswaan_dashboard&page=<?php echo $page - 1; ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php else: ?>
                    <span class="px-3 py-2 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                <?php endif; ?>

                <!-- Page Numbers -->
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++): 
                ?>
                    <?php if ($i == $page): ?>
                        <span class="px-3 py-2 bg-blue-600 text-white rounded-lg"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?action=admin_kesiswaan_dashboard&page=<?php echo $i; ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <!-- Next Button -->
                <?php if ($page < $total_pages): ?>
                    <a href="?action=admin_kesiswaan_dashboard&page=<?php echo $page + 1; ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php else: ?>
                    <span class="px-3 py-2 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                <?php endif; ?>
            <?php else: ?>
                <span class="text-gray-500">-</span>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
function kelola() {
    window.location.href = "<?php echo BASE_URL; ?>/public/index.php?action=admin_kesiswaan_presensi_sekolah";
}
</script>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
