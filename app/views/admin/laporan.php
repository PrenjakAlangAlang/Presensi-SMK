<?php
$page_title = "Laporan Presensi";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Laporan Presensi</h2>
    <p class="text-gray-600">
        Analisis dan monitoring kehadiran seluruh siswa - 
        <span class="font-semibold text-blue-600">
            <?php 
            $bulan_names = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $periode_display = $periode ?? 'bulanan';
            if ($periode_display === 'harian') {
                echo date('d ', strtotime($tanggal ?? date('Y-m-d'))) . $bulan_names[intval(date('m', strtotime($tanggal ?? date('Y-m-d')))) - 1] . ' ' . date('Y', strtotime($tanggal ?? date('Y-m-d')));
            } elseif ($periode_display === 'mingguan') {
                echo 'Minggu ke-' . ($minggu ?? date('W')) . ' Tahun ' . ($tahun ?? date('Y'));
            } else {
                echo $bulan_names[intval($bulan ?? date('m')) - 1] . ' ' . ($tahun ?? date('Y'));
            }
            ?>
        </span>
    </p>
</div>

<!-- Tab Navigation -->
<div class="mb-6">
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <a href="?action=admin_laporan&tipe=sekolah&bulan=<?php echo $bulan ?? date('m'); ?>&tahun=<?php echo $tahun ?? date('Y'); ?>" 
               class="<?php echo (!isset($_GET['tipe']) || $_GET['tipe'] == 'sekolah') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-school mr-2"></i>Presensi Sekolah
            </a>
            <a href="?action=admin_laporan&tipe=kelas&bulan=<?php echo $bulan ?? date('m'); ?>&tahun=<?php echo $tahun ?? date('Y'); ?><?php echo isset($_GET['kelas_id']) ? '&kelas_id='.$_GET['kelas_id'] : ''; ?>" 
               class="<?php echo (isset($_GET['tipe']) && $_GET['tipe'] == 'kelas') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-chalkboard mr-2"></i>Presensi Kelas
            </a>
        </nav>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Filter Laporan</h3>
    <form method="GET" action="<?php echo BASE_URL; ?>/public/index.php" class="space-y-4">
        <input type="hidden" name="action" value="admin_laporan">
        <input type="hidden" name="tipe" value="<?php echo $tipe_laporan; ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Pilih Periode -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                <select id="periodeSelect" name="periode" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="harian" <?php echo (isset($periode) && $periode === 'harian') ? 'selected' : ''; ?>>Harian</option>
                    <option value="mingguan" <?php echo (isset($periode) && $periode === 'mingguan') ? 'selected' : ''; ?>>Mingguan</option>
                    <option value="bulanan" <?php echo (!isset($periode) || $periode === 'bulanan') ? 'selected' : ''; ?>>Bulanan</option>
                </select>
            </div>
            
            <!-- Filter Harian -->
            <div id="filterHarian" class="<?php echo (!isset($periode) || $periode !== 'harian') ? 'hidden' : ''; ?>">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <input type="date" name="tanggal" value="<?php echo $tanggal ?? date('Y-m-d'); ?>" 
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <!-- Filter Mingguan -->
            <div id="filterMingguan" class="<?php echo (!isset($periode) || $periode !== 'mingguan') ? 'hidden' : ''; ?> md:col-span-2">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Minggu</label>
                        <input type="number" name="minggu" value="<?php echo $minggu ?? date('W'); ?>" min="1" max="53"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                        <input type="number" name="tahun" value="<?php echo $tahun ?? date('Y'); ?>" min="2020" max="2099"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>
            
            <!-- Filter Bulanan -->
            <div id="filterBulanan" class="<?php echo (!isset($periode) || $periode !== 'bulanan') ? 'hidden' : ''; ?> md:col-span-2">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                        <select name="bulan" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php
                            $bulan_names = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            for ($i = 1; $i <= 12; $i++) {
                                $bulan_value = str_pad($i, 2, '0', STR_PAD_LEFT);
                                $selected = (isset($bulan) && $bulan == $bulan_value) ? 'selected' : '';
                                echo "<option value='$bulan_value' $selected>" . $bulan_names[$i-1] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                        <input type="number" name="tahun" value="<?php echo $tahun ?? date('Y'); ?>" min="2020" max="2099" 
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>
        
            <?php if ($tipe_laporan === 'kelas'): ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                <select name="kelas_id" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Pilih Kelas</option>
                    <?php foreach($kelas_list as $kls): ?>
                        <option value="<?php echo $kls->id; ?>" <?php echo (isset($_GET['kelas_id']) && $_GET['kelas_id'] == $kls->id) ? 'selected' : ''; ?>>
                            <?php echo $kls->nama_kelas; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="" <?php echo (!isset($_GET['status']) || $_GET['status'] == '') ? 'selected' : ''; ?>>Semua Status</option>
                    <option value="hadir" <?php echo (isset($_GET['status']) && $_GET['status'] == 'hadir') ? 'selected' : ''; ?>>Hadir</option>
                    <option value="izin" <?php echo (isset($_GET['status']) && $_GET['status'] == 'izin') ? 'selected' : ''; ?>>Izin</option>
                    <option value="sakit" <?php echo (isset($_GET['status']) && $_GET['status'] == 'sakit') ? 'selected' : ''; ?>>Sakit</option>
                    <option value="alpha" <?php echo (isset($_GET['status']) && $_GET['status'] == 'alpha') ? 'selected' : ''; ?>>Alpha</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-lg transition-colors">
                    <i class="fas fa-filter mr-2"></i>Terapkan Filter
                </button>
            </div>
        </div>
    </form>
</div>

<?php if ($tipe_laporan === 'kelas' && $kelas_id && isset($laporan_kemajuan) && count($laporan_kemajuan) > 0): ?>
<!-- Laporan Kemajuan Section -->
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-clipboard-list mr-2"></i>Laporan Kemajuan (Sesi)
        </h3>
        <span class="text-sm text-gray-500">
            <i class="fas fa-calendar-alt mr-1"></i>
            <?php 
            if ($periode === 'harian') {
                echo date('d F Y', strtotime($tanggal));
            } elseif ($periode === 'mingguan') {
                echo 'Minggu ke-' . $minggu . ' Tahun ' . $tahun;
            } else {
                $bulan_names = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                echo $bulan_names[intval($bulan)] . ' ' . $tahun;
            }
            ?>
        </span>
    </div>
    <div class="text-sm text-gray-600 mb-4">
        <i class="fas fa-info-circle mr-1"></i>
        Menampilkan <?php echo count($laporan_kemajuan); ?> laporan kemajuan
    </div>
    <div class="space-y-3">
        <?php foreach($laporan_kemajuan as $l): ?>
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 hover:border-blue-300 transition-colors">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <p class="text-sm font-medium text-gray-800">
                        <i class="fas fa-calendar-day mr-1 text-blue-600"></i>
                        <?php echo date('d F Y', strtotime($l->tanggal)); ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-user mr-1"></i>
                        Oleh: <?php echo htmlspecialchars($l->guru_nama ?? 'Guru'); ?>
                    </p>
                </div>
                <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">
                    <i class="fas fa-clock mr-1"></i>
                    <?php echo date('H:i', strtotime($l->created_at)); ?>
                </span>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-200">
                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                    <?php echo nl2br(htmlspecialchars($l->catatan)); ?>
                </p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php elseif ($tipe_laporan === 'kelas' && $kelas_id && (!isset($laporan_kemajuan) || count($laporan_kemajuan) === 0)): ?>
<!-- Empty State -->
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6">
    <div class="text-center py-8">
        <i class="fas fa-clipboard-list text-gray-300 text-4xl mb-3"></i>
        <h3 class="text-lg font-semibold text-gray-600 mb-2">Tidak Ada Laporan Kemajuan</h3>
        <p class="text-sm text-gray-500">
            Belum ada laporan kemajuan untuk periode 
            <?php 
            if ($periode === 'harian') {
                echo date('d F Y', strtotime($tanggal));
            } elseif ($periode === 'mingguan') {
                echo 'Minggu ke-' . $minggu . ' Tahun ' . $tahun;
            } else {
                $bulan_names = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                echo $bulan_names[intval($bulan)] . ' ' . $tahun;
            }
            ?>
        </p>
    </div>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Ringkasan Kehadiran -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Ringkasan Kehadiran</h3>
        <?php if (!isset($statistik) || $statistik === null): ?>
        <div class="text-center py-8">
            <i class="fas fa-info-circle text-gray-300 text-4xl mb-3"></i>
            <p class="text-sm text-gray-500">Pilih kelas untuk melihat statistik kehadiran</p>
        </div>
        <?php else: ?>
        <div class="space-y-4">
            <?php 
            $total_siswa = isset($statistik->total_siswa) ? $statistik->total_siswa : 0;
            $hadir = isset($statistik->hadir) ? $statistik->hadir : 0;
            $izin = isset($statistik->izin) ? $statistik->izin : 0;
            $sakit = isset($statistik->sakit) ? $statistik->sakit : 0;
            $alpha = isset($statistik->alpha) ? $statistik->alpha : 0;
            $belum_presensi = $total_siswa - ($hadir + $izin + $sakit + $alpha);
            ?>
            <?php if ($total_siswa == 0): ?>
            <div class="text-center py-4 text-gray-500">
                <i class="fas fa-inbox text-3xl mb-2"></i>
                <p class="text-sm">Tidak ada data presensi untuk periode ini</p>
            </div>
            <?php else: ?>
            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Hadir</p>
                        <p class="text-lg font-bold text-gray-800"><?php echo $hadir; ?> siswa</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-green-600"><?php echo $total_siswa > 0 ? round(($hadir/$total_siswa)*100) : 0; ?>%</span>
            </div>
            
            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Izin</p>
                        <p class="text-lg font-bold text-gray-800"><?php echo $izin; ?> siswa</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-yellow-600"><?php echo $total_siswa > 0 ? round(($izin/$total_siswa)*100) : 0; ?>%</span>
            </div>
            
            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-heartbeat text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Sakit</p>
                        <p class="text-lg font-bold text-gray-800"><?php echo $sakit; ?> siswa</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-orange-600"><?php echo $total_siswa > 0 ? round(($sakit/$total_siswa)*100) : 0; ?>%</span>
            </div>
            
            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-times text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Alpha</p>
                        <p class="text-lg font-bold text-gray-800"><?php echo $alpha; ?> siswa</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-red-600"><?php echo $total_siswa > 0 ? round(($alpha/$total_siswa)*100) : 0; ?>%</span>
            </div>

            <?php if ($belum_presensi > 0): ?>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gray-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Belum Presensi</p>
                        <p class="text-lg font-bold text-gray-800"><?php echo $belum_presensi; ?> siswa</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-gray-600"><?php echo $total_siswa > 0 ? round(($belum_presensi/$total_siswa)*100) : 0; ?>%</span>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Distribusi Kehadiran -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Distribusi Kehadiran</h3>
        <?php if (!isset($statistik) || $statistik === null || (isset($statistik->total_siswa) && $statistik->total_siswa == 0)): ?>
        <div class="text-center py-16 text-gray-400">
            <i class="fas fa-chart-pie text-5xl mb-3"></i>
            <p class="text-sm">Grafik akan tampil setelah ada data presensi</p>
        </div>
        <?php else: ?>
        <div class="h-80">
            <canvas id="attendanceDistributionChart"></canvas>
        </div>
        <?php endif; ?>
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
                    <?php if ($tipe_laporan === 'kelas'): ?>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Kelas</th>
                    <?php endif; ?>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Tanggal</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Waktu</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Status</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Jarak</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Keterangan</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Lihat Detail</th>
                    <?php if ($tipe_laporan === 'sekolah' || $tipe_laporan === 'kelas'): ?>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Edit</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($presensi)): ?>
                <tr>
                    <td colspan="<?php echo ($tipe_laporan === 'kelas' ? '9' : '9'); ?>" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Tidak ada data presensi untuk <?php echo $tipe_laporan === 'kelas' ? 'kelas dan ' : ''; ?>periode yang dipilih</p>
                        <?php if ($tipe_laporan === 'kelas' && !isset($kelas_id)): ?>
                        <p class="text-sm mt-2">Silakan pilih kelas terlebih dahulu</p>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($presensi as $p): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-800"><?php echo htmlspecialchars($p->nama ?? 'Siswa'); ?></span>
                                    <?php if (isset($p->email)): ?>
                                    <br><span class="text-xs text-gray-500"><?php echo htmlspecialchars($p->email); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <?php if ($tipe_laporan === 'kelas'): ?>
                        <td class="px-6 py-4 text-gray-600">
                            <?php echo isset($p->nama_kelas) ? htmlspecialchars($p->nama_kelas) : '-'; ?>
                        </td>
                        <?php endif; ?>
                        <?php 
                        $waktuTs = (isset($p->waktu) && $p->waktu) ? strtotime($p->waktu) : null;
                        ?>
                        <td class="px-6 py-4 text-gray-600">
                            <?php echo $waktuTs ? date('d M Y', $waktuTs) : '-'; ?>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            <?php echo $waktuTs ? date('H:i', $waktuTs) : '-'; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if (isset($p->status) && $p->status): ?>
                                <?php if ($p->status == 'valid'): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        
                                        <?php echo isset($p->jenis) ? ucfirst($p->jenis) : 'Hadir'; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i> Tidak Valid
                                    </span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-minus-circle mr-1"></i> Belum Presensi
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            <?php echo isset($p->jarak) ? round($p->jarak, 2) . ' m' : '-'; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if (isset($p->jenis) && ($p->jenis == 'izin' || $p->jenis == 'sakit') && (isset($p->alasan) || isset($p->foto_bukti))): ?>
                                <div class="space-y-1">
                                    <?php if (isset($p->alasan) && $p->alasan): ?>
                                        <div class="text-sm text-gray-700">
                                            <span class="font-medium">Alasan:</span><br>
                                            <span class="text-gray-600"><?php echo htmlspecialchars($p->alasan); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (isset($p->foto_bukti) && $p->foto_bukti): ?>
                                        <div>
                                            <a href="<?php echo htmlspecialchars($p->foto_bukti); ?>" 
                                               target="_blank" 
                                               class="inline-flex items-center text-blue-600 hover:text-blue-800 text-xs">
                                                <i class="fas fa-paperclip mr-1"></i>
                                                Lihat Bukti
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-gray-400 text-sm">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <button onclick="lihatDetailPresensi(<?php echo $p->id; ?>)" class="text-blue-600 hover:text-blue-800 transition-colors">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                        <?php if ($tipe_laporan === 'sekolah'): ?>
                        <td class="px-6 py-4">
                            <?php 
                            $jenis = isset($p->jenis) ? $p->jenis : 'hadir';
                            $alasan = isset($p->alasan) ? $p->alasan : '';
                            $foto_bukti = isset($p->foto_bukti) ? $p->foto_bukti : '';
                            ?>
                            <button onclick="editPresensiSekolah('<?php echo $p->id; ?>', '<?php echo $p->user_id; ?>', '<?php echo htmlspecialchars($p->nama ?? '', ENT_QUOTES); ?>', '<?php echo $jenis; ?>', '<?php echo htmlspecialchars($alasan, ENT_QUOTES); ?>', '<?php echo htmlspecialchars($foto_bukti, ENT_QUOTES); ?>')" 
                                    class="text-green-600 hover:text-green-800 transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                        <?php elseif ($tipe_laporan === 'kelas'): ?>
                        <td class="px-6 py-4">
                            <?php 
                            $jenis = isset($p->jenis) ? $p->jenis : 'hadir';
                            $alasan = isset($p->alasan) ? $p->alasan : '';
                            $foto_bukti = isset($p->foto_bukti) ? $p->foto_bukti : '';
                            $kelas_id_val = isset($p->kelas_id) ? $p->kelas_id : ($kelas_id ?? '');
                            ?>
                            <button onclick="editPresensiKelas('<?php echo $p->id; ?>', '<?php echo $p->user_id; ?>', '<?php echo $kelas_id_val; ?>', '<?php echo htmlspecialchars($p->nama ?? '', ENT_QUOTES); ?>', '<?php echo $jenis; ?>', '<?php echo htmlspecialchars($alasan, ENT_QUOTES); ?>', '<?php echo htmlspecialchars($foto_bukti, ENT_QUOTES); ?>')" 
                                    class="text-green-600 hover:text-green-800 transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                        <?php endif; ?>
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
            $start = $offset + 1;
            $end = min($offset + count($presensi), $total_records);
            
            // Build query string with all filters
            $query_params = [
                'action' => 'admin_laporan',
                'tipe' => $tipe_laporan ?? 'sekolah',
                'periode' => $periode ?? 'bulanan',
                'bulan' => $bulan ?? date('m'),
                'tahun' => $tahun ?? date('Y'),
                'status' => $filter_status ?? ''
            ];
            
            if (isset($tanggal)) {
                $query_params['tanggal'] = $tanggal;
            }
            if (isset($minggu)) {
                $query_params['minggu'] = $minggu;
            }
            if (isset($_GET['sesi_id'])) {
                $query_params['sesi_id'] = $_GET['sesi_id'];
            }
            if (isset($kelas_id) && $kelas_id) {
                $query_params['kelas_id'] = $kelas_id;
            }
            
            $base_query = http_build_query($query_params);
            ?>
            Menampilkan <?php echo $start; ?>-<?php echo $end; ?> dari <?php echo $total_records; ?> data
        </div>
        <div class="flex space-x-2">
            <?php if ($total_pages > 1): ?>
                <!-- Previous Button -->
                <?php if ($page > 1): ?>
                    <a href="?<?php echo $base_query; ?>&page=<?php echo $page - 1; ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
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
                        <a href="?<?php echo $base_query; ?>&page=<?php echo $i; ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <!-- Next Button -->
                <?php if ($page < $total_pages): ?>
                    <a href="?<?php echo $base_query; ?>&page=<?php echo $page + 1; ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
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

