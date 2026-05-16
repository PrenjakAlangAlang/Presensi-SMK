<?php
$page_title = "Mata Pelajaran Saya";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Mata Pelajaran yang Diampu</h2>
    <p class="text-gray-600">Lihat mata pelajaran yang diampu dan jumlah siswa terdaftar</p>
</div>

<?php if (!empty($kelasSaya)): ?>
    <?php 
    $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    $jadwalByHari = array_fill_keys($hariList, []);
    $jadwalLainnya = [];

    foreach ($kelasSaya as $mapel) {
        $hari = $mapel->hari ?? '';
        if ($hari && isset($jadwalByHari[$hari])) {
            $jadwalByHari[$hari][] = $mapel;
        } elseif ($hari) {
            $jadwalLainnya[$hari][] = $mapel;
        } else {
            $jadwalLainnya['Belum Dijadwalkan'][] = $mapel;
        }
    }

    foreach ($jadwalByHari as &$items) {
        usort($items, function($a, $b) {
            return strcmp($a->jam_mulai ?? '', $b->jam_mulai ?? '');
        });
    }
    unset($items);

    $belumDitugaskan = array_filter($kelasSaya, function($m) { 
        return empty($m->nama_kelas); 
    });
    if (!empty($belumDitugaskan)): 
    ?>
    <div class="mb-4 bg-amber-50 border border-amber-200 rounded-lg p-4">
        <div class="flex items-center space-x-2 text-amber-800">
            <i class="fas fa-info-circle"></i>
            <p class="text-sm">
                <strong>Perhatian:</strong> Beberapa mata pelajaran belum ditugaskan ke kelas. 
                Silakan hubungi admin untuk menambahkan mata pelajaran ke kelas yang sesuai.
            </p>
        </div>
    </div>
    <?php endif; ?>
<?php endif; ?>

<?php if(!empty($kelasSaya)): ?>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    <?php foreach($jadwalByHari as $hari => $items): ?>
        <section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800"><?php echo $hari; ?></h3>
                <span class="text-xs font-medium px-2 py-1 rounded-full <?php echo !empty($items) ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500'; ?>">
                    <?php echo count($items); ?> mapel
                </span>
            </div>
            <div class="divide-y divide-gray-100">
                <?php if(!empty($items)): ?>
                    <?php foreach($items as $mapel): ?>
                        <?php
                            $jamMulai = !empty($mapel->jam_mulai) ? date('H:i', strtotime($mapel->jam_mulai)) : '--:--';
                            $jamSelesai = !empty($mapel->jam_selesai) ? date('H:i', strtotime($mapel->jam_selesai)) : '--:--';
                        ?>
                        <div class="p-5 flex gap-4">
                            <div class="w-20 shrink-0 text-sm font-semibold text-blue-700">
                                <?php echo $jamMulai; ?><br>
                                <span class="text-xs font-normal text-gray-500"><?php echo $jamSelesai; ?></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-gray-800 leading-snug"><?php echo htmlspecialchars($mapel->nama_mata_pelajaran ?? '-'); ?></p>
                                <p class="text-sm <?php echo !empty($mapel->nama_kelas) ? 'text-gray-600' : 'text-amber-600 italic'; ?>">
                                    <?php echo !empty($mapel->nama_kelas) ? htmlspecialchars($mapel->nama_kelas . ' - ' . ($mapel->tahun_ajaran ?? '-')) : 'Belum ditugaskan ke kelas'; ?>
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2 text-xs text-gray-600">
                                    <span class="inline-flex items-center px-2 py-1 bg-gray-100 rounded">
                                        <i class="fas fa-door-open mr-1 text-gray-500"></i><?php echo htmlspecialchars($mapel->ruang ?: '-'); ?>
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 bg-gray-100 rounded">
                                        <i class="fas fa-users mr-1 text-gray-500"></i><?php echo (int)($mapel->total_siswa ?? 0); ?> siswa
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-5 text-sm text-gray-400 text-center">Tidak ada jadwal</div>
                <?php endif; ?>
            </div>
        </section>
    <?php endforeach; ?>
</div>

<?php if(!empty($jadwalLainnya)): ?>
    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        <?php foreach($jadwalLainnya as $hari => $items): ?>
            <section class="bg-white rounded-xl shadow-sm border border-amber-100 overflow-hidden">
                <div class="px-5 py-4 bg-amber-50 border-b border-amber-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800"><?php echo htmlspecialchars($hari); ?></h3>
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-amber-100 text-amber-700"><?php echo count($items); ?> mapel</span>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php foreach($items as $mapel): ?>
                        <div class="p-5">
                            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($mapel->nama_mata_pelajaran ?? '-'); ?></p>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($mapel->nama_kelas ?? 'Belum ditugaskan ke kelas'); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php else: ?>
    <div class="col-span-full bg-white rounded-xl shadow-sm p-12 border border-gray-100 text-center">
        <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Mata Pelajaran</h3>
        <p class="text-gray-500">Anda belum ditugaskan untuk mengampu mata pelajaran apapun.</p>
    </div>
<?php endif; ?>

<!-- Modal Tutup Presensi -->
<div id="tutupPresensiModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Tutup Presensi Mata Pelajaran</h3>
        </div>
        <form id="tutupPresensiForm">
            <input type="hidden" id="kelasIdTutup" name="kelas_id">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Kemajuan Kelas</label>
                    <textarea name="catatan" rows="4" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Tulis catatan kemajuan belajar hari ini..."></textarea>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeTutupPresensiModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Batal
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Simpan & Tutup
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentMapelId = null;

function bukaPresensi(mapelId) {
    if (confirm('Buka presensi untuk mata pelajaran ini?')) {
        fetch('index.php?action=buka_presensi_mapel', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `kelas_id=${mapelId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', 'Presensi mata pelajaran berhasil dibuka!');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('error', data.message || 'Gagal membuka presensi!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Gagal membuka presensi!');
        });
    }
}

function tutupPresensi(mapelId) {
    currentMapelId = mapelId;
    document.getElementById('kelasIdTutup').value = mapelId;
    document.getElementById('tutupPresensiModal').classList.remove('hidden');
}

function closeTutupPresensiModal() {
    document.getElementById('tutupPresensiModal').classList.add('hidden');
    document.getElementById('tutupPresensiForm').reset();
}

function lihatLaporan(mapelId) {
    window.location.href = `index.php?action=guru_laporan&kelas_id=${mapelId}`;
}

// Form submission untuk tutup presensi
document.getElementById('tutupPresensiForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('index.php?action=tutup_presensi_mapel', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeTutupPresensiModal();
            
            let message = data.message || 'Presensi mata pelajaran berhasil ditutup!';
            if (data.alpha_count > 0) {
                message += ` ${data.alpha_count} siswa ditandai alpha.`;
            }
            showNotification('success', message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('error', data.message || 'Gagal menutup presensi!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Gagal menutup presensi!');
    });
});

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

// Close modals when clicking outside
document.getElementById('tutupPresensiModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTutupPresensiModal();
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
