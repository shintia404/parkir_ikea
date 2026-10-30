<?php
/**
 * Model Log Aktivitas
 * Mencatat setiap aksi penting user ke tabel tb_log_aktivitas.
 */

require_once __DIR__ . '/koneksi.php';

class M_Log_Aktivitas
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Catat satu baris log.
     *
     * @param int|null $id_user   ID user yang melakukan aksi (null = sistem)
     * @param string   $aktivitas Deskripsi singkat aktivitas
     */
    public function catat(int|null $id_user, string $aktivitas): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu_aktivitas)
             VALUES (?, ?, NOW())"
        );
        if ($stmt) {
            $stmt->bind_param("is", $id_user, $aktivitas);
            $stmt->execute();
            $stmt->close();
        }
    }

    /**
     * Ambil log terbaru beserta nama user.
     *
     * @param int $limit Jumlah baris yang diambil (default 15)
     * @return array
     */
    public function tampil_terbaru(int $limit = 15): array
    {
        $stmt = $this->db->prepare(
            "SELECT l.id_log,
                    COALESCE(u.nama_lengkap, 'Sistem') AS nama_user,
                    COALESCE(u.role, '-')               AS role_user,
                    l.aktivitas,
                    l.waktu_aktivitas
             FROM tb_log_aktivitas l
             LEFT JOIN tb_user u ON u.id_user = l.id_user
             ORDER BY l.waktu_aktivitas DESC
             LIMIT ?"
        );
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    /**
     * Hitung total baris log hari ini.
     */
    public function total_hari_ini(): int
    {
        $q = $this->db->query(
            "SELECT COUNT(*) AS n FROM tb_log_aktivitas
             WHERE DATE(waktu_aktivitas) = CURDATE()"
        );
        return (int)($q->fetch_assoc()['n'] ?? 0);
    }
}
