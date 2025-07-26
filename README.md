# Lunneettez Online Accessories Store

**Lunneettez** adalah aplikasi web penjualan aksesoris online dengan tampilan modern, elegan, dan mudah digunakan. Platform ini memudahkan admin untuk mengelola kategori, produk, pesanan, laporan penjualan, serta menangani komplain pelanggan. Backend dibangun dengan Laravel 12, siap diintegrasikan dengan aplikasi mobile berbasis Flutter.

---

## ✨ Fitur Utama

-   **Manajemen Kategori**  
    Tambah, edit, dan hapus kategori produk aksesoris.

-   **Manajemen Produk**  
    CRUD produk lengkap dengan upload gambar, deskripsi, harga, stok, dan kategori.

-   **Keranjang Belanja**  
    Pengelolaan keranjang belanja untuk setiap user.

-   **Manajemen Pesanan**  
    Lihat, filter, dan update status pesanan (pending, paid, shipped, completed, cancelled).

-   **Laporan Penjualan & Produk**  
    Laporan penjualan dan produk terjual, tersedia juga dalam format PDF.

-   **Manajemen Komplain Pelanggan**  
    Admin dapat melihat, menanggapi, dan memproses komplain pelanggan terkait pesanan.

-   **Autentikasi Admin**  
    Login dan registrasi khusus admin, dengan sistem peran (admin/pelanggan).

---

## 🛠️ Teknologi

-   **Backend:** Laravel 12.x (PHP 8.2+)
-   **Frontend:** Blade Template, Bootstrap, FontAwesome
-   **PDF Export:** barryvdh/laravel-dompdf
-   **Database:** MySQL/MariaDB
-   **Autentikasi:** Laravel Auth
-   **API Ready:** Struktur siap diintegrasikan dengan aplikasi mobile (Flutter)

---

## 🚀 Instalasi & Menjalankan

1. **Clone repo ini**

    ```bash
    git clone https://github.com/username/Lunneettez.git
    cd Lunneettez
    ```

2. **Install dependency**

    ```bash
    composer install
    npm install
    ```

3. **Copy file environment & generate key**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Atur koneksi database**  
   Edit `.env` dan sesuaikan DB_DATABASE, DB_USERNAME, DB_PASSWORD.

5. **Migrasi & seeder database**

    ```bash
    php artisan migrate --seed
    ```

6. **Jalankan server**

    ```bash
    php artisan serve
    ```

7. **Akses aplikasi**
   Buka [http://localhost:8000](http://localhost:8000) di browser.

---

## 📊 Struktur Database (Singkat)

-   **users:** Data user/admin
-   **kategoris:** Kategori produk
-   **produks:** Data produk
-   **keranjangs, item_keranjangs:** Keranjang belanja
-   **pesanans, detail_pesanans:** Data pesanan & detail item
-   **complaints:** Komplain pelanggan

---

## 📂 Fitur Admin

-   Dashboard admin modern
-   Manajemen kategori & produk
-   Manajemen pesanan & status
-   Laporan penjualan & produk (PDF)
-   Manajemen komplain pelanggan

---

## 📄 Lisensi

MIT License

---

> _"Perancangan Aplikasi Penjualan Aksesoris Lunneettez Online Berbasis Flutter & Laravel API"_  
> — Tim Lunneettez

---

**Catatan:**  
Proyek ini dapat dikembangkan lebih lanjut untuk integrasi API mobile, pembayaran online, dan fitur pelanggan.
