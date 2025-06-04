-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Jun 2025 pada 11.39
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wsatopup`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `hargatopup`
--

CREATE TABLE `hargatopup` (
  `hargaid` char(6) NOT NULL,
  `idgame` char(7) DEFAULT NULL,
  `nominal` varchar(5) DEFAULT NULL,
  `harga` int(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `hargatopup`
--

INSERT INTO `hargatopup` (`hargaid`, `idgame`, `nominal`, `harga`) VALUES
('PRC001', 'PLID001', '5928', 50000),
('PRC002', 'PLID002', '1200', 10000),
('PRC003', 'PLID003', '128', 15000),
('PRC004', 'PLID004', '2518', 100000),
('PRC005', 'PLID005', '5125', 30500);

-- --------------------------------------------------------

--
-- Struktur dari tabel `leaderboard`
--

CREATE TABLE `leaderboard` (
  `id` char(6) NOT NULL,
  `nama` varchar(20) DEFAULT NULL,
  `total_pengeluaran_pesanan` varchar(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `leaderboard`
--

INSERT INTO `leaderboard` (`id`, `nama`, `total_pengeluaran_pesanan`) VALUES
('WSA001', 'Wiyandra Syaiful A', '150000');

-- --------------------------------------------------------

--
-- Struktur dari tabel `listgames`
--

CREATE TABLE `listgames` (
  `idgame` char(7) NOT NULL,
  `nama_game` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `listgames`
--

INSERT INTO `listgames` (`idgame`, `nama_game`) VALUES
('PLID001', 'Mobile Legends'),
('PLID002', 'Point Blank'),
('PLID003', 'Blood Strike'),
('PLID004', 'Free Fire'),
('PLID005', 'PUBG Mobile');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengguna`
--

CREATE TABLE `pengguna` (
  `auto_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(30) UNIQUE,
  `password` varchar(255) NOT NULL,
  `nama` varchar(50),
  `email` varchar(100) UNIQUE,
  `no_hp` varchar(15),
  `total_pesanan` int(11) DEFAULT 0,
  `total_pengeluaran_pesanan` int(11) DEFAULT 0,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `riwayat` (
  `tanggal` date DEFAULT NULL,
  `idbeli` char(4) NOT NULL,
  `id` char(6) NOT NULL,
  `nominal` varchar(5) DEFAULT NULL,
  `harga` int(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Dumping data untuk tabel `pengguna`
--

INSERT INTO `pengguna` (`auto_id`, `username`, `password`, `nama`, `email`, `no_hp`, `total_pesanan`, `total_pengeluaran_pesanan`, `profile_image`) VALUES
(1, 'erer56', '$2y$10$BDSO8SY.dOBmcI4nSfncFe0GOnovvn8dvyirI1XMQwFD1jXx0365W', 'sapt', 'sap@sap.to', '085', 0, 0, 'uploads/1_profile_1748957064.jpg');

--
-- Indexes for dumped tables
--
-- Dumping data for table `riwayat`
--

INSERT INTO `riwayat` (`tanggal`, `idbeli`, `id`, `nominal`, `harga`) VALUES
('2022-11-03', 'S001', 'WSA001', '5928', 50000),
('2022-11-03', 'S002', 'WSA001', '5928', 50000),
('2025-04-12', 'S003', 'WSA001', '5928', 50000);

--
-- Triggers `riwayat`
--
DELIMITER $$
CREATE TRIGGER `update_leaderboard_after_insert` AFTER INSERT ON `riwayat` FOR EACH ROW BEGIN
    -- Periksa apakah pengguna sudah ada di leaderboard
    IF EXISTS (SELECT 1 FROM leaderboard WHERE id = NEW.id) THEN
        -- Jika sudah ada, update data leaderboard
        UPDATE leaderboard
        JOIN pengguna ON leaderboard.id = pengguna.id
        SET 
            leaderboard.total_pengeluaran_pesanan = pengguna.total_pengeluaran_pesanan,
            leaderboard.nama = pengguna.nama
        WHERE leaderboard.id = NEW.id;
    ELSE
        -- Jika belum ada, insert data baru ke leaderboard
        INSERT INTO leaderboard (id, total_pengeluaran_pesanan, nama)
        SELECT id, total_pengeluaran_pesanan, nama
        FROM pengguna
        WHERE id = NEW.id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_pengguna_after_insert` AFTER INSERT ON `riwayat` FOR EACH ROW BEGIN
    UPDATE pengguna
    SET 
        total_pesanan = total_pesanan + 1,
        total_pengeluaran_pesanan = total_pengeluaran_pesanan + NEW.harga
    WHERE id = NEW.id;
END
$$
DELIMITER ;

ALTER TABLE `leaderboard`
  ADD KEY `fk_nama` (`nama`),
  ADD KEY `fk_idlead` (`id`);

ALTER TABLE `listgames`
  ADD PRIMARY KEY (`idgame`);

ALTER TABLE `hargatopup`
  ADD CONSTRAINT `fk_games` FOREIGN KEY (`idgame`) REFERENCES `listgames` (`idgame`);

--
-- Constraints for table `leaderboard`
--
ALTER TABLE `leaderboard`
  ADD CONSTRAINT `fk_idlead` FOREIGN KEY (`id`) REFERENCES `pengguna` (`id`),
  ADD CONSTRAINT `fk_nama` FOREIGN KEY (`nama`) REFERENCES `pengguna` (`nama`);

--
-- Constraints for table `riwayat`
--
ALTER TABLE `riwayat`
  ADD CONSTRAINT `fk_harga` FOREIGN KEY (`harga`) REFERENCES `hargatopup` (`harga`),
  ADD CONSTRAINT `fk_id` FOREIGN KEY (`id`) REFERENCES `pengguna` (`id`),
  ADD CONSTRAINT `fk_nominal` FOREIGN KEY (`nominal`) REFERENCES `hargatopup` (`nominal`),
  ADD CONSTRAINT `fk_nominal1` FOREIGN KEY (`nominal`) REFERENCES `hargatopup` (`nominal`);
--
-- Indexes for dumped tables
--
ALTER TABLE `hargatopup`
  ADD PRIMARY KEY (`hargaid`),
  ADD KEY `fk_games` (`idgame`),
  ADD KEY `nominal` (`nominal`),
  ADD KEY `harga` (`harga`);

--
-- Indeks untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`auto_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `auto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
