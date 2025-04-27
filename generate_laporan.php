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

$nama_user = $r['nama_user'];
$uang = 0;

// Set default periode dan tanggal
$periode = isset($_GET['periode']) ? $_GET['periode'] : 'harian';
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Format tampilan tanggal
switch($periode) {
    case 'harian':
        $display_date = date('d-m-Y', strtotime($tanggal));
        $sql_date_condition = "DATE(o.waktu_pesan) = '$tanggal'";
        break;
    case 'bulanan':
        $display_date = date('F Y', strtotime($bulan));
        $sql_date_condition = "DATE_FORMAT(o.waktu_pesan, '%Y-%m') = '$bulan'";
        break;
    case 'tahunan':
        $display_date = $tahun;
        $sql_date_condition = "YEAR(o.waktu_pesan) = '$tahun'";
        break;
    default:
        $display_date = date('d-m-Y');
        $sql_date_condition = "DATE(o.waktu_pesan) = CURDATE()";
}

// Set page title
$page_title = "Generate Laporan";
$active_menu = "generate_laporan";

// Start output buffering
ob_start();
?>

<!-- Main content -->
<div class="p-4" id="printArea">
  <?php if($r['id_level'] == 1 || $r['id_level'] == 2 || $r['id_level'] == 3 || $r['id_level'] == 4): ?>
    <!-- Filter Section -->
    <div class="mb-6 bg-white rounded-lg shadow-lg p-6 no-print">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">Filter Laporan</h3>
      <form action="" method="GET" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Periode Select -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
            <select name="periode" id="periode" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" onchange="toggleDateInputs()">
              <option value="harian" <?php echo $periode == 'harian' ? 'selected' : ''; ?>>Harian</option>
              <option value="bulanan" <?php echo $periode == 'bulanan' ? 'selected' : ''; ?>>Bulanan</option>
              <option value="tahunan" <?php echo $periode == 'tahunan' ? 'selected' : ''; ?>>Tahunan</option>
            </select>
          </div>

          <!-- Tanggal Input (untuk Harian) -->
          <div id="tanggalInput" class="<?php echo $periode != 'harian' ? 'hidden' : ''; ?>">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
            <input type="date" name="tanggal" value="<?php echo $tanggal; ?>" 
                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
          </div>

          <!-- Bulan Input (untuk Bulanan) -->
          <div id="bulanInput" class="<?php echo $periode != 'bulanan' ? 'hidden' : ''; ?>">
            <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
            <input type="month" name="bulan" value="<?php echo $bulan; ?>"
                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
          </div>

          <!-- Tahun Input (untuk Tahunan) -->
          <div id="tahunInput" class="<?php echo $periode != 'tahunan' ? 'hidden' : ''; ?>">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
            <select name="tahun" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
              <?php 
              $current_year = date('Y');
              for($i = $current_year; $i >= $current_year - 5; $i--) {
                  echo "<option value='$i'" . ($tahun == $i ? 'selected' : '') . ">$i</option>";
              }
              ?>
            </select>
          </div>

          <!-- Submit Button -->
          <div class="flex items-end">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
              </svg>
              Filter
            </button>
          </div>
        </div>
      </form>
    </div>

    <!-- Sales Report -->
    <div class="grid grid-cols-1 gap-6">
      <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center">
            <svg class="w-6 h-6 text-blue-500 mr-2 no-print" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h2 class="text-2xl font-bold text-gray-800">
              Laporan Penjualan 
              <?php echo ucfirst($periode) . ': ' . $display_date; ?>
            </h2>
          </div>
          
          <button onclick="printReport()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center no-print">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak Laporan
          </button>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">No.</th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Nama Menu</th>
                <th class="px-4 py-2 text-center text-sm font-medium text-gray-500">Sisa Stok</th>
                <th class="px-4 py-2 text-center text-sm font-medium text-gray-500">Jumlah Terjual</th>
                <th class="px-4 py-2 text-right text-sm font-medium text-gray-500">Harga</th>
                <th class="px-4 py-2 text-right text-sm font-medium text-gray-500">Total Masukan</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <?php
              $no = 1;
              $query_lihat_menu = "select * from tb_masakan";
              $sql_lihat_menu = mysqli_query($conn, $query_lihat_menu);

              while($r_lihat_menu = mysqli_fetch_array($sql_lihat_menu)){
                $id_masakan = $r_lihat_menu['id_masakan'];
                
                // Check if tb_transaksi table exists
                $check_trans_table = mysqli_query($conn, "SHOW TABLES LIKE 'tb_transaksi'");
                
                if(mysqli_num_rows($check_trans_table) > 0) {
                  // Get items sold based on period from tb_transaksi join tb_order and tb_pesan
                  $query_jumlah = "SELECT SUM(p.jumlah) as jumlah_terjual 
                                  FROM tb_pesan p 
                                  JOIN tb_order o ON p.id_order = o.id_order 
                                  JOIN tb_transaksi t ON o.id_order = t.id_order
                                  WHERE p.id_masakan = $id_masakan 
                                  AND p.status_pesan = 'sudah' 
                                  AND o.status_order = 'sudah bayar'
                                  AND $sql_date_condition";
                } else {
                  // Fallback to just tb_order and tb_pesan if tb_transaksi doesn't exist
                  $query_jumlah = "SELECT SUM(p.jumlah) as jumlah_terjual 
                                  FROM tb_pesan p 
                                  JOIN tb_order o ON p.id_order = o.id_order 
                                  WHERE p.id_masakan = $id_masakan 
                                  AND p.status_pesan = 'sudah' 
                                  AND o.status_order = 'sudah bayar'
                                  AND $sql_date_condition";
                }
                                
                $sql_jumlah = mysqli_query($conn, $query_jumlah);
                $result_jumlah = mysqli_fetch_array($sql_jumlah);

                $jml = ($result_jumlah['jumlah_terjual'] != null) ? $result_jumlah['jumlah_terjual'] : 0;
                $total = $jml * $r_lihat_menu['harga'];
                $uang += $total;
                
                // Display all items with sales
                if ($jml > 0) {
              ?>
                <tr>
                  <td class="px-4 py-2 text-sm text-gray-900"><?php echo $no++;?>.</td>
                  <td class="px-4 py-2 text-sm text-gray-900"><?php echo $r_lihat_menu['nama_masakan'];?></td>
                  <td class="px-4 py-2 text-sm text-center text-gray-900"><?php echo $r_lihat_menu['stok'];?></td>
                  <td class="px-4 py-2 text-sm text-center text-gray-900"><?php echo $jml;?></td>
                  <td class="px-4 py-2 text-sm text-right text-gray-900">
                    Rp. <?php echo number_format($r_lihat_menu['harga'],0,',','.');?>,-
                  </td>
                  <td class="px-4 py-2 text-sm text-right font-medium text-gray-900">
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
                  <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                    Belum ada penjualan untuk periode ini
                  </td>
                </tr>
              <?php
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Summary Card -->
      <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <svg class="w-8 h-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
              <p class="text-sm font-medium text-gray-500">
                Total Pendapatan <?php echo ucfirst($periode); ?>: <?php echo $display_date; ?>
              </p>
              <h3 class="text-2xl font-bold text-gray-900">
                Rp. <?php echo number_format($uang,0,',','.');?>,-
              </h3>
            </div>
          </div>

          <div class="hidden sm:flex items-center space-x-2 text-green-500 no-print">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            <span class="text-sm font-medium">Pendapatan</span>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>

<style>
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
  }
  nav, footer, video, audio {
    display: none;
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

<script>
function toggleDateInputs() {
    const periode = document.getElementById('periode').value;
    const tanggalInput = document.getElementById('tanggalInput');
    const bulanInput = document.getElementById('bulanInput');
    const tahunInput = document.getElementById('tahunInput');
    
    // Hide all inputs first
    tanggalInput.classList.add('hidden');
    bulanInput.classList.add('hidden');
    tahunInput.classList.add('hidden');
    
    // Show relevant input based on selected period
    if (periode === 'harian') {
        tanggalInput.classList.remove('hidden');
    } else if (periode === 'bulanan') {
        bulanInput.classList.remove('hidden');
    } else if (periode === 'tahunan') {
        tahunInput.classList.remove('hidden');
    }
}

function printReport() {
    window.print();
}
</script>

<?php
$content = ob_get_clean();
include 'template/layouts/main.php';
?>