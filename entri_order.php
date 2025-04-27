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

// Bersihkan ghost order - order yang tergantung
$clear_ghost = "DELETE o FROM tb_order o 
                LEFT JOIN tb_pesan p ON o.id_order = p.id_order
                WHERE o.id_pengunjung = $id 
                AND o.status_order IN ('belum bayar', 'pending')
                AND p.id_pesan IS NULL";
mysqli_query($conn, $clear_ghost);

// Bersihkan pesanan yang tidak memiliki order
$clear_orphan = "DELETE FROM tb_pesan 
                 WHERE id_user = $id 
                 AND id_order IS NULL 
                 AND (status_pesan IS NULL OR status_pesan = '')";
mysqli_query($conn, $clear_orphan);

// Simpan nomor meja ke session jika ada
if(isset($_POST['no_meja']) && !empty($_POST['no_meja'])) {
  $_SESSION['selected_meja'] = $_POST['no_meja'];
}

// Cek apakah ada order yang sudah diproses - dengan pengecekan lebih ketat
$query_cek_order = "SELECT o.id_order 
                   FROM tb_order o
                   JOIN tb_pesan p ON o.id_order = p.id_order
                   WHERE o.id_pengunjung = $id 
                   AND o.status_order IN ('belum bayar', 'pending', 'diproses')
                   AND p.status_pesan = 'pending'
                   GROUP BY o.id_order
                   HAVING COUNT(p.id_pesan) > 0
                   LIMIT 1";

$sql_cek_order = mysqli_query($conn, $query_cek_order);
if(mysqli_num_rows($sql_cek_order) > 0) {
  header("location: review_order.php");
  exit;
}

// Jika user baru saja memproses order sukses, hapus sisa item di keranjang
if(isset($_SESSION['order_completed']) && $_SESSION['order_completed'] === true) {
  unset($_SESSION['order_completed']);
  // Hapus item keranjang dengan status kosong
  $query_clear_cart = "DELETE FROM tb_pesan 
                      WHERE id_user = $id 
                      AND (status_pesan IS NULL OR status_pesan = '')";
  mysqli_query($conn, $query_clear_cart);
  
  // Reset session terkait order
  unset($_SESSION['selected_meja']);
  unset($_SESSION['last_order_id']);
}

$page_title = "Menu Pesanan";
$active_menu = "entri_order";
ob_start();
$alerts = array();
?>

