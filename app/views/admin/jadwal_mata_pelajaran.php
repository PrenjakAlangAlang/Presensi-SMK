<?php
$page_title = "Jadwal Mata Pelajaran";
require_once __DIR__ . '/../layouts/header.php';

$hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
$selectedKelasArchived = $selectedKelas && (($selectedKelas->status ?? 'active') === 'archived');
?>

<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
    <div>
        <div class="flex flex-wrap items-center gap-2">
            <h2 class="text-2xl font-bold text-gray-800"><?php echo $selectedKelas ? 'Jadwal ' . htmlspecialchars($selectedKelas->nama_kelas) : 'Daftar Kelas'; ?></h2>
            <?php if($selectedKelas): ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?php echo $selectedKelasArchived ? 'bg-gray-100 text-gray-700' : 'bg-green-100 text-green-700'; ?>">
                    <i class="fas <?php echo $selectedKelasArchived ? 'fa-archive' : 'fa-check-circle'; ?> mr-1"></i>
                    <?php echo $selectedKelasArchived ? 'Arsip / Nonaktif' : 'Aktif'; ?>
                </span>
            <?php endif; ?>
        </div>
        <p class="text-gray-600"><?php echo $selectedKelas ? ($selectedKelasArchived ? 'Data arsip tetap dapat dilihat untuk riwayat dan laporan.' : 'Kelola mata pelajaran, guru pengampu, ruang, dan siswa peserta.') : 'Pilih kelas untuk mengelola jadwal mata pelajaran.'; ?></p>
    </div>
    <div class="flex flex-col sm:flex-row gap-2">
        <?php if($selectedKelas): ?>
            <a href="index.php?action=admin_jadwal_mata_pelajaran" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg flex items-center justify-center space-x-2 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            <?php if(!$selectedKelasArchived): ?>
                <button onclick="openAddJadwalModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center space-x-2 transition-colors">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Mapel</span>
                </button>
            <?php endif; ?>
        <?php else: ?>
            <button onclick="openAddMasterKelasModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg flex items-center justify-center space-x-2 transition-colors">
                <i class="fas fa-layer-group"></i>
                <span>Tambah Master Kelas</span>
            </button>
            <button onclick="openAddKelasModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center space-x-2 transition-colors">
                <i class="fas fa-plus"></i>
                <span>Aktifkan Kelas Semester</span>
            </button>
        <?php endif; ?>
    </div>
</div>

<?php if(isset($_SESSION['success'])): ?>
<div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
</div>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
<div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
</div>
<?php endif; ?>

<?php if(!$selectedKelas): ?>
<div class="mb-4">
    <h3 class="font-semibold text-gray-800">Kelas Semester</h3>
    <p class="text-sm text-gray-500">Periode kelas untuk jadwal, presensi, dan status arsip.</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    <?php foreach($kelasList as $kelas): ?>
    <?php $isArchived = (($kelas->status ?? 'active') === 'archived'); ?>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
        <a href="index.php?action=admin_jadwal_mata_pelajaran&kelas_id=<?php echo $kelas->id; ?>" class="block">
            <div class="flex items-start justify-between gap-3 mb-4">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($kelas->nama_kelas); ?></h3>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold <?php echo $isArchived ? 'bg-gray-100 text-gray-700' : 'bg-green-100 text-green-700'; ?>">
                            <i class="fas <?php echo $isArchived ? 'fa-archive' : 'fa-check-circle'; ?> mr-1"></i>
                            <?php echo $isArchived ? 'Arsip' : 'Aktif'; ?>
                        </span>
                    </div>
                    <p class="text-sm text-gray-500">
                        <?php echo htmlspecialchars($kelas->tahun_ajaran ?? '-'); ?>
                        <?php if(!empty($kelas->semester)): ?>
                            <span class="mx-1">-</span><?php echo htmlspecialchars($kelas->semester); ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="w-11 h-11 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-school text-blue-600"></i>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500">Mapel</p>
                    <p class="font-semibold text-gray-800"><?php echo (int)($kelas->jumlah_mapel ?? 0); ?></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500">Jadwal</p>
                    <p class="font-semibold text-gray-800"><?php echo (int)($kelas->jumlah_jadwal ?? 0); ?></p>
                </div>
            </div>
        </a>
        <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-gray-100">
            <button onclick="toggleKelasStatus(<?php echo $kelas->id; ?>, '<?php echo $isArchived ? 'active' : 'archived'; ?>')"
                    class="<?php echo $isArchived ? 'text-green-600 hover:text-green-700' : 'text-amber-600 hover:text-amber-700'; ?>"
                    title="<?php echo $isArchived ? 'Aktifkan Kelas' : 'Nonaktifkan / Arsipkan Kelas'; ?>">
                <i class="fas <?php echo $isArchived ? 'fa-check-circle' : 'fa-archive'; ?>"></i>
            </button>
            <button onclick="openEditKelasModal(this)"
                    data-id="<?php echo $kelas->id; ?>"
                    data-master-kelas-id="<?php echo htmlspecialchars($kelas->master_kelas_id ?? '', ENT_QUOTES); ?>"
                    data-nama-kelas="<?php echo htmlspecialchars($kelas->nama_kelas, ENT_QUOTES); ?>"
                    data-tahun-ajaran="<?php echo htmlspecialchars($kelas->tahun_ajaran ?? '', ENT_QUOTES); ?>"
                    data-semester="<?php echo htmlspecialchars($kelas->semester ?? '', ENT_QUOTES); ?>"
                    class="text-blue-600 hover:text-blue-700" title="Edit Kelas">
                <i class="fas fa-edit"></i>
            </button>
            <button onclick="deleteKelas(<?php echo $kelas->id; ?>)" class="text-red-600 hover:text-red-700" title="Hapus Kelas">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if(empty($kelasList)): ?>
    <div class="col-span-full bg-white rounded-lg shadow-sm border border-gray-100 p-10 text-center text-gray-500">
        <i class="fas fa-school text-4xl mb-3"></i>
        <p>Belum ada kelas. Klik "Tambah Kelas" untuk membuat kelas baru.</p>
    </div>
    <?php endif; ?>
