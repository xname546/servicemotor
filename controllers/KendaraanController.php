<?php

class KendaraanController
{
    public const JENIS_KENDARAAN_LIST = [
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

    private function getActiveKendaraanId(): int
    {
        if (($_SESSION['role'] ?? '') !== 'kendaraan' || empty($_SESSION['kendaraan_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        return (int)$_SESSION['kendaraan_id'];
    }

    // SISI ADMIN: Daftar seluruh kendaraan
    public function index(): void
    {
        $this->ensureAdmin();
        $pdo = getDB();
        $stmt = $pdo->query("SELECT * FROM `Kendaraan` ORDER BY `ID_Kendaraan` DESC");
        $kendaraanList = $stmt->fetchAll();

        require __DIR__ . '/../views/kendaraan/index.php';
    }

    // SISI ADMIN: Form tambah kendaraan baru
    public function create(): void
    {
        $this->ensureAdmin();
        require __DIR__ . '/../views/kendaraan/create.php';
    }

    // SISI ADMIN: Simpan kendaraan baru
    public function store(): void
    {
        $this->ensureAdmin();

        $nama = trim($_POST['Nama_Kendaraan'] ?? '');
        $jenis = trim($_POST['Jenis_Kendaraan'] ?? '');
        $nopol = strtoupper(trim($_POST['Nomor_Polisi'] ?? ''));
        $kmAktual = max(0, (int)($_POST['KM_Aktual'] ?? 0));

        if ($nama === '' || $jenis === '' || $nopol === '') {
            setFlash('danger', 'Semua kolom wajib diisi.');
            header('Location: index.php?page=kendaraan&action=create');
            exit;
        }

        if (!in_array($jenis, self::JENIS_KENDARAAN_LIST, true)) {
            setFlash('danger', 'Kategori jenis kendaraan tidak valid.');
            header('Location: index.php?page=kendaraan&action=create');
            exit;
        }

        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("
                INSERT INTO `Kendaraan` (`Nama_Kendaraan`, `Jenis_Kendaraan`, `Nomor_Polisi`, `KM_Aktual`, `Tgl_Update_KM`)
                VALUES (:nama, :jenis, :nopol, :km, CURDATE())
            ");
            $stmt->execute([
                'nama' => $nama,
                'jenis' => $jenis,
                'nopol' => $nopol,
                'km' => $kmAktual,
            ]);

            setFlash('success', 'Kendaraan baru berhasil ditambahkan.');
            header('Location: index.php?page=kendaraan');
            exit;
        } catch (PDOException $e) {
            setFlash('danger', 'Gagal menyimpan kendaraan (Nomor Polisi mungkin sudah terdaftar).');
            header('Location: index.php?page=kendaraan&action=create');
            exit;
        }
    }

    // SISI AKUN KENDARAAN: Form ubah data kendaraan aktif
    public function edit(): void
    {
        $idKendaraan = $this->getActiveKendaraanId();
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM `Kendaraan` WHERE `ID_Kendaraan` = :id");
        $stmt->execute(['id' => $idKendaraan]);
        $kendaraan = $stmt->fetch();

        if (!$kendaraan) {
            header('Location: index.php?page=logout');
            exit;
        }

        require __DIR__ . '/../views/kendaraan/edit.php';
    }

    // SISI AKUN KENDARAAN: Simpan perubahan data kendaraan aktif
    public function update(): void
    {
        $idKendaraan = $this->getActiveKendaraanId();

        $nama = trim($_POST['Nama_Kendaraan'] ?? '');
        $jenis = trim($_POST['Jenis_Kendaraan'] ?? '');
        $nopol = strtoupper(trim($_POST['Nomor_Polisi'] ?? ''));
        $kmAktual = max(0, (int)($_POST['KM_Aktual'] ?? 0));

        if ($nama === '' || $jenis === '' || $nopol === '') {
            setFlash('danger', 'Semua kolom wajib diisi.');
            header('Location: index.php?page=kendaraan&action=edit');
            exit;
        }

        if (!in_array($jenis, self::JENIS_KENDARAAN_LIST, true)) {
            setFlash('danger', 'Kategori jenis kendaraan tidak valid.');
            header('Location: index.php?page=kendaraan&action=edit');
            exit;
        }

        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("
                UPDATE `Kendaraan`
                SET `Nama_Kendaraan` = :nama,
                    `Jenis_Kendaraan` = :jenis,
                    `Nomor_Polisi` = :nopol,
                    `KM_Aktual` = :km,
                    `Tgl_Update_KM` = CURDATE()
                WHERE `ID_Kendaraan` = :id
            ");
            $stmt->execute([
                'nama' => $nama,
                'jenis' => $jenis,
                'nopol' => $nopol,
                'km' => $kmAktual,
                'id' => $idKendaraan,
            ]);

            $_SESSION['kendaraan_nama'] = $nama;
            $_SESSION['kendaraan_nopol'] = $nopol;
            $_SESSION['kendaraan_jenis'] = $jenis;

            setFlash('success', 'Data kendaraan berhasil diperbarui.');
            header('Location: index.php?page=dashboard');
            exit;
        } catch (PDOException $e) {
            setFlash('danger', 'Gagal memperbarui data kendaraan (Nomor Polisi mungkin sudah digunakan kendaraan lain).');
            header('Location: index.php?page=kendaraan&action=edit');
            exit;
        }
    }
}
