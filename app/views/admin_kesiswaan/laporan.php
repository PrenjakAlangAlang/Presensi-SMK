<?php
$page_title = "Laporan Presensi";
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            <a href="?action=admin_kesiswaan_laporan&tipe=sekolah&bulan=<?php echo $bulan ?? date('m'); ?>&tahun=<?php echo $tahun ?? date('Y'); ?>" 
               class="<?php echo (!isset($_GET['tipe']) || $_GET['tipe'] == 'sekolah') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-school mr-2"></i>Presensi Sekolah
            </a>
            <a href="?action=admin_kesiswaan_laporan&tipe=kelas&bulan=<?php echo $bulan ?? date('m'); ?>&tahun=<?php echo $tahun ?? date('Y'); ?><?php echo isset($_GET['kelas_id']) ? '&kelas_id='.$_GET['kelas_id'] : ''; ?>" 
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
        <input type="hidden" name="action" value="admin_kesiswaan_laporan">
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



<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Ringkasan Kehadiran Hari Ini -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Ringkasan Kehadiran Hari Ini</h3>
        <div class="space-y-4">
            <?php 
            $total_siswa = isset($statistik->total_siswa) ? $statistik->total_siswa : 0;
            $hadir = isset($statistik->hadir) ? $statistik->hadir : 0;
            $izin = isset($statistik->izin) ? $statistik->izin : 0;
            $sakit = isset($statistik->sakit) ? $statistik->sakit : 0;
            $alpha = isset($statistik->alpha) ? $statistik->alpha : 0;
            $belum_presensi = $total_siswa - ($hadir + $izin + $sakit + $alpha);
            ?>
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
                    <?php if ($tipe_laporan === 'kelas'): ?>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Kelas</th>
                    <?php endif; ?>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Tanggal</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Waktu</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Status</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Jarak</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Keterangan</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Aksi</th>
                    <?php if ($tipe_laporan === 'sekolah'): ?>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Edit</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($presensi)): ?>
                <tr>
                    <td colspan="<?php echo $tipe_laporan === 'kelas' ? '8' : '8'; ?>" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Tidak ada data presensi untuk <?php echo $tipe_laporan === 'kelas' ? 'kelas dan ' : ''; ?>tanggal yang dipilih</p>
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
                                        <i class="fas fa-check-circle mr-1"></i> 
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
                            <button onclick="lihatDetailPresensi(<?php echo $p->siswa_id; ?>)" class="text-blue-600 hover:text-blue-800 transition-colors">
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
                            <button onclick="editPresensiSekolah('<?php echo $p->siswa_id; ?>', '<?php echo htmlspecialchars($p->nama ?? '', ENT_QUOTES); ?>', '<?php echo $jenis; ?>', '<?php echo htmlspecialchars($alasan, ENT_QUOTES); ?>', '<?php echo htmlspecialchars($foto_bukti, ENT_QUOTES); ?>')" 
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
            ?>
            Menampilkan <?php echo $start; ?>-<?php echo $end; ?> dari <?php echo $total_records; ?> data
        </div>
        <div class="flex space-x-2">
            <?php if ($total_pages > 1): ?>
                <!-- Previous Button -->
                <?php if ($page > 1): ?>
                    <a href="?action=admin_kesiswaan_laporan&tanggal=<?php echo urlencode($tanggal); ?>&status=<?php echo urlencode($filter_status ?? ''); ?>&sesi_id=<?php echo urlencode($_GET['sesi_id'] ?? ''); ?>&page=<?php echo $page - 1; ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
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
                        <a href="?action=admin_kesiswaan_laporan&tanggal=<?php echo urlencode($tanggal); ?>&status=<?php echo urlencode($filter_status ?? ''); ?>&sesi_id=<?php echo urlencode($_GET['sesi_id'] ?? ''); ?>&page=<?php echo $i; ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <!-- Next Button -->
                <?php if ($page < $total_pages): ?>
                    <a href="?action=admin_kesiswaan_laporan&tanggal=<?php echo urlencode($tanggal); ?>&status=<?php echo urlencode($filter_status ?? ''); ?>&sesi_id=<?php echo urlencode($_GET['sesi_id'] ?? ''); ?>&page=<?php echo $page + 1; ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
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

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Data presensi dari PHP untuk modal detail
const presensiData = <?php echo json_encode(isset($presensi) ? $presensi : []); ?>;

function editPresensiSekolah(siswa_id, nama, jenis, alasan, foto_bukti) {
    document.getElementById('edit_siswa_id').value = siswa_id;
    document.getElementById('edit_nama_siswa').textContent = nama;
    document.getElementById('edit_jenis').value = jenis || 'hadir';
    document.getElementById('edit_alasan').value = alasan || '';
    document.getElementById('edit_foto_bukti').value = foto_bukti || '';
    
    toggleEditKeteranganSekolah();
    document.getElementById('modalEditPresensiSekolah').classList.remove('hidden');
}

function closeModalEdit() {
    document.getElementById('modalEditPresensiSekolah').classList.add('hidden');
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
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Alasan harus diisi untuk status izin/sakit'
        });
        return false;
    }
    
    // Confirm before submit
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin mengubah status presensi siswa ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Ubah',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('index.php?action=admin_kesiswaan_ubah_status_presensi_sekolah', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message || 'Status presensi berhasil diubah!'
                    }).then(() => {
                        closeModalEdit();
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Gagal mengubah status presensi'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat mengubah status'
                });
            });
        }
    });
    
    return false;
}

