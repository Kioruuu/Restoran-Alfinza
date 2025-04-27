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

$nama_user = $r['nama_user'];

// Set page title and active menu
$page_title = "Transaksi";
$active_menu = "entri_transaksi";

// Start output buffering
ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transaksi Pembayaran</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
    
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
    }
    
    .glass-effect {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }
  </style>
</head>
<body class="p-8">

<?php if($r['id_level'] == 1 || $r['id_level'] == 3): // Allow both admin and kasir
  if(!isset($_SESSION['edit_order'])) {
    echo '<div class="max-w-7xl mx-auto text-center py-12">
            <div class="glass-effect rounded-2xl p-8">
              <h2 class="text-2xl font-bold text-gray-800 mb-4">Tidak ada transaksi yang dipilih</h2>
              <p class="text-gray-600 mb-6">Silakan pilih order yang akan dibayar dari halaman Entri Transaksi</p>
              <a href="entri_transaksi.php" class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg shadow-blue-500/30 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Entri Transaksi
              </a>
            </div>
          </div>';
  } else {
    $id_order = $_SESSION['edit_order'];
    $query_pemesan = "select * from tb_order left join tb_user on tb_order.id_pengunjung = tb_user.id_user where id_order = $id_order";
    $sql_pemesan = mysqli_query($conn, $query_pemesan);
    $result_pemesan = mysqli_fetch_array($sql_pemesan);
    $id_pemesan = $result_pemesan['id_pengunjung'];
?>
  <div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Transaction Details -->
      <div class="glass-effect rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-4 flex items-center">
          <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
          </svg>
          <h3 class="text-lg font-semibold">
            Transaksi Pembayaran - <?php echo $result_pemesan['nama_user']; ?>
          </h3>
        </div>

        <div class="p-6">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b-2 border-gray-200">
                  <th class="py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                  <th class="py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Menu</th>
                  <th class="py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                  <th class="py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                  <th class="py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <?php
                $no_order_fiks = 1;
                $total_semua = 0;
                $query_order_fiks = "SELECT * FROM tb_pesan 
                                    LEFT JOIN tb_masakan ON tb_pesan.id_masakan = tb_masakan.id_masakan 
                                    WHERE id_order = $id_order 
                                    AND (status_pesan IN ('pending', 'belum bayar', '') OR status_pesan IS NULL)";
                $sql_order_fiks = mysqli_query($conn, $query_order_fiks);
                while($r_order_fiks = mysqli_fetch_array($sql_order_fiks)){
                  $subtotal = $r_order_fiks['harga'] * $r_order_fiks['jumlah'];
                  $total_semua += $subtotal;
                ?>
                  <tr class="hover:bg-gray-50/50 transition-colors duration-200">
                    <td class="py-4 text-sm text-gray-600">
                      <?php echo $no_order_fiks++; ?>.
                    </td>
                    <td class="py-4 text-sm font-medium text-gray-800">
                      <?php echo $r_order_fiks['nama_masakan']; ?>
                    </td>
                    <td class="py-4 text-sm text-center text-gray-600">
                      <?php echo $r_order_fiks['jumlah']; ?>
                    </td>
                    <td class="py-4 text-sm text-right text-gray-600">
                      Rp. <?php echo number_format($r_order_fiks['harga'], 0, ',', '.'); ?>,-
                    </td>
                    <td class="py-4 text-sm text-right font-medium text-gray-800">
                      Rp. <?php echo number_format($subtotal, 0, ',', '.'); ?>,-
                    </td>
                  </tr>
                <?php
                }
                
                mysqli_query($conn, "UPDATE tb_order SET total_harga = $total_semua WHERE id_order = $id_order");
                
                $query_harga = "SELECT * FROM tb_order 
                               WHERE id_order = $id_order";
                $sql_harga = mysqli_query($conn, $query_harga);
                $result_harga = mysqli_fetch_array($sql_harga);
                ?>
              </tbody>
              <tfoot>
                <tr class="border-t-2 border-gray-300">
                  <td colspan="2" class="py-4 text-sm font-bold text-gray-900 text-center">Total</td>
                  <td colspan="2"></td>
                  <td class="py-4 text-sm font-bold text-purple-600 text-right">
                    Rp. <span id="total_biaya"><?php echo number_format($total_semua, 0, ',', '.'); ?></span>,-
                  </td>
                </tr>
                <tr>
                  <td colspan="2" class="py-4 text-sm font-bold text-gray-900 text-center">No. Meja</td>
                  <td colspan="2"></td>
                  <td class="py-4 text-sm font-bold text-gray-900 text-center">
                    <?php echo $result_harga['no_meja']; ?>
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>

          <!-- Payment Form -->
          <form action="#" method="post" class="mt-8 space-y-6">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Membayar (Rp)
                </label>
                <div class="relative rounded-xl overflow-hidden shadow-sm">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <span class="text-gray-500">Rp</span>
                  </div>
                  <input type="number" 
                    id="uang_bayar" 
                    name="uang_bayar" 
                    class="block w-full pl-12 pr-12 py-3 text-base border-gray-200 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="0"
                    onchange="return operasi()"
                    required>
                  <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <span class="text-gray-500">,-</span>
                  </div>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Kembalian (Rp)
                </label>
                <div class="relative rounded-xl overflow-hidden shadow-sm">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <span class="text-gray-500">Rp</span>
                  </div>
                  <input type="number" 
                    id="uang_kembali1" 
                    class="block w-full pl-12 pr-12 py-3 text-base border-gray-200 bg-gray-50"
                    placeholder="0"
                    disabled>
                  <input type="hidden" id="uang_kembali" name="uang_kembali">
                  <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <span class="text-gray-500">,-</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex space-x-4 justify-center">
              <button type="submit" 
                value="<?php echo $result_harga['id_order']; ?>" 
                name="save_order"
                class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl text-sm font-medium text-white bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-lg shadow-green-500/30 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Transaksi Selesai
              </button>

              <button type="submit" 
                name="back_order"
                class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl text-sm font-medium text-white bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-lg shadow-red-500/30 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Preview Section -->
      <div class="glass-effect rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-4 flex items-center">
          <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <h3 class="text-lg font-semibold">Preview Struk</h3>
        </div>

        <div class="p-6">
          <div class="bg-white/50 backdrop-blur-sm rounded-xl p-6 shadow-inner">
            <div class="text-center mb-6">
              <div class="mb-3">
                <svg class="w-16 h-16 mx-auto text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/>
                </svg>
              </div>
              <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-indigo-600">
                RESTAURANT CEPAT SAJI
              </h2>
              <p class="text-sm text-gray-600 mt-2">
                Jl. Imam Bonjol No. 103 Ds. Tembarak<br>
                Kec. Kertosono, Kab. Nganjuk, Jatim<br>
                Telp. +6289 xxx xxx xxx
              </p>
            </div>

            <div class="h-px bg-gradient-to-r from-transparent via-gray-400/50 to-transparent my-6"></div>

            <div class="grid grid-cols-2 gap-6 mb-6">
              <div>
                <p class="text-sm text-gray-500">Pelanggan:</p>
                <p class="font-medium text-gray-800"><?php echo $result_pemesan['nama_user']; ?></p>
              </div>
              <div>
                <p class="text-sm text-gray-500">No. Meja:</p>
                <p class="font-medium text-gray-800"><?php echo $result_harga['no_meja']; ?></p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Total Bayar:</p>
                <p class="font-medium text-purple-600">
                  Rp. <?php echo number_format($result_harga['total_harga'], 0, ',', '.'); ?>,-
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Kasir:</p>
                <p class="font-medium text-gray-800"><?php echo $nama_user; ?></p>
              </div>
            </div>

            <div class="h-px bg-gradient-to-r from-transparent via-gray-400/50 to-transparent mb-6"></div>

            <!-- QR Code -->
            <div class="flex justify-center">
              <div class="p-4 bg-white/80 rounded-xl inline-block">
                <svg class="w-24 h-24 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M3 3h6v6H3V3zm2 2v2h2V5H5zm8-2h6v6h-6V3zm2 2v2h2V5h-2zM3 13h6v6H3v-6zm2 2v2h2v-2H5zm13-2h3v2h-3v-2zm-3 0h2v3h-2v-3zm3 3h3v3h-3v-3zm-3 0h2v2h-2v-2z"/>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function operasi(){
      var total_biaya = <?php echo $result_harga['total_harga']; ?>;
      var uang_bayar = document.getElementById('uang_bayar').value;
      var kembalian = uang_bayar - total_biaya;
      
      document.getElementById('uang_kembali1').value = kembalian;
      document.getElementById('uang_kembali').value = kembalian;
    }
  </script>
<?php 
  }