</div>
<?php else: ?>
<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Kelas</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Mata Pelajaran</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Guru</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Jadwal</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Ruang</th>
                    <th class="px-5 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Siswa</th>
                    <th class="px-5 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach($mataPelajaran as $mp): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-4">
                        <div class="font-semibold text-gray-800"><?php echo htmlspecialchars($mp->nama_kelas); ?></div>
                        <div class="text-xs text-gray-500">
                            <?php echo htmlspecialchars($mp->tahun_ajaran ?? '-'); ?>
                            <?php if(!empty($mp->semester)): ?>
                                <span class="mx-1">-</span><?php echo htmlspecialchars($mp->semester); ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <div>
                            <div class="font-medium text-gray-800"><?php echo htmlspecialchars($mp->nama_mata_pelajaran); ?></div>
                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($mp->hari); ?>, <?php echo substr($mp->jam_mulai, 0, 5); ?> - <?php echo substr($mp->jam_selesai, 0, 5); ?></div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-700">
                        <?php echo $mp->guru_pengampu_nama ? htmlspecialchars($mp->guru_pengampu_nama) : '<span class="text-gray-400">Belum ditentukan</span>'; ?>
                    </td>
                    <td class="px-5 py-4 text-gray-700">
                        <div><?php echo htmlspecialchars($mp->hari); ?></div>
                        <div class="text-xs text-gray-500"><?php echo substr($mp->jam_mulai, 0, 5); ?> - <?php echo substr($mp->jam_selesai, 0, 5); ?></div>
                    </td>
                    <td class="px-5 py-4 text-gray-700"><?php echo htmlspecialchars($mp->ruang ?? '-'); ?></td>
                    <td class="px-5 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-users mr-1"></i><?php echo (int)($mp->jumlah_siswa ?? 0); ?>
                        </span>
                    </td>
                    <td class="px-5 py-4 text-right whitespace-nowrap">
                        <?php if($selectedKelasArchived): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                <i class="fas fa-lock mr-1"></i>Arsip
                            </span>
                        <?php else: ?>
                            <button onclick="kelolaSiswaJadwal(<?php echo $mp->id; ?>, '<?php echo htmlspecialchars(addslashes($mp->nama_kelas . ' - ' . $mp->nama_mata_pelajaran)); ?>')" class="text-purple-600 hover:text-purple-700 mr-3" title="Kelola Siswa">
                                <i class="fas fa-users"></i>
                            </button>
                            <button onclick="openEditJadwalModal(this)"
                                    data-id="<?php echo $mp->id; ?>"
                                    data-kelas-jadwal-id="<?php echo (int) $mp->kelas_jadwal_id; ?>"
                                    data-nama-kelas="<?php echo htmlspecialchars($mp->nama_kelas, ENT_QUOTES); ?>"
                                    data-nama-mapel="<?php echo htmlspecialchars($mp->nama_mata_pelajaran, ENT_QUOTES); ?>"
                                    data-guru="<?php echo htmlspecialchars($mp->guru_pengampu ?? '', ENT_QUOTES); ?>"
                                    data-hari="<?php echo htmlspecialchars($mp->hari, ENT_QUOTES); ?>"
                                    data-jam-mulai="<?php echo substr($mp->jam_mulai, 0, 5); ?>"
                                    data-jam-selesai="<?php echo substr($mp->jam_selesai, 0, 5); ?>"
                                    data-ruang="<?php echo htmlspecialchars($mp->ruang ?? '', ENT_QUOTES); ?>"
                                    class="text-blue-600 hover:text-blue-700 mr-3" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteJadwal(<?php echo $mp->id; ?>)" class="text-red-600 hover:text-red-700" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($mataPelajaran)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                        <i class="fas fa-calendar-alt text-4xl mb-3"></i>
                        <p>Belum ada jadwal mata pelajaran.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php
