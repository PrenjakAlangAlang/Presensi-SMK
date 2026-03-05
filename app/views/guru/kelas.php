<?php
$page_title = "Mata Pelajaran Saya";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Mata Pelajaran yang Diampu</h2>
    <p class="text-gray-600">Kelola presensi dan monitoring siswa per kelas</p>
</div>

<?php if (!empty($kelasSaya)): ?>
    <?php 
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

<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
    <?php foreach($kelasSaya as $mapel): ?>
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chalkboard text-blue-600 text-xl"></i>
                </div>
                <div>
                    <?php if(isset($mapel->nama_kelas) && !empty($mapel->nama_kelas)): ?>
                    <h3 class="font-semibold text-gray-800 text-lg"><?php echo htmlspecialchars($mapel->nama_mata_pelajaran); ?></h3>
                    <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($mapel->nama_kelas); ?> - <?php echo htmlspecialchars($mapel->tahun_ajaran); ?></p>
                    <?php else: ?>
                    <h3 class="font-semibold text-gray-800 text-lg"><?php echo htmlspecialchars($mapel->nama_mata_pelajaran); ?></h3>
                    <p class="text-amber-600 text-sm italic">Belum ditugaskan ke kelas</p>
                    <?php endif; ?>
                    <?php if(!empty($mapel->jadwal)): ?>
                    <p class="text-gray-500 text-xs mt-1">
                        <i class="fas fa-clock mr-1"></i><?php echo htmlspecialchars($mapel->jadwal); ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="space-y-3 mb-4">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Total Siswa:</span>
                <span class="font-medium text-gray-800">
                    <?php 
                    echo isset($mapel->total_siswa) ? $mapel->total_siswa : '0'; 
                    ?> siswa
                </span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Presensi:</span>
                <span class="font-medium text-blue-600" id="statusPresensi<?php echo $mapel->id; ?>">
                    <?php echo isset($mapel->sesi_aktif) && $mapel->sesi_aktif ? 'Aktif' : 'Tutup'; ?>
                </span>
            </div>
        </div>

        <div class="flex space-x-2">
            <?php if (isset($mapel->sesi_aktif) && $mapel->sesi_aktif): ?>
                <button onclick="tutupPresensi(<?php echo $mapel->id; ?>)" 
                        class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg transition-colors text-sm font-medium">
                    <i class="fas fa-stop mr-1"></i>Tutup Sesi
                </button>
            <?php else: ?>
                <button onclick="bukaPresensi(<?php echo $mapel->id; ?>)" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors text-sm font-medium">
                    <i class="fas fa-play mr-1"></i>Buka Sesi
                </button>
            <?php endif; ?>

            <button onclick="lihatLaporan(<?php echo $mapel->id; ?>)" 
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition-colors text-sm font-medium">
                <i class="fas fa-chart-bar mr-1"></i>Laporan
            </button>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if(empty($kelasSaya)): ?>
    <div class="col-span-full bg-white rounded-xl shadow-sm p-12 border border-gray-100 text-center">
        <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Mata Pelajaran</h3>
        <p class="text-gray-500">Anda belum ditugaskan untuk mengampu mata pelajaran apapun.</p>
    </div>
    <?php endif; ?>
</div>

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
        fetch('index.php?action=buka_presensi_kelas', {
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
    
    fetch('index.php?action=tutup_presensi_kelas', {
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
