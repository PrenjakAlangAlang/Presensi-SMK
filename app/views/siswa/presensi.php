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
                        class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium py-4 px-6 rounded-lg transition duration-300 flex items-center justify-center space-x-3 text-lg">
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

        <!-- Presensi Kelas -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chalkboard text-green-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Presensi Kelas</h2>
                    <p class="text-gray-600">Presensi kehadiran di kelas</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kelas</label>
                    <select id="kelasSelect" class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                        <option value="">-- Pilih Kelas yang Akan Diikuti --</option>
                        <?php foreach($kelas as $k): ?>
                            <option value="<?php echo $k->id; ?>" 
                                    data-nama="<?php echo htmlspecialchars($k->nama_kelas); ?>"
                                    data-tahun="<?php echo htmlspecialchars($k->tahun_ajaran ?? '-'); ?>"
                                    data-wali="<?php echo htmlspecialchars($k->wali_kelas_nama ?? 'Belum ditentukan'); ?>"
                                    data-jadwal="<?php echo htmlspecialchars($k->jadwal ?? 'Belum diatur'); ?>">
                                <?php echo htmlspecialchars($k->nama_kelas); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Info Kelas Detail (ditampilkan setelah kelas dipilih) -->
                <div id="kelasDetailCard" class="hidden">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-5 border border-green-200">
                        <div class="flex items-start space-x-4">
                            <div class="w-14 h-14 bg-green-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-chalkboard-teacher text-white text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 id="kelasDetailNama" class="text-lg font-bold text-gray-800 mb-1">-</h3>
                                <p id="kelasDetailTahun" class="text-sm text-gray-600 mb-3">-</p>
                                
                                <div class="grid grid-cols-1 gap-2 text-sm">
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
                        class="w-full bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium py-4 px-6 rounded-lg transition duration-300 flex items-center justify-center space-x-3 text-lg shadow-lg">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Presensi Kelas</span>
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
                            <span>Radius Presensi (100m)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Mapping kelas_id => whether session active (from server)
const kelasSesi = <?php
    $map = [];
    foreach($kelas as $kk) { $map[$kk->id] = $kk->sesi_aktif ? true : false; }
    echo json_encode($map);
?>;

let userLocation = null;
let schoolLocation = { lat: <?php echo $lokasiSekolah->latitude ?? DEFAULT_LATITUDE; ?>, lng: <?php echo $lokasiSekolah->longitude ?? DEFAULT_LONGITUDE; ?> };
let radiusPresensi = <?php echo $lokasiSekolah->radius_presensi ?? MAX_RADIUS; ?>;
let map, userMarker, schoolMarker, accuracyCircle;
let sessionActive = false;
let sessionAlreadyPresenced = false;

// Initialize map
function initMap() {
    map = L.map('map').setView([schoolLocation.lat, schoolLocation.lng], 17);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Create red icon for school marker
    const redIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });
    
    // Add school marker with red icon
    schoolMarker = L.marker([schoolLocation.lat, schoolLocation.lng], {icon: redIcon})
        .addTo(map)
        .bindPopup(`
            <div class="text-center">
                <strong>SMK Negeri 7 Yogyakarta</strong><br>
                <small>Lokasi Presensi</small>
            </div>
        `)
        .openPopup();
    
    // Add circle for radius
    L.circle([schoolLocation.lat, schoolLocation.lng], {
        color: 'blue',
        fillColor: '#ff0000',
        fillOpacity: 0.1,
        radius: radiusPresensi,
        weight: 2
    }).addTo(map).bindPopup('Radius Presensi: ' + radiusPresensi + ' meter');
}

// Get user location
function getUserLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                    accuracy: position.coords.accuracy
                };
                
                updateLocationStatus();
                updateMap();
            },
            function(error) {
                document.getElementById('locationStatus').innerHTML = `
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        <div>
                            <p class="font-medium text-red-800">Gagal mendapatkan lokasi</p>
                            <p class="text-red-700 text-sm">Error: ${error.message}</p>
                        </div>
                    </div>
                `;
                document.getElementById('loadingSpinner').classList.add('hidden');
            },
            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            }
        );
    } else {
        document.getElementById('locationStatus').innerHTML = `
            <div class="flex items-center space-x-3">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                <div>
                    <p class="font-medium text-red-800">Browser tidak mendukung geolocation</p>
                    <p class="text-red-700 text-sm">Gunakan browser modern seperti Chrome atau Firefox</p>
                </div>
            </div>
        `;
        document.getElementById('loadingSpinner').classList.add('hidden');
    }
}

