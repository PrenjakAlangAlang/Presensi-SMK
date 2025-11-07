<?php
$page_title = "Manajemen Presensi Sekolah";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Presensi Sekolah — Sesi (Auto + Manual)</h2>

    <div class="mb-6">
        <form id="createSessionForm" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <input type="datetime-local" name="waktu_buka" required class="p-2 border rounded" />
            <input type="datetime-local" name="waktu_tutup" required class="p-2 border rounded" />
            <div class="flex space-x-2">
                <input type="text" name="note" placeholder="Catatan (opsional)" class="p-2 border rounded flex-1" />
                <button class="bg-blue-600 text-white px-4 py-2 rounded" type="submit">Buat Sesi Manual</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg border p-4">
        <h3 class="font-semibold mb-3">Daftar Sesi (terbaru)</h3>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-gray-600">
                    <th>ID</th>
                    <th>Waktu Buka</th>
                    <th>Waktu Tutup</th>
                    <th>Manual?</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($sessions as $s): ?>
                <tr class="border-t">
                    <td><?php echo $s->id; ?></td>
                    <td><?php echo $s->waktu_buka; ?></td>
                    <td><?php echo $s->waktu_tutup; ?></td>
                    <td><?php echo $s->is_manual ? 'Ya' : 'Auto'; ?></td>
                    <td><?php echo $s->status; ?></td>
                    <td>
                        <?php if($s->status == 'open'): ?>
                            <button data-id="<?php echo $s->id; ?>" class="close-btn bg-red-500 text-white px-2 py-1 rounded">Tutup</button>
                        <?php endif; ?>
                        <button data-id="<?php echo $s->id; ?>" class="extend-btn bg-yellow-500 text-white px-2 py-1 rounded">Perpanjang</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('createSessionForm').addEventListener('submit', function(e){
    e.preventDefault();
    const fd = new FormData(this);
    fetch('index.php?action=admin_create_presensi_sekolah', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(json => {
            if (json.success) location.reload();
            else alert('Gagal membuat sesi');
        });
});

document.querySelectorAll('.close-btn').forEach(b => b.addEventListener('click', function(){
    const id = this.dataset.id;
    const fd = new FormData(); fd.append('id', id);
    fetch('index.php?action=admin_close_presensi_sekolah', { method: 'POST', body: fd })
        .then(r => r.json()).then(json => { if (json.success) location.reload(); else alert('Gagal menutup sesi'); });
}));

document.querySelectorAll('.extend-btn').forEach(b => b.addEventListener('click', function(){
    const id = this.dataset.id;
    const newTutup = prompt('Masukkan waktu tutup baru (YYYY-MM-DD HH:MM:SS)', '');
    if (!newTutup) return;
    const fd = new FormData(); fd.append('id', id); fd.append('waktu_tutup', newTutup);
    fetch('index.php?action=admin_extend_presensi_sekolah', { method: 'POST', body: fd })
        .then(r => r.json()).then(json => { if (json.success) location.reload(); else alert('Gagal perpanjang'); });
}));
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
