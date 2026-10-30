<?php
// Memulai sesi untuk menyimpan notifikasi (SweetAlert-style)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Semua waktu di aplikasi ini memakai zona waktu Indonesia (WIB)
date_default_timezone_set('Asia/Jakarta');

// --- PROTEKSI AKSES ---
// Sebelumnya file ini tidak mengecek login/role sama sekali, jadi siapa pun
// yang tahu URL-nya bisa langsung tambah/edit/hapus transaksi & menandai
// kendaraan keluar walau belum login. Sekarang wajib login dulu, dan hanya
// admin/petugas yang boleh (pola sama seperti di DashBoard.php).
if (empty($_SESSION['login'])) {
    header('Location: ../view/login.php');
    exit();
}
if (!in_array($_SESSION['role'] ?? '', ['admin', 'petugas'], true)) {
    $_SESSION['pesan'] = [
        'tipe'  => 'error',
        'judul' => 'Akses Ditolak!',
        'teks'  => 'Halaman transaksi hanya bisa diakses oleh admin atau petugas.'
    ];
    header('Location: ../view/DashBoard_Owner.php');
    exit();
}

// __DIR__ dipakai supaya path tidak bergantung dari mana script diakses
include_once __DIR__ . '/../models/m_transaksi.php';
require_once __DIR__ . '/../models/m_log.php';

// Inisialisasi koneksi database
$koneksi_obj  = new koneksi();
$dbConnection = $koneksi_obj->koneksi;

// Menyiapkan model transaksi
$transaksi_model = new M_Transaksi($dbConnection);

// Menangkap perintah aksi dari URL (tampil, edit, hapus)
$aksi = $_GET['aksi'] ?? 'tampil';
$id   = $_GET['id'] ?? null;

// --- BATAS TANGGAL UNTUK TRANSAKSI BARU (form Tambah Transaksi) ---
// Waktu masuk tidak boleh sebelum 23 September 2026.
// Waktu keluar tidak boleh sebelum tanggal hari ini (zona waktu WIB).
$tgl_masuk_min  = '2026-09-23';
$tgl_keluar_min = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d');

// Ubah 'YYYY-MM-DD' menjadi '23 September 2026' untuk pesan error
function tgl_indo($ymd) {
    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $t = strtotime($ymd);
    return date('j', $t) . ' ' . $bulan[(int) date('n', $t)] . ' ' . date('Y', $t);
}


/* ==========================================================================
   1. LOGIKA TAMPIL DATA (READ)
   ========================================================================== */
if ($aksi === 'tampil') {
    // Data Parkir hanya menampilkan kendaraan yang masih parkir (status masuk).
    // Yang sudah keluar ada di menu Riwayat (controller/c_riwayat.php).
    $data_transaksi = $transaksi_model->tampil_masuk();
    $data_kendaraan = $transaksi_model->ambil_kendaraan();
    $data_area      = $transaksi_model->ambil_area();
    $data_tarif     = $transaksi_model->ambil_tarif();
    include __DIR__ . '/../view/v_transaksi.php';
    exit();
}


/* ==========================================================================
   2. LOGIKA EDIT (FORM EDIT TRANSAKSI)
   ========================================================================== */
elseif ($aksi === 'edit' && $id) {
    $transaksi      = $transaksi_model->tampil_data_by_id($id);
    $data_kendaraan = $transaksi_model->ambil_kendaraan();
    $data_area      = $transaksi_model->ambil_area();
    $data_tarif     = $transaksi_model->ambil_tarif();
    $data_user      = $transaksi_model->ambil_user();
    include __DIR__ . '/../view/v_edit_transaksi.php';
    exit();
}


/* ==========================================================================
   2B. HALAMAN KELUAR KENDARAAN (form proses keluar — khusus petugas/admin)
   ========================================================================== */
elseif ($aksi === 'keluar_kendaraan' && $id) {
    $transaksi      = $transaksi_model->tampil_struk_by_id($id); // sudah JOIN kendaraan, area, tarif
    $data_tarif     = $transaksi_model->ambil_tarif();

    if (!$transaksi) {
        $_SESSION['pesan'] = [
            'tipe'  => 'error',
            'judul' => 'Data Tidak Ditemukan!',
            'teks'  => 'Transaksi tidak ditemukan.'
        ];
        header('Location: ../controller/c_transaksi.php?aksi=tampil');
        exit();
    }

    include __DIR__ . '/../view/v_keluar_kendaraan.php';
    exit();
}


