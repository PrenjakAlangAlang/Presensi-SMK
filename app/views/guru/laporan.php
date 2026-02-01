<?php
$page_title = "Laporan Kelas";
require_once __DIR__ . '/../layouts/header.php';

$kelas_id = $_GET['kelas_id'] ?? null;
$selected_kelas = null;

// load selected sesi id from GET if present
$selected_sesi_id = isset($_GET['sesi_id']) ? intval($_GET['sesi_id']) : null;

if ($kelas_id) {
    foreach($kelasSaya as $kelas) {
        if ($kelas->id == $kelas_id) {
            $selected_kelas = $kelas;
            break;
        }
    }
}
?>

<div class="mb-6 no-print">
    <h2 class="text-2xl font-bold text-gray-800">Laporan Kelas</h2>
    <p class="text-gray-600">Monitoring dan analisis kehadiran siswa per kelas</p>
</div>

<!-- Pilih Kelas -->
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6 no-print">
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
<!-- Sessions selector -->
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6 no-print">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Sesi Presensi</h3>
    <div class="flex flex-wrap gap-3">
        <?php $sessions = $laporan[$selected_kelas->id]['sessions'] ?? []; ?>
        <?php if(count($sessions) > 0): ?>
            <?php foreach($sessions as $s): ?>
                <?php $active = ($laporan[$selected_kelas->id]['selected_sesi'] && $laporan[$selected_kelas->id]['selected_sesi']->id == $s->id); ?>
                <a href="index.php?action=guru_laporan&kelas_id=<?php echo $selected_kelas->id; ?>&sesi_id=<?php echo $s->id; ?>" 
                   class="px-4 py-2 rounded-lg border transition-colors <?php echo $active ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'; ?>">
                    <?php echo date('Y-m-d H:i', strtotime($s->waktu_buka)); ?>
                    <?php if($s->status == 'open'): ?>
                        <span class="ml-2 text-xs text-green-600">(Open)</span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-gray-500">Belum ada sesi presensi untuk kelas ini.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Laporan kemajuan per sesi -->
<?php $laporan_kemajuan = $laporan[$selected_kelas->id]['laporan_kemajuan'] ?? []; ?>
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6 no-print">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Laporan Kemajuan (Sesi)</h3>
    <?php if(count($laporan_kemajuan) > 0): ?>
        <?php foreach($laporan_kemajuan as $l): ?>
            <div class="border p-4 rounded-md mb-3">
                <div class="text-sm text-gray-500"><?php echo date('Y-m-d H:i', strtotime($l->created_at)); ?></div>
                <div class="mt-2 text-gray-800"><?php echo nl2br(htmlspecialchars($l->catatan)); ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-gray-500">Belum ada laporan kemajuan khusus untuk sesi ini.</div>
    <?php endif; ?>
</div>
<!-- Statistik Kelas -->
<?php 
// Hitung statistik berdasarkan data presensi
$hadir = 0;
$izin = 0;
$sakit = 0;
$alpha = 0;
$totalSiswa = count($selected_kelas->siswa ?? []);

if(isset($laporan[$selected_kelas->id]['presensi'])) {
    foreach($laporan[$selected_kelas->id]['presensi'] as $presensi) {
        if($presensi->status == 'valid') {
            // Cek jenis presensi
            if(isset($presensi->jenis)) {
                if($presensi->jenis == 'hadir') {
                    $hadir++;
                } elseif($presensi->jenis == 'izin') {
                    $izin++;
                } elseif($presensi->jenis == 'sakit') {
                    $sakit++;
                } elseif($presensi->jenis == 'alpha') {
                    $alpha++;
                }
            } else {
                // Default ke hadir jika tidak ada jenis
                $hadir++;
            }
        } elseif($presensi->status == null) {
            // Belum presensi = alpha
            $alpha++;
        }
    }
}
// Hitung yang belum presensi sama sekali
$belumPresensi = $totalSiswa - ($hadir + $izin + $sakit + $alpha);
$alpha += $belumPresensi; // Tambahkan ke alpha

