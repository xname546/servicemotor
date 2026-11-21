-- Tabel Relasi Kendaraan dan Komponen (Menyimpan Posisi Terakhir Cek & Ganti)
CREATE TABLE IF NOT EXISTS `Kendaraan_Komponen` (
    `ID_Kendaraan_Komponen` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ID_Kendaraan` INT UNSIGNED NOT NULL,
    `ID_Komponen` INT UNSIGNED NOT NULL,
    `KM_Terakhir_Cek` INT UNSIGNED NULL DEFAULT NULL,
    `Tanggal_Terakhir_Cek` DATE NULL DEFAULT NULL,
    `KM_Terakhir_Ganti` INT UNSIGNED NULL DEFAULT NULL,
    `Tanggal_Terakhir_Ganti` DATE NULL DEFAULT NULL,
    `Created_At` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `Updated_At` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_kendaraan_komponen` (`ID_Kendaraan`, `ID_Komponen`),
    CONSTRAINT `fk_kk_kendaraan` FOREIGN KEY (`ID_Kendaraan`) REFERENCES `Kendaraan` (`ID_Kendaraan`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_kk_komponen` FOREIGN KEY (`ID_Komponen`) REFERENCES `Komponen` (`ID_Komponen`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
