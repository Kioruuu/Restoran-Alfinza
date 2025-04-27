<?php
include "connection/koneksi.php";
session_start();
ob_start();

$id = $_SESSION['id_user'];

if(isset($_SESSION['edit_menu'])){
  echo $_SESSION['edit_menu'];
  unset($_SESSION['edit_menu']);
}

if(!isset($_SESSION['username'])){
  header("location: index.php");
}

$query = "select * from tb_user natural join tb_level where id_user = $id";
$sql = mysqli_query($conn, $query);
$r = mysqli_fetch_array($sql);

// Set page title
$page_title = "Entri Referensi";
$active_menu = "entri_referensi";

// Start output buffering
ob_start();
?>

<!-- Main content -->
<div class="p-4">
  <?php if($r['id_level'] == 1): ?>
    <div class="bg-white rounded-lg shadow-lg p-6">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Referensi Makanan</h2>
        <a href="tambah_menu.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Tambah Menu
        </a>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php
        $query_data_makanan = "select * from tb_masakan order by id_masakan desc";
        $sql_data_makanan = mysqli_query($conn, $query_data_makanan);
        
        while($r_dt_makanan = mysqli_fetch_array($sql_data_makanan)):
        ?>
          <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:scale-105">
            <img src="gambar/<?php echo $r_dt_makanan['gambar_masakan']?>" alt="<?php echo $r_dt_makanan['nama_masakan']?>" class="w-full h-48 object-cover">
            
            <div class="p-4">
              <h3 class="text-lg font-semibold text-gray-800 mb-2"><?php echo $r_dt_makanan['nama_masakan']?></h3>
              
              <div class="space-y-2 text-sm text-gray-600">
                <div class="flex justify-between">
                  <span>Harga/Porsi:</span>
                  <span class="font-medium">Rp. <?php echo number_format($r_dt_makanan['harga'],0,',','.')?></span>
                </div>
                <div class="flex justify-between">
                  <span>Stok:</span>
                  <span class="font-medium"><?php echo $r_dt_makanan['stok']?> Porsi</span>
                </div>
              </div>

              <div class="mt-4 flex space-x-2">
                <form action="" method="post" class="flex-1">
                  <button type="submit" name="edit_menu" value="<?php echo $r_dt_makanan['id_masakan']?>" 
                    class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                  </button>
                </form>

                <form action="" method="post" class="flex-1">
                  <button type="submit" name="hapus_menu" value="<?php echo $r_dt_makanan['id_masakan']?>"
                    class="w-full bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg flex items-center justify-center"
                    onclick="return confirm('Yakin ingin menghapus menu ini?')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus
                  </button>
                </form>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php
// Process form submissions
if(isset($_REQUEST['hapus_menu'])){
  $id_masakan = $_REQUEST['hapus_menu'];

  $query_lihat = "select * from tb_masakan where id_masakan = $id_masakan";
  $sql_lihat = mysqli_query($conn, $query_lihat);
  $result_lihat = mysqli_fetch_array($sql_lihat);
  
  if(file_exists('gambar/'.$result_lihat['gambar_masakan'])){
    unlink('gambar/'.$result_lihat['gambar_masakan']);
  }
  
  $query_hapus_masakan = "delete from tb_masakan where id_masakan = $id_masakan";
  $sql_hapus_masakan = mysqli_query($conn, $query_hapus_masakan);
  
  if($sql_hapus_masakan){
    header('location: entri_referensi.php');
  }
}

if(isset($_REQUEST['edit_menu'])){
  $id_masakan = $_REQUEST['edit_menu'];
  $_SESSION['edit_menu'] = $id_masakan;
  header('location: tambah_menu.php');
}

$content = ob_get_clean();
include 'template/layouts/main.php';
?>