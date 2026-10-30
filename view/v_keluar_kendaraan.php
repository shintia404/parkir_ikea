<?php
/*
 * HALAMAN PROSES KELUAR KENDARAAN (khusus petugas)
 * Data: $transaksi (object), $data_tarif (array)
 * Disiapkan oleh c_transaksi.php?aksi=keluar_kendaraan
 */
if (session_status() === PHP_SESSION_NONE) session_start();
date_default_timezone_set('Asia/Jakarta'); // waktu Indonesia (WIB)
if (!isset($transaksi)) {
    header('Location: ../controller/c_transaksi.php?aksi=tampil');
    exit();
}

$pesan = $_SESSION['pesan'] ?? null;
unset($_SESSION['pesan']);

$no_trx     = 'TRX' . str_pad($transaksi->id_parkir, 3, '0', STR_PAD_LEFT);
$plat       = htmlspecialchars($transaksi->plat_nomor      ?? '-');
$jenis      = htmlspecialchars(ucfirst($transaksi->jenis_kendaraan ?? '-'));
$area       = htmlspecialchars($transaksi->nama_area       ?? '-');
$petugas    = htmlspecialchars($transaksi->nama_petugas    ?? '-');
$tgl_masuk  = !empty($transaksi->waktu_masuk) ? date('d/m/Y H:i', strtotime($transaksi->waktu_masuk)) : '-';
$sudah_keluar = strtolower($transaksi->status ?? '') === 'keluar';

