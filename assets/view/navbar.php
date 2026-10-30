<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$role_sekarang = $_SESSION['role'] ?? '';
$nama_sekarang = $_SESSION['nama'] ?? '';

// Link "Dashboard" beda tujuan tergantung role:
// owner -> dashboard khusus owner, admin/petugas -> dashboard utama
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

            <!-- Owner hanya boleh melihat Riwayat & Rekap Riwayat -->
            <a href="../view/v_riwayat.php" class="parkir-menu-link"><span>📋</span>Riwayat</a>
            <a href="../view/v_laporan.php" class="parkir-menu-link"><span>📊</span>Rekap Riwayat</a>

        <?php else: ?>

            <a href="../controller/c_kendaraan.php?aksi=tampil" class="parkir-menu-link"><span>🚗</span>Kendaraan</a>
            <a href="../view/v_area_parkir.php" class="parkir-menu-link"><span>🅿️</span>Area Parkir</a>
            <a href="../controller/c_transaksi.php?aksi=tampil" class="parkir-menu-link"><span>🎫</span>Transaksi</a>
            <a href="../view/v_riwayat.php" class="parkir-menu-link"><span>📋</span>Riwayat</a>
            <a href="../view/v_laporan.php" class="parkir-menu-link"><span>📊</span>Laporan</a>
            <a href="../controller/c_tarif.php?aksi=tampil" class="parkir-menu-link"><span>💵</span>Tarif</a>

            <?php if ($role_sekarang === 'admin'): ?>
            <a href="../view/v_user.php" class="parkir-menu-link"><span>👤</span>User</a>
            <?php endif; ?>

        <?php endif; ?>
    </nav>

    <a href="../controller/c_logout.php" class="parkir-logout">🚪 Logout</a>
</aside>
