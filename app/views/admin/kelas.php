<?php
$page_title = "Manajemen Kelas";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Kelas</h2>
        <p class="text-gray-600">Kelola data kelas dan penempatan siswa</p>
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
                <p class="text-gray-500 text-sm">Total Kelas</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo count($kelas); ?></h3>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chalkboard text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Guru</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $totalGuru ?? '0'; ?></h3>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chalkboard-teacher text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Siswa</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $totalSiswa ?? '0'; ?></h3>
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
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Nama Kelas</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Tahun Ajaran</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Wali Kelas</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Jadwal Kelas</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Jumlah Siswa</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Status</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach($kelas as $k): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chalkboard text-blue-600"></i>
                            </div>
                            <span class="font-medium text-gray-800"><?php echo htmlspecialchars($k->nama_kelas); ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($k->tahun_ajaran); ?></td>
                    <td class="px-6 py-4">
                        <?php if($k->wali_kelas_nama): ?>
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-green-600 text-sm"></i>
                                </div>
                                <span class="text-gray-800"><?php echo htmlspecialchars($k->wali_kelas_nama); ?></span>
                            </div>
                        <?php else: ?>
                            <span class="text-gray-400">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-gray-700 text-sm">
                            <i class="fas fa-clock text-gray-500 mr-1"></i>
                            <?php echo htmlspecialchars($k->jadwal ?? 'Belum diatur'); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <?php 
                        $jumlahSiswa = count($kelasModel->getSiswaInKelas($k->id));
                        echo $jumlahSiswa . ' siswa';
                        ?>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Aktif
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                                                    <button 
                                                        data-id="<?php echo $k->id; ?>"
                                                        data-nama_kelas="<?php echo htmlspecialchars($k->nama_kelas, ENT_QUOTES); ?>"
                                                        data-tahun_ajaran="<?php echo htmlspecialchars($k->tahun_ajaran, ENT_QUOTES); ?>"
                                                        data-wali_kelas="<?php echo $k->wali_kelas; ?>"
                                                        data-jadwal="<?php echo htmlspecialchars($k->jadwal ?? '', ENT_QUOTES); ?>"
                                                        onclick="openEditKelasModal(this)" 
                                                        class="text-blue-600 hover:text-blue-800 transition-colors p-2 rounded-lg hover:bg-blue-50">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                            <button onclick="kelolaSiswa(<?php echo $k->id; ?>)" 
                                    class="text-green-600 hover:text-green-800 transition-colors p-2 rounded-lg hover:bg-green-50">
                                <i class="fas fa-users"></i>
                            </button>
                            <button onclick="deleteKelas(<?php echo $k->id; ?>)" 
                                    class="text-red-600 hover:text-red-800 transition-colors p-2 rounded-lg hover:bg-red-50">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kelas</label>
                    <input type="text" name="nama_kelas" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Contoh: XI RPL 1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Contoh: 2024/2025">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Wali Kelas</label>
                    <select name="wali_kelas" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        <option value="">Pilih Wali Kelas</option>
                        <?php foreach($guru as $g): ?>
                            <option value="<?php echo $g->id; ?>"><?php echo htmlspecialchars($g->nama); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jadwal</label>
                    <input type="text" name="jadwal" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Contoh: Senin, 08:00 - 09:30">
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeAddKelasModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Batal
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kelas</label>
                    <input type="text" name="nama_kelas" id="edit_kelas_nama" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" id="edit_kelas_tahun" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Wali Kelas</label>
                    <select name="wali_kelas" id="edit_kelas_wali" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        <option value="">Pilih Wali Kelas</option>
                        <?php foreach($guru as $g): ?>
                            <option value="<?php echo $g->id; ?>"><?php echo htmlspecialchars($g->nama); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jadwal</label>
                    <input type="text" name="jadwal" id="edit_kelas_jadwal" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Contoh: Senin, 08:00 - 09:30">
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeEditKelasModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Batal
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Kelola Siswa -->
<div id="kelolaSiswaModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Kelola Siswa Kelas</h3>
            <p class="text-gray-600 text-sm" id="modalKelasTitle"></p>
        </div>
        <div class="p-6 overflow-y-auto max-h-96">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Daftar Siswa di Kelas -->
                <div>
                    <h4 class="font-semibold text-gray-800 mb-4">Siswa dalam Kelas</h4>
                    <div id="siswaDalamKelas" class="space-y-2">
                        <!-- Data akan dimuat via JavaScript -->
                    </div>
                </div>
                
                <!-- Tambah Siswa ke Kelas -->
                <div>
                    <h4 class="font-semibold text-gray-800 mb-4">Tambah Siswa</h4>
                    <div class="space-y-3">
                        <select id="siswaSelect" class="w-full p-3 border border-gray-300 rounded-lg">
                            <option value="">Pilih Siswa</option>
                            <!-- Options akan dimuat via JavaScript -->
                        </select>
                        <button onclick="tambahSiswaKeKelas()" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>Tambah ke Kelas
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-6 border-t border-gray-200 flex justify-end">
            <button onclick="closeKelolaSiswaModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
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

function editKelas(kelasId) {
    // kept for backward compatibility
    openEditKelasModal(document.querySelector('[data-id="' + kelasId + '"]'));
}

