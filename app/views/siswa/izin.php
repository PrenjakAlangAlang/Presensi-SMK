<?php
$page_title = "Ajukan Izin";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Form Pengajuan Izin -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Ajukan Izin</h2>
        <p class="text-gray-600 mb-6">Formulir pengajuan izin tidak hadir</p>
        
        <form method="POST" action="index.php?action=ajukan_izin" id="izinForm">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Izin</label>
                    <input type="date" name="tanggal" required 
                           min="<?php echo date('Y-m-d'); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Izin</label>
                    <select name="jenis_izin" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        <option value="">Pilih Jenis Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="izin">Izin</option>
                        <option value="acara_keluarga">Acara Keluarga</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan</label>
                    <textarea name="alasan" rows="4" required 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition resize-none"
                              placeholder="Jelaskan alasan izin secara detail..."></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bukti (Opsional)</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3"></i>
                        <p class="text-gray-600 mb-2">Upload bukti izin</p>
                        <p class="text-sm text-gray-500 mb-3">Format: JPG, PNG, PDF (Maks. 2MB)</p>
                        <input type="file" name="bukti" 
                               class="hidden" id="fileInput"
                               accept=".jpg,.jpeg,.png,.pdf">
                        <button type="button" onclick="document.getElementById('fileInput').click()" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Pilih File
                        </button>
                    </div>
                    <div id="fileName" class="text-sm text-gray-600 mt-2 hidden"></div>
                </div>
                
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h4 class="font-medium text-blue-800 mb-2">Perhatian</h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Pengajuan izin harus diajukan maksimal H-1</li>
                        <li>• Izin akan diverifikasi oleh wali kelas</li>
                        <li>• Status pengajuan dapat dilihat di riwayat</li>
                        <li>• Untuk keadaan darurat, hubungi wali kelas langsung</li>
                    </ul>
                </div>
                
                <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center space-x-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Ajukan Izin</span>
                </button>
            </div>
        </form>
    </div>
    
    <!-- Riwayat Pengajuan Izin -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Riwayat Pengajuan</h2>
        <p class="text-gray-600 mb-6">Daftar pengajuan izin yang telah diajukan</p>
        
        <div class="space-y-4">
            <?php if(!empty($riwayatIzin)): ?>
                <?php foreach($riwayatIzin as $izin): ?>
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="font-semibold text-gray-800">
                                <?php echo date('d M Y', strtotime($izin->tanggal)); ?>
                            </h3>
                            <p class="text-sm text-gray-600 capitalize">
                                <?php echo $izin->alasan; ?>
                            </p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            <?php echo $izin->status == 'disetujui' ? 'bg-green-100 text-green-800' : 
                                   ($izin->status == 'ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                            <?php echo $izin->status == 'disetujui' ? 'Disetujui' : 
                                  ($izin->status == 'ditolak' ? 'Ditolak' : 'Menunggu'); ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-sm text-gray-500">
                        <span>Diajukan: <?php echo date('d M Y H:i', strtotime($izin->waktu_pengajuan)); ?></span>
                        <span class="capitalize"><?php echo $izin->jenis_izin ?? 'izin'; ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Belum ada pengajuan izin</p>
                    <p class="text-sm text-gray-400">Ajukan izin pertama Anda menggunakan form di samping</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Statistik Izin -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h4 class="font-semibold text-gray-800 mb-3">Statistik Izin</h4>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <div class="text-2xl font-bold text-blue-600">
                        <?php 
                        $totalIzin = count($riwayatIzin);
                        echo $totalIzin;
                        ?>
                    </div>
                    <div class="text-sm text-gray-600">Total</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-green-600">
                        <?php 
                        $disetujui = 0;
                        foreach($riwayatIzin as $izin) {
                            if($izin->status == 'disetujui') $disetujui++;
                        }
                        echo $disetujui;
                        ?>
                    </div>
                    <div class="text-sm text-gray-600">Disetujui</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-yellow-600">
                        <?php 
                        $pending = 0;
                        foreach($riwayatIzin as $izin) {
                            if($izin->status == 'pending') $pending++;
                        }
                        echo $pending;
                        ?>
                    </div>
                    <div class="text-sm text-gray-600">Pending</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl p-6 max-w-sm mx-4">
        <div class="text-center mb-4">
            <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Izin Berhasil Diajukan!</h3>
            <p class="text-gray-600">Pengajuan izin Anda telah dikirim dan sedang menunggu persetujuan.</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="closeConfirmModal()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors">
                Oke
            </button>
        </div>
    </div>
</div>

<script>
// File input handling
document.getElementById('fileInput').addEventListener('change', function(e) {
    const fileName = document.getElementById('fileName');
    if (this.files.length > 0) {
        fileName.textContent = 'File terpilih: ' + this.files[0].name;
        fileName.classList.remove('hidden');
    } else {
        fileName.classList.add('hidden');
    }
});

// Form submission handling
document.getElementById('izinForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Basic validation
    const tanggal = document.querySelector('input[name="tanggal"]').value;
    const jenisIzin = document.querySelector('select[name="jenis_izin"]').value;
    const alasan = document.querySelector('textarea[name="alasan"]').value;
    
    if (!tanggal || !jenisIzin || !alasan) {
        showNotification('error', 'Harap lengkapi semua field yang wajib diisi!');
        return;
    }
    
    // Check if date is not in the past
    const selectedDate = new Date(tanggal);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if (selectedDate < today) {
        showNotification('error', 'Tanggal izin tidak boleh di masa lalu!');
        return;
    }
    
    // Show confirmation modal
    showConfirmModal();
});

function showConfirmModal() {
    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    
    // Reset form
    document.getElementById('izinForm').reset();
    document.getElementById('fileName').classList.add('hidden');
    
    // Show success notification
    showNotification('success', 'Izin berhasil diajukan! Status dapat dilihat di riwayat.');
}

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
    }, 5000);
}

// Close modal when clicking outside
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmModal();
    }
});

// Add some interactivity to the form
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.querySelector('input[name="tanggal"]').min = today;
    
    // Add character counter for alasan textarea
    const alasanTextarea = document.querySelector('textarea[name="alasan"]');
    const counter = document.createElement('div');
    counter.className = 'text-sm text-gray-500 text-right mt-1';
    counter.textContent = '0/500 karakter';
    alasanTextarea.parentNode.appendChild(counter);
    
    alasanTextarea.addEventListener('input', function() {
        const length = this.value.length;
        counter.textContent = length + '/500 karakter';
        
        if (length > 500) {
            counter.classList.add('text-red-500');
        } else {
            counter.classList.remove('text-red-500');
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>