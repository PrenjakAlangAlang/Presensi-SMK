<?php
$page_title = "Buku Induk Siswa";
$saveAction = $saveAction ?? 'admin_save_buku_induk';
require_once __DIR__ . '/../layouts/header.php';

$dokumenFields = [
    'dokumen_ijasah' => 'Dokumen Ijazah',
    'dokumen_pas_foto' => 'Pas Foto',
    'dokumen_akta_kelahiran' => 'Akta Kelahiran',
    'dokumen_kk' => 'Kartu Keluarga',
];
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Buku Induk</h2>
        <p class="text-gray-600">Kelola biodata lengkap siswa dan dokumen pendukung</p>
    </div>
</div>

<div id="editPanel" class="hidden bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex items-start justify-between gap-4 mb-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Edit Buku Induk</h3>
            <p id="selectedStudentLabel" class="text-sm text-gray-500 mt-1"></p>
        </div>
        <button type="button" id="cancelEditBtn" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900">
            Batal
        </button>
    </div>

    <form id="editForm" method="POST" action="<?php echo BASE_URL; ?>/index.php?action=<?php echo $saveAction; ?>" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="hidden" name="user_id" />

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="nama" required class="w-full border rounded-lg px-4 py-2" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Induk Siswa</label>
            <input type="text" name="nis" required class="w-full border rounded-lg px-4 py-2" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Induk Siswa Nasional</label>
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
            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon Orang Tua/Wali</label>
            <input type="text" name="no_telp_ortu" class="w-full border rounded-lg px-4 py-2" placeholder="08xxxxxxxxxx" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Orang Tua/Wali</label>
            <input type="email" name="email_ortu" class="w-full border rounded-lg px-4 py-2" placeholder="email@example.com" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password Siswa</label>
            <input type="password" name="password" minlength="6" autocomplete="new-password" class="w-full border rounded-lg px-4 py-2" placeholder="Kosongkan jika tidak ingin mengubah password" />
            <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
        </div>

        <?php foreach ($dokumenFields as $field => $label): ?>
            <?php $existingKey = str_replace('dokumen_', '', $field); ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo $label; ?> <?php echo $field === 'dokumen_pas_foto' ? '(PDF/JPG/PNG)' : '(*PDF)'; ?></label>
                <input type="file" name="<?php echo $field; ?>" accept="<?php echo $field === 'dokumen_pas_foto' ? 'application/pdf,image/*' : 'application/pdf'; ?>" class="w-full" />
                <input type="hidden" name="existing_<?php echo $existingKey; ?>" id="existing_<?php echo $existingKey; ?>" />
                <p id="current_<?php echo $existingKey; ?>" class="hidden text-sm mt-1"></p>
            </div>
        <?php endforeach; ?>

        <div class="md:col-span-2 flex justify-end space-x-3">
            <button type="button" id="cancelEditBtnBottom" class="px-4 py-2 text-gray-600">Batal</button>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Simpan Perubahan</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-4 border-b border-gray-100">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Daftar Buku Induk</h3>
                <p class="text-sm text-gray-500"><span id="visibleCount"><?php echo count($records ?? []); ?></span> data ditampilkan</p>
            </div>
            <div class="relative w-full md:w-80">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input id="searchBukuInduk" type="search" placeholder="Cari nama, nomor induk siswa, nomor induk siswa nasional, alamat..." class="w-full border rounded-lg pl-10 pr-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" />
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-gray-600">
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Nomor Induk Siswa</th>
                    <th class="px-4 py-3">Nomor Induk Siswa Nasional</th>
                    <th class="px-4 py-3">Tempat, Tanggal Lahir</th>
                    <th class="px-4 py-3">Alamat</th>
                    <th class="px-4 py-3">Nomor Telepon Orang Tua</th>
                    <th class="px-4 py-3">Email Orang Tua</th>
                    <th class="px-4 py-3">Dokumen Pendukung</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody id="bukuIndukRows" class="divide-y divide-gray-100">
                <?php if(!empty($records)): foreach($records as $r): ?>
                    <?php
                        $totalDocs = 0;
                        foreach ($dokumenFields as $field => $label) {
                            if (!empty($r->{$field})) $totalDocs++;
                        }
                        $searchText = implode(' ', [
                            $r->nama ?? '',
                            $r->nis ?? '',
                            $r->nisn ?? '',
                            $r->tempat_lahir ?? '',
                            $r->tanggal_lahir ?? '',
                            $r->alamat ?? '',
                            $r->nama_ayah ?? '',
                            $r->nama_ibu ?? '',
                            $r->nama_wali ?? '',
                            $r->no_telp_ortu ?? '',
                            $r->email_ortu ?? '',
                        ]);
                    ?>
                    <tr class="buku-row" data-search="<?php echo htmlspecialchars(strtolower($searchText), ENT_QUOTES); ?>">
                        <td class="px-4 py-3"><?php echo htmlspecialchars($r->nama); ?></td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($r->nis); ?></td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($r->nisn); ?></td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($r->tempat_lahir . ', ' . $r->tanggal_lahir); ?></td>
                        <td class="px-4 py-3 max-w-xs truncate" title="<?php echo htmlspecialchars($r->alamat); ?>"><?php echo htmlspecialchars($r->alamat); ?></td>
                        <td class="px-4 py-3"><?php echo !empty($r->no_telp_ortu) ? htmlspecialchars($r->no_telp_ortu) : '-'; ?></td>
                        <td class="px-4 py-3"><?php echo !empty($r->email_ortu) ? htmlspecialchars($r->email_ortu) : '-'; ?></td>
                        <td class="px-4 py-3">
                            <?php if($totalDocs > 0): ?>
                                <button class="text-blue-600 hover:text-blue-800 view-docs-btn" data-record-id="<?php echo $r->id; ?>">
                                    <i class="fas fa-folder"></i> <?php echo $totalDocs; ?> Dokumen
                                </button>
                            <?php else: ?>
                                <span class="text-gray-400">Tidak ada dokumen</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <button class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 edit-btn"
                                    data-user-id="<?php echo $r->id; ?>"
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
                                    data-ijasah="<?php echo htmlspecialchars($r->dokumen_ijasah ?? '', ENT_QUOTES); ?>"
                                    data-pas-foto="<?php echo htmlspecialchars($r->dokumen_pas_foto ?? '', ENT_QUOTES); ?>"
                                    data-akta-kelahiran="<?php echo htmlspecialchars($r->dokumen_akta_kelahiran ?? '', ENT_QUOTES); ?>"
                                    data-kk="<?php echo htmlspecialchars($r->dokumen_kk ?? '', ENT_QUOTES); ?>">
                                <i class="fas fa-edit"></i>Edit
                            </button>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="9" class="px-4 py-4 text-center text-gray-500">Belum ada data.</td></tr>
                <?php endif; ?>
                <tr id="emptySearchRow" class="hidden">
                    <td colspan="9" class="px-4 py-4 text-center text-gray-500">Data tidak ditemukan.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="dokumenModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Dokumen Buku Induk</h3>
            <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="dokumenList" class="space-y-3"></div>
    </div>
