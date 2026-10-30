<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Harus sudah login
if (empty($_SESSION['login'])) {
    header("Location: ../view/login.php");
    exit;
}

// Owner ke dashboardnya sendiri
if (!in_array($_SESSION['role'] ?? '', ['admin', 'petugas'], true)) {
    header("Location: ../view/DashBoard_Owner.php");
    exit;
}

require_once __DIR__ . '/../models/koneksi.php';
require_once __DIR__ . '/../models/m_log.php';
$koneksi_obj = new koneksi();
$db          = $koneksi_obj->koneksi;

$role = $_SESSION['role'] ?? '';

// ── Helper ────────────────────────────────────────
function fmt_rupiah(int $n): string {
    return 'Rp' . number_format($n, 0, ',', '.');
}
function safe(mixed $v, string $fb = '-'): string {
    return htmlspecialchars($v ?? $fb, ENT_QUOTES, 'UTF-8');
}

// ══════════════════════════════════════════════════
//  QUERY UNTUK PETUGAS: data parkir aktif + riwayat
// ══════════════════════════════════════════════════

// — Data Parkir Aktif —
$q_aktif = mysqli_query($db,
    "SELECT t.id_parkir, t.waktu_masuk,
            COALESCE(k.plat_nomor,      '(tidak diketahui)') AS plat_nomor,
            COALESCE(k.jenis_kendaraan, '-')                  AS jenis_kendaraan,
            COALESCE(a.nama_area,       '-')                  AS nama_area
     FROM   transaksi_parkir t
     LEFT JOIN tb_kendaraan   k ON k.id_kendaraan = t.id_kendaraan
     LEFT JOIN tb_area_parkir a ON a.id_area      = t.id_area
     WHERE  t.status = 'Masuk'
     ORDER  BY t.waktu_masuk DESC
     LIMIT  20");
$aktif = [];
while ($r = mysqli_fetch_assoc($q_aktif)) $aktif[] = $r;

// — Riwayat Parkir Terbaru —
$q_riwayat = mysqli_query($db,
    "SELECT t.id_parkir, t.waktu_masuk, t.waktu_keluar,
            t.durasi_jam, t.biaya_total,
            COALESCE(k.plat_nomor,      '(tidak diketahui)') AS plat_nomor,
            COALESCE(k.jenis_kendaraan, '-')                  AS jenis_kendaraan,
            COALESCE(a.nama_area,       '-')                  AS nama_area
     FROM   transaksi_parkir t
     LEFT JOIN tb_kendaraan   k ON k.id_kendaraan = t.id_kendaraan
     LEFT JOIN tb_area_parkir a ON a.id_area      = t.id_area
     WHERE  t.status = 'keluar'
     ORDER  BY t.waktu_keluar DESC
     LIMIT  10");
$riwayat = [];
while ($r = mysqli_fetch_assoc($q_riwayat)) $riwayat[] = $r;

// ══════════════════════════════════════════════════
//  QUERY TAMBAHAN UNTUK ADMIN: statistik + area
// ══════════════════════════════════════════════════
if ($role === 'admin') {
    $total_kendaraan = (int)(mysqli_fetch_assoc(mysqli_query($db,
        "SELECT COUNT(*) n FROM tb_kendaraan"))['n'] ?? 0);
    $slot_tersedia   = (int)(mysqli_fetch_assoc(mysqli_query($db,
        "SELECT COALESCE(SUM(kapasitas - terisi), 0) s FROM tb_area_parkir"))['s'] ?? 0);
    $parkir_aktif_n  = count($aktif);
    $pendapatan_hari = (int)(mysqli_fetch_assoc(mysqli_query($db,
        "SELECT COALESCE(SUM(biaya_total),0) t FROM transaksi_parkir
         WHERE DATE(waktu_keluar) = CURDATE() AND status = 'keluar'"))['t'] ?? 0);

    // — Log Aktivitas terbaru (khusus admin) —
    $log_aktivitas = ambil_log_terbaru($db, 10);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Parkir Ikea</title>
    <link rel="stylesheet" href="../assets/DashBoard.css">
    <link rel="stylesheet" href="../assets/navbar.css">
    <style>
        .main { overflow-y: auto; height: 100vh; padding-bottom: 48px; }
        .plat {
            display: inline-block;
            background: #fff0f6; color: #d94f87;
            padding: 4px 10px; border-radius: 8px;
            font-weight: 700; font-size: 12px;
        }
        .harga {
            display: inline-block;
            background: #fce4ec; color: #c83f76;
            padding: 4px 10px; border-radius: 8px;
            font-weight: 700; font-size: 12px;
        }
        .btn-aksi {
            display: inline-block;
            background: #ffe1ea; color: #e87598;
            padding: 5px 10px; border-radius: 8px;
            font-size: 11px; text-decoration: none;
            white-space: nowrap;
        }
        .btn-aksi:hover { background: #ffc9d9; }
        .badge-role {
            display: inline-block;
            padding: 3px 9px; border-radius: 8px;
            font-size: 11px; font-weight: 700;
            background: #f3e8ff; color: #8e5bb5;
        }
        .badge-role.admin   { background: #ffe1ea; color: #d94f87; }
        .badge-role.petugas { background: #e3f6ec; color: #2e9b63; }
        .badge-role.owner   { background: #fff4d6; color: #b8860b; }
    </style>
</head>

<body>
<div class="wrapper">

    <?php include __DIR__ . '/navbar.php'; ?>

    <main class="main">

        <!-- ══ HEADER ══ -->
        <header class="header">
            <div>
                <h1>Dashboard 🌷</h1>
                <p>Selamat datang, <strong><?= safe($_SESSION['nama'] ?? 'Pengguna') ?></strong>!</p>
            </div>
            <div class="profile">
                <div class="avatar">
                    <?= strtoupper(substr($_SESSION['nama'] ?? 'P', 0, 1)) ?>
                </div>
                <div>
                    <strong><?= safe($_SESSION['nama'] ?? 'Pengguna') ?></strong>
                    <small><?= ucfirst($role) ?></small>
                </div>
            </div>
        </header>


        <?php if ($role === 'admin'): ?>
        <!-- ══ STATISTIK (hanya admin) ══════════════════════ -->
        <section class="stats">
            <div class="card pink">
                <div class="card-icon">🚗</div>
                <div><span>Total Kendaraan</span><h2><?= $total_kendaraan ?></h2></div>
            </div>
            <div class="card purple">
                <div class="card-icon">🅿️</div>
                <div><span>Slot Tersedia</span><h2><?= $slot_tersedia ?></h2></div>
            </div>
            <div class="card green">
                <div class="card-icon">🚘</div>
                <div><span>Sedang Parkir</span><h2><?= $parkir_aktif_n ?></h2></div>
            </div>
            <div class="card yellow">
                <div class="card-icon">💰</div>
                <div>
                    <span>Pendapatan Hari Ini</span>
                    <h2>Rp<?= number_format($pendapatan_hari, 0, ',', '.') ?></h2>
                </div>
            </div>
        </section>
        <?php endif; ?>


        <!-- ══ DATA PARKIR AKTIF (semua role) ══════════════ -->
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
                            <th>Area</th>
                            <th>Jam Masuk</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($aktif): ?>
                        <?php foreach ($aktif as $i => $t): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><span class="plat"><?= safe($t['plat_nomor']) ?></span></td>
                            <td><?= safe(ucfirst($t['jenis_kendaraan'])) ?></td>
                            <td><?= safe($t['nama_area']) ?></td>
                            <td><?= date('H:i', strtotime($t['waktu_masuk'])) ?></td>
                            <td><span class="status parkir">Parkir</span></td>
                            <td>
                                <a class="btn-aksi"
                                   href="../controller/c_transaksi.php?aksi=keluar_kendaraan&id=<?= $t['id_parkir'] ?>">
                                    🚗 Keluar Kendaraan
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center;color:#aaa;padding:24px;">
                                Tidak ada kendaraan yang sedang parkir.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>


        <!-- ══ RIWAYAT PARKIR TERBARU (semua role) ═════════ -->
        <section class="content-card">
            <div class="section-title">
                <div>
                    <h2>📋 Riwayat Parkir Terbaru</h2>
                    <p>Kendaraan yang baru saja selesai parkir</p>
                </div>
                <a href="../controller/c_riwayat.php" class="small-button">Lihat Semua</a>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Kendaraan</th>
                            <th>Jenis</th>
                            <th>Area</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Durasi</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($riwayat): ?>
                        <?php foreach ($riwayat as $i => $r): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><span class="plat"><?= safe($r['plat_nomor']) ?></span></td>
                            <td><?= safe(ucfirst($r['jenis_kendaraan'])) ?></td>
                            <td><?= safe($r['nama_area']) ?></td>
                            <td><?= date('H:i', strtotime($r['waktu_masuk'])) ?></td>
                            <td><?= $r['waktu_keluar'] ? date('H:i', strtotime($r['waktu_keluar'])) : '-' ?></td>
                            <td><?= $r['durasi_jam'] ? $r['durasi_jam'] . ' Jam' : '-' ?></td>
                            <td><span class="harga"><?= fmt_rupiah((int)$r['biaya_total']) ?></span></td>
                            <td><span class="status selesai">Selesai</span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align:center;color:#aaa;padding:24px;">
                                Belum ada riwayat parkir.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <?php if ($role === 'admin'): ?>
        <!-- ══ LOG AKTIVITAS (hanya admin) ═════════════════ -->
        <section class="content-card">
            <div class="section-title">
                <div>
                    <h2>🕒 Log Aktivitas</h2>
                    <p>10 aktivitas terakhir pengguna di sistem</p>
                </div>
                <a href="../controller/c_log.php" class="small-button">Lihat Semua</a>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Role</th>
                            <th>Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($log_aktivitas)): ?>
                        <?php foreach ($log_aktivitas as $i => $l): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($l['waktu_aktivitas'])) ?></td>
                            <td><?= safe($l['nama_lengkap']) ?></td>
                            <td><span class="badge-role <?= safe($l['role']) ?>"><?= safe(ucfirst($l['role'])) ?></span></td>
                            <td><?= safe($l['aktivitas']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center;color:#aaa;padding:24px;">
                                Belum ada aktivitas tercatat.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php endif; ?>

    </main>
</div>

<script src="../assets/navbar.js"></script>
</body>
</html>
