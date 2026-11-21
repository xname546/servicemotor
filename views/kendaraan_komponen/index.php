<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Daftar Komponen Kendaraan</h2>
        <p class="text-muted small mb-0">Komponen yang dipantau pada kendaraan ini beserta statistik jumlah riwayat pengecekan &amp; penggantian</p>
    </div>
    <a href="index.php?page=kendaraan_komponen&amp;action=create" class="btn btn-primary fw-semibold">+ Komponen Baru</a>
</div>

<div class="row g-3">
    <?php if (empty($komponenKendaraanList)): ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5 text-center text-muted">
                    Belum ada komponen yang ditambahkan ke kendaraan ini.<br>
                    <a href="index.php?page=kendaraan_komponen&amp;action=create" class="btn btn-primary btn-sm mt-3">+ Tambahkan Komponen Sekarang</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($komponenKendaraanList as $item): ?>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge bg-light text-dark border"><?= htmlspecialchars($item['Kode_Komponen']) ?></span>
                                    <?php if ((int)$item['Total_Riwayat'] > 0): ?>
                                        <span class="badge bg-info text-dark">
                                            <?= (int)$item['Total_Riwayat'] ?> Riwayat (<?= (int)$item['Jumlah_Ganti'] ?>x Ganti, <?= (int)$item['Jumlah_Cek'] ?>x Cek)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">0 Riwayat</span>
                                    <?php endif; ?>
                                </div>

                                <form action="index.php?page=kendaraan_komponen&amp;action=delete" method="post">
                                    <input type="hidden" name="ID_Kendaraan_Komponen" value="<?= (int)$item['ID_Kendaraan_Komponen'] ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                                </form>
                            </div>

                            <h3 class="h6 fw-bold mb-1"><?= htmlspecialchars($item['Nama_Komponen']) ?></h3>
                            <div class="small text-muted mb-3">
                                Interval: <strong><?= htmlspecialchars(formatIntervalKomponen($item)) ?></strong>
                            </div>
                        </div>

                        <div>
                            <div class="bg-light rounded p-2 px-3 small mb-3 border">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Cek Terakhir:</span>
                                    <?php if ($item['KM_Terakhir_Cek'] !== null): ?>
                                        <strong><?= formatKM($item['KM_Terakhir_Cek']) ?> (<?= htmlspecialchars($item['Tanggal_Terakhir_Cek'] ?? '-') ?>)</strong>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Ganti Terakhir:</span>
                                    <?php if ($item['KM_Terakhir_Ganti'] !== null): ?>
                                        <strong class="text-primary"><?= formatKM($item['KM_Terakhir_Ganti']) ?> (<?= htmlspecialchars($item['Tanggal_Terakhir_Ganti'] ?? '-') ?>)</strong>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="d-grid">
                                <a href="index.php?page=kendaraan_komponen&amp;action=show&amp;id=<?= (int)$item['ID_Kendaraan_Komponen'] ?>" class="btn btn-outline-primary btn-sm fw-semibold">
                                    Lihat Riwayat &amp; Interval KM
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
