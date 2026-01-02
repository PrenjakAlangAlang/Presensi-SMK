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
<div id="statistik-sekolah" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
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

<!-- Statistik Ringkas Kelas -->
<div id="statistik-kelas" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 hidden">
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-calendar-check text-green-600 text-xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo $statistikKelas->hadir ?? '0'; ?></h3>
        <p class="text-gray-600 text-sm">Hadir</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-envelope text-yellow-600 text-xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo $statistikKelas->izin ?? '0'; ?></h3>
        <p class="text-gray-600 text-sm">Izin</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-first-aid text-orange-600 text-xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo $statistikKelas->sakit ?? '0'; ?></h3>
        <p class="text-gray-600 text-sm">Sakit</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-times text-red-600 text-xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo $statistikKelas->alpha ?? '0'; ?></h3>
        <p class="text-gray-600 text-sm">Alpha</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Grafik Kehadiran Sekolah -->
    <div id="grafik-sekolah" class="contents">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">
                Grafik Kehadiran Sekolah
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

        <!-- Presentase Kehadiran Sekolah -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">Presentase Kehadiran Sekolah</h3>
            <div class="h-64">
                <canvas id="percentageChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Grafik Kehadiran Kelas -->
    <div id="grafik-kelas" class="contents hidden">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">
                Grafik Kehadiran Kelas
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
                <canvas id="monthlyChartKelas"></canvas>
            </div>
        </div>

        <!-- Presentase Kehadiran Kelas -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">Presentase Kehadiran Kelas</h3>
            <div class="h-64">
                <canvas id="percentageChartKelas"></canvas>
            </div>
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
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">No</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Tanggal</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Waktu</th>
                        <!--
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Status</th>
                        -->
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Jenis</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Jarak</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Alasan</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if(!empty($presensiSekolah)): ?>
                        <?php $no = 1; foreach($presensiSekolah as $presensi): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo $no++; ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php echo date('d M Y', strtotime($presensi->waktu)); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <?php echo date('H:i', strtotime($presensi->waktu)); ?>
                            </td>
                            <!--
                            <td class="px-6 py-4">
                                <?php 
                                $statusBadgeClass = 'bg-gray-100 text-gray-800';
                                $statusIcon = 'fa-question';
                                $statusText = 'Belum';
                                
                                if($presensi->status === 'valid') {
                                    $statusBadgeClass = 'bg-green-100 text-green-800';
                                    $statusIcon = 'fa-check-circle';
                                    $statusText = 'Valid';
                                } else if($presensi->status === 'invalid') {
                                    $statusBadgeClass = 'bg-red-100 text-red-800';
                                    $statusIcon = 'fa-times-circle';
                                    $statusText = 'Tidak Valid';
                                }
                                ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo $statusBadgeClass; ?>">
                                    <i class="fas <?php echo $statusIcon; ?> mr-1"></i>
                                    <?php echo $statusText; ?>
                                </span>
                            </td>
                            -->
                            <td class="px-6 py-4">
                                <?php 
                                $jenis = $presensi->jenis ?? 'hadir';
                                $jenisBadgeClass = 'bg-green-100 text-green-800';
                                $jenisIcon = 'fa-check';
                                
                                if($jenis === 'izin') {
                                    $jenisBadgeClass = 'bg-yellow-100 text-yellow-800';
                                    $jenisIcon = 'fa-envelope';
                                } else if($jenis === 'sakit') {
                                    $jenisBadgeClass = 'bg-orange-100 text-orange-800';
                                    $jenisIcon = 'fa-first-aid';
                                } else if($jenis === 'alpha') {
                                    $jenisBadgeClass = 'bg-red-100 text-red-800';
                                    $jenisIcon = 'fa-times';
                                }
                                ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo $jenisBadgeClass; ?>">
                                    <i class="fas <?php echo $jenisIcon; ?> mr-1"></i>
                                    <?php echo ucfirst($jenis); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <?php 
                                if($jenis === 'hadir') {
                                    echo $presensi->jarak ? round($presensi->jarak, 2) . ' m' : '-';
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?php 
                                if(!empty($presensi->alasan)) {
                                    echo '<span class="line-clamp-2" title="' . htmlspecialchars($presensi->alasan) . '">' . 
                                         htmlspecialchars(substr($presensi->alasan, 0, 50)) . 
                                         (strlen($presensi->alasan) > 50 ? '...' : '') . 
                                         '</span>';
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td class="px-6 py-4">
                                <button onclick='lihatDetailRiwayat(<?php echo json_encode($presensi); ?>)' 
                                        class="text-blue-600 hover:text-blue-800 transition-colors p-2 rounded-lg hover:bg-blue-50">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3 text-gray-400"></i>
                                <p class="text-sm">Belum ada data presensi sekolah untuk periode ini</p>
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
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">No</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Tanggal</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Kelas</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Waktu</th>
                        <!--
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Status</th>
                        -->
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Jenis</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Jarak</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Alasan</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if(!empty($presensiKelas)): ?>
                        <?php $no = 1; foreach($presensiKelas as $presensi): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo $no++; ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php echo date('d M Y', strtotime($presensi->waktu)); ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                <?php echo htmlspecialchars($presensi->nama_kelas ?? 'Kelas'); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <?php echo date('H:i', strtotime($presensi->waktu)); ?>
                            </td>
                            <!--
                            <td class="px-6 py-4">
                                <?php 
                                $statusBadgeClass = 'bg-gray-100 text-gray-800';
                                $statusIcon = 'fa-question';
                                $statusText = 'Belum';
                                
                                if($presensi->status === 'valid') {
                                    $statusBadgeClass = 'bg-green-100 text-green-800';
                                    $statusIcon = 'fa-check-circle';
                                    $statusText = 'Valid';
                                } else if($presensi->status === 'invalid') {
                                    $statusBadgeClass = 'bg-red-100 text-red-800';
                                    $statusIcon = 'fa-times-circle';
                                    $statusText = 'Tidak Valid';
                                }
                                ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo $statusBadgeClass; ?>">
                                    <i class="fas <?php echo $statusIcon; ?> mr-1"></i>
                                    <?php echo $statusText; ?>
                                </span>
                            </td>
                            -->
                            <td class="px-6 py-4">
                                <?php 
                                $jenis = $presensi->jenis ?? 'hadir';
                                $jenisBadgeClass = 'bg-green-100 text-green-800';
                                $jenisIcon = 'fa-check';
                                
                                if($jenis === 'izin') {
                                    $jenisBadgeClass = 'bg-yellow-100 text-yellow-800';
                                    $jenisIcon = 'fa-envelope';
                                } else if($jenis === 'sakit') {
                                    $jenisBadgeClass = 'bg-orange-100 text-orange-800';
                                    $jenisIcon = 'fa-first-aid';
                                } else if($jenis === 'alpha') {
                                    $jenisBadgeClass = 'bg-red-100 text-red-800';
                                    $jenisIcon = 'fa-times';
                                }
                                ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo $jenisBadgeClass; ?>">
                                    <i class="fas <?php echo $jenisIcon; ?> mr-1"></i>
                                    <?php echo ucfirst($jenis); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <?php 
                                if($jenis === 'hadir') {
                                    echo $presensi->jarak ? round($presensi->jarak, 2) . ' m' : '-';
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?php 
                                if(!empty($presensi->alasan)) {
                                    echo '<span class="line-clamp-2" title="' . htmlspecialchars($presensi->alasan) . '">' . 
                                         htmlspecialchars(substr($presensi->alasan, 0, 50)) . 
                                         (strlen($presensi->alasan) > 50 ? '...' : '') . 
                                         '</span>';
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td class="px-6 py-4">
                                <button onclick='lihatDetailRiwayat(<?php echo json_encode($presensi); ?>)' 
                                        class="text-blue-600 hover:text-blue-800 transition-colors p-2 rounded-lg hover:bg-blue-50">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3 text-gray-400"></i>
                                <p class="text-sm">Belum ada data presensi kelas untuk periode ini</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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

// Tab switching
function switchTab(tab) {
    // Hide all content
    document.getElementById('content-sekolah').classList.add('hidden');
    document.getElementById('content-kelas').classList.add('hidden');
    
    // Hide/show statistik
    document.getElementById('statistik-sekolah').classList.add('hidden');
    document.getElementById('statistik-kelas').classList.add('hidden');
    
    // Hide/show grafik
    document.getElementById('grafik-sekolah').classList.add('hidden');
    document.getElementById('grafik-kelas').classList.add('hidden');
    
    // Remove active state from all tabs
    document.getElementById('tab-sekolah').classList.remove('border-blue-500', 'text-blue-600');
    document.getElementById('tab-sekolah').classList.add('border-transparent', 'text-gray-500');
    document.getElementById('tab-kelas').classList.remove('border-blue-500', 'text-blue-600');
    document.getElementById('tab-kelas').classList.add('border-transparent', 'text-gray-500');
    
    // Show selected content and activate tab
    if (tab === 'sekolah') {
        document.getElementById('content-sekolah').classList.remove('hidden');
        document.getElementById('statistik-sekolah').classList.remove('hidden');
        document.getElementById('grafik-sekolah').classList.remove('hidden');
        document.getElementById('tab-sekolah').classList.add('border-blue-500', 'text-blue-600');
        document.getElementById('tab-sekolah').classList.remove('border-transparent', 'text-gray-500');
    } else {
        document.getElementById('content-kelas').classList.remove('hidden');
        document.getElementById('statistik-kelas').classList.remove('hidden');
        document.getElementById('grafik-kelas').classList.remove('hidden');
        document.getElementById('tab-kelas').classList.add('border-blue-500', 'text-blue-600');
        document.getElementById('tab-kelas').classList.remove('border-transparent', 'text-gray-500');
    }
}

// Modal untuk detail riwayat
function lihatDetailRiwayat(data) {
    const modal = document.createElement('div');
    modal.id = 'detailRiwayatModal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    
    const jenis = data.jenis || 'hadir';
    let jenisBadge = '';
    if(jenis === 'hadir') {
        jenisBadge = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check mr-1"></i> Hadir</span>';
    } else if(jenis === 'izin') {
        jenisBadge = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"><i class="fas fa-envelope mr-1"></i> Izin</span>';
    } else if(jenis === 'sakit') {
        jenisBadge = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800"><i class="fas fa-first-aid mr-1"></i> Sakit</span>';
    } else if(jenis === 'alpha') {
        jenisBadge = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-times mr-1"></i> Alpha</span>';
    }
    
    let statusBadge = '';
    if(data.status === 'valid') {
        statusBadge = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i> Valid</span>';
    } else if(data.status === 'invalid') {
        statusBadge = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i> Tidak Valid</span>';
    }
    
    modal.innerHTML = `
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-800">Detail Presensi</h3>
                    <button onclick="closeDetailRiwayatModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tanggal</p>
                        <p class="text-gray-900 font-medium">${new Date(data.waktu).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Waktu</p>
                        <p class="text-gray-900 font-medium">${new Date(data.waktu).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Status Validasi</p>
                        <p>${statusBadge}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Jenis Kehadiran</p>
                        <p>${jenisBadge}</p>
                    </div>
                </div>
                
                ${jenis === 'hadir' ? `
                <div>
                    <p class="text-sm text-gray-600 mb-1">Jarak dari Sekolah</p>
                    <p class="text-gray-900 font-medium">${data.jarak ? Math.round(data.jarak * 100) / 100 + ' meter' : '-'}</p>
                </div>
                ` : ''}
                
                ${data.alasan ? `
                <div>
                    <p class="text-sm text-gray-600 mb-1">Alasan</p>
                    <p class="text-gray-900">${data.alasan}</p>
                </div>
                ` : ''}
                
                ${data.foto_bukti ? `
                <div>
                    <p class="text-sm text-gray-600 mb-2">Bukti</p>
                    <a href="${data.foto_bukti}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Lihat Bukti
                    </a>
                </div>
                ` : ''}
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end">
                <button onclick="closeDetailRiwayatModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Close on click outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeDetailRiwayatModal();
        }
    });
}

function closeDetailRiwayatModal() {
    const modal = document.getElementById('detailRiwayatModal');
    if (modal) {
        modal.remove();
    }
}

// Period selector change handler
document.getElementById('periodeSelect').addEventListener('change', function() {
    const periode = this.value;
    
    // Hide all filters
    document.getElementById('filterHarian').classList.add('hidden');
    document.getElementById('filterMingguan').classList.add('hidden');
    document.getElementById('filterBulanan').classList.add('hidden');
    
    // Show selected filter
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

// Grafik Bulanan Kelas
const monthlyCtxKelas = document.getElementById('monthlyChartKelas').getContext('2d');
const monthlyChartKelas = new Chart(monthlyCtxKelas, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($chartDataKelas['labels']); ?>,
        datasets: [{
            label: 'Kehadiran',
            data: <?php echo json_encode($chartDataKelas['values']); ?>,
            backgroundColor: '#8b5cf6',
            borderColor: '#8b5cf6',
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

// Grafik Presentase Kelas
const percentageCtxKelas = document.getElementById('percentageChartKelas').getContext('2d');
const percentageChartKelas = new Chart(percentageCtxKelas, {
    type: 'doughnut',
    data: {
        labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
        datasets: [{
            data: [
                <?php echo $statistikKelas->hadir ?? 0; ?>,
                <?php echo $statistikKelas->izin ?? 0; ?>,
                <?php echo $statistikKelas->sakit ?? 0; ?>,
                <?php echo $statistikKelas->alpha ?? 0; ?>
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

</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>