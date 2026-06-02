<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<style>
    /* Responsive Sidebar */
    #admin-sidebar {
        transition: transform 0.3s ease-in-out;
    }

    #admin-sidebar.open {
        transform: translateX(0);
    }

    #admin-sidebar-overlay {
        transition: all 0.3s ease-in-out;
    }

    #admin-sidebar-overlay.open {
        opacity: 0.5;
        visibility: visible;
    }

    /* Mobile topbar spacing */
    @media (max-width: 768px) {
        main {
            margin-left: 0 !important;
            padding-top: calc(2rem + 3.5rem) !important; /* topbar height + extra space */
        }
    }

    @media (min-width: 769px) {
        main {
            margin-left: 16rem !important;
            padding-top: 2rem !important;
        }
    }

    body.menu-open {
        overflow: hidden;
    }
</style>

<!-- Mobile Topbar with Hamburger Menu -->
<div class="md:hidden fixed top-0 left-0 right-0 z-50 bg-slate-900 text-white px-4 py-3 flex items-center justify-between border-b border-slate-700">
    <div class="flex items-center gap-2">
        <i class="fas fa-futbol text-emerald-400 text-lg"></i>
        <span class="font-bold text-emerald-400 text-sm">Admin</span>
    </div>
    <button id="admin-menu-toggle" class="text-xl focus:outline-none hover:text-emerald-400 transition">
        <i class="fas fa-bars"></i>
    </button>
</div>

<!-- Desktop Sidebar -->
<aside id="admin-sidebar" class="fixed inset-y-0 left-0 w-64 bg-slate-900 text-white flex flex-col transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-40">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 p-6 border-b border-slate-700">
        <div class="flex items-center gap-2">
            <i class="fas fa-futbol text-emerald-400 text-2xl"></i>
            <div>
                <div class="font-bold text-white text-lg">FutsalBook</div>
                <div class="text-emerald-400 text-xs">Admin Panel</div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-2 overflow-y-auto">
        <a href="dashboard.php" class="block py-3 px-4 rounded-lg transition flex items-center gap-3 <?php echo $current_page === 'dashboard.php' ? 'bg-emerald-600 font-semibold text-white' : 'text-gray-300 hover:bg-slate-800 hover:text-white'; ?>" onclick="closeSidebar()">
            <i class="fas fa-chart-line w-5"></i>
            <span>Dashboard</span>
        </a>

        <a href="manage_lapangan.php" class="block py-3 px-4 rounded-lg transition flex items-center gap-3 <?php echo $current_page === 'manage_lapangan.php' ? 'bg-emerald-600 font-semibold text-white' : 'text-gray-300 hover:bg-slate-800 hover:text-white'; ?>" onclick="closeSidebar()">
            <i class="fas fa-soccer-ball w-5"></i>
            <span>Kelola Lapangan</span>
        </a>

        <a href="manage_booking.php" class="block py-3 px-4 rounded-lg transition flex items-center gap-3 <?php echo $current_page === 'manage_booking.php' ? 'bg-emerald-600 font-semibold text-white' : 'text-gray-300 hover:bg-slate-800 hover:text-white'; ?>" onclick="closeSidebar()">
            <i class="fas fa-calendar-check w-5"></i>
            <span>Kelola Booking</span>
        </a>

        <a href="manage_konten.php" class="block py-3 px-4 rounded-lg transition flex items-center gap-3 <?php echo $current_page === 'manage_konten.php' ? 'bg-emerald-600 font-semibold text-white' : 'text-gray-300 hover:bg-slate-800 hover:text-white'; ?>" onclick="closeSidebar()">
            <i class="fas fa-file-alt w-5"></i>
            <span>Kelola Konten</span>
        </a>

        <a href="manage_gallery.php" class="block py-3 px-4 rounded-lg transition flex items-center gap-3 <?php echo $current_page === 'manage_gallery.php' ? 'bg-emerald-600 font-semibold text-white' : 'text-gray-300 hover:bg-slate-800 hover:text-white'; ?>" onclick="closeSidebar()">
            <i class="fas fa-images w-5"></i>
            <span>Kelola Gallery</span>
        </a>
    </nav>

    <!-- Logout Button -->
    <div class="border-t border-slate-700 p-3">
        <a href="auth/logout.php" class="block py-3 px-4 rounded-lg transition flex items-center gap-3 text-red-400 hover:bg-red-900/20 hover:text-red-300">
            <i class="fas fa-sign-out-alt w-5"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>

<!-- Sidebar Overlay (Mobile) -->
<div id="admin-sidebar-overlay" class="fixed inset-0 bg-black opacity-0 invisible md:hidden transition-all duration-300 ease-in-out z-30" onclick="closeSidebar()"></div>

<script>
    const adminMenuToggle = document.getElementById('admin-menu-toggle');
    const adminSidebar = document.getElementById('admin-sidebar');
    const adminSidebarOverlay = document.getElementById('admin-sidebar-overlay');

    // Open sidebar
    if (adminMenuToggle) {
        adminMenuToggle.addEventListener('click', function() {
            adminSidebar.classList.add('open');
            adminSidebarOverlay.classList.add('open');
            document.body.classList.add('menu-open');
        });
    }

    // Close sidebar
    function closeSidebar() {
        adminSidebar.classList.remove('open');
        adminSidebarOverlay.classList.remove('open');
        document.body.classList.remove('menu-open');
    }

    // Close on overlay click
    if (adminSidebarOverlay) {
        adminSidebarOverlay.addEventListener('click', closeSidebar);
    }

    // Close on ESC press
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && adminSidebar.classList.contains('open')) {
            closeSidebar();
        }
    });
</script>
