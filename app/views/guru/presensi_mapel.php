<?php
$page_title = "Presensi Mata Pelajaran";
require_once __DIR__ . '/../layouts/header.php';

$hariSekarang = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'][(int) date('N') - 1];
?>

<div class="max-w-7xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Kelola Presensi Mata Pelajaran</h2>
        <p class="text-gray-600">Pilih mata pelajaran, lalu kelola sesi presensi dan status kehadiran siswa.</p>
    </div>

    <?php if (empty($mapelSaya)): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-10 text-center">
            <i class="fas fa-calendar-day text-gray-300 text-5xl mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-700">Tidak ada mata pelajaran</h3>
            <p class="text-gray-500">Mata pelajaran yang Anda ampu akan tampil di halaman ini.</p>
        </div>
    <?php else: ?>
        <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between gap-4 mb-4">
                <div>
                    <h3 class="font-semibold text-gray-800">Mata Pelajaran yang Diampu</h3>
                    <p class="text-sm text-gray-500">Klik salah satu mapel untuk melihat semua sesi presensinya.</p>
                </div>
                <span class="text-sm text-gray-500"><?php echo count($mapelSaya); ?> mapel</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <?php foreach ($mapelSaya as $mapel): ?>
                    <?php
                    $isSelected = $selectedJadwal && (int) $selectedJadwal->id === (int) $mapel->id;
                    $jadwalRingkas = $mapel->jadwal_ringkas ?? (($mapel->hari ?? '-') . ', ' . date('H:i', strtotime($mapel->jam_mulai)) . ' - ' . date('H:i', strtotime($mapel->jam_selesai)));
                    ?>
                    <a href="<?php echo BASE_URL; ?>/index.php?action=guru_presensi_mapel&kelas_id=<?php echo (int) $mapel->id; ?>"
                       class="block rounded-lg border p-4 transition hover:border-blue-300 hover:bg-blue-50 <?php echo $isSelected ? 'border-blue-500 bg-blue-50' : 'border-gray-100 bg-white'; ?>">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-800 truncate"><?php echo htmlspecialchars($mapel->nama_mata_pelajaran); ?></p>
                                <p class="text-sm text-gray-600 truncate"><?php echo htmlspecialchars($mapel->nama_kelas ?? '-'); ?></p>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($jadwalRingkas); ?></p>
                            </div>
                            <span class="shrink-0 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                <?php echo (int) ($mapel->jumlah_pertemuan ?? 1); ?>x/minggu
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($selectedJadwal): ?>
        <?php
        $selectedJadwalRingkas = $selectedJadwal->jadwal_ringkas ?? (($selectedJadwal->hari ?? '-') . ', ' . date('H:i', strtotime($selectedJadwal->jam_mulai)) . ' - ' . date('H:i', strtotime($selectedJadwal->jam_selesai)));
        ?>
        <section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h3 class="font-semibold text-gray-800"><?php echo htmlspecialchars($selectedJadwal->nama_mata_pelajaran); ?></h3>
                    <p class="text-sm text-gray-500">
                        <?php echo htmlspecialchars($selectedJadwal->nama_kelas ?? '-'); ?> -
                        <?php echo htmlspecialchars($selectedJadwalRingkas); ?>
                    </p>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="openCreateSessionModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                        Buat Sesi
                    </button>
                </div>
            </div>

            <div class="p-5">
                <h4 class="font-semibold text-gray-800 mb-3">Sesi Presensi</h4>
                <?php if (empty($sesiMapel)): ?>
                    <div class="border border-dashed border-gray-200 rounded-lg p-8 text-center text-gray-500">
                        Belum ada sesi presensi untuk mata pelajaran ini.
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="text-left px-4 py-3">Tanggal</th>
                                    <th class="text-left px-4 py-3">Jam Sesi</th>
                                    <th class="text-left px-4 py-3">Status</th>
                                    <th class="text-center px-4 py-3">Hadir</th>
                                    <th class="text-center px-4 py-3">Izin</th>
                                    <th class="text-center px-4 py-3">Sakit</th>
                                    <th class="text-center px-4 py-3">Alpha</th>
                                    <th class="text-right px-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($sesiMapel as $sesi): ?>
                                    <?php
                                    $total = (int) ($sesi->total_siswa ?? 0);
                                    $hadir = (int) ($sesi->hadir ?? 0);
                                    $izin = (int) ($sesi->izin ?? 0);
                                    $sakit = (int) ($sesi->sakit ?? 0);
                                    $alpha = (int) ($sesi->alpha ?? 0);
                                    $belum = max(0, $total - $hadir - $izin - $sakit - $alpha);
                                    $isActiveRow = (int) $selectedSesiId === (int) $sesi->id;
                                    $sessionStarted = strtotime($sesi->waktu_buka) <= time();
                                    $sessionEnded = strtotime($sesi->waktu_tutup) <= time();
                                    $sessionLabel = $sesi->status === 'closed' ? 'Ditutup' : ($sessionStarted && !$sessionEnded ? 'Terbuka' : ($sessionEnded ? 'Lewat Jam' : 'Terjadwal'));
                                    ?>
                                    <tr class="<?php echo $isActiveRow ? 'bg-blue-50' : ''; ?>">
                                        <td class="px-4 py-3 font-medium text-gray-800"><?php echo date('d/m/Y', strtotime($sesi->waktu_buka)); ?></td>
                                        <td class="px-4 py-3 text-gray-600">
                                            <?php echo htmlspecialchars($sesi->hari ?? '-'); ?>,
                                            <?php echo date('H:i', strtotime($sesi->waktu_buka)); ?> - <?php echo date('H:i', strtotime($sesi->waktu_tutup)); ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium <?php echo $sesi->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'; ?>">
                                                <?php echo $sessionLabel; ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-green-700 font-semibold"><?php echo $hadir; ?></td>
                                        <td class="px-4 py-3 text-center text-blue-700 font-semibold"><?php echo $izin; ?></td>
                                        <td class="px-4 py-3 text-center text-yellow-700 font-semibold"><?php echo $sakit; ?></td>
                                        <td class="px-4 py-3 text-center text-red-700 font-semibold"><?php echo $alpha + $belum; ?></td>
                                        <td class="px-4 py-3">
                                            <div class="flex justify-end gap-2">
                                                <a href="<?php echo BASE_URL; ?>/index.php?action=guru_presensi_mapel&kelas_id=<?php echo (int) $selectedJadwal->id; ?>&sesi_id=<?php echo (int) $sesi->id; ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-xs">
                                                    Kelola
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/index.php?action=guru_laporan&kelas_id=<?php echo (int) $selectedJadwal->id; ?>&sesi_id=<?php echo (int) $sesi->id; ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-xs">
                                                    Laporan
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/index.php?action=guru_export_pdf&kelas_id=<?php echo (int) $selectedJadwal->id; ?>&sesi_id=<?php echo (int) $sesi->id; ?>" target="_blank" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs">
                                                    PDF
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/index.php?action=guru_export_excel&kelas_id=<?php echo (int) $selectedJadwal->id; ?>&sesi_id=<?php echo (int) $sesi->id; ?>" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-xs">
                                                    Excel
                                                </a>
                                                <?php if ($sesi->status === 'open' && $sessionStarted): ?>
                                                    <button type="button" onclick="closeSession(<?php echo (int) $sesi->id; ?>)" class="bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded text-xs">
                                                        Tutup
                                                    </button>
                                                <?php endif; ?>
                                                <button type="button" onclick="deleteSession(<?php echo (int) $sesi->id; ?>)" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($selectedJadwal && $selectedSesi): ?>
        <section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Update Kehadiran Siswa</h3>
                <p class="text-sm text-gray-500">
                    Sesi <?php echo date('d/m/Y H:i', strtotime($selectedSesi->waktu_buka)); ?> -
                    <?php echo date('H:i', strtotime($selectedSesi->waktu_tutup)); ?>
                </p>
            </div>
            <div class="p-5 border-b border-gray-100 bg-gray-50">
                <form id="laporanKemajuanForm" class="space-y-3">
                    <input type="hidden" name="sesi_id" value="<?php echo (int) $selectedSesi->id; ?>">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
                        <div>
                            <h4 class="font-semibold text-gray-800">Laporan Kemajuan Kelas</h4>
                            <p class="text-sm text-gray-500">Catatan ini tersimpan pada sesi presensi ini.</p>
                        </div>
                    </div>
                    <textarea name="laporan_kemajuan" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Materi hari ini, capaian pembelajaran, kendala kelas, atau tindak lanjut."><?php echo htmlspecialchars($selectedSesi->laporan_kemajuan ?? ''); ?></textarea>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                            Simpan Laporan Kemajuan
                        </button>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="text-left px-5 py-3">Nama Siswa</th>
                            <th class="text-left px-5 py-3">Status</th>
                            <th class="text-left px-5 py-3">Waktu</th>
                            <th class="text-left px-5 py-3">Keterangan</th>
                            <th class="text-left px-5 py-3">Ubah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($detailPresensi as $row): ?>
                            <tr>
                                <td class="px-5 py-3 font-medium text-gray-800"><?php echo htmlspecialchars($row->nama ?? '-'); ?></td>
                                <td class="px-5 py-3"><?php echo $row->jenis ? ucfirst($row->jenis) : 'Belum Presensi'; ?></td>
                                <td class="px-5 py-3 text-gray-600"><?php echo $row->waktu ? date('H:i', strtotime($row->waktu)) : '-'; ?></td>
                                <td class="px-5 py-3 text-gray-600"><?php echo htmlspecialchars($row->alasan ?? '-'); ?></td>
                                <td class="px-5 py-3">
                                    <form class="js-status-form flex flex-col md:flex-row gap-2">
                                        <input type="hidden" name="siswa_id" value="<?php echo (int) $row->siswa_id; ?>">
                                        <input type="hidden" name="kelas_id" value="<?php echo (int) $selectedJadwal->id; ?>">
                                        <input type="hidden" name="sesi_id" value="<?php echo (int) $selectedSesiId; ?>">
                                        <select name="jenis" class="border border-gray-300 rounded px-2 py-1">
                                            <?php foreach (['hadir', 'izin', 'sakit', 'alpha'] as $jenis): ?>
                                                <option value="<?php echo $jenis; ?>" <?php echo ($row->jenis ?? '') === $jenis ? 'selected' : ''; ?>><?php echo ucfirst($jenis); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input name="alasan" value="<?php echo htmlspecialchars($row->alasan ?? ''); ?>" class="border border-gray-300 rounded px-2 py-1" placeholder="Keterangan">
                                        <button class="bg-blue-600 text-white px-3 py-1 rounded" type="submit">Simpan</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endif; ?>
