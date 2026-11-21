<?php
/**
 * Definisi Routing Aplikasi
 */

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/DashboardController.php';
require_once __DIR__ . '/../controllers/KendaraanController.php';
require_once __DIR__ . '/../controllers/KomponenController.php';
require_once __DIR__ . '/../controllers/KendaraanKomponenController.php';
require_once __DIR__ . '/../controllers/ServiceController.php';
require_once __DIR__ . '/../controllers/ServiceKomponenController.php';

$page = $_GET['page'] ?? 'login';
$action = $_GET['action'] ?? 'index';

switch ($page) {
    case 'login':
        $controller = new AuthController();
        if ($action === 'authenticate') {
            $controller->authenticate();
        } else {
            $controller->login();
        }
        break;

    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'dashboard':
        $controller = new DashboardController();
        if ($action === 'cek_km') {
            $controller->cekKm();
        } else {
            $controller->index();
        }
        break;

    case 'kendaraan':
        $controller = new KendaraanController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'store') {
            $controller->store();
        } elseif ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'update') {
            $controller->update();
        } else {
            $controller->index();
        }
        break;

    case 'komponen':
        $controller = new KomponenController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'store') {
            $controller->store();
        } elseif ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'update') {
            $controller->update();
        } else {
            $controller->index();
        }
        break;

    case 'kendaraan_komponen':
        $controller = new KendaraanKomponenController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'store') {
            $controller->store();
        } elseif ($action === 'show') {
            $controller->show();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        break;

    case 'service':
        $controller = new ServiceController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'store') {
            $controller->store();
        } elseif ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'update') {
            $controller->update();
        } elseif ($action === 'show') {
            $controller->show();
        } else {
            $controller->index();
        }
        break;

    case 'service_komponen':
        $controller = new ServiceKomponenController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'store') {
            $controller->store();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            header('Location: index.php?page=service');
            exit;
        }
        break;

    default:
        header('Location: index.php?page=login');
        exit;
}
