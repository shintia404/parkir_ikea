<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$role_sekarang = $_SESSION['role'] ?? '';
$nama_sekarang = $_SESSION['nama'] ?? '';

$link_dashboard = ($role_sekarang === 'owner')
    ? '../view/DashBoard_Owner.php'
    : '../view/DashBoard.php';
?>
<aside class="parkir-sidebar" id="parkirSidebar">
    <div class="parkir-brand">
        <div class="parkir-brand-icon">🚗</div>
        <div>
            <h2>Parkir Ikea</h2>
            <span>Parking System</span>
        </div>
    </div>

    <?php if ($nama_sekarang): ?>
    <div class="parkir-user-info">
        <strong><?= htmlspecialchars($nama_sekarang) ?></strong>
        <span class="parkir-role-badge">Masuk sebagai <?= htmlspecialchars(ucfirst($role_sekarang)) ?></span>
    </div>
    <?php endif; ?>

    <nav class="parkir-nav" aria-label="Navigasi utama">
        <a href="<?= $link_dashboard ?>" class="parkir-menu-link"><span>🏠</span>Dashboard</a>

        <?php if ($role_sekarang === 'owner'): ?>

            <!-- Owner: Riwayat & Rekap saja -->
            <a href="../controller/c_riwayat.php" class="parkir-menu-link"><span>📋</span>Riwayat</a>
            <a href="../controller/c_laporan.php" class="parkir-menu-link"><span>📊</span>Rekap Riwayat</a>

        <?php elseif ($role_sekarang === 'petugas'): ?>

            <!-- Petugas: Data Parkir & Riwayat saja -->
            <a href="../controller/c_transaksi.php?aksi=tampil" class="parkir-menu-link"><span>🎫</span>Data Parkir</a>
            <a href="../controller/c_riwayat.php" class="parkir-menu-link"><span>📋</span>Riwayat</a>

        <?php else: ?>

            <!-- Admin: semua menu -->
            <a href="../controller/c_kendaraan.php?aksi=tampil" class="parkir-menu-link"><span>🚗</span>Kendaraan</a>
            <a href="../controller/c_area_parkir.php?aksi=tampil" class="parkir-menu-link"><span>🅿️</span>Area Parkir</a>
            <a href="../controller/c_transaksi.php?aksi=tampil" class="parkir-menu-link"><span>🎫</span>Transaksi</a>
            <a href="../controller/c_riwayat.php" class="parkir-menu-link"><span>📋</span>Riwayat</a>
            <a href="../controller/c_laporan.php" class="parkir-menu-link"><span>📊</span>Laporan</a>
            <a href="../controller/c_tarif.php?aksi=tampil" class="parkir-menu-link"><span>💵</span>Tarif</a>
            <a href="../view/v_user.php" class="parkir-menu-link"><span>👤</span>User</a>
            <a href="../controller/c_log.php" class="parkir-menu-link"><span>🕒</span>Log Aktivitas</a>

        <?php endif; ?>
    </nav>

    <a href="../controller/c_logout.php" class="parkir-logout">🚪 Logout</a>
</aside>
