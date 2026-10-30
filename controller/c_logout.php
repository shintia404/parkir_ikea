<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Catat log logout sebelum session dihapus
if (!empty($_SESSION['id_user'])) {
    require_once __DIR__ . '/../models/m_log.php';
    $k = new koneksi();
    catat_log($k->koneksi, $_SESSION['id_user'], 'Logout dari sistem');
}

// Menghapus semua data session lalu kembali ke halaman login
$_SESSION = [];
session_destroy();

header("Location: ../view/login.php");
exit;
