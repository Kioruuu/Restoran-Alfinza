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
$id_order = $_REQUEST['konten'];

// Check if tb_transaksi exists and the order is in it
$check_trans_table = mysqli_query($conn, "SHOW TABLES LIKE 'tb_transaksi'");
$order_in_trans = false;

if(mysqli_num_rows($check_trans_table) > 0) {
  $check_order = mysqli_query($conn, "SELECT * FROM tb_transaksi WHERE id_order = $id_order");
  $order_in_trans = mysqli_num_rows($check_order) > 0;
}

// Get order details
$query_order = "select * from tb_order left join tb_user on tb_order.id_pengunjung = tb_user.id_user where id_order = $id_order";
$sql_order = mysqli_query($conn, $query_order);
$result_order = mysqli_fetch_array($sql_order);

// Cek apakah order ada dan sudah dibayar
if(!$result_order || $result_order['status_order'] != 'sudah bayar') {
  echo "<div style='padding: 20px; background-color: #f8d7da; color: #721c24; text-align: center; margin: 20px;'>
          <h3>Transaksi tidak ditemukan atau belum dibayar</h3>
          <p>Silakan kembali ke halaman transaksi.</p>
          <a href='entri_transaksi.php' style='padding: 10px 15px; background-color: #6c757d; color: white; text-decoration: none; display: inline-block; margin-top: 10px;'>Kembali</a>
        </div>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cetak Transaksi #<?php echo $id_order; ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
    
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .receipt-shadow {
      box-shadow: 0 0 60px rgba(0, 0, 0, 0.1);
    }
    
    @media print {
      body {
        background: none;
      }
      .no-print {
        display: none !important;
      }
      .print-area {
        margin: 0;
        padding: 0;
        box-shadow: none;
      }
      @page {
        margin: 0.5cm;
      }
    }
  </style>
</head>
<body class="min-h-screen py-12">
  <div class="max-w-3xl mx-auto px-4">
    <!-- Action Buttons -->
    <div class="mb-6 flex justify-between items-center no-print">
      <a href="entri_transaksi.php" 
        class="inline-flex items-center px-6 py-3 bg-white/20 hover:bg-white/30 text-white rounded-xl backdrop-blur-sm transition-all duration-200 border border-white/30">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Transaksi
      </a>
      
      <button onclick="window.print()" 
        class="inline-flex items-center px-6 py-3 bg-emerald-500/90 hover:bg-emerald-600/90 text-white rounded-xl backdrop-blur-sm transition-all duration-200 shadow-lg shadow-emerald-500/30">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Cetak Struk
      </button>
    </div>

    <!-- Receipt Content -->
    <div class="bg-white/95 backdrop-blur-xl rounded-[2rem] receipt-shadow print-area">
      <div class="p-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <div class="mb-3 relative">
            <svg class="w-16 h-16 mx-auto text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/>
            </svg>
          </div>
          <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-indigo-600">
            RESTAURANT CEPAT SAJI
          </h1>
          <p class="text-gray-500 text-sm mt-2">
            Jl. Imam Bonjol No. 103 Ds. Tembarak, Kec. Kertosono,<br>
            Kab. Nganjuk, Jatim<br>
            Telp. +6289 xxx xxx xxx || E-mail exsample@gmail.com
          </p>
        </div>

        <div class="h-px bg-gradient-to-r from-transparent via-gray-400/50 to-transparent my-6"></div>

        <!-- Order Info -->
        <div class="grid grid-cols-2 gap-8 mb-8">
          <div class="space-y-4">
            <div>
              <p class="text-sm text-gray-500">Nama Pelanggan</p>
              <p class="font-semibold text-gray-800"><?php echo $result_order['nama_user']; ?></p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Nama Kasir</p>
              <p class="font-semibold text-gray-800"><?php echo $nama_user; ?></p>
            </div>
          </div>
          <div class="space-y-4">
            <div>
              <p class="text-sm text-gray-500">Waktu Pesan</p>
              <p class="font-semibold text-gray-800"><?php echo $result_order['waktu_pesan']; ?></p>
            </div>
            <div>
              <p class="text-sm text-gray-500">No Meja</p>
              <p class="font-semibold text-gray-800"><?php echo $result_order['no_meja']; ?></p>
            </div>
          </div>
        </div>

        <!-- Order Items -->
        <div class="rounded-xl border border-gray-100 overflow-hidden mb-8">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-50/50">
                <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Menu</th>
                <th class="py-3 px-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                <th class="py-3 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                <th class="py-3 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <?php
              $no_order_fiks = 1;
              $query_order_fiks = "select * from tb_pesan natural join tb_masakan where id_order = $id_order";
              $sql_order_fiks = mysqli_query($conn, $query_order_fiks);
              while($r_order_fiks = mysqli_fetch_array($sql_order_fiks)){
                $subtotal = $r_order_fiks['harga'] * $r_order_fiks['jumlah'];
              ?>
                <tr class="hover:bg-gray-50/50 transition-colors duration-200">
                  <td class="py-4 px-4 text-sm text-gray-600"><?php echo $no_order_fiks++; ?>.</td>
                  <td class="py-4 px-4 text-sm text-gray-800 font-medium"><?php echo $r_order_fiks['nama_masakan']; ?></td>
                  <td class="py-4 px-4 text-sm text-gray-600 text-center"><?php echo $r_order_fiks['jumlah']; ?></td>
                  <td class="py-4 px-4 text-sm text-gray-600 text-right">
                    Rp. <?php echo number_format($r_order_fiks['harga'], 0, ',', '.'); ?>,-
                  </td>
                  <td class="py-4 px-4 text-sm font-medium text-gray-800 text-right">
                    Rp. <?php echo number_format($subtotal, 0, ',', '.'); ?>,-
                  </td>
                </tr>
              <?php } ?>
            </tbody>
            <tfoot>
              <?php
              $query_harga = "select * from tb_order where id_order = $id_order";
              $sql_harga = mysqli_query($conn, $query_harga);
              $result_harga = mysqli_fetch_array($sql_harga);
              ?>
              <tr class="border-t-2 border-gray-200">
                <td colspan="3" class="py-4 px-4"></td>
                <td class="py-4 px-4 text-sm font-bold text-gray-600 text-right">Total:</td>
                <td class="py-4 px-4 text-sm font-bold text-purple-600 text-right">
                  Rp. <?php echo number_format($result_harga['total_harga'], 0, ',', '.'); ?>,-
                </td>
              </tr>
              <tr>
                <td colspan="3" class="py-4 px-4"></td>
                <td class="py-4 px-4 text-sm font-bold text-gray-600 text-right">Bayar:</td>
                <td class="py-4 px-4 text-sm font-bold text-emerald-600 text-right">
                  Rp. <?php echo number_format($result_harga['uang_bayar'], 0, ',', '.'); ?>,-
                </td>
              </tr>
              <tr>
                <td colspan="3" class="py-4 px-4"></td>
                <td class="py-4 px-4 text-sm font-bold text-gray-600 text-right">Kembali:</td>
                <td class="py-4 px-4 text-sm font-bold text-indigo-600 text-right">
                  Rp. <?php echo number_format($result_harga['uang_kembali'], 0, ',', '.'); ?>,-
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="h-px bg-gradient-to-r from-transparent via-gray-400/50 to-transparent mb-8"></div>

        <!-- Footer -->
        <div class="text-center">
          <h2 class="text-xl font-bold text-gray-800 mb-2">TERIMAKASIH ATAS KUNJUNGANNYA</h2>
          <p class="text-gray-500">Silahkan berkunjung kembali</p>
          
          <!-- QR Code (Optional) -->
          <div class="mt-6 inline-flex items-center justify-center p-4 bg-gray-50 rounded-xl">
            <svg class="w-20 h-20 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
              <path d="M3 3h6v6H3V3zm2 2v2h2V5H5zm8-2h6v6h-6V3zm2 2v2h2V5h-2zM3 13h6v6H3v-6zm2 2v2h2v-2H5zm13-2h3v2h-3v-2zm-3 0h2v3h-2v-3zm3 3h3v3h-3v-3zm-3 0h2v2h-2v-2z"/>
            </svg>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Auto print when page loads (optional)
    // window.onload = function() {
    //   window.print();
    // }
  </script>
</body>
</html>
