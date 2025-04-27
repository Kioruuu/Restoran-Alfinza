<?php
include "connection/koneksi.php";
session_start();

if(!isset($_SESSION['username'])){
  header("location: index.php");
  exit;
}

$id = $_SESSION['id_user'];

// Dapatkan semua order aktif
$query_orders = "SELECT o.*, u.nama_user 
                FROM tb_order o 
                LEFT JOIN tb_user u ON o.id_pengunjung = u.id_user
                WHERE o.status_order IN ('pending', 'belum bayar', 'diproses')
                ORDER BY o.id_order DESC";
$orders = mysqli_query($conn, $query_orders);

// Dapatkan semua pesanan yang belum diproses
$query_unproc = "SELECT p.*, m.nama_masakan, m.harga, u.nama_user, u.username 
                FROM tb_pesan p 
                JOIN tb_masakan m ON p.id_masakan = m.id_masakan
                JOIN tb_user u ON p.id_user = u.id_user 
                WHERE (p.status_pesan IS NULL OR p.status_pesan = '' OR p.status_pesan = 'pending')
                ORDER BY p.id_pesan DESC";
$unproc = mysqli_query($conn, $query_unproc);

// Fix broken relationships
$fixed = false;
if(isset($_POST['fix'])) {
  $order_id = (int)$_POST['order_id'];
  $user_id = (int)$_POST['user_id'];
  
  if($order_id > 0 && $user_id > 0) {
    $fix_query = "UPDATE tb_pesan SET id_order = $order_id, status_pesan = 'pending' 
                  WHERE id_user = $user_id AND (status_pesan IS NULL OR status_pesan = '')";
    
    if(mysqli_query($conn, $fix_query)) {
      $fixed = true;
    }
  }
}

