<?php
include_once __DIR__ . '/koneksi.php';

class M_Transaksi {
    private $db;

    // Inisialisasi koneksi database
    public function __construct($koneksi) {
        $this->db = $koneksi;
    }

    /* ==========================================================
       DATA UTAMA TRANSAKSI
       ========================================================== */

    // Ambil semua transaksi, sudah di-JOIN supaya plat nomor, jenis
    // kendaraan, nama area, dan nama petugas langsung ikut tampil
    // (tabel aslinya cuma menyimpan ID, bukan nama).
    public function tampil_semua() {
        $result = [];
        $sql = "SELECT t.*,
                       k.plat_nomor, k.jenis_kendaraan,
                       a.nama_area,
                       u.nama_lengkap AS nama_petugas
                FROM transaksi_parkir t
                LEFT JOIN tb_kendaraan   k ON k.id_kendaraan = t.id_kendaraan
                LEFT JOIN tb_area_parkir a ON a.id_area = t.id_area
                LEFT JOIN tb_user        u ON u.id_user = t.id_user
                ORDER BY t.id_parkir DESC";
        $query = mysqli_query($this->db, $sql);

        if ($query && mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_object($query)) {
                $result[] = $data;
            }
        }
        return $result;
    }

    // Ambil transaksi yang kendaraannya MASIH DI DALAM (status "masuk").
    // Dipakai untuk halaman Data Parkir — kendaraan yang sudah keluar
    // tidak tampil di sini, tapi pindah ke Riwayat (lihat tampil_riwayat).
    public function tampil_masuk() {
        $result = [];
        $sql = "SELECT t.*,
                       k.plat_nomor, k.jenis_kendaraan,
                       a.nama_area,
                       u.nama_lengkap AS nama_petugas
                FROM transaksi_parkir t
                LEFT JOIN tb_kendaraan   k ON k.id_kendaraan = t.id_kendaraan
                LEFT JOIN tb_area_parkir a ON a.id_area = t.id_area
                LEFT JOIN tb_user        u ON u.id_user = t.id_user
                WHERE LOWER(t.status) = 'masuk'
                ORDER BY t.id_parkir DESC";
        $query = mysqli_query($this->db, $sql);

        if ($query && mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_object($query)) {
                $result[] = $data;
            }
        }
        return $result;
    }

    // Ambil transaksi yang kendaraannya SUDAH KELUAR (status "keluar").
    // Dipakai untuk halaman Riwayat; yang terbaru keluar tampil paling atas.
    public function tampil_riwayat() {
        $result = [];
        $sql = "SELECT t.*,
                       k.plat_nomor, k.jenis_kendaraan,
                       a.nama_area,
                       u.nama_lengkap AS nama_petugas
                FROM transaksi_parkir t
                LEFT JOIN tb_kendaraan   k ON k.id_kendaraan = t.id_kendaraan
                LEFT JOIN tb_area_parkir a ON a.id_area = t.id_area
                LEFT JOIN tb_user        u ON u.id_user = t.id_user
                WHERE LOWER(t.status) = 'keluar'
                ORDER BY t.waktu_keluar DESC, t.id_parkir DESC";
        $query = mysqli_query($this->db, $sql);

        if ($query && mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_object($query)) {
                $result[] = $data;
            }
        }
        return $result;
    }

    // Cari satu transaksi berdasarkan ID (untuk form edit)
    public function tampil_data_by_id($id_parkir) {
        $sql = "SELECT * FROM transaksi_parkir WHERE id_parkir = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_parkir);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_object();
        $stmt->close();
        return $data;
    }

    // Ambil satu transaksi (sudah di-JOIN dengan kendaraan, area, petugas,
    // dan tarif) khusus untuk kebutuhan cetak struk parkir.
    public function tampil_struk_by_id($id_parkir) {
        $sql = "SELECT t.*,
                       k.plat_nomor, k.jenis_kendaraan,
                       a.nama_area,
                       u.nama_lengkap AS nama_petugas,
                       tr.tarif_per_jam
                FROM transaksi_parkir t
                LEFT JOIN tb_kendaraan   k  ON k.id_kendaraan = t.id_kendaraan
                LEFT JOIN tb_area_parkir a  ON a.id_area = t.id_area
                LEFT JOIN tb_user        u  ON u.id_user = t.id_user
                LEFT JOIN tb_tarif       tr ON tr.id_tarif = t.id_tarif
                WHERE t.id_parkir = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_parkir);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_object();
        $stmt->close();
        return $data;
    }

    // Tambah transaksi baru
    public function tambah_data($data) {
        $sql = "INSERT INTO transaksi_parkir
                    (id_kendaraan, waktu_masuk, waktu_keluar, id_tarif, durasi_jam, biaya_total, status, id_user, id_area)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(
            "issiiisii",
            $data['id_kendaraan'],
            $data['waktu_masuk'],
            $data['waktu_keluar'],
            $data['id_tarif'],
            $data['durasi_jam'],
            $data['biaya_total'],
            $data['status'],
            $data['id_user'],
            $data['id_area']
        );
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    // Ubah transaksi yang sudah ada
    public function ubah_data($id_parkir, $data) {
        $sql = "UPDATE transaksi_parkir SET
                    id_kendaraan = ?,
                    waktu_masuk  = ?,
                    waktu_keluar = ?,
                    id_tarif     = ?,
                    durasi_jam   = ?,
                    biaya_total  = ?,
                    status       = ?,
                    id_user      = ?,
                    id_area      = ?
                WHERE id_parkir = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(
            "issiiisiii",
            $data['id_kendaraan'],
            $data['waktu_masuk'],
            $data['waktu_keluar'],
            $data['id_tarif'],
            $data['durasi_jam'],
            $data['biaya_total'],
            $data['status'],
            $data['id_user'],
            $data['id_area'],
            $id_parkir
        );
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    // Hapus transaksi
    public function hapus_data($id_parkir) {
        $sql = "DELETE FROM transaksi_parkir WHERE id_parkir = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_parkir);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    /* ==========================================================
       DATA PENDUKUNG UNTUK DROPDOWN FORM
       (kendaraan, area, tarif, petugas dipilih dari daftar yang
       sudah ada di database, bukan diketik manual, supaya ID yang
       tersimpan selalu valid / tidak salah ketik).
       ========================================================== */

    public function ambil_kendaraan() {
        $result = [];
        $sql = "SELECT * FROM tb_kendaraan ORDER BY plat_nomor ASC";
        $query = mysqli_query($this->db, $sql);
        if ($query) {
            while ($data = mysqli_fetch_object($query)) {
                $result[] = $data;
            }
        }
        return $result;
    }

    public function ambil_area() {
        $result = [];
        $sql = "SELECT * FROM tb_area_parkir
                WHERE nama_area IN ('Gedung 1', 'Gedung 2')
                ORDER BY id_area ASC";
        $query = mysqli_query($this->db, $sql);
        if ($query) {
            while ($data = mysqli_fetch_object($query)) {
                $result[] = $data;
            }
        }
        return $result;
    }

    // Cari tarif otomatis berdasarkan jenis kendaraan yang dipilih
    // (mobil -> 5000/jam, motor -> 2000/jam sesuai isi tb_tarif).
    public function tampil_tarif_by_kendaraan($id_kendaraan) {
        $sql = "SELECT tr.*
                FROM tb_kendaraan k
                JOIN tb_tarif tr ON tr.jenis_kendaraan = k.jenis_kendaraan
                WHERE k.id_kendaraan = ?
                ORDER BY tr.id_tarif ASC
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_kendaraan);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_object();
        $stmt->close();
        return $data;
    }

    public function ambil_tarif() {
        $result = [];
        $sql = "SELECT * FROM tb_tarif
                WHERE (jenis_kendaraan = 'motor' AND tarif_per_jam = 2000)
                   OR (jenis_kendaraan = 'mobil' AND tarif_per_jam = 5000)
                ORDER BY tarif_per_jam ASC";
        $query = mysqli_query($this->db, $sql);
        if ($query) {
            while ($data = mysqli_fetch_object($query)) {
                $result[] = $data;
            }
        }
        return $result;
    }

    public function ambil_user() {
        $result = [];
        $sql = "SELECT * FROM tb_user WHERE status_aktif = 1 ORDER BY nama_lengkap ASC";
        $query = mysqli_query($this->db, $sql);
        if ($query) {
            while ($data = mysqli_fetch_object($query)) {
                $result[] = $data;
            }
        }
        return $result;
    }

    // Ambil riwayat untuk halaman Rekap Riwayat/Laporan, dengan filter
    // opsional: rentang tanggal keluar dan jenis kendaraan. Filter yang
    // dikosongkan (string kosong) diabaikan.
    public function tampil_laporan($tgl_mulai = '', $tgl_akhir = '', $jenis = '') {
        $result = [];
        $sql = "SELECT t.*,
                       k.plat_nomor, k.jenis_kendaraan,
                       a.nama_area
                FROM transaksi_parkir t
                LEFT JOIN tb_kendaraan   k ON k.id_kendaraan = t.id_kendaraan
                LEFT JOIN tb_area_parkir a ON a.id_area = t.id_area
                WHERE LOWER(t.status) = 'keluar'";
        $types  = '';
        $params = [];

        if ($tgl_mulai !== '') {
            $sql   .= " AND DATE(t.waktu_keluar) >= ?";
            $types .= 's';
            $params[] = $tgl_mulai;
        }
        if ($tgl_akhir !== '') {
            $sql   .= " AND DATE(t.waktu_keluar) <= ?";
            $types .= 's';
            $params[] = $tgl_akhir;
        }
        if ($jenis !== '') {
            $sql   .= " AND LOWER(k.jenis_kendaraan) = ?";
            $types .= 's';
            $params[] = strtolower($jenis);
        }
        $sql .= " ORDER BY t.waktu_keluar DESC, t.id_parkir DESC";

        $stmt = $this->db->prepare($sql);
        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $query = $stmt->get_result();
        while ($data = mysqli_fetch_object($query)) {
            $result[] = $data;
        }
        $stmt->close();
        return $result;
    }

    // Ambil satu tarif (dipakai untuk menghitung biaya_total otomatis)
    public function tampil_tarif_by_id($id_tarif) {
        $sql = "SELECT * FROM tb_tarif WHERE id_tarif = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_tarif);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_object();
        $stmt->close();
        return $data;
    }
}
