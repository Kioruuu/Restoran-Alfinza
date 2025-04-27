-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2025 at 04:31 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restoran_alfinza`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_level`
--

CREATE TABLE `tb_level` (
  `id_level` int(11) NOT NULL,
  `nama_level` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_level`
--

INSERT INTO `tb_level` (`id_level`, `nama_level`) VALUES
(1, 'Administrator'),
(2, 'Waiter'),
(3, 'Kasir'),
(4, 'Owner'),
(5, 'Pelanggan');

-- --------------------------------------------------------

--
-- Table structure for table `tb_masakan`
--

CREATE TABLE `tb_masakan` (
  `id_masakan` int(11) NOT NULL,
  `nama_masakan` varchar(150) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `status_masakan` varchar(150) NOT NULL DEFAULT 'tersedia',
  `gambar_masakan` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_masakan`
--

INSERT INTO `tb_masakan` (`id_masakan`, `nama_masakan`, `harga`, `stok`, `status_masakan`, `gambar_masakan`) VALUES
(14, 'Sate Ayam', 30000.00, 37, 'tersedia', 'Sate Ayam.jpeg'),
(15, 'Sayur Asem', 15000.00, 45, 'tersedia', 'Sayur Asem.jpeg'),
(16, 'Ayam Geprek', 25000.00, 40, 'tersedia', 'Ayam Geprek.jpeg'),
(17, 'Nasi Pecel', 15000.00, 49, 'tersedia', 'Nasi Pecel.jpg'),
(18, 'Cincau', 8000.00, 95, 'tersedia', 'Cincau.jpg'),
(19, 'Nasi Putih', 5000.00, 196, 'tersedia', 'no_image.png'),
(20, 'Es Teh Manis', 5000.00, 99, 'tersedia', 'no_image.png'),
(21, 'Es Jeruk', 7000.00, 77, 'tersedia', 'no_image.png'),
(22, 'Soto Ayam', 20000.00, 47, 'tersedia', 'no_image.png'),
(23, 'Gado-gado', 15000.00, 29, 'tersedia', 'no_image.png');

-- --------------------------------------------------------

--
-- Table structure for table `tb_order`
--

CREATE TABLE `tb_order` (
  `id_order` int(11) NOT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `id_pengunjung` int(11) NOT NULL,
  `waktu_pesan` datetime NOT NULL DEFAULT current_timestamp(),
  `no_meja` varchar(50) NOT NULL,
  `total_harga` decimal(10,2) NOT NULL DEFAULT 0.00,
  `uang_bayar` decimal(10,2) DEFAULT NULL,
  `uang_kembali` decimal(10,2) DEFAULT NULL,
  `status_order` enum('pending','diproses','sudah bayar','batal') NOT NULL DEFAULT 'pending',
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_order`
--

INSERT INTO `tb_order` (`id_order`, `id_admin`, `id_pengunjung`, `waktu_pesan`, `no_meja`, `total_harga`, `uang_bayar`, `uang_kembali`, `status_order`, `catatan`) VALUES
(15, NULL, 1, '2025-04-17 14:54:16', '6', 322000.00, 500000.00, 178000.00, 'sudah bayar', NULL),
(16, NULL, 1, '2025-04-17 14:54:48', '1', 322000.00, 500000.00, 178000.00, 'sudah bayar', NULL),
(19, NULL, 1, '2025-04-17 15:04:01', '2', 14000.00, 20000.00, 6000.00, 'sudah bayar', NULL),
(23, NULL, 3, '2025-04-17 15:14:26', '2', 106000.00, 120000.00, 14000.00, 'sudah bayar', NULL),
(25, NULL, 1, '2025-04-24 17:50:15', '2', 7000.00, NULL, NULL, '', NULL),
(27, NULL, 1, '2025-04-24 17:52:21', '6', 50000.00, NULL, NULL, '', NULL),
(29, NULL, 1, '2025-04-24 17:57:47', '1', 60000.00, NULL, NULL, '', NULL),
(31, NULL, 1, '2025-04-24 17:58:27', '3', 75000.00, NULL, NULL, '', NULL),
(33, NULL, 1, '2025-04-24 21:51:28', '4', 14000.00, 15000.00, 1000.00, 'sudah bayar', NULL),
(35, NULL, 2, '2025-04-24 22:06:01', '5', 30000.00, 50000.00, 20000.00, 'sudah bayar', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_order_meja`
--

CREATE TABLE `tb_order_meja` (
  `id_order_meja` int(11) NOT NULL,
  `id_order` int(11) NOT NULL,
  `no_meja` int(11) NOT NULL,
  `status_meja` enum('kosong','terisi') NOT NULL DEFAULT 'terisi'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_pesan`
--

CREATE TABLE `tb_pesan` (
  `id_pesan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_order` int(11) DEFAULT NULL,
  `id_masakan` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `status_pesan` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_pesan`
--

INSERT INTO `tb_pesan` (`id_pesan`, `id_user`, `id_order`, `id_masakan`, `jumlah`, `status_pesan`) VALUES
(1, 1, NULL, 23, 1, 'sudah'),
(2, 1, NULL, 18, 3, 'sudah'),
(26, 1, NULL, 16, 3, 'sudah'),
(28, 1, NULL, 14, 3, 'sudah'),
(29, 1, NULL, 17, 1, 'sudah'),
(30, 1, NULL, 21, 1, 'sudah'),
(31, 1, NULL, 20, 1, 'sudah'),
(41, 1, NULL, 21, 2, 'sudah'),
(42, 1, 16, 15, 2, 'sudah'),
(43, 1, 16, 21, 1, 'sudah'),
(44, 1, 16, 21, 2, 'sudah'),
(45, 1, 16, 16, 2, 'sudah'),
(46, 1, 16, 21, 2, 'sudah'),
(47, 1, 16, 19, 2, 'sudah'),
(48, 1, 16, 16, 2, 'sudah'),
(49, 1, 16, 22, 2, 'sudah'),
(50, 1, 16, 19, 2, 'sudah'),
(51, 1, 16, 21, 2, 'sudah'),
(52, 1, 16, 21, 3, 'sudah'),
(53, 1, 16, 21, 2, 'sudah'),
(54, 1, 16, 21, 2, 'sudah'),
(55, 1, 16, 21, 2, 'sudah'),
(56, 1, 16, 22, 1, 'sudah'),
(57, 1, NULL, 21, 2, 'sudah'),
(59, 3, 23, 15, 1, 'sudah'),
(60, 3, 23, 16, 3, 'sudah'),
(61, 3, 23, 18, 2, 'sudah'),
(66, 1, 33, 21, 2, 'sudah'),
(67, 2, 35, 15, 2, 'sudah');

-- --------------------------------------------------------

--
-- Table structure for table `tb_restoran`
--

CREATE TABLE `tb_restoran` (
  `id_restoran` int(11) NOT NULL,
  `nama_resto` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `telp` varchar(20) NOT NULL,
  `email` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_restoran`
--

INSERT INTO `tb_restoran` (`id_restoran`, `nama_resto`, `alamat`, `telp`, `email`) VALUES
(1, 'RESTAURANT CEPAT SAJI', 'Jl. Imam Bonjol No. 103 Ds. Tembarak, Kec. Kertosono, Kab. Nganjuk, Jatim', '+6289 xxx xxx xxx', 'exsample@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `tb_stok`
--

CREATE TABLE `tb_stok` (
  `id_stok` int(11) NOT NULL,
  `id_pesan` int(11) NOT NULL,
  `jumlah_terjual` int(11) DEFAULT NULL,
  `status_cetak` varchar(150) NOT NULL DEFAULT 'belum cetak'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_stok`
--

INSERT INTO `tb_stok` (`id_stok`, `id_pesan`, `jumlah_terjual`, `status_cetak`) VALUES
(1, 1, 1, 'belum cetak'),
(2, 41, 2, 'belum cetak'),
(3, 42, 2, 'belum cetak'),
(4, 43, 1, 'belum cetak'),
(6, 44, 2, 'belum cetak'),
(7, 45, 2, 'belum cetak'),
(9, 46, 2, 'belum cetak'),
(10, 47, 2, 'belum cetak'),
(11, 48, 2, 'belum cetak'),
(12, 49, 2, 'belum cetak'),
(13, 50, 2, 'belum cetak'),
(14, 51, 2, 'belum cetak'),
(15, 42, 2, 'belum cetak'),
(16, 43, 1, 'belum cetak'),
(17, 44, 2, 'belum cetak'),
(18, 45, 2, 'belum cetak'),
(19, 46, 2, 'belum cetak'),
(20, 47, 2, 'belum cetak'),
(21, 48, 2, 'belum cetak'),
(22, 49, 2, 'belum cetak'),
(23, 50, 2, 'belum cetak'),
(24, 51, 2, 'belum cetak'),
(25, 52, 3, 'belum cetak'),
(26, 53, 2, 'belum cetak'),
(27, 54, 2, 'belum cetak'),
(28, 55, 2, 'belum cetak'),
(29, 56, 1, 'belum cetak'),
(30, 42, 2, 'belum cetak'),
(31, 43, 1, 'belum cetak'),
(32, 44, 2, 'belum cetak'),
(33, 45, 2, 'belum cetak'),
(34, 46, 2, 'belum cetak'),
(35, 47, 2, 'belum cetak'),
(36, 48, 2, 'belum cetak'),
(37, 49, 2, 'belum cetak'),
(38, 50, 2, 'belum cetak'),
(39, 51, 2, 'belum cetak'),
(40, 52, 3, 'belum cetak'),
(41, 53, 2, 'belum cetak'),
(42, 54, 2, 'belum cetak'),
(43, 55, 2, 'belum cetak'),
(44, 56, 1, 'belum cetak'),
(46, 59, 1, 'belum cetak'),
(47, 60, 3, 'belum cetak'),
(48, 61, 2, 'belum cetak'),
(55, 66, 2, 'belum cetak'),
(56, 67, 2, 'belum cetak');

-- --------------------------------------------------------

--
-- Table structure for table `tb_transaksi`
--

CREATE TABLE `tb_transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_order` int(11) NOT NULL,
  `tanggal_transaksi` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_transaksi`
--

INSERT INTO `tb_transaksi` (`id_transaksi`, `id_order`, `tanggal_transaksi`) VALUES
(2, 15, '2025-04-17 14:59:21'),
(3, 16, '2025-04-17 14:59:36'),
(5, 19, '2025-04-17 15:04:58'),
(8, 23, '2025-04-17 15:15:45'),
(10, 33, '2025-04-24 22:05:05'),
(11, 35, '2025-04-24 22:06:38');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `nama_user` varchar(150) NOT NULL,
  `id_level` int(11) NOT NULL,
  `status` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `username`, `password`, `nama_user`, `id_level`, `status`) VALUES
(1, 'admin', '123', 'Admin Restoran', 1, 'aktif'),
(2, 'waiter1', '123', 'Waiter 1', 2, 'aktif'),
(3, 'kasir1', '123', 'Kasir 1', 3, 'aktif'),
(4, 'owner', '123', 'Owner Restoran', 4, 'aktif'),
(5, 'pelanggan1', '123', 'Pelanggan 1', 5, 'aktif');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_level`
--
ALTER TABLE `tb_level`
  ADD PRIMARY KEY (`id_level`);

--
-- Indexes for table `tb_masakan`
--
ALTER TABLE `tb_masakan`
  ADD PRIMARY KEY (`id_masakan`);

--
-- Indexes for table `tb_order`
--
ALTER TABLE `tb_order`
  ADD PRIMARY KEY (`id_order`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_pengunjung` (`id_pengunjung`);

--
-- Indexes for table `tb_order_meja`
--
ALTER TABLE `tb_order_meja`
  ADD PRIMARY KEY (`id_order_meja`),
  ADD KEY `id_order` (`id_order`);

--
-- Indexes for table `tb_pesan`
--
ALTER TABLE `tb_pesan`
  ADD PRIMARY KEY (`id_pesan`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_order` (`id_order`),
  ADD KEY `id_masakan` (`id_masakan`);

--
-- Indexes for table `tb_restoran`
--
ALTER TABLE `tb_restoran`
  ADD PRIMARY KEY (`id_restoran`);

--
-- Indexes for table `tb_stok`
--
ALTER TABLE `tb_stok`
  ADD PRIMARY KEY (`id_stok`),
  ADD KEY `id_pesan` (`id_pesan`);

--
-- Indexes for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD UNIQUE KEY `id_order` (`id_order`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_level` (`id_level`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_level`
--
ALTER TABLE `tb_level`
  MODIFY `id_level` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_masakan`
--
ALTER TABLE `tb_masakan`
  MODIFY `id_masakan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tb_order`
--
ALTER TABLE `tb_order`
  MODIFY `id_order` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `tb_order_meja`
--
ALTER TABLE `tb_order_meja`
  MODIFY `id_order_meja` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_pesan`
--
ALTER TABLE `tb_pesan`
  MODIFY `id_pesan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `tb_restoran`
--
ALTER TABLE `tb_restoran`
  MODIFY `id_restoran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_stok`
--
ALTER TABLE `tb_stok`
  MODIFY `id_stok` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_order`
--
ALTER TABLE `tb_order`
  ADD CONSTRAINT `tb_order_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `tb_user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_order_ibfk_2` FOREIGN KEY (`id_pengunjung`) REFERENCES `tb_user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_order_meja`
--
ALTER TABLE `tb_order_meja`
  ADD CONSTRAINT `tb_order_meja_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `tb_order` (`id_order`) ON DELETE CASCADE;

--
-- Constraints for table `tb_pesan`
--
ALTER TABLE `tb_pesan`
  ADD CONSTRAINT `tb_pesan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_pesan_ibfk_2` FOREIGN KEY (`id_order`) REFERENCES `tb_order` (`id_order`) ON DELETE SET NULL,
  ADD CONSTRAINT `tb_pesan_ibfk_3` FOREIGN KEY (`id_masakan`) REFERENCES `tb_masakan` (`id_masakan`) ON DELETE CASCADE;

--
-- Constraints for table `tb_stok`
--
ALTER TABLE `tb_stok`
  ADD CONSTRAINT `tb_stok_ibfk_1` FOREIGN KEY (`id_pesan`) REFERENCES `tb_pesan` (`id_pesan`) ON DELETE CASCADE;

--
-- Constraints for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD CONSTRAINT `fk_transaksi_order` FOREIGN KEY (`id_order`) REFERENCES `tb_order` (`id_order`) ON DELETE CASCADE;

--
-- Constraints for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD CONSTRAINT `tb_user_ibfk_1` FOREIGN KEY (`id_level`) REFERENCES `tb_level` (`id_level`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
