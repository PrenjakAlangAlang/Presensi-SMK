<?php
$page_title = "Buku Induk Saya";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Biodata Buku Induk</h2>
        <p class="text-gray-600">Perbarui data diri dan unggah dokumen PDF jika diminta.</p>
    </div>  

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="<?php echo BASE_URL; ?>/public/index.php?action=siswa_save_buku_induk" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" required class="w-full border rounded-lg px-4 py-2" value="<?php echo htmlspecialchars($record->nama ?? ($_SESSION['user_nama'] ?? ''), ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NIS</label>
                <input type="text" name="nis" required class="w-full border rounded-lg px-4 py-2" value="<?php echo htmlspecialchars($record->nis ?? '', ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NISN</label>
                <input type="text" name="nisn" required class="w-full border rounded-lg px-4 py-2" value="<?php echo htmlspecialchars($record->nisn ?? '', ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" required class="w-full border rounded-lg px-4 py-2" value="<?php echo htmlspecialchars($record->tempat_lahir ?? '', ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" required class="w-full border rounded-lg px-4 py-2" value="<?php echo htmlspecialchars($record->tanggal_lahir ?? '', ENT_QUOTES); ?>" />
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="alamat" rows="3" class="w-full border rounded-lg px-4 py-2" required><?php echo htmlspecialchars($record->alamat ?? '', ENT_QUOTES); ?></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ayah</label>
                <input type="text" name="nama_ayah" class="w-full border rounded-lg px-4 py-2" value="<?php echo htmlspecialchars($record->nama_ayah ?? '', ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu</label>
                <input type="text" name="nama_ibu" class="w-full border rounded-lg px-4 py-2" value="<?php echo htmlspecialchars($record->nama_ibu ?? '', ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon Orang Tua</label>
                <input type="text" name="no_telp_ortu" class="w-full border rounded-lg px-4 py-2" placeholder="08xxxxxxxxxx" value="<?php echo htmlspecialchars($record->no_telp_ortu ?? '', ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Orang Tua</label>
                <input type="email" name="email_ortu" class="w-full border rounded-lg px-4 py-2" placeholder="email@example.com" value="<?php echo htmlspecialchars($record->email_ortu ?? '', ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dokumen KTP (*PDF)</label>
                <input type="file" name="dokumen_pdf" accept="application/pdf" class="w-full" />
                <input type="hidden" name="existing_pdf" value="<?php echo htmlspecialchars($record->dokumen_pdf ?? '', ENT_QUOTES); ?>" />
                <?php if(!empty($record->dokumen_pdf)): ?>
                    <p class="text-sm mt-1">Dokumen saat ini: <a class="text-blue-600 hover:underline" target="_blank" href="<?php echo $record->dokumen_pdf; ?>">Lihat</a></p>
                <?php endif; ?>
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
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
    
    <?php if(!empty($dokumen)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Dokumen yang Telah Diunggah</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach($dokumen as $dok): ?>
                <div class="border rounded-lg p-4 flex justify-between items-center">
                    <div class="flex-1">
                        <p class="font-medium text-gray-800"><?php echo htmlspecialchars($dok->nama_file); ?></p>
                        <?php if(!empty($dok->keterangan)): ?>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($dok->keterangan); ?></p>
                        <?php endif; ?>
                        <p class="text-xs text-gray-500 mt-1"><?php echo date('d M Y H:i', strtotime($dok->created_at)); ?></p>
                    </div>
                    <div class="flex gap-2">
                        <a href="<?php echo $dok->path_file; ?>" target="_blank" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form method="POST" action="<?php echo BASE_URL; ?>/public/index.php?action=siswa_delete_dokumen" onsubmit="return confirm('Hapus dokumen ini?')" class="inline">
                            <input type="hidden" name="dokumen_id" value="<?php echo $dok->id; ?>" />
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
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
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
