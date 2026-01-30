<?php
$page_title = "Lokasi Sekolah";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Lokasi Sekolah</h2>
    <p class="text-gray-600">Atur koordinat GPS dan radius presensi sekolah</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Form Pengaturan Lokasi -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Pengaturan Lokasi</h3>
        
        <form method="POST" action="index.php?action=admin_update_lokasi">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Sekolah</label>
                    <input type="text" name="nama_sekolah" required 
                           value="<?php echo htmlspecialchars($lokasi->nama_sekolah ?? 'SMK Negeri 7 Yogyakarta'); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                        <input type="number" step="any" name="latitude" required 
                               value="<?php echo $lokasi->latitude ?? DEFAULT_LATITUDE; ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                        <input type="number" step="any" name="longitude" required 
                               value="<?php echo $lokasi->longitude ?? DEFAULT_LONGITUDE; ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Radius Presensi (meter)
                    </label>
                    <input type="number" name="radius_presensi" required 
                           value="<?php echo $lokasi->radius_presensi ?? MAX_RADIUS; ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           min="50" max="500">
                    <!--<p class="text-sm text-gray-500 mt-1">Rentang: 50 - 500 meter</p>-->
                </div>
                   
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300">
                    <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
    
    <!-- Peta dan Preview -->
    <div class="space-y-6">
        <!-- Peta Interaktif -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Peta Lokasi</h3>
            <div id="map" class="h-80 rounded-lg border border-gray-300" style="position:relative; z-index:0;"></div>
            <p class="text-sm text-gray-600 mt-2 text-center">
                Klik pada peta untuk mengatur koordinat sekolah
            </p>
        </div>
        
        <!-- Informasi Radius -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Preview Radius</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Radius Saat Ini:</span>
                    <span class="font-semibold text-blue-600"><?php echo $lokasi->radius_presensi ?? MAX_RADIUS; ?> meter</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Koordinat:</span>
                    <span class="font-mono text-sm text-gray-800">
                        <?php echo $lokasi->latitude ?? DEFAULT_LATITUDE; ?>, <?php echo $lokasi->longitude ?? DEFAULT_LONGITUDE; ?>
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: 100%"></div>
                </div>
                <div class="text-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i>
                        Lokasi Valid
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat Perubahan -->
<div class="mt-8 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Perubahan Lokasi</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Waktu</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Koordinat</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Radius</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Diubah Oleh</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if(isset($riwayat_lokasi) && count($riwayat_lokasi) > 0): ?>
                    <?php foreach($riwayat_lokasi as $riwayat): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-gray-600"><?php echo isset($riwayat->created_at) ? date('d M Y H:i', strtotime($riwayat->created_at)) : date('d M Y H:i'); ?></td>
                        <td class="px-4 py-3 text-gray-600">
                            <span class="font-mono text-sm"><?php echo $riwayat->latitude; ?>, <?php echo $riwayat->longitude; ?></span>
                        </td>
                        <td class="px-4 py-3 text-gray-600"><?php echo $riwayat->radius_presensi; ?> meter</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                                <span class="text-gray-800"><?php echo htmlspecialchars($riwayat->updated_by_nama ?? 'System'); ?></span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                            <i class="fas fa-info-circle mr-2"></i>
                            Belum ada riwayat perubahan lokasi
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
let map, marker, circle;
const defaultLat = <?php echo $lokasi->latitude ?? DEFAULT_LATITUDE; ?>;
const defaultLng = <?php echo $lokasi->longitude ?? DEFAULT_LONGITUDE; ?>;
const defaultRadius = <?php echo $lokasi->radius_presensi ?? MAX_RADIUS; ?>;

// Initialize map
function initMap() {
    map = L.map('map').setView([defaultLat, defaultLng], 16);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Add marker
    marker = L.marker([defaultLat, defaultLng], {
        draggable: true
    }).addTo(map);
    
    // Add circle for radius
    circle = L.circle([defaultLat, defaultLng], {
        color: 'blue',
        fillColor: '#3b82f6',
        fillOpacity: 0.2,
        radius: defaultRadius
    }).addTo(map);
    
    // Bind popup to marker
    marker.bindPopup(`
        <div class="text-center">
            <strong>Lokasi Sekolah</strong><br>
            <small>Drag untuk mengubah posisi</small>
        </div>
    `).openPopup();
    
    // Update form when marker is dragged
    marker.on('dragend', function(e) {
        const position = marker.getLatLng();
        updateFormCoordinates(position.lat, position.lng);
        updateCirclePosition(position.lat, position.lng);
    });
    
    // Update coordinates when clicking on map
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateFormCoordinates(e.latlng.lat, e.latlng.lng);
        updateCirclePosition(e.latlng.lat, e.latlng.lng);
    });
}

function updateFormCoordinates(lat, lng) {
    document.querySelector('input[name="latitude"]').value = lat.toFixed(6);
    document.querySelector('input[name="longitude"]').value = lng.toFixed(6);
}

function updateCirclePosition(lat, lng) {
    circle.setLatLng([lat, lng]);
}

// Update radius when input changes
document.querySelector('input[name="radius_presensi"]').addEventListener('input', function(e) {
    const newRadius = parseInt(e.target.value);
    if (newRadius >= 50 && newRadius <= 500) {
        circle.setRadius(newRadius);
    }
});

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    initMap();
});

// Prevent page from scrolling when the user scrolls over the map element
;(function(){
    var mapEl = document.getElementById('map');
    if(!mapEl) return;

    // Prevent wheel events from scrolling the page while over the map
    mapEl.addEventListener('wheel', function(e){
        // Allow Leaflet to handle zooming but stop the wheel from bubbling to the page
        e.preventDefault();
    }, { passive: false });

    // On touch devices, prevent touchmove from causing the page to scroll when touching the map
    mapEl.addEventListener('touchmove', function(e){
        // don't block map panning; stop propagation so page doesn't also scroll
        e.stopPropagation();
    }, { passive: true });
})();

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const radius = parseInt(document.querySelector('input[name="radius_presensi"]').value);
    
    if (radius < 50 || radius > 500) {
        e.preventDefault();
        showNotification('error', 'Radius harus antara 50 - 500 meter!');
        return;
    }
    
    showNotification('success', 'Pengaturan lokasi berhasil disimpan!');
});

function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas fa-${type === 'success' ? 'check' : 'exclamation-triangle'}-circle"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>