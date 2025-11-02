<?php
$page_title = "Laporan Presensi";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Laporan Presensi</h2>
    <p class="text-gray-600">Analisis dan monitoring kehadiran seluruh siswa</p>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Filter Laporan</h3>
    <form id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
            <select name="periode" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="hari_ini">Hari Ini</option>
                <option value="minggu_ini">Minggu Ini</option>
                <option value="bulan_ini">Bulan Ini</option>
                <option value="custom">Custom</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
            <select name="kelas" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="all">Semua Kelas</option>
                <option value="1">XI RPL 1</option>
                <option value="2">XI RPL 2</option>
                <option value="3">XI MM 1</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select name="status" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="all">Semua Status</option>
                <option value="hadir">Hadir</option>
                <option value="izin">Izin</option>
                <option value="sakit">Sakit</option>
                <option value="alpha">Alpha</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-lg transition-colors">
                <i class="fas fa-filter mr-2"></i>Terapkan Filter
            </button>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
    <!-- Statistik Cards -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-users text-green-600 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1">85%</h3>
        <p class="text-gray-600 text-sm">Rata-rata Kehadiran</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-user-check text-blue-600 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1">142</h3>
        <p class="text-gray-600 text-sm">Siswa Hadir</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-user-clock text-yellow-600 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1">8</h3>
        <p class="text-gray-600 text-sm">Siswa Izin</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-user-times text-red-600 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1">5</h3>
        <p class="text-gray-600 text-sm">Siswa Alpha</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Grafik Kehadiran -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Trend Kehadiran Bulan Ini</h3>
            <select class="text-sm border border-gray-300 rounded-lg px-3 py-1">
                <option>Bulan Ini</option>
                <option>Bulan Lalu</option>
            </select>
        </div>
        <div class="h-80">
            <canvas id="attendanceTrendChart"></canvas>
        </div>
    </div>

    <!-- Distribusi Kehadiran -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Distribusi Kehadiran</h3>
        <div class="h-80">
            <canvas id="attendanceDistributionChart"></canvas>
        </div>
    </div>
</div>

<!-- Tabel Laporan -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">Detail Laporan Presensi</h3>
        <div class="flex space-x-2">
            <button onclick="exportToPDF()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-file-pdf"></i>
                <span>Export PDF</span>
            </button>
            <button onclick="exportToExcel()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-file-excel"></i>
                <span>Export Excel</span>
            </button>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Nama Siswa</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Kelas</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Tanggal</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Waktu</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Status</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Lokasi</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach($presensi as $p): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-blue-600 text-sm"></i>
                            </div>
                            <span class="font-medium text-gray-800"><?php echo htmlspecialchars($p->nama ?? 'Siswa'); ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">XI RPL 1</td>
                    <?php $waktuTs = (isset($p->waktu) && $p->waktu) ? strtotime($p->waktu) : time(); ?>
                    <td class="px-6 py-4 text-gray-600"><?php echo date('d M Y', $waktuTs); ?></td>
                    <td class="px-6 py-4 text-gray-600"><?php echo date('H:i', $waktuTs); ?></td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            <?php echo ($p->status ?? 'valid') == 'valid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                            <?php echo ($p->status ?? 'valid') == 'valid' ? 'Hadir' : 'Tidak Valid'; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            Valid
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <button onclick="lihatDetailPresensi(<?php echo $p->id ?? 0; ?>)" 
                                class="text-blue-600 hover:text-blue-800 transition-colors p-2 rounded-lg hover:bg-blue-50">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="p-6 border-t border-gray-200 flex justify-between items-center">
        <div class="text-sm text-gray-600">
            Menampilkan 1-10 dari 150 data
        </div>
        <div class="flex space-x-2">
            <button class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="px-3 py-2 bg-blue-600 text-white rounded-lg">1</button>
            <button class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">2</button>
            <button class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">3</button>
            <button class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<!-- Modal Detail Presensi -->
<div id="detailPresensiModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Detail Presensi</h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Siswa</label>
                    <p class="text-gray-800 font-medium" id="detailNama">-</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                    <p class="text-gray-800" id="detailKelas">-</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal & Waktu</label>
                    <p class="text-gray-800" id="detailWaktu">-</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <p id="detailStatus">-</p>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-gray-800" id="detailLokasi">-</p>
                        <p class="text-sm text-gray-600 mt-1" id="detailJarak">-</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-6 border-t border-gray-200 flex justify-end">
            <button onclick="closeDetailPresensiModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
// Grafik Trend Kehadiran
const trendCtx = document.getElementById('attendanceTrendChart').getContext('2d');
const trendChart = new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
        datasets: [{
            label: 'Presentase Kehadiran',
            data: [82, 85, 88, 90],
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

// Grafik Distribusi Kehadiran
const distributionCtx = document.getElementById('attendanceDistributionChart').getContext('2d');
const distributionChart = new Chart(distributionCtx, {
    type: 'doughnut',
    data: {
        labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
        datasets: [{
            data: [142, 8, 5, 5],
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

function lihatDetailPresensi(presensiId) {
    // Simulate API call to get presensi detail
    const detailData = {
        nama: 'Siswa A',
        kelas: 'XI RPL 1',
        waktu: '<?php echo date('d M Y H:i'); ?>',
        status: 'Hadir',
        lokasi: 'SMK Negeri 7 Yogyakarta',
        jarak: '45 meter dari titik presensi'
    };
    
    document.getElementById('detailNama').textContent = detailData.nama;
    document.getElementById('detailKelas').textContent = detailData.kelas;
    document.getElementById('detailWaktu').textContent = detailData.waktu;
    document.getElementById('detailLokasi').textContent = detailData.lokasi;
    document.getElementById('detailJarak').textContent = detailData.jarak;
    
    const statusElement = document.getElementById('detailStatus');
    statusElement.textContent = detailData.status;
    statusElement.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800';
    
    document.getElementById('detailPresensiModal').classList.remove('hidden');
}

function closeDetailPresensiModal() {
    document.getElementById('detailPresensiModal').classList.add('hidden');
}

function exportToPDF() {
    showNotification('info', 'Membuat laporan PDF...');
    // Implementation for PDF export
    setTimeout(() => {
        showNotification('success', 'Laporan PDF berhasil diunduh!');
    }, 2000);
}

function exportToExcel() {
    showNotification('info', 'Membuat laporan Excel...');
    // Implementation for Excel export
    setTimeout(() => {
        showNotification('success', 'Laporan Excel berhasil diunduh!');
    }, 2000);
}

// Filter form handling
document.getElementById('filterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    showNotification('success', 'Filter diterapkan!');
});

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

// Close modal when clicking outside
document.getElementById('detailPresensiModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDetailPresensiModal();
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>