endif; 
?>

</body>
</html>

<?php
if(isset($_POST['save_order'])){
  $id_order = $_POST['save_order'];
  $uang_bayar = $_POST['uang_bayar'];
  $uang_kembali = $_POST['uang_kembali'];
  
  // Validasi input
  if($uang_bayar <= 0) {
    $alerts[] = array(
      'icon' => 'error',
      'title' => 'Error!',
      'text' => 'Jumlah pembayaran harus lebih dari 0!'
    );
  } else {
    // Cek status order
    $check_status = mysqli_query($conn, "SELECT status_order, total_harga FROM tb_order WHERE id_order = $id_order");
    if(!$check_status || mysqli_num_rows($check_status) == 0) {
      $alerts[] = array(
        'icon' => 'error',
        'title' => 'Error!',
        'text' => 'Order tidak ditemukan!'
      );
    } else {
      $order_data = mysqli_fetch_assoc($check_status);
      if($order_data['status_order'] == 'sudah bayar') {
        $alerts[] = array(
          'icon' => 'error',
          'title' => 'Error!',
          'text' => 'Order ini sudah dibayar!'
        );
      } else if($uang_bayar < $order_data['total_harga']) {
        $alerts[] = array(
          'icon' => 'error',
          'title' => 'Error!',
          'text' => 'Pembayaran kurang! Total: Rp ' . number_format($order_data['total_harga'], 0, ',', '.')
        );
      } else {
        // Mulai transaksi database
        mysqli_begin_transaction($conn);
        
        try {
          // Update status order menjadi sudah bayar
          $query_bayar = "update tb_order set uang_bayar = $uang_bayar, uang_kembali = $uang_kembali, status_order = 'sudah bayar' where id_order = $id_order";
          if(!mysqli_query($conn, $query_bayar)) {
            throw new Exception("Gagal update status order: " . mysqli_error($conn));
          }
          
          // Update status pesanan
          $query_pesan_bayar = "update tb_pesan set status_pesan = 'sudah' where id_order = $id_order";
          if(!mysqli_query($conn, $query_pesan_bayar)) {
            throw new Exception("Gagal update status pesanan: " . mysqli_error($conn));
          }
          
          // Check if tb_transaksi exists
          $check_trans_table = mysqli_query($conn, "SHOW TABLES LIKE 'tb_transaksi'");
          
          if(mysqli_num_rows($check_trans_table) > 0) {
            // Copy data ke tb_transaksi
            $query_trans = "INSERT INTO tb_transaksi (id_order, tanggal_transaksi) 
                           VALUES ($id_order, NOW())
                           ON DUPLICATE KEY UPDATE tanggal_transaksi = NOW()";
            if(!mysqli_query($conn, $query_trans)) {
              throw new Exception("Gagal menyimpan data transaksi: " . mysqli_error($conn));
            }
          }
          
          // Commit transaksi jika semuanya berhasil
          mysqli_commit($conn);
          
          // Set session untuk notifikasi sukses
          $_SESSION['transaksi_sukses'] = true;
          
          // Redirect ke entri transaksi
          header('location: entri_transaksi.php');
          exit;
        } catch(Exception $e) {
          // Rollback jika terjadi error
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

if(isset($_POST['back_order'])){
  header('location: entri_transaksi.php');
}

mysqli_close($conn);
?>