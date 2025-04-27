<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
include "connection/koneksi.php";
session_start();
ob_start();

$id = $_SESSION['id_user'];

if(!isset($_SESSION['username'])){
  header("location: index.php");
}

$query = "select * from tb_user natural join tb_level where id_user = $id";
$sql = mysqli_query($conn, $query);
$r = mysqli_fetch_array($sql);

// Set page title and active menu
$page_title = "Review Order";
$active_menu = "entri_order";

// Start output buffering
ob_start();

// Alert Messages Array
$alerts = array();

// Auto show success notification if redirected from entri_order
if(isset($_SESSION['order_success'])) {
  $alerts[] = array(
    'icon' => 'success',
    'title' => 'Pesanan Berhasil!',
    'text' => 'Pesanan Anda sedang diproses'
  );
  unset($_SESSION['order_success']);
}
?>

<!-- Main content -->
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 p-4">
  <div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Notifications -->
      <div class="bg-white rounded-2xl shadow-xl p-6 backdrop-blur-lg bg-opacity-90">
        <div class="flex items-center mb-4">
          <svg class="w-6 h-6 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <h2 class="text-xl font-bold text-gray-800">Status Pesanan</h2>
        </div>
        <div class="bg-indigo-50 text-indigo-800 p-6 rounded-xl mb-6">
          <div class="flex items-center mb-4">
            <svg class="w-8 h-8 text-indigo-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h4 class="text-lg font-bold">Pesanan Berhasil!</h4>
          </div>
          <p class="text-indigo-700 leading-relaxed">
            Terima kasih telah melakukan pemesanan.<br>
            Mohon tunggu sebentar, pesanan Anda sedang diproses dan akan segera diantar ke meja.
            <br><br>
            <?php
              // Get order status
              if(isset($id_order)) {
                $query_status = "SELECT status_order FROM tb_order WHERE id_order = $id_order";
                $status_result = mysqli_query($conn, $query_status);
                if($status_result && mysqli_num_rows($status_result) > 0) {
                  $order_status = mysqli_fetch_assoc($status_result)['status_order'];
                  echo '<div class="bg-white/50 p-3 rounded-xl inline-block">';
                  echo '<strong>Status Pesanan:</strong> ';
                  
                  if($order_status == 'pending') {
                    echo '<span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>';
                  } elseif($order_status == 'diproses') {
                    echo '<span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Diproses</span>';
                  } elseif($order_status == 'belum bayar') {
                    echo '<span class="px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Belum Bayar</span>';
                  } elseif($order_status == 'sudah bayar') {
                    echo '<span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Sudah Bayar</span>';
                  } else {
                    echo '<span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">' . ucfirst($order_status) . '</span>';
                  }
                  
                  echo '</div>';
                }
              }
            ?>
            <br><br>
            Setelah selesai menyantap hidangan, silakan melakukan pembayaran di kasir.
          </p>
        </div>

        <!-- Tombol Back & New Order -->
        <div class="grid grid-cols-2 gap-4">
          <button type="button" onclick="handleNewOrder()" 
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-xl flex items-center justify-center transition duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Order Baru
          </button>
          <a href="beranda.php" 
            class="w-full bg-gray-500 hover:bg-gray-600 text-white py-3 px-4 rounded-xl flex items-center justify-center transition duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
          </a>
        </div>
      </div>

      <!-- Order Details -->
      <div class="bg-white rounded-2xl shadow-xl p-6 backdrop-blur-lg bg-opacity-90">
        <div class="flex items-center mb-6">
          <svg class="w-6 h-6 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
          </svg>
          <h2 class="text-xl font-bold text-gray-800">Rincian Pesanan</h2>
        </div>
        
        <div class="bg-gray-50 rounded-xl p-4 mb-6">
          <?php 
            $query_meja = "SELECT no_meja FROM tb_order WHERE id_pengunjung = $id AND status_order IN ('pending', 'belum bayar', 'diproses') ORDER BY id_order DESC LIMIT 1";
            $sql_meja = mysqli_query($conn, $query_meja);
            $result_meja = mysqli_fetch_array($sql_meja);
            $no_meja = isset($result_meja['no_meja']) ? $result_meja['no_meja'] : '-';
          ?>
          <div class="flex items-center text-gray-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span class="font-medium">Meja <?php echo $no_meja; ?></span>
          </div>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">No.</th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Menu</th>
                <th class="px-4 py-2 text-center text-sm font-medium text-gray-500">Jumlah</th>
                <th class="px-4 py-2 text-center text-sm font-medium text-gray-500">Status</th>
                <th class="px-4 py-2 text-right text-sm font-medium text-gray-500">Harga</th>
                <th class="px-4 py-2 text-right text-sm font-medium text-gray-500">Total</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <?php
              // Get latest order ID
              $query_order = "SELECT id_order FROM tb_order WHERE id_pengunjung = $id AND status_order IN ('pending', 'belum bayar', 'diproses') ORDER BY id_order DESC LIMIT 1";

              // Check if we have a last_order_id in session
              if (isset($_SESSION['last_order_id'])) {
                $id_order = $_SESSION['last_order_id'];
                $r_order = true;
              } else {
                // Otherwise use the original query
                $sql_order = mysqli_query($conn, $query_order);
                $r_order = mysqli_fetch_array($sql_order);
                
                if($r_order) {
                  $id_order = $r_order['id_order'];
                }
              }

              if($r_order) {
                // Get order details
                $query_order_detail = "SELECT m.nama_masakan, p.jumlah, m.harga, p.status_pesan, (p.jumlah * m.harga) as subtotal
                                     FROM tb_pesan p 
                                     JOIN tb_masakan m ON p.id_masakan = m.id_masakan 
                                     WHERE p.id_order = $id_order AND p.status_pesan IN ('pending', 'sudah', 'diproses', '')
                                     ORDER BY m.nama_masakan ASC";
                
                $sql_order_detail = mysqli_query($conn, $query_order_detail);
                $row_count = mysqli_num_rows($sql_order_detail);
                
                $no = 1;
                $total = 0;
                
                if(mysqli_num_rows($sql_order_detail) > 0) {
                  while($r_detail = mysqli_fetch_array($sql_order_detail)) {
                    $total += $r_detail['subtotal'];
              ?>
                <tr class="hover:bg-gray-50 transition duration-150">
                  <td class="px-4 py-3 text-sm text-gray-900"><?php echo $no++; ?>.</td>
                  <td class="px-4 py-3 text-sm text-gray-900"><?php echo $r_detail['nama_masakan']; ?></td>
                  <td class="px-4 py-3 text-sm text-center text-gray-900"><?php echo $r_detail['jumlah']; ?></td>
                  <td class="px-4 py-3 text-sm text-center text-gray-900">
                    <?php
                      $status_text = $r_detail['status_pesan'];
                      $badge_color = '';
                      
                      if ($status_text == 'pending') {
                        $badge_color = 'bg-yellow-100 text-yellow-800';
                        $status_text = 'Pending';
                      } elseif ($status_text == 'diproses') {
                        $badge_color = 'bg-blue-100 text-blue-800';
                        $status_text = 'Diproses';
                      } elseif ($status_text == 'sudah') {
                        $badge_color = 'bg-green-100 text-green-800';
                        $status_text = 'Selesai';
                      } elseif ($status_text == '') {
                        $badge_color = 'bg-gray-100 text-gray-800';
                        $status_text = 'Baru';
                      }
                    ?>
                    <span class="px-2 py-1 rounded-full text-xs font-medium <?php echo $badge_color; ?>">
                      <?php echo $status_text; ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 text-sm text-right text-gray-900">Rp. <?php echo number_format($r_detail['harga'],0,',','.'); ?>,-</td>
                  <td class="px-4 py-3 text-sm text-right font-medium text-indigo-600">
                    Rp. <?php echo number_format($r_detail['subtotal'],0,',','.'); ?>,-
                  </td>
                </tr>
              <?php
                  }
                ?>
                  <tr class="bg-gray-50 font-bold">
                    <td colspan="5" class="px-4 py-3 text-sm text-right text-gray-900">Total Pembayaran</td>
                    <td class="px-4 py-3 text-sm text-right text-indigo-600">
                      Rp. <?php echo number_format($total,0,',','.'); ?>,-
                    </td>
                  </tr>
                <?php
                } else {
                ?>
                  <tr>
                    <td colspan="6" class="px-4 py-3 text-center text-gray-500">
                      Data pesanan tidak ditemukan
                    </td>
                  </tr>
                <?php
                }
              } else {
              ?>
                <tr>
                  <td colspan="6" class="px-4 py-3 text-center text-gray-500">
                    Tidak ada pesanan yang aktif
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
</div>

<!-- Form untuk submit new order -->
<form id="newOrderForm" method="post" action="" class="hidden">
  <input type="hidden" name="new_order" value="1">
</form>

<?php
// Handler untuk tombol Order Baru
if(isset($_POST['new_order'])) {
  // Mulai transaksi database
  mysqli_begin_transaction($conn);
  
  try {
    // Dapatkan id_order yang akan diupdate
    $query_order_id = "SELECT id_order FROM tb_order WHERE id_pengunjung = $id AND status_order IN ('pending', 'belum bayar', 'diproses') ORDER BY id_order DESC LIMIT 1";
    $sql_order_id = mysqli_query($conn, $query_order_id);
    $result_order_id = mysqli_fetch_array($sql_order_id);
    
    if ($result_order_id) {
      $id_order = $result_order_id['id_order'];
      
      // Update status order jadi diproses (BUKAN sudah bayar)
      $query_update = "UPDATE tb_order SET status_order = 'diproses' WHERE id_order = $id_order";
      $update_result = mysqli_query($conn, $query_update);
      
      if (!$update_result) {
        throw new Exception("Error updating order: " . mysqli_error($conn));
      }
      
      // Update status pesan
      $query_update_pesan = "UPDATE tb_pesan SET status_pesan = 'diproses' WHERE id_order = $id_order AND status_pesan = 'pending'";
      $update_pesan_result = mysqli_query($conn, $query_update_pesan);
      
      if (!$update_pesan_result) {
        throw new Exception("Error updating pesan: " . mysqli_error($conn));
      }
    }
    
    // Commit transaksi jika berhasil
    mysqli_commit($conn);
    
    // Set flag bahwa order sudah selesai (untuk clear cart di entri_order)
    $_SESSION['order_completed'] = true;
    
    // Set session untuk alert di dashboard
    $_SESSION['pesanan_diproses'] = true;
    
    // Redirect ke beranda
    header("location: beranda.php");
    exit;
  } catch (Exception $e) {
    // Rollback jika terjadi error
    mysqli_rollback($conn);
    echo "Error: " . $e->getMessage();
  }
}

$content = ob_get_clean();
include 'template/layouts/main.php';
?>

<script>
function handleNewOrder() {
  Swal.fire({
    title: 'Proses Pesanan?',
    text: "Pesanan sebelumnya akan diproses dan Anda akan kembali ke dashboard",
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, Proses!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('newOrderForm').submit();
    }
  });
}

// Show alerts if any
<?php if(!empty($alerts)): ?>
window.onload = function() {
  <?php foreach($alerts as $alert): ?>
  Swal.fire({
    icon: '<?php echo $alert['icon']; ?>',
    title: '<?php echo $alert['title']; ?>',
    text: '<?php echo $alert['text']; ?>',
    showConfirmButton: false,
    timer: 2000
  });
  <?php endforeach; ?>
}
<?php endif; ?>
</script>