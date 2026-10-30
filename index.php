<?php
// Pintu masuk aplikasi: http://localhost/parkir_ikea/
// Arahkan ke halaman yang sesuai dengan status login & role.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['login'])) {
    header("Location: view/login.php");
    exit;
}

if (in_array($_SESSION['role'] ?? '', ['admin', 'petugas'], true)) {
    header("Location: view/DashBoard.php");
} else {
    header("Location: view/DashBoard_Owner.php");
}
exit;