$presentase = $totalSiswa > 0 ? round(($hadir / $totalSiswa) * 100) : 0;
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8 no-print">
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-users text-green-600 text-xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo $totalSiswa; ?></h3>
        <p class="text-gray-600 text-sm">Total Siswa</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-user-check text-blue-600 text-xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo $hadir; ?></h3>
        <p class="text-gray-600 text-sm">Hadir</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-file-alt text-yellow-600 text-xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo $izin; ?></h3>
        <p class="text-gray-600 text-sm">Izin</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-heartbeat text-orange-600 text-xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo $sakit; ?></h3>
        <p class="text-gray-600 text-sm">Sakit</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-user-times text-red-600 text-xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo $alpha; ?></h3>
        <p class="text-gray-600 text-sm">Alpha</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-percentage text-purple-600 text-xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo $presentase; ?>%</h3>
        <p class="text-gray-600 text-sm">Presentase Kehadiran</p>
    </div>
</div>

<!-- Grafik Distribusi Kehadiran -->
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-8 no-print">
    <h3 class="text-lg font-semibold text-gray-800 mb-6">Distribusi Kehadiran</h3>
    <div class="h-80">
        <canvas id="attendanceDistributionChart"></canvas>
    </div>
</div>

<!-- Header Cetak (hanya muncul saat print) -->
<div class="print-only" style="display: none;">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Laporan Presensi Kelas</h1>
        <h2 class="text-xl font-semibold text-gray-700 mt-2"><?php echo htmlspecialchars($selected_kelas->nama_kelas ?? ''); ?></h2>
        <p class="text-gray-600 mt-2">Tanggal Cetak: <?php echo date('d F Y, H:i'); ?></p>
        <?php if(isset($laporan[$selected_kelas->id]['selected_sesi'])): ?>
            <p class="text-gray-600">Sesi: <?php echo date('d-m-Y H:i', strtotime($laporan[$selected_kelas->id]['selected_sesi']->waktu_buka)); ?></p>
        <?php endif; ?>
    </div>
    <hr class="my-4">
</div>

