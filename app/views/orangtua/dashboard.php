<?php
$page_title = "Dashboard Orang Tua";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Monitoring Anak</h2>
    <p class="text-gray-600">Pantau kehadiran dan aktivitas anak di sekolah</p>
</div>

<?php if(empty($dataAnak)): ?>
<div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
    <i class="fas fa-exclamation-triangle text-yellow-600 text-3xl mb-3"></i>
    <h3 class="text-lg font-semibold text-yellow-800 mb-2">Belum Terhubung dengan Siswa</h3>
    <p class="text-yellow-700">Silakan hubungi administrator untuk menghubungkan akun dengan siswa.</p>
</div>
<?php else: ?>

<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
    <?php foreach($dataAnak as $anak): ?>
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <!-- Header Info Anak -->
        <div class="flex items-center space-x-4 mb-4">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user-graduate text-blue-600 text-2xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-800 text-lg"><?php echo $anak['siswa']->nama; ?></h3>
                <p class="text-gray-600 text-sm">
                    <?php 
                    if(!empty($anak['kelas'])) {
                        echo $anak['kelas'][0]->nama_kelas;
                    } else {
                        echo 'Kelas belum ditentukan';
                    }
                    ?>
                </p>
            </div>
        </div>

        <!-- Statistik Kehadiran -->
        <div class="space-y-3 mb-4">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Kehadiran Bulan Ini:</span>
                <span class="font-semibold text-green-600">
                    <?php echo $anak['statistik']->hadir ?? '0'; ?> hari
                </span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Izin:</span>
                <span class="font-semibold text-yellow-600">
                    <?php echo $anak['statistik']->izin ?? '0'; ?> hari
                </span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Sakit:</span>
                <span class="font-semibold text-orange-600">
                    <?php echo $anak['statistik']->sakit ?? '0'; ?> hari
                </span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Alpha:</span>
                <span class="font-semibold text-red-600">
                    <?php echo $anak['statistik']->alpha ?? '0'; ?> hari
                </span>
            </div>
        </div>

        <!-- Presentase Kehadiran -->
        <div class="mb-4">
            <div class="flex justify-between text-sm mb-1">
                <span class="text-gray-600">Presentase Kehadiran:</span>
                <span class="font-semibold text-gray-800">
                    <?php 
                    $total = $anak['statistik']->total ?? 1;
                    $hadir = $anak['statistik']->hadir ?? 0;
                    echo $total > 0 ? round(($hadir / $total) * 100) : 0;
                    ?>%
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-600 h-2 rounded-full" 
                     style="width: <?php echo $total > 0 ? ($hadir / $total) * 100 : 0; ?>%"></div>
            </div>
        </div>

        <!-- Status Hari Ini -->
        <div class="p-3 bg-gray-50 rounded-lg mb-4">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Status Hari Ini:</span>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    <i class="fas fa-clock mr-1"></i>Belum Presensi
                </span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-2">
            <button onclick="lihatDetail(<?php echo $anak['siswa']->id; ?>)" 
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-lg transition-colors text-sm font-medium">
                <i class="fas fa-eye mr-1"></i>Detail
            </button>
            <button onclick="lihatLaporan(<?php echo $anak['siswa']->id; ?>)" 
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-3 rounded-lg transition-colors text-sm font-medium">
                <i class="fas fa-chart-bar mr-1"></i>Laporan
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Riwayat Presensi Terbaru -->
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Presensi Terbaru</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Nama Siswa</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Tanggal</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Waktu</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach($dataAnak as $anak): ?>
                    <?php foreach(array_slice($anak['presensi_terakhir'], 0, 2) as $presensi): ?>
                    <tr>
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                                <span class="font-medium text-gray-800"><?php echo $anak['siswa']->nama; ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600"><?php echo date('d M Y', strtotime($presensi->waktu)); ?></td>
                        <td class="px-4 py-3 text-gray-600"><?php echo date('H:i', strtotime($presensi->waktu)); ?></td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                <?php echo $presensi->status == 'valid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo $presensi->status == 'valid' ? 'Hadir' : 'Tidak Valid'; ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            <?php echo $presensi->jenis == 'hadir' ? 'Presensi Sekolah' : ucfirst($presensi->jenis); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Grafik Kehadiran -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Grafik Kehadiran Bulan Ini</h3>
        <div class="h-64">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Statistik Kehadiran</h3>
        <div class="h-64">
            <canvas id="statisticsChart"></canvas>
        </div>
    </div>
