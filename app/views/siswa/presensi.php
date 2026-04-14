<?php
$page_title = "Presensi";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="max-w-6xl mx-auto">
    <!-- Header Presensi -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Sistem Presensi Digital</h1>
        <p class="text-gray-600">Presensi berbasis lokasi GPS dengan validasi menggunakan algoritma Haversine</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Presensi Sekolah -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-school text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Presensi Sekolah</h2>
                    <p class="text-gray-600">Presensi kehadiran di sekolah</p>
                </div>
            </div>
            
            <div id="locationStatus" class="mb-6 p-4 rounded-lg bg-yellow-100 border border-yellow-300">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-map-marker-alt text-yellow-600 text-xl"></i>
                        <div>
                            <p class="font-medium text-yellow-800">Mendeteksi lokasi...</p>
                            <p class="text-yellow-700 text-sm">Pastikan Anda mengizinkan akses lokasi</p>
                        </div>
                    </div>
                    <div id="loadingSpinner" class="animate-spin rounded-full h-6 w-6 border-b-2 border-yellow-600"></div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h4 class="font-medium text-blue-800 mb-3">Informasi Presensi</h4>
                    <div class="space-y-2 text-sm text-blue-700">
                        <div class="flex justify-between">
                            <span>Status Sesi:</span>
                            <span id="sessionSekolahStatus" class="font-medium">Memeriksa...</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Status Lokasi:</span>
                            <span id="locationValid" class="font-medium">Memeriksa...</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Jarak dari Sekolah:</span>
                            <span id="distanceInfo" class="font-medium">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Waktu Sekarang:</span>
                            <span id="currentTime" class="font-medium"><?php echo date('H:i:s'); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tanggal:</span>
                            <span class="font-medium"><?php echo date('d F Y'); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Jenis Presensi Sekolah -->
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Presensi</label>
                    <select id="jenisPresensiSekolah" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="hadir">Hadir</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                    </select>
                </div>

                <!-- Form Alasan (hidden by default) -->
                <div id="formAlasanSekolah" class="p-4 bg-gray-50 rounded-lg border border-gray-200 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan <span class="text-red-500">*</span></label>
                    <textarea id="alasanSekolah" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Jelaskan alasan Anda..."></textarea>
                    
                    <label class="block text-sm font-medium text-gray-700 mt-3 mb-2">Bukti (Opsional)</label>
                    <input type="file" id="buktiSekolah" accept="image/*,.pdf" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, atau PDF. Maks 2MB</p>
                </div>

                <button id="presensiSekolahBtn" 
                        onclick="submitPresensiSekolah()" 
                        disabled
                        class="w-full bg-gray-400 cursor-not-allowed text-white font-medium py-4 px-6 rounded-lg transition duration-300 flex items-center justify-center space-x-3 text-lg">
                    <i class="fas fa-fingerprint"></i>
                    <span>Presensi Sekolah</span>
                </button>

                <div class="text-center">
                    <p id="infoGPSSekolah" class="text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Untuk presensi Hadir, Anda harus berada dalam radius <?php echo $lokasiSekolah->radius_presensi ?? MAX_RADIUS; ?>m dari sekolah
                    </p>
                </div>
            </div>
        </div>

        <!-- Presensi Mata Pelajaran -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book text-green-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Presensi Mata Pelajaran</h2>
                    <p class="text-gray-600">Presensi kehadiran di mata pelajaran</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Mata Pelajaran</label>
                    <select id="kelasSelect" class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                        <option value="">-- Pilih Mata Pelajaran yang Akan Diikuti --</option>
                        <?php foreach($kelas as $k): ?>
                            <option value="<?php echo $k->id; ?>" 
                                    data-nama="<?php echo htmlspecialchars($k->nama_mata_pelajaran); ?>"
                                    data-kelas="<?php echo htmlspecialchars($k->nama_kelas ?? 'Tidak ada kelas'); ?>"
                                    data-tahun="<?php echo htmlspecialchars($k->tahun_ajaran ?? '-'); ?>"
                                    data-guru="<?php echo htmlspecialchars($k->guru_pengampu_nama ?? 'Belum ditentukan'); ?>"
                                    data-wali="<?php echo htmlspecialchars($k->wali_kelas_nama ?? 'Belum ditentukan'); ?>"
                                    data-jadwal="<?php echo htmlspecialchars($k->jadwal ?? 'Belum diatur'); ?>">
                                <?php echo htmlspecialchars($k->nama_mata_pelajaran); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Info Mata Pelajaran Detail -->
                <div id="kelasDetailCard" class="hidden">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-5 border border-green-200">
                        <div class="flex items-start space-x-4">
                            <div class="w-14 h-14 bg-green-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-book-open text-white text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 id="kelasDetailNama" class="text-lg font-bold text-gray-800 mb-1">-</h3>
                                <p id="kelasDetailKelas" class="text-sm text-gray-600 mb-1">-</p>
                                <p id="kelasDetailTahun" class="text-sm text-gray-600 mb-3">-</p>
                                
                                <div class="grid grid-cols-1 gap-2 text-sm">
                                    <div class="flex items-center text-gray-700">
                                        <i class="fas fa-chalkboard-teacher w-5 text-green-600"></i>
                                        <span class="ml-2"><strong>Guru Pengampu:</strong> <span id="kelasDetailGuru">-</span></span>
                                    </div>
                                    <div class="flex items-center text-gray-700">
                                        <i class="fas fa-user-tie w-5 text-green-600"></i>
                                        <span class="ml-2"><strong>Wali Kelas:</strong> <span id="kelasDetailWali">-</span></span>
                                    </div>
                                    <div class="flex items-center text-gray-700">
                                        <i class="fas fa-clock w-5 text-green-600"></i>
                                        <span class="ml-2"><strong>Jadwal:</strong> <span id="kelasDetailJadwal">-</span></span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-info-circle w-5 text-green-600"></i>
                                        <span class="ml-2"><strong>Status Sesi:</strong> <span id="statusKelas" class="font-semibold">-</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Jenis Presensi Kelas -->
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Presensi</label>
                    <select id="jenisPresensiKelas" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="hadir">Hadir</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                    </select>
                </div>

                <!-- Form Alasan Kelas (hidden by default) -->
                <div id="formAlasanKelas" class="p-4 bg-gray-50 rounded-lg border border-gray-200 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan <span class="text-red-500">*</span></label>
                    <textarea id="alasanKelas" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Jelaskan alasan Anda..."></textarea>
                    
                    <label class="block text-sm font-medium text-gray-700 mt-3 mb-2">Bukti (Opsional)</label>
                    <input type="file" id="buktiKelas" accept="image/*,.pdf" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, atau PDF. Maks 2MB</p>
                </div>

                <button id="presensiKelasBtn" 
                        onclick="submitPresensiKelas()" 
                        disabled
                        class="w-full bg-gray-400 cursor-not-allowed text-white font-medium py-4 px-6 rounded-lg transition duration-300 flex items-center justify-center space-x-3 text-lg">
                    <i class="fas fa-book"></i>
                    <span>Presensi Mata Pelajaran</span>
                </button>
                
                <div class="text-center">
                    <p id="infoGPSKelas" class="text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Untuk presensi Hadir, Anda harus berada dalam radius <?php echo $lokasiSekolah->radius_presensi ?? MAX_RADIUS; ?>m dari sekolah
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Peta Lokasi -->
    <div class="mt-8 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Peta Lokasi</h3>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div id="map" class="h-96 rounded-lg border border-gray-300"></div>
            </div>
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-800 mb-2">Legenda Peta</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-red-500 rounded-full border-2 border-white shadow"></div>
                            <span>Posisi Sekolah</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-blue-500 rounded-full border-2 border-white shadow"></div>
                            <span>Posisi Anda</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 border-2 border-blue-500 bg-blue-100 rounded-full"></div>
                            <span>Radius Presensi (<?php echo $lokasiSekolah->radius_presensi ?? MAX_RADIUS; ?>m)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ============================================================
