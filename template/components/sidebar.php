<?php
// Get user level
$id = $_SESSION['id_user'];
$query = "SELECT * FROM tb_user NATURAL JOIN tb_level WHERE id_user = $id";
$sql = mysqli_query($conn, $query);
$r = mysqli_fetch_array($sql);

$level = isset($r['id_level']) ? $r['id_level'] : '';

// Define menu access based on user level
$menu_access = [
    'administrator' => ['login', 'logout', 'entri_referensi'],
    'waiter' => ['login', 'logout', 'entri_order', 'generate_laporan'],
    'kasir' => ['login', 'logout', 'entri_transaksi', 'generate_laporan'],
    'owner' => ['login', 'logout', 'generate_laporan']
];

// Get current user's menu access
$user_menu = [];
if($level == 1) {
    $user_menu = $menu_access['administrator'];
} elseif($level == 2) {
    $user_menu = $menu_access['waiter'];
} elseif($level == 3) {
    $user_menu = $menu_access['kasir'];
} elseif($level == 4) {
    $user_menu = $menu_access['owner'];
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="h-full flex flex-col bg-gradient-to-b from-indigo-900 via-indigo-800 to-indigo-900">
    <!-- Logo -->
    <div class="p-6">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-white mb-2">Restaurant</h1>
            <p class="text-indigo-200 text-sm">Management System</p>
        </div>
    </div>

    <!-- User Info -->
    <div class="px-6 py-4">
        <div class="bg-white/10 rounded-lg p-4 backdrop-blur-sm">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center">
                    <span class="text-lg font-semibold text-white">
                        <?php echo substr($r['nama_user'], 0, 1); ?>
                    </span>
                </div>
                <div>
                    <h3 class="text-white font-medium"><?php echo $r['nama_user']; ?></h3>
                    <p class="text-indigo-200 text-sm"><?php echo $r['nama_level']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6">
        <ul class="space-y-2">
            <!-- Beranda - Semua user bisa akses -->
            <li>
                <a href="beranda.php" 
                   class="nav-link flex items-center px-4 py-3 rounded-lg <?php echo $current_page == 'beranda.php' ? 'active bg-white/20 text-white' : 'text-indigo-100 hover:bg-white/10'; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Beranda
                </a>
            </li>

            <?php if($r['id_level'] == 1): // Admin Only ?>
                <li>
                    <a href="entri_referensi.php" 
                       class="nav-link flex items-center px-4 py-3 rounded-lg <?php echo $current_page == 'entri_referensi.php' ? 'active bg-white/20 text-white' : 'text-indigo-100 hover:bg-white/10'; ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Entri Referensi
                    </a>
                </li>
            <?php endif; ?>

            <?php if($r['id_level'] == 2): // Waiter Only ?>
                <li>
                    <a href="entri_order.php" 
                       class="nav-link flex items-center px-4 py-3 rounded-lg <?php echo $current_page == 'entri_order.php' ? 'active bg-white/20 text-white' : 'text-indigo-100 hover:bg-white/10'; ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Entri Order
                    </a>
                </li>
            <?php endif; ?>

            <?php if($r['id_level'] == 3): // Kasir Only ?>
                <li>
                    <a href="entri_transaksi.php" 
                       class="nav-link flex items-center px-4 py-3 rounded-lg <?php echo $current_page == 'entri_transaksi.php' ? 'active bg-white/20 text-white' : 'text-indigo-100 hover:bg-white/10'; ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Entri Transaksi
                    </a>
                </li>
            <?php endif; ?>

            <?php if($r['id_level'] == 2 || $r['id_level'] == 3 || $r['id_level'] == 4): // Waiter, Kasir, Owner ?>
                <li>
                    <a href="generate_laporan.php" 
                       class="nav-link flex items-center px-4 py-3 rounded-lg <?php echo $current_page == 'generate_laporan.php' ? 'active bg-white/20 text-white' : 'text-indigo-100 hover:bg-white/10'; ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Generate Laporan
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Logout -->
    <div class="p-4 mt-auto">
        <a href="logout.php" 
           class="flex items-center justify-center px-4 py-3 bg-red-500/20 hover:bg-red-500/30 text-red-100 rounded-lg transition-all duration-200">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Logout
        </a>
    </div>
</div> 