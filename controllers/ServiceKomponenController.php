<?php

class ServiceKomponenController
{
    private function getActiveKendaraanId(): int
    {
        if (($_SESSION['role'] ?? '') !== 'kendaraan' || empty($_SESSION['kendaraan_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        return (int)$_SESSION['kendaraan_id'];
    }

    public function create(): void
    {
        $idKendaraan = $this->getActiveKendaraanId();
        $idService = (int)($_GET['service_id'] ?? 0);
        header('Location: index.php?page=service&action=show&id=' . $idService);
        exit;
    }

    public function store(): void
    {
        $idKendaraan = $this->getActiveKendaraanId();
        $idService = (int)($_POST['ID_Service'] ?? 0);
        $idKomponen = (int)($_POST['ID_Komponen'] ?? 0);
        $tindakan = ($_POST['Tindakan'] ?? 'Ganti') === 'Cek' ? 'Cek' : 'Ganti';
        $qty = max(1, (int)($_POST['Qty'] ?? 1));
        $hargaSatuan = max(0, (float)($_POST['Harga_Satuan'] ?? 0));
        $keterangan = trim($_POST['Keterangan'] ?? '');

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

        if (!$service || $idKomponen <= 0) {
            setFlash('danger', 'Data servis atau komponen tidak valid.');
            header('Location: index.php?page=service');
            exit;
        }

        $subtotal = $qty * $hargaSatuan;

        try {
            $pdo->beginTransaction();

            // 1. Simpan detail tindakan komponen ke Service_Komponen
            $ins = $pdo->prepare("
                INSERT INTO `Service_Komponen` (`ID_Service`, `ID_Komponen`, `Tindakan`, `Qty`, `Harga_Satuan`, `Subtotal_Harga`, `Keterangan`)
                VALUES (:id_service, :id_komponen, :tindakan, :qty, :harga, :subtotal, :keterangan)
            ");
            $ins->execute([
                'id_service' => $idService,
                'id_komponen' => $idKomponen,
                'tindakan' => $tindakan,
                'qty' => $qty,
                'harga' => $hargaSatuan,
                'subtotal' => $subtotal,
                'keterangan' => $keterangan !== '' ? $keterangan : null,
            ]);

            // 2. Hitung ulang Total_Biaya_Komponen & Total_Biaya pada tabel Service
            $this->recalculateServiceTotal($pdo, $idService, (float)$service['Biaya_Jasa']);

            // 3. Pastikan komponen terdaftar di Kendaraan_Komponen & rekalkulasi posisi KM_Terakhir_Cek dan KM_Terakhir_Ganti
            $this->ensureKendaraanKomponenExists($pdo, $idKendaraan, $idKomponen);
            $this->recalculateLastGantiKomponen($pdo, $idKendaraan, $idKomponen);

            $pdo->commit();

            setFlash('success', 'Tindakan komponen (' . $tindakan . ') berhasil dicatat dan riwayat KM komponen telah diperbarui.');
            header('Location: index.php?page=service&action=show&id=' . $idService);
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            setFlash('danger', 'Gagal menyimpan tindakan komponen servis.');
            header('Location: index.php?page=service&action=show&id=' . $idService);
            exit;
        }
    }

    public function delete(): void
    {
        $idKendaraan = $this->getActiveKendaraanId();
        $idSk = (int)($_POST['ID_Service_Komponen'] ?? 0);
        $idService = (int)($_POST['ID_Service'] ?? 0);

        $pdo = getDB();
        $stmt = $pdo->prepare("
            SELECT s.*, sk.`ID_Komponen`
            FROM `Service_Komponen` sk
            JOIN `Service` s ON sk.`ID_Service` = s.`ID_Service`
            WHERE sk.`ID_Service_Komponen` = :id_sk
              AND s.`ID_Service` = :id_service
              AND s.`ID_Kendaraan` = :id_kendaraan
        ");
        $stmt->execute([
            'id_sk' => $idSk,
            'id_service' => $idService,
            'id_kendaraan' => $idKendaraan,
        ]);
        $row = $stmt->fetch();

        if ($row) {
            try {
                $pdo->beginTransaction();

                $del = $pdo->prepare("DELETE FROM `Service_Komponen` WHERE `ID_Service_Komponen` = :id_sk");
                $del->execute(['id_sk' => $idSk]);

                // Hitung ulang total biaya di Service
                $this->recalculateServiceTotal($pdo, $idService, (float)$row['Biaya_Jasa']);

                // Rekalkulasi KM_Terakhir_Cek & KM_Terakhir_Ganti dari riwayat yang tersisa
                $this->recalculateLastGantiKomponen($pdo, $idKendaraan, (int)$row['ID_Komponen']);

                $pdo->commit();
                setFlash('success', 'Item tindakan komponen berhasil dihapus dari transaksi servis.');
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                setFlash('danger', 'Gagal menghapus komponen servis.');
            }
        }

        header('Location: index.php?page=service&action=show&id=' . $idService);
        exit;
    }

    public function recalculateServiceTotal(PDO $pdo, int $idService, float $biayaJasa): void
    {
        $stmtSum = $pdo->prepare("
            SELECT COALESCE(SUM(`Subtotal_Harga`), 0) AS total_komponen
            FROM `Service_Komponen`
            WHERE `ID_Service` = :id_service
        ");
        $stmtSum->execute(['id_service' => $idService]);
        $totalKomponen = (float)$stmtSum->fetchColumn();
        $totalBiaya = $biayaJasa + $totalKomponen;

        $upd = $pdo->prepare("
            UPDATE `Service`
            SET `Total_Biaya_Komponen` = :total_komp, `Total_Biaya` = :total_biaya
            WHERE `ID_Service` = :id_service
        ");
        $upd->execute([
            'total_komp' => $totalKomponen,
            'total_biaya' => $totalBiaya,
            'id_service' => $idService,
        ]);
    }

    private function ensureKendaraanKomponenExists(PDO $pdo, int $idKendaraan, int $idKomponen): void
    {
        $cek = $pdo->prepare("
            SELECT `ID_Kendaraan_Komponen`
            FROM `Kendaraan_Komponen`
            WHERE `ID_Kendaraan` = :id_kendaraan AND `ID_Komponen` = :id_komponen
        ");
        $cek->execute([
            'id_kendaraan' => $idKendaraan,
            'id_komponen' => $idKomponen,
        ]);

        if (!$cek->fetch()) {
            $ins = $pdo->prepare("
                INSERT INTO `Kendaraan_Komponen` (
                    `ID_Kendaraan`, `ID_Komponen`,
                    `KM_Terakhir_Cek`, `Tanggal_Terakhir_Cek`,
                    `KM_Terakhir_Ganti`, `Tanggal_Terakhir_Ganti`
                ) VALUES (:id_kendaraan, :id_komponen, NULL, NULL, NULL, NULL)
            ");
            $ins->execute([
                'id_kendaraan' => $idKendaraan,
                'id_komponen' => $idKomponen,
            ]);
        }
    }

    public function recalculateLastGantiKomponen(PDO $pdo, int $idKendaraan, int $idKomponen): void
    {
        // 1. Cari KM_Service tertinggi untuk semua tindakan ('Cek' maupun 'Ganti') -> KM_Terakhir_Cek
        $stmtCek = $pdo->prepare("
            SELECT s.`KM_Service`, s.`Tanggal_Service`
            FROM `Service_Komponen` sk
            JOIN `Service` s ON sk.`ID_Service` = s.`ID_Service`
            WHERE s.`ID_Kendaraan` = :id_kendaraan AND sk.`ID_Komponen` = :id_komponen
            ORDER BY s.`KM_Service` DESC, s.`Tanggal_Service` DESC, s.`ID_Service` DESC
            LIMIT 1
        ");
        $stmtCek->execute([
            'id_kendaraan' => $idKendaraan,
            'id_komponen' => $idKomponen,
        ]);
        $lastCek = $stmtCek->fetch();

        // 2. Cari KM_Service tertinggi khusus tindakan 'Ganti' -> KM_Terakhir_Ganti
        $stmtGanti = $pdo->prepare("
            SELECT s.`KM_Service`, s.`Tanggal_Service`
            FROM `Service_Komponen` sk
            JOIN `Service` s ON sk.`ID_Service` = s.`ID_Service`
            WHERE s.`ID_Kendaraan` = :id_kendaraan
              AND sk.`ID_Komponen` = :id_komponen
              AND sk.`Tindakan` = 'Ganti'
            ORDER BY s.`KM_Service` DESC, s.`Tanggal_Service` DESC, s.`ID_Service` DESC
            LIMIT 1
        ");
        $stmtGanti->execute([
            'id_kendaraan' => $idKendaraan,
            'id_komponen' => $idKomponen,
        ]);
        $lastGanti = $stmtGanti->fetch();

        $upd = $pdo->prepare("
            UPDATE `Kendaraan_Komponen`
            SET `KM_Terakhir_Cek` = :km_cek,
                `Tanggal_Terakhir_Cek` = :tgl_cek,
                `KM_Terakhir_Ganti` = :km_ganti,
                `Tanggal_Terakhir_Ganti` = :tgl_ganti
            WHERE `ID_Kendaraan` = :id_kendaraan AND `ID_Komponen` = :id_komponen
        ");
        $upd->execute([
            'km_cek' => $lastCek ? (int)$lastCek['KM_Service'] : null,
            'tgl_cek' => $lastCek ? $lastCek['Tanggal_Service'] : null,
            'km_ganti' => $lastGanti ? (int)$lastGanti['KM_Service'] : null,
            'tgl_ganti' => $lastGanti ? $lastGanti['Tanggal_Service'] : null,
            'id_kendaraan' => $idKendaraan,
            'id_komponen' => $idKomponen,
        ]);
    }
}
