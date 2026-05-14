<?php
$page_title = "Manajemen Presensi Sekolah";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Presensi Sekolah</h2>
            <p class="text-gray-600">Kelola sesi presensi sekolah</p>
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
                        <td class="px-6 py-4"><?php echo $s->status; ?></td>
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

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-sm mx-4 text-center">
        <div class="flex justify-center mb-4">
            <svg class="animate-spin h-12 w-12 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Menutup Sesi Presensi</h3>
        
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
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
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
            <div class="border-t border-gray-200 pt-4">
                <button type="button" id="toggleMultipleSession" class="flex items-center gap-2 text-left text-gray-800 font-semibold">
                    <i id="multipleSessionIcon" class="fas fa-chevron-right text-sm"></i>
                    <span>Multiple sessions</span>
                </button>
                <div id="multipleSessionPanel" class="hidden mt-4 space-y-4">
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="repeat_enabled" value="1" id="repeatEnabled" class="rounded border-gray-300" />
                        <span>Ulangi sesi di atas dengan jadwal berikut</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-[140px_1fr] gap-3 text-sm">
                        <div class="text-gray-600 pt-2">Ulangi pada</div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <label class="flex items-center gap-2"><input type="checkbox" name="repeat_days[]" value="1" class="repeat-day rounded border-gray-300" /> Senin</label>
                            <label class="flex items-center gap-2"><input type="checkbox" name="repeat_days[]" value="2" class="repeat-day rounded border-gray-300" /> Selasa</label>
                            <label class="flex items-center gap-2"><input type="checkbox" name="repeat_days[]" value="3" class="repeat-day rounded border-gray-300" /> Rabu</label>
                            <label class="flex items-center gap-2"><input type="checkbox" name="repeat_days[]" value="4" class="repeat-day rounded border-gray-300" /> Kamis</label>
                            <label class="flex items-center gap-2"><input type="checkbox" name="repeat_days[]" value="5" class="repeat-day rounded border-gray-300" /> Jumat</label>
                            <label class="flex items-center gap-2"><input type="checkbox" name="repeat_days[]" value="6" class="repeat-day rounded border-gray-300" /> Sabtu</label>
                            <label class="flex items-center gap-2"><input type="checkbox" name="repeat_days[]" value="0" class="repeat-day rounded border-gray-300" /> Minggu</label>
                        </div>
                        <div class="text-gray-600 pt-2">Ulangi setiap</div>
                        <div class="flex items-center gap-2">
                            <input type="number" name="repeat_every_weeks" value="1" min="1" max="52" class="w-24 px-3 py-2 border border-gray-300 rounded-lg" />
                            <span>minggu</span>
                        </div>
                        <div class="text-gray-600 pt-2">Ulangi sampai</div>
                        <div>
                            <input type="date" name="repeat_until" id="repeatUntil" class="w-full md:w-56 px-3 py-2 border border-gray-300 rounded-lg" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeCreateSessionModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Buat Sesi</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateSessionModal() {
    document.getElementById('createSessionModal').classList.remove('hidden');
}

function closeCreateSessionModal() {
    document.getElementById('createSessionModal').classList.add('hidden');
}

const createSessionForm = document.getElementById('createSessionForm');
const multipleSessionPanel = document.getElementById('multipleSessionPanel');
const multipleSessionIcon = document.getElementById('multipleSessionIcon');
const repeatEnabled = document.getElementById('repeatEnabled');

document.getElementById('toggleMultipleSession').addEventListener('click', () => {
    multipleSessionPanel.classList.toggle('hidden');
    multipleSessionIcon.classList.toggle('fa-chevron-right');
    multipleSessionIcon.classList.toggle('fa-chevron-down');
});

createSessionForm.querySelector('input[name="waktu_buka"]').addEventListener('change', function() {
    if (!this.value) return;
    const selectedDate = new Date(this.value);
    const day = selectedDate.getDay().toString();
    createSessionForm.querySelectorAll('.repeat-day').forEach(cb => {
        cb.checked = cb.value === day;
    });
    document.getElementById('repeatUntil').min = this.value.slice(0, 10);
});

// Close modal when clicking outside
document.getElementById('createSessionModal').addEventListener('click', function(e) {
    if (e.target === this) closeCreateSessionModal();
});

createSessionForm.addEventListener('submit', function(e){
    e.preventDefault();
    if (repeatEnabled.checked) {
        const hasDay = Array.from(this.querySelectorAll('.repeat-day')).some(cb => cb.checked);
        if (!hasDay || !document.getElementById('repeatUntil').value) {
            alert('Pilih hari pengulangan dan tanggal selesai.');
            return;
        }
    }
    const fd = new FormData(this);
    fetch('index.php?action=admin_create_presensi_sekolah', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(json => {
            if (json.success) location.reload();
            else alert('Gagal membuat sesi: ' + (json.message || 'Unknown error'));
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Terjadi kesalahan saat membuat sesi');
        });
});

document.querySelectorAll('.close-btn').forEach(b => b.addEventListener('click', function(){
    if (!confirm('Tutup sesi presensi ini? Siswa yang belum presensi akan ditandai alpha.')) return;
    
    // Show loading
    document.getElementById('loadingOverlay').classList.remove('hidden');
    
    const id = this.dataset.id;
    const fd = new FormData(); fd.append('id', id);
    fetch('index.php?action=admin_close_presensi_sekolah', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(json => {
            // Hide loading
            document.getElementById('loadingOverlay').classList.add('hidden');
            
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
            // Hide loading on error
            document.getElementById('loadingOverlay').classList.add('hidden');
            console.error('Error:', err);
            alert('Terjadi kesalahan saat menutup sesi');
        });
}));

// open extend modal when clicking extend button
document.querySelectorAll('.extend-btn').forEach(b => b.addEventListener('click', function(){
    const id = this.dataset.id;
    const currentTutup = this.dataset.tutup || '';
    openExtendSessionModal(id, currentTutup);
}));

function openExtendSessionModal(id, currentTutup) {
    document.getElementById('extend_session_id').value = id;
    // convert MySQL datetime 'YYYY-MM-DD HH:MM:SS' to datetime-local value 'YYYY-MM-DDTHH:MM'
    if (currentTutup) {
        const v = currentTutup.replace(' ', 'T').slice(0,16);
        document.getElementById('extend_waktu_tutup').value = v;
    } else {
        document.getElementById('extend_waktu_tutup').value = '';
    }
    document.getElementById('extendSessionModal').classList.remove('hidden');
}

function closeExtendSessionModal() {
    document.getElementById('extendSessionModal').classList.add('hidden');
}

// Close extend modal when clicking outside
document.getElementById('extendSessionModal').addEventListener('click', function(e) {
    if (e.target === this) closeExtendSessionModal();
});

document.getElementById('extendSessionForm').addEventListener('submit', function(e){
    e.preventDefault();
    const fd = new FormData(this);
    fetch('index.php?action=admin_extend_presensi_sekolah', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(json => {
            if (json.success) location.reload();
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
    fetch('index.php?action=admin_delete_presensi_sekolah', { method: 'POST', body: fd })
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
    
    fetch('index.php?action=admin_delete_multiple_presensi_sekolah', { method: 'POST', body: fd })
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
