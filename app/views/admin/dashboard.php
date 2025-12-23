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
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>