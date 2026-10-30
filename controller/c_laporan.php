<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- PROTEKSI AKSES ---
// Sebelumnya v_laporan.php dibuka langsung sebagai view, tanpa controller
// dan tanpa cek login sama sekali. Sekarang wajib login dulu, dan boleh
// dilihat admin, petugas, maupun owner (sama seperti halaman Riwayat).
if (empty($_SESSION['login'])) {
    header('Location: ../view/login.php');
    exit();
}
if (!in_array($_SESSION['role'] ?? '', ['admin', 'petugas', 'owner'], true)) {
    header('Location: ../view/login.php');
    exit();
}

include_once __DIR__ . '/../models/m_transaksi.php';

$koneksi_obj     = new koneksi();
$transaksi_model = new M_Transaksi($koneksi_obj->koneksi);

// --- FILTER (dikirim lewat form GET di v_laporan.php) ---
$tgl_mulai = trim($_GET['tanggal_mulai'] ?? '');
$tgl_akhir = trim($_GET['tanggal_akhir'] ?? '');
$jenis     = trim($_GET['jenis_kendaraan'] ?? '');

$data_laporan = $transaksi_model->tampil_laporan($tgl_mulai, $tgl_akhir, $jenis);

// --- RINGKASAN (dihitung dari hasil yang sudah difilter di atas) ---
$total_kendaraan  = count($data_laporan);
$total_motor      = 0;
$total_mobil      = 0;
$total_pendapatan = 0;

foreach ($data_laporan as $row) {
    $jenis_row = strtolower($row->jenis_kendaraan ?? '');
    if ($jenis_row === 'motor') {
        $total_motor++;
    } elseif ($jenis_row === 'mobil') {
        $total_mobil++;
    }
    $total_pendapatan += (int) ($row->biaya_total ?? 0);
}

include __DIR__ . '/../view/v_laporan.php';
exit();
