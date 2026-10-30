

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
 * Data disiapkan oleh controller:
 * $data_transaksi, $data_kendaraan, $data_area, $data_tarif, $data_user
 */

if (!isset($data_transaksi)) { $data_transaksi = []; }
if (!isset($data_kendaraan)) { $data_kendaraan = []; }
if (!isset($data_area))      { $data_area = []; }
if (!isset($data_tarif))     { $data_tarif = []; }
if (!isset($data_user))      { $data_user = []; }

// Ambil & bersihkan pesan notifikasi (sukses/gagal) dari session, kalau ada
$pesan = $_SESSION['pesan'] ?? null;
unset($_SESSION['pesan']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Transaksi Parkir - Parkir IKEA</title>

    <link rel="stylesheet" href="../assets/transaksi.css">
    <link rel="stylesheet" href="../assets/navbar.css">
</head>

<body>

<?php include __DIR__ . '/navbar.php'; ?>

<div class="container">

    <!-- HEADER -->
    <div class="header">

        <div>
            <h1>Transaksi Parkir</h1>
            <p>Kelola data transaksi kendaraan yang masuk dan keluar.</p>
        </div>

        <a href="DashBoard.php" class="btn-kembali">
            ← Kembali
        </a>

    </div>


    <?php if ($pesan): ?>
        <div class="alert alert-<?= $pesan['tipe'] === 'success' ? 'success' : 'error'; ?>">
            <strong><?= htmlspecialchars($pesan['judul']); ?></strong>
            <span><?= htmlspecialchars($pesan['teks']); ?></span>
        </div>
    <?php endif; ?>


    <!-- ================= FORM TAMBAH TRANSAKSI ================= -->

    <div class="card mb-card">

        <div class="card-header">
            <h2>Tambah Transaksi</h2>
        </div>

        <form action="../controller/c_transaksi.php?aksi=tambah"
              method="POST">

            <div class="form-grid">

                <!-- KENDARAAN -->
                <div class="form-group">
                    <label>Kendaraan</label>

                    <select name="id_kendaraan" required>

                        <option value="" selected disabled>
                            Pilih Kendaraan
                        </option>

                        <?php foreach ($data_kendaraan as $kendaraan): ?>

                            <option value="<?= $kendaraan->id_kendaraan; ?>">
                                <?= htmlspecialchars($kendaraan->plat_nomor); ?>
                                -
                                <?= htmlspecialchars($kendaraan->jenis_kendaraan); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>
                </div>


                <!-- AREA PARKIR -->
                <div class="form-group">
                    <label>Area Parkir</label>

                    <select name="id_area" required>

                        <option value="" selected disabled>
                            Pilih Area
                        </option>

                        <?php foreach ($data_area as $area): ?>

                            <option value="<?= $area->id_area; ?>">
                                <?= htmlspecialchars($area->nama_area); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>
                </div>


                <!-- TARIF -->
                <div class="form-group">
                    <label>Tarif</label>

                    <select name="id_tarif" required>

                        <option value="" selected disabled>
                            Pilih Tarif
                        </option>

                        <?php foreach ($data_tarif as $tarif): ?>

                            <option value="<?= $tarif->id_tarif; ?>">
                                <?= htmlspecialchars(ucfirst($tarif->jenis_kendaraan)); ?>
                                -
                                Rp<?= number_format((float) $tarif->tarif_per_jam, 0, ',', '.'); ?>/jam
                            </option>

                        <?php endforeach; ?>

                    </select>
                </div>


                <!-- PETUGAS -->
                <div class="form-group">
                    <label>Petugas</label>

                    <select name="id_user" required>

                        <option value="" selected disabled>
                            Pilih Petugas
                        </option>

                        <?php foreach ($data_user as $user): ?>

                            <option value="<?= $user->id_user; ?>">
                                <?= htmlspecialchars($user->nama_lengkap); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>
                </div>


                <!-- WAKTU MASUK -->
                <div class="form-group">
                    <label>Waktu Masuk</label>

                    <input
                        type="datetime-local"
                        name="waktu_masuk"
                        required
                    >
                </div>


                <!-- WAKTU KELUAR (opsional) -->
                <div class="form-group">
                    <label>Waktu Keluar (opsional)</label>

                    <input
                        type="datetime-local"
                        name="waktu_keluar"
                    >
                </div>

            </div>

            <p style="font-size:12px;color:#a9899a;margin:-8px 0 15px;">
                Kosongkan "Waktu Keluar" jika kendaraan baru masuk. Durasi &amp; biaya akan dihitung otomatis begitu waktu keluar diisi.
            </p>


            <!-- TOMBOL -->

            <div class="form-actions">

                <button type="submit" class="btn-tambah">
                    + Tambah Transaksi
                </button>

                <a href="../controller/c_transaksi.php?aksi=tampil"
                   class="btn-batal">
                    Batal
                </a>

            </div>

        </form>

    </div>


    <!-- ================= DATA TRANSAKSI ================= -->

    <div class="card">

        <div class="card-header">

            <h2>Data Transaksi</h2>

            <span class="total-badge">
                Total: <?= count($data_transaksi); ?>
            </span>

        </div>


        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>
                        <th>No</th>
                        <th>Kode Transaksi</th>
                        <th>No. Polisi</th>
                        <th>Jenis Kendaraan</th>
                        <th>Area Parkir</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Biaya</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>

                </thead>


                <tbody>

                <?php if (!empty($data_transaksi)): ?>

                    <?php $no = 1; ?>

                    <?php foreach ($data_transaksi as $row): ?>

                        <?php $status_lower = strtolower($row->status ?? ''); ?>

                        <tr>

                            <td>
                                <?= $no++; ?>
                            </td>

                            <td>
                                TRX<?= str_pad($row->id_parkir, 3, '0', STR_PAD_LEFT); ?>
                            </td>

                            <td>
                                <span class="plat">
                                    <?= htmlspecialchars($row->plat_nomor ?? '-'); ?>
                                </span>
                            </td>

                            <td>
                                <?= htmlspecialchars($row->jenis_kendaraan ?? '-'); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row->nama_area ?? '-'); ?>
                            </td>

                            <td>
                                <?= !empty($row->waktu_masuk)
                                    ? date('d/m/Y H:i', strtotime($row->waktu_masuk))
                                    : '-'; ?>
                            </td>

                            <td>
                                <?= !empty($row->waktu_keluar)
                                    ? date('d/m/Y H:i', strtotime($row->waktu_keluar))
                                    : '-'; ?>
                            </td>

                            <td>
                                <?= !empty($row->biaya_total)
                                    ? 'Rp' . number_format((float) $row->biaya_total, 0, ',', '.')
                                    : '-'; ?>
                            </td>

                            <td>

                                <?php if ($status_lower === 'masuk'): ?>

                                    <span class="status masuk">
                                        Masuk
                                    </span>

                                <?php else: ?>

                                    <span class="status keluar">
                                        Keluar
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <div class="aksi-group">

                                    <?php if ($status_lower === 'keluar'): ?>

                                        <a
                                            href="../controller/c_transaksi.php?aksi=cetak_struk&id=<?= $row->id_parkir; ?>"
                                            class="btn-aksi btn-cetak"
                                            target="_blank"
                                            title="Cetak Struk">
                                            🖨 <span>Cetak</span>
                                        </a>

                                    <?php endif; ?>

                                    <a
                                        href="../controller/c_transaksi.php?aksi=edit&id=<?= $row->id_parkir; ?>"
                                        class="btn-aksi btn-edit"
                                        title="Edit Transaksi">
                                        ✏️ <span>Edit</span>
                                    </a>

                                    <a
                                        href="../controller/c_transaksi.php?aksi=hapus&id=<?= $row->id_parkir; ?>"
                                        class="btn-aksi btn-hapus"
                                        onclick="return confirm('Yakin ingin menghapus transaksi ini?');"
                                        title="Hapus Transaksi">
                                        🗑️ <span>Hapus</span>
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="10" class="empty-state">
                            Belum ada data transaksi.
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script src="../assets/navbar.js"></script>

</body>
</html>
