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
$uang = 0;

// Set tanggal
$today = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$tanggal_indo = date('d-m-Y', strtotime($today));

// Data restoran default
$nama_resto = "RESTAURANT CEPAT SAJI";
$alamat_resto = "Jl. Imam Bonjol No. 103 Ds. Tembarak, Kec. Kertosono, Kab. Nganjuk, Jatim";
$telp_resto = "+6289 xxx xxx xxx";

// Coba cek apakah tabel tb_restoran ada
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'tb_restoran'");
if(mysqli_num_rows($check_table) > 0) {
  // Tabel ada, ambil data dari tabel
  $query_info = "SELECT * FROM tb_restoran LIMIT 1";
  $sql_info = mysqli_query($conn, $query_info);
  if($sql_info && mysqli_num_rows($sql_info) > 0) {
    $resto_info = mysqli_fetch_array($sql_info);
    $nama_resto = $resto_info['nama_resto'] ?: $nama_resto;
    $alamat_resto = $resto_info['alamat'] ?: $alamat_resto;
    $telp_resto = $resto_info['telp'] ?: $telp_resto;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cetak Laporan Penjualan <?php echo $tanggal_indo; ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
    
    body {
      font-family: 'Poppins', sans-serif;
      background: #f1f5f9;
    }
    
    .print-shadow {
      box-shadow: 0 0 60px rgba(0, 0, 0, 0.1);
    }
    
    @media print {
      body {
        background: white;
        font-size: 12pt;
      }
      .no-print {
        display: none !important;
      }
      .print-area {
        max-width: 100%;
        margin: 0;
        padding: 0;
        box-shadow: none;
      }
      @page {
        margin: 1.5cm;
      }
      table {
        page-break-inside: auto;
      }
      tr {
        page-break-inside: avoid;
        page-break-after: auto;
      }
      thead {
        display: table-header-group;
      }
      tfoot {
        display: table-footer-group;
      }
    }
  </style>
</head>
<body class="min-h-screen py-12">
  <div class="max-w-5xl mx-auto px-4">
    <!-- Action Buttons -->
    <div class="mb-6 flex justify-between items-center no-print">
      <a href="generate_laporan.php" 
        class="inline-flex items-center px-6 py-3 bg-white/20 hover:bg-white/30 text-gray-700 rounded-xl backdrop-blur-sm transition-all duration-200 border border-gray-300 bg-white shadow-sm">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Generate Laporan
      </a>
      
      <button onclick="window.print()" 
        class="inline-flex items-center px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl backdrop-blur-sm transition-all duration-200 shadow-lg shadow-emerald-500/30">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Cetak Laporan
      </button>
    </div>

    <!-- Report Content -->
    <div class="bg-white rounded-xl print-shadow print-area">
      <div class="p-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <div class="mb-3">
            <svg class="w-16 h-16 mx-auto text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/>
            </svg>
          </div>
          <h1 class="text-3xl font-bold text-gray-800">
            <?php echo $nama_resto; ?>
          </h1>
          <p class="text-gray-500 text-sm mt-2">
            <?php echo $alamat_resto; ?><br>
            Telp. <?php echo $telp_resto; ?>
          </p>
        </div>

        <div class="h-px bg-gradient-to-r from-transparent via-gray-400/50 to-transparent my-6"></div>

        <!-- Report Title -->
        <div class="text-center mb-8">
          <h2 class="text-2xl font-bold text-gray-800">LAPORAN PENJUALAN</h2>
          <p class="text-gray-600 mt-1">Tanggal: <?php echo $tanggal_indo; ?></p>
          <p class="text-gray-500 text-sm mt-1">Dicetak oleh: <?php echo $nama_user; ?></p>
        </div>

        <!-- Sales Report -->
        <div class="overflow-x-auto mb-8">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-100">
                <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Menu</th>
                <th class="py-3 px-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                <th class="py-3 px-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Terjual</th>
                <th class="py-3 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                <th class="py-3 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <?php
              $no = 1;
              $query_lihat_menu = "select * from tb_masakan ORDER BY nama_masakan ASC";
              $sql_lihat_menu = mysqli_query($conn, $query_lihat_menu);

              while($r_lihat_menu = mysqli_fetch_array($sql_lihat_menu)){
                $id_masakan = $r_lihat_menu['id_masakan'];
                
                // Check if tb_transaksi table exists
                $check_trans_table = mysqli_query($conn, "SHOW TABLES LIKE 'tb_transaksi'");
                
                if(mysqli_num_rows($check_trans_table) > 0) {
                  // Get items sold using direct join to tb_order and tb_pesan, without referring to tb_transaksi
                  $query_jumlah = "SELECT SUM(p.jumlah) as jumlah_terjual 
                                  FROM tb_pesan p 
                                  JOIN tb_order o ON p.id_order = o.id_order 
                                  WHERE p.id_masakan = $id_masakan 
                                  AND p.status_pesan = 'sudah' 
                                  AND o.status_order = 'sudah bayar'
                                  AND DATE(o.waktu_pesan) = '$today'";
                } else {
                  // Same query as above if tb_transaksi doesn't exist
                  $query_jumlah = "SELECT SUM(p.jumlah) as jumlah_terjual 
                                  FROM tb_pesan p 
                                  JOIN tb_order o ON p.id_order = o.id_order 
                                  WHERE p.id_masakan = $id_masakan 
                                  AND p.status_pesan = 'sudah' 
                                  AND o.status_order = 'sudah bayar'
                                  AND DATE(o.waktu_pesan) = '$today'";
                }
                                
                $sql_jumlah = mysqli_query($conn, $query_jumlah);
                $result_jumlah = mysqli_fetch_array($sql_jumlah);

                $jml = ($result_jumlah['jumlah_terjual'] != null) ? $result_jumlah['jumlah_terjual'] : 0;
                $total = $jml * $r_lihat_menu['harga'];
                $uang += $total;
                
                // Display all items with sales
                if ($jml > 0) {
              ?>
                <tr class="hover:bg-gray-50/50 transition-colors duration-200">
                  <td class="py-4 px-4 text-sm text-gray-600"><?php echo $no++;?>.</td>
                  <td class="py-4 px-4 text-sm font-medium text-gray-800"><?php echo $r_lihat_menu['nama_masakan'];?></td>
                  <td class="py-4 px-4 text-sm text-center text-gray-600"><?php echo $r_lihat_menu['stok'];?></td>
                  <td class="py-4 px-4 text-sm text-center font-medium text-gray-800"><?php echo $jml;?></td>
                  <td class="py-4 px-4 text-sm text-right text-gray-600">
                    Rp. <?php echo number_format($r_lihat_menu['harga'],0,',','.');?>,-
                  </td>
                  <td class="py-4 px-4 text-sm text-right font-medium text-gray-800">
                    Rp. <?php echo number_format($total,0,',','.');?>,-
                  </td>
                </tr>
              <?php 
                }
              } 
              
              // If no sales
              if ($no == 1) {
              ?>
                <tr>
                  <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                    Belum ada penjualan untuk tanggal <?php echo $tanggal_indo; ?>
                  </td>
                </tr>
              <?php
              }
              ?>
            </tbody>
            <tfoot>
              <tr class="bg-gray-50">
                <td colspan="4" class="py-4 px-4"></td>
                <td class="py-4 px-4 text-sm font-bold text-gray-700 text-right">TOTAL:</td>
                <td class="py-4 px-4 text-sm font-bold text-purple-600 text-right">
                  Rp. <?php echo number_format($uang,0,',','.');?>,-
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="h-px bg-gradient-to-r from-transparent via-gray-400/50 to-transparent mb-8"></div>

        <!-- Footer -->
        <div class="mt-12 text-right">
          <p class="text-gray-600"><?php echo date('d F Y'); ?></p>
          <div class="h-20"></div>
          <p class="text-gray-800 font-medium"><?php echo $nama_user; ?></p>
          <p class="text-gray-500 text-sm">Kasir <?php echo $nama_resto; ?></p>
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