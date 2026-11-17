# PANDUAN PENGGUNAAN SIPAFIK

SIPAFIK — Sistem Informasi Pengelolaan Kegiatan dan Fasilitas Pendukung Event Kampus Berbasis Web.
Akun untuk mencoba ada di `PANDUAN_LOGIN_DEMO.md`. Semua role (kecuali Super Admin) masuk lewat **/login** memakai **email**.
Setiap halaman dashboard memiliki tombol **"Keluar dari akun ↗"** di bagian bawah sidebar (di HP, sidebar tampil di atas halaman).

## Alur umum
1. Buka `/login` → isi email dan password → **Masuk**.
2. Dashboard menampilkan kartu statistik, menu modul, dan panel aktivitas terbaru sesuai role.
3. Menu di sidebar kiri; **Profil & Kampus** menampilkan data akun dan informasi kampus.
4. Selesai → **Keluar dari akun**. Tombol "Back" browser tidak akan membuka dashboard lagi tanpa login.

## 1. Super Admin (`/super-admin/login`)
1. Login → Dashboard: statistik, tabel *Pengawasan Aktivitas Sistem* (pengguna, kegiatan, lab, permohonan per kampus).
2. **Verifikasi kampus**: pada daftar pendaftaran kampus, buka dokumen identitas, lalu **Setujui** atau **Tolak** (alasan penolakan wajib diisi). Setelah disetujui, akun Admin Kampus menjadi aktif.
3. **Manajemen Pengguna** (`/super-admin/users`): lihat seluruh akun, aktifkan/nonaktifkan akun.
4. Keluar: tombol *Keluar dari akun* (tersedia di dashboard dan halaman pengguna).

## 2. Admin Kampus
* **Pengguna Kampus** → *Tambah Pengguna* (dosen / organisasi / petugas fasilitas / mahasiswa; langsung aktif dan terhubung ke kampus Anda) dan tombol *Nonaktifkan/Aktifkan*.
* **Kegiatan** → melihat dan mengelola semua kegiatan kampus (buat, ubah, ubah status), melihat peserta.
* **Laboratorium / Jadwal Laboratorium** → tambah, edit, aktif/nonaktifkan lab; tambah dan ubah jadwal (sistem menolak jadwal bentrok).
* **Permohonan Fasilitas** → menyetujui/menolak permohonan (penolakan wajib alasan).
* **Pendaftar Kegiatan** → melihat seluruh pendaftar dan menandai hadir/batal.

## 3. Petugas Fasilitas
* **Data Laboratorium**: *Tambah Laboratorium*, *Edit*, *Nonaktifkan/Aktifkan* (kewenangan sama dengan Admin Kampus).
* **Permohonan Masuk**: tab *Menunggu*; pilih *Setujui* / *Tolak* + catatan → **Proses**. Tab **Riwayat diproses** menampilkan keputusan yang sudah dibuat.
* **Jadwal Penggunaan**: tambah/ubah jadwal lab.

## 4. Organisasi
* **Kegiatan Organisasi** → *+ Buat Kegiatan* (judul, kategori, tanggal, waktu, lokasi, kuota, visibilitas Kampus/Publik, status Draf/Dibuka). Hanya kegiatan milik sendiri yang dapat diubah.
* **Pendaftar Kegiatan**: pantau mahasiswa yang mendaftar, tandai kehadiran.
* **Jadwal Kegiatan**: agenda kegiatan mendatang.
* **Permohonan Fasilitas** → *+ Ajukan Fasilitas*: pilih lab aktif, isi keperluan, tanggal, waktu, jumlah peserta (≤ kapasitas). Pantau status serta catatan petugas.

## 5. Dosen
Sama seperti Organisasi untuk kegiatan, peserta, dan permohonan fasilitas; ditambah **Jadwal Laboratorium** (hanya melihat).

## 6. Mahasiswa
* **Jelajahi Kegiatan**: tab *Kampus Saya* dan *Kampus Lain (Publik)*, cari/filter kategori → **Lihat Detail** → **Daftar Kegiatan Ini** (kuota dicek otomatis). Dapat **Batalkan Pendaftaran** sebelum kegiatan.
* **Pendaftaran Saya**: riwayat pendaftaran dan status.
* **Jadwal Kegiatan**: agenda per tanggal; kegiatan yang diikuti diberi label *Terdaftar*.
* **Permohonan Fasilitas**: ajukan dan pantau; jika ditolak, alasan tampil di kolom *Catatan / Proses*.

## Aturan penting sistem
* Data tiap kampus terpisah. Mahasiswa kampus lain hanya dapat melihat kegiatan berstatus **Publik**.
* Laboratorium **nonaktif** tidak muncul pada pengajuan baru.
* Jadwal/permohonan yang waktunya bersinggungan di laboratorium yang sama ditolak. Waktu yang bersebelahan (10:00 selesai, 10:00 mulai) diperbolehkan.
* Akun dari kampus yang belum disetujui hanya melihat halaman pemberitahuan.
