# Website Rizhaqi Laundry

Kelompok 7 :
- **Putri Sahara Tampubolon** - 241402015 - *Frontend*
- **Anggun Dwikasih Mahrani** - 241402018 - *Frontend*
- **Dhaifina Raiqa Zahira** - 241402036 - *Backend*
- **Davina Caecilia Marpaung** - 241402076 - *Backend*
- **Jelita Amy Syafira Rangkuti** - 241402114 - *Frontend*

## Description



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

    git clone https://github.com/davinacaecilia/BookScape.git

Masuk ke dalam folder proyek

    cd BookScape

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


### Running Website

Selanjutnya, Anda perlu membuat symbolic link (tautan simbolis) dari direktori storage/app/public ke direktori public/storage. Ini memungkinkan file yang disimpan di direktori storage/app/public dapat diakses secara publik melalui URL.

    php artisan storage:link
    
