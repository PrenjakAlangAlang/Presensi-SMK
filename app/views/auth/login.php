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
    background-image: url('https://lh3.googleusercontent.com/gps-cs-s/AHVAweoQjf3tZGgu8icpjOiRZXIW3FgKnxj6IDqHqKQudytavVYlVgvOgs8VNuBWEQxUELCAfq0l4EJqWZntjWl3AQar86rszko9E1a6GsfPvD0l0FFSvoljzzcsEc8VLt3N20UChRxb=s1920-w1920-h1080');
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
            
            <?php if(isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 animate-pulse">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?php echo $error; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-medium mb-2" for="email">
                        <i class="fas fa-envelope mr-2 text-blue-500"></i>Alamat Email
                    </label>
                    <input type="email" id="email" name="email" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-300"
                           placeholder="masukkan email Anda"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-medium mb-2" for="password">
                        <i class="fas fa-lock mr-2 text-blue-500"></i>Password
                    </label>
                    <input type="password" id="password" name="password" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-300"
                           placeholder="masukkan password Anda">
                </div>
                
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>Masuk ke Sistem
                </button>
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
                        <div class="text-purple-600">siswa@smk7.sch.id</div>
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
        });
    </script>
</body>
</html>