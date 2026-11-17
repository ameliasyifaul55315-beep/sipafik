# PANDUAN HOSTING SIPAFIK

> **Status jujur**: saya **tidak mengetahui penyedia hosting yang Anda pakai**, jadi saya **tidak dapat menyatakan hosting Anda mendukung SIPAFIK**. Aplikasi ini siap di-hosting *jika* hosting memenuhi syarat di bagian 1. Pastikan dengan `hosting/cek_server.php` sebelum mengunggah apa pun.

## 1. Syarat hosting (Laravel 12.71 / SIPAFIK)
| Kebutuhan | Minimal | Catatan |
|---|---|---|
| PHP | **8.2** (disarankan 8.3) | Hosting PHP 7.x/8.0/8.1 **tidak bisa** menjalankan Laravel 12 |
| Ekstensi PHP | ctype, curl, dom, fileinfo, filter, hash, mbstring, openssl, pcre, **pdo_mysql**, session, tokenizer, xml, json, bcmath | cek dengan `hosting/cek_server.php` |
| Database | MySQL 5.7+/8 atau MariaDB 10.3+ | Aplikasi diuji pada MariaDB 10.11 |
| Apache + `mod_rewrite` (atau Nginx dengan `try_files`) | wajib | `public/.htaccess` sudah ada |
| Document root | idealnya diarahkan ke folder `public/` | jika tidak bisa, pakai skema `hosting/htaccess_untuk_public_html.txt` |
| SSH / Composer | **Disarankan**; tidak wajib | tanpa SSH: unggah folder `vendor/` dan impor SQL via phpMyAdmin |
| SSL / HTTPS | sangat disarankan | cookie sesi aman |
| Upload | `upload_max_filesize` ≥ 2 MB | dokumen identitas kampus ≤ 2 MB |

Jalur yang cocok: **VPS** (paling mudah), **cPanel dengan Terminal/SSH**, atau **shared hosting cPanel tanpa SSH** (bisa, dengan jalur B di bawah). Hosting gratis yang membatasi versi PHP, ekstensi, atau melarang `symlink`/`.htaccess` perlu dicek dulu.

## 2. Backup SEBELUM deployment
* Lokal: salinan `sipafik_db.sql` asli Anda + ZIP asli sudah merupakan backup. Buat satu lagi dari DB Laragon Anda saat ini: `mysqldump -u root sipafik_db > backup_sebelum_hosting.sql` (atau Export di phpMyAdmin/HeidiSQL).
* Di server (jika sudah ada data): export database dari phpMyAdmin sebelum menjalankan `migrate`.

## 3. Menyiapkan berkas
1. Ekstrak `SIPAFIK_FINAL.zip`. **Jangan** unggah file `.env` lokal Anda (tidak disertakan di ZIP final).
2. Folder yang **tidak perlu** diunggah: `tests/`, `DOKUMENTASI_FINAL/` (boleh disimpan lokal), `node_modules/`.
3. Pertimbangkan dua jalur database (pilih **salah satu**):
   * **Jalur 1 — impor SQL final (disarankan untuk tampilan demo)**: `DOKUMENTASI_FINAL/hosting/sipafik_db_final.sql` berisi seluruh struktur + data lama + data demo. ⚠️ Berisi akun demo dengan password yang diketahui publik di dokumen ini → **ganti password Super Admin segera** (bagian 6).
   * **Jalur 2 — kosong/bersih (produksi sungguhan)**: buat DB kosong → `php artisan migrate --force` → `php artisan db:seed --class=SuperAdminSeeder --force` → langsung ganti password Super Admin. Tanpa data demo.

## 4. Jalur A — VPS / cPanel dengan SSH
```bash
# 1) unggah proyek ke ~/sipafik (di luar public_html), arahkan document root domain ke ~/sipafik/public
cd ~/sipafik
composer install --no-dev --optimize-autoloader        # jika vendor/ tidak diunggah
cp .env.production.example .env && nano .env            # isi domain & DB (bagian 5)
php artisan key:generate --force                        # APP_KEY BARU khusus server
php artisan migrate --force                             # Jalur 2; (Jalur 1: impor SQL dulu, lalu migrate = "Nothing to migrate" / 0 baru)
php artisan db:seed --class=SuperAdminSeeder --force    # Jalur 2 saja
php artisan storage:link
chmod -R ug+rwX storage bootstrap/cache
php artisan config:cache && php artisan route:cache && php artisan view:cache
```
> **Jangan** menjalankan `migrate:fresh`, `migrate:refresh`, `db:wipe`. Perintah `db:seed` tanpa `--class` aman (idempotent) tetapi akan menambah data demo; di produksi sungguhan jalankan hanya `SuperAdminSeeder`.

