<?php
$page_title = "Kelas Saya";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Kelas yang Diampu</h2>
    <p class="text-gray-600">Kelola presensi dan monitoring siswa</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
    <?php foreach($kelasSaya as $kelas): ?>
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chalkboard text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800 text-lg"><?php echo $kelas->nama_kelas; ?></h3>
                    <p class="text-gray-600 text-sm"><?php echo $kelas->tahun_ajaran; ?></p>                    <?php if(!empty($kelas->jadwal)): ?>
                    <p class="text-gray-500 text-xs mt-1">
                        <i class="fas fa-clock mr-1"></i><?php echo htmlspecialchars($kelas->jadwal); ?>
                    </p>
                    <?php endif; ?>                </div>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                Aktif
            </span>
        </div>

            <div class="space-y-3 mb-4">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Total Siswa:</span>
                <span class="font-medium text-gray-800"><?php echo isset($kelas->total_siswa) ? $kelas->total_siswa : count($kelas->siswa); ?> siswa</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Presensi Dibuka:</span>
                <span class="font-medium text-blue-600" id="statusPresensi<?php echo $kelas->id; ?>">
                    <?php echo isset($kelas->sesi_aktif) && $kelas->sesi_aktif ? 'Aktif' : 'Tutup'; ?>
                </span>
            </div>
        </div>

        <div class="flex space-x-2">
            <?php if (isset($kelas->sesi_aktif) && $kelas->sesi_aktif): ?>
                <button onclick="tutupPresensi(<?php echo $kelas->id; ?>)" 
                        class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg transition-colors text-sm font-medium">
                    <i class="fas fa-stop mr-1"></i>Tutup Sesi
                </button>
            <?php else: ?>
                <button onclick="bukaPresensi(<?php echo $kelas->id; ?>)" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors text-sm font-medium">
                    <i class="fas fa-play mr-1"></i>Buka Sesi
                </button>
            <?php endif; ?>

            <button onclick="lihatLaporan(<?php echo $kelas->id; ?>)" 
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition-colors text-sm font-medium">
                <i class="fas fa-chart-bar mr-1"></i>Laporan
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Modal Lihat Siswa -->
<div id="siswaModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Daftar Siswa</h3>
            <p class="text-gray-600 text-sm" id="modalKelasInfo"></p>
        </div>
        <div class="p-6 overflow-y-auto max-h-96">
            <div id="siswaList" class="space-y-3">
                <!-- Daftar siswa akan dimuat di sini -->
            </div>
        </div>
        <div class="p-6 border-t border-gray-200 flex justify-end">
            <button onclick="closeSiswaModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal Tutup Presensi -->
<div id="tutupPresensiModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Tutup Presensi Kelas</h3>
        </div>
        <form id="tutupPresensiForm">
            <input type="hidden" id="kelasIdTutup" name="kelas_id">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Kemajuan Kelas</label>
                    <textarea name="catatan" rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                              placeholder="Isi laporan kemajuan kelas hari ini..."></textarea>
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
let currentKelasId = null;

function bukaPresensi(kelasId) {
    if (confirm('Buka presensi untuk kelas ini?')) {
        fetch('index.php?action=buka_presensi_kelas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `kelas_id=${kelasId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`statusPresensi${kelasId}`).textContent = 'Aktif';
                document.getElementById(`statusPresensi${kelasId}`).className = 'font-medium text-green-600';
                showNotification('success', 'Presensi kelas berhasil dibuka!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Gagal membuka presensi!');
        });
    }
}

function tutupPresensi(kelasId) {
    currentKelasId = kelasId;
    document.getElementById('kelasIdTutup').value = kelasId;
    document.getElementById('tutupPresensiModal').classList.remove('hidden');
}

function closeTutupPresensiModal() {
    document.getElementById('tutupPresensiModal').classList.add('hidden');
    document.getElementById('tutupPresensiForm').reset();
}

function lihatLaporan(kelasId) {
    window.location.href = `index.php?action=guru_laporan&kelas_id=${kelasId}`;
}

function lihatSiswa(kelasId, kelasNama) {
    currentKelasId = kelasId;
    document.getElementById('modalKelasInfo').textContent = `Kelas: ${kelasNama}`;
    
    // Load siswa data (simulated)
    const siswaList = document.getElementById('siswaList');
    siswaList.innerHTML = `
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Siswa A</p>
                        <p class="text-sm text-gray-600">Hadir: 07:45</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Hadir
                </span>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Siswa B</p>
                        <p class="text-sm text-gray-600">Hadir: 07:50</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Hadir
                </span>
            </div>
        </div>
    `;
    
    document.getElementById('siswaModal').classList.remove('hidden');
}

function closeSiswaModal() {
    document.getElementById('siswaModal').classList.add('hidden');
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
            document.getElementById(`statusPresensi${currentKelasId}`).textContent = 'Tutup';
            document.getElementById(`statusPresensi${currentKelasId}`).className = 'font-medium text-gray-600';
            closeTutupPresensiModal();
            
            let message = data.message || 'Presensi kelas berhasil ditutup!';
            if (data.alpha_count > 0) {
                message += ` ${data.alpha_count} siswa ditandai alpha.`;
            }
            showNotification('success', message);
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
document.getElementById('siswaModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSiswaModal();
    }
});

document.getElementById('tutupPresensiModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTutupPresensiModal();
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>