<?php
$page_title = "Riwayat Presensi";
require_once __DIR__ . '/../layouts/header.php';

// Get current filter values
$periode = $_GET['periode'] ?? 'bulanan';
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$minggu = $_GET['minggu'] ?? date('W');
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Riwayat Presensi</h2>
    <p class="text-gray-600">Lihat history kehadiran Anda di sekolah dan kelas</p>
</div>

<!-- Filter Periode -->
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Pilih Periode -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
            <select id="periodeSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="harian" <?php echo $periode === 'harian' ? 'selected' : ''; ?>>Harian</option>
                <option value="mingguan" <?php echo $periode === 'mingguan' ? 'selected' : ''; ?>>Mingguan</option>
                <option value="bulanan" <?php echo $periode === 'bulanan' ? 'selected' : ''; ?>>Bulanan</option>
            </select>
        </div>
        
        <!-- Filter Harian -->
        <div id="filterHarian" class="<?php echo $periode !== 'harian' ? 'hidden' : ''; ?>">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
            <input type="date" id="tanggalInput" value="<?php echo $tanggal; ?>" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <!-- Filter Mingguan -->
        <div id="filterMingguan" class="<?php echo $periode !== 'mingguan' ? 'hidden' : ''; ?> md:col-span-2">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Minggu</label>
                    <input type="number" id="mingguInput" value="<?php echo $minggu; ?>" min="1" max="53"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                    <input type="number" id="tahunMingguInput" value="<?php echo $tahun; ?>" min="2020" max="2099"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>
        
        <!-- Filter Bulanan -->
        <div id="filterBulanan" class="<?php echo $periode !== 'bulanan' ? 'hidden' : ''; ?> md:col-span-2">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                    <select id="bulanInput" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <?php
                        $bulan_names = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        for ($i = 1; $i <= 12; $i++) {
                            $selected = $bulan == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '';
                            echo "<option value='" . str_pad($i, 2, '0', STR_PAD_LEFT) . "' $selected>" . $bulan_names[$i-1] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                    <input type="number" id="tahunBulanInput" value="<?php echo $tahun; ?>" min="2020" max="2099"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>
        
        <!-- Tombol Filter -->
        <div class="flex items-end">
            <button onclick="applyFilter()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors flex items-center justify-center">
                <i class="fas fa-filter mr-2"></i>Terapkan
            </button>
        </div>
    </div>
</div>

<!-- Statistik Ringkas -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-calendar-check text-green-600 text-xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo $statistik->hadir ?? '0'; ?></h3>
        <p class="text-gray-600 text-sm">Hadir</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-envelope text-yellow-600 text-xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo $statistik->izin ?? '0'; ?></h3>
        <p class="text-gray-600 text-sm">Izin</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-first-aid text-orange-600 text-xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo $statistik->sakit ?? '0'; ?></h3>
        <p class="text-gray-600 text-sm">Sakit</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-times text-red-600 text-xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo $statistik->alpha ?? '0'; ?></h3>
        <p class="text-gray-600 text-sm">Alpha</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Grafik Kehadiran -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">
            Grafik Kehadiran 
            <?php 
            if ($periode === 'harian') {
                echo date('d M Y', strtotime($tanggal));
            } elseif ($periode === 'mingguan') {
                echo 'Minggu ' . $minggu . ' Tahun ' . $tahun;
            } else {
                $bulan_names = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                echo $bulan_names[$bulan - 1] . ' ' . $tahun;
            }
            ?>
        </h3>
        <div class="h-64">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <!-- Presentase Kehadiran -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Presentase Kehadiran</h3>
        <div class="h-64">
            <canvas id="percentageChart"></canvas>
        </div>
    </div>
</div>

<!-- Tabs untuk Riwayat -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <div class="border-b border-gray-200">
        <nav class="flex -mb-px">
            <button onclick="switchTab('sekolah')" 
                    id="tab-sekolah" 
                    class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors border-blue-500 text-blue-600">
                <i class="fas fa-school mr-2"></i>Presensi Sekolah
            </button>
            <button onclick="switchTab('kelas')" 
                    id="tab-kelas" 
                    class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                <i class="fas fa-chalkboard mr-2"></i>Presensi Kelas
            </button>
        </nav>
    </div>

    <!-- Content untuk Presensi Sekolah -->
    <div id="content-sekolah" class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Tanggal</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Waktu</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Lokasi</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if(!empty($presensiSekolah)): ?>
                        <?php foreach($presensiSekolah as $presensi): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-gray-600">
                                <?php echo date('d M Y', strtotime($presensi->waktu)); ?>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                <?php echo date('H:i', strtotime($presensi->waktu)); ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    <?php echo $presensi->status == 'valid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $presensi->status == 'valid' ? 'Valid' : 'Invalid'; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <?php if($presensi->status == 'valid'): ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        Dalam Radius
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                <?php 
                                $jenis = $presensi->jenis ?? 'hadir';
                                echo $jenis == 'hadir' ? 'Presensi Sekolah' : ucfirst($jenis);
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-3xl mb-2 text-gray-300"></i>
                                <p>Belum ada riwayat presensi sekolah</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Content untuk Presensi Kelas -->
    <div id="content-kelas" class="p-6 hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Tanggal</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Kelas</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Waktu</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Lokasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if(!empty($presensiKelas)): ?>
                        <?php foreach($presensiKelas as $presensi): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-gray-600">
                                <?php echo date('d M Y', strtotime($presensi->waktu)); ?>
                            </td>
                            <td class="px-4 py-3 text-gray-800 font-medium">
                                <?php echo htmlspecialchars($presensi->nama_kelas ?? 'Kelas'); ?>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                <?php echo date('H:i', strtotime($presensi->waktu)); ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    <?php echo $presensi->status == 'valid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $presensi->status == 'valid' ? 'Valid' : 'Invalid'; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <?php if($presensi->status == 'valid'): ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        Valid
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-3xl mb-2 text-gray-300"></i>
                                <p>Belum ada riwayat presensi kelas</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Ringkasan Bulanan -->
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        Ringkasan 
        <?php 
        if ($periode === 'harian') {
            echo date('d M Y', strtotime($tanggal));
        } elseif ($periode === 'mingguan') {
            echo 'Minggu ' . $minggu . ' Tahun ' . $tahun;
        } else {
            $bulan_names = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            echo $bulan_names[$bulan - 1] . ' ' . $tahun;
        }
        ?>
    </h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="text-center p-4 bg-gray-50 rounded-lg">
            <div class="text-2xl font-bold text-blue-600"><?php echo $statistik->hadir ?? '0'; ?></div>
            <div class="text-sm text-gray-600">Hari Hadir</div>
        </div>
        <div class="text-center p-4 bg-gray-50 rounded-lg">
            <div class="text-2xl font-bold text-green-600">
                <?php 
                $total = $statistik->total ?? 1;
                $hadir = $statistik->hadir ?? 0;
                echo $total > 0 ? round(($hadir / $total) * 100) : 0;
                ?>%
            </div>
            <div class="text-sm text-gray-600">Presentase</div>
        </div>
        <div class="text-center p-4 bg-gray-50 rounded-lg">
            <div class="text-2xl font-bold text-yellow-600"><?php echo ($statistik->izin ?? 0) + ($statistik->sakit ?? 0); ?></div>
            <div class="text-sm text-gray-600">Izin & Sakit</div>
        </div>
        <div class="text-center p-4 bg-gray-50 rounded-lg">
            <div class="text-2xl font-bold text-red-600"><?php echo $statistik->alpha ?? '0'; ?></div>
            <div class="text-sm text-gray-600">Tidak Hadir</div>
        </div>
    </div>
</div>

<script>
// Period filter functions
function applyFilter() {
    const periode = document.getElementById('periodeSelect').value;
    let url = '<?php echo BASE_URL; ?>/public/index.php?action=siswa_riwayat&periode=' + periode;
    
    if (periode === 'harian') {
        const tanggal = document.getElementById('tanggalInput').value;
        url += '&tanggal=' + tanggal;
    } else if (periode === 'mingguan') {
        const minggu = document.getElementById('mingguInput').value;
        const tahun = document.getElementById('tahunMingguInput').value;
        url += '&minggu=' + minggu + '&tahun=' + tahun;
    } else {
        const bulan = document.getElementById('bulanInput').value;
        const tahun = document.getElementById('tahunBulanInput').value;
        url += '&bulan=' + bulan + '&tahun=' + tahun;
    }
    
    window.location.href = url;
}

document.getElementById('periodeSelect').addEventListener('change', function() {
    const periode = this.value;
    
    document.getElementById('filterHarian').classList.add('hidden');
    document.getElementById('filterMingguan').classList.add('hidden');
    document.getElementById('filterBulanan').classList.add('hidden');
    
    if (periode === 'harian') {
        document.getElementById('filterHarian').classList.remove('hidden');
    } else if (periode === 'mingguan') {
        document.getElementById('filterMingguan').classList.remove('hidden');
    } else {
        document.getElementById('filterBulanan').classList.remove('hidden');
    }
});

// Grafik Bulanan
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($chartData['labels']); ?>,
        datasets: [{
            label: 'Kehadiran',
            data: <?php echo json_encode($chartData['values']); ?>,
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
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Grafik Presentase
const percentageCtx = document.getElementById('percentageChart').getContext('2d');
const percentageChart = new Chart(percentageCtx, {
    type: 'doughnut',
    data: {
        labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
        datasets: [{
            data: [
                <?php echo $statistik->hadir ?? 0; ?>,
                <?php echo $statistik->izin ?? 0; ?>,
                <?php echo $statistik->sakit ?? 0; ?>,
                <?php echo $statistik->alpha ?? 0; ?>
            ],
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

// Tab switching function
function switchTab(tabName) {
    // Hide all content
    document.getElementById('content-sekolah').classList.add('hidden');
    document.getElementById('content-kelas').classList.add('hidden');
    
    // Remove active state from all tabs
    document.getElementById('tab-sekolah').classList.remove('border-blue-500', 'text-blue-600');
    document.getElementById('tab-sekolah').classList.add('border-transparent', 'text-gray-500');
    document.getElementById('tab-kelas').classList.remove('border-blue-500', 'text-blue-600');
    document.getElementById('tab-kelas').classList.add('border-transparent', 'text-gray-500');
    
    // Show selected content and set active tab
    document.getElementById('content-' + tabName).classList.remove('hidden');
    document.getElementById('tab-' + tabName).classList.add('border-blue-500', 'text-blue-600');
    document.getElementById('tab-' + tabName).classList.remove('border-transparent', 'text-gray-500');
}

// Initialize with sekolah tab active
document.addEventListener('DOMContentLoaded', function() {
    switchTab('sekolah');
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>