## 5. Jalur B — shared hosting tanpa SSH (cPanel)
1. **Buat database** (MySQL Databases): catat nama DB, user, password. Beri user semua hak (ALL PRIVILEGES).
2. **phpMyAdmin → Import** → pilih `sipafik_db_final.sql` (Jalur 1). Jika ukuran melebihi batas, unggah `.sql.zip`/gz.
3. **Unggah proyek** lewat File Manager/FTP ke `/home/USER/sipafik/` (ZIP lalu *Extract*; sertakan folder `vendor/`).
4. **Isi `public_html`** dengan isi folder `public/` (index.php, .htaccess, `sipafik/`, favicon.ico, robots.txt) dan ubah path di `public_html/index.php` seperti petunjuk `hosting/htaccess_untuk_public_html.txt`. Atau, bila panel mengizinkan, ubah *document root* ke `/home/USER/sipafik/public`.
5. **Buat `.env`** di `/home/USER/sipafik/.env` dari `.env.production.example`. `APP_KEY` wajib diisi: di komputer lokal jalankan `php artisan key:generate --show` dan salin hasilnya (`base64:…`) — **gunakan key baru, bukan key lokal lama**.
6. **Permission**: `storage/` dan `bootstrap/cache/` → 775 (rekursif).
7. **storage:link tanpa SSH**: buat `public_html/link.php` berisi
   `<?php symlink('/home/USER/sipafik/storage/app/public', '/home/USER/public_html/storage'); echo 'ok';`
   buka sekali di browser, lalu **hapus file itu**. (Diperlukan agar dokumen identitas kampus dapat dibuka Super Admin.)
8. Jika `symlink` diblokir hosting: salin isi `storage/app/public` ke `public_html/storage` setiap ada unggahan baru, atau minta dukungan hosting.

## 6. Konfigurasi `.env` produksi
Gunakan `.env.production.example` (tanpa secret). Wajib: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://domain`, kredensial DB, `APP_KEY` baru. Aktifkan `APP_FORCE_HTTPS=true` dan `SESSION_SECURE_COOKIE=true` **setelah** HTTPS berfungsi (jika diaktifkan sebelum HTTPS, login akan gagal). Setelah mengubah `.env` di server yang memakai cache: `php artisan config:clear`.

### Ganti password akun demo/Super Admin (WAJIB)
Tanpa SSH, buat hash di lokal: `php -r "echo password_hash('PasswordBaruKuat123!', PASSWORD_BCRYPT);"` lalu di phpMyAdmin:
`UPDATE users SET password='HASH_DI_ATAS' WHERE email='superadmin@sipafik.test';`
Untuk produksi sungguhan sebaiknya nonaktifkan/hapus akun demo (`UPDATE users SET is_active=0 WHERE email LIKE '%@sipafik.test' AND role<>'super_admin';`).

## 7. Keamanan
* Jangan pernah meletakkan `.env`, `vendor/`, `storage/` di folder yang dapat diakses web. Document root = `public/` saja.
* `.env`, `APP_KEY`, password DB **tidak boleh** masuk repository publik (sudah ada di `.gitignore`).
* `APP_DEBUG=false` di produksi. Hapus `cek_server.php` dan `link.php` setelah dipakai.
* Dokumen identitas kampus tersimpan di disk `public` (URL `/storage/campus-documents/<nama-acak>`); nama acak sulit ditebak, tetapi siapa pun yang memiliki URL dapat membukanya. Untuk data sensitif, pertimbangkan memindahkan ke disk privat (belum dikerjakan).
* Login dibatasi percobaan (rate limiter) dan sesi dibatalkan saat logout; CSRF aktif pada semua form.
* Rutin backup database (cron `mysqldump` atau fitur backup hosting).

## 8. Pengujian setelah online
1. `https://domain/up` → 200. Beranda `/` tampil lengkap dengan gambar/video.
2. `/login` → `mahasiswa02a@sipafik.test` (Jalur 1) → dashboard → buka tiap menu → logout → tombol Back tidak membuka dashboard.
3. `/super-admin/login` → dashboard, unduh dokumen identitas kampus (cek `storage:link`).
4. Ajukan fasilitas sebagai mahasiswa → setujui sebagai petugas → lihat sebagai mahasiswa.
5. URL acak (`/xyz`) → halaman 404 bergaya SIPAFIK; **tidak boleh** ada halaman error berisi path/kode (artinya `APP_DEBUG=false`).
6. Cek `storage/logs/laravel.log` untuk error.
7. Uji dari HP (responsif) dan pastikan HTTPS (gembok).

## 9. Troubleshooting singkat
| Gejala | Penyebab umum |
|---|---|
| 500 tanpa pesan | `APP_KEY` kosong, `storage/` tidak writable, versi PHP < 8.2, `vendor/` tidak lengkap |
| 404 pada semua halaman selain `/` | `mod_rewrite`/`.htaccess` tidak aktif, atau document root bukan `public/` |
| CSS/gambar tidak muncul | `APP_URL` salah, atau isi `public/` tidak lengkap di `public_html` |
| 419 Page Expired saat login | cookie sesi aman diaktifkan tanpa HTTPS, atau tabel `sessions` hilang |
| "Access denied for user" | kredensial `.env` salah; `DB_HOST` bukan `localhost` pada hosting tertentu |
| Dokumen identitas 404 | `storage:link` belum dibuat |