function renderJadwalFormFields($guru, $hariList, $prefix = '', $multiple = false, $slotContainerId = 'jadwalSlotContainer', $addFunction = 'addJadwalSlot', $renderInitialSlot = true) {
    $id = $prefix ? $prefix . '_' : '';
?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran *</label>
            <input type="text" name="nama_mata_pelajaran" id="<?php echo $id; ?>nama_mata_pelajaran" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Pend. Agama">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Guru Pengampu</label>
            <div class="space-y-2">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text"
                           id="<?php echo $id; ?>guru_pengampu_search"
                           class="guru-pengampu-search w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Cari nama guru..."
                           autocomplete="off"
                           data-target="<?php echo $id; ?>guru_pengampu">
                </div>
                <select name="guru_pengampu" id="<?php echo $id; ?>guru_pengampu" class="guru-pengampu-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Pilih Guru</option>
                <?php foreach($guru as $g): ?>
                    <option value="<?php echo $g->id; ?>"><?php echo htmlspecialchars($g->nama); ?></option>
                <?php endforeach; ?>
                </select>
            </div>
            <p class="guru-pengampu-empty hidden text-sm text-gray-500 mt-2" data-for="<?php echo $id; ?>guru_pengampu">Guru tidak ditemukan.</p>
        </div>
        <?php if (!$multiple): ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hari *</label>
                <select name="hari" id="<?php echo $id; ?>hari" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <?php foreach($hariList as $hari): ?>
                        <option value="<?php echo $hari; ?>"><?php echo $hari; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Waktu *</label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="time" name="jam_mulai" id="<?php echo $id; ?>jam_mulai" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <input type="time" name="jam_selesai" id="<?php echo $id; ?>jam_selesai" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ruang</label>
                <input type="text" name="ruang" id="<?php echo $id; ?>ruang" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Contoh: RB. 205">
            </div>
        <?php endif; ?>
    </div>
    <?php if ($multiple): ?>
        <div class="mt-6">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-gray-800">Pertemuan per Minggu</h4>
                <button type="button" onclick="<?php echo $addFunction; ?>()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm">
                    <i class="fas fa-plus mr-1"></i>Tambah Pertemuan
                </button>
            </div>
            <div id="<?php echo $slotContainerId; ?>" class="space-y-3">
                <?php if ($renderInitialSlot): ?>
                <div class="jadwal-slot grid grid-cols-1 md:grid-cols-12 gap-3 p-4 border border-gray-200 rounded-lg bg-gray-50">
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hari *</label>
                        <select name="hari[]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <?php foreach($hariList as $hari): ?>
                                <option value="<?php echo $hari; ?>"><?php echo $hari; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Waktu *</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="time" name="jam_mulai[]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <input type="time" name="jam_selesai[]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ruang</label>
                        <input type="text" name="ruang[]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Contoh: RB. 205">
                    </div>
                    <div class="md:col-span-1 flex items-end">
                        <button type="button" onclick="removeJadwalSlot(this, '<?php echo $slotContainerId; ?>')" class="remove-jadwal-slot w-full px-3 py-2 rounded-lg text-gray-400 cursor-not-allowed" disabled title="Minimal satu pertemuan">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
<?php } ?>

<div id="addJadwalModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Tambah Jadwal Mata Pelajaran</h3>
        </div>
        <form method="POST" action="index.php?action=admin_create_mata_pelajaran">
            <input type="hidden" name="kelas_id" value="<?php echo $selectedKelas ? $selectedKelas->id : ''; ?>">
            <div class="p-6">
                <?php renderJadwalFormFields($guru, $hariList, '', true); ?>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeAddJadwalModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="editJadwalModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Edit Jadwal Mata Pelajaran</h3>
        </div>
        <form method="POST" action="index.php?action=admin_update_mata_pelajaran">
            <input type="hidden" name="id" id="edit_id">
            <input type="hidden" name="kelas_id" value="<?php echo $selectedKelas ? $selectedKelas->id : ''; ?>">
            <div class="p-6">
                <?php renderJadwalFormFields($guru, $hariList, 'edit', true, 'editJadwalSlotContainer', 'addEditJadwalSlot', false); ?>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeEditJadwalModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Update</button>
            </div>
        </form>
    </div>
</div>

<div id="addKelasModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Aktifkan Kelas Semester</h3>
        </div>
        <form method="POST" action="index.php?action=admin_create_kelas">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Master Kelas *</label>
                    <select name="kelas_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Master Kelas</option>
                        <?php foreach(($kelasMasterList ?? []) as $master): ?>
                            <option value="<?php echo (int) $master->id; ?>"><?php echo htmlspecialchars($master->label ?? trim(($master->nama_kelas ?? '') . ' ' . ($master->jurusan ?? ''))); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="2025/2026">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
                    <select name="semester" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Semester</option>
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeAddKelasModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="editKelasModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Edit Kelas Semester</h3>
        </div>
        <form method="POST" action="index.php?action=admin_update_kelas">
            <input type="hidden" name="id" id="edit_kelas_id">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Master Kelas *</label>
                    <select name="kelas_id" id="edit_kelas_master_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Master Kelas</option>
                        <?php foreach(($kelasMasterList ?? []) as $master): ?>
                            <option value="<?php echo (int) $master->id; ?>"><?php echo htmlspecialchars($master->label ?? trim(($master->nama_kelas ?? '') . ' ' . ($master->jurusan ?? ''))); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" id="edit_kelas_tahun_ajaran" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
                    <select name="semester" id="edit_kelas_semester" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Semester</option>
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeEditKelasModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Update</button>
            </div>
        </form>
    </div>
</div>

