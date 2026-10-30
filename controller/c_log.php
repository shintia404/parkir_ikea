<?php
// Controller Log Aktivitas: menampilkan seluruh log (khusus admin).
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Harus login
if (empty($_SESSION['login'])) {
    header('Location: ../view/login.php');
    exit();
}

// Hanya admin yang boleh melihat log
if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../view/DashBoard.php');
    exit();
}

require_once __DIR__ . '/../models/koneksi.php';
require_once __DIR__ . '/../models/m_log.php';

$koneksi_obj = new koneksi();
$db          = $koneksi_obj->koneksi;

$cari       = trim($_GET['cari'] ?? '');
$per_halaman = 20;
$halaman    = max(1, (int) ($_GET['halaman'] ?? 1));

$total_data  = hitung_log($db, $cari);
$total_hal   = max(1, (int) ceil($total_data / $per_halaman));
$halaman     = min($halaman, $total_hal);
$offset      = ($halaman - 1) * $per_halaman;

$data_log = ambil_semua_log($db, $cari, $per_halaman, $offset);

include __DIR__ . '/../view/v_log_aktivitas.php';
exit();
