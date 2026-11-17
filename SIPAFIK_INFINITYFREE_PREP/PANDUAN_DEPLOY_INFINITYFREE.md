# PANDUAN DEPLOY SIPAFIK KE INFINITYFREE

## Status
Paket ini disiapkan khusus untuk struktur InfinityFree. Belum dapat diuji pada akun hosting karena akun/domain belum dibuat.

## 1. Struktur upload
1. Buat akun dan website di InfinityFree.
2. Buka File Manager, masuk ke folder `htdocs`.
3. Upload isi paket ini ke `htdocs` (bukan membuat folder bertingkat `SIPAFIK_INFINITYFREE_PREP`).
4. Pastikan `.htaccess` di root `htdocs` ikut ter-upload. Aktifkan opsi tampilkan hidden files jika perlu.
5. Jangan menghapus `public/.htaccess`.

Root `.htaccess` mengarahkan request ke `public/` dan memetakan `/storage/...` ke `storage/app/public/...` karena InfinityFree tidak menyediakan symlink Laravel.

## 2. Database
1. Buat database MySQL di panel InfinityFree.
2. Catat MySQL Hostname, Database Name, Username, dan Password dari panel.
3. Buka phpMyAdmin database yang baru dibuat.
4. Import `DOKUMENTASI_FINAL/hosting/sipafik_db_final.sql`.
5. Import hanya ke database BARU/KOSONG. Dump ini berisi DROP TABLE dan akan mengganti tabel yang namanya sama.

## 3. File .env
1. Buat file bernama `.env` di root `htdocs`.
2. Salin isi `.env.production.example` ke `.env`.
3. Isi APP_KEY baru, domain, serta empat nilai database dari panel.
4. Buat APP_KEY baru di komputer lokal, dari folder proyek yang memiliki `artisan`:
   `php artisan key:generate --show`
5. Tempel nilai `base64:...` ke `APP_KEY` di `.env`.
6. Pastikan `APP_DEBUG=false`, `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync`.
7. Setelah HTTPS/SSL aktif, ubah `APP_URL` menjadi domain HTTPS, lalu `APP_FORCE_HTTPS=true` dan `SESSION_SECURE_COOKIE=true`.

## 4. Folder yang harus writable
Pastikan PHP dapat menulis ke:
- `storage/`
- `bootstrap/cache/`
- `storage/framework/sessions/`
- `storage/framework/views/`
- `storage/logs/`

## 5. Akun demo
Gunakan email login dari `DOKUMENTASI_FINAL/PANDUAN_LOGIN_DEMO.md`. Untuk pengujian dashboard, gunakan akun Kampus Demo Aceh Utara yang berstatus approved. Ganti password Super Admin setelah aplikasi online dan sebelum tautan dibagikan secara luas.

## 6. Batasan InfinityFree
- Tidak ada SSH/terminal untuk menjalankan Artisan di server.
- Tidak dapat menjalankan queue worker, scheduler/cron Laravel, atau WebSocket.
- Tidak mendukung `php artisan storage:link`; aturan rewrite root sudah disiapkan untuk dokumen pada public disk.
- Host database bukan `localhost`; gunakan hostname yang ditampilkan di panel.
- Uji seluruh form upload dan dokumen setelah online.

## 7. Pengujian setelah upload
- Buka `/` dan pastikan landing page tampil.
- Buka `/login` dan `/super-admin/login`.
- Login dengan akun demo, cek dashboard dan menu, lalu logout.
- Coba akses `/.env`, `/vendor/autoload.php`, `/storage/logs/laravel.log`; semuanya harus 404/tidak menampilkan isi file.
- Coba buka dokumen dummy dari panel Super Admin untuk memastikan rewrite `/storage/` bekerja.
- Coba dari desktop dan ponsel.

Jika halaman 500, jangan aktifkan APP_DEBUG pada situs publik. Gunakan opsi Display Errors sementara dari panel PHP bila tersedia, catat pesan error, lalu matikan kembali.

## Melihat tampilan sebelum hosting (opsional, Laragon)
1. Buat database lokal `sipafik_preview`, lalu import SQL final yang sama.
2. Salin `.env.preview.example` menjadi `.env`.
3. Jalankan `php artisan key:generate` dan `php artisan serve` dari folder proyek.
4. Buka `http://127.0.0.1:8000`.