/* ==========================================================================
   2C. PROSES SIMPAN KELUAR KENDARAAN (POST dari v_keluar_kendaraan)
   ========================================================================== */
elseif ($aksi === 'proses_keluar' && $id) {
    // Waktu keluar TIDAK diambil dari form: selalu waktu sekarang (WIB) dari
    // server, jadi tidak bisa diubah/dimanipulasi dari browser.
    $waktu_keluar = date('Y-m-d H:i:s');
    $id_tarif     = $_POST['id_tarif'] ?? '';

    if ($id_tarif === '') {
        $_SESSION['pesan'] = [
            'tipe'  => 'error',
            'judul' => 'Data Tidak Lengkap!',
            'teks'  => 'Tarif wajib diisi.'
        ];
        header('Location: ../controller/c_transaksi.php?aksi=keluar_kendaraan&id=' . $id);
        exit();
    }

    // Ambil data transaksi lama
    $transaksi_lama = $transaksi_model->tampil_data_by_id($id);
    if (!$transaksi_lama) {
        header('Location: ../controller/c_transaksi.php?aksi=tampil');
        exit();
    }

    $masuk  = new DateTime($transaksi_lama->waktu_masuk);
    $keluar = new DateTime($waktu_keluar);

    // Kalau kendaraan baru saja dicatat masuk (waktu sekarang <= waktu masuk),
    // waktu keluar dipatok minimal 1 menit setelah waktu masuk.
    if ($keluar <= $masuk) {
        $keluar = (clone $masuk)->modify('+1 minute');
        $waktu_keluar = $keluar->format('Y-m-d H:i:s');
    }

    $selisih_jam = ($keluar->getTimestamp() - $masuk->getTimestamp()) / 3600;
    $durasi_jam  = max(1, (int) ceil($selisih_jam));

    $tarif       = $transaksi_model->tampil_tarif_by_id($id_tarif);
    $biaya_total = $durasi_jam * (int)($tarif ? $tarif->tarif_per_jam : 0);

    $data = [
        'id_kendaraan' => $transaksi_lama->id_kendaraan,
        'waktu_masuk'  => $transaksi_lama->waktu_masuk,
        'waktu_keluar' => $waktu_keluar,
        'id_tarif'     => $id_tarif,
        'durasi_jam'   => $durasi_jam,
        'biaya_total'  => $biaya_total,
        'status'       => 'keluar',
        'id_user'      => $transaksi_lama->id_user,
        'id_area'      => $transaksi_lama->id_area,
    ];

    $result = $transaksi_model->ubah_data($id, $data);

    if ($result) {
        catat_log($dbConnection, $_SESSION['id_user'] ?? null, 'Mencatat kendaraan keluar (transaksi #' . (int) $id . ')');
        // Redirect langsung ke cetak struk setelah keluar berhasil
        header('Location: ../controller/c_transaksi.php?aksi=cetak_struk&id=' . $id);
    } else {
        $_SESSION['pesan'] = [
            'tipe'  => 'error',
            'judul' => 'Gagal!',
            'teks'  => 'Gagal menyimpan data keluar kendaraan.'
        ];
        header('Location: ../controller/c_transaksi.php?aksi=keluar_kendaraan&id=' . $id);
    }
    exit();
}


/* ==========================================================================
   3. LOGIKA CETAK STRUK (hanya untuk transaksi yang kendaraannya sudah keluar)
   ========================================================================== */
elseif ($aksi === 'cetak_struk' && $id) {
    $struk = $transaksi_model->tampil_struk_by_id($id);

    // Transaksi tidak ditemukan
    if (!$struk) {
        $_SESSION['pesan'] = [
            'tipe'  => 'error',
            'judul' => 'Struk Tidak Ditemukan!',
            'teks'  => 'Data transaksi tidak ditemukan.'
        ];
        header('Location: ../controller/c_transaksi.php?aksi=tampil');
        exit();
    }

    // Struk hanya boleh dicetak kalau kendaraan sudah keluar
    // (waktu_keluar & biaya_total baru pasti terisi di titik ini)
    if (strtolower($struk->status) !== 'keluar') {
        $_SESSION['pesan'] = [
            'tipe'  => 'error',
            'judul' => 'Belum Bisa Dicetak!',
            'teks'  => 'Struk hanya bisa dicetak setelah kendaraan keluar.'
        ];
        header('Location: ../controller/c_transaksi.php?aksi=tampil');
        exit();
    }

    include __DIR__ . '/../view/v_struk_parkir.php';
    exit();
}