<!-- Daftar Presensi -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">Daftar Presensi - <?php echo htmlspecialchars($selected_kelas->nama_kelas); ?></h3>
        <div class="flex space-x-2 no-print">
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
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Status</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Waktu Presensi</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Lokasi</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Keterangan</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700 no-print">Aksi</th>
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
                            <?php 
                            // Mapping jenis untuk tampilan status
                            $jenisMap = [
                                'hadir' => ['label' => 'Hadir', 'class' => 'bg-green-100 text-green-800'],
                                'izin' => ['label' => 'Izin', 'class' => 'bg-yellow-100 text-yellow-800'],
                                'sakit' => ['label' => 'Sakit', 'class' => 'bg-red-100 text-red-800'],
                                'acara_keluarga' => ['label' => 'Acara Keluarga', 'class' => 'bg-purple-100 text-purple-800'],
                                'lainnya' => ['label' => 'Lainnya', 'class' => 'bg-gray-100 text-gray-800'],
                                'alpha' => ['label' => 'Alpha', 'class' => 'bg-gray-300 text-gray-800']
                            ];
                            
                            $jenis = $presensi->jenis ?? 'hadir';
                            $statusInfo = $jenisMap[$jenis] ?? ['label' => 'Tidak Hadir', 'class' => 'bg-gray-100 text-gray-800'];
                            
                            // Jika tidak ada presensi sama sekali (status null)
                            if (!$presensi->status) {
                                $statusInfo = ['label' => 'Belum Presensi', 'class' => 'bg-gray-100 text-gray-600'];
                            }
                            ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $statusInfo['class']; ?>">
                                <?php echo $statusInfo['label']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            <?php echo $presensi->waktu ? date('H:i', strtotime($presensi->waktu)) : '-'; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                            // Jika izin atau sakit, tidak ada validasi lokasi GPS
                            if (isset($presensi->jenis) && ($presensi->jenis == 'izin' || $presensi->jenis == 'sakit' || $presensi->jenis == 'alpha')): 
                            ?>
                                <span class="text-gray-400">-</span>
                            <?php elseif($presensi->status == 'valid'): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Valid
                                </span>
                            <?php elseif($presensi->status == 'invalid'): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Invalid
                                </span>
                            <?php else: ?>
                                <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if (isset($presensi->jenis) && ($presensi->jenis == 'izin' || $presensi->jenis == 'sakit') && (isset($presensi->alasan) || isset($presensi->foto_bukti))): ?>
                                <div class="space-y-1">
                                    <?php if (isset($presensi->alasan) && $presensi->alasan): ?>
                                        <div class="text-sm text-gray-700">
                                            <span class="font-medium">Alasan:</span><br>
                                            <span class="text-gray-600"><?php echo htmlspecialchars($presensi->alasan); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (isset($presensi->foto_bukti) && $presensi->foto_bukti): ?>
                                        <div>
                                            <a href="<?php echo htmlspecialchars($presensi->foto_bukti); ?>" 
                                               target="_blank" 
                                               class="inline-flex items-center text-blue-600 hover:text-blue-800 text-xs">
                                                <i class="fas fa-paperclip mr-1"></i>
                                                Lihat Bukti
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-gray-400 text-sm">
                                    <?php 
                                    if ($presensi->jenis) {
                                        $keteranganMap = [
                                            'hadir' => 'Presensi Normal',
                                            'izin' => 'Izin',
                                            'sakit' => 'Sakit',
                                            'acara_keluarga' => 'Acara Keluarga',
                                            'lainnya' => 'Lainnya',
                                            'alpha' => 'Alpha'
                                        ];
                                        echo $keteranganMap[$presensi->jenis] ?? ucfirst($presensi->jenis);
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 no-print">
                            <button onclick="editPresensi('<?php echo $presensi->siswa_id; ?>', '<?php echo htmlspecialchars($presensi->nama ?? ''); ?>', '<?php echo $jenis; ?>', '<?php echo htmlspecialchars($presensi->alasan ?? ''); ?>', '<?php echo htmlspecialchars($presensi->foto_bukti ?? ''); ?>')" 
                                    class="text-blue-600 hover:text-blue-800 transition-colors">
                                <i class="fas fa-edit"></i> Ubah
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
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

<!-- Modal Edit Presensi -->
<div id="modalEditPresensi" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Ubah Status Presensi</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="formEditPresensi" onsubmit="return submitEditPresensi(event)">
            <input type="hidden" id="edit_siswa_id" name="siswa_id">
            <input type="hidden" id="edit_kelas_id" name="kelas_id" value="<?php echo $selected_kelas ? $selected_kelas->id : ''; ?>">
            <input type="hidden" id="edit_sesi_id" name="sesi_id" value="<?php echo isset($laporan[$selected_kelas->id]['selected_sesi']) ? $laporan[$selected_kelas->id]['selected_sesi']->id : ''; ?>">
            
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
                <select id="edit_jenis" name="jenis" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" onchange="toggleEditKeterangan()">
                    <option value="hadir">Hadir</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="alpha">Alpha</option>
                </select>
            </div>
            
            <div id="editKeteranganSection" class="hidden">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_alasan">
                        Alasan <span class="text-red-500">*</span>
                    </label>
                    <textarea id="edit_alasan" name="alasan" rows="3" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Masukkan alasan..."></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_foto_bukti">
                        Foto Bukti (URL)
                    </label>
                    <input type="text" id="edit_foto_bukti" name="foto_bukti" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Masukkan URL foto bukti...">
                </div>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Batal
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>

function editPresensi(siswa_id, nama, jenis, alasan, foto_bukti) {
    document.getElementById('edit_siswa_id').value = siswa_id;
    document.getElementById('edit_nama_siswa').textContent = nama;
    document.getElementById('edit_jenis').value = jenis || 'hadir';
    document.getElementById('edit_alasan').value = alasan || '';
    document.getElementById('edit_foto_bukti').value = foto_bukti || '';
    
    toggleEditKeterangan();
    document.getElementById('modalEditPresensi').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('modalEditPresensi').classList.add('hidden');
}

function toggleEditKeterangan() {
    const jenis = document.getElementById('edit_jenis').value;
    const keteranganSection = document.getElementById('editKeteranganSection');
    
    if (jenis === 'izin' || jenis === 'sakit') {
        keteranganSection.classList.remove('hidden');
    } else {
        keteranganSection.classList.add('hidden');
    }
}

function submitEditPresensi(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const jenis = formData.get('jenis');
    const alasan = formData.get('alasan');
    
    // Validasi: jika izin/sakit, alasan harus diisi
    if ((jenis === 'izin' || jenis === 'sakit') && !alasan) {
        showNotification('error', 'Alasan harus diisi untuk status izin/sakit');
        return false;
    }
    
    // Show loading
    showNotification('info', 'Menyimpan perubahan...');
    
    fetch('index.php?action=guru_ubah_status_presensi', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message || 'Status presensi berhasil diubah!');
            closeModal();
            // Reload page after 1 second
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification('error', data.message || 'Gagal mengubah status presensi');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Terjadi kesalahan saat mengubah status');
    });
    
    return false;
}