</div>

<?php if ($selectedJadwal): ?>
<div id="createSessionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Buat Sesi Presensi Mapel</h3>
            <p class="text-sm text-gray-500 mt-1">
                <?php echo htmlspecialchars($selectedJadwal->nama_mata_pelajaran); ?> memiliki jadwal:
                <?php echo htmlspecialchars($selectedJadwalRingkas); ?>.
            </p>
        </div>
        <form id="createSessionForm" class="p-6 space-y-4">
            <input type="hidden" name="kelas_id" value="<?php echo (int) $selectedJadwal->id; ?>">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Sesi</label>
                <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                <p class="text-xs text-gray-500 mt-1">Single session dibuat jika tanggal cocok dengan salah satu hari pertemuan mapel.</p>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="repeat_enabled" value="1" id="repeatEnabled" class="rounded border-gray-300">
                    <span>Buat multiple session untuk semua pertemuan mapel</span>
                </label>
                <div id="repeatPanel" class="hidden mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                        <input type="date" name="repeat_until" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ulangi Setiap</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="repeat_every_weeks" value="1" min="1" max="52" class="w-24 px-4 py-3 border border-gray-300 rounded-lg">
                            <span class="text-sm text-gray-600">minggu</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeCreateSessionModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Buat Sesi</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
async function postForm(action, data) {
    const res = await fetch('index.php?action=' + action, {
        method: 'POST',
        body: data instanceof FormData ? data : new URLSearchParams(data)
    });
    return res.json();
}

