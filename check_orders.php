<?php
include "connection/koneksi.php";

$query = "SELECT id_order, id_pengunjung, no_meja, total_harga, status_order FROM tb_order";
$result = mysqli_query($conn, $query);

echo "<h2>Data di tb_order:</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID Order</th><th>ID Pengunjung</th><th>No Meja</th><th>Total Harga</th><th>Status Order</th></tr>";

while($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['id_order'] . "</td>";
    echo "<td>" . $row['id_pengunjung'] . "</td>";
    echo "<td>" . $row['no_meja'] . "</td>";
    echo "<td>" . $row['total_harga'] . "</td>";
    echo "<td>" . $row['status_order'] . "</td>";
    echo "</tr>";
}

echo "</table>";
?> 