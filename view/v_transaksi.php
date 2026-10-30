<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($data_transaksi)) { $data_transaksi = []; }
if (!isset($data_kendaraan)) { $data_kendaraan = []; }
if (!isset($data_area))      { $data_area = []; }
if (!isset($data_tarif))     { $data_tarif = []; }
if (!isset($tgl_masuk_min))  { $tgl_masuk_min  = '2026-09-23'; }
if (!isset($tgl_keluar_min)) { $tgl_keluar_min = date('Y-m-d'); }

$pesan = $_SESSION['pesan'] ?? null;
unset($_SESSION['pesan']);

$role = $_SESSION['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Parkir - Parkir IKEA</title>
    <link rel="stylesheet" href="../assets/transaksi.css">
    <link rel="stylesheet" href="../assets/navbar.css">
</head>

<body>
<?php include __DIR__ . '/navbar.php'; ?>

<div class="container">

    <!-- HEADER -->
    <div class="header">
        <div>
            <h1>Data Parkir</h1>
            <p>Daftar kendaraan yang sedang parkir. Kendaraan yang sudah keluar ada di menu Riwayat.</p>
        </div>
        <div style="display:flex; gap:10px; align-items:center;">
            <?php if ($role === 'petugas' || $role === 'admin'): ?>
            <button onclick="document.getElementById('modalMasuk').style.display='flex'"
                    class="btn-kendaraan-masuk">
                🚗 + Kendaraan Masuk
            </button>
            <?php endif; ?>
            <a href="DashBoard.php" class="btn-kembali">← Kembali</a>
        </div>
    </div>

    <!-- ═══ MODAL: KENDARAAN MASUK ═══════════════════════════════════ -->
    <div id="modalMasuk" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45);
         z-index:9999; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:20px; padding:32px 28px; width:100%;
                    max-width:480px; box-shadow:0 12px 40px rgba(217,79,135,.2);
                    position:relative; margin:16px;">

            <!-- Tutup modal -->
            <button onclick="document.getElementById('modalMasuk').style.display='none'"
                    style="position:absolute; top:14px; right:18px; background:none;
                           border:none; font-size:20px; color:#bbb; cursor:pointer;">✕</button>

            <h2 style="color:#d94f87; font-size:20px; margin-bottom:6px;">🚗 Kendaraan Masuk</h2>
            <p style="color:#aaa; font-size:12px; margin-bottom:22px;">
                Waktu masuk dicatat otomatis sesuai waktu sekarang.
            </p>

            <form action="../controller/c_transaksi.php?aksi=tambah" method="POST">

                <!-- Waktu masuk otomatis (hidden) -->
                <input type="hidden" name="waktu_masuk" id="inputWaktuMasuk">
                <!-- Waktu keluar kosong (masih parkir) -->
                <input type="hidden" name="waktu_keluar" value="">
                <!-- Petugas otomatis dari session -->
                <input type="hidden" name="id_user" value="<?= $_SESSION['id_user'] ?? 1 ?>">

                <!-- KENDARAAN -->
                <div class="form-group" style="margin-bottom:14px;">
                    <label>Kendaraan (No. Polisi)</label>
                    <select name="id_kendaraan" required>
                        <option value="" disabled selected>Pilih Kendaraan</option>
                        <?php foreach ($data_kendaraan as $k): ?>
                        <option value="<?= $k->id_kendaraan ?>">
                            <?= htmlspecialchars($k->plat_nomor) ?> —
                            <?= htmlspecialchars(ucfirst($k->jenis_kendaraan)) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- AREA PARKIR -->
                <div class="form-group" style="margin-bottom:14px;">
                    <label>Area Parkir</label>
                    <select name="id_area" required>
                        <option value="" disabled selected>Pilih Area</option>
                        <?php foreach ($data_area as $a): ?>
                        <option value="<?= $a->id_area ?>">
                            <?= htmlspecialchars($a->nama_area) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tarif tidak dipilih manual: otomatis dari jenis kendaraan
                     (Mobil Rp5.000/jam, Motor Rp2.000/jam) di c_transaksi.php -->
                <div style="margin-bottom:22px;"></div>

                <div style="display:flex; gap:10px;">
                    <button type="submit" class="btn-tambah" style="flex:1;">
                        ✅ Catat Masuk
                    </button>
                    <button type="button"
                            onclick="document.getElementById('modalMasuk').style.display='none'"
                            class="btn-batal">
                        Batal
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script>
    // Isi waktu masuk otomatis saat modal dibuka
    function setWaktuMasuk() {
        const now = new Date();
        const pad = n => String(n).padStart(2, '0');
        const iso = now.getFullYear() + '-' + pad(now.getMonth()+1) + '-' + pad(now.getDate())
                  + 'T' + pad(now.getHours()) + ':' + pad(now.getMinutes());
        document.getElementById('inputWaktuMasuk').value = iso;
    }
    document.querySelector('[onclick*="modalMasuk"]')
        ?.addEventListener('click', setWaktuMasuk);
    // Tutup modal kalau klik di luar box
    document.getElementById('modalMasuk').addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
    </script>

    <?php if ($pesan): ?>
        <div class="alert alert-<?= $pesan['tipe'] === 'success' ? 'success' : 'error'; ?>">
            <strong><?= htmlspecialchars($pesan['judul']); ?></strong>
            <span><?= htmlspecialchars($pesan['teks']); ?></span>
        </div>
    <?php endif; ?>


    <!-- ═══════════════════════════════════════════════════════
         FORM TAMBAH — hanya tampil untuk ADMIN
         (Petugas tidak perlu isi manual, cukup pakai tombol
          Keluar Kendaraan dari tabel di bawah)
         ═══════════════════════════════════════════════════════ -->
    <?php if ($role === 'admin'): ?>
    <div class="card mb-card">
        <div class="card-header">
            <h2>Tambah Transaksi</h2>
        </div>

        <form action="../controller/c_transaksi.php?aksi=tambah" method="POST">
            <div class="form-grid">

                <!-- KENDARAAN -->
                <div class="form-group">
                    <label>Kendaraan</label>
                    <select name="id_kendaraan" required>
                        <option value="" selected disabled>Pilih Kendaraan</option>
                        <?php foreach ($data_kendaraan as $k): ?>
                            <option value="<?= $k->id_kendaraan ?>">
                                <?= htmlspecialchars($k->plat_nomor) ?> -
                                <?= htmlspecialchars($k->jenis_kendaraan) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- AREA PARKIR -->
                <div class="form-group">
                    <label>Area Parkir</label>
                    <select name="id_area" required>
                        <option value="" selected disabled>Pilih Area</option>
                        <?php foreach ($data_area as $a): ?>
                            <option value="<?= $a->id_area ?>">
                                <?= htmlspecialchars($a->nama_area) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- TARIF -->
                <div class="form-group">
                    <label>Tarif</label>
                    <select name="id_tarif" required>
                        <option value="" selected disabled>Pilih Tarif</option>
                        <?php foreach ($data_tarif as $t): ?>
                            <option value="<?= $t->id_tarif ?>">
                                <?= htmlspecialchars(ucfirst($t->jenis_kendaraan)) ?> —
                                Rp<?= number_format((float)$t->tarif_per_jam, 0, ',', '.') ?>/jam
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- WAKTU MASUK -->
                <div class="form-group">
                    <label>Waktu Masuk</label>
                    <input type="datetime-local" name="waktu_masuk"
                           min="<?= $tgl_masuk_min ?>T00:00" required>
                </div>

                <!-- WAKTU KELUAR (opsional) -->
                <div class="form-group">
                    <label>Waktu Keluar <span style="font-weight:400;color:#bbb;">(opsional)</span></label>
                    <input type="datetime-local" name="waktu_keluar"
                           min="<?= $tgl_keluar_min ?>T00:00">
                </div>

            </div>

            <p style="font-size:12px;color:#a9899a;margin:-8px 0 15px;">
                Waktu Masuk tidak boleh sebelum 23 September 2026, dan Waktu Keluar tidak boleh sebelum hari ini.
                Kosongkan "Waktu Keluar" jika kendaraan baru masuk.
                Durasi &amp; biaya dihitung otomatis saat waktu keluar diisi,
                dan transaksinya langsung pindah ke menu Riwayat.
            </p>

            <div class="form-actions">
                <button type="submit" class="btn-tambah">+ Tambah Transaksi</button>
                <a href="../controller/c_transaksi.php?aksi=tampil" class="btn-batal">Batal</a>
            </div>
        </form>
    </div>
    <?php endif; ?>


    <!-- ═══════════════════════════════════════════════════════
         TABEL DATA TRANSAKSI
         ═══════════════════════════════════════════════════════ -->
    <div class="card">
        <div class="card-header">
            <h2>Data Parkir</h2>
            <span class="total-badge">Total: <?= count($data_transaksi) ?></span>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>No. Polisi</th>
                        <th>Jenis</th>
                        <th>Area</th>
                        <th>Jam Masuk</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>

                <?php if (!empty($data_transaksi)): ?>
                    <?php $no = 1; ?>
                    <?php foreach ($data_transaksi as $row): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>TRX<?= str_pad($row->id_parkir, 3, '0', STR_PAD_LEFT) ?></td>
                        <td><span class="plat"><?= htmlspecialchars($row->plat_nomor ?? '-') ?></span></td>
                        <td><?= htmlspecialchars(ucfirst($row->jenis_kendaraan ?? '-')) ?></td>
                        <td><?= htmlspecialchars($row->nama_area ?? '-') ?></td>
                        <td><?= !empty($row->waktu_masuk) ? date('d/m/Y H:i', strtotime($row->waktu_masuk)) : '-' ?></td>
                        <td><span class="status masuk">Masuk</span></td>
                        <td>
                            <div class="aksi-group">

                            <?php if ($role === 'petugas'): ?>

                                <a href="../controller/c_transaksi.php?aksi=keluar_kendaraan&id=<?= $row->id_parkir ?>"
                                   class="btn-aksi btn-cetak" title="Proses Keluar">
                                    🚗 <span>Keluar Kendaraan</span>
                                </a>

                            <?php else: ?>

                                <a href="../controller/c_transaksi.php?aksi=edit&id=<?= $row->id_parkir ?>"
                                   class="btn-aksi btn-edit" title="Edit">
                                    ✏️ <span>Edit</span>
                                </a>

                                <a href="../controller/c_transaksi.php?aksi=hapus&id=<?= $row->id_parkir ?>"
                                   class="btn-aksi btn-hapus"
                                   onclick="return confirm('Yakin ingin menghapus transaksi ini?');"
                                   title="Hapus">
                                    🗑️ <span>Hapus</span>
                                </a>

                            <?php endif; ?>

                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="8" class="empty-state">Tidak ada kendaraan yang sedang parkir.</td>
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