<!-- Modal Edit Presensi Sekolah -->
<div id="modalEditPresensiSekolah" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Ubah Status Presensi Sekolah</h3>
            <button onclick="closeModalEdit()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="formEditPresensiSekolah" onsubmit="return submitEditPresensiSekolah(event)">
            <input type="hidden" id="edit_presensi_id" name="presensi_id">
            <input type="hidden" id="edit_user_id" name="user_id">
            <input type="hidden" id="edit_tanggal" name="tanggal" value="<?php echo $tanggal ?? date('Y-m-d'); ?>">
            <input type="hidden" id="edit_sesi_id" name="sesi_id" value="<?php echo $_GET['sesi_id'] ?? ''; ?>">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Nama Siswa
                </label>
                <p id="edit_nama_siswa" class="text-gray-600"></p>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_jenis">
                    Status Presensi
                </label>
                <select id="edit_jenis" name="jenis" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" onchange="toggleEditKeteranganSekolah()">
                    <option value="hadir">Hadir</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="alpha">Alpha</option>
                </select>
            </div>
            
            <div id="editKeteranganSekolahSection" class="hidden">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_alasan">
                        Alasan <span class="text-red-500">*</span>
                    </label>
                    <textarea id="edit_alasan" name="alasan" rows="3" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Masukkan alasan..."></textarea>
                </div>
                <!--
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_foto_bukti">
                        Foto Bukti (URL)
                    </label>
                    <input type="text" id="edit_foto_bukti" name="foto_bukti" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Masukkan URL foto bukti...">
                </div>
            -->
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModalEdit()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Batal
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Presensi Kelas -->
<div id="modalEditPresensiKelas" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Ubah Status Presensi Kelas</h3>
            <button onclick="closeModalEditKelas()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="formEditPresensiKelas" onsubmit="return submitEditPresensiKelas(event)">
            <input type="hidden" id="edit_kelas_presensi_id" name="presensi_id">
            <input type="hidden" id="edit_kelas_user_id" name="user_id">
            <input type="hidden" id="edit_kelas_kelas_id" name="kelas_id">
            <input type="hidden" id="edit_kelas_sesi_id" name="sesi_id" value="">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Nama Siswa
                </label>
                <p id="edit_kelas_nama_siswa" class="text-gray-600"></p>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_kelas_jenis">
                    Status Kehadiran
                </label>
                <select id="edit_kelas_jenis" name="jenis" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" onchange="toggleEditKeteranganKelas()">
                    <option value="hadir">Hadir</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="alpha">Alpha</option>
                </select>
            </div>
            
            <div id="editKeteranganKelasSection" class="hidden">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_kelas_alasan">
                        Alasan <span class="text-red-500">*</span>
                    </label>
                    <textarea id="edit_kelas_alasan" name="alasan" rows="3" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Masukkan alasan izin/sakit..."></textarea>
                </div>
                <!--
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_kelas_foto_bukti">
                        Foto Bukti (URL)
                    </label>
                    <input type="text" id="edit_kelas_foto_bukti" name="foto_bukti" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Masukkan URL foto bukti...">
                </div>
            -->
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModalEditKelas()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Batal
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Simpan
                </button>
            </div>
        </form>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <p class="text-gray-800" id="detailEmail">-</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                    <p class="text-gray-800" id="detailTanggal">-</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Waktu</label>
                    <p class="text-gray-800" id="detailWaktu">-</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <p id="detailStatus">-</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jarak</label>
                    <p class="text-gray-800" id="detailJarak">-</p>
                </div>
                <div class="col-span-2" id="detailKeteranganSection" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div id="detailAlasan" class="mb-2"></div>
                        <div id="detailBukti"></div>
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
// Data presensi dari PHP untuk modal detail
const presensiData = <?php echo json_encode(isset($presensi) ? $presensi : []); ?>;

