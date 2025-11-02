<?php
$page_title = "Dashboard Guru";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Dashboard Guru</h2>
    <p class="text-gray-600">Monitor kelas dan presensi siswa</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Statistik Cards -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Kelas</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo count($kelasSaya); ?></h3>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chalkboard text-blue-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <span class="text-green-600 text-sm font-medium">Aktif</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Siswa</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $totalSiswa; ?></h3>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-green-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <span class="text-green-600 text-sm font-medium">Terdaftar</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Kehadiran Hari Ini</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">82%</h3>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-fingerprint text-orange-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <span class="text-green-600 text-sm font-medium">+3% dari kemarin</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Presensi Aktif</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">2</h3>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-purple-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <span class="text-green-600 text-sm font-medium">Kelas dibuka</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Kelas Saya -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Kelas Saya</h3>
            <a href="index.php?action=guru_kelas" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Lihat Semua →
            </a>
        </div>
        <div class="space-y-4">
            <?php foreach(array_slice($kelasSaya, 0, 3) as $kelas): ?>
            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chalkboard text-blue-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800"><?php echo $kelas->nama_kelas; ?></h4>
                        <p class="text-sm text-gray-600"><?php echo $kelas->tahun_ajaran; ?></p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        Aktif
                    </span>
                    <p class="text-sm text-gray-600 mt-1"><?php echo count($this->kelasModel->getSiswaInKelas($kelas->id)); ?> siswa</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Grafik Kehadiran -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Kehadiran Minggu Ini</h3>
        <div class="h-64">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>
</div>

<!-- Aktivitas Terbaru -->
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Presensi Terbaru</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Siswa</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Kelas</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Waktu</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Lokasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <tr>
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-blue-600 text-sm"></i>
                            </div>
                            <span class="font-medium text-gray-800">Siswa A</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">XI RPL 1</td>
                    <td class="px-4 py-3 text-gray-600">07:45</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Hadir
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">Valid</td>
                </tr>
                <tr>
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-blue-600 text-sm"></i>
                            </div>
                            <span class="font-medium text-gray-800">Siswa B</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">XI RPL 1</td>
                    <td class="px-4 py-3 text-gray-600">07:50</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Hadir
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">Valid</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
// Grafik Kehadiran untuk Guru
const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
const attendanceChart = new Chart(attendanceCtx, {
    type: 'bar',
    data: {
        labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
        datasets: [{
            label: 'Presentase Kehadiran',
            data: [85, 88, 82, 90, 78],
            backgroundColor: '#3b82f6',
            borderColor: '#3b82f6',
            borderWidth: 1
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
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>