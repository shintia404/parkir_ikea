<?php
// Controller Riwayat Parkir: menampilkan kendaraan yang SUDAH KELUAR
// (kendaraan yang masih parkir ada di Data Parkir / c_transaksi.php).
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Harus login dulu
if (empty($_SESSION['login'])) {
    header('Location: ../view/login.php');
    exit();
}

// Riwayat boleh dilihat admin, petugas, dan owner
if (!in_array($_SESSION['role'] ?? '', ['admin', 'petugas', 'owner'], true)) {
    header('Location: ../view/login.php');
    exit();
}

include_once __DIR__ . '/../models/m_transaksi.php';

$koneksi_obj     = new koneksi();
$transaksi_model = new M_Transaksi($koneksi_obj->koneksi);

$data_riwayat = $transaksi_model->tampil_riwayat();

include __DIR__ . '/../view/v_riwayat.php';
exit();
