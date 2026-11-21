<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Daftar Riwayat Kunjungan Servis</h2>
        <p class="text-muted small mb-0">Catatan kronologis seluruh transaksi servis kendaraan aktif beserta foto nota &amp; rincian komponen</p>
    </div>
    <a href="index.php?page=service&amp;action=create" class="btn btn-primary fw-semibold">+ Catat Servis</a>
</div>

<div class="row g-3">
    <?php if (empty($serviceList)): ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5 text-center text-muted">
                    Belum ada riwayat transaksi servis pada kendaraan ini.
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($serviceList as $srv): ?>
            <?php
            $urutan = (int)$srv['Urutan_Service'];
            $totalSrv = (int)$srv['Total_Service_Kendaraan'];
            ?>
            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge bg-dark">Servis ke-<?= $urutan ?></span>
                                    <?php if ($urutan === $totalSrv): ?>
                                        <span class="badge bg-success">Servis Terakhir</span>
                                    <?php endif; ?>
                                    <?php if ($urutan === 1): ?>
                                        <span class="badge bg-secondary">Servis Pertama</span>
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-primary fs-6"><?= formatRupiah($srv['Total_Biaya']) ?></span>
                            </div>

                            <h3 class="h5 fw-bold mb-1"><?= htmlspecialchars($srv['Tanggal_Service']) ?></h3>
                            <div class="small text-muted mb-2">
                                <?= htmlspecialchars($srv['Lokasi_Bengkel'] ?: '-') ?> | <strong><?= formatKM($srv['KM_Service']) ?></strong>
                            </div>

                            <?php if (!empty($srv['Catatan_Service'])): ?>
                                <div class="small text-muted bg-light p-2 rounded border mb-2">
                                    Catatan: <?= htmlspecialchars($srv['Catatan_Service']) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex flex-wrap gap-2 pt-3 border-top mt-2">
                            <a href="index.php?page=service&amp;action=show&amp;id=<?= (int)$srv['ID_Service'] ?>" class="btn btn-outline-primary btn-sm fw-semibold flex-grow-1">
                                Lihat Detail &amp; Komponen
                            </a>
                            <?php if (!empty($srv['Nota'])): ?>
                                <a href="<?= htmlspecialchars($srv['Nota']) ?>" target="_blank" class="btn btn-outline-success btn-sm">
                                    Nota
                                </a>
                            <?php endif; ?>
                            <a href="index.php?page=service&amp;action=edit&amp;id=<?= (int)$srv['ID_Service'] ?>" class="btn btn-outline-secondary btn-sm">
                                Ubah
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
