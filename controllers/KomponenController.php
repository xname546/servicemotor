<?php

class KomponenController
{
    public const KATEGORI_LIST = [
        'Motor Matic',
        'Motor Bebek',
        'Motor Sport',
        'Mobil Matic',
        'Mobil Manual',
    ];

    private function ensureAdmin(): void
    {
        if (($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
    }

    public function index(): void
    {
        $this->ensureAdmin();
        $pdo = getDB();
        $stmt = $pdo->query("SELECT * FROM `Komponen` ORDER BY `Kode_Komponen` ASC");
        $komponenList = $stmt->fetchAll();

        require __DIR__ . '/../views/komponen/index.php';
    }

    public function create(): void
    {
        $this->ensureAdmin();
        require __DIR__ . '/../views/komponen/create.php';
    }

    public function store(): void
    {
        $this->ensureAdmin();

        $kode = strtoupper(trim($_POST['Kode_Komponen'] ?? ''));
        $nama = trim($_POST['Nama_Komponen'] ?? '');
        $kategori = trim($_POST['Kategori'] ?? '');
        $intervalCekRaw = trim((string)($_POST['Interval_Cek_KM'] ?? ''));
        $intervalGantiRaw = trim((string)($_POST['Interval_Ganti_KM'] ?? ''));
        $keterangan = trim($_POST['Keterangan'] ?? '');

        $intervalCek = ($intervalCekRaw !== '' && (int)$intervalCekRaw > 0) ? (int)$intervalCekRaw : null;
        $intervalGanti = ($intervalGantiRaw !== '' && (int)$intervalGantiRaw > 0) ? (int)$intervalGantiRaw : null;

        if ($kode === '' || $nama === '' || $kategori === '') {
            setFlash('danger', 'Kode, Nama Komponen, dan Kategori Kendaraan wajib diisi.');
            header('Location: index.php?page=komponen&action=create');
            exit;
        }

        if ($intervalCek === null && $intervalGanti === null) {
            setFlash('danger', 'Minimal salah satu dari Interval Cek KM atau Interval Ganti KM harus diisi (> 0).');
            header('Location: index.php?page=komponen&action=create');
            exit;
        }

        if ($intervalCek !== null && $intervalGanti !== null && $intervalCek >= $intervalGanti) {
            setFlash('danger', 'Jika keduanya diisi, Interval Cek KM harus lebih kecil dari Interval Ganti KM.');
            header('Location: index.php?page=komponen&action=create');
            exit;
        }

        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("
                INSERT INTO `Komponen` (`Kode_Komponen`, `Nama_Komponen`, `Kategori`, `Interval_Cek_KM`, `Interval_Ganti_KM`, `Keterangan`)
                VALUES (:kode, :nama, :kategori, :cek, :ganti, :keterangan)
            ");
            $stmt->execute([
                'kode' => $kode,
                'nama' => $nama,
                'kategori' => $kategori,
                'cek' => $intervalCek,
                'ganti' => $intervalGanti,
                'keterangan' => $keterangan !== '' ? $keterangan : null,
            ]);

            setFlash('success', 'Master komponen baru berhasil ditambahkan.');
            header('Location: index.php?page=komponen');
            exit;
        } catch (PDOException $e) {
            setFlash('danger', 'Gagal menyimpan komponen (Kode Komponen mungkin sudah terdaftar).');
            header('Location: index.php?page=komponen&action=create');
            exit;
        }
    }

    public function edit(): void
    {
        $this->ensureAdmin();
        $id = (int)($_GET['id'] ?? 0);

        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM `Komponen` WHERE `ID_Komponen` = :id");
        $stmt->execute(['id' => $id]);
        $komponen = $stmt->fetch();

        if (!$komponen) {
            setFlash('danger', 'Data komponen tidak ditemukan.');
            header('Location: index.php?page=komponen');
            exit;
        }

        require __DIR__ . '/../views/komponen/edit.php';
    }

    public function update(): void
    {
        $this->ensureAdmin();

        $id = (int)($_POST['ID_Komponen'] ?? 0);
        $kode = strtoupper(trim($_POST['Kode_Komponen'] ?? ''));
        $nama = trim($_POST['Nama_Komponen'] ?? '');
        $kategori = trim($_POST['Kategori'] ?? '');
        $intervalCekRaw = trim((string)($_POST['Interval_Cek_KM'] ?? ''));
        $intervalGantiRaw = trim((string)($_POST['Interval_Ganti_KM'] ?? ''));
        $keterangan = trim($_POST['Keterangan'] ?? '');

        $intervalCek = ($intervalCekRaw !== '' && (int)$intervalCekRaw > 0) ? (int)$intervalCekRaw : null;
        $intervalGanti = ($intervalGantiRaw !== '' && (int)$intervalGantiRaw > 0) ? (int)$intervalGantiRaw : null;

        if ($id <= 0 || $kode === '' || $nama === '' || $kategori === '') {
            setFlash('danger', 'Kode, Nama Komponen, dan Kategori Kendaraan wajib diisi.');
            header('Location: index.php?page=komponen&action=edit&id=' . $id);
            exit;
        }

        if ($intervalCek === null && $intervalGanti === null) {
            setFlash('danger', 'Minimal salah satu dari Interval Cek KM atau Interval Ganti KM harus diisi (> 0).');
            header('Location: index.php?page=komponen&action=edit&id=' . $id);
            exit;
        }

        if ($intervalCek !== null && $intervalGanti !== null && $intervalCek >= $intervalGanti) {
            setFlash('danger', 'Jika keduanya diisi, Interval Cek KM harus lebih kecil dari Interval Ganti KM.');
            header('Location: index.php?page=komponen&action=edit&id=' . $id);
            exit;
        }

        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("
                UPDATE `Komponen`
                SET `Kode_Komponen` = :kode,
                    `Nama_Komponen` = :nama,
                    `Kategori` = :kategori,
                    `Interval_Cek_KM` = :cek,
                    `Interval_Ganti_KM` = :ganti,
                    `Keterangan` = :keterangan
                WHERE `ID_Komponen` = :id
            ");
            $stmt->execute([
                'kode' => $kode,
                'nama' => $nama,
                'kategori' => $kategori,
                'cek' => $intervalCek,
                'ganti' => $intervalGanti,
                'keterangan' => $keterangan !== '' ? $keterangan : null,
                'id' => $id,
            ]);

            setFlash('success', 'Master komponen berhasil diperbarui.');
            header('Location: index.php?page=komponen');
            exit;
        } catch (PDOException $e) {
            setFlash('danger', 'Gagal memperbarui komponen (Kode Komponen mungkin sudah digunakan).');
            header('Location: index.php?page=komponen&action=edit&id=' . $id);
            exit;
        }
    }
}
