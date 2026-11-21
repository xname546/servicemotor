<?php
if (!defined('APP_RUNNING')) {
    header('Location: ../../index.php');
    exit;
}
$flash = getFlash();
$currentPage = $_GET['page'] ?? '';
$currentAction = $_GET['action'] ?? 'index';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ServisKendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row min-vh-100">
        <!-- Sidebar Desktop Dashboard Admin -->
        <aside class="col-12 col-md-3 col-lg-2 bg-dark text-white p-3 d-flex flex-column">
            <div class="pb-3 mb-3 border-bottom border-secondary">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark">ADMIN</span>
                    <span class="fs-5 fw-bold">ServisKendaraan</span>
                </div>
                <div class="small text-white-50 mt-1">Panel Manajemen Master Data</div>
            </div>

            <div class="text-uppercase text-white-50 small fw-bold mb-2 px-2">Menu Master</div>
            <ul class="nav nav-pills flex-column gap-1 mb-auto">
                <li class="nav-item">
                    <a href="index.php?page=kendaraan" class="nav-link <?= ($currentPage === 'kendaraan') ? 'active bg-warning text-dark fw-semibold' : 'text-white' ?>">
                        Daftar Kendaraan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=komponen" class="nav-link <?= ($currentPage === 'komponen') ? 'active bg-warning text-dark fw-semibold' : 'text-white' ?>">
                        Master Komponen
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
                    <span class="text-muted small">Mode Pengguna:</span>
                    <strong class="ms-1">Administrator Sistem</strong>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="index.php?page=kendaraan&amp;action=create" class="btn btn-sm btn-outline-dark">+ Kendaraan Baru</a>
                    <a href="index.php?page=komponen&amp;action=create" class="btn btn-sm btn-dark">+ Master Komponen</a>
                </div>
            </header>

            <!-- Main Workspace -->
            <main class="p-4 flex-grow-1">
                <?php if ($flash): ?>
                    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> py-2 px-3 mb-4 shadow-sm">
                        <?= htmlspecialchars($flash['message']) ?>
                    </div>
                <?php endif; ?>
