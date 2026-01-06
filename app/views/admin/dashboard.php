<?php
$page_title = "Dashboard Admin";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Dashboard Overview</h2>
    <p class="text-gray-600">Statistik dan monitoring sistem presensi</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

<!--<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">-->
    <!-- Statistik Cards -->
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
        <div class="mt-4">
            <span class="text-green-600 text-sm font-medium">Aktif</span>
        </div>
    </div>

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
        <div class="mt-4">
            <span class="text-green-600 text-sm font-medium">Aktif</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Kelas</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $totalKelas ?? '0'; ?></h3>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chalkboard text-purple-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <span class="text-green-600 text-sm font-medium">Aktif</span>
        </div>
    </div>
    <!--
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Kehadiran Hari Ini</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">78%</h3>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-fingerprint text-orange-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <span class="text-green-600 text-sm font-medium">+5% dari kemarin</span>
        </div>
    </div>
-->
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
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
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
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
                        <td class="px-6 py-4 text-gray-600 capitalize">
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
                    <a href="?action=admin_dashboard&page=<?php echo $page - 1; ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
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
                        <a href="?action=admin_dashboard&page=<?php echo $i; ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <!-- Next Button -->
                <?php if ($page < $total_pages): ?>
                    <a href="?action=admin_dashboard&page=<?php echo $page + 1; ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
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

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Grafik Kehadiran -->
     <!--
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Grafik Kehadiran Bulan Ini</h3>
            <select class="text-sm border border-gray-300 rounded-lg px-3 py-1">
                <option>Bulan Ini</option>
                <option>Bulan Lalu</option>
            </select>
        </div>
        <div class="h-80">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>
-->
    <!-- Pie Chart Status Kehadiran -->
     <!--
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Status Kehadiran Hari Ini</h3>
        <div class="h-80">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
-->
</div>

<div class="grid grid-cols-1 lg:grid-cols-1 gap-8">
    <!-- Aktivitas Terbaru -->
     <!--
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Terbaru</h3>
        <div class="space-y-4">
            <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-check text-blue-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">Siswa A melakukan presensi</p>
                    <p class="text-xs text-gray-500">2 menit yang lalu</p>
                </div>
            </div>
            <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher text-green-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">Guru B membuka presensi kelas</p>
                    <p class="text-xs text-gray-500">15 menit yang lalu</p>
                </div>
            </div>
            <div class="flex items-start space-x-3 p-3 bg-purple-50 rounded-lg">
                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-plus text-purple-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">Admin menambahkan user baru</p>
                    <p class="text-xs text-gray-500">1 jam yang lalu</p>
                </div>
            </div>
        </div>
    </div>
-->
    <!-- Quick Actions -->
     <!--
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 gap-4">
            <a href="index.php?action=admin_users" class="p-8 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 text-center transition-colors">
                <i class="fas fa-users text-blue-600 text-2xl mb-2"></i>
                <p class="text-sm font-medium text-blue-800">Kelola User</p>
            </a>
            <a href="index.php?action=admin_kelas" class="p-8 bg-green-50 hover:bg-green-100 rounded-lg border border-green-200 text-center transition-colors">
                <i class="fas fa-chalkboard text-green-600 text-2xl mb-2"></i>
                <p class="text-sm font-medium text-green-800">Data Kelas</p>
            </a>
            <a href="index.php?action=admin_lokasi" class="p-8 bg-orange-50 hover:bg-orange-100 rounded-lg border border-orange-200 text-center transition-colors">
                <i class="fas fa-map-marker-alt text-orange-600 text-2xl mb-2"></i>
                <p class="text-sm font-medium text-orange-800">Lokasi</p>
            </a>
            <a href="index.php?action=admin_laporan" class="p-8 bg-purple-50 hover:bg-purple-100 rounded-lg border border-purple-200 text-center transition-colors">
                <i class="fas fa-chart-bar text-purple-600 text-2xl mb-2"></i>
                <p class="text-sm font-medium text-purple-800">Laporan</p>
            </a>
        </div>
    </div>
-->
</div>

<script>
// Grafik Kehadiran
const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
const attendanceChart = new Chart(attendanceCtx, {
    type: 'line',
    data: {
        labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
        datasets: [{
            label: 'Presentase Kehadiran',
            data: [75, 82, 78, 85],
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        }
    }
});

// Pie Chart Status
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
        datasets: [{
            data: [78, 12, 6, 4],
            backgroundColor: [
                '#10b981',
                '#f59e0b',
                '#ef4444',
                '#6b7280'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

function kelola() {
    window.location.href = "<?php echo BASE_URL; ?>/public/index.php?action=admin_presensi_sekolah";
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>