// Update location status
function updateLocationStatus() {
    const distance = calculateDistance(
        userLocation.lat, 
        userLocation.lng, 
        schoolLocation.lat, 
        schoolLocation.lng
    );
    
    const isValid = distance <= radiusPresensi;
    const statusElement = document.getElementById('locationStatus');
    const validElement = document.getElementById('locationValid');
    const distanceElement = document.getElementById('distanceInfo');
    const presensiSekolahBtn = document.getElementById('presensiSekolahBtn');
    const presensiKelasBtn = document.getElementById('presensiKelasBtn');
    
    distanceElement.textContent = distance.toFixed(2) + ' meter';
    document.getElementById('loadingSpinner').classList.add('hidden');
    
    if (isValid) {
        statusElement.className = 'mb-6 p-4 rounded-lg bg-green-100 border border-green-300';
        statusElement.innerHTML = `
            <div class="flex items-center space-x-3">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                <div>
                    <p class="font-medium text-green-800">Lokasi valid untuk presensi</p>
                    <p class="text-green-700 text-sm">Anda berada dalam radius sekolah</p>
                </div>
            </div>
        `;
        validElement.textContent = 'Valid';
        validElement.className = 'font-medium text-green-600';
        // enable presensi sekolah only if there is an active session and user hasn't presenced yet
        if (sessionActive && !sessionAlreadyPresenced) {
            presensiSekolahBtn.disabled = false;
        } else {
            presensiSekolahBtn.disabled = true;
        }
        presensiKelasBtn.disabled = false;
    } else {
        statusElement.className = 'mb-6 p-4 rounded-lg bg-red-100 border border-red-300';
        statusElement.innerHTML = `
            <div class="flex items-center space-x-3">
                <i class="fas fa-times-circle text-red-600 text-xl"></i>
                <div>
                    <p class="font-medium text-red-800">Lokasi tidak valid</p>
                    <p class="text-red-700 text-sm">Anda berada di luar radius sekolah (${distance.toFixed(2)}m)</p>
                </div>
            </div>
        `;
        validElement.textContent = 'Tidak Valid';
        validElement.className = 'font-medium text-red-600';
        presensiSekolahBtn.disabled = true;
        presensiKelasBtn.disabled = true;
    }
}

// Update map with user location
function updateMap() {
    if (userMarker) {
        map.removeLayer(userMarker);
    }
    if (accuracyCircle) {
        map.removeLayer(accuracyCircle);
    }

    userMarker = L.marker([userLocation.lat, userLocation.lng])
        .addTo(map)
        .bindPopup(`
            <div class="text-center">
                <strong>Posisi Anda</strong><br>
                <small>Akurasi: ±${userLocation.accuracy.toFixed(1)}m</small>
            </div>
        `)
        .openPopup();

    // Add accuracy circle
    accuracyCircle = L.circle([userLocation.lat, userLocation.lng], {
        radius: userLocation.accuracy,
        color: 'blue',
        fillColor: '#3b82f6',
        fillOpacity: 0.1,
        weight: 1
    }).addTo(map);

    // Adjust map view to show both markers
    const group = new L.featureGroup([schoolMarker, userMarker]);
    map.fitBounds(group.getBounds().pad(0.1));
}

// Haversine formula to calculate distance
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371000; // Earth radius in meters
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = 
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
        Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    const distance = R * c;
    return distance;
}