<div id="addMasterKelasModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Tambah Master Kelas</h3>
        </div>
        <form method="POST" action="index.php?action=admin_create_kelas_master">
            <div class="p-6 space-y-4">
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button type="button" onclick="toggleExistingMasterKelas()" class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 text-left text-sm font-medium text-gray-700 hover:bg-gray-100">
                        <span>Master kelas yang sudah ada</span>
                        <i id="existingMasterKelasIcon" class="fas fa-chevron-down text-gray-400"></i>
                    </button>
                    <div id="existingMasterKelasList" class="hidden max-h-48 overflow-y-auto divide-y divide-gray-100">
                        <?php if(!empty($kelasMasterList)): ?>
                            <?php foreach($kelasMasterList as $master): ?>
                                <div class="px-4 py-2 text-sm flex items-center justify-between gap-3">
                                    <div>
                                        <div class="font-medium text-gray-800"><?php echo htmlspecialchars($master->nama_kelas ?? '-'); ?></div>
                                        <div class="text-xs text-gray-500">
                                            <?php echo !empty($master->jurusan) ? htmlspecialchars($master->jurusan) : 'Jurusan belum diisi'; ?>
                                            <span class="mx-1">&bull;</span><?php echo (int)($master->jumlah_periode ?? 0); ?> periode
                                            <span class="mx-1">&bull;</span><?php echo (int)($master->jumlah_siswa ?? 0); ?> siswa
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button type="button"
                                                onclick="openEditMasterKelasModal(this)"
                                                data-id="<?php echo (int) $master->id; ?>"
                                                data-nama-kelas="<?php echo htmlspecialchars($master->nama_kelas ?? '', ENT_QUOTES); ?>"
                                                data-jurusan="<?php echo htmlspecialchars($master->jurusan ?? '', ENT_QUOTES); ?>"
                                                class="text-blue-600 hover:text-blue-700" title="Edit Master Kelas">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" onclick="deleteMasterKelas(<?php echo (int) $master->id; ?>)" class="text-red-600 hover:text-red-700" title="Hapus Master Kelas">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="px-4 py-3 text-sm text-gray-500">Belum ada master kelas.</div>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kelas/Tingkat *</label>
                    <input type="text" name="nama_kelas" required maxlength="50" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Contoh: X">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jurusan</label>
                    <input type="text" name="jurusan" maxlength="100" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Contoh: MANAJEMEN PERKANTORAN 2">
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeAddMasterKelasModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="editMasterKelasModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Edit Master Kelas</h3>
        </div>
        <form method="POST" action="index.php?action=admin_update_kelas_master">
            <input type="hidden" name="id" id="edit_master_kelas_id">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kelas/Tingkat *</label>
                    <input type="text" name="nama_kelas" id="edit_master_kelas_nama" required maxlength="50" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jurusan</label>
                    <input type="text" name="jurusan" id="edit_master_kelas_jurusan" maxlength="100" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeEditMasterKelasModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Update</button>
            </div>
        </form>
    </div>
</div>

<div id="kelolaSiswaModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Kelola Siswa Jadwal</h3>
            <p class="text-gray-600 text-sm" id="modalJadwalTitle"></p>
        </div>
        <div class="p-6 overflow-y-auto max-h-96">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">Siswa Terdaftar</h4>
                    <div id="siswaDalamJadwal" class="space-y-2 max-h-64 overflow-y-auto"></div>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">Tambah Siswa</h4>
                    <div class="space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="md:col-span-3">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Cari Siswa</label>
                                <input type="search" id="filterSiswaKeyword" placeholder="Nama, NIPD, NISN, kelas, jurusan, agama" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Kelas</label>
                                <select id="filterSiswaKelas" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                    <option value="">Semua kelas</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Jurusan</label>
                                <select id="filterSiswaJurusan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                    <option value="">Semua jurusan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Agama</label>
                                <select id="filterSiswaAgama" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                    <option value="">Semua agama</option>
                                </select>
                            </div>
                        </div>
                        <p id="siswaFilterInfo" class="text-sm text-gray-500">Memuat daftar siswa...</p>
                        <select id="siswaJadwalSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="">Pilih Siswa</option>
                        </select>
                        <button onclick="tambahSiswaKeJadwal()" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-plus mr-2"></i>Tambahkan
                        </button>
                        <button onclick="tambahSemuaSiswaTerfilter()" id="tambahSemuaSiswaBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg disabled:bg-gray-300 disabled:cursor-not-allowed" disabled>
                            <i class="fas fa-users mr-2"></i>Tambahkan Semua Hasil Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-6 border-t border-gray-200 flex justify-end">
            <button onclick="closeKelolaSiswaModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">Tutup</button>
        </div>
    </div>
</div>

<script>
let currentJadwalId = null;
let siswaTersediaData = [];
let siswaTersediaFiltered = [];
const hariOptions = <?php echo json_encode($hariList); ?>;
const allJadwal = <?php echo json_encode($mataPelajaran); ?>;

function initGuruPengampuSearch() {
    document.querySelectorAll('.guru-pengampu-search').forEach(input => {
        const select = document.getElementById(input.dataset.target);
        if (!select) return;

        input.addEventListener('input', () => filterGuruPengampuOptions(input, select));
        select.addEventListener('change', () => syncGuruPengampuSearch(select.id));
        syncGuruPengampuSearch(select.id);
    });
}

function filterGuruPengampuOptions(input, select) {
    const query = input.value.trim().toLowerCase();
    let visibleCount = 0;
    let selectedStillVisible = !select.value;

    Array.from(select.options).forEach(option => {
        const isPlaceholder = option.value === '';
        const isMatch = !query || option.textContent.toLowerCase().includes(query);
        option.hidden = !isPlaceholder && !isMatch;

        if (!isPlaceholder && isMatch) visibleCount++;
        if (option.value === select.value && isMatch) selectedStillVisible = true;
    });

    if (!selectedStillVisible) select.value = '';
    if (select.options[0]) {
        select.options[0].textContent = query ? 'Pilih dari hasil pencarian' : 'Pilih Guru';
    }

    const emptyText = document.querySelector(`.guru-pengampu-empty[data-for="${select.id}"]`);
    if (emptyText) emptyText.classList.toggle('hidden', !query || visibleCount > 0);
}

