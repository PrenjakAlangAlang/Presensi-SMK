<?php
$page_title = "Presensi Sekolah";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Kelola Sesi Presensi</h2>
            <p class="text-gray-600">Buka, perpanjang, atau tutup sesi presensi sekolah</p>
        </div>
        <div class="flex space-x-2">
            <button id="deleteSelectedBtn" onclick="deleteSelectedSessions()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hidden">
                <i class="fas fa-trash"></i>
                <span>Hapus Terpilih (<span id="selectedCount">0</span>)</span>
            </button>
            <button onclick="openCreateSessionModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Buat Sesi Manual</span>
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-left text-xs text-gray-600">
                        <th class="px-6 py-3">
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300" />
                                <span>Pilih Semua</span>
                            </div>
                        </th>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Waktu Buka</th>
                        <th class="px-6 py-3">Waktu Tutup</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach($sessions as $s): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <input type="checkbox" class="session-checkbox rounded border-gray-300" value="<?php echo $s->id; ?>" />
                        </td>
                        <td class="px-6 py-4"><?php echo $s->id; ?></td>
                        <td class="px-6 py-4"><?php echo $s->waktu_buka; ?></td>
                        <td class="px-6 py-4"><?php echo $s->waktu_tutup; ?></td>
                        <td class="px-6 py-4 capitalize"><?php echo $s->status; ?></td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <?php if($s->status == 'open'): ?>
                                    <button data-id="<?php echo $s->id; ?>" class="close-btn text-white bg-red-500 hover:bg-red-600 px-3 py-1 rounded">Tutup</button>
                                <?php endif; ?>
                                <button data-id="<?php echo $s->id; ?>" data-tutup="<?php echo htmlspecialchars($s->waktu_tutup, ENT_QUOTES); ?>" class="extend-btn text-white bg-yellow-500 hover:bg-yellow-600 px-3 py-1 rounded">Perpanjang</button>
                                <button data-id="<?php echo $s->id; ?>" class="delete-btn text-white bg-gray-600 hover:bg-gray-700 px-3 py-1 rounded">
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
</div>

<!-- Modal: Extend Session -->
<div id="extendSessionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Perpanjang Sesi Presensi</h3>
        </div>
        <form id="extendSessionForm" class="p-6 space-y-4">
            <input type="hidden" name="id" id="extend_session_id" />
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Tutup Baru</label>
                <input type="datetime-local" name="waktu_tutup" id="extend_waktu_tutup" required class="w-full px-4 py-3 border border-gray-300 rounded-lg" />
            </div>
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeExtendSessionModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">Perpanjang</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Create Session -->
<div id="createSessionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Buat Sesi Presensi Manual</h3>
        </div>
        <form id="createSessionForm" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Buka</label>
                <input type="datetime-local" name="waktu_buka" required class="w-full px-4 py-3 border border-gray-300 rounded-lg" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Tutup</label>
                <input type="datetime-local" name="waktu_tutup" required class="w-full px-4 py-3 border border-gray-300 rounded-lg" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (opsional)</label>
                <input type="text" name="note" placeholder="Catatan (opsional)" class="w-full px-4 py-3 border border-gray-300 rounded-lg" />
            </div>
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeCreateSessionModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Buat Sesi</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateSessionModal() { document.getElementById('createSessionModal').classList.remove('hidden'); }
function closeCreateSessionModal() { document.getElementById('createSessionModal').classList.add('hidden'); }
function openExtendSessionModal(id, currentTutup) {
    document.getElementById('extend_session_id').value = id;
    document.getElementById('extend_waktu_tutup').value = currentTutup ? currentTutup.replace(' ', 'T').slice(0,16) : '';
    document.getElementById('extendSessionModal').classList.remove('hidden');
}
function closeExtendSessionModal() { document.getElementById('extendSessionModal').classList.add('hidden'); }

// Close modals when clicking outside
['createSessionModal','extendSessionModal'].forEach(id => {
    const modal = document.getElementById(id);
    modal.addEventListener('click', e => { if(e.target === modal) modal.classList.add('hidden'); });
});

