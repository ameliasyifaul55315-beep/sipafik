# LAPORAN PENGUJIAN SIPAFIK

Tanggal: 17 November 2026 · Lingkungan: PHP 8.3.6, Laravel 12.71.0, MariaDB 10.11 (kompatibel MySQL), server `php artisan serve`.
Database uji = salinan `sipafik_db.sql` Anda (database Laragon Anda **tidak disentuh**).

> **Batas jujur laporan ini**: pengujian dilakukan secara teknis (kode, HTTP nyata, database). **Tidak ada browser grafis** di lingkungan saya, sehingga tampilan visual/responsif **belum diperiksa mata** — lihat bagian "Perlu uji manual".

## 1. Pengujian teknis
| Uji | Langkah | Hasil aktual | Status |
|---|---|---|---|
| PHP syntax | `php -l` semua controller/model/middleware/migration/seeder | Tanpa error | ✅ |
| Kompilasi view | `php artisan view:cache` | Seluruh Blade ter-compile | ✅ |
| Route | `php artisan route:list` | Semua route baru terdaftar; 0 link rusak pada crawl | ✅ |
| Cache produksi | `APP_ENV=production php artisan optimize` (config, route, view) | Berhasil | ✅ |
| Migrasi **DB kosong** | `migrate` pada DB baru | 15 migration sukses (sebelumnya **gagal**) | ✅ |
| Migrasi **DB data asli** | `migrate` pada salinan dump | Hanya 3 migration baru berjalan; data utuh | ✅ |
| Kesamaan skema | Bandingkan kolom/index DB kosong vs DB di-upgrade | Tabel domain identik* | ✅ |
| Integritas data lama | MD5 baris users (id 1–70, termasuk hash), campuses, labs lama, permohonan lama, tokens sebelum vs sesudah migrate+seed | **Identik** | ✅ |
| Seeder idempotent | `SipafikModuleDemoSeeder` dijalankan 2× | Jumlah data tetap sama | ✅ |
| Seeder lama | `DemoUsersSeeder` dijalankan ulang | Tidak mereset password | ✅ |
| Log error | `storage/logs/laravel.log` | 0 error baru hari ini (15 entri lama dari 6–16 Nov) | ✅ |
| PHPUnit | `php artisan test` (SQLite in-memory) | **22 tes, 175 assertion lulus** | ✅ |

\* Selisih hanya pada tabel bawaan Laravel `cache`/`jobs`/`failed_jobs` (beda versi stub framework) dan indeks FK yang tercakup unik gabungan; tidak berpengaruh.

## 2. Pengujian alur (HTTP nyata + MySQL, data demo)
Crawler login ke tiap role, membuka **setiap link** pada dashboard, lalu logout:

| Role | Link dibuka | Non-200 | Tombol logout | Setelah logout buka dashboard |
|---|---|---|---|---|
| Super Admin | 2 | 0 | ada | → `/login` ✅ |
| Admin Kampus | 10 | 0 | ada | → `/login` ✅ |
| Organisasi | 10 | 0 | ada | → `/login` ✅ |
| Dosen | 6 | 0 | ada | → `/login` ✅ |
| Petugas Fasilitas | 7 | 0 | ada | → `/login` ✅ |
| Mahasiswa | 10 | 0 | ada | → `/login` ✅ |
| Admin lama (tanpa kampus) | — | — | ada (halaman pemberitahuan) | ✅ |
| Kampus pending (UNIGHA) | — | — | ada (halaman pemberitahuan) | ✅ |

Alur bisnis end-to-end (skrip `e2e`, MySQL):
| # | Skenario (sesuai brief bagian J) | Hasil |
|---|---|---|
| 1–3 | Super Admin login, **setujui** kampus 7, **tolak** kampus 8 dengan alasan | Status berubah; Admin Kampus 7 menjadi aktif ✅ |
| 4 | Admin kampus ditolak mencoba login | Ditolak ✅ |
| 5–6 | Petugas fasilitas menambah laboratorium | Tersimpan dengan `campus_id` kampus petugas ✅ |
| 7–8 | Mahasiswa login & mengajukan fasilitas | Status `pending`, `campus_id` benar ✅ |
| – | Pengajuan melebihi kapasitas (13 > 12) | Ditolak ✅ |
| 9–11 | Petugas menyetujui; pemohon melihat "Disetujui" | ✅ |
| – | Pengajuan bentrok dengan permohonan disetujui | Ditolak ✅ |
| – | Menolak tanpa alasan → ditolak sistem; dengan alasan → alasan tampil ke pemohon | ✅ |
| 12–13 | Jadwal lab pertama tersimpan; jadwal tumpang-tindih ditolak | ✅ |
| 14–15 | Logout lalu buka halaman terlindungi | Dialihkan ke login ✅ |
| – | **Lintas kampus**: buka kegiatan internal kampus lain | 404 ✅ |
| – | Kegiatan **publik** kampus lain | Dapat dibuka (200) ✅ |
| – | Ajukan lab kampus lain | Ditolak ✅ |
| – | Petugas kampus A memutuskan permohonan kampus B / edit lab kampus B | 404, data tidak berubah ✅ |
| – | Mahasiswa membuka `/super-admin/dashboard` | 403 ✅ |

Pengujian otomatis tambahan (PHPUnit) mencakup: kuota penuh, pendaftaran ganda, draf tak terlihat mahasiswa, hak edit kegiatan (pemilik/admin saja), pengelolaan pengguna terbatas pada kampus sendiri (tak bisa membuat `super_admin`), `GET /logout` ditolak (405), header `no-store`, halaman 404 ber-brand, kode lab sama boleh antar kampus tetapi unik dalam kampus.

## 3. Kesalahan pengujian saya sendiri (transparansi)
Dua cek awal sempat "gagal" pada skrip uji: ID permohonan yang saya pakai ternyata milik kampus yang sama, dan login admin kampus ditolak mengarah ke beranda (bukan `/login`). Keduanya **kesalahan skrip, bukan bug aplikasi**; dites ulang dengan benar dan lulus.

## 4. Perlu uji manual (belum bisa saya lakukan)
1. **Tampilan visual & responsif** desktop/HP seluruh halaman, termasuk halaman baru (sidebar horizontal di HP, kartu kegiatan, tabel yang bisa di-scroll). Buka di Chrome → F12 → mode perangkat.
2. **Upload dokumen identitas** pada `/register/campus` (PDF/JPG/PNG ≤ 2 MB) dan link dokumen pada dashboard Super Admin (butuh `php artisan storage:link`).
3. **Pendaftaran kampus baru** → muncul di Super Admin → disetujui → Admin Kampus login.
4. **Landing page** dan **video hero** (`public/sipafik/assets/videos`, ±8 MB) pada koneksi hosting.
5. **Seluruh pengujian di atas pada hosting sungguhan** (HTTPS, session, permission).
6. Email: aplikasi tidak mengirim email; tidak diuji.

## 5. Langkah uji manual singkat untuk dosen
1. `/login` → `mahasiswa02a@sipafik.test` → *Jelajahi Kegiatan* → daftar → *Pendaftaran Saya* → *Permohonan Fasilitas* → ajukan → keluar.
2. `/login` → `petugas02@sipafik.test` → *Permohonan Masuk* → proses → *Riwayat diproses* → keluar.
3. `/login` → mahasiswa tadi → lihat status/catatan → keluar → tekan tombol Back (tidak boleh terbuka).
4. `/super-admin/login` → verifikasi kampus (setujui/tolak) → *Pengawasan Aktivitas Sistem*.