// STATE GLOBAL
// ============================================================
const kelasSesi = <?php
    $map = [];
    foreach($kelas as $kk) { $map[$kk->id] = $kk->sesi_aktif ? true : false; }
    echo json_encode($map);
?>;

let userLocation        = null;   // koordinat GPS pengguna
let locationReady       = false;  // flag: GPS sudah didapat
let sessionReady        = false;  // flag: polling sesi sekolah sudah selesai pertama kali

let schoolLocation      = {
    lat: <?php echo $lokasiSekolah->latitude  ?? DEFAULT_LATITUDE; ?>,
    lng: <?php echo $lokasiSekolah->longitude ?? DEFAULT_LONGITUDE; ?>
};
let radiusPresensi      = <?php echo $lokasiSekolah->radius_presensi ?? MAX_RADIUS; ?>;

let map, userMarker, schoolMarker, accuracyCircle;

// State sesi sekolah (diisi oleh polling)
let sessionActive           = false;
let sessionAlreadyPresenced = false;

// State presensi kelas (lokal, reset tiap halaman)
let kelasAlreadyPresenced = {};
let isSubmittingSekolah = false;
let isSubmittingKelas = false;

// ============================================================
// INISIALISASI PETA
// ============================================================
function initMap() {
    map = L.map('map').setView([schoolLocation.lat, schoolLocation.lng], 17);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const redIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
        iconSize: [25, 41], iconAnchor: [12, 41],
        popupAnchor: [1, -34], shadowSize: [41, 41]
    });

    schoolMarker = L.marker([schoolLocation.lat, schoolLocation.lng], { icon: redIcon })
        .addTo(map)
        .bindPopup('<div class="text-center"><strong>SMK Negeri 7 Yogyakarta</strong><br><small>Lokasi Presensi</small></div>')
        .openPopup();

    L.circle([schoolLocation.lat, schoolLocation.lng], {
        color: 'blue', fillColor: '#3454be',
        fillOpacity: 0.1, radius: radiusPresensi, weight: 2
    }).addTo(map).bindPopup('Radius Presensi: ' + radiusPresensi + ' meter');
}

