# CATATAN PERUBAHAN

Semua perubahan bersifat **menambah / memperbaiki**. Tidak ada tabel/kolom/route/controller lama yang diganti namanya, tidak ada data yang dihapus. Versi awal proyek tersimpan pada ZIP asli yang Anda berikan (`sipasik.zip`) dan `sipafik_db.sql`.

## A. Masalah yang ditemukan lalu diperbaiki (sumber masalah, bukan tambalan)
| # | Temuan | Dampak | Perbaikan |
|---|---|---|---|
| 1 | File migration untuk `role`, `laboratories`, `lab_schedules`, `facility_requests`, `personal_access_tokens` **hilang** dari proyek (tercatat di tabel `migrations`, tetapi filenya tidak ada) | `php artisan migrate` pada hosting/DB kosong **gagal** | Direkonstruksi dari skema DB aktual dengan **nama sama persis** (aman: tidak dijalankan ulang pada DB lama). Diuji pada DB kosong dan DB salinan data asli |
| 2 | Migration `2026_11_06_035029` hanya memanggil `unique('username')`, padahal kolom `username` dan `campus_id` di `users` tidak pernah dibuat oleh migration | Instalasi baru gagal | Migration dilengkapi (kolom dibuat bila belum ada) |
| 3 | View `facility-requests/*` memakai `asset('css/auth.css')` (path salah) dan **tidak ada sidebar/logout** | Halaman permohonan tanpa gaya, user "terjebak" tanpa tombol keluar | Dipindah ke layout bersama `layouts/dashboard` |
| 4 | Halaman `super-admin/users` tidak punya tombol logout | Logout tidak tersedia | Ditambahkan form logout POST + CSRF |
| 5 | Validasi `code` laboratorium `max:50` padahal kolom DB `varchar(30)` | Error SQL pada kode 31–50 karakter | `max:30` (juga di form) |
| 6 | Kode laboratorium unik **global** | Kampus A dapat memblokir kode milik kampus B dan membocorkan keberadaan kode | Migration baru: unik per kampus `(campus_id, code)` |
| 7 | Permohonan fasilitas tidak memvalidasi **kapasitas** dan tidak memeriksa bentrok saat diajukan | Data tidak konsisten | Validasi kapasitas, bentrok (jadwal + permohonan disetujui), jam lewat; transaksi + lock saat persetujuan |
| 8 | Penolakan permohonan tanpa alasan diperbolehkan | Pemohon tak tahu alasan | Catatan wajib saat menolak |
| 9 | Jadwal lab hanya cek bentrok antar jadwal | Jadwal bisa menimpa permohonan yang sudah disetujui | Cek ke permohonan disetujui juga; tanggal tidak boleh lampau |
| 10 | Edit jadwal gagal jika lab-nya kemudian dinonaktifkan | Jadwal lama tidak bisa diedit | Lab yang sama tetap dapat dipilih saat edit |
| 11 | Akun role lama (`admin`, `petugas_laboratorium`) / tanpa kampus / kampus pending → **HTTP 403 mentah** | Terlihat sebagai error | Halaman pemberitahuan ramah + tombol logout |
| 12 | Semua menu dashboard Admin/Organisasi/Dosen/Mahasiswa (kegiatan, pengguna, peserta, jadwal) **tanpa link / tanpa tabel** | Menu hanya hiasan | Modul Kegiatan, Pendaftaran, Pengguna Kampus dibangun dan dihubungkan |
| 13 | `DemoUsersSeeder`/`SuperAdminSeeder` memakai `updateOrCreate` | Menjalankan ulang **me-reset password** akun | Diubah ke `firstOrCreate` |
| 14 | `DatabaseSeeder` membuat user `test@example.com` via factory | Gagal bila dijalankan ulang | Diganti memanggil seeder idempotent |
| 15 | Zona waktu `UTC`, bahasa `en` | Jam/tanggal tidak sesuai WIB | `APP_TIMEZONE=Asia/Jakarta`, locale `id`, pesan validasi Indonesia |
| 16 | Paginasi bawaan Laravel memerlukan Tailwind (tidak dimuat) | Tombol halaman rusak | Template paginasi bergaya SIPAFIK |
| 17 | File `.bak`/`.backup` ada di `public/` (dapat diunduh) | Kebocoran kode lama | Dipindah ke `DOKUMENTASI_FINAL/arsip_file_lama/` (tidak dihapus) |
| 18 | Back-button menampilkan dashboard setelah logout (cache browser) | Isi halaman terlihat | Middleware `PreventBackHistory` (no-store) |

## B. Fitur baru / yang disambungkan
* **Modul Kegiatan**: `events`, `event_registrations` (migration baru), model `Event`, `EventRegistration`, `EventController`, `EventRegistrationController`; kuota dengan lock baris (anti-overbooking), visibilitas *Kampus*/*Publik*, status Draf/Dibuka/Selesai/Dibatalkan.
* **Pengguna Kampus** (`CampusUserController`): tambah akun & aktif/nonaktif, hanya dalam kampus sendiri.
* **Profil & Kampus** (`ProfileController`).
* **Layout & sidebar bersama** per role (`layouts/dashboard`, `partials/sidebar`) + `modules.css`.
* **Dashboard**: statistik riil, panel aktivitas, semua kartu menu bertautan ke route nyata.
* **Super Admin**: panel "Pengawasan Aktivitas Sistem" + logout di halaman pengguna.
* **Middleware**: `campus.access:{roles}` (role + akun aktif + kampus disetujui), `PreventBackHistory`.
* **Halaman error** 403/404/419/429/500/503 bergaya SIPAFIK.
* **Seeder** `SipafikModuleDemoSeeder` (idempotent) → akun Admin Kampus + data modul.
* **Pengujian otomatis** `tests/Feature/SipafikFlowTest.php` (22 tes).
* **Hosting**: `.env.example` (lokal), `.env.production.example`, `hosting/cek_server.php`, `hosting/htaccess_untuk_public_html.txt`, flag `APP_FORCE_HTTPS`.

## C. Daftar file
**Baru**: 8 file migration (5 rekonstruksi + `events`, `event_registrations`, `make_laboratory_code_unique_per_campus`), `app/Models/Event.php`, `EventRegistration.php`, `app/Http/Controllers/{EventController,EventRegistrationController,CampusUserController,ProfileController}.php`, `app/Http/Middleware/{EnsureCampusAccess,PreventBackHistory}.php`, `database/seeders/SipafikModuleDemoSeeder.php`, `lang/id/validation.php`, `public/sipafik/css/modules.css`, view: `layouts/dashboard`, `partials/sidebar`, `pagination/sipafik`, `events/*`, `registrations/index`, `campus-users/*`, `profile/show`, `dashboard/notice`, `errors/*`, `tests/Feature/SipafikFlowTest.php`.
**Diubah**: `bootstrap/app.php`, `routes/web.php` (hanya penambahan), `config/app.php`, `AppServiceProvider`, `RoleDashboardController`, `FacilityRequestController`, `LaboratoryController`, `LabScheduleController`, `SuperAdminController`, `Models/User.php`, `Models/Campus.php`, migration `2026_11_06_035029`, `DatabaseSeeder`, `DemoUsersSeeder`, `SuperAdminSeeder`, view `dashboard/role`, `laboratories/*`, `lab-schedules/*`, `facility-requests/*`, `super-admin/users`, `super-admin/dashboard`, `.env.example`.
**Dihapus**: `tests/*/ExampleTest.php` (contoh bawaan Laravel). Tidak ada data/fitur pengguna yang dihapus.
