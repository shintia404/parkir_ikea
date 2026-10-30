-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 01, 2026 at 08:39 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `parkir_ikea`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_area_parkir`
--

CREATE TABLE `tb_area_parkir` (
  `id_area` int NOT NULL,
  `nama_area` varchar(50) NOT NULL,
  `kapasitas` int NOT NULL,
  `terisi` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_area_parkir`
--

INSERT INTO `tb_area_parkir` (`id_area`, `nama_area`, `kapasitas`, `terisi`) VALUES
(1, 'Gedung 1', 50, 0),
(2, 'Gedung 2', 50, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tb_kendaraan`
--

CREATE TABLE `tb_kendaraan` (
  `id_kendaraan` int NOT NULL,
  `plat_nomor` varchar(15) NOT NULL,
  `jenis_kendaraan` varchar(20) NOT NULL,
  `warna` varchar(20) DEFAULT NULL,
  `pemilik` varchar(100) DEFAULT NULL,
  `id_user` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_kendaraan`
--

INSERT INTO `tb_kendaraan` (`id_kendaraan`, `plat_nomor`, `jenis_kendaraan`, `warna`, `pemilik`, `id_user`) VALUES
(11, 'D 1234 AA', 'motor', 'Hitam', 'Andi Setiawan', 1),
(12, 'D 2345 BB', 'mobil', 'Putih', 'Budi Santoso', 2),
(13, 'D 3456 CC', 'motor', 'Merah', 'Citra Lestari', 3),
(14, 'D 4567 DD', 'mobil', 'Hitam', 'Deni Saputra', 4),
(15, 'D 5678 EE', 'motor', 'Biru', 'Eka Putri', 5),
(16, 'D 6789 FF', 'mobil', 'Silver', 'Fajar Ramadhan', 6),
(17, 'D 7890 GG', 'motor', 'Putih', 'Gilang Pratama', 7),
(18, 'D 8901 HH', 'mobil', 'Merah', 'Hana Sari', 8),
(19, 'D 9012 II', 'motor', 'Hitam', 'Irfan Maulana', 9),
(20, 'D 0123 JJ', 'mobil', 'Biru', 'Joko Susanto', 10);

-- --------------------------------------------------------

--
-- Table structure for table `tb_log_aktivitas`
--

CREATE TABLE `tb_log_aktivitas` (
  `id_log` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `aktivitas` varchar(100) NOT NULL,
  `waktu_aktivitas` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_log_aktivitas`
--

INSERT INTO `tb_log_aktivitas` (`id_log`, `id_user`, `aktivitas`, `waktu_aktivitas`) VALUES
(1, 1, 'Login ke sistem', '2026-08-31 18:38:39'),
(2, 2, 'Menambahkan data kendaraan', '2026-08-31 18:38:39'),
(3, 3, 'Mencatat kendaraan masuk', '2026-08-31 18:38:39'),
(4, 4, 'Mencatat kendaraan keluar', '2026-08-31 18:38:39'),
(5, 5, 'Melihat laporan parkir', '2026-08-31 18:38:39'),
(6, 6, 'Mengubah data tarif', '2026-08-31 18:38:39'),
(7, 7, 'Menambahkan data kendaraan', '2026-08-31 18:38:39'),
(8, 8, 'Mencatat kendaraan masuk', '2026-08-31 18:38:39'),
(9, 9, 'Melihat laporan keuangan', '2026-08-31 18:38:39'),
(10, 10, 'Logout dari sistem', '2026-08-31 18:38:39');

-- --------------------------------------------------------

--
-- Table structure for table `tb_tarif`
--

CREATE TABLE `tb_tarif` (
  `id_tarif` int NOT NULL,
  `jenis_kendaraan` enum('motor','mobil','lainnya') NOT NULL,
  `tarif_per_jam` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_tarif`
--

INSERT INTO `tb_tarif` (`id_tarif`, `jenis_kendaraan`, `tarif_per_jam`) VALUES
(1, 'motor', 2000),
(2, 'mobil', 5000);

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int NOT NULL,
  `nama_lengkap` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('admin','petugas','owner') NOT NULL,
  `status_aktif` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `nama_lengkap`, `username`, `password`, `role`, `status_aktif`) VALUES
(1, 'Andi Setiawan', 'andi', '123456', 'admin', 1),
(2, 'Budi Santoso', 'budi', '123456', 'petugas', 1),
(3, 'Citra Lestari', 'citra', '123456', 'petugas', 1),
(4, 'Deni Saputra', 'deni', '123456', 'petugas', 1),
(5, 'Eka Putri', 'eka', '123456', 'owner', 1),
(6, 'Fajar Ramadhan', 'fajar', '123456', 'petugas', 1),
(7, 'Gilang Pratama', 'gilang', '123456', 'petugas', 1),
(8, 'Hana Sari', 'hana', '123456', 'petugas', 1),
(9, 'Irfan Maulana', 'irfan', '123456', 'owner', 1),
(10, 'Joko Susanto', 'joko', '123456', 'petugas', 1);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_parkir`
--

CREATE TABLE `transaksi_parkir` (
  `id_parkir` int NOT NULL,
  `id_kendaraan` int NOT NULL,
  `waktu_masuk` datetime NOT NULL,
  `waktu_keluar` datetime DEFAULT NULL,
  `id_tarif` int NOT NULL,
  `durasi_jam` int DEFAULT NULL,
  `biaya_total` int DEFAULT '0',
  `status` varchar(20) NOT NULL,
  `id_user` int NOT NULL,
  `id_area` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi_parkir`
--

INSERT INTO `transaksi_parkir` (`id_parkir`, `id_kendaraan`, `waktu_masuk`, `waktu_keluar`, `id_tarif`, `durasi_jam`, `biaya_total`, `status`, `id_user`, `id_area`) VALUES
(1, 1, '2026-08-31 07:00:00', '2026-08-31 09:00:00', 1, 2, 6000, 'keluar', 1, 1),
(2, 2, '2026-08-31 07:30:00', '2026-08-31 10:30:00', 1, 3, 9000, 'keluar', 2, 1),
(3, 3, '2026-08-31 08:00:00', '2026-08-31 10:00:00', 2, 2, 10000, 'keluar', 1, 2),
(4, 4, '2026-08-31 08:30:00', '2026-08-31 12:30:00', 1, 4, 12000, 'keluar', 2, 2),
(5, 5, '2026-08-31 09:00:00', '2026-08-31 12:00:00', 2, 3, 15000, 'keluar', 3, 1),
(6, 6, '2026-08-31 09:30:00', '2026-08-31 11:30:00', 1, 2, 6000, 'keluar', 1, 3),
(7, 7, '2026-08-31 10:00:00', '2026-08-31 14:00:00', 2, 4, 20000, 'keluar', 3, 3),
(8, 8, '2026-08-31 11:00:00', '2026-08-31 14:00:00', 1, 3, 9000, 'keluar', 2, 2),
(9, 9, '2026-08-31 12:00:00', '2026-08-31 15:00:00', 2, 3, 15000, 'keluar', 1, 1),
(10, 10, '2026-08-31 13:00:00', '2026-08-31 18:00:00', 1, 5, 15000, 'keluar', 3, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_area_parkir`
--
ALTER TABLE `tb_area_parkir`
  ADD PRIMARY KEY (`id_area`);

--
-- Indexes for table `tb_kendaraan`
--
ALTER TABLE `tb_kendaraan`
  ADD PRIMARY KEY (`id_kendaraan`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `tb_log_aktivitas`
--
ALTER TABLE `tb_log_aktivitas`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `tb_tarif`
--
ALTER TABLE `tb_tarif`
  ADD PRIMARY KEY (`id_tarif`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `transaksi_parkir`
--
ALTER TABLE `transaksi_parkir`
  ADD PRIMARY KEY (`id_parkir`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_area_parkir`
--
ALTER TABLE `tb_area_parkir`
  MODIFY `id_area` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tb_kendaraan`
--
ALTER TABLE `tb_kendaraan`
  MODIFY `id_kendaraan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tb_log_aktivitas`
--
ALTER TABLE `tb_log_aktivitas`
  MODIFY `id_log` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tb_tarif`
--
ALTER TABLE `tb_tarif`
  MODIFY `id_tarif` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `transaksi_parkir`
--
ALTER TABLE `transaksi_parkir`
  MODIFY `id_parkir` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_kendaraan`
--
ALTER TABLE `tb_kendaraan`
  ADD CONSTRAINT `tb_kendaraan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tb_log_aktivitas`
--
ALTER TABLE `tb_log_aktivitas`
  ADD CONSTRAINT `tb_log_aktivitas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
