<?php
/**
 * Script Migrasi Database (Fresh Migrate)
 * Jalankan dari terminal: php database/migrate.php
 */

$host = '127.0.0.1';
$port = '3306';
$user = 'root';
$pass = ''; // Default Laragon kosong
$dbName = 'tryone_db';

// Urutan file SQL berdasarkan relasi Foreign Key
$sqlFiles = [
    'kendaraan.sql',
    'komponen.sql',
    'kendaraan_komponen.sql',
    'service.sql',
    'service_komponen.sql',
];

// Daftar tabel untuk di-reset saat migrate ulang
$tables = [
    'Service_Komponen',
    'Service',
    'Kendaraan_Komponen',
    'Komponen',
    'Kendaraan',
];

try {
    echo "Menghubungkan ke MySQL Server ($host:$port)...\n";
    $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // 1. Buat database jika belum ada
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "[OK] Database `$dbName` siap digunakan.\n";

    // 2. Gunakan database tersebut
    $pdo->exec("USE `$dbName`;");

    // 3. Hapus tabel lama jika ada (agar perubahan kolom langsung diterapkan)
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS `$table`;");
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    echo "[OK] Tabel lama dibersihkan (fresh migrate).\n";

    // 4. Eksekusi setiap file tabel secara berurutan
    foreach ($sqlFiles as $file) {
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . $file;

        if (!file_exists($filePath)) {
            throw new RuntimeException("File tidak ditemukan: $filePath");
        }

        $sql = file_get_contents($filePath);
        $pdo->exec($sql);
        echo "[OK] Berhasil mengeksekusi: $file\n";
    }

    echo "\nSelesai! Seluruh tabel berhasil dimigrasi ulang di database `$dbName`.\n";
} catch (PDOException $e) {
    echo "\n[ERROR Database] " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "\n[ERROR] " . $e->getMessage() . "\n";
    exit(1);
}