function editPresensiSekolah(presensi_id, user_id, nama, jenis, alasan, foto_bukti) {
    document.getElementById('edit_presensi_id').value = presensi_id;
    document.getElementById('edit_user_id').value = user_id;
    document.getElementById('edit_nama_siswa').textContent = nama;
    document.getElementById('edit_jenis').value = jenis || 'hadir';
    document.getElementById('edit_alasan').value = alasan || '';
    
    toggleEditKeteranganSekolah();
    document.getElementById('modalEditPresensiSekolah').classList.remove('hidden');
}

function closeModalEdit() {
    document.getElementById('modalEditPresensiSekolah').classList.add('hidden');
}

function editPresensiKelas(presensi_id, user_id, kelas_id, nama, jenis, alasan, foto_bukti) {
    document.getElementById('edit_kelas_presensi_id').value = presensi_id;
    document.getElementById('edit_kelas_user_id').value = user_id;
    document.getElementById('edit_kelas_kelas_id').value = kelas_id;
    document.getElementById('edit_kelas_nama_siswa').textContent = nama;
    document.getElementById('edit_kelas_jenis').value = jenis || 'hadir';
    document.getElementById('edit_kelas_alasan').value = alasan || '';
    
    toggleEditKeteranganKelas();
    document.getElementById('modalEditPresensiKelas').classList.remove('hidden');
}

