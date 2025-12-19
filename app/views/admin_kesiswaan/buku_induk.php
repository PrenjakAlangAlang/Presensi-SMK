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
            <label class="block text-sm font-medium text-gray-700 mb-1">Dokumen PDF (opsional)</label>
            <input type="file" name="dokumen_pdf" accept="application/pdf" class="w-full" />
            <input type="hidden" name="existing_pdf" id="existing_pdf" />
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
                        <td class="px-4 py-3">
                            <?php if(!empty($r->dokumen_pdf)): ?>
                                <a href="<?php echo $r->dokumen_pdf; ?>" target="_blank" class="text-blue-600 hover:underline">Lihat</a>
                            <?php else: ?>
                                <span class="text-gray-400">-</span>
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
                                    data-dokumen="<?php echo htmlspecialchars($r->dokumen_pdf ?? '', ENT_QUOTES); ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="7" class="px-4 py-4 text-center text-gray-500">Belum ada data.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
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
        document.getElementById('existing_pdf').value = btn.dataset.dokumen;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
