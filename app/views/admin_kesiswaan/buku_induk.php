<?php
$page_title = "Buku Induk Siswa";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Buku Induk</h2>
        <p class="text-gray-600">Kelola biodata lengkap siswa dan dokumen pendukung</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Tambah / Perbarui Buku Induk</h3>
    <form method="POST" action="<?php echo BASE_URL; ?>/public/index.php?action=admin_kesiswaan_save_buku_induk" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Siswa</label>
            <select name="user_id" required class="w-full border rounded-lg px-4 py-2">
                <option value="">-- Pilih Siswa --</option>
                <?php foreach($siswa as $s): ?>
                    <option value="<?php echo $s->id; ?>"><?php echo htmlspecialchars($s->nama); ?> (<?php echo htmlspecialchars($s->email); ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="nama" required class="w-full border rounded-lg px-4 py-2" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">NIS</label>
            <input type="text" name="nis" required class="w-full border rounded-lg px-4 py-2" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">NISN</label>
            <input type="text" name="nisn" required class="w-full border rounded-lg px-4 py-2" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" required class="w-full border rounded-lg px-4 py-2" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" required class="w-full border rounded-lg px-4 py-2" />
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
            <textarea name="alamat" rows="3" class="w-full border rounded-lg px-4 py-2" required></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ayah</label>
            <input type="text" name="nama_ayah" class="w-full border rounded-lg px-4 py-2" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu</label>
            <input type="text" name="nama_ibu" class="w-full border rounded-lg px-4 py-2" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Wali</label>
            <input type="text" name="nama_wali" class="w-full border rounded-lg px-4 py-2" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon Orang Tua/ Wali</label>
            <input type="text" name="no_telp_ortu" class="w-full border rounded-lg px-4 py-2" placeholder="08xxxxxxxxxx" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Orang Tua/ Wali</label>
            <input type="email" name="email_ortu" class="w-full border rounded-lg px-4 py-2" placeholder="email@example.com" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dokumen KTP (*PDF)</label>
            <input type="file" name="dokumen_pdf" accept="application/pdf" class="w-full" />
            <input type="hidden" name="existing_pdf" id="existing_pdf" />
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Dokumen Tambahan (Multiple PDF)</label>
            <div id="file-upload-area" class="space-y-3">
                <div class="file-input-group flex gap-2 items-start">
                    <div class="flex-1">
                        <input type="file" name="dokumen_files[]" accept="application/pdf" class="w-full mb-1" />
                        <input type="text" name="keterangan[]" placeholder="Keterangan dokumen (opsional)" class="w-full border rounded px-3 py-1 text-sm" />
                    </div>
                    <button type="button" class="add-file-btn bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="md:col-span-2 flex justify-end space-x-3">
            <button type="reset" class="px-4 py-2 text-gray-600">Reset</button>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Simpan</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">Daftar Buku Induk</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-gray-600">
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">NIS</th>
                    <th class="px-4 py-3">NISN</th>
                    <th class="px-4 py-3">TTL</th>
                    <th class="px-4 py-3">Alamat</th>
                    <th class="px-4 py-3">Nama Ayah</th>
                    <th class="px-4 py-3">Nama Ibu</th>
                    <th class="px-4 py-3">Nama Wali</th>
                    <th class="px-4 py-3">No. Telp Ortu</th>
                    <th class="px-4 py-3">Email Ortu</th>
                    <th class="px-4 py-3">Dokumen</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if(!empty($records)): foreach($records as $r): ?>
                    <tr>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($r->nama); ?></td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($r->nis); ?></td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($r->nisn); ?></td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($r->tempat_lahir . ', ' . $r->tanggal_lahir); ?></td>
                        <td class="px-4 py-3 max-w-xs truncate" title="<?php echo htmlspecialchars($r->alamat); ?>"><?php echo htmlspecialchars($r->alamat); ?></td>
                        <td class="px-4 py-3"><?php echo !empty($r->nama_ayah) ? htmlspecialchars($r->nama_ayah) : '-'; ?></td>
                        <td class="px-4 py-3"><?php echo !empty($r->nama_ibu) ? htmlspecialchars($r->nama_ibu) : '-'; ?></td>
                        <td class="px-4 py-3"><?php echo !empty($r->nama_wali) ? htmlspecialchars($r->nama_wali) : '-'; ?></td>
                        <td class="px-4 py-3"><?php echo !empty($r->no_telp_ortu) ? htmlspecialchars($r->no_telp_ortu) : '-'; ?></td>
                        <td class="px-4 py-3"><?php echo !empty($r->email_ortu) ? htmlspecialchars($r->email_ortu) : '-'; ?></td>
                        <td class="px-4 py-3">
                            <?php if(!empty($r->dokumen_pdf)): ?>
                                <a href="<?php echo $r->dokumen_pdf; ?>" target="_blank" class="text-blue-600 hover:underline"><i class="fas fa-eye"></i></a>
                            <?php else: ?>
                                <span class="text-gray-400">-</span>
                            <?php endif; ?>
                            <?php if(!empty($r->dokumen) && count($r->dokumen) > 0): ?>
                                <button class="text-green-600 hover:text-green-800 ml-2 view-docs-btn" 
                                        data-record-id="<?php echo $r->id; ?>" 
                                        title="Lihat semua dokumen (<?php echo count($r->dokumen); ?>)">
                                    <i class="fas fa-folder-open"></i> (<?php echo count($r->dokumen); ?>)
                                </button>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <button class="text-blue-600 hover:text-blue-800 edit-btn"
                                    data-user-id="<?php echo $r->user_id; ?>"
                                    data-nama="<?php echo htmlspecialchars($r->nama, ENT_QUOTES); ?>"
                                    data-nis="<?php echo htmlspecialchars($r->nis, ENT_QUOTES); ?>"
                                    data-nisn="<?php echo htmlspecialchars($r->nisn, ENT_QUOTES); ?>"
                                    data-tempat="<?php echo htmlspecialchars($r->tempat_lahir, ENT_QUOTES); ?>"
                                    data-tanggal="<?php echo $r->tanggal_lahir; ?>"
                                    data-alamat="<?php echo htmlspecialchars($r->alamat, ENT_QUOTES); ?>"
                                    data-nama-ayah="<?php echo htmlspecialchars($r->nama_ayah ?? '', ENT_QUOTES); ?>"
                                    data-nama-ibu="<?php echo htmlspecialchars($r->nama_ibu ?? '', ENT_QUOTES); ?>"
                                    data-nama-wali="<?php echo htmlspecialchars($r->nama_wali ?? '', ENT_QUOTES); ?>"
                                    data-no-telp="<?php echo htmlspecialchars($r->no_telp_ortu ?? '', ENT_QUOTES); ?>"
                                    data-email="<?php echo htmlspecialchars($r->email_ortu ?? '', ENT_QUOTES); ?>"
                                    data-dokumen="<?php echo htmlspecialchars($r->dokumen_pdf ?? '', ENT_QUOTES); ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="12" class="px-4 py-4 text-center text-gray-500">Belum ada data.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal untuk melihat semua dokumen -->
<div id="dokumenModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Dokumen Buku Induk</h3>
            <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="dokumenList" class="space-y-3">
            <!-- Dokumen akan dimuat di sini via JavaScript -->
        </div>
    </div>
</div>

<script>
// Add more file upload inputs
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('file-upload-area');
    
    uploadArea.addEventListener('click', function(e) {
        if(e.target.closest('.add-file-btn')) {
            const newGroup = document.createElement('div');
            newGroup.className = 'file-input-group flex gap-2 items-start';
            newGroup.innerHTML = `
                <div class="flex-1">
                    <input type="file" name="dokumen_files[]" accept="application/pdf" class="w-full mb-1" />
                    <input type="text" name="keterangan[]" placeholder="Keterangan dokumen (opsional)" class="w-full border rounded px-3 py-1 text-sm" />
                </div>
                <button type="button" class="remove-file-btn bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded">
                    <i class="fas fa-minus"></i>
                </button>
            `;
            uploadArea.appendChild(newGroup);
        }
        
        if(e.target.closest('.remove-file-btn')) {
            e.target.closest('.file-input-group').remove();
        }
    });
});