document.getElementById('createSessionForm').addEventListener('submit', function(e){
    e.preventDefault();
    const fd = new FormData(this);
    fetch('index.php?action=admin_kesiswaan_create_presensi_sekolah', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(json => { 
            if(json.success) location.reload(); 
            else alert('Gagal membuat sesi: ' + (json.message || 'Unknown error')); 
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Terjadi kesalahan saat membuat sesi');
        });
});

document.querySelectorAll('.close-btn').forEach(btn => btn.addEventListener('click', function(){
    if (!confirm('Tutup sesi presensi ini? Siswa yang belum presensi akan ditandai alpha.')) return;
    
    const fd = new FormData(); fd.append('id', this.dataset.id);
    fetch('index.php?action=admin_kesiswaan_close_presensi_sekolah', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(json => {
            if (json.success) {
                if (json.alpha_count > 0) {
                    alert(json.message || `Sesi ditutup. ${json.alpha_count} siswa ditandai alpha.`);
                }
                location.reload();
            } else {
                alert('Gagal menutup sesi: ' + (json.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Terjadi kesalahan saat menutup sesi');
        });
}));

document.querySelectorAll('.extend-btn').forEach(btn => btn.addEventListener('click', function(){
    openExtendSessionModal(this.dataset.id, this.dataset.tutup || '');
}));

document.getElementById('extendSessionForm').addEventListener('submit', function(e){
    e.preventDefault();
    const fd = new FormData(this);
    fetch('index.php?action=admin_kesiswaan_extend_presensi_sekolah', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(json => { 
            if(json.success) location.reload(); 
            else alert('Gagal perpanjang: ' + (json.message || 'Unknown error')); 
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Terjadi kesalahan saat perpanjang sesi');
        });
});

// Delete single session
document.querySelectorAll('.delete-btn').forEach(btn => btn.addEventListener('click', function(){
    if (!confirm('Hapus sesi presensi ini? Data presensi yang terkait akan ikut terhapus.')) return;
    
    const id = this.dataset.id;
    const fd = new FormData();
    fd.append('id', id);
    fetch('index.php?action=admin_kesiswaan_delete_presensi_sekolah', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(json => {
            if (json.success) {
                alert('Sesi berhasil dihapus');
                location.reload();
            } else {
                alert('Gagal menghapus sesi: ' + (json.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Terjadi kesalahan saat menghapus sesi');
        });
}));

// Checkbox management
const selectAllCheckbox = document.getElementById('selectAll');
const sessionCheckboxes = document.querySelectorAll('.session-checkbox');
const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
const selectedCountSpan = document.getElementById('selectedCount');

function updateSelectedCount() {
    const checkedBoxes = document.querySelectorAll('.session-checkbox:checked');
    const count = checkedBoxes.length;
    selectedCountSpan.textContent = count;
    
    if (count > 0) {
        deleteSelectedBtn.classList.remove('hidden');
    } else {
        deleteSelectedBtn.classList.add('hidden');
    }
}

selectAllCheckbox.addEventListener('change', function() {
    sessionCheckboxes.forEach(cb => cb.checked = this.checked);
    updateSelectedCount();
});

sessionCheckboxes.forEach(cb => cb.addEventListener('change', function() {
    const allChecked = Array.from(sessionCheckboxes).every(checkbox => checkbox.checked);
    selectAllCheckbox.checked = allChecked;
    updateSelectedCount();
}));

// Delete selected sessions
function deleteSelectedSessions() {
    const checkedBoxes = document.querySelectorAll('.session-checkbox:checked');
    const ids = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (ids.length === 0) {
        alert('Pilih minimal satu sesi untuk dihapus');
        return;
    }
    
    if (!confirm(`Hapus ${ids.length} sesi presensi terpilih? Data presensi yang terkait akan ikut terhapus.`)) return;
    
    const fd = new FormData();
    ids.forEach(id => fd.append('ids[]', id));
    
    fetch('index.php?action=admin_kesiswaan_delete_multiple_presensi_sekolah', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(json => {
            if (json.success) {
                alert(`${json.count} sesi berhasil dihapus`);
                location.reload();
            } else {
                alert('Gagal menghapus sesi: ' + (json.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Terjadi kesalahan saat menghapus sesi');
        });
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
