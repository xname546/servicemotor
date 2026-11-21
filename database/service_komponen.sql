-- Tabel Transaksi Detail Komponen yang Diganti atau Dicek Saat Service
CREATE TABLE IF NOT EXISTS `Service_Komponen` (
    `ID_Service_Komponen` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ID_Service` INT UNSIGNED NOT NULL,
    `ID_Komponen` INT UNSIGNED NOT NULL,
    `Tindakan` ENUM('Ganti', 'Cek') NOT NULL DEFAULT 'Ganti',
    `Qty` INT UNSIGNED NOT NULL DEFAULT 1,
    `Harga_Satuan` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `Subtotal_Harga` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `Keterangan` TEXT NULL DEFAULT NULL,
    `Created_At` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_sk_service` (`ID_Service`),
    INDEX `idx_sk_komponen` (`ID_Komponen`),
    CONSTRAINT `fk_sk_service` FOREIGN KEY (`ID_Service`) REFERENCES `Service` (`ID_Service`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_sk_komponen` FOREIGN KEY (`ID_Komponen`) REFERENCES `Komponen` (`ID_Komponen`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
