<?php
// app/views/layouts/sidebar.php
$current_action = $_GET['action'] ?? 'dashboard';
$user_role = $_SESSION['user_role'] ?? '';
$user_nama = $_SESSION['user_nama'] ?? '';

// Function to check if menu is active
function isActiveMenu($action, $current) {
    return strpos($current, $action) !== false ? 'bg-blue-700' : '';
}
?>

<!-- Mobile Menu Button -->


<!-- Sidebar -->
<div id="sidebar" class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-blue-800 text-white shadow-xl sidebar-transition sidebar-mobile lg:sidebar-mobile-open transform lg:transform-none">
    <!-- School Logo & Info -->
    <div class="p-4 border-b border-blue-700 bg-blue-900">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center overflow-hidden">
                    <img src="<?php echo BASE_URL; ?>/public/assets/images/logo.png" alt="Logo SMK Negeri 7" class="w-10 h-10 object-cover">
            </div>
            <div>
                <h1 class="font-bold text-lg text-white">SMK Negeri 7</h1>
                <p class="text-blue-200 text-xs">Yogyakarta</p>
            </div>
        </div>
    </div>
    
    <!-- User Profile -->
    <div class="p-4 border-b border-blue-700 lg:hidden">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center border-2 border-blue-400">
                <i class="fas fa-user text-white"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-white truncate"><?php echo htmlspecialchars($user_nama); ?></p>
                <p class="text-blue-200 text-sm capitalize truncate"><?php echo $user_role; ?></p>
            </div>
        </div>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="p-4 flex-1 overflow-y-auto">
        <ul class="space-y-1">
            <?php if($user_role == 'admin'): ?>
                <!-- Admin Menu -->
                <li>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?action=admin_dashboard" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1 <?php echo isActiveMenu('admin_dashboard', $current_action); ?>">
                        <i class="fas fa-tachometer-alt w-5 text-center"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?action=admin_presensi_sekolah" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1 <?php echo isActiveMenu('admin_presensi_sekolah', $current_action); ?>">
                        <i class="fas fa-fingerprint w-5 text-center"></i>
                        <span class="font-medium">Presensi Sekolah</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?action=admin_users" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1 <?php echo isActiveMenu('admin_users', $current_action); ?>">
                        <i class="fas fa-users w-5 text-center"></i>
                        <span class="font-medium">Manajemen User</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?action=admin_kelas" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1 <?php echo isActiveMenu('admin_kelas', $current_action); ?>">
                        <i class="fas fa-chalkboard w-5 text-center"></i>
                        <span class="font-medium">Data Kelas</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?action=admin_lokasi" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1 <?php echo isActiveMenu('admin_lokasi', $current_action); ?>">
                        <i class="fas fa-map-marker-alt w-5 text-center"></i>
                        <span class="font-medium">Lokasi Sekolah</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?action=admin_laporan" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1 <?php echo isActiveMenu('admin_laporan', $current_action); ?>">
                        <i class="fas fa-chart-bar w-5 text-center"></i>
                        <span class="font-medium">Laporan</span>
                    </a>
                </li>

            <?php elseif($user_role == 'admin_kesiswaan'): ?>
                <!-- Admin Kesiswaan Menu -->
                <li>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?action=admin_kesiswaan_dashboard" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1 <?php echo isActiveMenu('admin_kesiswaan_dashboard', $current_action); ?>">
                        <i class="fas fa-tachometer-alt w-5 text-center"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?action=admin_kesiswaan_buku_induk" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1 <?php echo isActiveMenu('admin_kesiswaan_buku_induk', $current_action); ?>">
                        <i class="fas fa-book-open w-5 text-center"></i>
                        <span class="font-medium">Buku Induk</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?action=admin_kesiswaan_presensi_sekolah" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1 <?php echo isActiveMenu('admin_kesiswaan_presensi_sekolah', $current_action); ?>">
                        <i class="fas fa-fingerprint w-5 text-center"></i>
                        <span class="font-medium">Presensi Sekolah</span>
                    </a>
                </li>

            <?php elseif($user_role == 'guru'): ?>
                <!-- Guru Menu -->
                <li>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?action=guru_dashboard" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1 <?php echo isActiveMenu('guru_dashboard', $current_action); ?>">
                        <i class="fas fa-tachometer-alt w-5 text-center"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?action=guru_kelas" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1 <?php echo isActiveMenu('guru_kelas', $current_action); ?>">
                        <i class="fas fa-chalkboard-teacher w-5 text-center"></i>
                        <span class="font-medium">Kelas Saya</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?action=guru_laporan" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1 <?php echo isActiveMenu('guru_laporan', $current_action); ?>">
                        <i class="fas fa-file-alt w-5 text-center"></i>
                        <span class="font-medium">Laporan</span>
                    </a>
                </li>
                <li>
                    <a href="#" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1">
                        <i class="fas fa-calendar-alt w-5 text-center"></i>
                        <span class="font-medium">Jadwal Mengajar</span>
                    </a>
                </li>

            <?php elseif($user_role == 'siswa'): ?>
                <!-- Siswa Menu -->
                <li>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?action=siswa_dashboard" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1 <?php echo isActiveMenu('siswa_dashboard', $current_action); ?>">
                        <i class="fas fa-tachometer-alt w-5 text-center"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?action=siswa_presensi" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1 <?php echo isActiveMenu('siswa_presensi', $current_action); ?>">
                        <i class="fas fa-fingerprint w-5 text-center"></i>
                        <span class="font-medium">Presensi</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?action=siswa_riwayat" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1 <?php echo isActiveMenu('siswa_riwayat', $current_action); ?>">
                        <i class="fas fa-history w-5 text-center"></i>
                        <span class="font-medium">Riwayat</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?action=siswa_izin" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1 <?php echo isActiveMenu('siswa_izin', $current_action); ?>">
                        <i class="fas fa-envelope w-5 text-center"></i>
                        <span class="font-medium">Ajukan Izin</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?action=siswa_buku_induk" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1 <?php echo isActiveMenu('siswa_buku_induk', $current_action); ?>">
                        <i class="fas fa-id-card w-5 text-center"></i>
                        <span class="font-medium">Buku Induk Saya</span>
                    </a>
                </li>
                <li>
                    <a href="#" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1">
                        <i class="fas fa-calendar w-5 text-center"></i>
                        <span class="font-medium">Jadwal Kelas</span>
                    </a>
                </li>

            <?php elseif($user_role == 'orangtua'): ?>
                <!-- Orang Tua Menu -->
                <li>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?action=orangtua_dashboard" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1 <?php echo isActiveMenu('orangtua_dashboard', $current_action); ?>">
                        <i class="fas fa-tachometer-alt w-5 text-center"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="#" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1">
                        <i class="fas fa-chart-line w-5 text-center"></i>
                        <span class="font-medium">Statistik</span>
                    </a>
                </li>
                <li>
                    <a href="#" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1">
                        <i class="fas fa-bell w-5 text-center"></i>
                        <span class="font-medium">Notifikasi</span>
                        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1">3</span>
                    </a>
                </li>
                <li>
                    <a href="#" 
                       class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1">
                        <i class="fas fa-cog w-5 text-center"></i>
                        <span class="font-medium">Pengaturan</span>
                    </a>
                </li>
            <?php endif; ?>
            
            <!-- Common Menu Items -->
            <li class="pt-4 mt-4 border-t border-blue-700">
                <!--
                <a href="#" 
                   class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1">
                    <i class="fas fa-question-circle w-5 text-center"></i>
                    <span class="font-medium">Bantuan</span>
                </a>
            -->
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/public/index.php?action=logout" 
                   class="flex items-center space-x-3 p-3 rounded-lg transition-all duration-200 hover:bg-red-600 hover:translate-x-1">
                    <i class="fas fa-sign-out-alt w-5 text-center"></i>
                    <span class="font-medium">Logout</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- System Status -->
     <!--
    <div class="p-4 border-t border-blue-700 bg-blue-900">
        <div class="flex items-center space-x-2 text-xs text-blue-300">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
            <span>Sistem Online</span>
        </div>
        <div class="text-xs text-blue-400 mt-1">
            <?php echo date('d M Y, H:i'); ?>
        </div>
    </div>
            -->
