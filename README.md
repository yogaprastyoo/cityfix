# CityFix Bumi Sholawat

Aplikasi pelaporan dan penanganan kerusakan fasilitas, dibangun dengan **Laravel 12**.

Panduan ini ditulis untuk menjalankan CityFix di komputer sendiri (Windows + **XAMPP**). Secara bawaan aplikasi memakai database **SQLite**, jadi kamu **tidak perlu menyalakan MySQL** untuk mulai.

---

## 1. Yang Harus Dipasang

| Aplikasi | Versi | Unduh |
|---|---|---|
| XAMPP | PHP **8.2** atau lebih baru | https://www.apachefriends.org/download.html |
| Composer | terbaru | https://getcomposer.org/Composer-Setup.exe |
| Node.js | **20.19+** atau **22.12+** (pilih versi LTS) | https://nodejs.org |
| Git | terbaru | https://git-scm.com/download/win |

> ⚠️ PHP **8.1 ke bawah tidak bisa**. Jika XAMPP-mu masih versi lama, pasang ulang XAMPP versi 8.2.

---

## 2. Persiapan XAMPP (cukup sekali)

### 2.1 Daftarkan PHP XAMPP ke PATH

Supaya perintah `php` bisa dipakai di terminal:

1. Tekan tombol **Windows**, ketik **"environment variables"**, buka **Edit the system environment variables**.
2. Klik **Environment Variables...**
3. Di bagian **System variables**, pilih **Path** lalu klik **Edit**.
4. Klik **New**, isi `C:\xampp\php`, lalu **OK** di semua jendela.

### 2.2 Aktifkan ekstensi PHP

1. Buka **XAMPP Control Panel** → baris **Apache** → **Config** → **PHP (php.ini)**.
2. Cari baris-baris berikut (tekan `Ctrl + F`). Jika di depannya ada tanda titik koma `;`, **hapus** titik komanya:

   ```ini
   extension=curl
   extension=fileinfo
   extension=mbstring
   extension=openssl
   extension=pdo_sqlite
   extension=sqlite3
   extension=zip
   ```

   Contoh: `;extension=zip` diubah menjadi `extension=zip`.

3. Simpan file `php.ini`.

### 2.3 Pasang Composer

Jalankan `Composer-Setup.exe`. Saat ditanya lokasi PHP, pilih **`C:\xampp\php\php.exe`**.

### 2.4 Cek semuanya

**Tutup lalu buka lagi** terminal (Command Prompt / PowerShell / terminal VS Code), kemudian jalankan:

```sh
php -v
composer -V
node -v
npm -v
```

Semua perintah harus menampilkan nomor versi. `php -v` harus menunjukkan **PHP 8.2** atau lebih baru.

---

## 3. Instalasi Project

Jalankan perintah berikut **satu per satu** di terminal:

```sh
# 1. Ambil source code
git clone https://github.com/yogaprastyoo/cityfix.git
cd cityfix

# 2. Pasang library PHP
composer install

# 3. Buat file pengaturan
copy .env.example .env
php artisan key:generate

# 4. Buat database + data awal
#    Jika muncul pertanyaan "Would you like to create it?", pilih "yes"
php artisan migrate --seed

# 5. Hubungkan folder upload foto
php artisan storage:link

# 6. Pasang & build tampilan (CSS/JS)
npm install
npm run build
```

> Di PowerShell atau Git Bash, ganti `copy` dengan `cp`.

> Project **tidak perlu** ditaruh di folder `htdocs`. Simpan di folder mana saja, misalnya `D:\Projects\cityfix`.

---

## 4. Menjalankan Aplikasi

```sh
php artisan serve
```

Buka browser ke **http://localhost:8000**.

Biarkan terminal tetap terbuka selama aplikasi dipakai. Untuk berhenti, tekan `Ctrl + C`.

### Akun Demo

| Peran | Email | Password |
|---|---|---|
| Admin | `admin@cityfix.local` | `admin123` |
| Verifikator | `verifier@cityfix.local` | `password` |
| Teknisi | `technician@cityfix.local` | `password` |
| Pelapor | `reporter@cityfix.local` | `password` |

### Sedang mengubah tampilan?

Jika kamu mengedit file di `resources/` dan ingin perubahan langsung terlihat, buka **terminal kedua** dan jalankan:

```sh
npm run dev
```

Setelah selesai mengedit, jalankan `npm run build` sekali lagi.

---

## 5. (Opsional) Memakai MySQL dari XAMPP

SQLite sudah cukup untuk belajar. Jika ingin memakai MySQL:

1. Di **XAMPP Control Panel**, klik **Start** pada **MySQL**.
2. Buka http://localhost/phpmyadmin, buat database baru bernama **`cityfix`**.
3. Di `php.ini` (lihat langkah 2.2), pastikan `extension=pdo_mysql` aktif.
4. Buka file `.env`, ubah bagian database menjadi:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cityfix
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Jalankan ulang migrasi:

   ```sh
   php artisan config:clear
   php artisan migrate --seed
   ```

---

## 6. Menjalankan Tes

```sh
php artisan test
```

Tes selalu memakai SQLite di memori, jadi tidak mengganggu data aplikasimu dan tidak butuh MySQL.

---

## 7. Mengatasi Masalah Umum

| Pesan error | Penyebab | Solusi |
|---|---|---|
| `'php' is not recognized...` | PHP belum ada di PATH | Ulangi langkah 2.1, lalu buka terminal baru |
| `'composer' is not recognized...` | Composer belum terpasang | Ulangi langkah 2.3, lalu buka terminal baru |
| `Your requirements could not be resolved...` / `requires php >=8.2` | Versi PHP terlalu lama | Pasang XAMPP dengan PHP 8.2 |
| `The zip extension and unzip/7z commands are both missing` | Ekstensi `zip` mati | Aktifkan `extension=zip` (langkah 2.2) |
| `could not find driver (Connection: sqlite...)` | Ekstensi SQLite mati | Aktifkan `pdo_sqlite` dan `sqlite3` (langkah 2.2) |
| `could not find driver (Connection: mysql...)` | Ekstensi MySQL mati | Aktifkan `extension=pdo_mysql` |
| `SQLSTATE[HY000] [2002] No connection could be made...` | MySQL belum dinyalakan | Start MySQL di XAMPP Control Panel |
| `No application encryption key has been specified` | `APP_KEY` kosong | `php artisan key:generate` |
| `Vite manifest not found` | Tampilan belum di-build | `npm install` lalu `npm run build` |
| Foto laporan tidak muncul | Folder upload belum dihubungkan | `php artisan storage:link` |
| `npm` error soal versi Node | Node.js terlalu lama | Pasang Node.js LTS terbaru |
| `Failed to listen on 127.0.0.1:8000` | Port 8000 sudah dipakai | `php artisan serve --port=8001` |

Setelah mengubah `php.ini` atau `.env`, selalu **tutup dan buka lagi** terminal, lalu jalankan `php artisan config:clear`.

---

## Dokumen Lain

Dokumen desain, pengujian, dan operasional ada di folder [`CITYFIX/`](CITYFIX/).
