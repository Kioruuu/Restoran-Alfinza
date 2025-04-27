<?php
include "connection/koneksi.php";

// Fix status pesanan dari string kosong ('') menjadi NULL
$query = "UPDATE tb_pesan SET status_pesan = NULL WHERE status_pesan = ''";
$result = mysqli_query($conn, $query);

if ($result) {
    echo "Berhasil memperbaiki status pesanan!";
} else {
    echo "Gagal memperbaiki status pesanan: " . mysqli_error($conn);
}
?> 