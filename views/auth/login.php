<?php
if (!defined('APP_RUNNING')) {
    header('Location: ../../index.php');
    exit;
}
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Akun Masuk - ServisKendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100 py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white text-center py-4">
                    <span class="badge bg-warning text-dark px-3 py-2 mb-2">DASHBOARD SISTEM</span>
                    <h1 class="h4 fw-bold mb-1">Servis &amp; Komponen Kendaraan</h1>
                    <p class="text-white-50 small mb-0">Pilih akun Administrator atau Profil Kendaraan untuk masuk ke dashboard</p>
                </div>
                <div class="card-body p-4 p-md-5">

                    <?php if ($flash): ?>
                        <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> py-2 px-3 small mb-3">
                            <?= htmlspecialchars($flash['message']) ?>
                        </div>
                    <?php endif; ?>

                    <form action="index.php?page=login&amp;action=authenticate" method="post">
                        <div class="mb-4">
                            <label for="akun" class="form-label fw-semibold">Pilih Akun / Kendaraan</label>
                            <select id="akun" name="akun" class="form-select form-select-lg" required>
                                <option value="">-- Pilih Akun --</option>
                                <optgroup label="Administrator">
                                    <option value="admin">Admin (Kelola Kendaraan &amp; Master Komponen)</option>
                                </optgroup>
                                <optgroup label="Akun Kendaraan">
                                    <?php if (!empty($kendaraanList)): ?>
                                        <?php foreach ($kendaraanList as $item): ?>
                                            <option value="<?= (int)$item['ID_Kendaraan'] ?>">
                                                <?= htmlspecialchars($item['Nama_Kendaraan']) ?> (<?= htmlspecialchars($item['Nomor_Polisi']) ?> - <?= htmlspecialchars($item['Jenis_Kendaraan']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>Belum ada kendaraan (Masuk sebagai Admin dulu)</option>
                                    <?php endif; ?>
                                </optgroup>
                            </select>
                            <div class="form-text mt-2">
                                Masuk tanpa password. Seluruh data di dalam dashboard akan menyesuaikan akun yang dipilih.
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-semibold">Masuk ke Dashboard</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
