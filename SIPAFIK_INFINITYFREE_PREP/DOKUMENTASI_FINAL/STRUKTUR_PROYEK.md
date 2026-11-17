# STRUKTUR PROYEK SIPAFIK

Laravel 12.71 · PHP ≥ 8.2 · MySQL/MariaDB · Blade + CSS/JS biasa (tanpa Vite/Tailwind untuk tampilan). Aset tampilan ada di `public/sipafik/`.

```
SIPAFIK_Backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php               login/logout pengguna kampus (rate limit, sesi)
│   │   │   ├── SuperAdminController.php         login Super Admin, verifikasi kampus, kelola pengguna, statistik & aktivitas
│   │   │   ├── CampusRegistrationController.php pendaftaran kampus + unggah dokumen identitas
│   │   │   ├── RoleDashboardController.php      dashboard per role: kartu menu, statistik, panel; halaman pemberitahuan
│   │   │   ├── LaboratoryController.php         CRUD & aktif/nonaktif lab (admin_kampus + petugas_fasilitas)
│   │   │   ├── LabScheduleController.php        jadwal lab + cegah bentrok
│   │   │   ├── FacilityRequestController.php    pengajuan, filter status, keputusan petugas
│   │   │   ├── EventController.php              (BARU) kegiatan, daftar/batal daftar, jadwal kegiatan
│   │   │   ├── EventRegistrationController.php  (BARU) pendaftar kegiatan, tandai hadir/batal
│   │   │   ├── CampusUserController.php         (BARU) pengguna kampus (admin kampus)
│   │   │   └── ProfileController.php            (BARU) profil & informasi kampus
│   │   └── Middleware/
│   │       ├── EnsureCampusAccess.php           (BARU) alias `campus.access:role1,role2`
│   │       └── PreventBackHistory.php           (BARU) header no-store untuk halaman login
│   ├── Models/  User, Campus, Laboratory, LabSchedule, FacilityRequest, Event*, EventRegistration*   (*baru)
│   └── Providers/AppServiceProvider.php         paginasi SIPAFIK, locale tanggal, flag HTTPS
├── bootstrap/app.php                            registrasi middleware
├── config/app.php                               timezone Asia/Jakarta, locale id, APP_FORCE_HTTPS
├── database/
│   ├── migrations/                              15 file (5 direkonstruksi, 3 baru; lihat CATATAN_PERUBAHAN)
│   └── seeders/                                 SuperAdmin, SipafikDemoData (kampus & pengguna demo), DemoUsers, SipafikModuleDemo (BARU)
├── lang/id/validation.php                       pesan validasi Indonesia
├── public/                                      ← DOCUMENT ROOT hosting
│   ├── index.php, .htaccess
│   └── sipafik/ css (auth.css, style.css, modules.css), js, assets (gambar/video landing)
├── resources/views/
│   ├── landing.blade.php, auth/login, campus/register       halaman publik
│   ├── layouts/dashboard + partials/sidebar                  layout bersama (menu per role, logout)
│   ├── dashboard/role, dashboard/notice
│   ├── super-admin/ dashboard, users, login                  layout khusus Super Admin
│   ├── laboratories/, lab-schedules/, facility-requests/
│   ├── events/, registrations/, campus-users/, profile/
│   ├── pagination/sipafik, errors/ (403 404 419 429 500 503)
├── routes/web.php                               seluruh route (publik, auth, super-admin, campus/*)
├── storage/app/public/campus-documents/         dokumen identitas kampus (perlu `storage:link`)
├── tests/Feature/SipafikFlowTest.php            22 tes alur (SQLite in-memory)
├── .env.example (lokal) · .env.production.example (hosting)
└── DOKUMENTASI_FINAL/                           folder ini
    ├── PANDUAN_*.md, LAPORAN_PENGUJIAN.md, STRUKTUR_PROYEK.md, CATATAN_PERUBAHAN.md, STATUS_FINAL.md
    ├── hosting/  cek_server.php, htaccess_untuk_public_html.txt, sipafik_db_final.sql
    ├── backup/   sipafik_db_ASLI_sebelum_perubahan.sql
    └── arsip_file_lama/                         file .bak/.backup yang dipindah dari public/ & app/
```

## Model data (tabel utama)
```
campuses (status: pending|approved|rejected)
   ├─< users (role, campus_id, is_active)            role: super_admin(campus NULL) admin_kampus organisasi dosen petugas_fasilitas mahasiswa
   ├─< laboratories (campus_id, code unik per kampus, status active|inactive)
   │      ├─< lab_schedules (laboratory_id, lecturer_id→users, campus_id)
   │      └─< facility_requests (user_id, laboratory_id, approved_by, campus_id, status pending|approved|rejected, review_notes)
   └─< events (campus_id, organizer_id→users, visibility campus|public, status draft|published|completed|cancelled, quota)
          └─< event_registrations (event_id, user_id, campus_id=kampus penyelenggara, status registered|attended|cancelled; unik event+user)
```

## Hak akses (ringkas)
| Fitur | Admin Kampus | Petugas | Organisasi | Dosen | Mahasiswa |
|---|---|---|---|---|---|
| Kelola pengguna kampus | ✅ | – | – | – | – |
| Tambah/edit/aktifkan lab, jadwal lab | ✅ | ✅ | – | – (lihat jadwal) | – |
| Putuskan permohonan fasilitas | ✅ | ✅ | – | – | – |
| Ajukan fasilitas | – | – | ✅ | ✅ | ✅ |
| Buat/ubah kegiatan | ✅ (semua) | – | ✅ (milik sendiri) | ✅ (milik sendiri) | – |
| Lihat peserta & tandai hadir | ✅ | – | ✅ (miliknya) | ✅ (miliknya) | – |
| Daftar kegiatan | – | – | – | – | ✅ |

Admin Kampus dan Petugas memproses permohonan (tidak mengajukan).

## Konvensi
* Semua query modul **selalu** dibatasi `campus_id` pengguna; data kampus lain → 404 (tidak membocorkan keberadaan).
* Route kampus berawalan `campus/…` bernama `campus.*`; middleware `auth` + `campus.access:…` untuk modul baru.
* Gaya: token warna di `auth.css` (`--forest`, `--champagne`, `--ivory`…); komponen modul di `modules.css` (awalan `mod-`).
