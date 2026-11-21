<?php
/**
 * Konfigurasi Koneksi Database MySQL (PDO) & Helper Global
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $host = '127.0.0.1';
        $port = '3306';
        $dbName = 'tryone_db';
        $user = 'root';
        $pass = '';

        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$dbName;charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die('Koneksi database gagal: ' . htmlspecialchars($e->getMessage()) . '. Pastikan MySQL aktif dan sudah menjalankan: php database/migrate.php');
        }
    }

    return $pdo;
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function getFlash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function formatRupiah($angka): string
{
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

function formatKM($angka): string
{
    return number_format((int)$angka, 0, ',', '.') . ' KM';
}

function formatIntervalKomponen(array $komp): string
{
    $cek = (int)($komp['Interval_Cek_KM'] ?? 0);
    $ganti = (int)($komp['Interval_Ganti_KM'] ?? 0);
    $parts = [];

    if ($cek > 0) {
        $parts[] = 'Cek: ' . formatKM($cek);
    }
    if ($ganti > 0) {
        $parts[] = 'Ganti: ' . formatKM($ganti);
    }

    return !empty($parts) ? implode(' | ', $parts) : '-';
}

function uploadNotaFile(int $idKendaraan, string $tanggal): ?string
{
    if (!isset($_FILES['file_nota']) || $_FILES['file_nota']['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $tmpPath = $_FILES['file_nota']['tmp_name'];
    $origName = $_FILES['file_nota']['name'] ?? '';
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $allowedExtensions, true)) {
        return null;
    }

    $uploadDir = __DIR__ . '/../uploads/nota';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $safeDate = preg_replace('/[^0-9\-]/', '', $tanggal);
    $fileName = 'Nota_Kendaraan' . $idKendaraan . '_' . $safeDate . '_' . time() . '.' . $ext;
    $destPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

    if (move_uploaded_file($tmpPath, $destPath)) {
        return 'uploads/nota/' . $fileName;
    }

    return null;
}
