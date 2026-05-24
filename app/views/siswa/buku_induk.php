<?php
$page_title = "Buku Induk Saya";
require_once __DIR__ . '/../layouts/header.php';

$dokumenFields = [
    'dokumen_ijasah' => 'Dokumen Ijasah',
    'dokumen_pas_foto' => 'Pas Foto',
    'dokumen_akta_kelahiran' => 'Akta Kelahiran',
    'dokumen_kk' => 'KK',
];
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Biodata Buku Induk</h2>
        <p class="text-gray-600">Perbarui data diri dan unggah dokumen pendukung.</p>
    </div>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    <?php if(isset($_SESSION['error'])): ?>
        <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?action=siswa_save_buku_induk" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" required maxlength="50" class="w-full border rounded-lg px-4 py-2" value="<?php echo htmlspecialchars($record->nama ?? ($_SESSION['user_nama'] ?? ''), ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NIPD</label>
                <input type="text" name="nipd" required class="w-full border rounded-lg px-4 py-2" value="<?php echo htmlspecialchars($record->nipd ?? '', ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" disabled maxlength="50" class="w-full border rounded-lg px-4 py-2 bg-gray-50 text-gray-600" value="<?php echo htmlspecialchars($record->email ?? '', ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NISN</label>
                <input type="text" name="nisn" class="w-full border rounded-lg px-4 py-2" value="<?php echo htmlspecialchars($record->nisn ?? '', ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                <input type="text" disabled class="w-full border rounded-lg px-4 py-2 bg-gray-50 text-gray-600" value="<?php echo htmlspecialchars($record->kelas ?? '', ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                <input type="text" disabled class="w-full border rounded-lg px-4 py-2 bg-gray-50 text-gray-600" value="<?php echo htmlspecialchars($record->jurusan ?? '', ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Diterima di Sekolah</label>
                <input type="date" disabled class="w-full border rounded-lg px-4 py-2 bg-gray-50 text-gray-600" value="<?php echo htmlspecialchars($record->tanggal_diterima ?? '', ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Agama</label>
                <input type="text" disabled class="w-full border rounded-lg px-4 py-2 bg-gray-50 text-gray-600" value="<?php echo htmlspecialchars($record->agama ?? '', ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" class="w-full border rounded-lg px-4 py-2" value="<?php echo htmlspecialchars($record->tempat_lahir ?? '', ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="w-full border rounded-lg px-4 py-2" value="<?php echo htmlspecialchars($record->tanggal_lahir ?? '', ENT_QUOTES); ?>" />
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="alamat" rows="3" class="w-full border rounded-lg px-4 py-2"><?php echo htmlspecialchars($record->alamat ?? '', ENT_QUOTES); ?></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ayah</label>
                <input type="text" name="nama_ayah" maxlength="50" class="w-full border rounded-lg px-4 py-2" value="<?php echo htmlspecialchars($record->nama_ayah ?? '', ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu</label>
                <input type="text" name="nama_ibu" maxlength="50" class="w-full border rounded-lg px-4 py-2" value="<?php echo htmlspecialchars($record->nama_ibu ?? '', ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Wali</label>
                <input type="text" name="nama_wali" maxlength="50" class="w-full border rounded-lg px-4 py-2" value="<?php echo htmlspecialchars($record->nama_wali ?? '', ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon Orang Tua</label>
                <input type="text" name="no_telp_ortu" class="w-full border rounded-lg px-4 py-2" placeholder="08xxxxxxxxxx" value="<?php echo htmlspecialchars($record->no_telp_ortu ?? '', ENT_QUOTES); ?>" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Orang Tua</label>
                <input type="email" name="email_ortu" maxlength="50" class="w-full border rounded-lg px-4 py-2" placeholder="email@example.com" value="<?php echo htmlspecialchars($record->email_ortu ?? '', ENT_QUOTES); ?>" />
            </div>
            <div></div>

            <?php foreach ($dokumenFields as $field => $label): ?>
                <?php $existingKey = str_replace('dokumen_', '', $field); ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo $label; ?> <?php echo $field === 'dokumen_pas_foto' ? '(PDF/JPG/PNG)' : '(*PDF)'; ?></label>
                    <input type="file" name="<?php echo $field; ?>" accept="<?php echo $field === 'dokumen_pas_foto' ? 'application/pdf,image/*' : 'application/pdf'; ?>" class="w-full" />
                    <input type="hidden" name="existing_<?php echo $existingKey; ?>" value="<?php echo htmlspecialchars($record->{$field} ?? '', ENT_QUOTES); ?>" />
                    <?php if(!empty($record->{$field})): ?>
                        <p class="text-sm mt-1"><?php echo $label; ?> saat ini: <a class="text-blue-600 hover:underline" target="_blank" href="<?php echo $record->{$field}; ?>">Lihat</a></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <div class="md:col-span-2 flex justify-end space-x-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Simpan</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Ubah Password</h3>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?action=siswa_change_password" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" name="password" required minlength="6" class="w-full border rounded-lg px-4 py-2" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirm" required minlength="6" class="w-full border rounded-lg px-4 py-2" />
            </div>
            <div class="md:col-span-2 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Ubah Password</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