// ============================================================
// ALGORITMA HAVERSINE
// ============================================================
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R    = 6371000;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a    =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c    = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

// ============================================================
// EVALUASI TOMBOL — dipanggil setiap kali state berubah
// ============================================================
function evaluateUI() {
    // Hanya evaluasi setelah KEDUANYA siap (GPS + polling sesi)
    if (!locationReady || !sessionReady) return;

    const jenis    = document.getElementById('jenisPresensiSekolah').value;
    const btn      = document.getElementById('presensiSekolahBtn');
    const distance = calculateDistance(
        userLocation.lat, userLocation.lng,
        schoolLocation.lat, schoolLocation.lng
    );
    const inRadius = distance <= radiusPresensi;

    // --- Update UI lokasi ---
    const statusEl   = document.getElementById('locationStatus');
    const validEl    = document.getElementById('locationValid');
    const distanceEl = document.getElementById('distanceInfo');

    distanceEl.textContent = distance.toFixed(2) + ' meter';
    document.getElementById('loadingSpinner').classList.add('hidden');

    if (inRadius) {
        statusEl.className = 'mb-6 p-4 rounded-lg bg-green-100 border border-green-300';
        statusEl.innerHTML = `
            <div class="flex items-center space-x-3">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                <div>
                    <p class="font-medium text-green-800">Lokasi valid untuk presensi</p>
                    <p class="text-green-700 text-sm">Anda berada dalam radius sekolah</p>
                </div>
            </div>`;
        validEl.textContent  = 'Valid';
        validEl.className    = 'font-medium text-green-600';
    } else {
        statusEl.className = 'mb-6 p-4 rounded-lg bg-red-100 border border-red-300';
        statusEl.innerHTML = `
            <div class="flex items-center space-x-3">
                <i class="fas fa-times-circle text-red-600 text-xl"></i>
                <div>
                    <p class="font-medium text-red-800">Lokasi tidak valid</p>
                    <p class="text-red-700 text-sm">Anda berada di luar radius sekolah (${distance.toFixed(2)}m)</p>
                </div>
            </div>`;
        validEl.textContent  = 'Tidak Valid';
        validEl.className    = 'font-medium text-red-600';
    }

    // --- Evaluasi tombol presensi SEKOLAH ---
    if (sessionAlreadyPresenced) {
        // Sudah presensi hari ini
        setButtonState(btn, false, 'gray');
    } else if (!sessionActive) {
        // Sesi belum dibuka admin
        setButtonState(btn, false, 'gray');
    } else if (jenis === 'izin' || jenis === 'sakit') {
        // Izin/sakit tidak butuh validasi GPS
        setButtonState(btn, true, 'blue');
    } else {
        // Hadir: butuh lokasi valid
        setButtonState(btn, inRadius, inRadius ? 'blue' : 'gray');
    }

    // --- Evaluasi tombol presensi KELAS ---
    updatePresensiKelasButton();
}