// Add this after $fixed = false
$cleaned = false;
if(isset($_POST['clean_orders'])) {
  // Start transaction
  mysqli_begin_transaction($conn);
  
  try {
    // Hapus semua order yang pending dan belum ada pesanan terkait 
    $delete_empty = "DELETE FROM tb_order 
                     WHERE id_order NOT IN (
                       SELECT DISTINCT id_order FROM tb_pesan WHERE id_order IS NOT NULL
                     ) AND status_order IN ('pending', 'belum bayar')";
    $res1 = mysqli_query($conn, $delete_empty);
    $count1 = mysqli_affected_rows($conn);
    
    // Commit jika berhasil
    mysqli_commit($conn);
    $cleaned = true;
    $clean_msg = "Berhasil menghapus $count1 order kosong!";
  } catch(Exception $e) {
    // Rollback jika gagal
    mysqli_rollback($conn);
    $clean_error = $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Debug Order System</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
  <div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
      <h1 class="text-2xl font-bold text-gray-800 mb-4">Debug Order System</h1>
      <p class="mb-4">Page ini untuk debugging masalah dengan sistem order.</p>
      
      <?php if($fixed): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        Berhasil memperbaiki hubungan antara pesanan dan order!
      </div>
      <?php endif; ?>
      
      <div class="flex space-x-4 mb-6">
        <a href="entri_order.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
          &larr; Kembali ke Entri Order
        </a>
        <a href="review_order.php?debug=1" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded">
          Review Order (dengan Debug)
        </a>
      </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- Orders Table -->
      <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Daftar Order Aktif</h2>
        
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-2 text-left">ID Order</th>
                <th class="px-4 py-2 text-left">User</th>
                <th class="px-4 py-2 text-left">No. Meja</th>
                <th class="px-4 py-2 text-left">Status</th>
                <th class="px-4 py-2 text-right">Total</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <?php if(mysqli_num_rows($orders) > 0): ?>
                <?php while($order = mysqli_fetch_assoc($orders)): ?>
                <tr>
                  <td class="px-4 py-2"><?php echo $order['id_order']; ?></td>
                  <td class="px-4 py-2">
                    <?php echo $order['nama_user']; ?> (ID: <?php echo $order['id_pengunjung']; ?>)
                  </td>
                  <td class="px-4 py-2"><?php echo $order['no_meja']; ?></td>
                  <td class="px-4 py-2"><?php echo $order['status_order']; ?></td>
                  <td class="px-4 py-2 text-right">Rp. <?php echo number_format($order['total_harga'], 0, ',', '.'); ?></td>
                </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                    Tidak ada order aktif
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Unprocessed Items -->
      <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Item Belum Diproses</h2>
        
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-2 text-left">ID Pesan</th>
                <th class="px-4 py-2 text-left">User</th>
                <th class="px-4 py-2 text-left">Item</th>
                <th class="px-4 py-2 text-center">Qty</th>
                <th class="px-4 py-2 text-left">Status</th>
                <th class="px-4 py-2 text-left">ID Order</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <?php if(mysqli_num_rows($unproc) > 0): ?>
                <?php while($item = mysqli_fetch_assoc($unproc)): ?>
                <tr>
                  <td class="px-4 py-2"><?php echo $item['id_pesan']; ?></td>
                  <td class="px-4 py-2">
                    <?php echo $item['nama_user']; ?> (ID: <?php echo $item['id_user']; ?>)
                  </td>
                  <td class="px-4 py-2"><?php echo $item['nama_masakan']; ?></td>
                  <td class="px-4 py-2 text-center"><?php echo $item['jumlah']; ?></td>
                  <td class="px-4 py-2">
                    <?php echo empty($item['status_pesan']) ? '<span class="text-red-500">empty</span>' : $item['status_pesan']; ?>
                  </td>
                  <td class="px-4 py-2">
                    <?php if(empty($item['id_order']) || $item['id_order'] == 0): ?>
                      <span class="text-red-500">null</span>
                    <?php else: ?>
                      <?php echo $item['id_order']; ?>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                    Tidak ada item belum diproses
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    
    <!-- Fix Tool -->
    <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
      <h2 class="text-xl font-bold text-gray-800 mb-4">Fix Relationships</h2>
      
      <form method="post" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              ID Order
            </label>
            <select name="order_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
              <option value="">-- Pilih ID Order --</option>
              <?php 
              mysqli_data_seek($orders, 0);
              while($order = mysqli_fetch_assoc($orders)): 
              ?>
              <option value="<?php echo $order['id_order']; ?>">
                ID: <?php echo $order['id_order']; ?> - Meja <?php echo $order['no_meja']; ?> (<?php echo $order['nama_user']; ?>)
              </option>
              <?php endwhile; ?>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              User ID (untuk pesanan yang belum terhubung)
            </label>
            <select name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
              <option value="">-- Pilih User --</option>
              <?php 
              $query_users = "SELECT DISTINCT p.id_user, u.nama_user, u.username 
                             FROM tb_pesan p
                             JOIN tb_user u ON p.id_user = u.id_user
                             WHERE (p.status_pesan IS NULL OR p.status_pesan = '')
                             AND (p.id_order IS NULL OR p.id_order = 0)";
              $users = mysqli_query($conn, $query_users);
              while($user = mysqli_fetch_assoc($users)): 
              ?>
              <option value="<?php echo $user['id_user']; ?>">
                ID: <?php echo $user['id_user']; ?> - <?php echo $user['nama_user']; ?> (<?php echo $user['username']; ?>)
              </option>
              <?php endwhile; ?>
            </select>
          </div>
        </div>
        
        <button type="submit" name="fix" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">
          Perbaiki Relasi
        </button>
      </form>
    </div>

    <!-- Clean Tool -->
    <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
      <h2 class="text-xl font-bold text-gray-800 mb-4">Clean Duplicate/Empty Orders</h2>
      
      <?php if($cleaned): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <?php echo $clean_msg; ?>
      </div>
      <?php endif; ?>
      
      <?php if(isset($clean_error)): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        Error: <?php echo $clean_error; ?>
      </div>
      <?php endif; ?>
      
      <form method="post" onsubmit="return confirm('Yakin ingin menghapus order yang tidak terpakai? Tindakan ini tidak bisa dibatalkan!');">
        <button type="submit" name="clean_orders" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
          Hapus Order Kosong
        </button>
        <p class="text-sm text-gray-500 mt-2">
          Ini akan menghapus order dengan status pending/belum bayar yang tidak memiliki pesanan terkait.
        </p>
      </form>
    </div>
  </div>
</body>
</html> 