/* ==========================================================================
   4. LOGIKA HAPUS DATA (DELETE)
   ========================================================================== */
elseif ($aksi === 'hapus' && $id) {
    $result = $transaksi_model->hapus_data($id);

    if ($result) {
        catat_log($dbConnection, $_SESSION['id_user'] ?? null, 'Menghapus transaksi parkir #' . (int) $id);
        $_SESSION['pesan'] = [
            'tipe'  => 'success',
            'judul' => 'Berhasil Dihapus!',
            'teks'  => 'Data transaksi berhasil dihapus.'
        ];
    } else {
        $_SESSION['pesan'] = [
            'tipe'  => 'error',
            'judul' => 'Gagal Hapus!',
            'teks'  => 'Data transaksi gagal dihapus.'
        ];
    }

    header('Location: ../controller/c_transaksi.php?aksi=tampil');
    exit();
}


/* ==========================================================================
   5. LOGIKA SIMPAN DATA (POST: TAMBAH & UPDATE)
   ========================================================================== */
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_kendaraan = $_POST['id_kendaraan'] ?? '';
    $id_area      = $_POST['id_area'] ?? '';
    $id_tarif     = $_POST['id_tarif'] ?? '';
    $id_user      = $_POST['id_user'] ?? '';
    $waktu_masuk  = $_POST['waktu_masuk'] ?? '';
    $waktu_keluar = trim($_POST['waktu_keluar'] ?? '');

    // Transaksi baru: petugas diambil otomatis dari user yang sedang login
    // (form Tambah tidak lagi menampilkan pilihan petugas).
    if ($aksi === 'tambah') {
        $id_user = $_SESSION['id_user'] ?? $id_user;

        // Tarif ditentukan otomatis dari jenis kendaraan bila tidak dikirim
        // dari form (modal "Kendaraan Masuk" tidak punya pilihan tarif lagi).
        if ($id_tarif === '' && $id_kendaraan !== '') {
            $tarif_otomatis = $transaksi_model->tampil_tarif_by_kendaraan((int) $id_kendaraan);
            if ($tarif_otomatis) {
                $id_tarif = (string) $tarif_otomatis->id_tarif;
            }
        }
    }

    $redirect_gagal = $_SERVER['HTTP_REFERER'] ?? '../controller/c_transaksi.php?aksi=tampil';

    // --- VALIDASI DASAR: semua field wajib harus terisi ---
    if ($id_kendaraan === '' || $id_area === '' || $id_tarif === '' || $id_user === '' || $waktu_masuk === '') {
        $_SESSION['pesan'] = [
            'tipe'  => 'error',
            'judul' => 'Data Tidak Lengkap!',
            'teks'  => 'Kendaraan, area, dan waktu masuk wajib diisi (tarif mengikuti jenis kendaraan).'
        ];
        header('Location: ' . $redirect_gagal);
        exit();
    }

    // --- VALIDASI TANGGAL (khusus transaksi baru) ---
    // Sengaja dicek di server juga, bukan cuma lewat atribut min di form,
    // karena atribut min di browser bisa dilewati.
    if ($aksi === 'tambah') {
        $ts_masuk = strtotime($waktu_masuk);
        if ($ts_masuk === false) {
            $_SESSION['pesan'] = [
                'tipe'  => 'error',
                'judul' => 'Waktu Masuk Tidak Valid!',
                'teks'  => 'Format waktu masuk tidak dikenali.'
            ];
            header('Location: ' . $redirect_gagal);
            exit();
        }
        if (date('Y-m-d', $ts_masuk) < $tgl_masuk_min) {
            $_SESSION['pesan'] = [
                'tipe'  => 'error',
                'judul' => 'Waktu Masuk Tidak Valid!',
                'teks'  => 'Waktu masuk tidak boleh sebelum tanggal ' . tgl_indo($tgl_masuk_min) . '.'
            ];
            header('Location: ' . $redirect_gagal);
            exit();
        }

        if ($waktu_keluar !== '') {
            $ts_keluar = strtotime($waktu_keluar);
            if ($ts_keluar === false) {
                $_SESSION['pesan'] = [
                    'tipe'  => 'error',
                    'judul' => 'Waktu Keluar Tidak Valid!',
                    'teks'  => 'Format waktu keluar tidak dikenali.'
                ];
                header('Location: ' . $redirect_gagal);
                exit();
            }
            if (date('Y-m-d', $ts_keluar) < $tgl_keluar_min) {
                $_SESSION['pesan'] = [
                    'tipe'  => 'error',
                    'judul' => 'Waktu Keluar Tidak Valid!',
                    'teks'  => 'Waktu keluar tidak boleh sebelum tanggal hari ini (' . tgl_indo($tgl_keluar_min) . ').'
                ];
                header('Location: ' . $redirect_gagal);
                exit();
            }
        }
    }

    // --- HITUNG OTOMATIS DURASI & BIAYA JIKA WAKTU KELUAR SUDAH DIISI ---
    $durasi_jam  = null;
    $biaya_total = 0;
    $status      = 'Masuk';

    if ($waktu_keluar !== '') {
        $masuk  = new DateTime($waktu_masuk);
        $keluar = new DateTime($waktu_keluar);

        // Validasi: waktu keluar tidak boleh sebelum/sama dengan waktu masuk
        if ($keluar <= $masuk) {
            $_SESSION['pesan'] = [
                'tipe'  => 'error',
                'judul' => 'Waktu Tidak Valid!',
                'teks'  => 'Waktu keluar harus setelah waktu masuk.'
            ];
            header('Location: ' . $redirect_gagal);
            exit();
        }

        $selisih_jam = ($keluar->getTimestamp() - $masuk->getTimestamp()) / 3600;
        $durasi_jam  = (int) ceil($selisih_jam);
        if ($durasi_jam < 1) {
            $durasi_jam = 1;
        }

        // Ambil tarif per jam dari database supaya biaya selalu konsisten
        // dengan tarif yang dipilih (tidak diketik manual = tidak ada salah hitung)
        $tarif         = $transaksi_model->tampil_tarif_by_id($id_tarif);
        $tarif_per_jam = $tarif ? (int) $tarif->tarif_per_jam : 0;
        $biaya_total   = $durasi_jam * $tarif_per_jam;
        $status        = 'Keluar';
    } else {
        // Simpan sebagai NULL (bukan string kosong) supaya konsisten dengan kolom datetime
        $waktu_keluar = null;
    }

    $data = [
        'id_kendaraan' => $id_kendaraan,
        'waktu_masuk'  => $waktu_masuk,
        'waktu_keluar' => $waktu_keluar,
        'id_tarif'     => $id_tarif,
        'durasi_jam'   => $durasi_jam,
        'biaya_total'  => $biaya_total,
        'status'       => $status,
        'id_user'      => $id_user,
        'id_area'      => $id_area,
    ];

    // --- SUB-LOGIKA A: TAMBAH TRANSAKSI BARU ---
    if ($aksi === 'tambah') {
        $result = $transaksi_model->tambah_data($data);

        if ($result) {
            catat_log($dbConnection, $_SESSION['id_user'] ?? null, 'Mencatat kendaraan masuk');
            $_SESSION['pesan'] = [
                'tipe'  => 'success',
                'judul' => 'Berhasil Ditambahkan!',
                'teks'  => 'Transaksi parkir baru berhasil disimpan.'
            ];
        } else {
            $_SESSION['pesan'] = [
                'tipe'  => 'error',
                'judul' => 'Gagal Ditambahkan!',
                'teks'  => 'Gagal menyimpan transaksi baru.'
            ];
        }

        header('Location: ../controller/c_transaksi.php?aksi=tampil');
        exit();
    }

    // --- SUB-LOGIKA B: UPDATE / UBAH TRANSAKSI ---
    elseif ($aksi === 'update' && $id) {
        $result = $transaksi_model->ubah_data($id, $data);

        if ($result) {
            catat_log($dbConnection, $_SESSION['id_user'] ?? null, 'Mengubah data transaksi parkir #' . (int) $id);
            $_SESSION['pesan'] = [
                'tipe'  => 'success',
                'judul' => 'Berhasil Diperbarui!',
                'teks'  => 'Transaksi berhasil diperbarui.'
            ];
        } else {
            $_SESSION['pesan'] = [
                'tipe'  => 'error',
                'judul' => 'Gagal Diperbarui!',
                'teks'  => 'Gagal memperbarui transaksi.'
            ];
        }

        header('Location: ../controller/c_transaksi.php?aksi=tampil');
        exit();
    }

    // Kalau aksi tidak dikenali, kembali ke daftar transaksi
    header('Location: ../controller/c_transaksi.php?aksi=tampil');
    exit();
}


/* ==========================================================================
   6. DEFAULT FALLBACK
   ========================================================================== */
else {
    header('Location: ../controller/c_transaksi.php?aksi=tampil');
    exit();
}