<?php include __DIR__ . '/../layouts/header_admin.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Katalog Master Data Komponen</h2>
        <p class="text-muted small mb-0">Daftar komponen berdasarkan kategori kendaraan beserta patokan Interval Cek KM (Kuning) &amp; Interval Ganti KM (Merah)</p>
    </div>
    <a href="index.php?page=komponen&amp;action=create" class="btn btn-dark fw-semibold">+ Tambah Master Komponen</a>
</div>

<div class="row g-3">
    <?php if (empty($komponenList)): ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5 text-center text-muted">
                    Belum ada master komponen. Silakan klik tombol <strong>+ Tambah Master Komponen</strong> di sudut kanan atas.
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($komponenList as $item): ?>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge bg-secondary"><?= htmlspecialchars($item['Kode_Komponen']) ?></span>
                                    <span class="badge bg-dark"><?= htmlspecialchars($item['Kategori']) ?></span>
                                </div>
                                <a href="index.php?page=komponen&amp;action=edit&amp;id=<?= (int)$item['ID_Komponen'] ?>" class="btn btn-outline-secondary btn-sm">Edit</a>
                            </div>
                            <h3 class="h6 fw-bold mb-1"><?= htmlspecialchars($item['Nama_Komponen']) ?></h3>
                            <?php if (!empty($item['Keterangan'])): ?>
                                <p class="small text-muted mb-2"><?= htmlspecialchars($item['Keterangan']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="pt-3 border-top mt-2 small fw-semibold text-primary">
                            <?= htmlspecialchars(formatIntervalKomponen($item)) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer_admin.php'; ?>
