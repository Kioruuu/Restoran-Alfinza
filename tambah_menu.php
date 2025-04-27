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

$id_masakan = "";
$nama_masakan = "";
$harga = "";
$stok = "";
$gambar_masakan = "no_image.png";

if(isset($_SESSION['edit_menu'])){
  $id = $_SESSION['edit_menu'];
  $query_data_edit = "select * from tb_masakan where id_masakan = $id";
  $sql_data_edit = mysqli_query($conn, $query_data_edit);
  $result_data_edit = mysqli_fetch_array($sql_data_edit);

  $id_masakan = $result_data_edit['id_masakan'];
  $nama_masakan = $result_data_edit['nama_masakan'];
  $harga = $result_data_edit['harga'];
  $stok = $result_data_edit['stok'];
  $gambar_masakan = $result_data_edit['gambar_masakan'];
}

// Set page title and active menu
$page_title = isset($_SESSION['edit_menu']) ? "Edit Menu" : "Tambah Menu";
$active_menu = "entri_referensi";

// Start output buffering
ob_start();
?>

<!-- Main content -->
<div class="p-4">
  <?php if($r['id_level'] == 1): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Form Section -->
      <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center mb-6">
          <svg class="w-6 h-6 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          <h2 class="text-xl font-bold text-gray-800">
            <?php echo isset($_SESSION['edit_menu']) ? 'Edit Menu' : 'Tambah Menu Baru'; ?>
          </h2>
        </div>

        <form action="" method="post" enctype="multipart/form-data" class="space-y-6">
          <!-- Nama Masakan -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Masakan</label>
            <input type="<?php echo isset($_SESSION['edit_menu']) ? 'hidden' : 'text'; ?>" 
              name="nama_masakan" 
              value="<?php echo $nama_masakan; ?>"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Masukkan nama masakan"
              <?php echo isset($_SESSION['edit_menu']) ? '' : 'required'; ?>>
            <?php if(isset($_SESSION['edit_menu'])): ?>
              <input type="text" value="<?php echo $nama_masakan; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" disabled>
            <?php endif; ?>
          </div>

          <!-- Harga -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Harga per Porsi</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
              <input type="number" 
                name="harga" 
                value="<?php echo $harga; ?>"
                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="0"
                required>
            </div>
          </div>

          <!-- Stok -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Stok Persediaan</label>
            <input type="number" 
              name="stok" 
              value="<?php echo $stok; ?>"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="0"
              required>
          </div>

          <!-- Gambar -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Masakan</label>
            <div class="space-y-2">
              <input type="file"
                name="gambar"
                accept="image/*"
                onchange="preview(this,'previewImage')"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              
              <div class="mt-2">
                <img id="previewImage" 
                  src="gambar/<?php echo $gambar_masakan; ?>" 
                  class="w-32 h-32 object-cover rounded-lg border border-gray-200">
              </div>
            </div>
          </div>

          <!-- Buttons -->
          <div class="flex space-x-3">
            <?php if(isset($_SESSION['edit_menu'])): ?>
              <button type="submit" name="ubah_menu" 
                class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                Simpan Perubahan
              </button>
            <?php else: ?>
              <button type="submit" name="tambah_menu"
                class="flex-1 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Menu
              </button>
            <?php endif; ?>

            <button type="submit" name="batal_menu"
              class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
              Batalkan
            </button>
          </div>
        </form>
      </div>

      <!-- Preview Section -->
      <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center mb-6">
          <svg class="w-6 h-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
          </svg>
          <h2 class="text-xl font-bold text-gray-800">Preview Menu</h2>
        </div>

        <div class="bg-gray-50 rounded-lg p-4">
          <div class="aspect-w-16 aspect-h-9 mb-4">
            <img id="menuPreview" 
              src="gambar/<?php echo $gambar_masakan; ?>" 
              class="w-full h-48 object-cover rounded-lg">
          </div>

          <div class="space-y-2">
            <h3 class="text-lg font-semibold text-gray-900" id="previewNama">
              <?php echo $nama_masakan ?: 'Nama Masakan'; ?>
            </h3>
            
            <div class="flex justify-between items-center text-sm">
              <span class="text-gray-600">Harga per Porsi:</span>
              <span class="font-medium text-gray-900" id="previewHarga">
                Rp. <?php echo number_format($harga ?: 0, 0, ',', '.'); ?>,-
              </span>
            </div>

            <div class="flex justify-between items-center text-sm">
              <span class="text-gray-600">Stok Tersedia:</span>
              <span class="font-medium text-gray-900" id="previewStok">
                <?php echo $stok ?: '0'; ?> porsi
              </span>
            </div>

            <div class="pt-2">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo ($stok > 0) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                <?php echo ($stok > 0) ? 'Tersedia' : 'Habis'; ?>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>