function exportToPDF() {
    const kelasId = '<?php echo $kelas_id ?? ''; ?>';
    const sesiId = '<?php echo $selected_sesi_id ?? ''; ?>';
    
    if (!kelasId) {
        showNotification('error', 'Pilih kelas terlebih dahulu');
        return;
    }
    
    let url = '<?php echo BASE_URL; ?>/public/index.php?action=guru_export_pdf&kelas_id=' + kelasId;
    if (sesiId) {
        url += '&sesi_id=' + sesiId;
    }
    
    window.open(url, '_blank');
}

function exportToExcel() {
    const kelasId = '<?php echo $kelas_id ?? ''; ?>';
    const sesiId = '<?php echo $selected_sesi_id ?? ''; ?>';
    
    if (!kelasId) {
        showNotification('error', 'Pilih kelas terlebih dahulu');
        return;
    }
    
    let url = '<?php echo BASE_URL; ?>/public/index.php?action=guru_export_excel&kelas_id=' + kelasId;
    if (sesiId) {
        url += '&sesi_id=' + sesiId;
    }
    
    window.location.href = url;
}

function showNotification(type, message) {
    const notification = document.createElement('div');
    let bgColor = 'bg-blue-500';
    let icon = 'info';
    
    if (type === 'success') {
        bgColor = 'bg-green-500';
        icon = 'check';
    } else if (type === 'error') {
        bgColor = 'bg-red-500';
        icon = 'exclamation';
    }
    
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-transform duration-300 ${bgColor} text-white`;
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas fa-${icon}-circle"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

<?php if($selected_kelas): ?>
// Grafik Distribusi Kehadiran - Data Real dari PHP
const distributionCtx = document.getElementById('attendanceDistributionChart');
if (distributionCtx) {
    const distributionChart = new Chart(distributionCtx.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
            datasets: [{
                data: [
                    <?php echo $hadir; ?>,
                    <?php echo $izin; ?>,
                    <?php echo $sakit; ?>,
                    <?php echo $alpha; ?>
                ],
                backgroundColor: [
                    '#10B981', // Green - Hadir
                    '#F59E0B', // Yellow - Izin
                    '#EF4444', // Red - Sakit
                    '#6B7280'  // Gray - Alpha
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
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = <?php echo $totalSiswa; ?>;
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}
<?php endif; ?>

// Print styles
</script>
<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    .print-only {
        display: block !important;
    }

    body {
        background: white !important;
        padding: 20px;
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
    
    .rounded-xl, .rounded-lg {
        border-radius: 0 !important;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    thead {
        background-color: #f3f4f6 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    th, td {
        border: 1px solid #000 !important;
        padding: 8px !important;
        text-align: left;
    }
    
    /* Cetak badge warna */
    .bg-green-100 {
        background-color: #d1fae5 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .bg-yellow-100 {
        background-color: #fef3c7 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .bg-red-100 {
        background-color: #fee2e2 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .bg-gray-100 {
        background-color: #f3f4f6 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    /* Halaman baru untuk tabel panjang */
    tr {
        page-break-inside: avoid;
    }
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>