</div>

<!-- Backdrop for mobile -->
<div id="sidebarBackdrop" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"></div>

<script>
// Mobile sidebar functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    
    // Toggle sidebar
    function toggleSidebar() {
        sidebar.classList.toggle('open');
        sidebarBackdrop.classList.toggle('hidden');
        document.body.classList.toggle('overflow-hidden');
    }
    
    // Open sidebar
    function openSidebar() {
        sidebar.classList.add('open');
        sidebarBackdrop.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    
    // Close sidebar
    function closeSidebar() {
        sidebar.classList.remove('open');
        sidebarBackdrop.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    
    // Event listeners
    mobileMenuBtn.addEventListener('click', toggleSidebar);
    sidebarBackdrop.addEventListener('click', closeSidebar);
    
    // Close sidebar when clicking on a link (mobile)
    sidebar.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 1024) {
                closeSidebar();
            }
        });
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            closeSidebar();
        }
    });
    
    // Add active state styling
    const currentPath = window.location.pathname + window.location.search;
    sidebar.querySelectorAll('a').forEach(link => {
        if (link.href === window.location.href || currentPath.includes(link.getAttribute('href'))) {
            link.classList.add('bg-blue-700', 'border-r-4', 'border-blue-400');
        }
    });
});
</script>

<style>
.sidebar-transition {
    transition: transform 0.3s ease-in-out;
}

