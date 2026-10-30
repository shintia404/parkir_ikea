# 🚗 Sistem Parkir IKEA

Sistem Parkir IKEA adalah aplikasi berbasis web yang dibangun menggunakan **PHP Native dengan konsep OOP (Object Oriented Programming)** dan **arsitektur MVC (Model View Controller)** untuk membantu pengelolaan data parkir kendaraan secara efektif dan terstruktur.

## 📖 Deskripsi

Aplikasi ini digunakan untuk mengelola aktivitas parkir kendaraan mulai dari pencatatan kendaraan masuk, kendaraan keluar, pengelolaan data parkir, hingga penyajian laporan parkir. Sistem mendukung beberapa level pengguna dengan hak akses yang berbeda sesuai kebutuhan operasional.

---

## ✨ Fitur Utama

### Administrator
- Mengelola data pengguna
- Mengelola data kendaraan
- Mengelola data parkir
- Melihat laporan parkir
- Mengelola seluruh sistem

### Owner
- Melihat dashboard
- Melihat laporan parkir
- Monitoring aktivitas parkir

### Petugas
- Input kendaraan masuk
- Input kendaraan keluar
- Melihat data parkir aktif
- Mengelola transaksi parkir harian

---

## 🔐 Akun Demo

| Role | Username | Password |
|--------|----------|----------|
| Administrator | shintia | 3030 |
| Owner | alifa | 2424 |
| Petugas | wafina | 3131 |

> Gunakan akun sesuai dengan hak akses yang tersedia pada sistem.

---

## 🏗️ Arsitektur Sistem

Project ini menggunakan pola **MVC (Model View Controller)**.

```text
parkir_ikea/
│
├── app/
│   ├── controllers/
│   ├── models/
│   └── views/
│
├── config/
├── database/
├── assets/
├── public/
├── index.php
│
└── README.md
```

### Penjelasan MVC

#### Model
Bertanggung jawab untuk mengelola data dan proses yang berhubungan dengan database.

#### View
Bertanggung jawab untuk menampilkan antarmuka pengguna.

#### Controller
Bertanggung jawab menerima request pengguna, memprosesnya melalui model, lalu menampilkan hasil ke view.

---

## 🛠️ Teknologi yang Digunakan

- PHP Native
- Object Oriented Programming (OOP)
- MVC Architecture
- MySQL
- HTML5
- CSS3
- Bootstrap
- JavaScript

---

## ⚙️ Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/shintia404/parkir_ikea.git
```

### 2. Pindahkan Project

Simpan project pada folder web server.

Contoh menggunakan XAMPP:

```text
C:\xampp\htdocs\parkir_ikea
```

atau Laragon:

```text
C:\laragon\www\parkir_ikea
```

### 3. Buat Database

Buat database baru pada MySQL:

```sql
CREATE DATABASE parkir_ikea;
```

### 4. Import Database

Import file SQL yang tersedia pada folder database.

### 5. Konfigurasi Database

Sesuaikan konfigurasi koneksi database.

Contoh:

```php
private $host = "localhost";
private $user = "root";
private $pass = "";
private $db   = "parkir_ikea";
```

### 6. Jalankan Aplikasi

Akses melalui browser:

```text
http://localhost/parkir_ikea
```

---

## 📋 Hak Akses Pengguna

### Administrator

- Dashboard
- Kelola User
- Kelola Data Kendaraan
- Kelola Data Parkir
- Laporan
- Monitoring Sistem

### Owner

- Dashboard
- Laporan Parkir
- Monitoring Aktivitas

### Petugas

- Dashboard
- Kendaraan Masuk
- Kendaraan Keluar
- Data Parkir Aktif

---

## 📊 Alur Sistem

1. Petugas login ke sistem.
2. Petugas mencatat kendaraan masuk.
3. Sistem menyimpan data parkir aktif.
4. Saat kendaraan keluar, petugas memproses kendaraan keluar.
5. Sistem memperbarui status parkir.
6. Data tersimpan sebagai riwayat parkir.
7. Administrator dan Owner dapat melihat laporan parkir.

---

## 📸 Screenshot

### Dashboard

Tambahkan screenshot dashboard di sini.

### Data Kendaraan

Tambahkan screenshot data kendaraan di sini.

### Data Parkir

Tambahkan screenshot data parkir di sini.

### Laporan

Tambahkan screenshot laporan di sini.

---

## 👩‍💻 Developer

**Shintia**

GitHub: https://github.com/shintia404

---

## 📄 License

Project ini dibuat untuk keperluan pembelajaran, pengembangan, dan implementasi sistem informasi parkir berbasis web menggunakan PHP OOP dan MVC.
