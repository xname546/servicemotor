<?php

class AuthController
{
    public function login(): void
    {
        $pdo = getDB();
        $stmt = $pdo->query("SELECT * FROM `Kendaraan` ORDER BY `Nama_Kendaraan` ASC");
        $kendaraanList = $stmt->fetchAll();

        require __DIR__ . '/../views/auth/login.php';
    }

    public function authenticate(): void
    {
        $akun = trim($_POST['akun'] ?? $_GET['akun'] ?? '');

        if ($akun === '') {
            setFlash('danger', 'Silakan pilih akun terlebih dahulu.');
            header('Location: index.php?page=login');
            exit;
        }

        if ($akun === 'admin') {
            $_SESSION['role'] = 'admin';
            unset(
                $_SESSION['kendaraan_id'],
                $_SESSION['kendaraan_nama'],
                $_SESSION['kendaraan_nopol'],
                $_SESSION['kendaraan_jenis']
            );
            header('Location: index.php?page=kendaraan');
            exit;
        }

        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM `Kendaraan` WHERE `ID_Kendaraan` = :id");
        $stmt->execute(['id' => (int)$akun]);
        $kendaraan = $stmt->fetch();

        if (!$kendaraan) {
            setFlash('danger', 'Data kendaraan tidak ditemukan.');
            header('Location: index.php?page=login');
            exit;
        }

        $_SESSION['role'] = 'kendaraan';
        $_SESSION['kendaraan_id'] = (int)$kendaraan['ID_Kendaraan'];
        $_SESSION['kendaraan_nama'] = $kendaraan['Nama_Kendaraan'];
        $_SESSION['kendaraan_nopol'] = $kendaraan['Nomor_Polisi'];
        $_SESSION['kendaraan_jenis'] = $kendaraan['Jenis_Kendaraan'];

        header('Location: index.php?page=dashboard');
        exit;
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }
}
