<?php
$page_title = "Dashboard Siswa";
require_once __DIR__ . '/../layouts/header.php';

$hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
$jadwalByHari = array_fill_keys($hariList, []);

foreach ($kelas ?? [] as $item) {
    $hari = $item->hari ?? null;
    if ($hari && isset($jadwalByHari[$hari])) {
        $jadwalByHari[$hari][] = $item;
    }
}
?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
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
            <?php if($presensiHariIni): ?>
                <div class="text-3xl font-bold text-gray-800 mb-2"><?php echo date('H:i', strtotime($presensiHariIni->waktu)); ?></div>
                <?php 
                $jenis = $presensiHariIni->jenis ?? 'hadir';
                $status = $presensiHariIni->status ?? 'valid';
                
                // Determine badge styling based on type and status
                if($jenis == 'hadir' && $status == 'valid') {
                    $badgeClass = 'bg-green-100 text-green-800';
                    $icon = 'fa-check-circle';
                    $label = 'Hadir - Valid';
                } elseif($jenis == 'hadir' && $status == 'invalid') {
                    $badgeClass = 'bg-red-100 text-red-800';
                    $icon = 'fa-exclamation-circle';
                    $label = 'Hadir - Lokasi Invalid';
                } elseif($jenis == 'izin') {
                    $badgeClass = 'bg-yellow-100 text-yellow-800';
                    $icon = 'fa-envelope';
                    $label = 'Izin';
                } elseif($jenis == 'sakit') {
                    $badgeClass = 'bg-orange-100 text-orange-800';
                    $icon = 'fa-first-aid';
                    $label = 'Sakit';
                } elseif($jenis == 'alpha') {
                    $badgeClass = 'bg-gray-100 text-gray-800';
                    $icon = 'fa-times-circle';
                    $label = 'Alpha';
                } else {
                    $badgeClass = 'bg-gray-100 text-gray-600';
                    $icon = 'fa-question-circle';
                    $label = ucfirst($jenis);
                }
                ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $badgeClass; ?>">
                    <i class="fas <?php echo $icon; ?> mr-1"></i>
                    <?php echo $label; ?>
                </span>
                <?php if($presensiHariIni->alasan): ?>
                    <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($presensiHariIni->alasan); ?></p>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-3xl font-bold text-gray-800 mb-2">-</div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    <i class="fas fa-clock mr-1"></i>
                    Belum Presensi
                </span>
            <?php endif; ?>
        </div>
        <?php if(!$presensiHariIni): ?>
            <a href="index.php?action=siswa_presensi" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg transition-colors block">
                <i class="fas fa-fingerprint mr-2"></i>Lakukan Presensi
            </a>
        <?php else: ?>
            <a href="index.php?action=siswa_riwayat" class="w-full bg-gray-600 hover:bg-gray-700 text-white text-center py-2 px-4 rounded-lg transition-colors block">
                <i class="fas fa-history mr-2"></i>Lihat Riwayat
            </a>
        <?php endif; ?>
    </div>

    
</div>

<div class="grid grid-cols-1 gap-8">
    <!-- Mata Pelajaran Aktif -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-book text-purple-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Jadwal Mata Pelajaran</h3>
                <p class="text-gray-600 text-sm">Dikelompokkan berdasarkan hari</p>
            </div>
        </div>

        <?php if(!empty($kelas)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <?php foreach($hariList as $hari): ?>
                    <?php $jadwalHari = $jadwalByHari[$hari] ?? []; ?>
                    <section class="border border-gray-200 rounded-lg overflow-hidden bg-white">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                            <h4 class="font-semibold text-gray-800"><?php echo $hari; ?></h4>
                            <span class="text-xs font-medium px-2 py-1 rounded-full <?php echo !empty($jadwalHari) ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-500'; ?>">
                                <?php echo count($jadwalHari); ?> mapel
                            </span>
                        </div>

                        <div class="divide-y divide-gray-100">
                            <?php if(!empty($jadwalHari)): ?>
                                <?php foreach($jadwalHari as $jadwal): ?>
                                    <?php
                                        $jamMulai = !empty($jadwal->jam_mulai) ? date('H:i', strtotime($jadwal->jam_mulai)) : '--:--';
                                        $jamSelesai = !empty($jadwal->jam_selesai) ? date('H:i', strtotime($jadwal->jam_selesai)) : '--:--';
                                    ?>
                                    <div class="p-4 flex gap-3">
                                        <div class="w-20 shrink-0 text-sm font-semibold text-purple-700">
                                            <?php echo $jamMulai; ?><br>
                                            <span class="text-xs font-normal text-gray-500"><?php echo $jamSelesai; ?></span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-medium text-gray-800 leading-snug"><?php echo htmlspecialchars($jadwal->nama_mata_pelajaran ?? '-'); ?></p>
                                            <div class="mt-2 flex flex-wrap gap-2 text-xs text-gray-600">
                                                <?php if(!empty($jadwal->ruang)): ?>
                                                    <span class="inline-flex items-center px-2 py-1 bg-gray-100 rounded">
                                                        <i class="fas fa-door-open mr-1 text-gray-500"></i><?php echo htmlspecialchars($jadwal->ruang); ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if(!empty($jadwal->guru_pengampu_nama)): ?>
                                                    <span class="inline-flex items-center px-2 py-1 bg-gray-100 rounded">
                                                        <i class="fas fa-user-tie mr-1 text-gray-500"></i><?php echo htmlspecialchars($jadwal->guru_pengampu_nama); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="p-4 text-sm text-gray-400 text-center">Tidak ada jadwal</div>
                            <?php endif; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-info-circle mb-2"></i>
                <p class="text-sm">Anda belum terdaftar di mata pelajaran manapun</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

