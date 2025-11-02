<?php
$page_title = "Laporan Kelas";
require_once __DIR__ . '/../layouts/header.php';

$kelas_id = $_GET['kelas_id'] ?? null;
$selected_kelas = null;

if ($kelas_id) {
    foreach($kelasSaya as $kelas) {
        if ($kelas->id == $kelas_id) {
            $selected_kelas = $kelas;
            break;
        }
    }
}
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Laporan Kelas</h2>
    <p class="text-gray-600">Monitoring dan analisis kehadiran siswa per kelas</p>
</div>

<!-- Pilih Kelas -->
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Pilih Kelas</h3>
    <div class="flex flex-wrap gap-3">
        <?php foreach($kelasSaya as $kelas): ?>
            <a href="index.php?action=guru_laporan&kelas_id=<?php echo $kelas->id; ?>" 
               class="px-4 py-2 rounded-lg border transition-colors <?php echo $selected_kelas && $selected_kelas->id == $kelas->id ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'; ?>">
                <?php echo htmlspecialchars($kelas->nama_kelas); ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php if($selected_kelas): ?>
<!-- Statistik Kelas -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-users text-green-600 text-xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo count($selected_kelas->siswa ?? []); ?></h3>
        <p class="text-gray-600 text-sm">Total Siswa</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-user-check text-blue-600 text-xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1">
            <?php 
            $hadir = 0;
            if(isset($laporan[$selected_kelas->id]['presensi'])) {
                foreach($laporan[$selected_kelas->id]['presensi'] as $presensi) {
                    if($presensi->status == 'valid') $hadir++;
                }
            }
            echo $hadir;
            ?>
        </h3>
        <p class="text-gray-600 text-sm">Hadir Hari Ini</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-user-clock text-yellow-600 text-xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1">
            <?php 
            $tidakHadir = count($selected_kelas->siswa ?? []) - $hadir;
            echo $tidakHadir > 0 ? $tidakHadir : 0;
            ?>
        </h3>
        <p class="text-gray-600 text-sm">Tidak Hadir</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-percentage text-purple-600 text-xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1">
            <?php 
            $totalSiswa = count($selected_kelas->siswa ?? []);
            $presentase = $totalSiswa > 0 ? round(($hadir / $totalSiswa) * 100) : 0;
            echo $presentase;
            ?>%
        </h3>
        <p class="text-gray-600 text-sm">Presentase</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Grafik Kehadiran -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Kehadiran 7 Hari Terakhir</h3>
        <div class="h-64">
            <canvas id="weeklyAttendanceChart"></canvas>
        </div>
    </div>

    <!-- Status Kehadiran -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Status Kehadiran Hari Ini</h3>
        <div class="h-64">
            <canvas id="attendanceStatusChart"></canvas>
        </div>
    </div>
</div>

<!-- Daftar Presensi -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">Daftar Presensi - <?php echo htmlspecialchars($selected_kelas->nama_kelas); ?></h3>
        <div class="flex space-x-2">
            <button onclick="cetakLaporan()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-print"></i>
                <span>Cetak Laporan</span>
            </button>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Nama Siswa</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Status</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Waktu Presensi</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Lokasi</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if(isset($laporan[$selected_kelas->id]['presensi'])): ?>
                    <?php foreach($laporan[$selected_kelas->id]['presensi'] as $index => $presensi): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                                <span class="font-medium text-gray-800"><?php echo htmlspecialchars($presensi->nama ?? 'Siswa ' . ($index + 1)); ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                <?php echo $presensi->status == 'valid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo $presensi->status == 'valid' ? 'Hadir' : 'Tidak Hadir'; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            <?php echo $presensi->waktu ? date('H:i', strtotime($presensi->waktu)) : '-'; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($presensi->status == 'valid'): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Valid
                                </span>
                            <?php else: ?>
                                <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            <?php echo $presensi->jenis ? ucfirst($presensi->jenis) : 'Presensi Sekolah'; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-2 text-gray-300"></i>
                            <p>Belum ada data presensi untuk hari ini</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php else: ?>
<!-- Placeholder when no class selected -->
<div class="bg-white rounded-xl shadow-sm p-12 border border-gray-100 text-center">
    <i class="fas fa-chalkboard-teacher text-4xl text-gray-300 mb-4"></i>
    <h3 class="text-xl font-semibold text-gray-600 mb-2">Pilih Kelas</h3>
    <p class="text-gray-500">Silakan pilih kelas terlebih dahulu untuk melihat laporan</p>
</div>
<?php endif; ?>

<script>
// Grafik Kehadiran Mingguan
const weeklyCtx = document.getElementById('weeklyAttendanceChart').getContext('2d');
const weeklyChart = new Chart(weeklyCtx, {
    type: 'bar',
    data: {
        labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
        datasets: [{
            label: 'Presentase Kehadiran',
            data: [85, 88, 82, 90, 78, 0, 0],
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

// Grafik Status Kehadiran
const statusCtx = document.getElementById('attendanceStatusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
        datasets: [{
            data: [<?php echo $hadir; ?>, 2, 1, <?php echo $tidakHadir - 3 > 0 ? $tidakHadir - 3 : 0; ?>],
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

function cetakLaporan() {
    showNotification('info', 'Mempersiapkan laporan untuk dicetak...');
    
    // Simulate print preparation
    setTimeout(() => {
        window.print();
        showNotification('success', 'Laporan siap dicetak!');
    }, 1000);
}

function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-blue-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas fa-${type === 'success' ? 'check' : 'info'}-circle"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Print styles
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        background: white !important;
    }
    
    .bg-gray-50 {
        background: white !important;
    }
    
    .shadow-sm, .shadow-lg {
        box-shadow: none !important;
    }
    
    .border {
        border: 1px solid #000 !important;
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>