</div>

<?php endif; ?>

<!-- Modal Detail -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Detail Kehadiran</h3>
            <p class="text-gray-600 text-sm" id="detailSiswaInfo"></p>
        </div>
        <div class="p-6 overflow-y-auto max-h-96">
            <div id="detailContent">
                <!-- Konten detail akan dimuat di sini -->
            </div>
        </div>
        <div class="p-6 border-t border-gray-200 flex justify-end">
            <button onclick="closeDetailModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
function lihatDetail(siswaId) {
    fetch(`index.php?action=get_detail_anak&siswa_id=${siswaId}`)
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            showNotification('error', data.error);
            return;
        }
        
        document.getElementById('detailSiswaInfo').textContent = 'Detail kehadiran siswa';
        document.getElementById('detailContent').innerHTML = `
            <div class="space-y-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-blue-600">${data.statistik.hadir || 0}</div>
                        <div class="text-sm text-blue-800">Hadir</div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-yellow-600">${data.statistik.izin || 0}</div>
                        <div class="text-sm text-yellow-800">Izin</div>
                    </div>
                    <div class="bg-orange-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-orange-600">${data.statistik.sakit || 0}</div>
                        <div class="text-sm text-orange-800">Sakit</div>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-red-600">${data.statistik.alpha || 0}</div>
                        <div class="text-sm text-red-800">Alpha</div>
                    </div>
                </div>
                
                <h4 class="font-semibold text-gray-800 mt-6">Riwayat Presensi Terbaru</h4>
                <div class="space-y-2">
                    ${data.presensi.slice(0, 10).map(presensi => `
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-800">${new Date(presensi.waktu).toLocaleDateString('id-ID')}</p>
                                <p class="text-sm text-gray-600">${new Date(presensi.waktu).toLocaleTimeString('id-ID')}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${
                                presensi.status === 'valid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                            }">
                                ${presensi.status === 'valid' ? 'Valid' : 'Invalid'}
                            </span>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
        
        document.getElementById('detailModal').classList.remove('hidden');
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Gagal memuat data detail!');
    });
}

function lihatLaporan(siswaId) {
    fetch(`index.php?action=get_laporan_mingguan&siswa_id=${siswaId}`)
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            showNotification('error', data.error);
            return;
        }
        
        document.getElementById('detailSiswaInfo').textContent = `Laporan Mingguan - ${data.periode}`;
        document.getElementById('detailContent').innerHTML = `
            <div class="space-y-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-green-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-green-600">${data.hadir}</div>
                        <div class="text-sm text-green-800">Hadir</div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-yellow-600">${data.izin}</div>
                        <div class="text-sm text-yellow-800">Izin</div>
                    </div>
                    <div class="bg-orange-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-orange-600">${data.sakit}</div>
                        <div class="text-sm text-orange-800">Sakit</div>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-red-600">${data.alpha}</div>
                        <div class="text-sm text-red-800">Alpha</div>
                    </div>
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-blue-800 mb-2">Ringkasan</h4>
                    <div class="space-y-2 text-sm text-blue-700">
                        <div class="flex justify-between">
                            <span>Total Hari:</span>
                            <span class="font-medium">${data.total_hari} hari</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Presentase Kehadiran:</span>
                            <span class="font-medium">${data.presentase}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Periode:</span>
                            <span class="font-medium">${data.periode}</span>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <button onclick="cetakLaporan(${siswaId})" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-print mr-2"></i>Cetak Laporan
                    </button>
                </div>
            </div>
        `;
        
        document.getElementById('detailModal').classList.remove('hidden');
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Gagal memuat laporan!');
    });
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
}

function cetakLaporan(siswaId) {
    window.print();
}

function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Grafik untuk Orang Tua
const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
const attendanceChart = new Chart(attendanceCtx, {
    type: 'line',
    data: {
        labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
        datasets: [{
            label: 'Presentase Kehadiran',
            data: [85, 90, 88, 92],
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

const statisticsCtx = document.getElementById('statisticsChart').getContext('2d');
const statisticsChart = new Chart(statisticsCtx, {
    type: 'doughnut',
    data: {
        labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
        datasets: [{
            data: [18, 2, 1, 0],
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

// Close modal when clicking outside
document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDetailModal();
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>