function closeModalEditKelas() {
    document.getElementById('modalEditPresensiKelas').classList.add('hidden');
}

function toggleEditKeteranganKelas() {
    const jenis = document.getElementById('edit_kelas_jenis').value;
    const keteranganSection = document.getElementById('editKeteranganKelasSection');
    
    if (jenis === 'izin' || jenis === 'sakit') {
        keteranganSection.classList.remove('hidden');
    } else {
        keteranganSection.classList.add('hidden');
    }
}

function submitEditPresensiKelas(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const jenis = formData.get('jenis');
    const alasan = formData.get('alasan');
    
    // Validasi: jika izin/sakit, alasan harus diisi
    if ((jenis === 'izin' || jenis === 'sakit') && !alasan) {
        alert('Alasan harus diisi untuk status izin/sakit');
        return false;
    }
    
    // Show loading
    if (confirm('Apakah Anda yakin ingin mengubah status presensi siswa ini?')) {
        fetch('index.php?action=admin_ubah_status_presensi_kelas', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Status presensi berhasil diubah!');
                closeModalEditKelas();
                location.reload();
            } else {
                alert(data.message || 'Gagal mengubah status presensi');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengubah status');
        });
    }
    
    return false;
}

function toggleEditKeteranganSekolah() {
    const jenis = document.getElementById('edit_jenis').value;
    const keteranganSection = document.getElementById('editKeteranganSekolahSection');
    
    if (jenis === 'izin' || jenis === 'sakit') {
        keteranganSection.classList.remove('hidden');
    } else {
        keteranganSection.classList.add('hidden');
    }
}

