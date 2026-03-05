<?php
$page_title = "Manajemen Kelas";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Kelas</h2>
        <p class="text-gray-600">Kelola data kelas, siswa, dan mata pelajaran</p>
    </div>
    <button onclick="openAddKelasModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
        <i class="fas fa-plus"></i>
        <span>Tambah Kelas</span>
    </button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Statistik Kelas -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total Kelas</p>
                <h3 class="text-2xl font-bold text-gray-800"><?php echo count($kelas); ?></h3>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-school text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total Guru</p>
                <h3 class="text-2xl font-bold text-gray-800"><?php echo $totalGuru; ?></h3>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chalkboard-teacher text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total Siswa</p>
                <h3 class="text-2xl font-bold text-gray-800"><?php echo $totalSiswa; ?></h3>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Kelas</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tahun Ajaran</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Wali Kelas</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah Siswa</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah Mapel</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach($kelas as $k): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-school text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($k->nama_kelas); ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <?php echo htmlspecialchars($k->tahun_ajaran); ?>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <?php if($k->wali_kelas_id && $k->wali_kelas_nama): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-user-tie mr-1"></i>
                                <?php echo htmlspecialchars($k->wali_kelas_nama); ?>
                            </span>
                        <?php else: ?>
                            <span class="text-gray-400 italic">Belum ditentukan</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-user-graduate mr-1"></i>
                            <?php echo $k->jumlah_siswa; ?> siswa
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-book mr-1"></i>
                            <?php echo $k->jumlah_mapel; ?> mapel
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="kelolaMapel(<?php echo $k->id; ?>, '<?php echo htmlspecialchars(addslashes($k->nama_kelas)); ?>')" 
                                class="text-green-600 hover:text-green-700 mr-2" title="Kelola Mata Pelajaran">
                            <i class="fas fa-book"></i>
                        </button>
                        <button onclick="openEditKelasModal(this)" 
                                data-id="<?php echo $k->id; ?>"
                                data-nama_kelas="<?php echo htmlspecialchars($k->nama_kelas); ?>"
                                data-tahun_ajaran="<?php echo htmlspecialchars($k->tahun_ajaran); ?>"
                                data-wali_kelas_id="<?php echo $k->wali_kelas_id ?? ''; ?>"
                                class="text-blue-600 hover:text-blue-700 mr-2" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteKelas(<?php echo $k->id; ?>)" 
                                class="text-red-600 hover:text-red-700" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($kelas)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Belum ada kelas. Klik "Tambah Kelas" untuk membuat kelas baru.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Kelas -->
<div id="addKelasModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Tambah Kelas Baru</h3>
        </div>
        <form method="POST" action="index.php?action=admin_create_kelas">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kelas *</label>
                    <input type="text" name="nama_kelas" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Contoh: X RPL 1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran *</label>
                    <input type="text" name="tahun_ajaran" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Contoh: 2025/2026">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Wali Kelas</label>
                    <select name="wali_kelas_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Pilih Wali Kelas --</option>
                        <?php foreach($guru as $g): ?>
                            <option value="<?php echo $g->id; ?>"><?php echo htmlspecialchars($g->nama); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeAddKelasModal()" 
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kelas -->
<div id="editKelasModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Edit Kelas</h3>
        </div>
        <form method="POST" action="index.php?action=admin_update_kelas">
            <input type="hidden" name="id" id="edit_kelas_id">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kelas *</label>
                    <input type="text" name="nama_kelas" id="edit_kelas_nama" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran *</label>
                    <input type="text" name="tahun_ajaran" id="edit_kelas_tahun" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Wali Kelas</label>
                    <select name="wali_kelas_id" id="edit_kelas_wali" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Pilih Wali Kelas --</option>
                        <?php foreach($guru as $g): ?>
                            <option value="<?php echo $g->id; ?>"><?php echo htmlspecialchars($g->nama); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeEditKelasModal()" 
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<!-- NOTE: Siswa dikelola PER MATA PELAJARAN, bukan per kelas -->
<!-- Untuk mengelola siswa, masuk ke Mata Pelajaran dan kelola siswa di sana -->

<!-- Modal Kelola Mata Pelajaran -->
<div id="kelolaMapelModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Kelola Mata Pelajaran</h3>
            <p class="text-gray-600 text-sm" id="modalMapelKelasTitle"></p>
        </div>
        <div class="p-6 overflow-y-auto max-h-96">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Mata Pelajaran dalam Kelas -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">Mata Pelajaran dalam Kelas</h4>
                    <div id="mapelDalamKelas" class="space-y-2 max-h-64 overflow-y-auto">
                        <!-- Will be loaded via JS -->
                    </div>
                </div>
                
                <!-- Tambah Mata Pelajaran -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">Tambah Mata Pelajaran</h4>
                    <div class="space-y-3">
                        <select id="mapelSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Mata Pelajaran</option>
                        </select>
                        <button onclick="tambahMapelKeKelas()" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>Tambahkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-6 border-t border-gray-200 flex justify-end">
            <button onclick="closeKelolaMapelModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
let currentKelasId = null;

