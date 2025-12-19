        </main>
        
        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-auto">
            <div class="px-6 py-4">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <!--
                    <div class="flex items-center space-x-4 mb-4 md:mb-0">
                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                            <i class="fas fa-shield-alt text-green-500"></i>
                            <span>Sistem Terjamin</span>
                        </div>
                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                            <i class="fas fa-bolt text-yellow-500"></i>
                            <span>Real-time</span>
                        </div>
                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                            <i class="fas fa-mobile-alt text-blue-500"></i>
                            <span>Responsif</span>
                        </div>
                    </div>
    -->
                    <div class="text-center md:text-right">
                        <p class="text-gray-600 text-sm">
                            &copy; 2025 SMK Negeri 7 Yogyakarta. 
                            <span class="font-medium">Sistem Presensi Berbasis Web dengan Geotagging.</span>
                        </p>
                        
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Global JavaScript -->
    <script>
        // Notification system
        function showNotification(type, message, duration = 5000) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 animate-fade-in ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                type === 'warning' ? 'bg-yellow-500 text-white' : 'bg-blue-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center space-x-3">
                    <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : type === 'warning' ? 'exclamation-circle' : 'info'}-circle"></i>
                    <span class="font-medium">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 hover:opacity-70">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, duration);
        }

        // Confirm dialog
        function confirmAction(message, callback) {
            const dialog = document.createElement('div');
            dialog.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            dialog.innerHTML = `
                <div class="bg-white rounded-xl shadow-2xl p-6 max-w-sm mx-4 animate-scale-in">
                    <div class="text-center mb-4">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-4xl mb-3"></i>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Konfirmasi</h3>
                        <p class="text-gray-600">${message}</p>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="this.closest('.fixed').remove()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg transition-colors">
                            Batal
                        </button>
                        <button onclick="callback(); this.closest('.fixed').remove()" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg transition-colors">
                            Ya, Lanjutkan
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(dialog);
        }

        // Loading spinner
        function showLoading() {
            const loading = document.createElement('div');
            loading.id = 'global-loading';
            loading.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            loading.innerHTML = `
                <div class="bg-white rounded-xl p-6 flex items-center space-x-3">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="text-gray-700">Memproses...</span>
                </div>
            `;
            document.body.appendChild(loading);
        }

        function hideLoading() {
            const loading = document.getElementById('global-loading');
            if (loading) {
                loading.remove();
            }
        }

        // Auto-hide flash notifications (only those with class .js-notification)
        // Prevents accidental removal of other elements that use bg-*-100 classes
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const notifications = document.querySelectorAll('.js-notification');
                notifications.forEach(notification => {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        if (notification.parentElement) notification.remove();
                    }, 300);
                });
            }, 5000);
        });

        // Add animation classes
        document.addEventListener('DOMContentLoaded', function() {
            // Add fade-in animation to main content
            const mainContent = document.querySelector('main');
            if (mainContent) {
                mainContent.classList.add('animate-fade-in');
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl + K for search (placeholder)
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                showNotification('info', 'Fitur pencarian akan segera hadir!');
            }
            
            // Escape to close modals
            if (e.key === 'Escape') {
                const openModals = document.querySelectorAll('.fixed:not(#global-loading)');
                openModals.forEach(modal => {
                    if (modal.id !== 'sidebarBackdrop') {
                        modal.remove();
                    }
                });
            }
        });

        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            @keyframes scaleIn {
                from { opacity: 0; transform: scale(0.9); }
                to { opacity: 1; transform: scale(1); }
            }
            
            .animate-fade-in {
                animation: fadeIn 0.3s ease-out;
            }
            
            .animate-scale-in {
                animation: scaleIn 0.2s ease-out;
            }
            
            /* Smooth scrolling */
            html {
                scroll-behavior: smooth;
            }
            
            /* Focus styles for accessibility */
            button:focus,
            input:focus,
            select:focus,
            textarea:focus {
                outline: 2px solid #3b82f6;
                outline-offset: 2px;
            }
            
            /* Print styles */
            @media print {
                .no-print {
                    display: none !important;
                }
                
                body {
                    background: white !important;
                }
                
                .bg-gray-50 {
                    background: white !important;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>