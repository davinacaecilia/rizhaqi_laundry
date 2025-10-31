# Rizhaqi Laundry Website

Kelompok 7 :
- **Putri Sahara Tampubolon** - 241402015 - *Frontend*
- **Anggun Dwikasih Mahrani** - 241402018 - *Frontend*
- **Dhaifina Raiqa Zahira** - 241402036 - *Backend*
- **Davina Caecilia Marpaung** - 241402076 - *Backend*
- **Jelita Amy Syafira Rangkuti** - 241402114 - *Frontend*

## Description

Rizhaqi Laundry, yang berlokasi di Blok A Jl. Taman Setia Budi Indah No. 49B (Simpang Jl. Perjuangan), Tanjung Rejo, Kec. Medan Sunggal, Kota Medan, Sumatera Utara 20122, merupakan salah satu usaha laundry yang sudah berjalan cukup lama dan memiliki pelanggan tetap. Seluruh proses administrasi di Rizhaqi Laundry masih dilakukan secara manual menggunakan buku tulis atau nota kertas. Seiring meningkatnya jumlah pesanan, metode ini tidak lagi efektif dan mulai menimbulkan berbagai masalah.

Website Rizhaqi Laundry ini dibuat dengan harapan dapat menyelesaikan permasalahan tersebut. Website ini menyediakan fitur yang berbeda untuk pelanggan dan pemilik/pegawai.

1. **Pelanggan**
- **Pengecekan Status Order** : Pelanggan dapat memantau dan mengecek status order secara *real-time* melalui website dengan menggunakan kode invoice order

2. **Pemilik**
- **Input Data Transaksi, Pegawai, Pelanggan, Layanan**
- **Update Status Order, Status Keaktifan Pegawai, Data Pelanggan dan Layanan**
- **Print Invoice** : Pemilik dapat mencetak invoice transaksi yang telah dilakukan
- **Generate Laporan Harian** : Pemilik dapat dengan mudah mencetak laporan harian yang dibutuhkan (laporan cucian masuk, laporan uang masuk, laporan uang keluar, laporan cuci harian pegawai) secara otomatis tanpa harus melakukan rekap data secara manual
- **View Log** : Pemilik dapat memantau aktivitas yang berhubungan dengan database (INSERT, UPDATE, DELETE)
  
3. **Pegawai**
- **Update Status Order**
- **Generate Laporan Cucian Harian** : Pegawai dapat mencetak laporan cucian harian pegawai yang dibutuhkan untuk menghitung gaji harian

## Tech Stack

- **Composer v2.8.6** Package manager PHP (untuk membuat project laravel)
- **Laravel v12.14.1** sebagai framework PHP untuk membangun aplikasi web
- **PHP v8.4.4** sebagai bahasa pemrograman
- **MySQL v15.1** sebagai database
- **XAMPP v3.3.0** sebagai server
- **HTML, CSS, dan JavaScript** untuk membuat tampilan dan interaktivitas website

### Installing

Untuk menjalankan web ini, langkah-langkah yang perlu dilakukan adalah sebagai berikut :

Clone repository ini ke dalam direktori yang diinginkan

    git clone https://github.com/davinacaecilia/rizhaqi_laundry.git

Masuk ke dalam folder proyek

    cd rizhaqi_laundry

Install dependensi PHP melalui Composer, pastikan Composer sudah terinstall

    composer install

Jika Composer belum diinstall : https://getcomposer.org/download/

Buat salinan file .env.example menjadi .env

    cp .env.example .env

Generate App Key

    php artisan key:generate

Atur file .env sesuai dengan konfigurasi database lokalmu

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nama_database
    DB_USERNAME=root
    DB_PASSWORD=

Jalankan migrasi database

    php artisan migrate

Jalankan server

    php artisan serve
