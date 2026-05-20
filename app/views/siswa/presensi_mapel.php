<?php
$page_title = "Presensi Mata Pelajaran";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="max-w-6xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Presensi Mata Pelajaran Hari Ini</h2>
        
    </div>

    <div id="gpsStatus" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center gap-3 text-gray-700">
            <i class="fas fa-location-dot text-blue-600"></i>
            <span>Mendeteksi lokasi GPS...</span>
        </div>
    </div>

    <?php if (empty($jadwalHariIni)): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-10 text-center">
            <i class="fas fa-calendar-day text-gray-300 text-5xl mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-700">Tidak ada jadwal hari ini</h3>
            <p class="text-gray-500">Mata pelajaran yang Anda ikuti akan tampil sesuai jadwal harian.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <?php foreach ($jadwalHariIni as $jadwal): ?>
                <?php
                $isOpen = !empty($jadwal->sesi_id) && ($jadwal->sesi_status ?? '') === 'open' && date('H:i:s') >= $jadwal->jam_mulai && date('H:i:s') <= $jadwal->jam_selesai;
                $sudahPresensi = !empty($jadwal->presensi_id);
                $jamMulai = date('H:i', strtotime($jadwal->jam_mulai));
                $jamSelesai = date('H:i', strtotime($jadwal->jam_selesai));
                ?>
                <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($jadwal->nama_mata_pelajaran); ?></h3>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($jadwal->nama_kelas ?? '-'); ?> &bull; <?php echo htmlspecialchars($jadwal->ruang ?: 'Ruang belum diisi'); ?></p>
                            <p class="text-sm text-gray-600"><?php echo $jamMulai; ?> - <?php echo $jamSelesai; ?> &bull; <?php echo htmlspecialchars($jadwal->guru_pengampu_nama ?? 'Guru belum ditentukan'); ?></p>
                        </div>
                        <?php if ($sudahPresensi): ?>
                            <span class="shrink-0 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Sudah <?php echo ucfirst($jadwal->presensi_jenis); ?></span>
                        <?php elseif ($isOpen): ?>
                            <span class="shrink-0 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Terbuka</span>
                        <?php else: ?>
                            <span class="shrink-0 px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Belum Aktif</span>
                        <?php endif; ?>
                    </div>

                    <?php if (!$sudahPresensi && $isOpen): ?>
                        <form class="mt-5 space-y-3 js-mapel-form" enctype="multipart/form-data">
                            <input type="hidden" name="kelas_id" value="<?php echo (int) $jadwal->id; ?>">
                            <input type="hidden" name="latitude">
                            <input type="hidden" name="longitude">
                            <input type="hidden" name="accuracy">
                            <input type="hidden" name="samples" value="[]">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Presensi</label>
                                <select name="jenis" class="js-jenis w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                    <option value="hadir">Hadir</option>
                                    <option value="izin">Izin</option>
                                    <option value="sakit">Sakit</option>
                                </select>
                            </div>
                            <div class="js-alasan hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan</label>
                                <textarea name="alasan" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"></textarea>
                                <input type="file" name="bukti" accept="image/*,.pdf" class="mt-2 w-full text-sm">
                            </div>
                            <button type="submit" class="js-submit w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-lg disabled:bg-gray-400 disabled:cursor-not-allowed" disabled>
                                <i class="fas fa-fingerprint mr-2"></i>Presensi Mata Pelajaran
                            </button>
                        </form>
                    <?php elseif ($sudahPresensi): ?>
                        <div class="mt-4 text-sm text-gray-600">
                            Dicatat pukul <?php echo date('H:i', strtotime($jadwal->waktu_presensi)); ?>.
                        </div>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">Riwayat Terbaru</h3>
        <div class="divide-y divide-gray-100">
            <?php foreach ($riwayatMapel as $row): ?>
                <div class="py-3 flex items-center justify-between gap-4">
                    <div>
                        <p class="font-medium text-gray-800"><?php echo htmlspecialchars($row->nama_mata_pelajaran); ?></p>
                        <p class="text-sm text-gray-500"><?php echo date('d/m/Y H:i', strtotime($row->waktu)); ?></p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700"><?php echo ucfirst($row->jenis); ?></span>
                </div>
            <?php endforeach; ?>
            <?php if (empty($riwayatMapel)): ?>
                <p class="text-sm text-gray-500">Belum ada presensi mata pelajaran.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
let currentLocation = null;
let locationSamples = [];
const radiusPresensi = <?php echo (int) ($lokasiSekolah->radius_presensi ?? MAX_RADIUS); ?>;

function setGpsMessage(message, ok) {
    document.getElementById('gpsStatus').innerHTML = `<div class="flex items-center gap-3 ${ok ? 'text-green-700' : 'text-gray-700'}"><i class="fas fa-location-dot ${ok ? 'text-green-600' : 'text-blue-600'}"></i><span>${message}</span></div>`;
}

function refreshButtons() {
    document.querySelectorAll('.js-mapel-form').forEach(form => {
        const jenis = form.querySelector('[name="jenis"]').value;
        const btn = form.querySelector('.js-submit');
        btn.disabled = jenis === 'hadir' && !currentLocation;
    });
}

if (navigator.geolocation) {
    const watchId = navigator.geolocation.watchPosition(pos => {
        currentLocation = {
            lat: pos.coords.latitude,
            lng: pos.coords.longitude,
            accuracy: pos.coords.accuracy
        };
        locationSamples.push(currentLocation);
        if (locationSamples.length >= 3) navigator.geolocation.clearWatch(watchId);
        setGpsMessage(`Lokasi siap. Akurasi sekitar ${currentLocation.accuracy.toFixed(1)} meter.`, true);
        refreshButtons();
    }, err => {
        setGpsMessage('GPS belum tersedia: ' + err.message, false);
    }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
} else {
    setGpsMessage('Browser tidak mendukung GPS.', false);
}

document.querySelectorAll('.js-jenis').forEach(select => {
    select.addEventListener('change', () => {
        const form = select.closest('form');
        const needsReason = select.value === 'izin' || select.value === 'sakit';
        form.querySelector('.js-alasan').classList.toggle('hidden', !needsReason);
        refreshButtons();
    });
});

document.querySelectorAll('.js-mapel-form').forEach(form => {
    form.addEventListener('submit', async e => {
        e.preventDefault();
        const jenis = form.querySelector('[name="jenis"]').value;
        if (jenis === 'hadir' && !currentLocation) return alert('Lokasi GPS belum siap.');

        if (currentLocation) {
            form.querySelector('[name="latitude"]').value = currentLocation.lat;
            form.querySelector('[name="longitude"]').value = currentLocation.lng;
            form.querySelector('[name="accuracy"]').value = currentLocation.accuracy;
            form.querySelector('[name="samples"]').value = JSON.stringify(locationSamples);
        }

        const btn = form.querySelector('.js-submit');
        btn.disabled = true;
        btn.textContent = 'Memproses...';
        const res = await fetch('index.php?action=submit_presensi_mapel', { method: 'POST', body: new FormData(form) });
        const json = await res.json();
        alert(json.message || (json.success ? 'Berhasil' : 'Gagal'));
        if (json.success) location.reload();
        btn.innerHTML = '<i class="fas fa-fingerprint mr-2"></i>Presensi Mata Pelajaran';
        refreshButtons();
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