function syncGuruPengampuSearch(selectId) {
    const select = document.getElementById(selectId);
    const input = document.querySelector(`.guru-pengampu-search[data-target="${selectId}"]`);
    if (!select || !input) return;

    const selected = select.options[select.selectedIndex];
    input.value = select.value && selected ? selected.textContent : '';
    filterGuruPengampuOptions(input, select);
}

function resetGuruPengampuSearch(selectId) {
    const select = document.getElementById(selectId);
    const input = document.querySelector(`.guru-pengampu-search[data-target="${selectId}"]`);
    if (!select || !input) return;

    input.value = '';
    filterGuruPengampuOptions(input, select);
}

function addJadwalSlot() {
    addJadwalSlotTo('jadwalSlotContainer');
}

function addEditJadwalSlot() {
    addJadwalSlotTo('editJadwalSlotContainer', { includeId: true });
}

function addJadwalSlotTo(containerId, options = {}) {
    const container = document.getElementById(containerId);
    const data = options.data || {};
    const includeId = options.includeId || false;
    const slot = document.createElement('div');
    slot.className = 'jadwal-slot grid grid-cols-1 md:grid-cols-12 gap-3 p-4 border border-gray-200 rounded-lg bg-gray-50';
    slot.innerHTML = `
        ${includeId ? `<input type="hidden" name="jadwal_id[]" value="${escapeHtml(data.id || '')}">` : ''}
        <div class="md:col-span-3">
            <label class="block text-sm font-medium text-gray-700 mb-2">Hari *</label>
            <select name="hari[]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                ${hariOptions.map(hari => `<option value="${hari}" ${hari === data.hari ? 'selected' : ''}>${hari}</option>`).join('')}
            </select>
        </div>
        <div class="md:col-span-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Waktu *</label>
            <div class="grid grid-cols-2 gap-2">
                <input type="time" name="jam_mulai[]" value="${escapeHtml(formatTime(data.jam_mulai || ''))}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <input type="time" name="jam_selesai[]" value="${escapeHtml(formatTime(data.jam_selesai || ''))}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <div class="md:col-span-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Ruang</label>
            <input type="text" name="ruang[]" value="${escapeHtml(data.ruang || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Contoh: RB. 205">
        </div>
        <div class="md:col-span-1 flex items-end">
            <button type="button" onclick="removeJadwalSlot(this, '${containerId}')" class="remove-jadwal-slot w-full px-3 py-2 rounded-lg text-red-600 hover:bg-red-50" title="Hapus pertemuan">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(slot);
    updateJadwalSlotButtons(containerId);
}

function removeJadwalSlot(button, containerId = 'jadwalSlotContainer') {
    const container = document.getElementById(containerId);
    if (container.querySelectorAll('.jadwal-slot').length <= 1) return;
    button.closest('.jadwal-slot').remove();
    updateJadwalSlotButtons(containerId);
}

function updateJadwalSlotButtons(containerId = 'jadwalSlotContainer') {
    const slots = document.querySelectorAll(`#${containerId} .jadwal-slot`);
    slots.forEach((slot, index) => {
        const button = slot.querySelector('.remove-jadwal-slot');
        if (!button) return;
        const disabled = slots.length === 1;
        button.disabled = disabled;
        button.className = disabled
            ? 'w-full px-3 py-2 rounded-lg text-gray-400 cursor-not-allowed'
            : 'w-full px-3 py-2 rounded-lg text-red-600 hover:bg-red-50';
        button.title = disabled ? 'Minimal satu pertemuan' : 'Hapus pertemuan';
    });
}

function formatTime(value) {
    return String(value || '').substring(0, 5);
}

function sameNullable(a, b) {
    return String(a || '') === String(b || '');
}

function getJadwalGroup(base) {
    return allJadwal.filter(item =>
        sameNullable(item.kelas_jadwal_id, base.kelas_jadwal_id) &&
        sameNullable(item.nama_mata_pelajaran, base.nama_mata_pelajaran) &&
        sameNullable(item.guru_pengampu, base.guru_pengampu)
    ).sort((a, b) => {
        const dayOrder = hariOptions.indexOf(a.hari) - hariOptions.indexOf(b.hari);
        if (dayOrder !== 0) return dayOrder;
        return formatTime(a.jam_mulai).localeCompare(formatTime(b.jam_mulai));
    });
}

function openAddJadwalModal() {
    document.getElementById('addJadwalModal').classList.remove('hidden');
    resetGuruPengampuSearch('guru_pengampu');
    updateJadwalSlotButtons();
}

function closeAddJadwalModal() {
    const modal = document.getElementById('addJadwalModal');
    modal.classList.add('hidden');
    const form = modal.querySelector('form');
    if (form) form.reset();
    resetGuruPengampuSearch('guru_pengampu');
    const slots = document.querySelectorAll('#jadwalSlotContainer .jadwal-slot');
    slots.forEach((slot, index) => {
        if (index > 0) slot.remove();
    });
    updateJadwalSlotButtons();
}

