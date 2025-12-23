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
    <form method="GET" action="<?php echo BASE_URL; ?>/public/index.php" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="hidden" name="action" value="admin_laporan">
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
            <select name="tanggal" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <?php if (!empty($tanggal_list)): ?>
                    <?php foreach($tanggal_list as $tgl): ?>
                        <option value="<?php echo $tgl->tanggal; ?>" <?php echo (isset($_GET['tanggal']) && $_GET['tanggal'] == $tgl->tanggal) || (!isset($_GET['tanggal']) && $tgl->tanggal == $tanggal) ? 'selected' : ''; ?>>
                            <?php echo date('d F Y', strtotime($tgl->tanggal)); ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="<?php echo date('Y-m-d'); ?>">Tidak ada data presensi</option>
                <?php endif; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select name="status" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="" <?php echo (!isset($_GET['status']) || $_GET['status'] == '') ? 'selected' : ''; ?>>Semua Status</option>
                <option value="valid" <?php echo (isset($_GET['status']) && $_GET['status'] == 'valid') ? 'selected' : ''; ?>>Hadir</option>
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
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
    <!-- Statistik Cards -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-users text-green-600 text-2xl"></i>
        </div>
        <?php 
        $total_siswa = isset($statistik->total_siswa) ? $statistik->total_siswa : 0;
        $hadir = isset($statistik->hadir) ? $statistik->hadir : 0;
        $persentase = $total_siswa > 0 ? round(($hadir / $total_siswa) * 100) : 0;
        ?>
        <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo $persentase; ?>%</h3>
        <p class="text-gray-600 text-sm">Rata-rata Kehadiran</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-user-check text-blue-600 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo isset($statistik->hadir) ? $statistik->hadir : 0; ?></h3>
        <p class="text-gray-600 text-sm">Siswa Hadir</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-user-clock text-yellow-600 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo isset($statistik->izin) ? $statistik->izin : 0; ?></h3>
        <p class="text-gray-600 text-sm">Siswa Izin</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-user-times text-red-600 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo isset($statistik->alpha) ? $statistik->alpha : 0; ?></h3>
        <p class="text-gray-600 text-sm">Siswa Alpha</p>
    </div>
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
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Tanggal</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Waktu</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Status</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Jarak</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Keterangan</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($presensi)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Tidak ada data presensi untuk tanggal yang dipilih</p>
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
                    <a href="?action=admin_laporan&tanggal=<?php echo urlencode($tanggal); ?>&status=<?php echo urlencode($filter_status ?? ''); ?>&page=<?php echo $page - 1; ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
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
                        <a href="?action=admin_laporan&tanggal=<?php echo urlencode($tanggal); ?>&status=<?php echo urlencode($filter_status ?? ''); ?>&page=<?php echo $i; ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <!-- Next Button -->
                <?php if ($page < $total_pages): ?>
                    <a href="?action=admin_laporan&tanggal=<?php echo urlencode($tanggal); ?>&status=<?php echo urlencode($filter_status ?? ''); ?>&page=<?php echo $page + 1; ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
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

function lihatDetailPresensi(siswaId) {
    // Cari data presensi berdasarkan siswa_id
    const data = presensiData.find(p => p.siswa_id == siswaId);
    
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
            <i class="fas fa-check-circle mr-1"></i> ${jenis.charAt(0).toUpperCase() + jenis.slice(1)}
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
    const tanggal = '<?php echo $tanggal ?? date('Y-m-d'); ?>';
    const status = '<?php echo $filter_status ?? ''; ?>';
    window.open('<?php echo BASE_URL; ?>/public/index.php?action=admin_export_pdf&tanggal=' + tanggal + '&status=' + status, '_blank');
}

function exportToExcel() {
    const tanggal = '<?php echo $tanggal ?? date('Y-m-d'); ?>';
    const status = '<?php echo $filter_status ?? ''; ?>';
    window.location.href = '<?php echo BASE_URL; ?>/public/index.php?action=admin_export_excel&tanggal=' + tanggal + '&status=' + status;
}

// Close modal when clicking outside
document.getElementById('detailPresensiModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDetailPresensiModal();
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