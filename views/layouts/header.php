<?php
if (!defined('APP_RUNNING')) {
    header('Location: ../../index.php');
    exit;
}
$flash = getFlash();
$currentPage = $_GET['page'] ?? '';
$currentAction = $_GET['action'] ?? 'index';
$namaKendaraanAktif = $_SESSION['kendaraan_nama'] ?? 'Kendaraan';
$nopolKendaraanAktif = $_SESSION['kendaraan_nopol'] ?? '-';
$jenisKendaraanAktif = $_SESSION['kendaraan_jenis'] ?? '-';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($namaKendaraanAktif) ?> - Dashboard ServisKendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row min-vh-100">
        <!-- Sidebar Desktop Dashboard Kendaraan -->
        <aside class="col-12 col-md-3 col-lg-2 bg-dark text-white p-3 d-flex flex-column">
            <div class="pb-3 mb-3 border-bottom border-secondary">
                <div class="fs-5 fw-bold text-white">ServisKendaraan</div>
                <div class="small text-white-50">Dashboard Pemilik Kendaraan</div>
            </div>

            <!-- Info Singkat Kendaraan Aktif di Sidebar -->
            <div class="bg-secondary bg-opacity-25 rounded p-3 mb-3 border border-secondary">
                <div class="small text-white-50">Kendaraan Aktif</div>
                <div class="fw-bold text-white"><?= htmlspecialchars($namaKendaraanAktif) ?></div>
                <div class="d-flex flex-wrap gap-1 mt-1">
                    <span class="badge bg-primary"><?= htmlspecialchars($nopolKendaraanAktif) ?></span>
                    <span class="badge bg-secondary"><?= htmlspecialchars($jenisKendaraanAktif) ?></span>
                </div>
            </div>

            <div class="text-uppercase text-white-50 small fw-bold mb-2 px-2">Menu Navigasi</div>
            <ul class="nav nav-pills flex-column gap-1 mb-auto">
                <li class="nav-item">
                    <a href="index.php?page=dashboard" class="nav-link <?= ($currentPage === 'dashboard') ? 'active fw-semibold' : 'text-white' ?>">
                        Beranda &amp; Cek KM
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=kendaraan_komponen" class="nav-link <?= ($currentPage === 'kendaraan_komponen') ? 'active fw-semibold' : 'text-white' ?>">
                        Komponen Kendaraan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=service" class="nav-link <?= (in_array($currentPage, ['service', 'service_komponen'], true)) ? 'active fw-semibold' : 'text-white' ?>">
                        Riwayat Servis
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=kendaraan&amp;action=edit" class="nav-link <?= ($currentPage === 'kendaraan' && $currentAction === 'edit') ? 'active fw-semibold' : 'text-white' ?>">
                        Ubah Profil Kendaraan
                    </a>
                </li>
            </ul>

            <div class="pt-3 mt-3 border-top border-secondary">
                <a href="index.php?page=logout" class="btn btn-outline-light btn-sm w-100">Ganti Akun / Keluar</a>
            </div>
        </aside>

        <!-- Area Konten Utama Desktop Dashboard -->
        <div class="col-12 col-md-9 col-lg-10 p-0 d-flex flex-column">
            <!-- Top Header Bar -->
            <header class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center shadow-sm">
                <div>
                    <span class="text-muted small">Profil Kendaraan:</span>
                    <strong class="ms-1"><?= htmlspecialchars($namaKendaraanAktif) ?></strong>
                    <span class="badge bg-dark ms-1"><?= htmlspecialchars($nopolKendaraanAktif) ?></span>
                    <span class="badge bg-secondary ms-1"><?= htmlspecialchars($jenisKendaraanAktif) ?></span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="index.php?page=kendaraan_komponen&amp;action=create" class="btn btn-sm btn-outline-primary">+ Pantau Komponen</a>
                    <a href="index.php?page=service&amp;action=create" class="btn btn-sm btn-success fw-semibold">+ Catat Servis Baru</a>
                </div>
            </header>

            <!-- Main Workspace -->
            <main class="p-4 flex-grow-1">
                <?php if ($flash): ?>
                    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> py-2 px-3 mb-4 shadow-sm">
                        <?= htmlspecialchars($flash['message']) ?>
                    </div>
                <?php endif; ?>
