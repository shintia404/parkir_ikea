<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Halaman ini dibuka lewat controller supaya datanya diambil dari database.
if (!isset($data_log)) {
    header('Location: ../controller/c_log.php');
    exit();
}

function h($v, string $fb = '-'): string {
    return htmlspecialchars($v ?? $fb, ENT_QUOTES, 'UTF-8');
}

// Membuat URL paginasi dengan mempertahankan kata kunci pencarian
function url_hal(int $hal, string $cari): string {
    $q = ['halaman' => $hal];
    if ($cari !== '') $q['cari'] = $cari;
    return '../controller/c_log.php?' . http_build_query($q);
}

$no_awal = ($halaman - 1) * $per_halaman + 1;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktivitas — Parkir Ikea</title>

    <link rel="stylesheet" href="../assets/log_aktivitas.css">
    <link rel="stylesheet" href="../assets/navbar.css">
</head>
<body>

    <?php include __DIR__ . '/navbar.php'; ?>

    <div class="container">

        <!-- HEADER -->
        <div class="header">
            <div>
                <h1>🕒 Log Aktivitas</h1>
                <p>Seluruh aktivitas pengguna di sistem (<?= $total_data ?> data)</p>
            </div>
            <a href="../view/DashBoard.php" class="btn-kembali">← Kembali</a>
        </div>

        <!-- CARD -->
        <div class="card">

            <div class="card-header">
                <h2>📜 Data Log</h2>

                <form class="search-box" method="get" action="../controller/c_log.php">
                    <input type="text" name="cari" value="<?= h($cari, '') ?>"
                           placeholder="Cari pengguna / aktivitas...">
                    <button type="submit">🔍</button>
                </form>
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
                    <?php if (!empty($data_log)): ?>
                        <?php foreach ($data_log as $i => $l): ?>
                        <tr>
                            <td><?= $no_awal + $i ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($l['waktu_aktivitas'])) ?></td>
                            <td><?= h($l['nama_lengkap']) ?></td>
                            <td><span class="badge-role <?= h($l['role']) ?>"><?= h(ucfirst($l['role'])) ?></span></td>
                            <td class="kolom-aktivitas"><?= h($l['aktivitas']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty-state">
                                <?= $cari !== '' ? 'Tidak ada log yang cocok dengan pencarian.' : 'Belum ada aktivitas tercatat.' ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_hal > 1): ?>
            <div class="pagination">
                <?php if ($halaman > 1): ?>
                    <a href="<?= url_hal($halaman - 1, $cari) ?>">‹ Sebelumnya</a>
                <?php endif; ?>

                <span class="info-hal">Halaman <?= $halaman ?> dari <?= $total_hal ?></span>

                <?php if ($halaman < $total_hal): ?>
                    <a href="<?= url_hal($halaman + 1, $cari) ?>">Berikutnya ›</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <script src="../assets/navbar.js"></script>
</body>
</html>