<script>
function preview(input, imgId) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    
    reader.onload = function(e) {
      document.getElementById(imgId).src = e.target.result;
      document.getElementById('menuPreview').src = e.target.result;
    }
    
    reader.readAsDataURL(input.files[0]);
  }
}

// Live preview untuk nama masakan
document.querySelector('input[name="nama_masakan"]').addEventListener('input', function(e) {
  document.getElementById('previewNama').textContent = e.target.value || 'Nama Masakan';
});

// Live preview untuk harga
document.querySelector('input[name="harga"]').addEventListener('input', function(e) {
  const harga = parseInt(e.target.value) || 0;
  document.getElementById('previewHarga').textContent = 
    'Rp. ' + harga.toLocaleString('id-ID') + ',-';
});

// Live preview untuk stok
document.querySelector('input[name="stok"]').addEventListener('input', function(e) {
  const stok = parseInt(e.target.value) || 0;
  document.getElementById('previewStok').textContent = stok + ' porsi';
  
  // Update status badge
  const badge = document.querySelector('.inline-flex');
  if (stok > 0) {
    badge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';
    badge.textContent = 'Tersedia';
  } else {
    badge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800';
    badge.textContent = 'Habis';
  }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
  const nama = document.querySelector('input[name="nama_masakan"]').value;
  const harga = document.querySelector('input[name="harga"]').value;
  const stok = document.querySelector('input[name="stok"]').value;
  
  if (!nama && !document.querySelector('input[name="nama_masakan"]').disabled) {
    e.preventDefault();
    alert('Nama masakan harus diisi!');
    return;
  }
  
  if (harga <= 0) {
    e.preventDefault();
    alert('Harga harus lebih dari 0!');
    return;
  }
  
  if (stok < 0) {
    e.preventDefault();
    alert('Stok tidak boleh negatif!');
    return;
  }
});
</script>

<?php
// Process form submission
if(isset($_POST['tambah_menu'])) {
  $nama = $_POST['nama_masakan'];
  $harga = $_POST['harga'];
  $stok = $_POST['stok'];
  
  // Handle image upload
  $gambar = $_FILES['gambar'];
  $gambar_name = $gambar['name'];
  $gambar_tmp = $gambar['tmp_name'];
  
  if($gambar_name) {
    $ext = pathinfo($gambar_name, PATHINFO_EXTENSION);
    $new_name = 'menu_' . time() . '.' . $ext;
    move_uploaded_file($gambar_tmp, "gambar/$new_name");
    $gambar_masakan = $new_name;
  }
  
  $query = "INSERT INTO tb_masakan (nama_masakan, harga, stok, gambar_masakan, status_masakan) 
            VALUES ('$nama', $harga, $stok, '$gambar_masakan', 'tersedia')";
            
  if(mysqli_query($conn, $query)) {
    echo "<script>
      alert('Menu berhasil ditambahkan!');
      window.location='entri_referensi.php';
    </script>";
  } else {
    echo "<script>alert('Gagal menambahkan menu!');</script>";
  }
}

if(isset($_POST['ubah_menu'])) {
  $id = $_SESSION['edit_menu'];
  $harga = $_POST['harga'];
  $stok = $_POST['stok'];
  
  // Handle image upload
  $gambar = $_FILES['gambar'];
  $gambar_name = $gambar['name'];
  $gambar_tmp = $gambar['tmp_name'];
  
  if($gambar_name) {
    $ext = pathinfo($gambar_name, PATHINFO_EXTENSION);
    $new_name = 'menu_' . time() . '.' . $ext;
    move_uploaded_file($gambar_tmp, "gambar/$new_name");
    $gambar_masakan = $new_name;
    
    // Delete old image if not default
    if($result_data_edit['gambar_masakan'] != 'no_image.png') {
      unlink("gambar/" . $result_data_edit['gambar_masakan']);
    }
  }
  
  $query = "UPDATE tb_masakan SET 
            harga = $harga,
            stok = $stok" . 
            ($gambar_name ? ", gambar_masakan = '$gambar_masakan'" : "") .
            " WHERE id_masakan = $id";
            
  if(mysqli_query($conn, $query)) {
    unset($_SESSION['edit_menu']);
    echo "<script>
      alert('Menu berhasil diupdate!');
      window.location='entri_referensi.php';
    </script>";
  } else {
    echo "<script>alert('Gagal mengupdate menu!');</script>";
  }
}

if(isset($_POST['batal_menu'])) {
  unset($_SESSION['edit_menu']);
  header("location: entri_referensi.php");
}

$content = ob_get_clean();
include 'template/layouts/main.php';
?>