function lihatDetailPresensi(siswaId) {
    // Cari data presensi berdasarkan siswa_id
    const data = presensiData.find(p => p.siswa_id == siswaId);
    
    if (!data) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Data presensi tidak ditemukan'
        });
        return;
    }
    
    // Buat konten modal
    let modalContent = `
        <div class="space-y-3 text-left">
            <div class="border-b pb-3">
                <p class="text-sm text-gray-600">Nama Siswa</p>
                <p class="text-lg font-semibold text-gray-800">${data.nama || '-'}</p>
            </div>
            <div class="border-b pb-3">
                <p class="text-sm text-gray-600">Email</p>
                <p class="text-gray-800">${data.email || '-'}</p>
            </div>
    `;
    
    // Format tanggal dan waktu
    if (data.waktu) {
        const waktu = new Date(data.waktu);
        const tanggalFormatted = waktu.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
        const waktuFormatted = waktu.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        modalContent += `
            <div class="grid grid-cols-2 gap-3 border-b pb-3">
                <div>
                    <p class="text-sm text-gray-600">Tanggal</p>
                    <p class="text-gray-800">${tanggalFormatted}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Waktu</p>
                    <p class="text-gray-800">${waktuFormatted}</p>
                </div>
            </div>
        `;
    } else {
        modalContent += `
            <div class="border-b pb-3">
                <p class="text-sm text-gray-600">Tanggal & Waktu</p>
                <p class="text-gray-800">-</p>
            </div>
        `;
    }
    
    // Status
    let statusBadge = '';
    if (data.status === 'valid') {
        const jenis = data.jenis || 'hadir';
        let statusClass = 'bg-green-100 text-green-800';
        if (jenis === 'izin') statusClass = 'bg-yellow-100 text-yellow-800';
        else if (jenis === 'sakit') statusClass = 'bg-orange-100 text-orange-800';
        else if (jenis === 'alpha') statusClass = 'bg-red-100 text-red-800';
        
        statusBadge = `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${statusClass}">
            <i class="fas fa-check-circle mr-1"></i> ${jenis.charAt(0).toUpperCase() + jenis.slice(1)}
        </span>`;
    } else if (data.status === 'invalid') {
        statusBadge = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i> Tidak Valid</span>';
    } else {
        statusBadge = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800"><i class="fas fa-minus-circle mr-1"></i> Belum Presensi</span>';
    }
    
    modalContent += `
        <div class="border-b pb-3">
            <p class="text-sm text-gray-600 mb-1">Status</p>
            ${statusBadge}
        </div>
        <div class="border-b pb-3">
            <p class="text-sm text-gray-600">Jarak</p>
            <p class="text-gray-800">${data.jarak ? Math.round(data.jarak * 100) / 100 + ' m' : '-'}</p>
        </div>
    `;
    
    // Keterangan (alasan dan bukti) jika ada
    if ((data.jenis === 'izin' || data.jenis === 'sakit') && (data.alasan || data.foto_bukti)) {
        modalContent += '<div class="bg-blue-50 p-3 rounded-lg">';
        modalContent += '<p class="text-sm font-semibold text-gray-700 mb-2">Keterangan</p>';
        
        if (data.alasan) {
            modalContent += `<p class="text-sm text-gray-700 mb-2"><span class="font-medium">Alasan:</span><br>${data.alasan}</p>`;
        }
        
        if (data.foto_bukti) {
            modalContent += `<a href="${data.foto_bukti}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                <i class="fas fa-paperclip mr-1"></i> Lihat Bukti
            </a>`;
        }
        
        modalContent += '</div>';
    }
    
    modalContent += '</div>';
    
    // Tampilkan dengan SweetAlert
    Swal.fire({
        title: 'Detail Presensi',
        html: modalContent,
        width: '600px',
        showCloseButton: true,
        showConfirmButton: false
    });
}

function exportToPDF() {
    const bulan = '<?php echo $bulan ?? date('m'); ?>';
    const tahun = '<?php echo $tahun ?? date('Y'); ?>';
    const status = '<?php echo $filter_status ?? ''; ?>';
    const tipe = '<?php echo $tipe_laporan ?? 'sekolah'; ?>';
    const kelasId = '<?php echo $kelas_id ?? ''; ?>';
    window.open('<?php echo BASE_URL; ?>/public/index.php?action=admin_kesiswaan_export_pdf&bulan=' + bulan + '&tahun=' + tahun + '&status=' + status + '&tipe=' + tipe + '&kelas_id=' + kelasId, '_blank');
}

function exportToExcel() {
    const bulan = '<?php echo $bulan ?? date('m'); ?>';
    const tahun = '<?php echo $tahun ?? date('Y'); ?>';
    const status = '<?php echo $filter_status ?? ''; ?>';
    const tipe = '<?php echo $tipe_laporan ?? 'sekolah'; ?>';
    const kelasId = '<?php echo $kelas_id ?? ''; ?>';
    window.location.href = '<?php echo BASE_URL; ?>/public/index.php?action=admin_kesiswaan_export_excel&bulan=' + bulan + '&tahun=' + tahun + '&status=' + status + '&tipe=' + tipe + '&kelas_id=' + kelasId;
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

// Grafik Distribusi Kehadiran - Data Real dari PHP
const distributionCtx = document.getElementById('attendanceDistributionChart').getContext('2d');
const distributionChart = new Chart(distributionCtx, {
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
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