// Default value untuk input "Waktu Keluar": biasanya "waktu sekarang", tapi
// kalau kendaraan baru SAJA dicatat masuk (masih di menit yang sama), waktu
// sekarang bisa kepotong ke detik :00 sehingga malah <= waktu masuk (yang
// presisinya sampai detik) — akibatnya hitungEstimasi() di JS gagal syarat
// "keluar > masuk" dan preview struk (durasi/tarif/total) tampil "—" semua.
// Pastikan default-nya selalu minimal 1 menit setelah waktu masuk.
$ts_masuk_raw      = !empty($transaksi->waktu_masuk) ? strtotime($transaksi->waktu_masuk) : time();
$default_keluar_ts = max(time(), $ts_masuk_raw + 60);
$default_keluar    = date('Y-m-d\TH:i', $default_keluar_ts);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keluar Kendaraan — Parkir Ikea</title>
    <link rel="stylesheet" href="../assets/navbar.css">
    <link rel="stylesheet" href="../assets/struk_parkir.css">
    <style>
        /* ── Layout ─────────────────────────────────── */
        /* struk_parkir.css (di-load setelah navbar.css) punya body{padding:30px}
           yang menimpa body{padding-left:280px} dari navbar.css, sehingga konten
           halaman ini kepotong/ketiban sidebar. Kembalikan jaraknya di sini. */
        body {
            background: #fcecf2;
            font-family: Arial, sans-serif;
            padding: 0 0 0 280px;
        }
        .wrapper { display: flex; }
        .main-content {
            flex: 1;
            padding: 32px 40px;
            min-height: 100vh;
        }

        /* ── Breadcrumb / Header ─────────────────────── */
        .page-header { margin-bottom: 28px; }
        .page-header h1 { font-size: 24px; color: #333; }
        .page-header p  { color: #888; font-size: 13px; margin-top: 4px; }
        .breadcrumb { font-size: 12px; color: #aaa; margin-bottom: 8px; }
        .breadcrumb a { color: #e87598; text-decoration: none; }

        /* ── Grid dua kolom ─────────────────────────── */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 28px;
            align-items: start;
        }
        /* Breakpoint dihitung dari lebar konten yang TERSISA setelah
           sidebar tetap (280px, lihat navbar.css) + padding halaman
           ini (40px*2), bukan dari lebar layar penuh. Sebelumnya
           breakpoint-nya 900px dari lebar layar total, jadi di layar
           ~900–1100px kontennya kepotong/overflow karena sidebar
           belum diperhitungkan. */
        @media (max-width: 1100px) {
            .grid-2 { grid-template-columns: 1fr; }
        }

        /* Di layar sempit (HP), sidebar 230px (lihat navbar.css) makin
           memakan porsi lebar layar — kecilkan padding & pecah
           info-grid jadi 1 kolom supaya tidak sesak/terpotong. */
        @media (max-width: 700px) {
            .main-content { padding: 20px 16px; }
            .info-grid    { grid-template-columns: 1fr; }
        }

        /* ── Card form ──────────────────────────────── */
        .card-form {
            background: #fff;
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 4px 18px rgba(232,117,152,.10);
        }
        .card-form h2 {
            font-size: 16px; color: #333; margin-bottom: 20px;
            padding-bottom: 12px; border-bottom: 1px solid #f0e0e8;
        }

        /* ── Info kendaraan ─────────────────────────── */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 22px;
        }
        .info-item label {
            font-size: 11px; color: #aaa; display: block; margin-bottom: 4px;
        }
        .info-item .val {
            font-size: 14px; font-weight: 600; color: #333;
        }
        .plat-badge {
            display: inline-block;
            background: #fff0f6; color: #d94f87;
            padding: 4px 12px; border-radius: 8px;
            font-weight: 700; font-size: 13px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px; border-radius: 8px;
            font-size: 12px; font-weight: 600;
        }
        .status-masuk  { background: #e8f8f0; color: #2e7d52; }
        .status-keluar { background: #fce4ec; color: #c83f76; }

        /* ── Form input ─────────────────────────────── */
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block; font-size: 12px;
            color: #888; margin-bottom: 6px; font-weight: 600;
        }
        .form-group input, .form-group select {
            width: 100%; padding: 10px 14px;
            border: 1.5px solid #f0c8d8; border-radius: 10px;
            font-size: 13px; color: #333; outline: none;
            background: #fff;
        }
        .form-group input:focus, .form-group select:focus {
            border-color: #e87598; box-shadow: 0 0 0 3px rgba(232,117,152,.12);
        }
        .note { font-size: 11px; color: #aaa; margin-top: 5px; }

        /* ── Tombol ─────────────────────────────────── */
        .btn-group { display: flex; gap: 12px; margin-top: 8px; }
        .btn-keluar {
            flex: 1; padding: 12px;
            background: linear-gradient(135deg,#ff8fab,#e87598);
            color: #fff; border: none; border-radius: 10px;
            font-size: 14px; font-weight: 700; cursor: pointer;
        }
        .btn-keluar:hover { background: linear-gradient(135deg,#e87598,#d45f85); }
        .btn-batal {
            padding: 12px 20px;
            background: #f5f5f5; color: #777;
            border: none; border-radius: 10px;
            font-size: 13px; cursor: pointer; text-decoration: none;
            display: flex; align-items: center;
        }
        .btn-batal:hover { background: #eee; }

        /* ── Alert ──────────────────────────────────── */
        .alert {
            padding: 12px 16px; border-radius: 10px;
            margin-bottom: 18px; font-size: 13px;
        }
        .alert-error   { background:#fce4ec; color:#c62828; border:1px solid #f48fb1; }
        .alert-success { background:#e8f5e9; color:#2e7d32; border:1px solid #a5d6a7; }

        /* ── Struk preview (kolom kanan) ─────────────── */
        /* Pakai class dari struk_parkir.css, tambahan kecil saja */
        .struk-preview-wrap { position: sticky; top: 24px; }
        .struk-preview-wrap .struk { width: 100%; box-sizing: border-box; }
        .struk-preview-wrap .aksi  { justify-content: center; }

        /* ── Sudah keluar → hanya tampil struk ─────── */
        .full-struk {
            max-width: 460px; margin: 0 auto;
        }

        /* ── Kalkulator estimasi ─────────────────────── */
        .estimasi-box {
            background: #fff8fb; border: 1px dashed #f0c8d8;
            border-radius: 10px; padding: 14px 16px;
            margin-bottom: 18px;
        }
        .estimasi-box h4 { font-size: 12px; color: #aaa; margin-bottom: 10px; }
        .estimasi-row {
            display: flex; justify-content: space-between;
            font-size: 13px; color: #555; margin-bottom: 6px;
        }
        .estimasi-total {
            display: flex; justify-content: space-between;
            font-size: 15px; font-weight: 700; color: #d94f87;
            border-top: 1px dashed #f0c8d8; padding-top: 10px; margin-top: 6px;
        }

        @media print {
            .main-content, .wrapper { display: block; }
            aside, .card-form, .page-header, .breadcrumb { display: none !important; }
            .struk-preview-wrap { position: static; }
            .grid-2 { display: block; }
            body { background: #fff; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include __DIR__ . '/navbar.php'; ?>

    <div class="main-content">

        <div class="breadcrumb">
            <a href="../controller/c_transaksi.php?aksi=tampil">← Data Parkir</a>
            &nbsp;/&nbsp; Proses Keluar Kendaraan
        </div>

        <div class="page-header">
            <h1>🚗 Proses Keluar Kendaraan</h1>
            <p>Catat waktu keluar dan hitung biaya parkir</p>
        </div>

        <?php if ($pesan): ?>
        <div class="alert alert-<?= $pesan['tipe'] === 'success' ? 'success' : 'error' ?>">
            <strong><?= htmlspecialchars($pesan['judul']) ?></strong> —
            <?= htmlspecialchars($pesan['teks']) ?>
        </div>
        <?php endif; ?>


        <?php if ($sudah_keluar): ?>
        <!-- ══════════════════════════════════════════════
             KENDARAAN SUDAH KELUAR → LANGSUNG TAMPIL STRUK
             ══════════════════════════════════════════════ -->
        <?php
            $ts_masuk  = !empty($transaksi->waktu_masuk)  ? strtotime($transaksi->waktu_masuk)  : null;
            $ts_keluar = !empty($transaksi->waktu_keluar) ? strtotime($transaksi->waktu_keluar) : null;
            $durasi_str   = $transaksi->durasi_jam ? $transaksi->durasi_jam . ' Jam' : '-';
            $tarif_str    = 'Rp' . number_format((float)($transaksi->tarif_per_jam ?? 0), 0, ',', '.') . '/Jam';
            $biaya_str    = 'Rp' . number_format((float)($transaksi->biaya_total ?? 0), 0, ',', '.');
        ?>
        <div class="full-struk">
            <div class="alert alert-success" style="margin-bottom:20px;">
                ✅ Kendaraan <strong><?= $plat ?></strong> sudah keluar. Berikut struk pembayarannya.
            </div>

            <div class="struk">
                <div class="header">
                    <div class="logo">P</div>
                    <h1>PARKIR IKEA</h1>
                    <p>BUKTI PEMBAYARAN PARKIR</p>
                </div>
                <div class="garis"></div>
                <div class="info">
                    <div class="row"><span>No. Struk</span><strong><?= $no_trx ?></strong></div>
                    <div class="row"><span>No. Polisi</span><strong><?= $plat ?></strong></div>
                    <div class="row"><span>Kendaraan</span><strong><?= $jenis ?></strong></div>
                    <div class="row"><span>Area Parkir</span><strong><?= $area ?></strong></div>
                    <div class="row"><span>Petugas</span><strong><?= $petugas ?></strong></div>
                </div>
                <div class="garis"></div>
                <div class="waktu">
                    <div class="waktu-item">
                        <span>Waktu Masuk</span>
                        <strong><?= $ts_masuk ? date('d/m/Y', $ts_masuk) : '-' ?></strong>
                        <small><?= $ts_masuk ? date('H:i', $ts_masuk) . ' WIB' : '' ?></small>
                    </div>
                    <div class="waktu-item">
                        <span>Waktu Keluar</span>
                        <strong><?= $ts_keluar ? date('d/m/Y', $ts_keluar) : '-' ?></strong>
                        <small><?= $ts_keluar ? date('H:i', $ts_keluar) . ' WIB' : '' ?></small>
                    </div>
                </div>
                <div class="garis"></div>
                <div class="biaya">
                    <div class="row"><span>Durasi</span><strong><?= $durasi_str ?></strong></div>
                    <div class="row"><span>Tarif</span><strong><?= $tarif_str ?></strong></div>
                    <div class="total">
                        <span>TOTAL</span>
                        <strong><?= $biaya_str ?></strong>
                    </div>
                </div>
                <div class="garis"></div>
                <div class="footer">
                    <p>Terima kasih telah menggunakan</p>
                    <strong>LAYANAN PARKIR IKEA 💗</strong>
                    <small>Simpan struk ini sebagai bukti parkir.</small>
                </div>
            </div>

            <div class="aksi" style="margin-top:20px; display:flex; gap:12px; justify-content:center;">
                <button onclick="window.print()" class="btn-keluar" style="max-width:180px;">
                    🖨️ Cetak Struk
                </button>
                <a href="../controller/c_transaksi.php?aksi=tampil" class="btn-batal">
                    ← Kembali
                </a>
            </div>
        </div>


        <?php else: ?>
        <!-- ══════════════════════════════════════════════
             KENDARAAN MASIH PARKIR → FORM PROSES KELUAR
             ══════════════════════════════════════════════ -->
        <div class="grid-2">

            <!-- Kolom Kiri: Form -->
            <div class="card-form">
                <h2>📋 Data Kendaraan</h2>

                <!-- Info kendaraan (readonly) -->
                <div class="info-grid">
                    <div class="info-item">
                        <label>No. Transaksi</label>
                        <div class="val"><?= $no_trx ?></div>
                    </div>
                    <div class="info-item">
                        <label>No. Polisi</label>
                        <div class="val"><span class="plat-badge"><?= $plat ?></span></div>
                    </div>
                    <div class="info-item">
                        <label>Jenis Kendaraan</label>
                        <div class="val"><?= $jenis ?></div>
                    </div>
                    <div class="info-item">
                        <label>Area Parkir</label>
                        <div class="val"><?= $area ?></div>
                    </div>
                    <div class="info-item">
                        <label>Waktu Masuk</label>
                        <div class="val"><?= $tgl_masuk ?></div>
                    </div>
                    <div class="info-item">
                        <label>Petugas</label>
                        <div class="val"><?= $petugas ?></div>
                    </div>
                    <div class="info-item">
                        <label>Status</label>
                        <div class="val">
                            <span class="status-badge status-masuk">🟢 Sedang Parkir</span>
                        </div>
                    </div>
                </div>

                <hr style="border:none; border-top:1px solid #f0e0e8; margin-bottom:20px;">
                <h2>🚪 Proses Keluar</h2>

                <form method="POST"
                      action="../controller/c_transaksi.php?aksi=proses_keluar&id=<?= $transaksi->id_parkir ?>">

                    <div class="form-group">
                        <label>Waktu Keluar *</label>
                        <!-- Nilai ISO dipakai JS untuk estimasi; tidak bisa diubah -->
                        <input type="hidden" id="waktu_keluar" value="<?= $default_keluar ?>">
                        <!-- Tampilan 24 jam WIB, hanya baca -->
                        <input type="text" id="waktu_keluar_tampil" readonly
                               value="<?= date('d/m/Y H:i', $default_keluar_ts) ?> WIB"
                               style="background:#f7f0f3; cursor:not-allowed;">
                        <div class="note">Waktu sekarang (WIB), otomatis dan tidak bisa diubah.</div>
                    </div>

                    <div class="form-group">
                        <label>Tarif *</label>
                        <select name="id_tarif" id="id_tarif" required onchange="hitungEstimasi()">
                            <option value="" disabled>Pilih Tarif</option>
                            <?php foreach ($data_tarif as $t): ?>
                            <option value="<?= $t->id_tarif ?>"
                                    data-tarif="<?= $t->tarif_per_jam ?>"
                                    <?= ($transaksi->id_tarif == $t->id_tarif) ? 'selected' : '' ?>>
                                <?= htmlspecialchars(ucfirst($t->jenis_kendaraan)) ?>
                                — Rp<?= number_format((float)$t->tarif_per_jam, 0, ',', '.') ?>/jam
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Estimasi biaya (dihitung JS) -->
                    <div class="estimasi-box" id="estimasiBox">
                        <h4>💰 ESTIMASI BIAYA</h4>
                        <div class="estimasi-row">
                            <span>Waktu Masuk</span>
                            <span><?= $tgl_masuk ?></span>
                        </div>
                        <div class="estimasi-row">
                            <span>Waktu Keluar</span>
                            <span id="estKeluar">—</span>
                        </div>
                        <div class="estimasi-row">
                            <span>Durasi</span>
                            <span id="estDurasi">—</span>
                        </div>
                        <div class="estimasi-row">
                            <span>Tarif/Jam</span>
                            <span id="estTarif">—</span>
                        </div>
                        <div class="estimasi-total">
                            <span>TOTAL</span>
                            <span id="estTotal">—</span>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn-keluar">
                            🚗 Konfirmasi Keluar & Cetak Struk
                        </button>
                        <a href="../controller/c_transaksi.php?aksi=tampil"
                           class="btn-batal">Batal</a>
                    </div>

                </form>
            </div>

            <!-- Kolom Kanan: Preview Struk -->
            <div class="struk-preview-wrap">
                <div class="struk">
                    <div class="header">
                        <div class="logo">P</div>
                        <h1>PARKIR IKEA</h1>
                        <p>BUKTI PEMBAYARAN PARKIR</p>
                    </div>
                    <div class="garis"></div>
                    <div class="info">
                        <div class="row"><span>No. Struk</span><strong><?= $no_trx ?></strong></div>
                        <div class="row"><span>No. Polisi</span><strong><?= $plat ?></strong></div>
                        <div class="row"><span>Kendaraan</span><strong><?= $jenis ?></strong></div>
                        <div class="row"><span>Area Parkir</span><strong><?= $area ?></strong></div>
                    </div>
                    <div class="garis"></div>
                    <div class="waktu">
                        <div class="waktu-item">
                            <span>Waktu Masuk</span>
                            <strong><?= date('d/m/Y', strtotime($transaksi->waktu_masuk)) ?></strong>
                            <small><?= date('H:i', strtotime($transaksi->waktu_masuk)) ?> WIB</small>
                        </div>
                        <div class="waktu-item">
                            <span>Waktu Keluar</span>
                            <strong id="prevKeluar">—</strong>
                            <small id="prevKeluarJam"></small>
                        </div>
                    </div>
                    <div class="garis"></div>
                    <div class="biaya">
                        <div class="row"><span>Durasi</span><strong id="prevDurasi">—</strong></div>
                        <div class="row"><span>Tarif</span><strong id="prevTarif">—</strong></div>
                        <div class="total">
                            <span>TOTAL</span>
                            <strong id="prevTotal">—</strong>
                        </div>
                    </div>
                    <div class="garis"></div>
                    <div class="footer">
                        <p>Terima kasih telah menggunakan</p>
                        <strong>LAYANAN PARKIR IKEA 💗</strong>
                        <small>Simpan struk sebagai bukti parkir.</small>
                    </div>
                </div>
            </div>

        </div><!-- .grid-2 -->
        <?php endif; ?>

    </div>
</div>

<script src="../assets/navbar.js"></script>
<script>
// Waktu masuk dari PHP (format ISO untuk JS)
const waktuMasukISO = '<?= date('Y-m-d\TH:i:s', strtotime($transaksi->waktu_masuk ?? 'now')) ?>';

function formatRupiah(n) {
    return 'Rp' + Math.round(n).toLocaleString('id-ID');
}
function formatTgl(dt) {
    return dt.toLocaleDateString('id-ID', { day:'2-digit', month:'2-digit', year:'numeric' });
}
function formatJam(dt) {
    return dt.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' }) + ' WIB';
}

function hitungEstimasi() {
    const elKeluar = document.getElementById('waktu_keluar');
    const elTarif  = document.getElementById('id_tarif');
    if (!elKeluar || !elTarif) return;

    const tsMasuk  = new Date(waktuMasukISO);
    const tsKeluar = elKeluar.value ? new Date(elKeluar.value) : null;
    const opt      = elTarif.selectedOptions[0];
    const tarifJam = opt ? parseFloat(opt.dataset.tarif || 0) : 0;

    // Update estimasi box
    document.getElementById('estKeluar').textContent =
        tsKeluar ? tsKeluar.toLocaleString('id-ID') : '—';

    if (tsKeluar && tsKeluar > tsMasuk && tarifJam > 0) {
        const selisihMs  = tsKeluar - tsMasuk;
        const durasi     = Math.max(1, Math.ceil(selisihMs / 3600000));
        const total      = durasi * tarifJam;
        const tarifLabel = opt ? opt.textContent.trim().split('—')[1]?.trim() : '—';

        document.getElementById('estDurasi').textContent = durasi + ' Jam';
        document.getElementById('estTarif').textContent  = tarifLabel || formatRupiah(tarifJam) + '/jam';
        document.getElementById('estTotal').textContent  = formatRupiah(total);

        // Update struk preview (kolom kanan)
        document.getElementById('prevKeluar').textContent    = formatTgl(tsKeluar);
        document.getElementById('prevKeluarJam').textContent = formatJam(tsKeluar);
        document.getElementById('prevDurasi').textContent    = durasi + ' Jam';
        document.getElementById('prevTarif').textContent     = tarifLabel || formatRupiah(tarifJam) + '/jam';
        document.getElementById('prevTotal').textContent     = formatRupiah(total);
    } else {
        ['estDurasi','estTarif','estTotal',
         'prevKeluar','prevKeluarJam','prevDurasi','prevTarif','prevTotal']
            .forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = '—';
            });
    }
}

// Hitung saat halaman pertama kali load
document.addEventListener('DOMContentLoaded', hitungEstimasi);
</script>
</body>
</html>