function openEditKelasModal(button) {
    var id = button.getAttribute('data-id');
    var nama = button.getAttribute('data-nama_kelas');
    var tahun = button.getAttribute('data-tahun_ajaran');
    var wali = button.getAttribute('data-wali_kelas');
    var jadwal = button.getAttribute('data-jadwal');

    document.getElementById('edit_kelas_id').value = id;
    document.getElementById('edit_kelas_nama').value = nama;
    document.getElementById('edit_kelas_tahun').value = tahun;
    document.getElementById('edit_kelas_wali').value = wali;
    document.getElementById('edit_kelas_jadwal').value = jadwal;

    document.getElementById('editKelasModal').classList.remove('hidden');
}

function closeEditKelasModal() {
    document.getElementById('editKelasModal').classList.add('hidden');
}

function kelolaSiswa(kelasId) {
    currentKelasId = kelasId;
    
    // Load data kelas
    const kelas = <?php echo json_encode($kelas); ?>;
    const selectedKelas = kelas.find(k => k.id == kelasId);
    
    if (selectedKelas) {
        document.getElementById('modalKelasTitle').textContent = `Kelas: ${selectedKelas.nama_kelas}`;
    }
    
    // Load siswa dalam kelas (from server)
    loadSiswaDalamKelas(kelasId);

    // Load siswa yang belum memiliki kelas
    loadSiswaTersedia();
    
    document.getElementById('kelolaSiswaModal').classList.remove('hidden');
}

function closeKelolaSiswaModal() {
    document.getElementById('kelolaSiswaModal').classList.add('hidden');
}

function loadSiswaDalamKelas(kelasId) {
    const container = document.getElementById('siswaDalamKelas');
    container.innerHTML = '';

    fetch(`index.php?action=admin_get_siswa_kelas&kelas_id=${kelasId}`)
        .then(res => res.json())
        .then(data => {
            if (!data || data.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center py-4">Belum ada siswa di kelas ini</p>';
                return;
            }

            data.forEach(siswa => {
                const div = document.createElement('div');
                div.className = 'flex items-center justify-between p-3 border border-gray-200 rounded-lg';
                div.innerHTML = `
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">${siswa.nama}</p>
                            <p class="text-sm text-gray-600">${siswa.nis || ''}</p>
                        </div>
                    </div>
                    <button onclick="hapusSiswaDariKelas(${siswa.id})" class="text-red-600 hover:text-red-800 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                container.appendChild(div);
            });
        })
        .catch(() => {
            container.innerHTML = '<p class="text-gray-500 text-center py-4">Gagal memuat data siswa</p>';
        });
}

function loadSiswaTersedia() {
    const select = document.getElementById('siswaSelect');
    select.innerHTML = '<option value="">Pilih Siswa</option>';

    fetch(`index.php?action=admin_get_siswa_tersedia&kelas_id=${currentKelasId}`)
        .then(res => res.json())
        .then(data => {
            data.forEach(siswa => {
                const option = document.createElement('option');
                option.value = siswa.id;
                option.textContent = siswa.nis ? `${siswa.nama} (${siswa.nis})` : siswa.nama;
                select.appendChild(option);
            });
        })
        .catch(() => {
            showNotification('error', 'Gagal memuat daftar siswa tersedia');
        });
}

function tambahSiswaKeKelas() {
    const select = document.getElementById('siswaSelect');
    const siswaId = select.value;
    
    if (!siswaId) {
        showNotification('warning', 'Pilih siswa terlebih dahulu!');
        return;
    }
    
    fetch('index.php?action=admin_add_siswa_kelas', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `siswa_id=${encodeURIComponent(siswaId)}&kelas_id=${encodeURIComponent(currentKelasId)}`
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.success) {
            showNotification('success', 'Siswa berhasil ditambahkan ke kelas!');
            loadSiswaDalamKelas(currentKelasId);
            loadSiswaTersedia();
            select.value = '';
        } else {
            showNotification('error', 'Gagal menambahkan siswa ke kelas');
        }
    })
    .catch(() => showNotification('error', 'Gagal menambahkan siswa ke kelas'));
}

function hapusSiswaDariKelas(siswaId) {
    if (confirm('Apakah Anda yakin ingin menghapus siswa dari kelas ini?')) {
        fetch('index.php?action=admin_remove_siswa_kelas', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `siswa_id=${encodeURIComponent(siswaId)}&kelas_id=${encodeURIComponent(currentKelasId)}`
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.success) {
                showNotification('success', 'Siswa berhasil dihapus dari kelas!');
                loadSiswaDalamKelas(currentKelasId);
                loadSiswaTersedia();
            } else {
                showNotification('error', 'Gagal menghapus siswa dari kelas');
            }
        })
        .catch(() => showNotification('error', 'Gagal menghapus siswa dari kelas'));
    }
}

function deleteKelas(kelasId) {
    if (confirm('Apakah Anda yakin ingin menghapus kelas ini? Semua data siswa dalam kelas akan terpengaruh.')) {
        // submit a small form to delete
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
document.getElementById('addKelasModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddKelasModal();
    }
});

document.getElementById('kelolaSiswaModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeKelolaSiswaModal();
    }
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
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : type === 'warning' ? 'exclamation-circle' : 'info'}-circle"></i>
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