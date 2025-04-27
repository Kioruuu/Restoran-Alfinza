<?php
session_start();
include "connection/koneksi.php";

// Kalo belum login, redirect ke index/login
if(!isset($_SESSION['id_user'])) {
    header('location: index.php');
    exit();
}

// Get user data
$id = $_SESSION['id_user'];
$query = "SELECT * FROM tb_user NATURAL JOIN tb_level WHERE id_user = $id";
$sql = mysqli_query($conn, $query);
$r = mysqli_fetch_array($sql);

// Get statistics
$total_menu = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_masakan"));
$total_orders = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_order"));
$total_users = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_user"));

// Lebih aman dengan COALESCE
$income_query = "SELECT COALESCE(SUM(total_harga), 0) as total FROM tb_order WHERE status_order = 'sudah bayar'";
$income_result = mysqli_query($conn, $income_query);
$total_income = 0;
if ($income_result) {
    $income_row = mysqli_fetch_assoc($income_result);
    $total_income = $income_row['total'];
}

// Get recent orders
$recent_orders_query = "SELECT o.*, m.nama_masakan, m.harga 
                      FROM tb_order o
                      JOIN tb_pesan p ON o.id_order = p.id_order
                      JOIN tb_masakan m ON p.id_masakan = m.id_masakan
                      WHERE o.status_order IN ('belum bayar', 'diproses')
                      GROUP BY o.id_order
                      ORDER BY o.id_order DESC LIMIT 5";
$recent_orders = mysqli_query($conn, $recent_orders_query);

// Get recent transactions
$recent_transactions_query = "SELECT t.id_transaksi, t.tanggal_transaksi, o.total_harga, u.nama_user, u.id_user 
                            FROM tb_transaksi t
                            JOIN tb_order o ON t.id_order = o.id_order
                            JOIN tb_user u ON o.id_pengunjung = u.id_user
                            ORDER BY t.id_transaksi DESC LIMIT 5";
$recent_transactions = mysqli_query($conn, $recent_transactions_query);

// Get total income
$income_query = "SELECT COALESCE(SUM(total_harga), 0) as total FROM tb_order WHERE status_order = 'sudah bayar'";
$income_result = mysqli_query($conn, $income_query);
$income_data = mysqli_fetch_assoc($income_result);
$total_income = $income_data['total'];

// Get total orders today
$today_orders_query = "SELECT COUNT(*) as total FROM tb_order WHERE DATE(waktu_pesan) = CURDATE()";
$today_orders_result = mysqli_query($conn, $today_orders_query);
$today_orders_data = mysqli_fetch_assoc($today_orders_result);
$total_orders_today = $today_orders_data['total'];

// Get total menu items
$menu_items_query = "SELECT COUNT(*) as total FROM tb_masakan WHERE stok > 0";
$menu_items_result = mysqli_query($conn, $menu_items_query);
$menu_items_data = mysqli_fetch_assoc($menu_items_result);
$total_menu_items = $menu_items_data['total'];

// Get popular menu items
$popular_items_query = "SELECT m.nama_masakan, m.harga, m.gambar_masakan,
                              COUNT(p.id_masakan) as total_ordered
                       FROM tb_masakan m
                       LEFT JOIN tb_pesan p ON m.id_masakan = p.id_masakan
                       GROUP BY m.id_masakan
                       ORDER BY total_ordered DESC
                       LIMIT 4";
$popular_items = mysqli_query($conn, $popular_items_query);

// Set page title
$page_title = "Beranda";

// Alert Messages Array 
$alerts = array();

// Show notification if order was processed
if(isset($_SESSION['pesanan_diproses'])) {
    $alerts[] = array(
        'icon' => 'success',
        'title' => 'Pesanan Diproses!',
        'text' => 'Pesanan Anda sedang diproses oleh dapur'
    );
    unset($_SESSION['pesanan_diproses']);
}

// Start output buffering
ob_start();
?>

<div class="fade-in">
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            Selamat Datang, <?php echo $r['nama_user']; ?>!
        </h1>
        <p class="text-gray-600 mt-2">
            Overview statistik dan aktivitas restoran hari ini
        </p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Income -->
        <div class="elegant-card p-6 bg-gradient-to-br from-green-500 to-emerald-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Total Pendapatan</p>
                    <h3 class="text-2xl font-bold text-white mt-1">
                        Rp <?php echo number_format($total_income, 0, ',', '.'); ?>
                    </h3>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Orders Today -->
        <div class="elegant-card p-6 bg-gradient-to-br from-blue-500 to-indigo-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Order Hari Ini</p>
                    <h3 class="text-2xl font-bold text-white mt-1">
                        <?php echo $total_orders_today; ?> Order
                    </h3>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Available Menu -->
        <div class="elegant-card p-6 bg-gradient-to-br from-purple-500 to-violet-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Menu Tersedia</p>
                    <h3 class="text-2xl font-bold text-white mt-1">
                        <?php echo $total_menu_items; ?> Menu
                    </h3>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Tables -->
        <div class="elegant-card p-6 bg-gradient-to-br from-orange-500 to-red-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Meja Aktif</p>
                    <h3 class="text-2xl font-bold text-white mt-1">
                        <?php 
                        $active_tables_query = "SELECT COUNT(DISTINCT no_meja) as total FROM tb_order WHERE status_order = 'belum bayar'";
                        $active_tables_result = mysqli_query($conn, $active_tables_query);
                        $active_tables = mysqli_fetch_assoc($active_tables_result);
                        echo $active_tables['total'];
                        ?> Meja
                    </h3>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Transactions -->
        <div class="elegant-card">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    Transaksi Terbaru
                </h2>
                <div class="space-y-4">
                    <?php while($trans = mysqli_fetch_assoc($recent_transactions)): ?>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800"><?php echo $trans['nama_user']; ?></p>
                                    <p class="text-sm text-gray-500">
                                        <?php echo date('d M Y H:i', strtotime($trans['tanggal_transaksi'])); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-purple-600">
                                    Rp <?php echo isset($trans['total_harga']) ? number_format($trans['total_harga']) : 0; ?>
                                </p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- Popular Menu Items -->
        <div class="elegant-card">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    Menu Populer
                </h2>
                <div class="grid grid-cols-2 gap-4">
                    <?php while($item = mysqli_fetch_assoc($popular_items)): ?>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="aspect-w-16 aspect-h-9 mb-3">
                                <img src="gambar/<?php echo $item['gambar_masakan']; ?>" 
                                     alt="<?php echo $item['nama_masakan']; ?>"
                                     class="w-full h-32 object-cover rounded-lg">
                            </div>
                            <h3 class="font-medium text-gray-800"><?php echo $item['nama_masakan']; ?></h3>
                            <div class="flex justify-between items-center mt-2">
                                <p class="text-sm text-purple-600">
                                    Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?>
                                </p>
                                <span class="text-sm text-gray-500">
                                    <?php echo $item['total_ordered']; ?> terjual
                                </span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'template/layouts/main.php';
?>