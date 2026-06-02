<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="fixed inset-y-0 left-0 w-64 bg-slate-900 text-white flex flex-col">
    <div class="p-6 text-xl font-bold text-emerald-400" style="font-size: 1.25rem;">Admin Panel</div>
    <nav class="flex-1 px-4 space-y-1">
        <a href="dashboard.php" class="block py-2.5 px-3 rounded transition <?php echo $current_page === 'dashboard.php' ? 'bg-emerald-700 font-semibold' : 'hover:bg-emerald-700'; ?>" style="font-size: 0.95rem;">Dashboard</a>
        <a href="manage_lapangan.php" class="block py-2.5 px-3 rounded transition <?php echo $current_page === 'manage_lapangan.php' ? 'bg-emerald-700 font-semibold' : 'hover:bg-emerald-700'; ?>" style="font-size: 0.95rem;">Kelola Lapangan</a>
        <a href="manage_booking.php" class="block py-2.5 px-3 rounded transition <?php echo $current_page === 'manage_booking.php' ? 'bg-emerald-700 font-semibold' : 'hover:bg-emerald-700'; ?>" style="font-size: 0.95rem;">Kelola Booking</a>
        <a href="manage_konten.php" class="block py-2.5 px-3 rounded transition <?php echo $current_page === 'manage_konten.php' ? 'bg-emerald-700 font-semibold' : 'hover:bg-emerald-700'; ?>" style="font-size: 0.95rem;">Kelola Konten</a>
        <a href="auth/logout.php" class="block py-2.5 px-3 rounded transition hover:bg-emerald-700" style="font-size: 0.95rem;">Logout</a>
    </nav>
</aside>
