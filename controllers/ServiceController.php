<?php

class ServiceController
{
    private function getActiveKendaraanId(): int
    {
        if (($_SESSION['role'] ?? '') !== 'kendaraan' || empty($_SESSION['kendaraan_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        return (int)$_SESSION['kendaraan_id'];
    }

    public function index(): void
    {
        $idKendaraan = $this->getActiveKendaraanId();
        $pdo = getDB();

        // Urutkan kronologis dari awal ke akhir untuk menentukan Servis ke-1 .. ke-N
        $stmt = $pdo->prepare("
            SELECT * FROM `Service`
            WHERE `ID_Kendaraan` = :id
            ORDER BY `Tanggal_Service` ASC, `KM_Service` ASC, `ID_Service` ASC
        ");
        $stmt->execute(['id' => $idKendaraan]);
        $rows = $stmt->fetchAll();

        $total = count($rows);
        foreach ($rows as $idx => &$row) {
            $row['Urutan_Service'] = $idx + 1;
            $row['Total_Service_Kendaraan'] = $total;
        }
        unset($row);

        // Balik urutan agar servis terbaru berada di paling atas
        $serviceList = array_reverse($rows);

        require __DIR__ . '/../views/service/index.php';
    }

    public function create(): void
    {
        $idKendaraan = $this->getActiveKendaraanId();
        $pdo = getDB();

        $stmt = $pdo->prepare("SELECT * FROM `Kendaraan` WHERE `ID_Kendaraan` = :id");
        $stmt->execute(['id' => $idKendaraan]);
        $kendaraan = $stmt->fetch();
        $todayDate = date('Y-m-d');

        require __DIR__ . '/../views/service/create.php';
    }

    public function store(): void
    {
        $idKendaraan = $this->getActiveKendaraanId();
        $pdo = getDB();

        $stmtKend = $pdo->prepare("SELECT `KM_Aktual` FROM `Kendaraan` WHERE `ID_Kendaraan` = :id");
        $stmtKend->execute(['id' => $idKendaraan]);
        $kendaraan = $stmtKend->fetch();

        $tanggal = trim($_POST['Tanggal_Service'] ?? date('Y-m-d'));
        $kmService = max(0, (int)($_POST['KM_Service'] ?? 0));
        $lokasi = trim($_POST['Lokasi_Bengkel'] ?? '');
        $biayaJasa = max(0, (float)($_POST['Biaya_Jasa'] ?? 0));
        $catatan = trim($_POST['Catatan_Service'] ?? '');

        if ($tanggal === '' || $lokasi === '') {
            setFlash('danger', 'Tanggal Servis dan Lokasi Bengkel wajib diisi.');
            header('Location: index.php?page=service&action=create');
            exit;
        }

        $notaPath = uploadNotaFile($idKendaraan, $tanggal);
        $kmAktual = (int)($kendaraan['KM_Aktual'] ?? 0);

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO `Service` (
                    `ID_Kendaraan`, `Tanggal_Service`, `Lokasi_Bengkel`, `KM_Service`,
                    `Biaya_Jasa`, `Total_Biaya_Komponen`, `Total_Biaya`, `Catatan_Service`, `Nota`
                ) VALUES (
                    :id_kendaraan, :tanggal, :lokasi, :km,
                    :biaya_jasa, 0, :total_biaya, :catatan, :nota
                )
            ");
            $stmt->execute([
                'id_kendaraan' => $idKendaraan,
                'tanggal' => $tanggal,
                'lokasi' => $lokasi,
                'km' => $kmService,
                'biaya_jasa' => $biayaJasa,
                'total_biaya' => $biayaJasa,
                'catatan' => $catatan !== '' ? $catatan : null,
                'nota' => $notaPath,
            ]);

            $idService = (int)$pdo->lastInsertId();

            // Aturan Update Odometer: Hanya update KM_Aktual jika KM_Service > KM_Aktual saat ini
            if ($kmService > $kmAktual) {
                $updKend = $pdo->prepare("
                    UPDATE `Kendaraan`
                    SET `KM_Aktual` = :km, `Tgl_Update_KM` = :tgl
                    WHERE `ID_Kendaraan` = :id
                ");
                $updKend->execute([
                    'km' => $kmService,
                    'tgl' => $tanggal,
                    'id' => $idKendaraan,
                ]);
            }

            $pdo->commit();

            setFlash('success', 'Data servis berhasil dibuat. Silakan tambahkan tindakan komponen (Ganti / Cek) pada servis ini.');
            header('Location: index.php?page=service&action=show&id=' . $idService);
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            setFlash('danger', 'Gagal menyimpan transaksi servis.');
            header('Location: index.php?page=service&action=create');
            exit;
        }
    }

    public function edit(): void
    {
        $idKendaraan = $this->getActiveKendaraanId();
        $idService = (int)($_GET['id'] ?? 0);

        $pdo = getDB();
        $stmtKend = $pdo->prepare("SELECT * FROM `Kendaraan` WHERE `ID_Kendaraan` = :id");
        $stmtKend->execute(['id' => $idKendaraan]);
        $kendaraan = $stmtKend->fetch();

        $stmt = $pdo->prepare("
            SELECT * FROM `Service`
            WHERE `ID_Service` = :id_service AND `ID_Kendaraan` = :id_kendaraan
        ");
        $stmt->execute([
            'id_service' => $idService,
            'id_kendaraan' => $idKendaraan,
        ]);
        $service = $stmt->fetch();

        if (!$service) {
            setFlash('danger', 'Data transaksi servis tidak ditemukan.');
            header('Location: index.php?page=service');
            exit;
        }

        require __DIR__ . '/../views/service/edit.php';
    }

    public function update(): void
    {
        $idKendaraan = $this->getActiveKendaraanId();
        $idService = (int)($_POST['ID_Service'] ?? 0);

        $pdo = getDB();
        $stmt = $pdo->prepare("
            SELECT * FROM `Service`
            WHERE `ID_Service` = :id_service AND `ID_Kendaraan` = :id_kendaraan
        ");
        $stmt->execute([
            'id_service' => $idService,
            'id_kendaraan' => $idKendaraan,
        ]);
        $service = $stmt->fetch();

        if (!$service) {
            setFlash('danger', 'Data transaksi servis tidak ditemukan.');
            header('Location: index.php?page=service');
            exit;
        }

        $tanggal = trim($_POST['Tanggal_Service'] ?? $service['Tanggal_Service']);
        $kmService = max(0, (int)($_POST['KM_Service'] ?? $service['KM_Service']));
        $lokasi = trim($_POST['Lokasi_Bengkel'] ?? '');
        $biayaJasa = max(0, (float)($_POST['Biaya_Jasa'] ?? 0));
        $catatan = trim($_POST['Catatan_Service'] ?? '');

        if ($tanggal === '' || $lokasi === '') {
            setFlash('danger', 'Tanggal Servis dan Lokasi Bengkel wajib diisi.');
            header('Location: index.php?page=service&action=edit&id=' . $idService);
            exit;
        }

        $newNotaPath = uploadNotaFile($idKendaraan, $tanggal);
        $notaFinal = $newNotaPath !== null ? $newNotaPath : $service['Nota'];

        try {
            $pdo->beginTransaction();

            $totalKomponen = (float)$service['Total_Biaya_Komponen'];
            $totalBiaya = $biayaJasa + $totalKomponen;

            $upd = $pdo->prepare("
                UPDATE `Service`
                SET `Tanggal_Service` = :tanggal,
                    `Lokasi_Bengkel` = :lokasi,
                    `KM_Service` = :km,
                    `Biaya_Jasa` = :biaya_jasa,
                    `Total_Biaya` = :total_biaya,
                    `Catatan_Service` = :catatan,
                    `Nota` = :nota
                WHERE `ID_Service` = :id_service AND `ID_Kendaraan` = :id_kendaraan
            ");
            $upd->execute([
                'tanggal' => $tanggal,
                'lokasi' => $lokasi,
                'km' => $kmService,
                'biaya_jasa' => $biayaJasa,
                'total_biaya' => $totalBiaya,
                'catatan' => $catatan !== '' ? $catatan : null,
                'nota' => $notaFinal,
                'id_service' => $idService,
                'id_kendaraan' => $idKendaraan,
            ]);

            // Cek apakah KM_Service baru > KM_Aktual kendaraan
            $stmtKend = $pdo->prepare("SELECT `KM_Aktual` FROM `Kendaraan` WHERE `ID_Kendaraan` = :id");
            $stmtKend->execute(['id' => $idKendaraan]);
            $kmAktual = (int)$stmtKend->fetchColumn();

            if ($kmService > $kmAktual) {
                $updKend = $pdo->prepare("
                    UPDATE `Kendaraan`
                    SET `KM_Aktual` = :km, `Tgl_Update_KM` = :tgl
                    WHERE `ID_Kendaraan` = :id
                ");
                $updKend->execute([
                    'km' => $kmService,
                    'tgl' => $tanggal,
                    'id' => $idKendaraan,
                ]);
            }

            // Rekalkulasi KM_Terakhir_Cek & KM_Terakhir_Ganti untuk semua komponen pada servis ini
            $stmtKomps = $pdo->prepare("SELECT DISTINCT `ID_Komponen` FROM `Service_Komponen` WHERE `ID_Service` = :id_service");
            $stmtKomps->execute(['id_service' => $idService]);
            $skController = new ServiceKomponenController();
            foreach ($stmtKomps->fetchAll() as $kRow) {
                $skController->recalculateLastGantiKomponen($pdo, $idKendaraan, (int)$kRow['ID_Komponen']);
            }

            $pdo->commit();

            setFlash('success', 'Data header transaksi servis berhasil diperbarui.');
            header('Location: index.php?page=service&action=show&id=' . $idService);
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            setFlash('danger', 'Gagal memperbarui transaksi servis.');
            header('Location: index.php?page=service&action=edit&id=' . $idService);
            exit;
        }
    }

    public function show(): void
    {
        $idKendaraan = $this->getActiveKendaraanId();
        $idService = (int)($_GET['id'] ?? 0);

        $pdo = getDB();
        $stmtKend = $pdo->prepare("SELECT * FROM `Kendaraan` WHERE `ID_Kendaraan` = :id");
        $stmtKend->execute(['id' => $idKendaraan]);
        $kendaraan = $stmtKend->fetch();

        $stmt = $pdo->prepare("
            SELECT * FROM `Service`
            WHERE `ID_Service` = :id_service AND `ID_Kendaraan` = :id_kendaraan
        ");
        $stmt->execute([
            'id_service' => $idService,
            'id_kendaraan' => $idKendaraan,
        ]);
        $service = $stmt->fetch();

        if (!$service) {
            setFlash('danger', 'Data transaksi servis tidak ditemukan.');
            header('Location: index.php?page=service');
            exit;
        }

        $stmtDetail = $pdo->prepare("
            SELECT sk.*, k.`Kode_Komponen`, k.`Nama_Komponen`
            FROM `Service_Komponen` sk
            JOIN `Komponen` k ON sk.`ID_Komponen` = k.`ID_Komponen`
            WHERE sk.`ID_Service` = :id_service
            ORDER BY sk.`ID_Service_Komponen` ASC
        ");
        $stmtDetail->execute(['id_service' => $idService]);
        $detailKomponenList = $stmtDetail->fetchAll();

        // Filter pilihan komponen sesuai Jenis_Kendaraan dari kendaraan aktif
        $stmtMaster = $pdo->prepare("
            SELECT * FROM `Komponen`
            WHERE `Kategori` = :kategori
            ORDER BY `Kode_Komponen` ASC
        ");
        $stmtMaster->execute(['kategori' => $kendaraan['Jenis_Kendaraan']]);
        $masterKomponenList = $stmtMaster->fetchAll();

        require __DIR__ . '/../views/service/show.php';
    }
}
