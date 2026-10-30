<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
 * Data disiapkan oleh controller:
 * $transaksi, $data_kendaraan, $data_area, $data_tarif, $data_user
 */

if (!isset($data_kendaraan)) { $data_kendaraan = []; }
if (!isset($data_area))      { $data_area = []; }
if (!isset($data_tarif))     { $data_tarif = []; }
if (!isset($data_user))      { $data_user = []; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Transaksi</title>

    <link rel="stylesheet" href="../assets/edit_transaksi.css">
    <link rel="stylesheet" href="../assets/navbar.css">
</head>

<body>

    <?php include __DIR__ . '/navbar.php'; ?>

<div class="container">
    <div class="form-card">

        <div class="header">
            <h1>Edit Transaksi</h1>

            <a href="../controller/c_transaksi.php?aksi=tampil" class="btn-kembali">
                Kembali
            </a>
        </div>

        <?php if (isset($transaksi) && $transaksi): ?>

        <form action="../controller/c_transaksi.php?aksi=update&id=<?= $transaksi->id_parkir; ?>" method="post">

            <div class="form-group">
                <label>Kendaraan</label>
                <select name="id_kendaraan" required>
                    <option value="" disabled>Pilih Kendaraan</option>
                    <?php foreach ($data_kendaraan as $kendaraan): ?>
                        <option value="<?= $kendaraan->id_kendaraan; ?>"
                            <?= ((int) $transaksi->id_kendaraan === (int) $kendaraan->id_kendaraan) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($kendaraan->plat_nomor); ?> - <?= htmlspecialchars($kendaraan->jenis_kendaraan); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Area Parkir</label>
                <select name="id_area" required>
                    <option value="" disabled>Pilih Area</option>
                    <?php foreach ($data_area as $area): ?>
                        <option value="<?= $area->id_area; ?>"
                            <?= ((int) $transaksi->id_area === (int) $area->id_area) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($area->nama_area); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Tarif</label>
                <select name="id_tarif" required>
                    <option value="" disabled>Pilih Tarif</option>
                    <?php foreach ($data_tarif as $tarif): ?>
                        <option value="<?= $tarif->id_tarif; ?>"
                            <?= ((int) $transaksi->id_tarif === (int) $tarif->id_tarif) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars(ucfirst($tarif->jenis_kendaraan)); ?> - Rp<?= number_format((float) $tarif->tarif_per_jam, 0, ',', '.'); ?>/jam
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Petugas</label>
                <select name="id_user" required>
                    <option value="" disabled>Pilih Petugas</option>
                    <?php foreach ($data_user as $user): ?>
                        <option value="<?= $user->id_user; ?>"
                            <?= ((int) $transaksi->id_user === (int) $user->id_user) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($user->nama_lengkap); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Waktu Masuk</label>
                <input type="datetime-local"
                       name="waktu_masuk"
                       value="<?= date('Y-m-d\TH:i', strtotime($transaksi->waktu_masuk)); ?>"
                       required>
            </div>

            <div class="form-group">
                <label>Waktu Keluar (opsional)</label>
                <input type="datetime-local"
                       name="waktu_keluar"
                       value="<?= !empty($transaksi->waktu_keluar) ? date('Y-m-d\TH:i', strtotime($transaksi->waktu_keluar)) : ''; ?>">
                <small style="color:#a9899a;">
                    Kosongkan jika kendaraan belum keluar. Durasi &amp; biaya dihitung otomatis dari selisih waktu masuk-keluar dan tarif yang dipilih.
                </small>
            </div>

            <div class="button-group">
                <a href="../controller/c_transaksi.php?aksi=tampil" class="btn-batal">
                    Batal
                </a>

                <button type="submit" class="btn-simpan">
                    Simpan Perubahan
                </button>
            </div>

        </form>

        <?php else: ?>

            <p style="color: red; text-align: center;">
                Data transaksi tidak ditemukan.
            </p>

        <?php endif; ?>

    </div>
</div>

    <script src="../assets/navbar.js"></script>

</body>
</html>
