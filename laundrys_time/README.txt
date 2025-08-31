# Laundry's Time - Aplikasi Manajemen Laundry Berbasis Web

Selamat datang di **Laundry's Time**, sebuah aplikasi manajemen laundry berbasis web yang dirancang untuk menyederhanakan proses pemesanan dan pengelolaan laundry. Aplikasi ini dibangun dari dasar menggunakan PHP Native, menunjukkan implementasi fundamental dari operasi CRUD (Create, Read, Update, Delete), manajemen sesi, dan sistem hak akses pengguna.

Aplikasi ini memiliki dua peran utama: **Admin**, yang mengelola seluruh operasional dari sisi backend, dan **User** (Pelanggan), yang dapat memesan layanan dan melacak status cucian mereka.

## Fitur Utama

Aplikasi ini mencakup serangkaian fitur yang komprehensif untuk memenuhi kebutuhan dasar sistem manajemen laundry.

### Fitur untuk Pelanggan (User)

* **Pendaftaran & Login**: Pengguna dapat membuat akun baru dan masuk ke sistem dengan aman. Proses ini mencakup validasi input dan hashing password untuk keamanan.
* **Dashboard Pengguna**: Setelah login, pengguna akan disambut di dashboard pribadi yang menampilkan riwayat semua pesanan mereka.
* **Pemesanan Online**: Pengguna dapat dengan mudah membuat pesanan laundry baru melalui formulir pemesanan, memilih jenis layanan, memasukkan berat cucian, dan menambahkan catatan khusus.
* **Detail & Pelacakan Pesanan**: Setiap pesanan memiliki halaman detail di mana pengguna dapat melihat rincian lengkap serta melacak riwayat status pesanan, mulai dari "Proses", "Selesai", hingga "Diambil".
* **Manajemen Profil**: Pengguna dapat melihat informasi akun mereka dan memiliki opsi untuk mengubah password mereka demi keamanan.

### Fitur untuk Administrator (Admin)

* **Dashboard Admin**: Halaman utama untuk admin yang menyajikan ringkasan statistik bisnis, seperti total pesanan, jumlah pesanan yang sedang diproses, dan total pendapatan.
* **Manajemen Pesanan**: Admin memiliki kontrol penuh atas semua pesanan yang masuk. Mereka dapat melihat, mencari, mengedit detail (layanan, berat), menghapus, dan yang terpenting, memperbarui status setiap pesanan.
* **Manajemen Layanan**: Admin dapat menambah, mengedit, atau menghapus jenis layanan laundry yang ditawarkan beserta harganya per kilogram.
* **Manajemen Pengguna**: Admin dapat melihat daftar semua pengguna yang terdaftar dalam sistem dan dapat menghapus akun pengguna jika diperlukan.
* **Laporan Transaksi**: Fitur untuk menghasilkan laporan transaksi berdasarkan rentang tanggal tertentu. Laporan ini mencakup total pesanan dan total pendapatan selama periode tersebut, serta daftar rinci transaksi.

## Tools dan Teknologi

Proyek ini dibangun menggunakan teknologi web standar dan fundamental, fokus pada fungsionalitas sisi server dengan PHP.

* **Backend**: **PHP 8+ (Native)**. Semua logika bisnis, interaksi database, dan manajemen sesi ditangani oleh skrip PHP.
* **Database**: **MySQL / MariaDB**. Digunakan untuk menyimpan semua data aplikasi, termasuk pengguna, layanan, pesanan, dan log status.
* **Frontend**: **HTML5** dan **CSS3**. Antarmuka pengguna dirancang untuk fungsionalitas, dengan tata letak yang bersih dan responsif untuk berbagai ukuran layar.
* **JavaScript**: Digunakan secara minimal untuk meningkatkan pengalaman pengguna, seperti menampilkan atau menyembunyikan password dan memberikan feedback visual saat form disubmit.
* **Web Server**: **Apache** (biasanya melalui paket XAMPP untuk pengembangan lokal).

## Cara Menjalankan Aplikasi

Ikuti langkah-langkah berikut untuk menjalankan aplikasi ini di lingkungan pengembangan lokal Anda.

1.  **Pindahkan Folder Proyek**
    Salin atau pindahkan seluruh folder `laundrys_time` ke dalam direktori `htdocs` dari instalasi XAMPP Anda. Lokasi umumnya adalah `C:\xampp\htdocs\`.

2.  **Jalankan Layanan XAMPP**
    Buka *XAMPP Control Panel* dan klik tombol **Start** untuk modul **Apache** dan **MySQL**.

3.  **Setup Database**
    - Buka browser Anda (Chrome, Firefox, dll.) dan navigasikan ke `http://localhost/phpmyadmin`.
    - Buat database baru dengan nama `laundry_db`.
    - Pilih database `laundry_db` yang baru saja Anda buat, lalu buka tab **Import**.
    - Klik "Choose File" dan pilih file `laundry_db.sql` yang ada di dalam folder proyek.
    - Gulir ke bawah dan klik **Go** (atau **Kirim**). Proses ini akan secara otomatis membuat semua tabel (`orders`, `services`, `status_logs`, `users`) dan memasukkan data awal, termasuk layanan dan akun dummy.

4.  **Akses Aplikasi**
    Setelah database berhasil diimpor, buka tab baru di browser Anda dan akses `http://localhost/laundrys_time/`. Halaman utama aplikasi akan ditampilkan.

## Akun Dummy untuk Pengujian

Untuk memudahkan pengujian, Anda bisa langsung login menggunakan akun yang sudah disediakan:

-   **Admin**
    -   **Email**: `admin@laundry.com`
    -   **Password**: `admin123`

-   **User (Pelanggan)**
    -   **Email**: `user@laundry.com`

    -   **Password**: `user123`