function submitEditPresensiSekolah(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const jenis = formData.get('jenis');
    const alasan = formData.get('alasan');
    
    // Validasi: jika izin/sakit, alasan harus diisi
    if ((jenis === 'izin' || jenis === 'sakit') && !alasan) {
        alert('Alasan harus diisi untuk status izin/sakit');
        return false;
    }
    
    // Show loading
    if (confirm('Apakah Anda yakin ingin mengubah status presensi siswa ini?')) {
        fetch('index.php?action=admin_ubah_status_presensi_sekolah', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Status presensi berhasil diubah!');
                closeModalEdit();
                location.reload();
            } else {
                alert(data.message || 'Gagal mengubah status presensi');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengubah status');
        });
    }
    
    return false;
}

function lihatDetailPresensi(presensiId) {
    // Cari data presensi berdasarkan ID presensi
    const data = presensiData.find(p => p.id == presensiId);
    
    if (!data) {
        alert('Data presensi tidak ditemukan');
        return;
    }
    
    // Set data ke modal
    document.getElementById('detailNama').textContent = data.nama || '-';
    document.getElementById('detailEmail').textContent = data.email || '-';
    
    // Format tanggal dan waktu
    if (data.waktu) {
        const waktu = new Date(data.waktu);
        const tanggalFormatted = waktu.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
        const waktuFormatted = waktu.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        document.getElementById('detailTanggal').textContent = tanggalFormatted;
        document.getElementById('detailWaktu').textContent = waktuFormatted;
    } else {
        document.getElementById('detailTanggal').textContent = '-';
        document.getElementById('detailWaktu').textContent = '-';
    }
    
    // Set status dengan badge
    const statusElement = document.getElementById('detailStatus');
    let statusHTML = '';
    if (data.status === 'valid') {
        const jenis = data.jenis || 'hadir';
        let statusClass = 'bg-green-100 text-green-800';
        if (jenis === 'izin') statusClass = 'bg-yellow-100 text-yellow-800';
        else if (jenis === 'sakit') statusClass = 'bg-orange-100 text-orange-800';
        else if (jenis === 'alpha') statusClass = 'bg-red-100 text-red-800';
        
        statusHTML = `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${statusClass}">
             ${jenis.charAt(0).toUpperCase() + jenis.slice(1)}
        </span>`;
    } else if (data.status === 'invalid') {
        statusHTML = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i> Tidak Valid</span>';
    } else {
        statusHTML = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800"><i class="fas fa-minus-circle mr-1"></i> Belum Presensi</span>';
    }
    statusElement.innerHTML = statusHTML;
    
    // Set jarak
    document.getElementById('detailJarak').textContent = data.jarak ? Math.round(data.jarak * 100) / 100 + ' m' : '-';
    
    // Set keterangan (alasan dan bukti) jika ada
    const keteranganSection = document.getElementById('detailKeteranganSection');
    const alasanDiv = document.getElementById('detailAlasan');
    const buktiDiv = document.getElementById('detailBukti');
    
    if ((data.jenis === 'izin' || data.jenis === 'sakit') && (data.alasan || data.foto_bukti)) {
        keteranganSection.style.display = 'block';
        
        // Set alasan
        if (data.alasan) {
            alasanDiv.innerHTML = `<p class="text-sm text-gray-700"><span class="font-medium">Alasan:</span><br><span class="text-gray-600">${data.alasan}</span></p>`;
        } else {
            alasanDiv.innerHTML = '';
        }
        
        // Set bukti
        if (data.foto_bukti) {
            buktiDiv.innerHTML = `<a href="${data.foto_bukti}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                <i class="fas fa-paperclip mr-1"></i> Lihat Bukti
            </a>`;
        } else {
            buktiDiv.innerHTML = '';
        }
    } else {
        keteranganSection.style.display = 'none';
    }
    
    // Tampilkan modal
    document.getElementById('detailPresensiModal').classList.remove('hidden');
}