</div>

<script>
const dokumenFields = <?php echo json_encode($dokumenFields); ?>;
const records = <?php echo json_encode($records); ?>;
const editPanel = document.getElementById('editPanel');
const selectedStudentLabel = document.getElementById('selectedStudentLabel');
const form = document.getElementById('editForm');
const searchInput = document.getElementById('searchBukuInduk');
const visibleCount = document.getElementById('visibleCount');
const emptySearchRow = document.getElementById('emptySearchRow');
const rows = Array.from(document.querySelectorAll('.buku-row'));

function setCurrentDocument(key, value, label) {
    const existingInput = document.getElementById(`existing_${key}`);
    const currentInfo = document.getElementById(`current_${key}`);
    existingInput.value = value || '';

    if (value) {
        currentInfo.classList.remove('hidden');
        currentInfo.innerHTML = `${label} saat ini: <a class="text-blue-600 hover:underline" target="_blank" href="${value}">Lihat</a>`;
    } else {
        currentInfo.classList.add('hidden');
        currentInfo.innerHTML = '';
    }
}

function closeEditPanel() {
    editPanel.classList.add('hidden');
    form.reset();
}

document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        form.reset();
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

        setCurrentDocument('ijasah', btn.dataset.ijasah, dokumenFields.dokumen_ijasah);
        setCurrentDocument('pas_foto', btn.dataset.pasFoto, dokumenFields.dokumen_pas_foto);
        setCurrentDocument('akta_kelahiran', btn.dataset.aktaKelahiran, dokumenFields.dokumen_akta_kelahiran);
        setCurrentDocument('kk', btn.dataset.kk, dokumenFields.dokumen_kk);

        selectedStudentLabel.textContent = `${btn.dataset.nama} - Nomor Induk Siswa ${btn.dataset.nis}`;
        editPanel.classList.remove('hidden');
        editPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});

document.getElementById('cancelEditBtn').addEventListener('click', closeEditPanel);
document.getElementById('cancelEditBtnBottom').addEventListener('click', closeEditPanel);

searchInput.addEventListener('input', () => {
    const keyword = searchInput.value.trim().toLowerCase();
    let shown = 0;

    rows.forEach(row => {
        const match = row.dataset.search.includes(keyword);
        row.classList.toggle('hidden', !match);
        if (match) shown++;
    });

    visibleCount.textContent = shown;
    emptySearchRow.classList.toggle('hidden', shown !== 0 || rows.length === 0);
});

const modal = document.getElementById('dokumenModal');
const closeModal = document.getElementById('closeModal');
const dokumenList = document.getElementById('dokumenList');

document.querySelectorAll('.view-docs-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const record = records.find(r => r.id == btn.dataset.recordId);
        dokumenList.innerHTML = '';

        Object.entries(dokumenFields).forEach(([field, label]) => {
            if (!record || !record[field]) return;
            const div = document.createElement('div');
            div.className = 'border rounded-lg p-4 flex justify-between items-center';
            div.innerHTML = `
                <div class="flex-1">
                    <p class="font-medium text-gray-800"><i class="fas fa-file text-blue-600 mr-2"></i>${label}</p>
                    <p class="text-sm text-gray-600">${record[field]}</p>
                </div>
                <a href="${record[field]}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg transition-colors">
                    <i class="fas fa-eye mr-1"></i>Lihat
                </a>
            `;
            dokumenList.appendChild(div);
        });

        if (!dokumenList.innerHTML) {
            dokumenList.innerHTML = '<p class="text-gray-500">Tidak ada dokumen.</p>';
        }
        modal.classList.remove('hidden');
    });
});

closeModal.addEventListener('click', () => modal.classList.add('hidden'));
modal.addEventListener('click', e => {
    if (e.target === modal) modal.classList.add('hidden');
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