function openCreateSessionModal() {
    document.getElementById('createSessionModal')?.classList.remove('hidden');
}

function closeCreateSessionModal() {
    document.getElementById('createSessionModal')?.classList.add('hidden');
}

document.getElementById('repeatEnabled')?.addEventListener('change', function() {
    document.getElementById('repeatPanel')?.classList.toggle('hidden', !this.checked);
});

document.getElementById('createSessionModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeCreateSessionModal();
});

document.getElementById('createSessionForm')?.addEventListener('submit', async e => {
    e.preventDefault();
    const form = e.currentTarget;
    if (document.getElementById('repeatEnabled')?.checked && !form.querySelector('[name="repeat_until"]').value) {
        alert('Tanggal selesai wajib diisi untuk multiple session.');
        return;
    }
    const json = await postForm('buka_presensi_mapel', new FormData(form));
    alert(json.message || (json.success ? 'Berhasil' : 'Gagal'));
    if (json.success) location.reload();
});

async function closeSession(id) {
    if (!confirm('Tutup sesi presensi ini? Siswa yang belum presensi akan ditandai alpha.')) return;
    const json = await postForm('tutup_presensi_mapel', { sesi_id: id });
    alert(json.message || (json.success ? 'Berhasil' : 'Gagal'));
    if (json.success) location.reload();
}

async function deleteSession(id) {
    if (!confirm('Hapus sesi presensi ini? Data presensi siswa pada sesi ini juga akan terhapus.')) return;
    const json = await postForm('hapus_presensi_mapel_sesi', { sesi_id: id });
    alert(json.message || (json.success ? 'Berhasil' : 'Gagal'));
    if (json.success) {
        const url = new URL(window.location.href);
        url.searchParams.delete('sesi_id');
        window.location.href = url.toString();
    }
}

document.querySelectorAll('.js-status-form').forEach(form => {
    form.addEventListener('submit', async e => {
        e.preventDefault();
        const json = await postForm('guru_ubah_status_presensi', new FormData(form));
        alert(json.message || (json.success ? 'Berhasil' : 'Gagal'));
        if (json.success) location.reload();
    });
});

document.getElementById('laporanKemajuanForm')?.addEventListener('submit', async e => {
    e.preventDefault();
    const json = await postForm('simpan_laporan_kemajuan_mapel', new FormData(e.currentTarget));
    alert(json.message || (json.success ? 'Berhasil' : 'Gagal'));
    if (json.success) location.reload();
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