function closeDetailPresensiModal() {
    document.getElementById('detailPresensiModal').classList.add('hidden');
}

function exportToPDF() {
    const periode = '<?php echo $periode ?? 'bulanan'; ?>';
    const tanggal = '<?php echo $tanggal ?? date('Y-m-d'); ?>';
    const minggu = '<?php echo $minggu ?? date('W'); ?>';
    const bulan = '<?php echo $bulan ?? date('m'); ?>';
    const tahun = '<?php echo $tahun ?? date('Y'); ?>';
    const status = '<?php echo $filter_status ?? ''; ?>';
    const tipe = '<?php echo $tipe_laporan ?? 'sekolah'; ?>';
    const kelasId = '<?php echo $kelas_id ?? ''; ?>';
    window.open('<?php echo BASE_URL; ?>/public/index.php?action=admin_export_pdf&periode=' + periode + '&tanggal=' + tanggal + '&minggu=' + minggu + '&bulan=' + bulan + '&tahun=' + tahun + '&status=' + status + '&tipe=' + tipe + '&kelas_id=' + kelasId, '_blank');
}

function exportToExcel() {
    const periode = '<?php echo $periode ?? 'bulanan'; ?>';
    const tanggal = '<?php echo $tanggal ?? date('Y-m-d'); ?>';
    const minggu = '<?php echo $minggu ?? date('W'); ?>';
    const bulan = '<?php echo $bulan ?? date('m'); ?>';
    const tahun = '<?php echo $tahun ?? date('Y'); ?>';
    const status = '<?php echo $filter_status ?? ''; ?>';
    const tipe = '<?php echo $tipe_laporan ?? 'sekolah'; ?>';
    const kelasId = '<?php echo $kelas_id ?? ''; ?>';
    window.location.href = '<?php echo BASE_URL; ?>/public/index.php?action=admin_export_excel&periode=' + periode + '&tanggal=' + tanggal + '&minggu=' + minggu + '&bulan=' + bulan + '&tahun=' + tahun + '&status=' + status + '&tipe=' + tipe + '&kelas_id=' + kelasId;
}

// Close modal when clicking outside
document.getElementById('detailPresensiModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDetailPresensiModal();
    }
});

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

// Grafik Distribusi Kehadiran - Data Real dari PHP
<?php if (isset($statistik) && $statistik !== null && isset($statistik->total_siswa) && $statistik->total_siswa > 0): ?>
const distributionCtx = document.getElementById('attendanceDistributionChart');
if (distributionCtx) {
    const distributionChart = new Chart(distributionCtx.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
            datasets: [{
                data: [
                    <?php echo isset($statistik->hadir) ? $statistik->hadir : 0; ?>,
                    <?php echo isset($statistik->izin) ? $statistik->izin : 0; ?>,
                    <?php echo isset($statistik->sakit) ? $statistik->sakit : 0; ?>,
                    <?php echo isset($statistik->alpha) ? $statistik->alpha : 0; ?>
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
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed + ' siswa';
                            return label;
                        }
                    }
                }
            }
        }
    });
}
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>