<?php
$page_title = "Manajemen User";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Manajemen User</h2>
        <p class="text-gray-600">Kelola data pengguna sistem</p>
    </div>
    <button onclick="openAddUserModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
        <i class="fas fa-plus"></i>
        <span>Tambah User</span>
    </button>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Nama</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Email</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Role</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Tanggal Dibuat</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach($users as $user): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-blue-600 text-sm"></i>
                            </div>
                            <span class="font-medium text-gray-800"><?php echo htmlspecialchars($user->nama); ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($user->email); ?></td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            <?php echo $user->role == 'admin' ? 'bg-red-100 text-red-800' : 
                                   ($user->role == 'admin_kesiswaan' ? 'bg-purple-100 text-purple-800' :
                                   ($user->role == 'guru' ? 'bg-green-100 text-green-800' : 
                                   ($user->role == 'siswa' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800'))); ?>">
                            <?php echo ucfirst($user->role); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600"><?php echo date('d M Y', strtotime($user->created_at)); ?></td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <button 
                                data-id="<?php echo $user->id; ?>" 
                                data-nama="<?php echo htmlspecialchars($user->nama, ENT_QUOTES); ?>" 
                                data-email="<?php echo htmlspecialchars($user->email, ENT_QUOTES); ?>" 
                                data-role="<?php echo $user->role; ?>"
                                onclick="openEditUserModal(this)" 
                                class="text-blue-600 hover:text-blue-800 transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteUser(<?php echo $user->id; ?>)" class="text-red-600 hover:text-red-800 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah User -->
<div id="addUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Tambah User Baru</h3>
        </div>
        <form method="POST" action="index.php?action=admin_create_user">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="nama" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        <option value="">Pilih Role</option>
                        <option value="admin">Admin</option>
                        <option value="guru">Guru</option>
                        <option value="admin_kesiswaan">Admin Kesiswaan</option>
                        <option value="siswa">Siswa</option>
                    </select>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeAddUserModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Batal
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit User -->
<div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Edit User</h3>
        </div>
        <form method="POST" action="index.php?action=admin_update_user">
            <input type="hidden" name="id" id="edit_user_id">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="nama" id="edit_user_nama" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="edit_user_email" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                    <input type="password" name="password" id="edit_user_password" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Kosongkan jika tidak ingin mengubah password">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role" id="edit_user_role" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        <option value="">Pilih Role</option>
                        <option value="admin">Admin</option>
                        <option value="guru">Guru</option>
                        <option value="admin_kesiswaan">Admin Kesiswaan</option>
                        <option value="siswa">Siswa</option>
                    </select>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeEditUserModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Batal
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddUserModal() {
    document.getElementById('addUserModal').classList.remove('hidden');
}

function closeAddUserModal() {
    document.getElementById('addUserModal').classList.add('hidden');
}

function openEditUserModal(button) {
    var id = button.getAttribute('data-id');
    var nama = button.getAttribute('data-nama');
    var email = button.getAttribute('data-email');
    var role = button.getAttribute('data-role');

    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_user_nama').value = nama;
    document.getElementById('edit_user_email').value = email;
    document.getElementById('edit_user_role').value = role;

    document.getElementById('editUserModal').classList.remove('hidden');
}

function closeEditUserModal() {
    document.getElementById('editUserModal').classList.add('hidden');
}

function deleteUser(userId) {
    if (confirm('Apakah Anda yakin ingin menghapus user ini? Data user akan dihapus permanen.')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?action=admin_delete_user';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = userId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modal when clicking outside
document.getElementById('addUserModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddUserModal();
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>