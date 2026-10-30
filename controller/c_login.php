<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Mengambil file koneksi database (lokasinya ada di folder models/)
require_once __DIR__ . '/../models/koneksi.php';
require_once __DIR__ . '/../models/m_log.php';

// Membuat koneksi ke database
$koneksi_obj = new koneksi();
$koneksi     = $koneksi_obj->koneksi;

// Cek apakah tombol login ditekan
if (isset($_POST['login'])) {

    // Mengambil data dari form login
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Mencari username di tabel tb_user (pakai prepared statement, aman dari SQL Injection)
    // Boleh masuk pakai "username" ATAU "nama lengkap" (mis. "Shintia Maharani"),
    // supaya tidak perlu hafal dua nama yang berbeda.
    $stmt = $koneksi->prepare("SELECT * FROM tb_user WHERE username = ? OR nama_lengkap = ? LIMIT 1");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $query = $stmt->get_result();

    // Cek apakah username ditemukan
    if ($query && $query->num_rows > 0) {

        $user = $query->fetch_assoc();

        // Cek status akun (kolomnya status_aktif: 1 = aktif, 0 = nonaktif)
        if ((int) $user['status_aktif'] !== 1) {

            echo "<script>
                    alert('Akun tidak aktif!');
                    window.location='../view/login.php';
                  </script>";
            exit;
        }

        // Cek password.
        // Password yang baru dibuat lewat form (password_hash) berformat $2y$/$2a$/$2b$/argon2.
        // Data awal (seed) di database masih teks biasa, jadi tetap dicek langsung
        // supaya akun contoh (andi/budi/dst dengan password 123456) tetap bisa login.
        if (preg_match('/^\$(2y|2a|2b|argon2i|argon2id)\$/', $user['password'])) {
            $passwordCocok = password_verify($password, $user['password']);
        } else {
            $passwordCocok = hash_equals($user['password'], $password);
        }

        if ($passwordCocok) {

            // Membuat session login
            $_SESSION['login']    = true;
            $_SESSION['id_user']  = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama']     = $user['nama_lengkap'];
            $_SESSION['role']     = $user['role'];

            // Catat ke log aktivitas
            catat_log($koneksi, $user['id_user'], 'Login ke sistem');

            // Mengarahkan ke dashboard.
            // Owner punya dashboard sendiri (hanya berisi Riwayat & Rekap Riwayat),
            // admin dan petugas tetap memakai DashBoard.php.
            if ($user['role'] === 'owner') {
                header("Location: ../view/DashBoard_Owner.php");
                exit;
            } elseif (in_array($user['role'], ['admin', 'petugas'], true)) {
                header("Location: ../view/DashBoard.php");
                exit;
            } else {
                echo "<script>
                        alert('Role user tidak dikenali!');
                        window.location='../view/login.php';
                      </script>";
                exit;
            }

        } else {

            echo "<script>
                    alert('Username atau password salah!');
                    window.location='../view/login.php';
                  </script>";
            exit;
        }

    } else {

        echo "<script>
                alert('Username atau password salah!');
                window.location='../view/login.php';
              </script>";
        exit;
    }

} else {

    // Jika file c_login.php dibuka langsung tanpa submit form
    header("Location: ../view/login.php");
    exit;
}
