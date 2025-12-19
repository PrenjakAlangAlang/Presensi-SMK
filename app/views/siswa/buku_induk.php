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
                <label class="block text-sm font-medium text-gray-700 mb-1">Dokumen PDF (opsional)</label>
                <input type="file" name="dokumen_pdf" accept="application/pdf" class="w-full" />
                <input type="hidden" name="existing_pdf" value="<?php echo htmlspecialchars($record->dokumen_pdf ?? '', ENT_QUOTES); ?>" />
                <?php if(!empty($record->dokumen_pdf)): ?>
                    <p class="text-sm mt-1">Dokumen saat ini: <a class="text-blue-600 hover:underline" target="_blank" href="<?php echo $record->dokumen_pdf; ?>">Lihat</a></p>
                <?php endif; ?>
            </div>
            <div class="md:col-span-2 flex justify-end space-x-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