function openEditJadwalModal(button) {
    const base = {
        kelas_jadwal_id: button.dataset.kelasJadwalId || '',
        nama_kelas: button.dataset.namaKelas || '',
        nama_mata_pelajaran: button.dataset.namaMapel || '',
        guru_pengampu: button.dataset.guru || ''
    };

    document.getElementById('edit_id').value = button.dataset.id;
    document.getElementById('edit_nama_mata_pelajaran').value = base.nama_mata_pelajaran;
    document.getElementById('edit_guru_pengampu').value = base.guru_pengampu;
    syncGuruPengampuSearch('edit_guru_pengampu');

    const container = document.getElementById('editJadwalSlotContainer');
    container.innerHTML = '';
    getJadwalGroup(base).forEach(item => {
        addJadwalSlotTo('editJadwalSlotContainer', { includeId: true, data: item });
    });
    if (!container.querySelector('.jadwal-slot')) {
        addJadwalSlotTo('editJadwalSlotContainer', { includeId: true, data: {
            id: button.dataset.id,
            hari: button.dataset.hari,
            jam_mulai: button.dataset.jamMulai,
            jam_selesai: button.dataset.jamSelesai,
            ruang: button.dataset.ruang
        } });
    }
    updateJadwalSlotButtons('editJadwalSlotContainer');

    document.getElementById('editJadwalModal').classList.remove('hidden');
}

function closeEditJadwalModal() {
    document.getElementById('editJadwalModal').classList.add('hidden');
    resetGuruPengampuSearch('edit_guru_pengampu');
}

function openAddKelasModal() {
    document.getElementById('addKelasModal').classList.remove('hidden');
}

function closeAddKelasModal() {
    const modal = document.getElementById('addKelasModal');
    modal.classList.add('hidden');
    const form = modal.querySelector('form');
    if (form) form.reset();
}

function openEditKelasModal(button) {
    document.getElementById('edit_kelas_id').value = button.dataset.id || '';
    document.getElementById('edit_kelas_master_id').value = button.dataset.masterKelasId || '';
    document.getElementById('edit_kelas_tahun_ajaran').value = button.dataset.tahunAjaran || '';
    document.getElementById('edit_kelas_semester').value = button.dataset.semester || '';
    document.getElementById('editKelasModal').classList.remove('hidden');
}

function closeEditKelasModal() {
    document.getElementById('editKelasModal').classList.add('hidden');
}

function openAddMasterKelasModal() {
    document.getElementById('addMasterKelasModal').classList.remove('hidden');
}

function closeAddMasterKelasModal() {
    const modal = document.getElementById('addMasterKelasModal');
    modal.classList.add('hidden');
    const form = modal.querySelector('form');
    if (form) form.reset();
}

function toggleExistingMasterKelas() {
    const list = document.getElementById('existingMasterKelasList');
    const icon = document.getElementById('existingMasterKelasIcon');
    const isHidden = list.classList.toggle('hidden');
    icon.className = isHidden ? 'fas fa-chevron-down text-gray-400' : 'fas fa-chevron-up text-gray-500';
}

function openEditMasterKelasModal(button) {
    document.getElementById('edit_master_kelas_id').value = button.dataset.id || '';
    document.getElementById('edit_master_kelas_nama').value = button.dataset.namaKelas || '';
    document.getElementById('edit_master_kelas_jurusan').value = button.dataset.jurusan || '';
    document.getElementById('editMasterKelasModal').classList.remove('hidden');
}

function closeEditMasterKelasModal() {
    document.getElementById('editMasterKelasModal').classList.add('hidden');
}

