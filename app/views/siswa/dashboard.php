<?php
$page_title = "Dashboard Siswa";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Statistik Kehadiran -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Kehadiran Bulan Ini</h3>
                <p class="text-gray-600 text-sm">Statistik presensi</p>
            </div>
        </div>
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Hadir:</span>
                <span class="font-semibold text-green-600"><?php echo $statistik->hadir ?? '0'; ?> hari</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Izin:</span>
                <span class="font-semibold text-yellow-600"><?php echo $statistik->izin ?? '0'; ?> hari</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Sakit:</span>
                <span class="font-semibold text-orange-600"><?php echo $statistik->sakit ?? '0'; ?> hari</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Alpha:</span>
                <span class="font-semibold text-red-600"><?php echo $statistik->alpha ?? '0'; ?> hari</span>
            </div>
        </div>
    </div>

    <!-- Presensi Hari Ini -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-fingerprint text-green-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Presensi Hari Ini</h3>
                <p class="text-gray-600 text-sm"><?php echo date('d F Y'); ?></p>
            </div>
        </div>
        <div class="text-center py-4">
            <div class="text-3xl font-bold text-gray-800 mb-2">-</div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                <i class="fas fa-clock mr-1"></i>
                Belum Presensi
            </span>
        </div>
        <a href="index.php?action=siswa_presensi" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg transition-colors block">
            <i class="fas fa-fingerprint mr-2"></i>Lakukan Presensi
        </a>
    </div>

    <!-- Kelas Aktif -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chalkboard text-purple-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Kelas Aktif</h3>
                <p class="text-gray-600 text-sm">Jadwal hari ini</p>
            </div>
        </div>
        <div class="space-y-3">
            <?php foreach($kelas as $k): ?>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-800"><?php echo $k->nama_kelas; ?></p>
                    <p class="text-sm text-gray-600">08:00 - 09:30</p>
                </div>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Aktif
                </span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Riwayat Presensi Terakhir -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Presensi Terakhir</h3>
        <div class="space-y-3">
            <?php foreach($presensiTerakhir as $presensi): ?>
            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-school text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Presensi Sekolah</p>
                        <p class="text-sm text-gray-600"><?php echo date('d M Y H:i', strtotime($presensi->waktu)); ?></p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                    <?php echo $presensi->status == 'valid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                    <?php echo $presensi->status == 'valid' ? 'Valid' : 'Invalid'; ?>
                </span>
            </div>
            <?php endforeach; ?>
        </div>
        <a href="index.php?action=siswa_riwayat" class="block text-center mt-4 text-blue-600 hover:text-blue-800 transition-colors">
            Lihat Semua Riwayat →
        </a>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 gap-4">
            <a href="index.php?action=siswa_presensi" class="p-4 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 text-center transition-colors">
                <i class="fas fa-fingerprint text-blue-600 text-2xl mb-2"></i>
                <p class="text-sm font-medium text-blue-800">Presensi</p>
            </a>
            <a href="index.php?action=siswa_riwayat" class="p-4 bg-green-50 hover:bg-green-100 rounded-lg border border-green-200 text-center transition-colors">
                <i class="fas fa-history text-green-600 text-2xl mb-2"></i>
                <p class="text-sm font-medium text-green-800">Riwayat</p>
            </a>
            <a href="index.php?action=siswa_buku_induk" class="p-4 bg-orange-50 hover:bg-orange-100 rounded-lg border border-orange-200 text-center transition-colors">
                <i class="fas fa-id-card text-orange-600 text-2xl mb-2"></i>
                <p class="text-sm font-medium text-orange-800">Buku Induk</p>
            </a>
            <a href="#" class="p-4 bg-purple-50 hover:bg-purple-100 rounded-lg border border-purple-200 text-center transition-colors">
                <i class="fas fa-user text-purple-600 text-2xl mb-2"></i>
                <p class="text-sm font-medium text-purple-800">Profil</p>
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>