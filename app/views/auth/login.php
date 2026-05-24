<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Presensi SMK Negeri 7 Yogyakarta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <style>
.login-bg {
    position: relative;
    background-color: #f0f4f8;
    min-height: 100vh;
}
.login-bg::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: url('https://file.data.kemendikdasmen.go.id/sekolahkita/20/2040/20403295-2.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    filter: blur(3px);
    -webkit-filter: blur(3px);
    z-index: 0;
}
.login-bg > * {
    position: relative;
    z-index: 1;
}
</style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 hover:scale-105">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 py-8 px-8 text-white text-center">
            <div class="flex items-center justify-center space-x-4 mb-4">
                <img src="<?php echo BASE_URL; ?>/public/assets/images/logo.png" alt="Logo SMK Negeri 7" class="w-12 h-12 object-cover">
                <div class="text-left">
                    <h1 class="text-3xl font-bold">SMK Negeri 7</h1>
                    <p class="text-blue-100">Yogyakarta</p>
                </div>
            </div>
            <p class="text-blue-100 mt-2">Sistem Presensi Digital Berbasis Geotagging dan Algoritma Haversine</p>
        </div>
        
        <div class="p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2 text-center">Masuk ke Akun</h2>
            <p class="text-gray-600 mb-6 text-center">Silakan login dengan kredensial Anda</p>
            
            <?php if(isset($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?php echo $success; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 animate-pulse">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?php echo $error; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-2 gap-2 mb-6">
                <button type="button" id="loginTab" class="py-2 rounded-lg bg-blue-600 text-white font-medium">Login</button>
                <button type="button" id="registerTab" class="py-2 rounded-lg bg-gray-100 text-gray-700 font-medium">Daftar Siswa</button>
            </div>
            
            <form method="POST" action="<?php echo BASE_URL; ?>/index.php?action=login" id="loginForm" autocomplete="on" class="<?php echo !empty($showRegister) ? 'hidden' : ''; ?>">
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-medium mb-2" for="email">
                        <i class="fas fa-user mr-2 text-blue-500"></i>Email / NIPD
                    </label>
                    <input type="text" id="email" name="email" required autocomplete="username"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-300"
                           placeholder="admin/guru: email, siswa: email atau NIPD"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-medium mb-2" for="password">
                        <i class="fas fa-lock mr-2 text-blue-500"></i>Password
                    </label>
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-300"
                           placeholder="masukkan password Anda">
                </div>
                
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>Masuk ke Sistem
                </button>
            </form>

            <form method="POST" action="<?php echo BASE_URL; ?>/index.php?action=register_siswa" id="registerForm" autocomplete="off" class="<?php echo empty($showRegister) ? 'hidden' : ''; ?>">
                <div class="grid grid-cols-1 gap-4">
                    <input type="text" name="nama" required autocomplete="off" placeholder="Nama lengkap" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" value="<?php echo htmlspecialchars($_POST['nama'] ?? '', ENT_QUOTES); ?>">
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" name="nipd" required inputmode="numeric" autocomplete="off" placeholder="NIPD" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" value="<?php echo htmlspecialchars($_POST['nipd'] ?? '', ENT_QUOTES); ?>">
                        <input type="text" name="nisn" required inputmode="numeric" autocomplete="off" placeholder="NISN" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" value="<?php echo htmlspecialchars($_POST['nisn'] ?? '', ENT_QUOTES); ?>">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="password" name="password" required minlength="6" autocomplete="new-password" placeholder="Password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <input type="password" name="password_confirm" required minlength="6" autocomplete="new-password" placeholder="Konfirmasi password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" name="tempat_lahir" required autocomplete="off" placeholder="Tempat lahir" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" value="<?php echo htmlspecialchars($_POST['tempat_lahir'] ?? '', ENT_QUOTES); ?>">
                        <input type="date" name="tanggal_lahir" required autocomplete="off" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" value="<?php echo htmlspecialchars($_POST['tanggal_lahir'] ?? '', ENT_QUOTES); ?>">
                    </div>
                    <textarea name="alamat" rows="3" required autocomplete="off" placeholder="Alamat" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"><?php echo htmlspecialchars($_POST['alamat'] ?? '', ENT_QUOTES); ?></textarea>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" name="nama_ayah" autocomplete="off" placeholder="Nama ayah" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" value="<?php echo htmlspecialchars($_POST['nama_ayah'] ?? '', ENT_QUOTES); ?>">
                        <input type="text" name="nama_ibu" autocomplete="off" placeholder="Nama ibu" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" value="<?php echo htmlspecialchars($_POST['nama_ibu'] ?? '', ENT_QUOTES); ?>">
                    </div>
                    <input type="text" name="nama_wali" autocomplete="off" placeholder="Nama wali (opsional)" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" value="<?php echo htmlspecialchars($_POST['nama_wali'] ?? '', ENT_QUOTES); ?>">
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" name="no_telp_ortu" inputmode="tel" autocomplete="off" placeholder="No. telp orang tua" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" value="<?php echo htmlspecialchars($_POST['no_telp_ortu'] ?? '', ENT_QUOTES); ?>">
                        <input type="email" name="email_ortu" autocomplete="off" placeholder="Email orang tua" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" value="<?php echo htmlspecialchars($_POST['email_ortu'] ?? '', ENT_QUOTES); ?>">
                    </div>
                    <p class="text-xs text-gray-500">Password siswa disimpan aman sebagai hash di data buku induk.</p>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300 shadow-lg">
                        <i class="fas fa-user-plus mr-2"></i>Daftar sebagai Siswa
                    </button>
                </div>
            </form>
            <!--
            <div class="mt-8 p-6 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-blue-200">
                <h3 class="font-bold text-blue-800 mb-3 text-center">
                    <i class="fas fa-info-circle mr-2"></i>Akun Demo untuk Testing:
                </h3>
                <div class="grid grid-cols-1 gap-3 text-sm">
                    <div class="bg-white p-3 rounded border">
                        <div class="font-semibold text-blue-700">👨‍💼 Admin</div>
                        <div class="text-blue-600">admin@smk7.sch.id</div>
                        <div class="text-gray-600">Password: admin123</div>
                    </div>
                    <div class="bg-white p-3 rounded border">
                        <div class="font-semibold text-green-700">👨‍🏫 Guru</div>
                        <div class="text-green-600">guru@smk7.sch.id</div>
                        <div class="text-gray-600">Password: guru123</div>
                    </div>
                    <div class="bg-white p-3 rounded border">
                        <div class="font-semibold text-purple-700">🎓 Siswa</div>
                        <div class="text-purple-600">NIPD atau nipd@smk7.sch.id</div>
                        <div class="text-gray-600">Password: siswa123</div>
                    </div>
                    <div class="bg-white p-3 rounded border">
                        <div class="font-semibold text-orange-700">👨‍👦 Orang Tua</div>
                        <div class="text-orange-600">ortu@smk7.sch.id</div>
                        <div class="text-gray-600">Password: ortu123</div>
                    </div>
                </div>
            </div>
            -->
           
        </div>
    </div>

    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('ring-2', 'ring-blue-200');
                });
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('ring-2', 'ring-blue-200');
                });
            });

            const loginTab = document.getElementById('loginTab');
            const registerTab = document.getElementById('registerTab');
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');

            function showLogin() {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                loginTab.className = 'py-2 rounded-lg bg-blue-600 text-white font-medium';
                registerTab.className = 'py-2 rounded-lg bg-gray-100 text-gray-700 font-medium';
            }

            function showRegister() {
                registerForm.classList.remove('hidden');
                loginForm.classList.add('hidden');
                registerTab.className = 'py-2 rounded-lg bg-green-600 text-white font-medium';
                loginTab.className = 'py-2 rounded-lg bg-gray-100 text-gray-700 font-medium';
            }

            loginTab.addEventListener('click', showLogin);
            registerTab.addEventListener('click', showRegister);

            <?php if(!empty($showRegister)): ?>
            showRegister();
            <?php endif; ?>
        });
    </script>
</body>
</html>