function deleteMasterKelas(id) {
    if (!confirm('Hapus master kelas ini? Hanya bisa dihapus jika belum dipakai.')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'index.php?action=admin_delete_kelas_master';
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'id';
    input.value = id;
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}

function deleteKelas(id) {
    if (!confirm('Hapus kelas ini? Semua jadwal di kelas ini juga akan dihapus.')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'index.php?action=admin_delete_kelas';
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'id';
    input.value = id;
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}

function toggleKelasStatus(id, status) {
    const message = status === 'archived'
        ? 'Nonaktifkan kelas ini? Jadwal tidak bisa diubah, tetapi riwayat dan laporan tetap dapat dilihat.'
        : 'Aktifkan kembali kelas ini untuk semester berjalan?';
    if (!confirm(message)) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'index.php?action=admin_toggle_kelas_status';

    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'id';
    idInput.value = id;
    form.appendChild(idInput);

    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = status;
    form.appendChild(statusInput);

    document.body.appendChild(form);
    form.submit();
}

function deleteJadwal(id) {
    if (!confirm('Hapus jadwal mata pelajaran ini?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'index.php?action=admin_delete_mata_pelajaran';
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'id';
    input.value = id;
    form.appendChild(input);
    const kelasInput = document.createElement('input');
    kelasInput.type = 'hidden';
    kelasInput.name = 'kelas_id';
    kelasInput.value = '<?php echo $selectedKelas ? (int) $selectedKelas->id : ''; ?>';
    form.appendChild(kelasInput);
    document.body.appendChild(form);
    form.submit();
}

function kelolaSiswaJadwal(jadwalId, title) {
    currentJadwalId = jadwalId;
    document.getElementById('modalJadwalTitle').textContent = title;
    resetSiswaFilters();
    loadSiswaDalamJadwal();
    loadSiswaTersediaJadwal();
    document.getElementById('kelolaSiswaModal').classList.remove('hidden');
}

function closeKelolaSiswaModal() {
    document.getElementById('kelolaSiswaModal').classList.add('hidden');
}

function loadSiswaDalamJadwal() {
    const container = document.getElementById('siswaDalamJadwal');
    container.innerHTML = '<p class="text-gray-500 text-center py-4">Loading...</p>';

    fetch(`index.php?action=admin_get_siswa_mapel&mapel_id=${currentJadwalId}`)
        .then(res => res.json())
        .then(data => {
            if (!data.length) {
                container.innerHTML = '<p class="text-gray-500 text-center py-4">Belum ada siswa</p>';
                return;
            }
            container.innerHTML = data.map(siswa => `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-purple-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">${escapeHtml(siswa.nama)}</p>
                            <p class="text-xs text-gray-500">${escapeHtml([siswa.nipd ? `NIPD ${siswa.nipd}` : '', siswa.kelas || '', siswa.jurusan || '', siswa.agama || ''].filter(Boolean).join(' - ') || 'Data kelas belum diisi')}</p>
                        </div>
                    </div>
                    <button onclick="hapusSiswaDariJadwal(${siswa.id})" class="text-red-600 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `).join('');
        })
        .catch(() => container.innerHTML = '<p class="text-red-500 text-center py-4">Gagal memuat data</p>');
}

function loadSiswaTersediaJadwal() {
    const select = document.getElementById('siswaJadwalSelect');
    const info = document.getElementById('siswaFilterInfo');
    select.innerHTML = '<option value="">Loading...</option>';
    info.textContent = 'Memuat daftar siswa...';
    document.getElementById('tambahSemuaSiswaBtn').disabled = true;

    fetch(`index.php?action=admin_get_siswa_tersedia_mapel&mapel_id=${currentJadwalId}`)
        .then(res => res.json())
        .then(data => {
            siswaTersediaData = Array.isArray(data) ? data : [];
            populateSiswaFilterOptions();
            renderSiswaTersediaOptions();
        })
        .catch(() => showNotification('error', 'Gagal memuat daftar siswa'));
}

function resetSiswaFilters() {
    const keyword = document.getElementById('filterSiswaKeyword');
    const kelas = document.getElementById('filterSiswaKelas');
    const jurusan = document.getElementById('filterSiswaJurusan');
    const agama = document.getElementById('filterSiswaAgama');
    if (keyword) keyword.value = '';
    if (kelas) kelas.innerHTML = '<option value="">Semua kelas</option>';
    if (jurusan) jurusan.innerHTML = '<option value="">Semua jurusan</option>';
    if (agama) agama.innerHTML = '<option value="">Semua agama</option>';
    siswaTersediaData = [];
    siswaTersediaFiltered = [];
}

function populateSiswaFilterOptions() {
    const kelasSelect = document.getElementById('filterSiswaKelas');
    const jurusanSelect = document.getElementById('filterSiswaJurusan');
    const agamaSelect = document.getElementById('filterSiswaAgama');
    const selectedKelas = kelasSelect.value;
    const selectedJurusan = jurusanSelect.value;
    const selectedAgama = agamaSelect.value;

    const kelasList = [...new Set(siswaTersediaData.map(s => s.kelas).filter(Boolean))].sort();
    const jurusanList = [...new Set(siswaTersediaData.map(s => s.jurusan).filter(Boolean))].sort();
    const agamaList = [...new Set(siswaTersediaData.map(s => s.agama).filter(Boolean))].sort();

    kelasSelect.innerHTML = '<option value="">Semua kelas</option>' + kelasList.map(kelas =>
        `<option value="${escapeHtml(kelas)}">${escapeHtml(kelas)}</option>`
    ).join('');
    jurusanSelect.innerHTML = '<option value="">Semua jurusan</option>' + jurusanList.map(jurusan =>
        `<option value="${escapeHtml(jurusan)}">${escapeHtml(jurusan)}</option>`
    ).join('');
    agamaSelect.innerHTML = '<option value="">Semua agama</option>' + agamaList.map(agama =>
        `<option value="${escapeHtml(agama)}">${escapeHtml(agama)}</option>`
    ).join('');

    kelasSelect.value = kelasList.includes(selectedKelas) ? selectedKelas : '';
    jurusanSelect.value = jurusanList.includes(selectedJurusan) ? selectedJurusan : '';
    agamaSelect.value = agamaList.includes(selectedAgama) ? selectedAgama : '';
}

function renderSiswaTersediaOptions() {
    const select = document.getElementById('siswaJadwalSelect');
    const keyword = document.getElementById('filterSiswaKeyword').value.trim().toLowerCase();
    const kelas = document.getElementById('filterSiswaKelas').value;
    const jurusan = document.getElementById('filterSiswaJurusan').value;
    const agama = document.getElementById('filterSiswaAgama').value;

    siswaTersediaFiltered = siswaTersediaData.filter(siswa => {
        const matchesKelas = !kelas || siswa.kelas === kelas;
        const matchesJurusan = !jurusan || siswa.jurusan === jurusan;
        const matchesAgama = !agama || siswa.agama === agama;
        const searchText = [
            siswa.nama || '',
            siswa.nipd || '',
            siswa.nisn || '',
            siswa.kelas || '',
            siswa.jurusan || '',
            siswa.agama || ''
        ].join(' ').toLowerCase();
        return matchesKelas && matchesJurusan && matchesAgama && (!keyword || searchText.includes(keyword));
    });

    select.innerHTML = '<option value="">Pilih Siswa</option>';
    if (!siswaTersediaFiltered.length) {
        select.innerHTML = '<option value="">Tidak ada siswa sesuai filter</option>';
    } else {
        siswaTersediaFiltered.forEach(siswa => {
            const kelasText = siswa.kelas ? ` - ${siswa.kelas}` : '';
            const jurusanText = siswa.jurusan ? ` (${siswa.jurusan})` : '';
            const agamaText = siswa.agama ? ` - ${siswa.agama}` : '';
            const nipdText = siswa.nipd ? `NIPD ${siswa.nipd} - ` : '';
            select.innerHTML += `<option value="${siswa.id}">${nipdText}${escapeHtml(siswa.nama || 'Nama tidak tersedia')}${escapeHtml(kelasText + jurusanText + agamaText)}</option>`;
        });
    }

    const total = siswaTersediaData.length;
    const shown = siswaTersediaFiltered.length;
    document.getElementById('siswaFilterInfo').textContent = `${shown} dari ${total} siswa tersedia ditampilkan`;
    document.getElementById('tambahSemuaSiswaBtn').disabled = shown === 0;
}

function tambahSiswaKeJadwal() {
    const select = document.getElementById('siswaJadwalSelect');
    if (!select.value) {
        showNotification('warning', 'Pilih siswa terlebih dahulu.');
        return;
    }

    fetch('index.php?action=admin_add_siswa_mapel', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `siswa_id=${encodeURIComponent(select.value)}&mapel_id=${encodeURIComponent(currentJadwalId)}`
    })
    .then(res => res.json())
    .then(resp => {
        if (!resp.success) throw new Error();
        showNotification('success', 'Siswa berhasil ditambahkan.');
        loadSiswaDalamJadwal();
        loadSiswaTersediaJadwal();
        setTimeout(() => location.reload(), 800);
    })
    .catch(() => showNotification('error', 'Gagal menambahkan siswa.'));
}

function tambahSemuaSiswaTerfilter() {
    if (!siswaTersediaFiltered.length) {
        showNotification('warning', 'Tidak ada siswa pada hasil filter.');
        return;
    }

    const keyword = document.getElementById('filterSiswaKeyword').value.trim();
    const kelas = document.getElementById('filterSiswaKelas').value;
    const jurusan = document.getElementById('filterSiswaJurusan').value;
    const agama = document.getElementById('filterSiswaAgama').value;
    const labelParts = [kelas, jurusan, agama, keyword ? `cari "${keyword}"` : ''].filter(Boolean);
    const label = labelParts.length ? labelParts.join(', ') : 'semua siswa tersedia';

    if (!confirm(`Tambahkan ${siswaTersediaFiltered.length} siswa dari filter ${label}?`)) return;

    const params = new URLSearchParams();
    params.append('mapel_id', currentJadwalId);
    siswaTersediaFiltered.forEach(siswa => params.append('siswa_ids[]', siswa.id));

    fetch('index.php?action=admin_add_multiple_siswa_mapel', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params.toString()
    })
    .then(res => res.json())
    .then(resp => {
        if (!resp.success) throw new Error();
        showNotification('success', `${resp.count || siswaTersediaFiltered.length} siswa berhasil ditambahkan.`);
        loadSiswaDalamJadwal();
        loadSiswaTersediaJadwal();
        setTimeout(() => location.reload(), 900);
    })
    .catch(() => showNotification('error', 'Gagal menambahkan siswa dari hasil filter.'));
}

function hapusSiswaDariJadwal(siswaId) {
    if (!confirm('Hapus siswa dari jadwal ini?')) return;
    fetch('index.php?action=admin_remove_siswa_mapel', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `siswa_id=${encodeURIComponent(siswaId)}&mapel_id=${encodeURIComponent(currentJadwalId)}`
    })
    .then(res => res.json())
    .then(resp => {
        if (!resp.success) throw new Error();
        showNotification('success', 'Siswa berhasil dihapus.');
        loadSiswaDalamJadwal();
        loadSiswaTersediaJadwal();
        setTimeout(() => location.reload(), 800);
    })
    .catch(() => showNotification('error', 'Gagal menghapus siswa.'));
}

function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 text-white ${
        type === 'success' ? 'bg-green-500' : type === 'warning' ? 'bg-yellow-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 2500);
}

function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[char]));
}

['filterSiswaKeyword', 'filterSiswaKelas', 'filterSiswaJurusan', 'filterSiswaAgama'].forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('input', renderSiswaTersediaOptions);
    el.addEventListener('change', renderSiswaTersediaOptions);
});

['addJadwalModal', 'editJadwalModal', 'kelolaSiswaModal', 'addKelasModal', 'editKelasModal', 'addMasterKelasModal', 'editMasterKelasModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target !== this) return;
        if (id === 'addJadwalModal') closeAddJadwalModal();
        else if (id === 'editJadwalModal') closeEditJadwalModal();
        else if (id === 'addKelasModal') closeAddKelasModal();
        else if (id === 'editKelasModal') closeEditKelasModal();
        else if (id === 'addMasterKelasModal') closeAddMasterKelasModal();
        else if (id === 'editMasterKelasModal') closeEditMasterKelasModal();
        else this.classList.add('hidden');
    });
});

initGuruPengampuSearch();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
