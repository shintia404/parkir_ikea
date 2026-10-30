<?php
/**
 * =============================================================
 *  SKRIP DIAGNOSTIK LOGIN — HANYA UNTUK DEBUGGING SEMENTARA!
 * =============================================================
 * Cara pakai:
 *   1. Taruh file ini di folder controller/ (sejajar dengan c_login.php)
 *   2. Buka lewat browser, contoh:
 *      http://localhost/parkir_ikea/controller/debug_login.php?username=andi&password=123456
 *   3. Baca hasilnya di layar, lalu HAPUS file ini kalau sudah selesai debug
 *      (jangan sampai ketinggalan di server produksi).
 * =============================================================
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../models/koneksi.php';

echo "<pre style='font-family:monospace;font-size:14px'>";

// 1. Cek koneksi database
echo "1) KONEKSI DATABASE\n";
try {
    $koneksi_obj = new koneksi();
    $koneksi     = $koneksi_obj->koneksi;
    echo "   OK - berhasil konek ke database.\n\n";
} catch (Throwable $e) {
    echo "   GAGAL: " . $e->getMessage() . "\n";
    echo "</pre>";
    exit;
}

$username = $_GET['username'] ?? '';
$password = $_GET['password'] ?? '';

echo "2) INPUT YANG DICEK\n";
echo "   username : '" . $username . "' (panjang: " . strlen($username) . " karakter)\n";
echo "   password : '" . $password . "' (panjang: " . strlen($password) . " karakter)\n\n";

if ($username === '') {
    echo "   -> Tambahkan ?username=...&password=... di URL untuk mulai tes.\n";
    echo "</pre>";
    exit;
}

// 2. Cek query ke tabel tb_user
echo "3) CARI USERNAME / NAMA LENGKAP DI TABEL tb_user\n";
$stmt = $koneksi->prepare("SELECT * FROM tb_user WHERE username = ? OR nama_lengkap = ? LIMIT 1");
if (!$stmt) {
    echo "   GAGAL prepare query: " . $koneksi->error . "\n";
    echo "   (Kemungkinan besar tabel tb_user belum ada / salah nama database)\n";
    echo "</pre>";
    exit;
}
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "   TIDAK DITEMUKAN user dengan username/nama persis '$username'.\n";
    echo "   -> Cek lagi isi kolom `username` di phpMyAdmin, siapa tau ada\n";
    echo "      spasi tersembunyi, huruf besar/kecil beda, atau memang belum\n";
    echo "      ke-import datanya.\n";
    echo "</pre>";
    exit;
}

$user = $result->fetch_assoc();
echo "   DITEMUKAN. Isi datanya:\n";
foreach ($user as $kolom => $nilai) {
    if ($kolom === 'password') {
        // Password ditampilkan sebagian saja demi keamanan
        $masked = substr($nilai, 0, 4) . str_repeat('*', max(0, strlen($nilai) - 4));
        echo "     - $kolom : $masked  (panjang asli: " . strlen($nilai) . ")\n";
    } else {
        echo "     - $kolom : $nilai\n";
    }
}
echo "\n";

// 3. Cek status akun
echo "4) CEK STATUS AKUN\n";
echo "   status_aktif di database = '" . $user['status_aktif'] . "'\n";
if ((int) $user['status_aktif'] !== 1) {
    echo "   -> INI MASALAHNYA: status_aktif bukan 1, jadi login akan ditolak\n";
    echo "      meskipun password benar.\n";
} else {
    echo "   OK - akun aktif.\n";
}
echo "\n";

// 4. Cek kecocokan password
echo "5) CEK PASSWORD\n";
$isHash = preg_match('/^\$(2y|2a|2b|argon2i|argon2id)\$/', $user['password']);
echo "   Format password di DB: " . ($isHash ? "sudah di-hash (bcrypt/argon2)" : "teks biasa (belum di-hash)") . "\n";

if ($isHash) {
    $cocok = password_verify($password, $user['password']);
} else {
    $cocok = hash_equals($user['password'], $password);
}

echo "   Password yang diinput cocok dengan yang di database? " . ($cocok ? "YA, COCOK" : "TIDAK COCOK") . "\n";

if (!$cocok && !$isHash) {
    echo "   -> Bandingkan persis: DB punya '{$user['password']}' vs input '{$password}'.\n";
    echo "      Kalau keduanya kelihatan sama tapi tetap 'TIDAK COCOK', biasanya\n";
    echo "      ada karakter tak kelihatan (spasi/enter) ikut ke-copy waktu\n";
    echo "      mengisi data password di database.\n";
}

echo "\n6) KESIMPULAN\n";
if ((int) $user['status_aktif'] === 1 && $cocok) {
    echo "   Semua cocok -> harusnya login BERHASIL. Kalau di c_login.php\n";
    echo "   asli masih gagal, berarti file c_login.php yang jalan di server\n";
    echo "   BUKAN versi yang sudah diperbaiki. Cek ulang isi file itu.\n";
} else {
    echo "   Ditemukan penyebabnya di atas ^ (lihat bagian 4 dan 5).\n";
}

echo "</pre>";