<!-- Main content -->
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 p-4">
  <div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Menu List -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-xl p-6 backdrop-blur-lg bg-opacity-90">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Daftar Menu
          </h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  <?php
                    $pesan = array();
            $query_lihat_pesan = "SELECT * FROM tb_pesan WHERE id_user = $id AND (status_pesan IS NULL OR status_pesan = '' OR status_pesan = 'pending')";
                    $sql_lihat_pesan = mysqli_query($conn, $query_lihat_pesan);

                    while($r_dt_pesan = mysqli_fetch_array($sql_lihat_pesan)){
                      array_push($pesan, $r_dt_pesan['id_masakan']);
                    }

                    $query_data_makanan = "select * from tb_masakan where stok > 0 order by id_masakan desc";
                    $sql_data_makanan = mysqli_query($conn, $query_data_makanan);

                    while($r_dt_makanan = mysqli_fetch_array($sql_data_makanan)){
                  ?>
              <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition duration-300 hover:scale-105">
                <div class="relative">
                  <img src="gambar/<?php echo $r_dt_makanan['gambar_masakan']?>" 
                       alt="<?php echo $r_dt_makanan['nama_masakan']?>" 
                       class="w-full h-48 object-cover">
                  <?php if($r_dt_makanan['stok'] < 5): ?>
                    <div class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded-full text-xs">
                      Sisa <?php echo $r_dt_makanan['stok']; ?> porsi
                    </div>
                  <?php endif; ?>
                </div>
                
                <div class="p-4">
                  <h3 class="text-lg font-semibold text-gray-800 mb-2"><?php echo $r_dt_makanan['nama_masakan']?></h3>
                  
                  <div class="space-y-2 text-sm text-gray-600 mb-4">
                    <div class="flex justify-between items-center">
                      <span>Harga:</span>
                      <span class="font-medium text-indigo-600">Rp. <?php echo number_format($r_dt_makanan['harga'],0,',','.');?>,-</span>
                    </div>
                    <div class="flex justify-between items-center">
                      <span>Stok:</span>
                      <span class="font-medium"><?php echo $r_dt_makanan['stok']?> Porsi</span>
                    </div>
                        </div>

                  <form action="" method="post" class="flex flex-col gap-2">
                    <?php if(in_array($r_dt_makanan['id_masakan'], $pesan)): ?>
                      <button type="submit" disabled
                        class="w-full bg-gray-400 text-white py-2 px-4 rounded-lg flex items-center justify-center opacity-50 cursor-not-allowed">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Sudah Dipesan
                      </button>
                    <?php else: ?>
                      <div class="flex items-center justify-between bg-gray-100 rounded-lg p-2 mb-2">
                        <button type="button" onclick="decrementQty(this)" 
                          class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 transition duration-200">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                          </svg>
                        </button>
                        <input type="number" name="jumlah" value="1" min="1" max="<?php echo $r_dt_makanan['stok']?>" 
                          class="w-16 text-center border-0 bg-transparent font-medium focus:outline-none">
                        <button type="button" onclick="incrementQty(this)" 
                          class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center hover:bg-green-600 transition duration-200">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                          </svg>
                              </button>
                      </div>
                      <button type="submit" name="tambah_pesan" value="<?php echo $r_dt_makanan['id_masakan']?>"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg flex items-center justify-center transition duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Tambah ke Keranjang
                              </button>
                    <?php endif; ?>
                        </form>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>

      <!-- Shopping Cart -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-xl p-6 backdrop-blur-lg bg-opacity-90 sticky top-4">
          <div class="flex items-center mb-6">
            <svg class="w-6 h-6 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h2 class="text-xl font-bold text-gray-800">Keranjang</h2>
          </div>

          <!-- Tambah input nomor meja -->
          <div class="mb-4">
            <label for="no_meja" class="block text-sm font-medium text-gray-700 mb-2">
              Nomor Meja
            </label>
            <form method="post" id="mejaForm">
              <select name="no_meja" id="no_meja" onchange="saveMeja()" 
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
                <option value="">Pilih Nomor Meja</option>
              <?php
                for($i = 1; $i <= 20; $i++) {
                  $query_cek_meja = "SELECT * FROM tb_order WHERE no_meja = $i AND status_order != 'sudah bayar'";
                  $sql_cek_meja = mysqli_query($conn, $query_cek_meja);
                  if(mysqli_num_rows($sql_cek_meja) == 0) {
                    $selected = (isset($_SESSION['selected_meja']) && $_SESSION['selected_meja'] == $i) ? 'selected' : '';
                    echo "<option value='$i' $selected>Meja $i</option>";
                  }
                }
                ?>
              </select>
            </form>
          </div>

          <!-- List pesanan -->
          <div class="space-y-4 mb-6 max-h-96 overflow-y-auto">
            <?php
            $query_order = "SELECT * FROM tb_pesan NATURAL JOIN tb_masakan WHERE id_user = $id AND (status_pesan IS NULL OR status_pesan = '' OR status_pesan = 'pending')";
            $sql_order = mysqli_query($conn, $query_order);
            $total = 0;
            while($r_order = mysqli_fetch_array($sql_order)) {
              $subtotal = $r_order['harga'] * $r_order['jumlah'];
              $total += $subtotal;
            ?>
              <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                <div>
                  <h4 class="font-medium text-gray-800"><?php echo $r_order['nama_masakan']; ?></h4>
                  <p class="text-sm text-gray-500"><?php echo $r_order['jumlah']; ?> x Rp <?php echo number_format($r_order['harga'],0,',','.'); ?></p>
                </div>
                <div class="text-right">
                  <p class="font-medium text-indigo-600">Rp <?php echo number_format($subtotal,0,',','.'); ?></p>
                  <form method="post" style="display: inline;">
                    <input type="hidden" name="hapus_pesan" value="<?php echo $r_order['id_pesan']; ?>">
                    <button type="submit" class="text-red-500 hover:text-red-600 text-sm transition duration-200">
                      Hapus
                    </button>
                  </form>
                </div>
              </div>
            <?php } ?>
          </div>

          <div class="border-t pt-4">
            <div class="flex justify-between items-center font-bold text-lg mb-6">
              <span>Total</span>
              <span class="text-indigo-600">Rp <?php echo number_format($total,0,',','.'); ?></span>
            </div>

            <?php if($total > 0): ?>
              <form method="post" id="orderForm">
                <input type="hidden" name="no_meja" id="hidden_meja" value="<?php echo isset($_SESSION['selected_meja']) ? $_SESSION['selected_meja'] : ''; ?>">
                <input type="hidden" name="proses_pesan" value="true">
                <button type="button" onclick="prosesOrder()" 
                  class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-lg flex items-center justify-center transition duration-200">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  Proses Pesanan
                </button>
              </form>
            <?php else: ?>
              <div class="text-center text-gray-500 py-4">
                Keranjang masih kosong
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

      <?php
// Process form submissions
          if(isset($_POST['hapus_pesan'])){
            $id_pesan = $_POST['hapus_pesan'];
  
  // Get jumlah and id_masakan before deleting
  $query_get_pesan = "SELECT jumlah, id_masakan FROM tb_pesan WHERE id_pesan = $id_pesan";
  $result_pesan = mysqli_query($conn, $query_get_pesan);
  
  if($result_pesan && mysqli_num_rows($result_pesan) > 0) {
    $pesan_data = mysqli_fetch_assoc($result_pesan);
    
    // Delete from tb_pesan
    $query_hapus_pesan = "DELETE FROM tb_pesan WHERE id_pesan = $id_pesan";
            $sql_hapus_pesan = mysqli_query($conn, $query_hapus_pesan);

            if($sql_hapus_pesan){
      // Restore stock
      $query_update_stok = "UPDATE tb_masakan 
                           SET stok = stok + {$pesan_data['jumlah']} 
                           WHERE id_masakan = {$pesan_data['id_masakan']}";
      mysqli_query($conn, $query_update_stok);
      
      $alerts[] = array(
        'icon' => 'success',
        'title' => 'Berhasil!',
        'text' => 'Menu berhasil dihapus dari keranjang!'
      );
    }
  } else {
    $alerts[] = array(
      'icon' => 'error',
      'title' => 'Error!',
      'text' => 'Data pesanan tidak ditemukan!'
    );
  }
}

if(isset($_POST['tambah_pesan'])){
  $id_masakan = mysqli_real_escape_string($conn, $_POST['tambah_pesan']);
  $jumlah = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 1;
  
  // Validasi jumlah pesanan
  if($jumlah <= 0) {
    $alerts[] = array(
      'icon' => 'error',
      'title' => 'Error!',
      'text' => 'Jumlah pesanan harus lebih dari 0!'
    );
  } else {
    // Cek stok
    $check_masakan = mysqli_query($conn, "SELECT * FROM tb_masakan WHERE id_masakan = '$id_masakan'");
    if(!$check_masakan || mysqli_num_rows($check_masakan) == 0) {
      $alerts[] = array(
        'icon' => 'error',
        'title' => 'Error!',
        'text' => 'Menu tidak ditemukan!'
      );
    } else {
      $masakan = mysqli_fetch_assoc($check_masakan);
      if($masakan['stok'] < $jumlah) {
        $alerts[] = array(
          'icon' => 'error',
          'title' => 'Stok Tidak Cukup!',
          'text' => 'Stok tersedia: ' . $masakan['stok']
        );
      } else {
        // Mulai transaksi
        mysqli_begin_transaction($conn);
        try {
          // Check if order exists first
          $check_order = mysqli_query($conn, "SELECT id_order FROM tb_order WHERE id_pengunjung = $id AND status_order = 'belum bayar' ORDER BY id_order DESC LIMIT 1");
          
          // Default meja (jika belum dipilih)
          $meja_no = isset($_SESSION['selected_meja']) ? (int)$_SESSION['selected_meja'] : 1;

          if(mysqli_num_rows($check_order) > 0) {
              // Get existing order id
              $order_data = mysqli_fetch_assoc($check_order);
              $id_order_current = $order_data['id_order'];
          } else {
              // Create new order if not exists
              $query_new_order = "INSERT INTO tb_order (id_pengunjung, no_meja, total_harga, status_order) 
                                 VALUES ($id, $meja_no, 0, 'belum bayar')";
              if(!mysqli_query($conn, $query_new_order)) {
                throw new Exception("Gagal membuat order baru: " . mysqli_error($conn));
              }
              $id_order_current = mysqli_insert_id($conn);
          }

          // Insert ke tb_pesan
          $query_tambah_pesan = "INSERT INTO tb_pesan (id_user, id_masakan, id_order, jumlah, status_pesan) 
                                VALUES ($id, $id_masakan, $id_order_current, $jumlah, '')";

          if(!mysqli_query($conn, $query_tambah_pesan)) {
            throw new Exception("Gagal menambahkan pesanan: " . mysqli_error($conn));
          }
          
          // Update stok
          $update_stok = mysqli_query($conn, "UPDATE tb_masakan 
                                          SET stok = stok - $jumlah 
                                          WHERE id_masakan = '$id_masakan'");
          
          if(!$update_stok) {
            throw new Exception("Gagal update stok: " . mysqli_error($conn));
          }

          // Commit transaksi
          mysqli_commit($conn);
          
          $alerts[] = array(
            'icon' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Menu berhasil ditambahkan ke keranjang!'
          );
          
        } catch (Exception $e) {
          // Rollback jika ada error
          mysqli_rollback($conn);
          $alerts[] = array(
            'icon' => 'error',
            'title' => 'Error!',
            'text' => $e->getMessage()
          );
        }
      }
    }
  }
}

if(isset($_POST['proses_pesan'])) {
  if(!isset($_POST['no_meja']) || empty($_POST['no_meja'])) {
    $alerts[] = array(
      'icon' => 'warning',
      'title' => 'Perhatian!', 
      'text' => 'Silahkan pilih nomor meja!'
    );
  } else {
    $no_meja = (int)$_POST['no_meja'];
    
    // Simpan nomor meja ke session
    $_SESSION['selected_meja'] = $no_meja;
    
    // Debug info
    error_log("DEBUG proses_pesan - id_user: $id, no_meja: $no_meja");
    
    // Cek dulu data pesanan yang belum diproses
    $check_items = mysqli_query($conn, "SELECT p.*, m.nama_masakan, m.harga FROM tb_pesan p 
                                        JOIN tb_masakan m ON p.id_masakan = m.id_masakan 
                                        WHERE p.id_user = $id AND (p.status_pesan IS NULL OR p.status_pesan = '' OR p.status_pesan = 'pending')");
    
    $count_items = mysqli_num_rows($check_items);
    error_log("DEBUG - Items in cart: " . $count_items);
    
    if($count_items > 0) {
      // Hitung total dengan menjumlahkan subtotal
      $manual_total = 0;
      $item_data = array();
      
      while($item = mysqli_fetch_assoc($check_items)) {
        $subtotal = $item['jumlah'] * $item['harga'];
        $manual_total += $subtotal;
        $item_data[] = $item;
        error_log("DEBUG - Item: " . $item['nama_masakan'] . ", Jumlah: " . $item['jumlah'] . ", Harga: " . $item['harga'] . ", Subtotal: " . $subtotal);
      }
      error_log("DEBUG - Total: $manual_total");
      
      if($manual_total > 0) {
        // Start transaction
        mysqli_begin_transaction($conn);
        try {
          // Insert ke tb_order dengan status pending
          $query_simpan_order = "INSERT INTO tb_order (id_pengunjung, no_meja, total_harga, status_order) 
                                VALUES ($id, $no_meja, $manual_total, 'belum bayar')";
          error_log("DEBUG - Query insert order: $query_simpan_order");
          
            $sql_simpan_order = mysqli_query($conn, $query_simpan_order);

          if($sql_simpan_order) {
            $id_order = mysqli_insert_id($conn);
            error_log("DEBUG - New order ID: $id_order");
            
            // Update status pesanan dan id_order
            $query_update_pesan = "UPDATE tb_pesan SET 
                                  id_order = $id_order,
                                  status_pesan = 'pending'
                                  WHERE id_user = $id AND (status_pesan IS NULL OR status_pesan = '' OR status_pesan = 'pending')";
            error_log("DEBUG - Query update pesan: $query_update_pesan");
            
            $update_result = mysqli_query($conn, $query_update_pesan);
            $affected_rows = mysqli_affected_rows($conn);
            error_log("DEBUG - Update status result: " . ($update_result ? "success" : "failed") . ", affected rows: " . $affected_rows);
            
            if($update_result && $affected_rows > 0) {
              // Verifikasi bahwa pesanan sudah terupdate dengan id_order
              $verify_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM tb_pesan WHERE id_order = $id_order");
              $verify_result = mysqli_fetch_assoc($verify_query);
              error_log("DEBUG - Pesanan dengan id_order $id_order: " . $verify_result['count']);
              
              // Insert ke tb_stok untuk tracking
              $query_insert_stok = "INSERT INTO tb_stok (id_pesan, jumlah_terjual, status_cetak)
                                   SELECT id_pesan, jumlah, 'belum cetak'
                                   FROM tb_pesan 
                                   WHERE id_order = $id_order";
              
              $stok_result = mysqli_query($conn, $query_insert_stok);
              error_log("DEBUG - Insert stok result: " . ($stok_result ? "success" : "failed") . ", affected rows: " . mysqli_affected_rows($conn));
              
              if($stok_result) {
                // Commit transaction
                mysqli_commit($conn);
                
                // Set order ID in session
                $_SESSION['last_order_id'] = $id_order;
                
                // Set flag order completed untuk reset cart nanti
                $_SESSION['order_completed'] = true;
                
                // Set alert success di session
                $_SESSION['order_success'] = true;
                
                // Redirect ke review order
                header("location: review_order.php");
                exit;
              } else {
                throw new Exception("Gagal insert stok: " . mysqli_error($conn));
              }
            } else {
              throw new Exception("Gagal update status pesanan: " . mysqli_error($conn));
            }
          } else {
            throw new Exception("Gagal simpan order: " . mysqli_error($conn));
          }
        } catch (Exception $e) {
          // Rollback jika ada error
          mysqli_rollback($conn);
          error_log("ERROR: " . $e->getMessage());
          $alerts[] = array(
            'icon' => 'error',
            'title' => 'Error!',
            'text' => $e->getMessage()
          );
        }
      } else {
        $alerts[] = array(
          'icon' => 'warning',
          'title' => 'Perhatian!',
          'text' => 'Total harga tidak valid!'
        );
      }
    } else {
      $alerts[] = array(
        'icon' => 'warning',
        'title' => 'Perhatian!',
        'text' => 'Keranjang masih kosong!'
      );
    }
  }
}

$content = ob_get_clean();
include 'template/layouts/main.php';
?>

<script>
function incrementQty(btn) {
  const input = btn.parentElement.querySelector('input');
  const max = parseInt(input.getAttribute('max'));
  const currentValue = parseInt(input.value);
  if (currentValue < max) {
    input.value = currentValue + 1;
  }
}

function decrementQty(btn) {
  const input = btn.parentElement.querySelector('input');
  const currentValue = parseInt(input.value);
  if (currentValue > 1) {
    input.value = currentValue - 1;
  }
}

function saveMeja() {
  const mejaSelect = document.getElementById('no_meja');
  const selectedMeja = mejaSelect.value;
  
  if (selectedMeja) {
    document.getElementById('mejaForm').submit();
  }
}

function prosesOrder() {
  const mejaSelect = document.getElementById('no_meja');
  const selectedMeja = mejaSelect.value;
  
  if (!selectedMeja) {
    Swal.fire({
      icon: 'warning',
      title: 'Perhatian!',
      text: 'Silahkan pilih nomor meja!'
    });
    return;
  }
  
  document.getElementById('hidden_meja').value = selectedMeja;
  document.getElementById('orderForm').submit();
}

// Show alerts if any
<?php if(!empty($alerts)): ?>
window.onload = function() {
  <?php foreach($alerts as $alert): ?>
  Swal.fire({
    icon: '<?php echo $alert['icon']; ?>',
    title: '<?php echo $alert['title']; ?>',
    text: '<?php echo $alert['text']; ?>',
    showConfirmButton: true,
    timer: false
  }).then(() => {
    <?php if($alert['icon'] == 'success' && isset($_POST['tambah_pesan'])): ?>
    window.location.href = window.location.pathname;
    <?php endif; ?>
  });
  <?php endforeach; ?>
}
<?php endif; ?>
</script>