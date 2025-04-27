<?php
include "connection/koneksi.php";
session_start();
ob_start();

$id = $_SESSION['id_user'];

if(isset($_SESSION['edit_order'])){
  //echo $_SESSION['edit_order'];
  unset($_SESSION['edit_order']);
}

if(!isset($_SESSION['username'])){
  header("location: index.php");
}

$query = "select * from tb_user natural join tb_level where id_user = $id";
$sql = mysqli_query($conn, $query);
$r = mysqli_fetch_array($sql);

// Set page title and active menu
$page_title = "Entri Transaksi";
$active_menu = "entri_transaksi";

// Alert Messages Array
$alerts = array();

// Notifikasi untuk transaksi selesai (dari transaksi.php)
if(isset($_SESSION['transaksi_sukses'])) {
  $alerts[] = array(
    'icon' => 'success',
    'title' => 'Transaksi Berhasil!',
    'text' => 'Pembayaran telah berhasil diproses'
  );
  unset($_SESSION['transaksi_sukses']);
}

// Start output buffering
ob_start();
?>

<div class="fade-in">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Entri Transaksi</h1>
        <p class="text-gray-600 mt-2">Kelola transaksi pembayaran pesanan</p>
    </div>

    <!-- Orders Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        $query_order = "SELECT o.*, u.nama_user, 
                              (SELECT COUNT(*) FROM tb_pesan WHERE id_order = o.id_order) as total_items
                       FROM tb_order o 
                       LEFT JOIN tb_user u ON o.id_pengunjung = u.id_user 
                       WHERE o.status_order = 'belum bayar'
                       ORDER BY o.waktu_pesan DESC";
        $sql_order = mysqli_query($conn, $query_order);
        
        if(mysqli_num_rows($sql_order) > 0) {
            while($r_order = mysqli_fetch_array($sql_order)){
        ?>
            <div class="elegant-card hover:scale-[1.02] transition-all duration-300">
                <div class="p-6">
                    <!-- Order Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Meja <?php echo $r_order['no_meja']; ?>
                            </h3>
                            <p class="text-sm text-gray-500">
                                <?php echo $r_order['nama_user']; ?>
                            </p>
                        </div>
                        <span class="status-badge status-pending">
                            Belum Bayar
                        </span>
                    </div>

                    <!-- Order Details -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Items:</span>
                            <span class="font-medium text-gray-800"><?php echo $r_order['total_items']; ?> menu</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Harga:</span>
                            <span class="font-medium text-purple-600">
                                Rp. <?php echo number_format($r_order['total_harga'],0,',','.'); ?>,-
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Waktu Order:</span>
                            <span class="font-medium text-gray-800">
                                <?php echo date('H:i', strtotime($r_order['waktu_pesan'])); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-3">
                        <form method="post" class="flex-1">
                            <input type="hidden" name="id_order" value="<?php echo $r_order['id_order']; ?>">
                            <button type="submit" name="edit_order" 
                                    class="w-full btn-primary flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Proses Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php 
            }
        } else {
        ?>
            <div class="col-span-full">
                <div class="elegant-card p-8 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Tidak Ada Transaksi</h3>
                        <p class="text-gray-600">Belum ada pesanan yang perlu diproses pembayarannya</p>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
    </div>

    <!-- Completed Orders Section -->
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Transaksi Selesai</h2>
        
        <div class="elegant-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="elegant-table">
                    <thead>
                        <tr>
                            <th>No. Meja</th>
                            <th>Pelanggan</th>
                            <th>Total Items</th>
                            <th>Total Harga</th>
                            <th>Waktu Order</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_selesai = "SELECT o.*, u.nama_user,
                                                (SELECT COUNT(*) FROM tb_pesan WHERE id_order = o.id_order) as total_items
                                         FROM tb_order o 
                                         LEFT JOIN tb_user u ON o.id_pengunjung = u.id_user
                                         WHERE o.status_order = 'sudah bayar'
                                         ORDER BY o.waktu_pesan DESC
                                         LIMIT 10";
                        $sql_selesai = mysqli_query($conn, $query_selesai);
                        
                        if(mysqli_num_rows($sql_selesai) > 0) {
                            while($r_selesai = mysqli_fetch_array($sql_selesai)){
                        ?>
                            <tr>
                                <td>Meja <?php echo $r_selesai['no_meja']; ?></td>
                                <td><?php echo $r_selesai['nama_user']; ?></td>
                                <td><?php echo $r_selesai['total_items']; ?> menu</td>
                                <td class="text-purple-600 font-medium">
                                    Rp. <?php echo number_format($r_selesai['total_harga'],0,',','.'); ?>,-
                                </td>
                                <td><?php echo date('H:i', strtotime($r_selesai['waktu_pesan'])); ?></td>
                                <td>
                                    <span class="status-badge status-success">
                                        Sudah Bayar
                                    </span>
                                </td>
                            </tr>
                        <?php
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="6" class="text-center py-8 text-gray-500">
                                    Belum ada transaksi yang selesai
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Process form submissions
if(isset($_POST['edit_order'])){
    $_SESSION['edit_order'] = $_POST['id_order'];
    header('location: transaksi.php');
}

$content = ob_get_clean();
include 'template/layouts/main.php';
?>