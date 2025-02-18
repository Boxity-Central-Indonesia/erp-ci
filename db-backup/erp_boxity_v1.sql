-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.24-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.3.0.6589
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for erp_boxity_v1
CREATE DATABASE IF NOT EXISTS `erp_boxity_v1` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `erp_boxity_v1`;

-- Dumping structure for table erp_boxity_v1.absensipegawai
CREATE TABLE IF NOT EXISTS `absensipegawai` (
  `Tanggal` date NOT NULL,
  `KodePegawai` varchar(20) NOT NULL,
  `IDFinger` varchar(20) DEFAULT NULL,
  `JamKerjaMasuk` datetime DEFAULT NULL,
  `JamKerjaPulang` datetime DEFAULT NULL,
  `JamMasuk` datetime DEFAULT NULL,
  `JamPulang` datetime DEFAULT NULL,
  `Telat` int(11) DEFAULT NULL,
  `Keterangan` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`Tanggal`,`KodePegawai`) USING BTREE,
  KEY `KodePegawai` (`KodePegawai`),
  CONSTRAINT `FK1990` FOREIGN KEY (`KodePegawai`) REFERENCES `mstpegawai` (`KodePegawai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.accesslevel
CREATE TABLE IF NOT EXISTS `accesslevel` (
  `LevelID` int(11) NOT NULL,
  `LevelName` varchar(255) DEFAULT NULL,
  `IsAktif` bit(1) DEFAULT NULL,
  `DivisiName` char(150) DEFAULT NULL,
  PRIMARY KEY (`LevelID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.aktivitasproduksi
CREATE TABLE IF NOT EXISTS `aktivitasproduksi` (
  `NoTrAktivitas` varchar(25) NOT NULL,
  `KodePegawai` varchar(20) DEFAULT NULL,
  `KodeAktivitas` varchar(20) DEFAULT NULL,
  `Biaya` double DEFAULT NULL,
  `JenisAktivitas` varchar(25) DEFAULT NULL,
  `NoTrans` varchar(25) DEFAULT NULL,
  `NoUrut` int(11) DEFAULT NULL,
  `TglAktivitas` date DEFAULT NULL,
  `Keterangan` varchar(255) DEFAULT NULL,
  `JmlAmpasDapur` float DEFAULT NULL,
  `GoniAmpasDapur` float DEFAULT NULL,
  `Satuan` varchar(25) DEFAULT NULL,
  `UserName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`NoTrAktivitas`),
  KEY `R_49` (`KodePegawai`),
  KEY `R_50` (`KodeAktivitas`),
  KEY `UserName` (`UserName`),
  CONSTRAINT `FK_aktivitasproduksi_userlogin` FOREIGN KEY (`UserName`) REFERENCES `userlogin` (`UserName`),
  CONSTRAINT `R_49` FOREIGN KEY (`KodePegawai`) REFERENCES `mstpegawai` (`KodePegawai`),
  CONSTRAINT `R_50` FOREIGN KEY (`KodeAktivitas`) REFERENCES `mstaktivitas` (`KodeAktivitas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.chat
CREATE TABLE IF NOT EXISTS `chat` (
  `KodeChat` varchar(25) CHARACTER SET utf8mb4 NOT NULL,
  `TglChat` datetime DEFAULT NULL,
  `Pengirim` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Penerima` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `IsiPesan` text DEFAULT NULL,
  `File` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `FileName` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `IsHapus` tinyint(4) DEFAULT NULL,
  `IsRead` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`KodeChat`),
  KEY `Pengirim` (`Pengirim`),
  KEY `Penerima` (`Penerima`),
  CONSTRAINT `FK_chat_userlogin` FOREIGN KEY (`Pengirim`) REFERENCES `userlogin` (`UserName`),
  CONSTRAINT `FK_chat_userlogin_2` FOREIGN KEY (`Penerima`) REFERENCES `userlogin` (`UserName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.detailsetakun
CREATE TABLE IF NOT EXISTS `detailsetakun` (
  `NoUrut` int(11) NOT NULL,
  `KodeSetAkun` varchar(25) NOT NULL,
  `JenisJurnal` varchar(25) DEFAULT NULL,
  `KodeAkun` varchar(50) DEFAULT NULL,
  `StatusAkun` varchar(20) DEFAULT NULL,
  `IsBank` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`NoUrut`,`KodeSetAkun`),
  KEY `KodeSetAkun` (`KodeSetAkun`),
  KEY `KodeAkun` (`KodeAkun`),
  CONSTRAINT `FK__mstakun` FOREIGN KEY (`KodeAkun`) REFERENCES `mstakun` (`KodeAkun`),
  CONSTRAINT `FK__setakunjurnal` FOREIGN KEY (`KodeSetAkun`) REFERENCES `setakunjurnal` (`KodeSetAkun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.draftbahanproduksi
CREATE TABLE IF NOT EXISTS `draftbahanproduksi` (
  `DraftID` int(11) NOT NULL,
  `IDTransJual` varchar(25) CHARACTER SET utf8mb4 NOT NULL,
  `NoUrut` int(11) NOT NULL,
  `KodeBarang` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Qty` float DEFAULT NULL,
  `SatuanBarang` varchar(25) CHARACTER SET utf8mb4 DEFAULT NULL,
  PRIMARY KEY (`DraftID`,`IDTransJual`,`NoUrut`),
  KEY `KodeBarang` (`KodeBarang`),
  KEY `IDTransJual` (`IDTransJual`),
  KEY `FK_draftbahanproduksi_itempenjualan` (`IDTransJual`,`NoUrut`),
  CONSTRAINT `FK_draftbahanproduksi_itempenjualan` FOREIGN KEY (`IDTransJual`, `NoUrut`) REFERENCES `itempenjualan` (`IDTransJual`, `NoUrut`),
  CONSTRAINT `FK_draftbahanproduksi_mstbarang` FOREIGN KEY (`KodeBarang`) REFERENCES `mstbarang` (`KodeBarang`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.fiturlevel
CREATE TABLE IF NOT EXISTS `fiturlevel` (
  `LevelID` int(11) NOT NULL,
  `FiturID` int(11) NOT NULL,
  `ViewData` bit(1) DEFAULT NULL,
  `AddData` bit(1) DEFAULT NULL,
  `EditData` bit(1) DEFAULT NULL,
  `DeleteData` bit(1) DEFAULT NULL,
  `PrintData` bit(1) DEFAULT NULL,
  PRIMARY KEY (`LevelID`,`FiturID`),
  KEY `FK__FiturLeve__Fitur__20C1E124` (`FiturID`),
  CONSTRAINT `FK__FiturLeve__Fitur__20C1E124` FOREIGN KEY (`FiturID`) REFERENCES `serverfitur` (`FiturID`),
  CONSTRAINT `FK__FiturLeve__Level__1FCDBCEB` FOREIGN KEY (`LevelID`) REFERENCES `accesslevel` (`LevelID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.flip
CREATE TABLE IF NOT EXISTS `flip` (
  `FlipID` varchar(20) CHARACTER SET utf8mb4 NOT NULL,
  `LinkID` int(11) DEFAULT NULL,
  `LinkUrl` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Title` varchar(255) DEFAULT NULL,
  `Amount` double DEFAULT NULL,
  `ExpDate` datetime DEFAULT NULL,
  `SenderName` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `SenderEmail` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `SenderPhoneNumber` varchar(25) CHARACTER SET utf8mb4 DEFAULT NULL,
  `SenderAddress` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `SenderBank` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
  `BankName` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `SenderBankType` varchar(25) CHARACTER SET utf8mb4 DEFAULT NULL,
  `NoRekening` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Status` varchar(25) CHARACTER SET utf8mb4 DEFAULT NULL,
  `PaymentUrl` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `UserName` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  PRIMARY KEY (`FlipID`) USING BTREE,
  KEY `UserName` (`UserName`),
  CONSTRAINT `FK_flip_userlogin` FOREIGN KEY (`UserName`) REFERENCES `userlogin` (`UserName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.itembarang
CREATE TABLE IF NOT EXISTS `itembarang` (
  `NoUrut` int(11) NOT NULL,
  `KodeTahun` char(10) NOT NULL,
  `NoTransKas` varchar(25) NOT NULL,
  `KodeBarang` varchar(20) DEFAULT NULL,
  `HargaSatuan` float DEFAULT NULL,
  `Qty` float DEFAULT NULL,
  `Diskon` float DEFAULT NULL,
  `Total` float DEFAULT NULL,
  PRIMARY KEY (`NoUrut`,`KodeTahun`,`NoTransKas`),
  KEY `KodeTahun` (`KodeTahun`,`NoTransKas`),
  CONSTRAINT `itembarang_ibfk_1` FOREIGN KEY (`KodeTahun`, `NoTransKas`) REFERENCES `transaksikas` (`KodeTahun`, `NoTransKas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.iteminsentifbulanan
CREATE TABLE IF NOT EXISTS `iteminsentifbulanan` (
  `NoUrut` int(11) NOT NULL,
  `IDRekap` varchar(25) NOT NULL,
  `KodePegawai` varchar(20) NOT NULL,
  `BagianPekerjaan` varchar(100) DEFAULT NULL,
  `NamaPekerjaan` varchar(100) DEFAULT NULL,
  `SatuanPekerjaan` varchar(50) DEFAULT NULL,
  `RateSatuan` float DEFAULT NULL,
  `JmlPerolehan` double DEFAULT NULL,
  `NominalInsentif` double DEFAULT NULL,
  `CaraHitung` varchar(25) DEFAULT NULL,
  `Keterangan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`NoUrut`,`KodePegawai`,`IDRekap`),
  KEY `KodePegawai` (`KodePegawai`),
  CONSTRAINT `FK1969` FOREIGN KEY (`KodePegawai`) REFERENCES `mstpegawai` (`KodePegawai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.itempembelian
CREATE TABLE IF NOT EXISTS `itempembelian` (
  `IDTransBeli` varchar(25) NOT NULL,
  `NoUrut` int(11) NOT NULL,
  `Spesifikasi` varchar(255) DEFAULT NULL,
  `KodeBarang` varchar(20) DEFAULT NULL,
  `HargaSatuan` double DEFAULT NULL,
  `Qty` float DEFAULT NULL,
  `Diskon` double DEFAULT NULL,
  `Total` double DEFAULT NULL,
  `SatuanBarang` varchar(20) DEFAULT NULL,
  `Deskripsi` varchar(255) DEFAULT NULL,
  `HPPSaatBeli` double DEFAULT NULL,
  `IsVoid` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`IDTransBeli`,`NoUrut`),
  KEY `R_41` (`KodeBarang`),
  CONSTRAINT `R_40` FOREIGN KEY (`IDTransBeli`) REFERENCES `transpembelian` (`IDTransBeli`),
  CONSTRAINT `R_41` FOREIGN KEY (`KodeBarang`) REFERENCES `mstbarang` (`KodeBarang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.itempenjualan
CREATE TABLE IF NOT EXISTS `itempenjualan` (
  `IDTransJual` varchar(25) NOT NULL,
  `NoUrut` int(11) NOT NULL,
  `KodeBarang` varchar(20) DEFAULT NULL,
  `JenisBarang` varchar(150) DEFAULT NULL,
  `Kategory` varchar(150) DEFAULT NULL,
  `Spesifikasi` varchar(255) DEFAULT NULL,
  `Deskripsi` varchar(255) DEFAULT NULL,
  `HargaSatuan` double DEFAULT NULL,
  `Diskon` double DEFAULT NULL,
  `Qty` float DEFAULT NULL,
  `Total` double DEFAULT NULL,
  `SatuanBarang` varchar(25) DEFAULT NULL,
  `AdditionalName` varchar(255) DEFAULT NULL,
  `HPPSaatJual` double DEFAULT NULL,
  `ProdUkuran` varchar(20) DEFAULT NULL,
  `ProdJmlDaun` int(11) DEFAULT NULL,
  `SatuanPenjualan` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`IDTransJual`,`NoUrut`),
  KEY `R_44` (`KodeBarang`),
  CONSTRAINT `R_43` FOREIGN KEY (`IDTransJual`) REFERENCES `transpenjualan` (`IDTransJual`),
  CONSTRAINT `R_44` FOREIGN KEY (`KodeBarang`) REFERENCES `mstbarang` (`KodeBarang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.itemretur
CREATE TABLE IF NOT EXISTS `itemretur` (
  `NoUrut` int(11) NOT NULL,
  `IDTransRetur` varchar(25) CHARACTER SET utf8mb4 NOT NULL,
  `JenisRetur` varchar(50) CHARACTER SET utf8mb4 NOT NULL,
  `KodeBarang` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `SatuanBarang` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `AdditionalName` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `JmlJual` float DEFAULT NULL,
  `HargaJual` double DEFAULT NULL,
  `JmlRetur` float DEFAULT NULL,
  `TotalRetur` double DEFAULT NULL,
  `AlasanRetur` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `IsVoid` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`NoUrut`,`IDTransRetur`,`JenisRetur`),
  KEY `IDTransRetur` (`IDTransRetur`),
  KEY `KodeBarang` (`KodeBarang`),
  CONSTRAINT `FK_itemretur_mstbarang` FOREIGN KEY (`KodeBarang`) REFERENCES `mstbarang` (`KodeBarang`),
  CONSTRAINT `FK_itemretur_transaksiretur` FOREIGN KEY (`IDTransRetur`) REFERENCES `transaksiretur` (`IDTransRetur`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.itemtransaksibarang
CREATE TABLE IF NOT EXISTS `itemtransaksibarang` (
  `NoUrut` int(11) NOT NULL,
  `NoTrans` varchar(25) NOT NULL,
  `KodeBarang` varchar(20) DEFAULT NULL,
  `Qty` float DEFAULT NULL,
  `HargaSatuan` double DEFAULT NULL,
  `Total` double DEFAULT NULL,
  `BeratKotor` float DEFAULT NULL,
  `NoRefProduksi` varchar(25) DEFAULT NULL,
  `Deskripsi` varchar(255) DEFAULT NULL,
  `JenisStok` varchar(25) DEFAULT NULL,
  `GudangAsal` varchar(20) DEFAULT NULL,
  `GudangTujuan` varchar(20) DEFAULT NULL,
  `SatuanBarang` varchar(20) DEFAULT NULL,
  `JenisBarang` varchar(150) DEFAULT NULL,
  `Kategory` varchar(150) DEFAULT NULL,
  `StokSistemPenyesuaian` float DEFAULT NULL,
  `StokFisikPenyesuaian` float DEFAULT NULL,
  `IsBarangJadi` tinyint(4) DEFAULT NULL,
  `IsBahanBaku` tinyint(4) DEFAULT NULL,
  `ProdUkuran` varchar(20) DEFAULT NULL,
  `ProdJmlDaun` int(11) DEFAULT NULL,
  `IsHapus` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`NoUrut`,`NoTrans`) USING BTREE,
  KEY `R_26` (`KodeBarang`),
  KEY `R_20` (`NoTrans`) USING BTREE,
  CONSTRAINT `R_20` FOREIGN KEY (`NoTrans`) REFERENCES `transaksibarang` (`NoTrans`),
  CONSTRAINT `R_26` FOREIGN KEY (`KodeBarang`) REFERENCES `mstbarang` (`KodeBarang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.mstaktivitas
CREATE TABLE IF NOT EXISTS `mstaktivitas` (
  `KodeAktivitas` varchar(25) NOT NULL,
  `BatasBawah` float DEFAULT NULL,
  `JmlDaun` int(11) DEFAULT NULL,
  `BatasAtas` float DEFAULT NULL,
  `KodeJenisAktivitas` varchar(25) DEFAULT NULL,
  `JenisAktivitas` varchar(25) DEFAULT NULL,
  `Biaya` double DEFAULT NULL,
  `Satuan` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`KodeAktivitas`),
  KEY `KodeJenisAktivitas` (`KodeJenisAktivitas`),
  CONSTRAINT `FK_mstaktivitas_mstjenisaktivitas` FOREIGN KEY (`KodeJenisAktivitas`) REFERENCES `mstjenisaktivitas` (`KodeJenisAktivitas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.mstakun
CREATE TABLE IF NOT EXISTS `mstakun` (
  `KodeAkun` varchar(50) NOT NULL DEFAULT '',
  `IsParent` bit(1) DEFAULT NULL,
  `NamaAkun` varchar(50) DEFAULT NULL,
  `Keterangan` varchar(50) DEFAULT NULL,
  `AkunInduk` varchar(20) DEFAULT NULL,
  `KelompokAkun` varchar(50) DEFAULT NULL,
  `Kriteria1` varchar(50) DEFAULT NULL,
  `Kriteria2` varchar(50) DEFAULT NULL,
  `JenisAkun` varchar(20) DEFAULT NULL,
  `IsAktif` bit(1) DEFAULT NULL,
  `KategoriArusKas` varchar(100) DEFAULT NULL,
  `SaldoAwalDebet` double DEFAULT NULL,
  `SaldoAwalKredit` double DEFAULT NULL,
  `IsPersediaan` bit(1) DEFAULT NULL,
  PRIMARY KEY (`KodeAkun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.mstbarang
CREATE TABLE IF NOT EXISTS `mstbarang` (
  `KodeBarang` varchar(20) NOT NULL,
  `NamaBarang` varchar(150) DEFAULT NULL,
  `DeskripsiBarang` varchar(255) DEFAULT NULL,
  `HargaBeliTerakhir` double DEFAULT NULL,
  `HargaJual` double DEFAULT NULL,
  `NilaiHPP` double DEFAULT NULL,
  `IsAktif` tinyint(1) DEFAULT NULL,
  `Foto1` varchar(150) DEFAULT NULL,
  `Foto2` varchar(150) DEFAULT NULL,
  `TglInput` timestamp NULL DEFAULT NULL,
  `SatuanBarang` varchar(20) DEFAULT NULL,
  `Spesifikasi` varchar(255) DEFAULT NULL,
  `BeratBarang` float DEFAULT NULL,
  `ProductionCode` varchar(150) DEFAULT NULL,
  `KodeManual` varchar(255) DEFAULT NULL,
  `KodeJenis` varchar(20) DEFAULT NULL,
  `KodeKategori` varchar(25) DEFAULT NULL,
  `HPPOpeningBalance` double DEFAULT NULL,
  `StokOpeningBalance` float DEFAULT NULL,
  `KodeGudang` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`KodeBarang`),
  KEY `R_38` (`KodeJenis`),
  KEY `KodeKategori` (`KodeKategori`),
  KEY `KodeGudang` (`KodeGudang`),
  CONSTRAINT `FK_mstbarang_mstgudang` FOREIGN KEY (`KodeGudang`) REFERENCES `mstgudang` (`KodeGudang`),
  CONSTRAINT `R_38` FOREIGN KEY (`KodeJenis`) REFERENCES `mstjenisbarang` (`KodeJenis`),
  CONSTRAINT `R_382` FOREIGN KEY (`KodeKategori`) REFERENCES `mstkategori` (`KodeKategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.mstgudang
CREATE TABLE IF NOT EXISTS `mstgudang` (
  `KodeGudang` varchar(20) NOT NULL,
  `NamaGudang` varchar(255) DEFAULT NULL,
  `Alamat` varchar(255) DEFAULT NULL,
  `Deskripsi` varchar(255) DEFAULT NULL,
  `TglInput` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`KodeGudang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.mstjabatan
CREATE TABLE IF NOT EXISTS `mstjabatan` (
  `KodeJabatan` varchar(20) NOT NULL,
  `NamaJabatan` varchar(150) DEFAULT NULL,
  `Deskripsi` varchar(255) DEFAULT NULL,
  `IsAktif` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`KodeJabatan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.mstjenisaktivitas
CREATE TABLE IF NOT EXISTS `mstjenisaktivitas` (
  `KodeJenisAktivitas` varchar(25) CHARACTER SET utf8mb4 NOT NULL,
  `NoUrut` smallint(6) DEFAULT NULL,
  `JenisAktivitas` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
  `IsAktif` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`KodeJenisAktivitas`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.mstjenisbarang
CREATE TABLE IF NOT EXISTS `mstjenisbarang` (
  `KodeJenis` varchar(20) NOT NULL,
  `NamaJenisBarang` varchar(50) DEFAULT NULL,
  `Deskripsi` varchar(255) DEFAULT NULL,
  `IsAktif` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`KodeJenis`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.mstkategori
CREATE TABLE IF NOT EXISTS `mstkategori` (
  `KodeKategori` varchar(25) NOT NULL,
  `NamaKategori` varchar(255) DEFAULT NULL,
  `IsAktif` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`KodeKategori`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.mstkomponengaji
CREATE TABLE IF NOT EXISTS `mstkomponengaji` (
  `KodeKompGaji` varchar(20) NOT NULL,
  `NamaKomponenGaji` varchar(150) DEFAULT NULL,
  `IsAktif` tinyint(1) DEFAULT NULL,
  `JenisKomponen` varchar(50) DEFAULT NULL,
  `NominalRp` double DEFAULT NULL,
  `NominalProses` double DEFAULT NULL,
  `Deskripsi` varchar(255) DEFAULT NULL,
  `CaraHitung` varchar(255) DEFAULT NULL,
  `Kriteria` varchar(255) DEFAULT NULL,
  `KodeJabatan` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`KodeKompGaji`),
  KEY `KodeJabatan` (`KodeJabatan`),
  CONSTRAINT `FK199` FOREIGN KEY (`KodeJabatan`) REFERENCES `mstjabatan` (`KodeJabatan`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.mstpegawai
CREATE TABLE IF NOT EXISTS `mstpegawai` (
  `KodePegawai` varchar(20) NOT NULL,
  `NIP` varchar(16) DEFAULT NULL,
  `IDFinger` varchar(20) DEFAULT NULL,
  `NamaPegawai` varchar(150) DEFAULT NULL,
  `TTL` varchar(150) DEFAULT NULL,
  `Alamat` varchar(255) DEFAULT NULL,
  `TelpHP` varchar(20) DEFAULT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `TglMulaiKerja` date DEFAULT NULL,
  `TglResign` date DEFAULT NULL,
  `IsAktif` tinyint(1) DEFAULT NULL,
  `KodeJabatan` varchar(20) DEFAULT NULL,
  `KodeJabAtasanLangsung` varchar(20) DEFAULT NULL,
  `KodeBank` varchar(100) DEFAULT NULL,
  `NoRek` varchar(50) DEFAULT NULL,
  `GajiPokok` double DEFAULT NULL,
  `IsGajiHarian` tinyint(4) DEFAULT NULL,
  `JenisPegawai` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`KodePegawai`),
  KEY `R_36` (`KodeJabatan`),
  CONSTRAINT `R_36` FOREIGN KEY (`KodeJabatan`) REFERENCES `mstjabatan` (`KodeJabatan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.mstperson
CREATE TABLE IF NOT EXISTS `mstperson` (
  `KodePerson` varchar(20) NOT NULL,
  `NamaPersonCP` varchar(150) DEFAULT NULL,
  `NoHP` varchar(25) DEFAULT NULL,
  `AlamatPerson` varchar(255) DEFAULT NULL,
  `NamaUsaha` varchar(150) DEFAULT NULL,
  `Keterangan` varchar(255) DEFAULT NULL,
  `IsAktif` tinyint(1) DEFAULT NULL,
  `JenisPerson` varchar(50) DEFAULT NULL,
  `KodeManual` varchar(50) DEFAULT NULL,
  `TglInput` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`KodePerson`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.msttahunanggaran
CREATE TABLE IF NOT EXISTS `msttahunanggaran` (
  `KodeTahun` char(10) NOT NULL,
  `Keterangan` char(255) DEFAULT NULL,
  `IsAktif` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`KodeTahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.neracalabarugi
CREATE TABLE IF NOT EXISTS `neracalabarugi` (
  `BulanTahun` varchar(20) NOT NULL,
  `KodeTahun` varchar(20) NOT NULL,
  `NoUrut` int(11) NOT NULL,
  `KodeAkun` varchar(20) NOT NULL,
  `NamaAkun` varchar(150) DEFAULT NULL,
  `Debet` double DEFAULT NULL,
  `Kredit` double DEFAULT NULL,
  `Saldo` double DEFAULT NULL,
  `Keterangan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`BulanTahun`,`KodeAkun`,`KodeTahun`,`NoUrut`) USING BTREE,
  KEY `FK__NeracaLab__KodeA__1F98B2C1` (`KodeAkun`),
  KEY `FK__NeracaLab__KodeT__1EA48E88` (`KodeTahun`),
  CONSTRAINT `FK__NeracaLab__KodeA__1F98B2C1` FOREIGN KEY (`KodeAkun`) REFERENCES `mstakun` (`KodeAkun`),
  CONSTRAINT `FK__NeracaLab__KodeT__1EA48E88` FOREIGN KEY (`KodeTahun`) REFERENCES `msttahunanggaran` (`KodeTahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.neracasaldo
CREATE TABLE IF NOT EXISTS `neracasaldo` (
  `BulanTahun` varchar(20) NOT NULL,
  `KodeTahun` varchar(20) NOT NULL,
  `NoUrut` int(11) NOT NULL,
  `KodeAkun` varchar(20) NOT NULL,
  `NamaAkun` varchar(150) DEFAULT NULL,
  `SaldoDebet` double DEFAULT NULL,
  `SaldoKredit` double DEFAULT NULL,
  `SaldoAkhir` double DEFAULT NULL,
  `Keterangan` varchar(20) DEFAULT NULL,
  `Keterangan1` varchar(20) DEFAULT NULL,
  `Keterangan2` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`BulanTahun`,`KodeAkun`,`KodeTahun`,`NoUrut`) USING BTREE,
  KEY `FK__NeracaSal__KodeA__236943A5` (`KodeAkun`),
  KEY `FK__NeracaSal__KodeT__22751F6C` (`KodeTahun`),
  CONSTRAINT `FK__NeracaSal__KodeA__236943A5` FOREIGN KEY (`KodeAkun`) REFERENCES `mstakun` (`KodeAkun`),
  CONSTRAINT `FK__NeracaSal__KodeT__22751F6C` FOREIGN KEY (`KodeTahun`) REFERENCES `msttahunanggaran` (`KodeTahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.rekapinsentifbulanan
CREATE TABLE IF NOT EXISTS `rekapinsentifbulanan` (
  `IDRekap` varchar(25) NOT NULL,
  `KodePegawai` varchar(20) NOT NULL,
  `KodeTahun` char(10) DEFAULT NULL,
  `Bulan` varchar(25) DEFAULT NULL,
  `TglRekap` datetime DEFAULT NULL,
  `TotalInsentif` double DEFAULT NULL,
  `Keterangan` varchar(255) DEFAULT NULL,
  `IsTelahDibayarkan` tinyint(4) DEFAULT NULL,
  `NoTransBayar` varchar(25) DEFAULT NULL,
  `UserName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`KodePegawai`,`IDRekap`),
  KEY `KodePegawai` (`KodePegawai`),
  KEY `KodeTahun` (`KodeTahun`),
  KEY `UserName` (`UserName`),
  CONSTRAINT `FK1199` FOREIGN KEY (`KodePegawai`) REFERENCES `mstpegawai` (`KodePegawai`),
  CONSTRAINT `FK2199` FOREIGN KEY (`KodeTahun`) REFERENCES `msttahunanggaran` (`KodeTahun`),
  CONSTRAINT `FK_rekapinsentifbulanan_userlogin` FOREIGN KEY (`UserName`) REFERENCES `userlogin` (`UserName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.serverfitur
CREATE TABLE IF NOT EXISTS `serverfitur` (
  `FiturID` int(11) NOT NULL,
  `FiturName` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`FiturID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.serverlog
CREATE TABLE IF NOT EXISTS `serverlog` (
  `LogID` varchar(100) NOT NULL,
  `UserName` varchar(50) NOT NULL,
  `DateTimeLog` timestamp NULL DEFAULT NULL,
  `NoTransaksi` varchar(50) DEFAULT NULL,
  `JenisTransaksi` varchar(50) DEFAULT NULL,
  `Action` varchar(255) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `IPUser` char(15) DEFAULT NULL,
  PRIMARY KEY (`LogID`,`UserName`),
  KEY `R_28` (`UserName`),
  CONSTRAINT `R_28` FOREIGN KEY (`UserName`) REFERENCES `userlogin` (`UserName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.setakunjurnal
CREATE TABLE IF NOT EXISTS `setakunjurnal` (
  `KodeSetAkun` varchar(25) NOT NULL,
  `NoUrut` int(11) DEFAULT NULL,
  `NamaTransaksi` varchar(255) DEFAULT NULL,
  `JenisTransaksi` varchar(50) DEFAULT NULL,
  `IsAktif` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`KodeSetAkun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.sistemsetting
CREATE TABLE IF NOT EXISTS `sistemsetting` (
  `KodeSetting` int(11) NOT NULL DEFAULT 0,
  `NamaSetting` varchar(255) DEFAULT NULL,
  `ValueSetting` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`KodeSetting`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.transaksibarang
CREATE TABLE IF NOT EXISTS `transaksibarang` (
  `NoTrans` varchar(25) NOT NULL,
  `TanggalTransaksi` datetime DEFAULT NULL,
  `UserName` varchar(50) DEFAULT NULL,
  `KodePerson` varchar(20) DEFAULT NULL,
  `Deskripsi` varchar(255) DEFAULT NULL,
  `JenisTransaksi` varchar(50) DEFAULT NULL,
  `NoRefTrSistem` varchar(25) DEFAULT NULL,
  `NoRefTrManual` varchar(150) DEFAULT NULL,
  `ProdTglSelesai` datetime DEFAULT NULL,
  `ProdUkuran` varchar(20) DEFAULT NULL,
  `ProdJmlDaun` int(11) DEFAULT NULL,
  `GudangAsal` varchar(20) DEFAULT NULL,
  `GudangTujuan` varchar(20) DEFAULT NULL,
  `KodeBarang` varchar(20) DEFAULT NULL,
  `KodeProduksi` varchar(100) DEFAULT NULL,
  `JmlProduksi` float DEFAULT NULL,
  `BiayaProduksi` double DEFAULT NULL,
  `BeratKotor` float DEFAULT NULL,
  `BeratBersih` float DEFAULT NULL,
  `HPPProduksi` double DEFAULT NULL,
  `IsHapus` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`NoTrans`) USING BTREE,
  KEY `R_23` (`UserName`),
  KEY `R_24` (`KodePerson`),
  KEY `GudangAsal` (`GudangAsal`),
  KEY `GudangTujuan` (`GudangTujuan`),
  KEY `KodeBarang` (`KodeBarang`),
  CONSTRAINT `R_23` FOREIGN KEY (`UserName`) REFERENCES `userlogin` (`UserName`),
  CONSTRAINT `R_24` FOREIGN KEY (`KodePerson`) REFERENCES `mstperson` (`KodePerson`),
  CONSTRAINT `R_89` FOREIGN KEY (`GudangAsal`) REFERENCES `mstgudang` (`KodeGudang`),
  CONSTRAINT `R_90` FOREIGN KEY (`GudangTujuan`) REFERENCES `mstgudang` (`KodeGudang`),
  CONSTRAINT `R_91` FOREIGN KEY (`KodeBarang`) REFERENCES `mstbarang` (`KodeBarang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.transaksikas
CREATE TABLE IF NOT EXISTS `transaksikas` (
  `NoTransKas` varchar(25) NOT NULL,
  `KodeTahun` char(10) NOT NULL,
  `TanggalTransaksi` datetime NOT NULL,
  `NoRef_Sistem` varchar(50) DEFAULT NULL,
  `NoRef_Manual` varchar(150) DEFAULT NULL,
  `Uraian` varchar(255) DEFAULT NULL,
  `UserName` varchar(50) DEFAULT NULL,
  `KodePerson` varchar(20) DEFAULT NULL,
  `NominalBelumPajak` double DEFAULT NULL,
  `PPN` double DEFAULT NULL,
  `PPh` double DEFAULT NULL,
  `TotalTransaksi` double DEFAULT NULL,
  `IsDijurnalkan` tinyint(1) DEFAULT NULL,
  `JenisTransaksiKas` varchar(25) DEFAULT NULL,
  `NoTransJurnal` varchar(25) DEFAULT NULL,
  `TipeJurnal` varchar(25) DEFAULT NULL,
  `NarasiJurnal` varchar(255) DEFAULT NULL,
  `Diskon` double DEFAULT NULL,
  `KodePegawai` varchar(20) DEFAULT NULL,
  `IDRekap` varchar(25) DEFAULT NULL,
  `TanggalJatuhTempo` datetime DEFAULT NULL,
  `Status` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`KodeTahun`,`NoTransKas`),
  KEY `KodeTahun` (`KodeTahun`),
  KEY `KodePegawai` (`KodePegawai`,`IDRekap`),
  CONSTRAINT `FK313` FOREIGN KEY (`KodeTahun`) REFERENCES `msttahunanggaran` (`KodeTahun`),
  CONSTRAINT `FK_transaksikas_rekapinsentifbulanan` FOREIGN KEY (`KodePegawai`, `IDRekap`) REFERENCES `rekapinsentifbulanan` (`KodePegawai`, `IDRekap`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.transaksiretur
CREATE TABLE IF NOT EXISTS `transaksiretur` (
  `IDTransRetur` varchar(25) CHARACTER SET utf8mb4 NOT NULL,
  `JenisRetur` varchar(50) CHARACTER SET utf8mb4 NOT NULL,
  `IDTrans` varchar(25) CHARACTER SET utf8mb4 NOT NULL,
  `KodePerson` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `TanggalTransaksi` datetime DEFAULT NULL,
  `TotalRetur` double DEFAULT NULL,
  `Keterangan` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `KodeGudang` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `IsRealisasi` tinyint(4) DEFAULT NULL,
  `JenisRealisasi` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `KetRealisasi` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `IsDijurnalkan` tinyint(4) DEFAULT NULL,
  `NoTransJurnal` varchar(25) CHARACTER SET utf8mb4 DEFAULT NULL,
  `UserName` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `IsVoid` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`IDTransRetur`,`JenisRetur`,`IDTrans`) USING BTREE,
  KEY `KodePerson` (`KodePerson`),
  KEY `KodeGudang` (`KodeGudang`),
  KEY `UserName` (`UserName`),
  CONSTRAINT `FK_transaksiretur_mstgudang` FOREIGN KEY (`KodeGudang`) REFERENCES `mstgudang` (`KodeGudang`),
  CONSTRAINT `FK_transaksiretur_mstperson` FOREIGN KEY (`KodePerson`) REFERENCES `mstperson` (`KodePerson`),
  CONSTRAINT `FK_transaksiretur_userlogin` FOREIGN KEY (`UserName`) REFERENCES `userlogin` (`UserName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.transjurnal
CREATE TABLE IF NOT EXISTS `transjurnal` (
  `IDTransJurnal` varchar(25) NOT NULL,
  `KodeTahun` char(10) NOT NULL,
  `TglTransJurnal` datetime DEFAULT NULL,
  `TipeJurnal` varchar(25) DEFAULT NULL,
  `NarasiJurnal` varchar(255) DEFAULT NULL,
  `NominalTransaksi` double DEFAULT NULL,
  `NoRefTrans` varchar(25) DEFAULT NULL,
  `UserName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`IDTransJurnal`,`KodeTahun`),
  KEY `KodeTahun` (`KodeTahun`),
  KEY `UserName` (`UserName`),
  CONSTRAINT `FK_transjurnal_msttahunanggaran` FOREIGN KEY (`KodeTahun`) REFERENCES `msttahunanggaran` (`KodeTahun`),
  CONSTRAINT `FK_transjurnal_userlogin` FOREIGN KEY (`UserName`) REFERENCES `userlogin` (`UserName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.transjurnalitem
CREATE TABLE IF NOT EXISTS `transjurnalitem` (
  `NoUrut` int(11) NOT NULL,
  `IDTransJurnal` varchar(25) NOT NULL,
  `KodeTahun` char(10) NOT NULL,
  `KodeAkun` varchar(50) DEFAULT NULL,
  `NamaAkun` varchar(150) DEFAULT NULL,
  `Debet` double DEFAULT NULL,
  `Kredit` double DEFAULT NULL,
  `Uraian` varchar(255) DEFAULT NULL,
  `Keterangan2` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`KodeTahun`,`IDTransJurnal`,`NoUrut`) USING BTREE,
  KEY `KodeTahun` (`KodeTahun`),
  KEY `IDTransJurnal` (`IDTransJurnal`),
  KEY `FK_transjurnalitem_mstakun` (`KodeAkun`),
  CONSTRAINT `FK315` FOREIGN KEY (`KodeTahun`) REFERENCES `msttahunanggaran` (`KodeTahun`),
  CONSTRAINT `FK_transjurnalitem_mstakun` FOREIGN KEY (`KodeAkun`) REFERENCES `mstakun` (`KodeAkun`),
  CONSTRAINT `FK_transjurnalitem_transjurnal` FOREIGN KEY (`IDTransJurnal`) REFERENCES `transjurnal` (`IDTransJurnal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.transpembelian
CREATE TABLE IF NOT EXISTS `transpembelian` (
  `IDTransBeli` varchar(25) NOT NULL,
  `NoPO` varchar(25) DEFAULT NULL,
  `KodePerson` varchar(20) DEFAULT NULL,
  `TglPO` datetime DEFAULT NULL,
  `UserPO` varchar(150) DEFAULT NULL,
  `ApprovedNo` varchar(25) DEFAULT NULL,
  `ApprovedDate` datetime DEFAULT NULL,
  `ApprovedBy` varchar(150) DEFAULT NULL,
  `ApprovedDesc` varchar(255) DEFAULT NULL,
  `TotalNilaiBarang` double DEFAULT NULL,
  `TotalNilaiBarangReal` double DEFAULT NULL,
  `PPN` double DEFAULT NULL,
  `PPh` double DEFAULT NULL,
  `DiskonBawah` double DEFAULT NULL,
  `NominalBelumPajak` double DEFAULT NULL,
  `TotalTagihan` double DEFAULT NULL,
  `StatusProses` varchar(20) DEFAULT NULL,
  `StatusKirim` varchar(20) DEFAULT NULL,
  `StatusBayar` varchar(20) DEFAULT NULL,
  `NoRef_Manual` varchar(150) DEFAULT NULL,
  `TanggalPembelian` datetime DEFAULT NULL,
  `UraianPembelian` varchar(255) DEFAULT NULL,
  `TanggalJatuhTempo` datetime DEFAULT NULL,
  `IsDijurnalkan` tinyint(4) DEFAULT NULL,
  `NoTransJurnal` varchar(25) DEFAULT NULL,
  `CatatanKirimPembayaran` double DEFAULT NULL,
  `IsVoid` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`IDTransBeli`),
  KEY `R_39` (`KodePerson`),
  CONSTRAINT `R_39` FOREIGN KEY (`KodePerson`) REFERENCES `mstperson` (`KodePerson`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.transpenjualan
CREATE TABLE IF NOT EXISTS `transpenjualan` (
  `IDTransJual` varchar(25) NOT NULL,
  `KodePerson` varchar(20) DEFAULT NULL,
  `NoSlipOrder` varchar(25) DEFAULT NULL,
  `TglSlipOrder` datetime DEFAULT NULL,
  `SODibuatOleh` varchar(150) DEFAULT NULL,
  `EstimasiSelesai` datetime DEFAULT NULL,
  `SPKDisetujuiOleh` varchar(150) DEFAULT NULL,
  `SPKDisetujuiTgl` datetime DEFAULT NULL,
  `SPKDiketahuiOleh` varchar(150) DEFAULT NULL,
  `SPKDiketahuiTgl` datetime DEFAULT NULL,
  `SPKDibuatOleh` varchar(150) DEFAULT NULL,
  `SPKTanggal` datetime DEFAULT NULL,
  `SPKNomor` varchar(25) DEFAULT NULL,
  `TotalNilaiBarang` double DEFAULT NULL,
  `TotalNilaiBarangReal` double DEFAULT NULL,
  `PPN` double DEFAULT NULL,
  `PPh` double DEFAULT NULL,
  `DiskonBawah` double DEFAULT NULL,
  `NominalBelumPajak` double DEFAULT NULL,
  `TotalTagihan` double DEFAULT NULL,
  `StatusProses` varchar(20) DEFAULT NULL,
  `StatusKirim` varchar(20) DEFAULT NULL,
  `StatusBayar` varchar(20) DEFAULT NULL,
  `NoRef_Manual` varchar(150) DEFAULT NULL,
  `TanggalPenjualan` datetime DEFAULT NULL,
  `KodeGudang` varchar(20) DEFAULT NULL,
  `StatusProduksi` varchar(20) DEFAULT NULL,
  `TanggalJatuhTempo` datetime DEFAULT NULL,
  `IsDijurnalkan` tinyint(4) DEFAULT NULL,
  `NoTransJurnal` varchar(25) DEFAULT NULL,
  `CatatanTerimaBayar` double DEFAULT NULL,
  PRIMARY KEY (`IDTransJual`),
  KEY `R_42` (`KodePerson`),
  CONSTRAINT `R_42` FOREIGN KEY (`KodePerson`) REFERENCES `mstperson` (`KodePerson`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.trpinjamankaryawan
CREATE TABLE IF NOT EXISTS `trpinjamankaryawan` (
  `KodeTrPinjam` varchar(20) CHARACTER SET utf8mb4 NOT NULL,
  `TanggalPinjam` datetime DEFAULT NULL,
  `NominalPinjam` double DEFAULT NULL,
  `MingguKe` int(11) DEFAULT NULL,
  `IsDibayar` tinyint(4) DEFAULT NULL,
  `NominalDibayar` double DEFAULT NULL,
  `KodePegawai` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Keterangan` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `IsHapus` tinyint(4) DEFAULT NULL,
  `UserName` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  PRIMARY KEY (`KodeTrPinjam`),
  KEY `KodePegawai` (`KodePegawai`),
  KEY `UserName` (`UserName`),
  CONSTRAINT `FK_trpinjamankaryawan_mstpegawai` FOREIGN KEY (`KodePegawai`) REFERENCES `mstpegawai` (`KodePegawai`),
  CONSTRAINT `FK_trpinjamankaryawan_userlogin` FOREIGN KEY (`UserName`) REFERENCES `userlogin` (`UserName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table erp_boxity_v1.userlogin
CREATE TABLE IF NOT EXISTS `userlogin` (
  `UserName` varchar(50) NOT NULL,
  `UserPsw` varchar(255) DEFAULT NULL,
  `ActualName` varchar(255) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `Phone` varchar(50) DEFAULT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Photo` varchar(255) DEFAULT NULL,
  `LevelID` int(11) DEFAULT NULL,
  `IsAktif` bit(1) DEFAULT NULL,
  `IsOnline` tinyint(4) DEFAULT NULL,
  `TglTerakhirLogin` datetime DEFAULT NULL,
  `Token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`UserName`),
  KEY `FK__UserLogin__Level__239E4DCF` (`LevelID`),
  CONSTRAINT `FK__UserLogin__Level__239E4DCF` FOREIGN KEY (`LevelID`) REFERENCES `accesslevel` (`LevelID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