function openAddKelasModal() {
    document.getElementById('addKelasModal').classList.remove('hidden');
}

function closeAddKelasModal() {
    document.getElementById('addKelasModal').classList.add('hidden');
}

function openEditKelasModal(button) {
    var id = button.getAttribute('data-id');
    var nama = button.getAttribute('data-nama_kelas');
    var tahun = button.getAttribute('data-tahun_ajaran');
    var waliKelasId = button.getAttribute('data-wali_kelas_id');

    document.getElementById('edit_kelas_id').value = id;
    document.getElementById('edit_kelas_nama').value = nama;
    document.getElementById('edit_kelas_tahun').value = tahun;
    document.getElementById('edit_kelas_wali').value = waliKelasId || '';

    document.getElementById('editKelasModal').classList.remove('hidden');
}

function closeEditKelasModal() {
    document.getElementById('editKelasModal').classList.add('hidden');
}

// NOTE: Siswa dikelola PER MATA PELAJARAN, bukan per kelas
// Silakan kelola siswa melalui menu Mata Pelajaran

function kelolaMapel(kelasId, kelasNama) {
    currentKelasId = kelasId;
    document.getElementById('modalMapelKelasTitle').textContent = `Kelas: ${kelasNama}`;
    
    loadMapelDalamKelas(kelasId);
    loadMapelTersedia();
    
    document.getElementById('kelolaMapelModal').classList.remove('hidden');
}

function closeKelolaMapelModal() {
    document.getElementById('kelolaMapelModal').classList.add('hidden');
}

function loadMapelDalamKelas(kelasId) {
    const container = document.getElementById('mapelDalamKelas');
    container.innerHTML = '<p class="text-gray-500 text-center py-4">Loading...</p>';

    fetch(`index.php?action=admin_get_mapel_kelas&kelas_id=${kelasId}`)
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center py-4">Belum ada mata pelajaran</p>';
                return;
            }
            container.innerHTML = data.map(mapel => `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-book text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">${mapel.nama_mata_pelajaran}</p>
                            <p class="text-sm text-gray-500">${mapel.guru_pengampu_nama || 'Belum ada guru'}</p>
                        </div>
                    </div>
                    <button onclick="hapusMapelDariKelas(${mapel.id})" 
                            class="text-red-600 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `).join('');
        })
        .catch(() => {
            container.innerHTML = '<p class="text-red-500 text-center py-4">Gagal memuat data</p>';
        });
}

function loadMapelTersedia() {
    const select = document.getElementById('mapelSelect');
    select.innerHTML = '<option value="">Loading...</option>';

    fetch(`index.php?action=admin_get_mapel_tersedia_kelas&kelas_id=${currentKelasId}`)
        .then(res => res.json())
        .then(data => {
            select.innerHTML = '<option value="">Pilih Mata Pelajaran</option>';
            data.forEach(mapel => {
                select.innerHTML += `<option value="${mapel.id}">${mapel.nama_mata_pelajaran}</option>`;
            });
        })
        .catch(() => {
            showNotification('error', 'Gagal memuat daftar mata pelajaran');
        });
}

function tambahMapelKeKelas() {
    const select = document.getElementById('mapelSelect');
    const mapelId = select.value;
    
    if (!mapelId) {
        showNotification('warning', 'Pilih mata pelajaran terlebih dahulu!');
        return;
    }
    
    fetch('index.php?action=admin_add_mapel_kelas', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `mata_pelajaran_id=${encodeURIComponent(mapelId)}&kelas_id=${encodeURIComponent(currentKelasId)}`
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.success) {
            showNotification('success', 'Mata pelajaran berhasil ditambahkan!');
            loadMapelDalamKelas(currentKelasId);
            loadMapelTersedia();
            select.value = '';
        } else {
            showNotification('error', 'Gagal menambahkan mata pelajaran!');
        }
    })
    .catch(() => showNotification('error', 'Terjadi kesalahan!'));
}

function hapusMapelDariKelas(mapelId) {
    if (confirm('Apakah Anda yakin ingin menghapus mata pelajaran dari kelas ini?')) {
        fetch('index.php?action=admin_remove_mapel_kelas', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `mata_pelajaran_id=${encodeURIComponent(mapelId)}&kelas_id=${encodeURIComponent(currentKelasId)}`
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.success) {
                showNotification('success', 'Mata pelajaran berhasil dihapus!');
                loadMapelDalamKelas(currentKelasId);
                loadMapelTersedia();
            } else {
                showNotification('error', 'Gagal menghapus mata pelajaran!');
            }
        })
        .catch(() => showNotification('error', 'Terjadi kesalahan!'));
    }
}

function deleteKelas(kelasId) {
    if (confirm('Apakah Anda yakin ingin menghapus kelas ini? Semua data terkait akan terhapus.')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?action=admin_delete_kelas';
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = kelasId;
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modals when clicking outside
['addKelasModal', 'editKelasModal', 'kelolaMapelModal'].forEach(modalId => {
    document.getElementById(modalId).addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
});

function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        type === 'warning' ? 'bg-yellow-500 text-white' : 'bg-blue-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation' : 'info'}-circle"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
