-- Tabel Transaksi Header Service
CREATE TABLE IF NOT EXISTS `Service` (
    `ID_Service` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ID_Kendaraan` INT UNSIGNED NOT NULL,
    `Tanggal_Service` DATE NOT NULL,
    `Lokasi_Bengkel` VARCHAR(150) NULL DEFAULT NULL,
    `KM_Service` INT UNSIGNED NOT NULL DEFAULT 0,
    `Biaya_Jasa` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `Total_Biaya_Komponen` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `Total_Biaya` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `Catatan_Service` TEXT NULL DEFAULT NULL,
    `Nota` VARCHAR(255) NULL DEFAULT NULL,
    `Created_At` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `Updated_At` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_service_kendaraan_tanggal` (`ID_Kendaraan`, `Tanggal_Service`),
    CONSTRAINT `fk_service_kendaraan` FOREIGN KEY (`ID_Kendaraan`) REFERENCES `Kendaraan` (`ID_Kendaraan`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