// Isi form ketika klik edit
const editButtons = document.querySelectorAll('.edit-btn');
const form = document.querySelector('form');
editButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        form.user_id.value = btn.dataset.userId;
        form.nama.value = btn.dataset.nama;
        form.nis.value = btn.dataset.nis;
        form.nisn.value = btn.dataset.nisn;
        form.tempat_lahir.value = btn.dataset.tempat;
        form.tanggal_lahir.value = btn.dataset.tanggal;
        form.alamat.value = btn.dataset.alamat;
        form.nama_ayah.value = btn.dataset.namaAyah || '';
        form.nama_ibu.value = btn.dataset.namaIbu || '';
        form.nama_wali.value = btn.dataset.namaWali || '';
        form.no_telp_ortu.value = btn.dataset.noTelp || '';
        form.email_ortu.value = btn.dataset.email || '';
        document.getElementById('existing_pdf').value = btn.dataset.dokumen;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});

// Handle view documents modal
const modal = document.getElementById('dokumenModal');
const closeModal = document.getElementById('closeModal');
const dokumenList = document.getElementById('dokumenList');

document.querySelectorAll('.view-docs-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const recordId = btn.dataset.recordId;
        // Get dokumen from PHP data
        const record = <?php echo json_encode($records); ?>.find(r => r.id == recordId);
        
        if(record && record.dokumen) {
            dokumenList.innerHTML = '';
            record.dokumen.forEach(dok => {
                const div = document.createElement('div');
                div.className = 'border rounded-lg p-4 flex justify-between items-center';
                div.innerHTML = `
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">${dok.nama_file}</p>
                        ${dok.keterangan ? `<p class="text-sm text-gray-600">${dok.keterangan}</p>` : ''}
                        <p class="text-xs text-gray-500 mt-1">${new Date(dok.created_at).toLocaleString('id-ID')}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="${dok.path_file}" target="_blank" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form method="POST" action="<?php echo BASE_URL; ?>/public/index.php?action=admin_kesiswaan_delete_dokumen" 
                              onsubmit="return confirm('Hapus dokumen ini?')" class="inline">
                            <input type="hidden" name="dokumen_id" value="${dok.id}" />
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                `;
                dokumenList.appendChild(div);
            });
            modal.classList.remove('hidden');
        }
    });
});

closeModal.addEventListener('click', () => {
    modal.classList.add('hidden');
});

modal.addEventListener('click', (e) => {
    if(e.target === modal) {
        modal.classList.add('hidden');
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
