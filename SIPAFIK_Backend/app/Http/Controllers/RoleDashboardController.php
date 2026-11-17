<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class RoleDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Super Admin menggunakan dashboard khusus.
        if ($user->role === 'super_admin') {
            return redirect()->route('super-admin.dashboard');
        }

        // Role yang wajib terhubung dengan kampus.
        $campusRoles = [
            'admin_kampus',
            'organisasi',
            'dosen',
            'petugas_fasilitas',
            'mahasiswa',
        ];

        // Pastikan akun aktif dan kampus sudah disetujui.
        if (in_array($user->role, $campusRoles, true)) {
            abort_unless(
                $user->is_active,
                403,
                'Akun Anda sedang tidak aktif.'
            );

            $user->loadMissing('campus');

            abort_unless(
                $user->campus !== null &&
                $user->campus->status === 'approved',
                403,
                'Kampus Anda belum disetujui oleh Super Admin.'
            );
        }

        $roleContent = [
            'admin_kampus' => [
                'title' => 'Dashboard Admin Kampus',
                'subtitle' => 'Kelola identitas kampus, pengguna, kegiatan, dan fasilitas kampus.',
                'items' => [
                    ['number' => '01', 'title' => 'Data Pengguna', 'description' => 'Kelola akun dosen, petugas fasilitas, dan mahasiswa kampus.'],
                    ['number' => '02', 'title' => 'Kegiatan Kampus', 'description' => 'Pantau kegiatan yang diselenggarakan di kampus.'],
                    [
                        'number' => '03',
                        'title' => 'Fasilitas Kampus',
                        'description' => 'Kelola informasi ruang dan fasilitas pendukung.',
                        'url' => route('campus.laboratories.index'),
                    ],
                    [
                        'number' => '04',
                        'title' => 'Permohonan Fasilitas',
                        'description' => 'Pantau dan proses pengajuan penggunaan fasilitas kampus.',
                        'url' => route('campus.facility-requests.index'),
                    ],
                ],
            ],

            'organisasi' => [
                'title' => 'Dashboard Organisasi',
                'subtitle' => 'Kelola kegiatan organisasi dan kebutuhan fasilitas kampus.',
                'items' => [
                    ['number' => '01', 'title' => 'Kegiatan Organisasi', 'description' => 'Buat dan kelola kegiatan organisasi kampus.'],
                    ['number' => '02', 'title' => 'Pendaftaran Peserta', 'description' => 'Pantau mahasiswa yang mendaftar kegiatan.'],
                    ['number' => '03', 'title' => 'Jadwal Kegiatan', 'description' => 'Atur dan pantau jadwal kegiatan organisasi.'],
                    [
                        'number' => '04',
                        'title' => 'Ajukan Fasilitas',
                        'description' => 'Ajukan penggunaan ruangan dan fasilitas kampus.',
                        'url' => route('campus.facility-requests.index'),
                    ],
                ],
            ],

            'dosen' => [
                'title' => 'Dashboard Dosen / Penyelenggara',
                'subtitle' => 'Kelola kegiatan, peserta, jadwal, dan kebutuhan fasilitas.',
                'items' => [
                    ['number' => '01', 'title' => 'Kegiatan Saya', 'description' => 'Buat dan kelola informasi kegiatan kampus.'],
                    ['number' => '02', 'title' => 'Peserta', 'description' => 'Pantau pendaftaran peserta kegiatan.'],
                    [
                        'number' => '03',
                        'title' => 'Jadwal',
                        'description' => 'Lihat jadwal penggunaan laboratorium kampus.',
                        'url' => route('campus.lab-schedules.index'),
                    ],
                    [
                        'number' => '04',
                        'title' => 'Ajukan Fasilitas',
                        'description' => 'Ajukan kebutuhan ruang untuk kegiatan.',
                        'url' => route('campus.facility-requests.index'),
                    ],
                ],
            ],

            'petugas_fasilitas' => [
                'title' => 'Dashboard Petugas Fasilitas',
                'subtitle' => 'Kelola fasilitas dan tinjau permohonan penggunaan ruang.',
                'items' => [
                    [
                        'number' => '01',
                        'title' => 'Data Fasilitas',
                        'description' => 'Lihat informasi ruang dan fasilitas kampus.',
                        'url' => route('campus.laboratories.index'),
                    ],
                    [
                        'number' => '02',
                        'title' => 'Permohonan Masuk',
                        'description' => 'Tinjau, setujui, atau tolak pengajuan penggunaan fasilitas.',
                        'url' => route('campus.facility-requests.index'),
                    ],
                    [
                        'number' => '03',
                        'title' => 'Jadwal Penggunaan',
                        'description' => 'Pantau jadwal penggunaan fasilitas kampus.',
                        'url' => route('campus.lab-schedules.index'),
                    ],
                    ['number' => '04', 'title' => 'Riwayat', 'description' => 'Lihat riwayat persetujuan dan penggunaan.'],
                ],
            ],

            'mahasiswa' => [
                'title' => 'Dashboard Mahasiswa',
                'subtitle' => 'Temukan kegiatan kampus dan pantau aktivitas yang kamu ikuti.',
                'items' => [
                    ['number' => '01', 'title' => 'Jelajahi Kegiatan', 'description' => 'Temukan kegiatan dari kampus sendiri maupun kampus lain.'],
                    ['number' => '02', 'title' => 'Pendaftaran Saya', 'description' => 'Pantau kegiatan yang sudah kamu daftarkan.'],
                    ['number' => '03', 'title' => 'Jadwal Kegiatan', 'description' => 'Lihat jadwal kegiatan yang akan berlangsung.'],
                    [
                        'number' => '04',
                        'title' => 'Permohonan Fasilitas',
                        'description' => 'Ajukan fasilitas kampus dan pantau status permohonan.',
                        'url' => route('campus.facility-requests.index'),
                    ],
                ],
            ],
        ];

        abort_unless(
            isset($roleContent[$user->role]),
            403,
            'Role akun tidak dikenali atau belum memiliki dashboard.'
        );

        return view('dashboard.role', [
            'user' => $user,
            'content' => $roleContent[$user->role],
        ]);
    }
}
