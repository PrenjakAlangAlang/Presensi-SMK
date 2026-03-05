<?php
$page_title = "Manajemen Mata Pelajaran";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">ManajemenMata Pelajaran</h2>
        <p class="text-gray-600">Kelola data mata pelajaran dan guru pengampu</p>
    </div>
    <button onclick="openAddMapelModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
        <i class="fas fa-plus"></i>
        <span>Tambah Mata Pelajaran</span>
    </button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Statistik -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total Mata Pelajaran</p>
                <h3 class="text-2xl font-bold text-gray-800"><?php echo count($mataPelajaran); ?></h3>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-book text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Guru Pengampu</p>
                <h3 class="text-2xl font-bold text-gray-800">
                    <?php 
                    $guruPengampu = array_filter(array_unique(array_column($mataPelajaran, 'guru_pengampu')));
                    echo count($guruPengampu); 
                    ?>
                </h3>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chalkboard-teacher text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total Kelas</p>
                <h3 class="text-2xl font-bold text-gray-800"><?php echo count($kelas); ?></h3>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-school text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Mata Pelajaran</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Guru Pengampu</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jadwal</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah Siswa</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach($mataPelajaran as $mp): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-book text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($mp->nama_mata_pelajaran); ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <?php if($mp->guru_pengampu_nama): ?>
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-chalkboard-teacher text-green-600 text-sm"></i>
                                </div>
                                <span class="text-gray-800"><?php echo htmlspecialchars($mp->guru_pengampu_nama); ?></span>
                            </div>
                        <?php else: ?>
                            <span class="text-gray-400">Belum ditentukan</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        <?php echo $mp->jadwal ? htmlspecialchars($mp->jadwal) : '<span class="text-gray-400">Tidak ada</span>'; ?>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <?php 
                        $jumlahSiswa = $mataPelajaranModel->getTotalSiswaByMataPelajaran($mp->id);
                        ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-users mr-1"></i>
                            <?php echo $jumlahSiswa; ?> siswa
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="kelolaSiswaMapel(<?php echo $mp->id; ?>, '<?php echo htmlspecialchars(addslashes($mp->nama_mata_pelajaran)); ?>')" 
                                class="text-purple-600 hover:text-purple-700 mr-2" title="Kelola Siswa">
                            <i class="fas fa-users"></i>
                        </button>
                        <button onclick="openEditMapelModal(this)" 
                                data-id="<?php echo $mp->id; ?>"
                                data-nama="<?php echo htmlspecialchars($mp->nama_mata_pelajaran); ?>"
                                data-guru="<?php echo $mp->guru_pengampu; ?>"
                                data-jadwal="<?php echo htmlspecialchars($mp->jadwal); ?>"
                                class="text-blue-600 hover:text-blue-700 mr-2" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteMapel(<?php echo $mp->id; ?>)" 
                                class="text-red-600 hover:text-red-700" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($mataPelajaran)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Belum ada mata pelajaran. Klik "Tambah Mata Pelajaran" untuk membuat data baru.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Mata Pelajaran -->
<div id="addMapelModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Tambah Mata Pelajaran Baru</h3>
        </div>
        <form method="POST" action="index.php?action=admin_create_mata_pelajaran">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Mata Pelajaran *</label>
                    <input type="text" name="nama_mata_pelajaran" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Contoh: Matematika">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Guru Pengampu</label>
                    <select name="guru_pengampu" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Guru</option>
                        <?php foreach($guru as $g): ?>
                            <option value="<?php echo $g->id; ?>"><?php echo htmlspecialchars($g->nama); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jadwal</label>
                    <textarea name="jadwal" rows="2" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Contoh: Senin 08:00-10:00"></textarea>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeAddMapelModal()" 
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

<!-- Modal Edit Mata Pelajaran -->
<div id="editMapelModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Edit Mata Pelajaran</h3>
        </div>
        <form method="POST" action="index.php?action=admin_update_mata_pelajaran">
            <input type="hidden" name="id" id="edit_mapel_id">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Mata Pelajaran *</label>
                    <input type="text" name="nama_mata_pelajaran" id="edit_mapel_nama" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Guru Pengampu</label>
                    <select name="guru_pengampu" id="edit_mapel_guru" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Guru</option>
                        <?php foreach($guru as $g): ?>
                            <option value="<?php echo $g->id; ?>"><?php echo htmlspecialchars($g->nama); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jadwal</label>
                    <textarea name="jadwal" id="edit_mapel_jadwal" rows="2" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeEditMapelModal()" 
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

<!-- Modal Kelola Siswa Mata Pelajaran -->
<div id="kelolaSiswaMapelModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Kelola Siswa</h3>
            <p class="text-gray-600 text-sm" id="modalMapelTitle"></p>
        </div>
        <div class="p-6 overflow-y-auto max-h-96">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Siswa dalam Mata Pelajaran -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">Siswa Terdaftar</h4>
                    <div id="siswaDalamMapel" class="space-y-2 max-h-64 overflow-y-auto">
                        <!-- Will be loaded via JS -->
                    </div>
                </div>
                
                <!-- Tambah Siswa -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">Tambah Siswa</h4>
                    <div class="space-y-3">
                        <select id="siswaMapelSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="">Pilih Siswa</option>
                        </select>
                        <button onclick="tambahSiswaKeMapel()" 
                                class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>Tambahkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-6 border-t border-gray-200 flex justify-end">
            <button onclick="closeKelolaSiswaMapelModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