.sidebar-mobile {
    transform: translateX(-100%);
}

.sidebar-mobile.open {
    transform: translateX(0);
}

/* Ensure on desktop the sidebar occupies the space between header and footer */
@media (min-width: 1024px) {
    #sidebar {
        position: fixed; /* keep it fixed on desktop */
        left: 0;
        width: 16rem; /* w-64 */
        /* top and bottom will be set dynamically via JS to match header/footer heights */
        overflow: hidden;
    }

    /* allow the nav inside the sidebar to scroll independently */
    #sidebar nav {
        max-height: calc(100vh - 8rem); /* fallback; actual height set by JS */
        overflow-y: auto;
    }
}

@media (min-width: 1024px) {
    .sidebar-mobile {
        transform: translateX(0);
    }
}

/* Custom scrollbar for sidebar */
#sidebar nav::-webkit-scrollbar {
    width: 4px;
}

#sidebar nav::-webkit-scrollbar-track {
    background: #1e40af;
}

#sidebar nav::-webkit-scrollbar-thumb {
    background: #3b82f6;
    border-radius: 2px;
}

#sidebar nav::-webkit-scrollbar-thumb:hover {
    background: #60a5fa;
}

/* Animation for menu items */
#sidebar li a {
    transition: all 0.2s ease-in-out;
}

#sidebar li a:hover {
    transform: translateX(4px);
}

/* Active menu item highlight */
#sidebar li a.bg-blue-700 {
    position: relative;
}

#sidebar li a.bg-blue-700::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 60%;
    background: #60a5fa;
    border-radius: 0 2px 2px 0;
}
</style>

<script>
// Adjust sidebar top/bottom so it starts after header and ends before footer (desktop only)
function adjustSidebarBetweenHeaderFooter() {
    const sidebar = document.getElementById('sidebar');
    const header = document.querySelector('#mainContainer < header');
    const footer = document.querySelector('#mainContainer > footer');

    if (!sidebar || !header || !footer) return;

    if (window.innerWidth >= 1024) {
        const headerRect = header.getBoundingClientRect();
        const footerRect = footer.getBoundingClientRect();

        // top position should be the bottom of the header (relative to viewport)
        const top = Math.max(0, Math.round(headerRect.bottom));

        // bottom should be the visible part of the footer (distance from footer top to viewport bottom)
        // if footer is below the viewport, bottom = 0
        const bottom = Math.max(0, Math.round(window.innerHeight - footerRect.top));

        sidebar.style.top = top + 'px';
        sidebar.style.bottom = bottom + 'px';

        // Ensure the nav scroll area matches available space between header and footer
        const nav = sidebar.querySelector('nav');
        if (nav) {
            const available = window.innerHeight - top - bottom;
            nav.style.maxHeight = (available > 0 ? available : 0) + 'px';
        }
    } else {
        // On mobile, reset to default overlay behavior
        sidebar.style.top = '';
        sidebar.style.bottom = '';
        const nav = sidebar.querySelector('nav');
        if (nav) nav.style.maxHeight = '';
    }
}

// Run on load and resize
document.addEventListener('DOMContentLoaded', function() {
    adjustSidebarBetweenHeaderFooter();
    window.addEventListener('resize', adjustSidebarBetweenHeaderFooter);
    window.addEventListener('scroll', adjustSidebarBetweenHeaderFooter, { passive: true });

    // Also observe size changes to header/footer (e.g., breadcrumb appears) and recalc
    const header = document.querySelector('#mainContainer > header');
    const footer = document.querySelector('#mainContainer > footer');
    if (window.ResizeObserver && header && footer) {
        const ro = new ResizeObserver(adjustSidebarBetweenHeaderFooter);
        ro.observe(header);
        ro.observe(footer);
    }
});
</script>