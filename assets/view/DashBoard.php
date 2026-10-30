<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Halaman ini hanya boleh diakses setelah login
if (empty($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Hanya petugas & admin yang boleh pakai dashboard ini
if (!in_array($_SESSION['role'] ?? '', ['admin', 'petugas'], true)) {
    header("Location: DashBoard_Owner.php");
    exit;
}

// ── Koneksi & model ───────────────────────────────────────────────
require_once __DIR__ . '/../models/koneksi.php';
require_once __DIR__ . '/../models/m_transaksi.php';

$koneksi_obj  = new koneksi();
$db           = $koneksi_obj->koneksi;
$trx_model    = new M_Transaksi($db);

// ── Statistik ringkasan ───────────────────────────────────────────
// Total kendaraan terdaftar
$q_total_kendaraan = mysqli_query($db, "SELECT COUNT(*) AS n FROM tb_kendaraan");
$total_kendaraan   = mysqli_fetch_assoc($q_total_kendaraan)['n'] ?? 0;

// Slot tersedia (kapasitas − terisi di semua area)
$q_slot = mysqli_query($db, "SELECT SUM(kapasitas - terisi) AS sisa FROM tb_area_parkir");
$slot_tersedia = mysqli_fetch_assoc($q_slot)['sisa'] ?? 0;

// Transaksi hari ini (status Masuk / aktif sedang parkir)
$q_hari_ini = mysqli_query($db,
    "SELECT COUNT(*) AS n FROM transaksi_parkir
     WHERE DATE(waktu_masuk) = CURDATE() OR status = 'Masuk'");
$parkir_hari_ini = mysqli_fetch_assoc($q_hari_ini)['n'] ?? 0;

// Total pendapatan hari ini
$q_pendapatan = mysqli_query($db,
    "SELECT COALESCE(SUM(biaya_total),0) AS total FROM transaksi_parkir
     WHERE DATE(waktu_masuk) = CURDATE() AND status = 'keluar'");
$pendapatan_hari_ini = mysqli_fetch_assoc($q_pendapatan)['total'] ?? 0;

// ── Data area parkir (6 slot pertama) ────────────────────────────
$q_area = mysqli_query($db, "SELECT * FROM tb_area_parkir ORDER BY id_area ASC LIMIT 6");
$area_parkir = [];
while ($row = mysqli_fetch_assoc($q_area)) {
    $area_parkir[] = $row;
}

// ── Transaksi aktif (status Masuk) untuk tabel dashboard ─────────
$q_aktif = mysqli_query($db,
    "SELECT t.id_parkir, k.plat_nomor, k.jenis_kendaraan,
            t.waktu_masuk, t.status
     FROM transaksi_parkir t
     LEFT JOIN tb_kendaraan k ON k.id_kendaraan = t.id_kendaraan
     WHERE t.status = 'Masuk'
     ORDER BY t.waktu_masuk DESC
     LIMIT 10");
$transaksi_aktif = [];
while ($row = mysqli_fetch_assoc($q_aktif)) {
    $transaksi_aktif[] = $row;
}

// ── Riwayat terbaru (status keluar) ──────────────────────────────
$q_riwayat = mysqli_query($db,
    "SELECT t.id_parkir, k.plat_nomor, k.jenis_kendaraan,
            a.nama_area,
            t.waktu_masuk, t.waktu_keluar,
            t.durasi_jam, t.biaya_total
     FROM transaksi_parkir t
     LEFT JOIN tb_kendaraan   k ON k.id_kendaraan = t.id_kendaraan
     LEFT JOIN tb_area_parkir a ON a.id_area = t.id_area
     WHERE t.status = 'keluar'
     ORDER BY t.waktu_keluar DESC
     LIMIT 5");
$riwayat_terbaru = [];
while ($row = mysqli_fetch_assoc($q_riwayat)) {
    $riwayat_terbaru[] = $row;
}

// ── Helper format rupiah ──────────────────────────────────────────
function rupiah($angka) {
    return 'Rp' . number_format($angka, 0, ',', '.');
}
function rupiah_singkat($angka) {
    if ($angka >= 1000000) return 'Rp' . round($angka/1000000, 1) . 'Jt';
    if ($angka >= 1000)    return 'Rp' . round($angka/1000, 0) . 'K';
    return rupiah($angka);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ParkirKu</title>
    <link rel="stylesheet" href="../assets/DashBoard.css">
    <link rel="stylesheet" href="../assets/navbar.css">
</head>

<body>
<div class="wrapper">

    <?php include __DIR__ . '/navbar.php'; ?>

    <main class="main" style="overflow-y:auto; height:100vh; padding-bottom:40px;">

        <!-- ══════════ HEADER ══════════ -->
        <header class="header">
            <div>
                <h1>Dashboard 🌷</h1>
                <p>Selamat datang kembali, <?= htmlspecialchars($_SESSION['nama'] ?? 'Pengguna') ?>!</p>
            </div>
            <div class="profile">
                <div class="avatar">
                    <?= htmlspecialchars(strtoupper(substr($_SESSION['nama'] ?? 'U', 0, 1))) ?>
                </div>
                <div>
                    <strong><?= htmlspecialchars($_SESSION['nama'] ?? 'Pengguna') ?></strong>
                    <small><?= htmlspecialchars(ucfirst($_SESSION['role'] ?? '')) ?></small>
                </div>
            </div>
        </header>


        <!-- ══════════ STATISTIK ══════════ -->
        <section class="stats">
            <div class="card pink">
                <div class="card-icon">🚗</div>
                <div>
                    <span>Total Kendaraan</span>
                    <h2><?= $total_kendaraan ?></h2>
                </div>
            </div>

            <div class="card purple">
                <div class="card-icon">🅿️</div>
                <div>
                    <span>Slot Tersedia</span>
                    <h2><?= $slot_tersedia ?></h2>
                </div>
            </div>

            <div class="card green">
                <div class="card-icon">🎫</div>
                <div>
                    <span>Parkir Hari Ini</span>
                    <h2><?= $parkir_hari_ini ?></h2>
                </div>
            </div>

            <div class="card yellow">
                <div class="card-icon">💰</div>
                <div>
                    <span>Pendapatan Hari Ini</span>
                    <h2><?= rupiah_singkat($pendapatan_hari_ini) ?></h2>
                </div>
            </div>
        </section>


        <!-- ══════════ STATUS AREA PARKIR ══════════ -->
        <section class="content-card">
            <div class="section-title">
                <div>
                    <h2>Status Area Parkir</h2>
                    <p>Kondisi slot parkir saat ini</p>
                </div>
                <a href="../view/v_area_parkir.php" class="small-button">Lihat Semua</a>
            </div>

            <div class="parking-grid">
                <?php if (!empty($area_parkir)): ?>
                    <?php foreach ($area_parkir as $i => $area):
                        $sisa   = (int)$area['kapasitas'] - (int)$area['terisi'];
                        $penuh  = $sisa <= 0;
                        $kelas  = $penuh ? 'occupied' : 'available';
                        $ikon   = $penuh ? '🚙' : '🚗';
                        $label  = $penuh ? 'Penuh' : 'Tersedia';
                        $kode   = 'P-' . str_pad($area['id_area'], 2, '0', STR_PAD_LEFT);
                    ?>
                    <a href="../view/v_area_parkir.php?id=<?= $area['id_area'] ?>"
                       class="parking-slot <?= $kelas ?>" style="text-decoration:none;">
                        <div class="slot-number"><?= htmlspecialchars($kode) ?></div>
                        <div class="slot-icon"><?= $ikon ?></div>
                        <div style="font-size:10px; color:#888; margin-bottom:4px;">
                            <?= htmlspecialchars($area['nama_area']) ?>
                        </div>
                        <span><?= $label ?> (<?= $sisa ?>/<?= $area['kapasitas'] ?>)</span>
                    </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color:#aaa; font-size:13px; grid-column:1/-1;">
                        Belum ada data area parkir.
                    </p>
                <?php endif; ?>
            </div>
        </section>


        <!-- ══════════ DATA PARKIR AKTIF ══════════ -->
        <section class="content-card">
            <div class="section-title">
                <div>
                    <h2>🚘 Data Parkir Aktif</h2>
                    <p>Kendaraan yang sedang parkir saat ini</p>
                </div>
                <a href="../controller/c_transaksi.php?aksi=tampil" class="small-button">
                    Lihat Semua
                </a>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Polisi</th>
                            <th>Jenis</th>
                            <th>Jam Masuk</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transaksi_aktif)): ?>
                            <?php foreach ($transaksi_aktif as $no => $trx): ?>
                            <tr>
                                <td><?= $no + 1 ?></td>
                                <td><strong><?= htmlspecialchars($trx['plat_nomor']) ?></strong></td>
                                <td><?= htmlspecialchars(ucfirst($trx['jenis_kendaraan'])) ?></td>
                                <td><?= date('H:i', strtotime($trx['waktu_masuk'])) ?></td>
                                <td>
                                    <span class="status parkir">Parkir</span>
                                </td>
                                <td>
                                    <a href="../controller/c_transaksi.php?aksi=edit&id=<?= $trx['id_parkir'] ?>"
                                       style="background:#ffe1ea; color:#e87598; padding:5px 10px;
                                              border-radius:8px; font-size:11px; text-decoration:none;">
                                        ✏️ Proses Keluar
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center; color:#aaa; padding:20px;">
                                    Tidak ada kendaraan yang sedang parkir saat ini.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>


        <!-- ══════════ RIWAYAT TERBARU ══════════ -->
        <section class="content-card">
            <div class="section-title">
                <div>
                    <h2>📋 Riwayat Parkir Terbaru</h2>
                    <p>Kendaraan yang baru saja selesai parkir</p>
                </div>
                <a href="../view/v_riwayat.php" class="small-button">Lihat Semua</a>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Kendaraan</th>
                            <th>Jenis</th>
                            <th>Area</th>
                            <th>Waktu Masuk</th>
                            <th>Waktu Keluar</th>
                            <th>Durasi</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($riwayat_terbaru)): ?>
                            <?php foreach ($riwayat_terbaru as $no => $r): ?>
                            <tr>
                                <td><?= $no + 1 ?></td>
                                <td><strong><?= htmlspecialchars($r['plat_nomor']) ?></strong></td>
                                <td><?= htmlspecialchars(ucfirst($r['jenis_kendaraan'])) ?></td>
                                <td><?= htmlspecialchars($r['nama_area'] ?? '-') ?></td>
                                <td><?= date('H:i', strtotime($r['waktu_masuk'])) ?></td>
                                <td><?= $r['waktu_keluar'] ? date('H:i', strtotime($r['waktu_keluar'])) : '-' ?></td>
                                <td><?= $r['durasi_jam'] ? $r['durasi_jam'] . ' Jam' : '-' ?></td>
                                <td>
                                    <span style="background:#fce4ec; color:#c83f76; padding:5px 9px;
                                                 border-radius:8px; font-weight:bold; font-size:11px;">
                                        <?= rupiah($r['biaya_total']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status selesai">Selesai</span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" style="text-align:center; color:#aaa; padding:20px;">
                                    Belum ada riwayat parkir.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

</div>
<script src="../assets/navbar.js"></script>
</body>
</html>
