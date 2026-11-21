<?php include __DIR__ . '/../layouts/header.php'; ?>

<?php
$jmlHarusGanti = 0;
$jmlPerluDicek = 0;
$jmlAman = 0;
$jmlBelumRiwayat = 0;
foreach ($komponenEvaluasi as $ev) {
    if ($ev['Status'] === 'Harus Diganti') {
        $jmlHarusGanti++;
    } elseif ($ev['Status'] === 'Perlu Dicek') {
        $jmlPerluDicek++;
    } elseif ($ev['Status'] === 'Aman') {
        $jmlAman++;
    } else {
        $jmlBelumRiwayat++;
    }
}
?>

<!-- Ringkasan Statistik Dashboard (Desktop KPI Cards) -->
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm border-start border-danger border-4 h-100">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold">Harus Diganti (Merah)</div>
                <div class="h3 fw-bold text-danger mb-0"><?= $jmlHarusGanti ?> <span class="fs-6 fw-normal text-muted">Komponen</span></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm border-start border-warning border-4 h-100">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold">Perlu Dicek (Kuning)</div>
                <div class="h3 fw-bold text-warning-emphasis mb-0"><?= $jmlPerluDicek ?> <span class="fs-6 fw-normal text-muted">Komponen</span></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm border-start border-success border-4 h-100">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold">Kondisi Aman (Hijau)</div>
                <div class="h3 fw-bold text-success mb-0"><?= $jmlAman ?> <span class="fs-6 fw-normal text-muted">Komponen</span></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm border-start border-secondary border-4 h-100">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold">Belum Ada Riwayat</div>
                <div class="h3 fw-bold text-secondary mb-0"><?= $jmlBelumRiwayat ?> <span class="fs-6 fw-normal text-muted">Komponen</span></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Kolom Kiri: Profil Kendaraan & Form Input Cek KM -->
    <div class="col-12 col-lg-4">
        <!-- 1. Kartu Profil Kendaraan -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold fs-6">Profil Kendaraan Aktif</h5>
                <a href="index.php?page=kendaraan&amp;action=edit" class="btn btn-outline-secondary btn-sm">Ubah Data</a>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <span class="badge bg-dark"><?= htmlspecialchars($kendaraan['Nomor_Polisi']) ?></span>
                        <span class="badge bg-secondary"><?= htmlspecialchars($kendaraan['Jenis_Kendaraan']) ?></span>
                    </div>
                    <h2 class="h4 fw-bold mb-0"><?= htmlspecialchars($kendaraan['Nama_Kendaraan']) ?></h2>
                </div>

                <div class="bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted">Odometer (KM Aktual)</div>
                        <div class="h4 fw-bold text-primary mb-0"><?= formatKM($kendaraan['KM_Aktual']) ?></div>
                    </div>
                    <div class="text-end small text-muted">
                        Update Terakhir<br>
                        <strong><?= htmlspecialchars($kendaraan['Tgl_Update_KM'] ?? '-') ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Kartu Form Cek KM -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold fs-6">Cek Kebutuhan Servis (Update KM)</h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">
                    Masukkan angka kilometer odometer terbaru untuk memperbarui KM aktual dan memeriksa komponen mana yang harus diganti atau dicek.
                </p>

                <form action="index.php?page=dashboard&amp;action=cek_km" method="post">
                    <div class="mb-3">
                        <label for="km_odometer" class="form-label fw-semibold small">Odometer Saat Ini</label>
                        <div class="input-group">
                            <input type="number" id="km_odometer" name="km_odometer" class="form-control" value="<?= (int)$kendaraan['KM_Aktual'] ?>" min="<?= (int)$kendaraan['KM_Aktual'] ?>" required>
                            <span class="input-group-text">KM</span>
                        </div>
                        <div class="form-text">Minimal <?= formatKM($kendaraan['KM_Aktual']) ?></div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-semibold">Update &amp; Cek KM</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Daftar Evaluasi Status Komponen -->
    <div class="col-12 col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="h5 fw-bold mb-0">Status Komponen (Odometer <?= formatKM($kendaraan['KM_Aktual']) ?>)</h3>
                <small class="text-muted">Diurutkan otomatis berdasarkan prioritas: Harus Diganti (Merah) &rarr; Perlu Dicek (Kuning) &rarr; Aman</small>
            </div>
            <a href="index.php?page=service&amp;action=create" class="btn btn-success fw-semibold">+ Catat Servis</a>
        </div>

        <div class="row g-3">
            <?php if (empty($komponenEvaluasi)): ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-5 text-center text-muted">
                            Belum ada komponen yang dipantau pada kendaraan ini.<br>
                            <a href="index.php?page=kendaraan_komponen&amp;action=create" class="btn btn-outline-primary btn-sm mt-3">+ Masukkan Komponen Kendaraan</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($komponenEvaluasi as $item): ?>
                    <?php
                    $status = $item['Status'];
                    if ($status === 'Harus Diganti') {
                        $cardClass = 'bg-danger bg-opacity-10 border border-danger border-opacity-25';
                        $badgeClass = 'bg-danger';
                    } elseif ($status === 'Perlu Dicek') {
                        $cardClass = 'bg-warning bg-opacity-10 border border-warning border-opacity-50';
                        $badgeClass = 'bg-warning text-dark';
                    } elseif ($status === 'Aman') {
                        $cardClass = 'border-0';
                        $badgeClass = 'bg-success';
                    } else {
                        $cardClass = 'border-0';
                        $badgeClass = 'bg-secondary';
                    }
                    ?>
                    <div class="col-12 col-md-6">
                        <div class="card shadow-sm h-100 <?= $cardClass ?>">
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div>
                                            <span class="badge bg-light text-dark border mb-1"><?= htmlspecialchars($item['Kode_Komponen']) ?></span>
                                            <div class="fw-bold"><?= htmlspecialchars($item['Nama_Komponen']) ?></div>
                                        </div>
                                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                                    </div>

                                    <div class="small text-muted mb-2">
                                        Interval: <strong><?= htmlspecialchars(formatIntervalKomponen($item)) ?></strong>
                                    </div>

                                    <div class="small text-muted">
                                        <?php if ($item['KM_Terakhir_Cek_Efektif'] !== null): ?>
                                            <div>Cek Terakhir: <strong><?= formatKM($item['KM_Terakhir_Cek_Efektif']) ?></strong></div>
                                        <?php endif; ?>
                                        <?php if ($item['KM_Terakhir_Ganti'] !== null): ?>
                                            <div>Ganti Terakhir: <strong><?= formatKM($item['KM_Terakhir_Ganti']) ?></strong></div>
                                        <?php endif; ?>
                                        <?php if ($item['KM_Terakhir_Cek_Efektif'] === null && $item['KM_Terakhir_Ganti'] === null): ?>
                                            <div>Belum pernah tercatat cek atau ganti pada servis</div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if ($item['Target_KM_Cek'] !== null || $item['Target_KM_Ganti'] !== null): ?>
                                    <div class="pt-2 border-top mt-2 small">
                                        <?php if ($item['Target_KM_Cek'] !== null): ?>
                                            <div class="d-flex justify-content-between">
                                                <span>Target Cek: <strong><?= formatKM($item['Target_KM_Cek']) ?></strong></span>
                                                <span class="<?= ($item['Sisa_KM_Cek'] <= 0) ? 'text-warning-emphasis fw-bold' : 'text-success fw-semibold' ?>">
                                                    Sisa: <?= formatKM($item['Sisa_KM_Cek']) ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($item['Target_KM_Ganti'] !== null): ?>
                                            <div class="d-flex justify-content-between mt-1">
                                                <span>Target Ganti: <strong><?= formatKM($item['Target_KM_Ganti']) ?></strong></span>
                                                <span class="<?= ($item['Sisa_KM'] <= 0) ? 'text-danger fw-bold' : 'text-success fw-semibold' ?>">
                                                    Sisa: <?= formatKM($item['Sisa_KM']) ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
