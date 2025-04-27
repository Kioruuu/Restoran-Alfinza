# 🍽️ Sistem Manajemen Restoran

Aplikasi web manajemen restoran berbasis PHP Native dengan tampilan modern menggunakan Tailwind CSS. Project ini dibuat khusus oleh Alfinza Untuk menamatkan LSP
Dengan ini mudah mudahan alfinza dapat Lulus LSP Dengan Nilai bagus bahkan sempurna.

## 📋 Fitur Utama

### 👥 Hak Akses Per Level
| Fitur | Administrator | Waiter | Kasir | Owner |
|-------|--------------|--------|-------|-------|
| Login | ✅ | ✅ | ✅ | ✅ |
| Logout | ✅ | ✅ | ✅ | ✅ |
| Entri Meja | ✅ | ❌ | ❌ | ❌ |
| Entri Barang | ✅ | ❌ | ❌ | ❌ |
| Entri Order | ❌ | ✅ | ❌ | ❌ |
| Entri Transaksi | ❌ | ❌ | ✅ | ❌ |
| Generate Laporan | ❌ | ✅ | ✅ | ✅ |

### 🛠️ Modul-modul
1. **Entri Meja & Barang** (Admin)
   - Manajemen data meja
   - CRUD menu makanan/minuman
   - Upload gambar menu
   - Update stok

2. **Entri Order** (Waiter)
   - Input pesanan per meja
   - Pilih menu dengan gambar
   - Kalkulasi total otomatis
   - Status order realtime

3. **Entri Transaksi** (Kasir)
   - Proses pembayaran
   - Detail order & total
   - Status pembayaran
   - Riwayat transaksi

4. **Generate Laporan** (Waiter, Kasir, Owner)
   - Filter periode (Harian/Bulanan/Tahunan)
   - Laporan penjualan detail
   - Total pendapatan
   - Print-ready format

## 💻 Teknologi

- PHP 7.4+ (Native)
- MySQL/MariaDB
- Tailwind CSS
- JavaScript
- SweetAlert2

## 📦 Prasyarat

- XAMPP/WAMP/LAMP
- PHP >= 7.4
- MySQL/MariaDB
- Web Browser Modern

## 🚀 Instalasi

1. Clone repository ini ke folder htdocs:
```bash
git clone [url-repo] restoran
```

2. Buat database baru bernama 'db_restoran'

3. Import file SQL:
```bash
db_restoran.sql
```

4. Konfigurasi database di `connection/koneksi.php`:
```php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "nama_db";
```

5. Akses aplikasi:
```
http://localhost/restoran
```

## 👤 Default Login

### Admin
- Username: admin
- Password: 123

### Waiter
- Username: waiter1
- Password: 123

### Kasir
- Username: kasir1 
- Password: 123

### Owner
- Username: owner
- Password: 123

## 📱 Screenshot

For LSP

## 🔒 Keamanan
- Session management
- Password hashing
- Input validation
- SQL injection prevention

## 📝 Struktur Database

### Tabel Utama
- tb_user
- tb_level
- tb_masakan
- tb_order
- tb_pesan
- tb_transaksi

## 🤝 Kontribusi
Silakan buat issue atau pull request untuk kontribusi.

## 📄 Lisensi
[MIT License](LICENSE)

## 👨‍💻 Developer
[Alfinza/KioruuuIndustries]