function setButtonState(btn, enabled, color) {
    if (enabled) {
        btn.disabled  = false;
        btn.className = `w-full bg-${color}-600 hover:bg-${color}-700 text-white font-medium py-4 px-6 rounded-lg transition duration-300 flex items-center justify-center space-x-3 text-lg`;
    } else {
        btn.disabled  = true;
        btn.className = 'w-full bg-gray-400 cursor-not-allowed text-white font-medium py-4 px-6 rounded-lg transition duration-300 flex items-center justify-center space-x-3 text-lg';
    }
}

// ============================================================
// GPS: DAPATKAN LOKASI PENGGUNA
// ============================================================
function getUserLocation() {
    if (!navigator.geolocation) {
        document.getElementById('locationStatus').innerHTML = `
            <div class="flex items-center space-x-3">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                <div>
                    <p class="font-medium text-red-800">Browser tidak mendukung geolocation</p>
                    <p class="text-red-700 text-sm">Gunakan browser modern seperti Chrome atau Firefox</p>
                </div>
            </div>`;
        document.getElementById('loadingSpinner').classList.add('hidden');
        return;
    }

    navigator.geolocation.getCurrentPosition(
        function(position) {
            userLocation = {
                lat:      position.coords.latitude,
                lng:      position.coords.longitude,
                accuracy: position.coords.accuracy
            };
            locationReady = true;   // ✅ GPS siap

            updateMap();
            evaluateUI();           // evaluasi setelah GPS selesai
        },
        function(error) {
            document.getElementById('locationStatus').innerHTML = `
                <div class="flex items-center space-x-3">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    <div>
                        <p class="font-medium text-red-800">Gagal mendapatkan lokasi</p>
                        <p class="text-red-700 text-sm">Error: ${error.message}</p>
                    </div>
                </div>`;
            document.getElementById('loadingSpinner').classList.add('hidden');
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
    );
}

// ============================================================
// POLLING SESI SEKOLAH
// ============================================================
function fetchSchoolSessionStatus() {
    fetch('index.php?action=get_presensi_sekolah_status')
        .then(r => r.json())
        .then(json => {
            sessionActive           = !!json.active;
            sessionAlreadyPresenced = !!json.already_presenced;
            sessionReady            = true;   // ✅ polling pertama selesai

            // Update label status sesi
            const statusEl = document.getElementById('sessionSekolahStatus');
            if (sessionActive) {
                if (sessionAlreadyPresenced) {
                    statusEl.textContent = 'Aktif - Sudah Presensi';
                    statusEl.className   = 'font-medium text-gray-600';
                } else {
                    statusEl.textContent = 'Aktif - Belum Presensi';
                    statusEl.className   = 'font-medium text-green-600';
                }
            } else {
                statusEl.textContent = 'Tidak Aktif';
                statusEl.className   = 'font-medium text-red-600';
            }

            evaluateUI();   // evaluasi setelah polling selesai
        })
        .catch(err => console.error('Failed to fetch session status:', err));
}

// ============================================================
// UPDATE PETA
// ============================================================
function updateMap() {
    if (userMarker)     map.removeLayer(userMarker);
    if (accuracyCircle) map.removeLayer(accuracyCircle);

    userMarker = L.marker([userLocation.lat, userLocation.lng])
        .addTo(map)
        .bindPopup(`
            <div class="text-center">
                <strong>Posisi Anda</strong><br>
                <small>Akurasi: ±${userLocation.accuracy.toFixed(1)}m</small>
            </div>`)
        .openPopup();

    const group = new L.featureGroup([schoolMarker, userMarker]);
    map.fitBounds(group.getBounds().pad(0.1));
}

// ============================================================
// TOMBOL PRESENSI KELAS
// ============================================================
function updatePresensiKelasButton() {
    const btn          = document.getElementById('presensiKelasBtn');
    const selectedKelas = document.getElementById('kelasSelect').value;
    const jenisPresensi = document.getElementById('jenisPresensiKelas').value;

    // Default: nonaktif
    setButtonState(btn, false, 'gray');

    if (!selectedKelas)                      return; // belum pilih kelas
    if (kelasAlreadyPresenced[selectedKelas]) return; // sudah presensi
    if (!kelasSesi[selectedKelas])           return; // tidak ada sesi aktif

    if (jenisPresensi === 'izin' || jenisPresensi === 'sakit') {
        // Izin/sakit tidak butuh GPS
        setButtonState(btn, true, 'blue');
    } else {
        // Hadir: butuh lokasi valid
        if (locationReady) {
            const distance = calculateDistance(
                userLocation.lat, userLocation.lng,
                schoolLocation.lat, schoolLocation.lng
            );
            setButtonState(btn, distance <= radiusPresensi, 'blue');
        }
        // Jika GPS belum ready, tombol tetap gray
    }
}

// ============================================================
// SUBMIT PRESENSI SEKOLAH
// ============================================================
function submitPresensiSekolah() {
    if (isSubmittingSekolah) return;

    const btn    = document.getElementById('presensiSekolahBtn');
    const jenis  = document.getElementById('jenisPresensiSekolah').value;
    const alasan = document.getElementById('alasanSekolah').value;
    const bukti  = document.getElementById('buktiSekolah').files[0];

    if ((jenis === 'izin' || jenis === 'sakit') && !alasan.trim()) {
        showNotification('error', 'Alasan wajib diisi untuk jenis ' + jenis);
        return;
    }

    if (jenis === 'hadir' && !locationReady) {
        showNotification('error', 'Lokasi belum tersedia. Pastikan GPS aktif.');
        return;
    }

    btn.disabled  = true;
    btn.innerHTML = '<i class="fas fa-spinner animate-spin"></i><span>Memproses...</span>';
    isSubmittingSekolah = true;

    const fd = new FormData();
    fd.append('latitude',  jenis === 'hadir' ? userLocation.lat : 0);
    fd.append('longitude', jenis === 'hadir' ? userLocation.lng : 0);
    fd.append('jenis', jenis);
    if (alasan.trim()) fd.append('alasan', alasan);
    if (bukti)         fd.append('bukti',  bukti);

    let presensiSekolahBerhasil = false;

    fetch('index.php?action=submit_presensi_sekolah', { method: 'POST', body: fd })
        .then(async res => {
            const responseText = await res.text();

            let json;
            try {
                json = JSON.parse(responseText);
            } catch (parseError) {
                throw new Error('Respons server tidak valid: ' + responseText.slice(0, 200));
            }

            if (!res.ok) {
                throw new Error(json.message || 'HTTP ' + res.status);
            }

            return json;
        })
        .then(json => {
            if (json.success) {
                const label = jenis === 'hadir' ? 'Hadir' : jenis === 'izin' ? 'Izin' : 'Sakit';
                showNotification('success', 'Presensi sekolah berhasil dicatat! Status: ' + label);
                addToPresensiHistory('Sekolah', label, new Date().toLocaleTimeString());

                presensiSekolahBerhasil = true;
                sessionAlreadyPresenced = true;

                // Reset form
                document.getElementById('jenisPresensiSekolah').value = 'hadir';
                document.getElementById('alasanSekolah').value        = '';
                document.getElementById('buktiSekolah').value         = '';
                document.getElementById('formAlasanSekolah').classList.add('hidden');

                try {
                    // Update status label
                    const statusEl       = document.getElementById('sessionSekolahStatus');
                    statusEl.textContent = 'Aktif - Sudah Presensi';
                    statusEl.className   = 'font-medium text-gray-600';
                } catch (uiError) {
                    console.error('UI update error after presensi sekolah sukses:', uiError);
                }

                evaluateUI();
            } else {
                showNotification('error', json.message || 'Gagal mencatat presensi');
            }
        })
        .catch(err => {
            console.error(err);
            if (!presensiSekolahBerhasil) {
                showNotification('error', err.message || 'Terjadi kesalahan saat mengirim presensi');
            }
        })
        .finally(() => {
            btn.innerHTML = '<i class="fas fa-fingerprint"></i><span>Presensi Sekolah</span>';
            isSubmittingSekolah = false;
            evaluateUI();
        });
}

// ============================================================
// SUBMIT PRESENSI KELAS
// ============================================================
function submitPresensiKelas() {
    if (isSubmittingKelas) return;

    const kelasSelect   = document.getElementById('kelasSelect');
    const selectedKelas = kelasSelect.value;

    if (!selectedKelas) {
        showNotification('error', 'Pilih kelas terlebih dahulu!');
        return;
    }
    if (!kelasSesi[selectedKelas]) {
        showNotification('error', 'Belum ada sesi presensi aktif untuk kelas ini.');
        return;
    }

    const btn       = document.getElementById('presensiKelasBtn');
    const kelasName = kelasSelect.options[kelasSelect.selectedIndex].text;
    const jenis     = document.getElementById('jenisPresensiKelas').value;
    const alasan    = document.getElementById('alasanKelas').value;
    const bukti     = document.getElementById('buktiKelas').files[0];

    if ((jenis === 'izin' || jenis === 'sakit') && !alasan.trim()) {
        showNotification('error', 'Alasan wajib diisi untuk jenis ' + jenis);
        return;
    }

    if (jenis === 'hadir' && !locationReady) {
        showNotification('error', 'Lokasi belum tersedia. Pastikan GPS aktif.');
        return;
    }

    btn.disabled  = true;
    btn.innerHTML = '<i class="fas fa-spinner animate-spin"></i><span>Memproses...</span>';
    isSubmittingKelas = true;

    const fd = new FormData();
    fd.append('kelas_id',  selectedKelas);
    fd.append('latitude',  jenis === 'hadir' ? userLocation.lat : 0);
    fd.append('longitude', jenis === 'hadir' ? userLocation.lng : 0);
    fd.append('jenis', jenis);
    if (alasan.trim()) fd.append('alasan', alasan);
    if (bukti)         fd.append('bukti',  bukti);

    let presensiKelasBerhasil = false;

    fetch('index.php?action=submit_presensi_mapel', { method: 'POST', body: fd })
        .then(async res => {
            const responseText = await res.text();

            let json;
            try {
                json = JSON.parse(responseText);
            } catch (parseError) {
                throw new Error('Respons server tidak valid: ' + responseText.slice(0, 200));
            }

            if (!res.ok) {
                throw new Error(json.message || 'HTTP ' + res.status);
            }

            return json;
        })
        .then(data => {
            if (data.success) {
                const label = jenis === 'hadir' ? 'Hadir' : jenis === 'izin' ? 'Izin' : 'Sakit';
                showNotification('success', `Presensi kelas ${kelasName} berhasil! Status: ${label}`);
                addToPresensiHistory(kelasName, label, new Date().toLocaleTimeString());

                presensiKelasBerhasil = true;
                kelasAlreadyPresenced[selectedKelas] = true;

                // Reset form
                document.getElementById('jenisPresensiKelas').value = 'hadir';
                document.getElementById('alasanKelas').value        = '';
                document.getElementById('buktiKelas').value         = '';
                document.getElementById('formAlasanKelas').classList.add('hidden');

                try {
                    document.getElementById('statusKelas').textContent = 'Sudah Presensi';
                    document.getElementById('statusKelas').className   = 'text-gray-600 font-semibold';
                } catch (uiError) {
                    console.error('UI update error after presensi kelas sukses:', uiError);
                }

                updatePresensiKelasButton();
            } else {
                showNotification('error', data.message || 'Gagal mencatat presensi kelas');
            }
        })
        .catch(err => {
            console.error(err);
            if (!presensiKelasBerhasil) {
                showNotification('error', err.message || 'Terjadi kesalahan saat mengirim presensi');
            }
        })
        .finally(() => {
            btn.innerHTML = '<i class="fas fa-book"></i><span>Presensi Mata Pelajaran</span>';
            isSubmittingKelas = false;
            updatePresensiKelasButton();
        });
}

// ============================================================
// HELPER: STATUS KELAS
// ============================================================
function updateStatusKelas() {
    const selectedKelas = document.getElementById('kelasSelect').value;
    const jenisPresensi = document.getElementById('jenisPresensiKelas').value;
    const statusEl      = document.getElementById('statusKelas');

    if (!selectedKelas) {
        statusEl.textContent = 'Belum dipilih';
        statusEl.className   = 'text-yellow-600 font-semibold';
        return;
    }
    if (kelasAlreadyPresenced[selectedKelas]) {
        statusEl.textContent = 'Sudah Presensi';
        statusEl.className   = 'text-gray-600 font-semibold';
        return;
    }
    if (!kelasSesi[selectedKelas]) {
        statusEl.textContent = 'Tidak ada sesi aktif';
        statusEl.className   = 'text-red-600 font-semibold';
        return;
    }
    if (jenisPresensi === 'izin' || jenisPresensi === 'sakit') {
        statusEl.textContent = 'Sesi Aktif';
        statusEl.className   = 'text-green-600 font-semibold';
        return;
    }
    if (!locationReady) {
        statusEl.textContent = 'Menunggu lokasi GPS...';
        statusEl.className   = 'text-yellow-600 font-semibold';
        return;
    }
    const distance = calculateDistance(
        userLocation.lat, userLocation.lng,
        schoolLocation.lat, schoolLocation.lng
    );
    if (distance <= radiusPresensi) {
        statusEl.textContent = 'Sesi Aktif - Lokasi Valid';
        statusEl.className   = 'text-green-600 font-semibold';
    } else {
        statusEl.textContent = 'Sesi Aktif - Lokasi Terlalu Jauh';
        statusEl.className   = 'text-red-600 font-semibold';
    }
}

// ============================================================
// NOTIFIKASI
// ============================================================
function showNotification(type, message) {
    const n = document.createElement('div');
    n.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    n.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i>
            <span>${message}</span>
        </div>`;
    document.body.appendChild(n);
    setTimeout(() => n.remove(), 3000);
}

function addToPresensiHistory(jenis, status, waktu) {
    console.log(`Presensi ${jenis}: ${status} pada ${waktu}`);
}

function updateTime() {
    document.getElementById('currentTime').textContent = new Date().toLocaleTimeString();
}

// ============================================================
// INISIALISASI
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    initMap();
    getUserLocation();          // async — set locationReady = true saat selesai
    fetchSchoolSessionStatus(); // async — set sessionReady  = true saat selesai
    setInterval(fetchSchoolSessionStatus, 15000);
    setInterval(updateTime, 1000);

    // Jenis presensi SEKOLAH berubah
    document.getElementById('jenisPresensiSekolah').addEventListener('change', function() {
        const formAlasan       = document.getElementById('formAlasanSekolah');
        const btn              = document.getElementById('presensiSekolahBtn');
        const infoGPS          = document.getElementById('infoGPSSekolah');
        const isIzinSakit      = this.value === 'izin' || this.value === 'sakit';

        formAlasan.classList.toggle('hidden', !isIzinSakit);

        if (isIzinSakit) {
            btn.innerHTML    = '<i class="fas fa-paper-plane"></i><span>Submit</span>';
            infoGPS.textContent = 'Untuk izin/sakit, tidak perlu validasi GPS';
        } else {
            btn.innerHTML    = '<i class="fas fa-fingerprint"></i><span>Presensi Sekolah</span>';
            infoGPS.textContent = 'Untuk presensi Hadir, Anda harus berada dalam radius ' + radiusPresensi + 'm dari sekolah';
        }

        evaluateUI();
    });

    // Jenis presensi KELAS berubah
    document.getElementById('jenisPresensiKelas').addEventListener('change', function() {
        const formAlasan  = document.getElementById('formAlasanKelas');
        const btn         = document.getElementById('presensiKelasBtn');
        const infoGPS     = document.getElementById('infoGPSKelas');
        const isIzinSakit = this.value === 'izin' || this.value === 'sakit';

        formAlasan.classList.toggle('hidden', !isIzinSakit);

        if (isIzinSakit) {
            btn.innerHTML       = '<i class="fas fa-paper-plane"></i><span>Submit</span>';
            infoGPS.textContent = 'Untuk izin/sakit, tidak perlu validasi GPS';
        } else {
            btn.innerHTML       = '<i class="fas fa-book"></i><span>Presensi Mata Pelajaran</span>';
            infoGPS.textContent = 'Untuk presensi Hadir, Anda harus berada dalam radius ' + radiusPresensi + 'm dari sekolah';
        }

        updateStatusKelas();
        updatePresensiKelasButton();
    });

    // Pilih kelas berubah
    document.getElementById('kelasSelect').addEventListener('change', function() {
        const selected      = this.value;
        const kelasDetailCard = document.getElementById('kelasDetailCard');

        if (!selected) {
            kelasDetailCard.classList.add('hidden');
            updateStatusKelas();
            updatePresensiKelasButton();
            return;
        }

        const opt = this.options[this.selectedIndex];
        document.getElementById('kelasDetailNama').textContent   = opt.getAttribute('data-nama');
        document.getElementById('kelasDetailKelas').textContent  = 'Kelas: '        + opt.getAttribute('data-kelas');
        document.getElementById('kelasDetailTahun').textContent  = 'Tahun Ajaran: ' + opt.getAttribute('data-tahun');
        document.getElementById('kelasDetailGuru').textContent   = opt.getAttribute('data-guru');
        document.getElementById('kelasDetailWali').textContent   = opt.getAttribute('data-wali');
        document.getElementById('kelasDetailJadwal').textContent = opt.getAttribute('data-jadwal');
        kelasDetailCard.classList.remove('hidden');

        updateStatusKelas();
        updatePresensiKelasButton();
    });
});

// Refresh lokasi saat halaman kembali aktif
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        locationReady = false;  // reset agar evaluateUI menunggu GPS baru
        getUserLocation();
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>