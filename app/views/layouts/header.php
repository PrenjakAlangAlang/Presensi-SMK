<?php
// app/views/layouts/header.php
$page_title = $page_title ?? 'Sistem Presensi';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SMK Negeri 7 Yogyakarta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom Styles -->
    <style>
    /* Ensure main content starts at top and aligns with fixed sidebar */
    html, body { height: 100%; }
    /* sidebar top will be set dynamically via JS so it starts below the header */
        #mainContainer { margin-top: 0; padding-top: 0; position: relative; }
        #mainContainer > header { margin-top: 0; }
        main { margin-top: 0; }

        .sidebar-transition {
            transition: all 0.3s ease;
        }
        .sidebar-mobile {
            transform: translateX(-100%);
        }
        .sidebar-mobile.open {
            transform: translateX(0);
        }
        @media (min-width: 1024px) {
            .sidebar-mobile {
                transform: translateX(0);
            }
        }
        
        /* Smooth transitions */
        * {
            transition: color 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }
    </style>
</head>
    <body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/sidebar.php'; ?>
    
    <!-- Main Content -->
    <div id="mainContainer" class="lg:ml-64 min-h-screen flex flex-col">
        <!-- Top Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-20">
            <div class="flex justify-between items-center px-6 py-4">
                <div class="flex items-center space-x-4">
                    <!-- Mobile menu button (left of page title) -->
                    <button id="mobileMenuBtn" class="lg:hidden p-2 bg-white-600 text-blue rounded-lg " aria-label="Buka sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="text-2xl font-bold text-gray-800"><?php echo $page_title; ?></h1>
                    <?php if(isset($page_subtitle)): ?>
                    <span class="text-gray-400 hidden md:block">|</span>
                    <p class="text-gray-600 hidden md:block"><?php echo $page_subtitle; ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div class="relative">
                        <button class="p-2 text-gray-600 hover:text-blue-600 transition-colors relative">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center animate-pulse">3</span>
                        </button>
                    </div>
                    
                    <!-- User Info -->
                    <div class="flex items-center space-x-3">
                        <div class="text-right hidden md:block">
                            <p class="text-sm font-medium text-gray-800"><?php echo $_SESSION['user_nama'] ?? 'User'; ?></p>
                            <p class="text-xs text-gray-600 capitalize"><?php echo $_SESSION['user_role'] ?? 'Role'; ?></p>
                        </div>
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold shadow-lg">
                            <?php 
                            $initial = strtoupper(substr($_SESSION['user_nama'] ?? 'U', 0, 1));
                            echo $initial;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Breadcrumb -->
            <div class="px-6 py-2 bg-gray-50 border-t border-gray-100">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm text-gray-600">
                        <li>
                            <a href="<?php echo BASE_URL; ?>/public/index.php?action=<?php echo $_SESSION['user_role'] ?? 'admin'; ?>_dashboard" class="hover:text-blue-600 transition-colors">
                                <i class="fas fa-home mr-1"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                            <span class="text-gray-800 font-medium"><?php echo $page_title; ?></span>
                        </li>
                    </ol>
                </nav>
            </div>
        </header>
        
        <!-- Main Content Area -->
        <main class="flex-1 p-6">
            <!-- Notifications -->
            <?php if(isset($_SESSION['success'])): ?>
                <!-- Flash message: beri kelas khusus agar JS auto-hide menarget hanya notifikasi -->
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 animate-fade-in js-notification">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['error'])): ?>
                <!-- Flash message: beri kelas khusus agar JS auto-hide menarget hanya notifikasi -->
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 animate-fade-in js-notification">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['warning'])): ?>
                <!-- Flash message: beri kelas khusus agar JS auto-hide menarget hanya notifikasi -->
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6 animate-fade-in js-notification">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <?php echo $_SESSION['warning']; unset($_SESSION['warning']); ?>
                    </div>
                </div>
            <?php endif; ?>