// Submit presensi sekolah
function submitPresensiSekolah() {
    const btn = document.getElementById('presensiSekolahBtn');
    const originalText = btn.innerHTML;
    const jenis = document.getElementById('jenisPresensiSekolah').value;
    const alasan = document.getElementById('alasanSekolah').value;
    const buktiFile = document.getElementById('buktiSekolah').files[0];
    
    // Validasi alasan jika jenis izin atau sakit
    if ((jenis === 'izin' || jenis === 'sakit') && !alasan.trim()) {
        showNotification('error', 'Alasan wajib diisi untuk jenis ' + jenis);
        return;
    }
    
    // Untuk izin/sakit, tidak perlu validasi lokasi GPS
    if (jenis === 'izin' || jenis === 'sakit') {
        // Langsung submit tanpa cek lokasi
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner animate-spin"></i><span>Memproses...</span>';
        
        const fd = new FormData();
        fd.append('latitude', 0);
        fd.append('longitude', 0);
        fd.append('jenis', jenis);
        if (alasan.trim()) fd.append('alasan', alasan);
        if (buktiFile) fd.append('bukti', buktiFile);

        fetch('index.php?action=submit_presensi_sekolah', {
            method: 'POST',
            body: fd
        })
        .then(res => res.json())
        .then(json => {
            if (json.success) {
                const jenisText = jenis === 'izin' ? 'Izin' : 'Sakit';
                showNotification('success', 'Presensi sekolah berhasil dicatat! Status: ' + jenisText);
                addToPresensiHistory('Sekolah', jenisText, new Date().toLocaleTimeString());
                sessionAlreadyPresenced = true;
                document.getElementById('presensiSekolahBtn').disabled = true;
                
                // Reset form
                document.getElementById('jenisPresensiSekolah').value = 'hadir';
                document.getElementById('alasanSekolah').value = '';
                document.getElementById('buktiSekolah').value = '';
                document.getElementById('formAlasanSekolah').classList.add('hidden');
            } else {
                showNotification('error', json.message || 'Gagal mencatat presensi');
            }
        })
        .catch(err => {
            console.error(err);
            showNotification('error', 'Terjadi kesalahan saat mengirim presensi');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
        return;
    }
    
    // Untuk hadir, perlu validasi lokasi GPS
    if (!userLocation) {
        showNotification('error', 'Lokasi belum tersedia. Pastikan GPS aktif.');
        return;
    }
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner animate-spin"></i><span>Memproses...</span>';
    
    // Real API call to server
    const fd = new FormData();
    fd.append('latitude', userLocation.lat);
    fd.append('longitude', userLocation.lng);
    fd.append('jenis', jenis);
    if (alasan.trim()) fd.append('alasan', alasan);
    if (buktiFile) fd.append('bukti', buktiFile);

    fetch('index.php?action=submit_presensi_sekolah', {
        method: 'POST',
        body: fd
    })
    .then(res => res.json())
    .then(json => {
        if (json.success) {
            const jenisText = jenis === 'hadir' ? 'Hadir' : jenis === 'izin' ? 'Izin' : 'Sakit';
            showNotification('success', 'Presensi sekolah berhasil dicatat! Status: ' + jenisText);
            addToPresensiHistory('Sekolah', jenisText, new Date().toLocaleTimeString());
            // mark that current user has presenced for this session to prevent duplicate
            sessionAlreadyPresenced = true;
            document.getElementById('presensiSekolahBtn').disabled = true;
            
            // Reset form
            document.getElementById('jenisPresensiSekolah').value = 'hadir';
            document.getElementById('alasanSekolah').value = '';
            document.getElementById('buktiSekolah').value = '';
            document.getElementById('formAlasanSekolah').classList.add('hidden');
            // Reset teks tombol kembali ke default
            btn.innerHTML = '<i class="fas fa-fingerprint"></i><span>Presensi Sekolah</span>';
            // Reset teks tombol kembali ke default
            btn.innerHTML = '<i class="fas fa-fingerprint"></i><span>Presensi Sekolah</span>';
        } else {
            showNotification('error', json.message || 'Gagal mencatat presensi');
        }
    })
    .catch(err => {
        console.error(err);
        showNotification('error', 'Terjadi kesalahan saat mengirim presensi');
    })
    .finally(() => {
        btn.innerHTML = '<i class="fas fa-fingerprint"></i><span>Presensi Sekolah</span>';
        btn.disabled = false;
    });
}

// Submit presensi kelas
function submitPresensiKelas() {
    const kelasSelect = document.getElementById('kelasSelect');
    const selectedKelas = kelasSelect.value;

    if (!selectedKelas) {
        showNotification('error', 'Pilih kelas terlebih dahulu!');
        return;
    }

    // Check if there's an active session for the selected class
    if (!kelasSesi[selectedKelas]) {
        showNotification('error', 'Belum ada sesi presensi aktif untuk kelas ini.');
        return;
    }

    const btn = document.getElementById('presensiKelasBtn');
    const originalText = btn.innerHTML;
    const kelasName = kelasSelect.options[kelasSelect.selectedIndex].text;
    const jenis = document.getElementById('jenisPresensiKelas').value;
    const alasan = document.getElementById('alasanKelas').value;
    const buktiFile = document.getElementById('buktiKelas').files[0];
    
    // Validasi alasan jika jenis izin atau sakit
    if ((jenis === 'izin' || jenis === 'sakit') && !alasan.trim()) {
        showNotification('error', 'Alasan wajib diisi untuk jenis ' + jenis);
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner animate-spin"></i><span>Memproses...</span>';

    // Build form data and POST to server
    const formData = new FormData();
    formData.append('kelas_id', selectedKelas);
    
    // Untuk izin/sakit, tidak perlu lokasi GPS
    if (jenis === 'izin' || jenis === 'sakit') {
        formData.append('latitude', 0);
        formData.append('longitude', 0);
    } else {
        // Untuk hadir, cek lokasi dulu
        if (!userLocation) {
            showNotification('error', 'Lokasi belum tersedia. Pastikan GPS aktif.');
            btn.innerHTML = originalText;
            btn.disabled = false;
            return;
        }
        formData.append('latitude', userLocation.lat);
        formData.append('longitude', userLocation.lng);
    }
    
    formData.append('jenis', jenis);
    if (alasan.trim()) formData.append('alasan', alasan);
    if (buktiFile) formData.append('bukti', buktiFile);

    fetch('index.php?action=submit_presensi_kelas', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const jenisText = jenis === 'hadir' ? 'Hadir' : jenis === 'izin' ? 'Izin' : 'Sakit';
            showNotification('success', `Presensi kelas ${kelasName} berhasil! Status: ${jenisText}`);
            addToPresensiHistory(kelasName, jenisText, new Date().toLocaleTimeString());
            
            // Reset form
            document.getElementById('jenisPresensiKelas').value = 'hadir';
            document.getElementById('alasanKelas').value = '';
            document.getElementById('buktiKelas').value = '';
            document.getElementById('formAlasanKelas').classList.add('hidden');
            // Reset teks tombol kembali ke default
            btn.innerHTML = '<i class="fas fa-chalkboard-teacher"></i><span>Presensi Kelas</span>';
        } else {
            showNotification('error', data.message || 'Gagal mencatat presensi kelas');
        }
    })
    .catch(err => {
        console.error(err);
        showNotification('error', 'Terjadi kesalahan saat mengirim presensi');
    })
    .finally(() => {
        btn.innerHTML = '<i class="fas fa-chalkboard-teacher"></i><span>Presensi Kelas</span>';
        btn.disabled = false;
    });
}

// Show notification
function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Add to presensi history (simulated)
function addToPresensiHistory(jenis, status, waktu) {
    // This would typically update a history list
    console.log(`Presensi ${jenis}: ${status} pada ${waktu}`);
}

// Update current time
function updateTime() {
    document.getElementById('currentTime').textContent = new Date().toLocaleTimeString();
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initMap();
    getUserLocation();
    setInterval(updateTime, 1000);
    
    // Event listener untuk jenis presensi sekolah
    document.getElementById('jenisPresensiSekolah').addEventListener('change', function() {
        const formAlasan = document.getElementById('formAlasanSekolah');
        const presensiSekolahBtn = document.getElementById('presensiSekolahBtn');
        const infoGPSSekolah = document.getElementById('infoGPSSekolah');
        
        if (this.value === 'izin' || this.value === 'sakit') {
            formAlasan.classList.remove('hidden');
            // Ubah teks tombol menjadi "Submit"
            presensiSekolahBtn.innerHTML = '<i class="fas fa-paper-plane"></i><span>Submit</span>';
            // Ubah info GPS
            infoGPSSekolah.textContent = 'Untuk izin/sakit, tidak perlu validasi GPS';
            // Untuk izin/sakit, tidak perlu validasi lokasi - enable button jika ada sesi aktif
            if (sessionActive && !sessionAlreadyPresenced) {
                presensiSekolahBtn.disabled = false;
            }
        } else {
            formAlasan.classList.add('hidden');
            // Kembalikan teks tombol ke "Presensi Sekolah"
            presensiSekolahBtn.innerHTML = '<i class="fas fa-fingerprint"></i><span>Presensi Sekolah</span>';
            // Kembalikan info GPS
            infoGPSSekolah.textContent = 'Untuk presensi Hadir, Anda harus berada dalam radius ' + radiusPresensi + 'm dari sekolah';
            // Untuk hadir, perlu validasi lokasi - enable button hanya jika lokasi valid
            if (sessionActive && !sessionAlreadyPresenced && userLocation) {
                const distance = calculateDistance(userLocation.lat, userLocation.lng, schoolLocation.lat, schoolLocation.lng);
                presensiSekolahBtn.disabled = distance > radiusPresensi;
            } else {
                presensiSekolahBtn.disabled = true;
            }
        }
    });
    
    // Event listener untuk jenis presensi kelas
    document.getElementById('jenisPresensiKelas').addEventListener('change', function() {
        const formAlasan = document.getElementById('formAlasanKelas');
        const presensiKelasBtn = document.getElementById('presensiKelasBtn');
        const kelasSelect = document.getElementById('kelasSelect');
        const selectedKelas = kelasSelect.value;
        const infoGPSKelas = document.getElementById('infoGPSKelas');
        
        if (this.value === 'izin' || this.value === 'sakit') {
            formAlasan.classList.remove('hidden');
            // Ubah teks tombol menjadi "Submit"
            presensiKelasBtn.innerHTML = '<i class="fas fa-paper-plane"></i><span>Submit</span>';
            // Ubah info GPS
            infoGPSKelas.textContent = 'Untuk izin/sakit, tidak perlu validasi GPS';
            // Untuk izin/sakit, tidak perlu validasi lokasi - enable button jika kelas dipilih dan ada sesi
            if (selectedKelas && kelasSesi[selectedKelas]) {
                presensiKelasBtn.disabled = false;
                document.getElementById('statusKelas').textContent = 'Sesi Aktif';
                document.getElementById('statusKelas').className = 'text-green-600 font-semibold';
            }
        } else {
            formAlasan.classList.add('hidden');
            // Kembalikan teks tombol ke "Presensi Kelas"
            presensiKelasBtn.innerHTML = '<i class="fas fa-chalkboard-teacher"></i><span>Presensi Kelas</span>';
            // Kembalikan info GPS
            infoGPSKelas.textContent = 'Untuk presensi Hadir, Anda harus berada dalam radius ' + radiusPresensi + 'm dari sekolah';
            // Untuk hadir, perlu validasi lokasi - enable button hanya jika lokasi valid
            if (selectedKelas && kelasSesi[selectedKelas] && userLocation) {
                const distance = calculateDistance(userLocation.lat, userLocation.lng, schoolLocation.lat, schoolLocation.lng);
                presensiKelasBtn.disabled = distance > radiusPresensi;
                document.getElementById('statusKelas').textContent = distance <= radiusPresensi ? 'Sesi Aktif - Lokasi Valid' : 'Sesi Aktif - Lokasi Terlalu Jauh';
                document.getElementById('statusKelas').className = distance <= radiusPresensi ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold';
            } else {
                presensiKelasBtn.disabled = true;
                document.getElementById('statusKelas').textContent = !userLocation ? 'Menunggu lokasi GPS...' : 'Tidak ada sesi aktif';
                document.getElementById('statusKelas').className = 'text-yellow-600 font-semibold';
            }
        }
    });
    // Poll server for active school presensi session (auto open/close + admin override)
    function fetchSchoolSessionStatus() {
        fetch('index.php?action=get_presensi_sekolah_status')
            .then(r => r.json())
            .then(json => {
                // update global session flags and re-evaluate UI
                sessionActive = !!json.active;
                sessionAlreadyPresenced = !!json.already_presenced;
                if (json.active) {
                    const session = json.session;
                    document.getElementById('statusKelas').textContent = 'Sesi Sekolah: Aktif';
                    if (sessionAlreadyPresenced) {
                        document.getElementById('statusKelas').textContent += ' — Sudah presensi';
                    }
                } else {
                    document.getElementById('statusKelas').textContent = 'Tidak ada sesi sekolah aktif';
                }
                // Re-run local validation to enable/disable buttons correctly
                if (userLocation) updateLocationStatus();
            })
            .catch(err => console.error('Failed to fetch session status', err));
    }

    // initial fetch and poll every 15s
    fetchSchoolSessionStatus();
    setInterval(fetchSchoolSessionStatus, 15000);
    
    // Kelas select change handler
    document.getElementById('kelasSelect').addEventListener('change', function() {
        const selected = this.value;
        const presensiKelasBtn = document.getElementById('presensiKelasBtn');
        const jenisPresensi = document.getElementById('jenisPresensiKelas').value;
        const kelasDetailCard = document.getElementById('kelasDetailCard');

        if (!selected) {
            presensiKelasBtn.disabled = true;
            kelasDetailCard.classList.add('hidden');
            document.getElementById('statusKelas').textContent = 'Belum dipilih';
            document.getElementById('statusKelas').className = 'text-yellow-600 font-semibold';
            return;
        }

        // Tampilkan detail kelas yang dipilih
        const selectedOption = this.options[this.selectedIndex];
        const kelasNama = selectedOption.getAttribute('data-nama');
        const kelasTahun = selectedOption.getAttribute('data-tahun');
        const kelasWali = selectedOption.getAttribute('data-wali');
        const kelasJadwal = selectedOption.getAttribute('data-jadwal');

        document.getElementById('kelasDetailNama').textContent = kelasNama;
        document.getElementById('kelasDetailTahun').textContent = 'Tahun Ajaran: ' + kelasTahun;
        document.getElementById('kelasDetailWali').textContent = kelasWali;
        document.getElementById('kelasDetailJadwal').textContent = kelasJadwal;
        kelasDetailCard.classList.remove('hidden');

        // enable only if session active
        const sesiAktif = kelasSesi[selected] || false;
        if (!sesiAktif) {
            presensiKelasBtn.disabled = true;
            document.getElementById('statusKelas').textContent = 'Tidak ada sesi aktif';
            document.getElementById('statusKelas').className = 'text-red-600 font-semibold';
            return;
        }

        // Jika izin/sakit, tidak perlu validasi lokasi
        if (jenisPresensi === 'izin' || jenisPresensi === 'sakit') {
            presensiKelasBtn.disabled = false;
            document.getElementById('statusKelas').textContent = 'Sesi Aktif';
            document.getElementById('statusKelas').className = 'text-green-600 font-semibold';
        } else {
            // Untuk hadir, harus ada lokasi dan dalam radius
            if (!userLocation) {
                presensiKelasBtn.disabled = true;
                document.getElementById('statusKelas').textContent = 'Menunggu lokasi GPS...';
                document.getElementById('statusKelas').className = 'text-yellow-600 font-semibold';
            } else {
                const distance = calculateDistance(userLocation.lat, userLocation.lng, schoolLocation.lat, schoolLocation.lng);
                presensiKelasBtn.disabled = distance > radiusPresensi;
                document.getElementById('statusKelas').textContent = distance <= radiusPresensi ? 'Sesi Aktif - Lokasi Valid' : 'Sesi Aktif - Lokasi Terlalu Jauh';
                document.getElementById('statusKelas').className = distance <= radiusPresensi ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold';
            }
        }
    });
});

// Handle page visibility change
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        getUserLocation(); // Refresh location when page becomes visible
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>