let currentMapelId = null;

function openAddMapelModal() {
    document.getElementById('addMapelModal').classList.remove('hidden');
}

function closeAddMapelModal() {
    document.getElementById('addMapelModal').classList.add('hidden');
}

function openEditMapelModal(button) {
    var id = button.getAttribute('data-id');
    var nama = button.getAttribute('data-nama');
    var guru = button.getAttribute('data-guru');
    var jadwal = button.getAttribute('data-jadwal');

    document.getElementById('edit_mapel_id').value = id;
    document.getElementById('edit_mapel_nama').value = nama;
    document.getElementById('edit_mapel_guru').value = guru || '';
    document.getElementById('edit_mapel_jadwal').value = jadwal || '';

    document.getElementById('editMapelModal').classList.remove('hidden');
}

function closeEditMapelModal() {
    document.getElementById('editMapelModal').classList.add('hidden');
}

function deleteMapel(mapelId) {
    if (confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?action=admin_delete_mata_pelajaran';
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = mapelId;
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

function kelolaSiswaMapel(mapelId, mapelNama) {
    currentMapelId = mapelId;
    document.getElementById('modalMapelTitle').textContent = `Mata Pelajaran: ${mapelNama}`;
    
    loadSiswaDalamMapel(mapelId);
    loadSiswaTersediaMapel();
    
    document.getElementById('kelolaSiswaMapelModal').classList.remove('hidden');
}

function closeKelolaSiswaMapelModal() {
    document.getElementById('kelolaSiswaMapelModal').classList.add('hidden');
}

function loadSiswaDalamMapel(mapelId) {
    const container = document.getElementById('siswaDalamMapel');
    container.innerHTML = '<p class="text-gray-500 text-center py-4">Loading...</p>';

    fetch(`index.php?action=admin_get_siswa_mapel&mapel_id=${mapelId}`)
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center py-4">Belum ada siswa</p>';
                return;
            }
            container.innerHTML = data.map(siswa => `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-purple-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">${siswa.nama}</p>
                        </div>
                    </div>
                    <button onclick="hapusSiswaDariMapel(${siswa.id})" 
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

function loadSiswaTersediaMapel() {
    const select = document.getElementById('siswaMapelSelect');
    select.innerHTML = '<option value="">Loading...</option>';

    fetch(`index.php?action=admin_get_siswa_tersedia_mapel&mapel_id=${currentMapelId}`)
        .then(res => res.json())
        .then(data => {
            select.innerHTML = '<option value="">Pilih Siswa</option>';
            data.forEach(siswa => {
                const namaLengkap = siswa.nama || 'Nama tidak tersedia';
                select.innerHTML += `<option value="${siswa.id}">${namaLengkap}</option>`;
            });
        })
        .catch(() => {
            showNotification('error', 'Gagal memuat daftar siswa');
        });
}

function tambahSiswaKeMapel() {
    const select = document.getElementById('siswaMapelSelect');
    const siswaId = select.value;
    
    if (!siswaId) {
        showNotification('warning', 'Pilih siswa terlebih dahulu!');
        return;
    }
    
    fetch('index.php?action=admin_add_siswa_mapel', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `siswa_id=${encodeURIComponent(siswaId)}&mapel_id=${encodeURIComponent(currentMapelId)}`
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.success) {
            showNotification('success', 'Siswa berhasil ditambahkan!');
            loadSiswaDalamMapel(currentMapelId);
            loadSiswaTersediaMapel();
            select.value = '';
            // Reload halaman untuk update jumlah siswa di tabel
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('error', 'Gagal menambahkan siswa!');
        }
    })
    .catch(() => showNotification('error', 'Terjadi kesalahan!'));
}

function hapusSiswaDariMapel(siswaId) {
    if (confirm('Apakah Anda yakin ingin menghapus siswa dari mata pelajaran ini?')) {
        fetch('index.php?action=admin_remove_siswa_mapel', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `siswa_id=${encodeURIComponent(siswaId)}&mapel_id=${encodeURIComponent(currentMapelId)}`
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.success) {
                showNotification('success', 'Siswa berhasil dihapus!');
                loadSiswaDalamMapel(currentMapelId);
                loadSiswaTersediaMapel();
                // Reload halaman untuk update jumlah siswa di tabel
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('error', 'Gagal menghapus siswa!');
            }
        })
        .catch(() => showNotification('error', 'Terjadi kesalahan!'));
    }
}

function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
    } text-white`;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Close modals when clicking outside
['addMapelModal', 'editMapelModal', 'kelolaSiswaMapelModal'].forEach(modalId => {
    document.getElementById(modalId).addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
