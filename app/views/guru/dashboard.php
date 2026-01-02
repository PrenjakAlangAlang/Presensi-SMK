<?php
$page_title = "Dashboard Guru";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Dashboard Guru</h2>
    <p class="text-gray-600">Monitor kelas dan presensi siswa</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Statistik Cards -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Kelas</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo count($kelasSaya); ?></h3>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chalkboard text-blue-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <span class="text-green-600 text-sm font-medium">Aktif</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Siswa</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $totalSiswa; ?></h3>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-green-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <span class="text-green-600 text-sm font-medium">Terdaftar</span>
        </div>
    </div>

    

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Presensi Aktif</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $presensiAktif; ?></h3>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-purple-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <span class="<?php echo $presensiAktif > 0 ? 'text-green-600' : 'text-gray-600'; ?> text-sm font-medium"><?php echo $presensiAktif > 0 ? 'Kelas dibuka' : 'Tidak ada kelas aktif'; ?></span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-1 gap-8 mb-8">
    <!-- Kelas Saya -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Kelas Saya</h3>
            <a href="index.php?action=guru_kelas" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Lihat Semua →
            </a>
        </div>
        <div class="space-y-4">
            <?php foreach(array_slice($kelasSaya, 0, 3) as $kelas): ?>
            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chalkboard text-blue-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800"><?php echo $kelas->nama_kelas; ?></h4>
                        <p class="text-sm text-gray-600"><?php echo $kelas->tahun_ajaran; ?></p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        Aktif
                    </span>
                    <p class="text-sm text-gray-600 mt-1"><?php echo count($this->kelasModel->getSiswaInKelas($kelas->id)); ?> siswa</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<!-- Aktivitas Terbaru -->
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Presensi Terbaru</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Siswa</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Kelas</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Waktu</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Lokasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if(!empty($aktivitasTerbaru)): ?>
                    <?php foreach($aktivitasTerbaru as $aktivitas): ?>
                    <tr>
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                                <span class="font-medium text-gray-800"><?php echo htmlspecialchars($aktivitas->nama ?? 'Siswa'); ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600"><?php echo htmlspecialchars($aktivitas->nama_kelas ?? '-'); ?></td>
                        <td class="px-4 py-3 text-gray-600">
                            <?php echo $aktivitas->waktu ? date('H:i', strtotime($aktivitas->waktu)) : '-'; ?>
                        </td>
                        <td class="px-4 py-3">
                            <?php 
                            $jenis = $aktivitas->jenis ?? 'hadir';
                            $badgeClass = 'bg-green-100 text-green-800';
                            $label = 'Hadir';
                            
                            if($jenis == 'izin') {
                                $badgeClass = 'bg-yellow-100 text-yellow-800';
                                $label = 'Izin';
                            } elseif($jenis == 'sakit') {
                                $badgeClass = 'bg-orange-100 text-orange-800';
                                $label = 'Sakit';
                            } elseif($jenis == 'alpha') {
                                $badgeClass = 'bg-gray-100 text-gray-800';
                                $label = 'Alpha';
                            }
                            ?>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo $badgeClass; ?>">
                                <?php echo $label; ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            <?php 
                            // Jika izin atau sakit, tidak ada validasi lokasi
                            if(isset($aktivitas->jenis) && ($aktivitas->jenis == 'izin' || $aktivitas->jenis == 'sakit' || $aktivitas->jenis == 'alpha')): 
                            ?>
                                <span class="text-gray-400">-</span>
                            <?php elseif($aktivitas->status == 'valid'): ?>
                                <span class="inline-flex items-center text-green-600 text-xs">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Valid
                                </span>
                            <?php elseif($aktivitas->status == 'invalid'): ?>
                                <span class="inline-flex items-center text-red-600 text-xs">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Invalid
                                </span>
                            <?php else: ?>
                                <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-2 text-gray-300"></i>
                            <p>Belum ada aktivitas presensi hari ini</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>


</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>