<?php
// Helper pencatat & pembaca log aktivitas (tabel tb_log_aktivitas).
require_once __DIR__ . '/koneksi.php';

if (!function_exists('catat_log')) {
    /**
     * Mencatat satu aktivitas ke tb_log_aktivitas.
     * Gagal mencatat log tidak boleh mengganggu proses utama, jadi error diabaikan.
     */
    function catat_log($db, $id_user, string $aktivitas): void {
        try {
            $aktivitas = mb_substr($aktivitas, 0, 100);
            $id_user   = $id_user ? (int) $id_user : null;
            $waktu     = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d H:i:s');
            $stmt = $db->prepare("INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu_aktivitas) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $id_user, $aktivitas, $waktu);
            $stmt->execute();
        } catch (Throwable $e) {
            // sengaja diabaikan
        }
    }
}

if (!function_exists('ambil_log_terbaru')) {
    /** Mengambil log terbaru beserta nama & role user. */
    function ambil_log_terbaru($db, int $limit = 10): array {
        $limit = max(1, $limit);
        $q = mysqli_query($db,
            "SELECT l.id_log, l.aktivitas, l.waktu_aktivitas,
                    COALESCE(u.nama_lengkap, '(user dihapus)') AS nama_lengkap,
                    COALESCE(u.role, '-') AS role
             FROM   tb_log_aktivitas l
             LEFT JOIN tb_user u ON u.id_user = l.id_user
             ORDER  BY l.waktu_aktivitas DESC, l.id_log DESC
             LIMIT  $limit");
        $rows = [];
        while ($q && ($r = mysqli_fetch_assoc($q))) $rows[] = $r;
        return $rows;
    }
}

if (!function_exists('hitung_log')) {
    /** Menghitung total log (opsional dengan kata kunci pencarian). */
    function hitung_log($db, string $cari = ''): int {
        $like = '%' . $cari . '%';
        $stmt = $db->prepare(
            "SELECT COUNT(*) AS n
             FROM   tb_log_aktivitas l
             LEFT JOIN tb_user u ON u.id_user = l.id_user
             WHERE  l.aktivitas LIKE ? OR u.nama_lengkap LIKE ? OR u.role LIKE ?");
        $stmt->bind_param("sss", $like, $like, $like);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int) ($row['n'] ?? 0);
    }
}

if (!function_exists('ambil_semua_log')) {
    /** Mengambil log dengan pencarian + paginasi. */
    function ambil_semua_log($db, string $cari = '', int $limit = 20, int $offset = 0): array {
        $like   = '%' . $cari . '%';
        $limit  = max(1, $limit);
        $offset = max(0, $offset);
        $stmt = $db->prepare(
            "SELECT l.id_log, l.aktivitas, l.waktu_aktivitas,
                    COALESCE(u.nama_lengkap, '(user dihapus)') AS nama_lengkap,
                    COALESCE(u.role, '-') AS role
             FROM   tb_log_aktivitas l
             LEFT JOIN tb_user u ON u.id_user = l.id_user
             WHERE  l.aktivitas LIKE ? OR u.nama_lengkap LIKE ? OR u.role LIKE ?
             ORDER  BY l.waktu_aktivitas DESC, l.id_log DESC
             LIMIT  ? OFFSET ?");
        $stmt->bind_param("sssii", $like, $like, $like, $limit, $offset);
        $stmt->execute();
        $res  = $stmt->get_result();
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        return $rows;
    }
}
