# rendang-bps

Repositori **rendang-bps** adalah aplikasi manajemen naskah berbasis web yang dikembangkan menggunakan framework Laravel (PHP). Proyek ini digunakan untuk mengelola proses administrasi dokumen/naskah di lingkungan BPS (Badan Pusat Statistik), khususnya terkait pengelolaan, persetujuan, revisi, hingga rilis naskah/publikasi.

## Fitur Utama

-   **Manajemen Naskah:** CRUD data naskah, pengunggahan file (PDF, DOCX, XLSX, PPTX), pengaturan status (Terkirim, Perlu Revisi, Revisi Diajukan, Menunggu Rilis, Rilis).
-   **Statistik Visual Naskah:** Widget statistik (NaskahChart) pada halaman daftar naskah yang menampilkan jumlah naskah berdasarkan status (Belum Ditanggapi, Dalam Review, Disetujui, Ditolak) secara real-time.
-   **Role & Permission:** Dukungan multi-role dengan otorisasi berbasis Laravel Filament Shield (contoh: super admin, kabkot).
-   **Workflow Persetujuan:** Proses revisi dan persetujuan naskah antara BPS Kota dan BPS Provinsi, termasuk logging aktivitas pengguna.
-   **Histori Aktivitas:** Riwayat penolakan, revisi, dan rilis naskah terdokumentasi dengan baik.
-   **Notifikasi:** Sistem pemberitahuan untuk perubahan status naskah.
-   **Manajemen Profil:** Edit profil dan password pengguna melalui plugin Filament Edit Profile.

## Teknologi yang Digunakan

-   **Backend:** PHP 8+, Laravel
-   **Admin Panel:** Filament (Laravel Admin Panel)
-   **Authorization:** Filament Shield
-   **Database:** MySQL/MariaDB (disarankan)
-   **Frontend:** Blade, Filament UI Components

## Instalasi

1. Clone repositori ini:

    ```bash
    git clone https://github.com/Ray123fa/rendang-bps.git
    cd rendang-bps
    ```

2. Install dependensi PHP:

    ```bash
    composer install
    ```

3. Salin file environment:

    ```bash
    cp .env.example .env
    ```

4. Atur konfigurasi database di file `.env`.

5. Generate key aplikasi:

    ```bash
    php artisan key:generate
    ```

6. Jalankan migrasi dan seeder (untuk role & permission):

    ```bash
    php artisan migrate --seed
    ```

7. Jalankan server lokal:
    ```bash
    php artisan serve
    ```

## Penggunaan

-   Akses admin panel melalui `/u` (atau sesuai route Filament).
-   Setelah login, Anda dapat melihat widget statistik naskah di halaman daftar naskah (Daftar Naskah) yang menampilkan jumlah naskah berdasarkan status.
-   Login menggunakan akun yang telah dibuat (default superadmin jika ada di seeder).
-   Kelola naskah, proses revisi, dan rilis sesuai dengan hak akses masing-masing role.

## Kontribusi

Kontribusi sangat terbuka untuk pengembangan lebih lanjut. Silakan fork repositori, buat branch fitur/fix baru, dan ajukan pull request.  
Laporkan bug atau permintaan fitur melalui fitur Issue di GitHub.

## Lisensi

MIT License  
(C) 2025 Muhammad Rayhan Faridh
