<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\FacilityRequest;
use App\Models\Laboratory;
use App\Models\LabSchedule;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RoleDashboardController extends Controller
{
    /** Role yang wajib terhubung ke kampus. */
    private const CAMPUS_ROLES = [
        'admin_kampus',
        'organisasi',
        'dosen',
        'petugas_fasilitas',
        'mahasiswa',
    ];

    public function index()
    {
        $user = Auth::user();

        // Super Admin menggunakan dashboard khusus.
        if ($user->role === 'super_admin') {
            return redirect()->route('super-admin.dashboard');
        }

        // Role lama (admin, petugas_laboratorium) atau role tak dikenal: tampilkan pemberitahuan, bukan error mentah.
        if (! in_array($user->role, self::CAMPUS_ROLES, true)) {
            return $this->notice(
                $user,
                'Akun belum memiliki dashboard',
                'Role akun ini ('.$user->role_label.') adalah akun lama yang belum dipetakan ke dashboard kampus. Hubungi Super Admin SIPAFIK untuk menyesuaikan role dan kampus akun Anda.'
            );
        }

        if (! $user->is_active) {
            return $this->notice(
                $user,
                'Akun sedang tidak aktif',
                'Akun Anda dinonaktifkan. Hubungi Admin Kampus Anda untuk mengaktifkannya kembali.'
            );
        }

        $user->loadMissing('campus');

        if ($user->campus === null) {
            return $this->notice(
                $user,
                'Akun belum terhubung ke kampus',
                'Akun ini belum terhubung ke kampus mana pun. Hubungi Super Admin atau Admin Kampus.'
            );
        }

        if ($user->campus->status !== 'approved') {
            $message = $user->campus->status === 'rejected'
                ? 'Pendaftaran kampus Anda ditolak. Alasan: '.($user->campus->rejection_reason ?: 'tidak dicantumkan').'.'
                : 'Pendaftaran kampus Anda masih menunggu verifikasi Super Admin. Dashboard akan terbuka setelah kampus disetujui.';

            return $this->notice($user, 'Kampus belum disetujui', $message);
        }

        $content = $this->roleContent($user);

        return view('dashboard.role', [
            'user' => $user,
            'content' => $content,
            'stats' => $this->stats($user),
            'panel' => $this->panel($user),
        ]);
    }

    private function notice(User $user, string $title, string $message)
    {
        return response()->view('dashboard.notice', [
            'user' => $user,
            'title' => $title,
            'message' => $message,
        ]);
    }

    private function roleContent(User $user): array
    {
        $roleContent = [
            'admin_kampus' => [
                'title' => 'Dashboard Admin Kampus',
                'subtitle' => 'Kelola identitas kampus, pengguna, kegiatan, dan fasilitas kampus.',
                'items' => [
                    ['number' => '01', 'title' => 'Data Pengguna', 'description' => 'Kelola akun dosen, organisasi, petugas fasilitas, dan mahasiswa kampus.', 'url' => route('campus.users.index')],
                    ['number' => '02', 'title' => 'Kegiatan Kampus', 'description' => 'Pantau dan kelola kegiatan yang diselenggarakan di kampus.', 'url' => route('campus.events.index')],
                    ['number' => '03', 'title' => 'Fasilitas Kampus', 'description' => 'Kelola informasi ruang dan fasilitas pendukung.', 'url' => route('campus.laboratories.index')],
                    ['number' => '04', 'title' => 'Permohonan Fasilitas', 'description' => 'Pantau dan proses pengajuan penggunaan fasilitas kampus.', 'url' => route('campus.facility-requests.index')],
                    ['number' => '05', 'title' => 'Jadwal Laboratorium', 'description' => 'Awasi dan atur jadwal penggunaan laboratorium.', 'url' => route('campus.lab-schedules.index')],
                    ['number' => '06', 'title' => 'Pendaftar Kegiatan', 'description' => 'Lihat seluruh pendaftar kegiatan di kampus.', 'url' => route('campus.registrations.index')],
                ],
            ],

            'organisasi' => [
                'title' => 'Dashboard Organisasi',
                'subtitle' => 'Kelola kegiatan organisasi dan kebutuhan fasilitas kampus.',
                'items' => [
                    ['number' => '01', 'title' => 'Kegiatan Organisasi', 'description' => 'Buat dan kelola kegiatan organisasi kampus.', 'url' => route('campus.events.index')],
                    ['number' => '02', 'title' => 'Pendaftaran Peserta', 'description' => 'Pantau mahasiswa yang mendaftar kegiatan.', 'url' => route('campus.registrations.index')],
                    ['number' => '03', 'title' => 'Jadwal Kegiatan', 'description' => 'Atur dan pantau jadwal kegiatan organisasi.', 'url' => route('campus.events.schedule')],
                    ['number' => '04', 'title' => 'Ajukan Fasilitas', 'description' => 'Ajukan penggunaan ruangan dan fasilitas kampus.', 'url' => route('campus.facility-requests.index')],
                ],
            ],

            'dosen' => [
                'title' => 'Dashboard Dosen / Penyelenggara',
                'subtitle' => 'Kelola kegiatan, peserta, jadwal, dan kebutuhan fasilitas.',
                'items' => [
                    ['number' => '01', 'title' => 'Kegiatan Saya', 'description' => 'Buat dan kelola informasi kegiatan kampus.', 'url' => route('campus.events.index')],
                    ['number' => '02', 'title' => 'Peserta', 'description' => 'Pantau pendaftaran peserta kegiatan.', 'url' => route('campus.registrations.index')],
                    ['number' => '03', 'title' => 'Jadwal', 'description' => 'Lihat jadwal penggunaan laboratorium kampus.', 'url' => route('campus.lab-schedules.index')],
                    ['number' => '04', 'title' => 'Ajukan Fasilitas', 'description' => 'Ajukan kebutuhan ruang untuk kegiatan.', 'url' => route('campus.facility-requests.index')],
                ],
            ],

            'petugas_fasilitas' => [
                'title' => 'Dashboard Petugas Fasilitas',
                'subtitle' => 'Kelola fasilitas dan tinjau permohonan penggunaan ruang.',
                'items' => [
                    ['number' => '01', 'title' => 'Data Fasilitas', 'description' => 'Tambah, ubah, dan aktifkan/nonaktifkan ruang laboratorium.', 'url' => route('campus.laboratories.index')],
                    ['number' => '02', 'title' => 'Permohonan Masuk', 'description' => 'Tinjau, setujui, atau tolak pengajuan penggunaan fasilitas.', 'url' => route('campus.facility-requests.index', ['status' => 'pending'])],
                    ['number' => '03', 'title' => 'Jadwal Penggunaan', 'description' => 'Pantau dan atur jadwal penggunaan fasilitas kampus.', 'url' => route('campus.lab-schedules.index')],
                    ['number' => '04', 'title' => 'Riwayat', 'description' => 'Lihat riwayat persetujuan dan penolakan permohonan.', 'url' => route('campus.facility-requests.index', ['status' => 'processed'])],
                ],
            ],

            'mahasiswa' => [
                'title' => 'Dashboard Mahasiswa',
                'subtitle' => 'Temukan kegiatan kampus dan pantau aktivitas yang kamu ikuti.',
                'items' => [
                    ['number' => '01', 'title' => 'Jelajahi Kegiatan', 'description' => 'Temukan kegiatan dari kampus sendiri maupun kegiatan publik kampus lain.', 'url' => route('campus.events.index')],
                    ['number' => '02', 'title' => 'Pendaftaran Saya', 'description' => 'Pantau kegiatan yang sudah kamu daftarkan.', 'url' => route('campus.registrations.index')],
                    ['number' => '03', 'title' => 'Jadwal Kegiatan', 'description' => 'Lihat jadwal kegiatan yang akan berlangsung.', 'url' => route('campus.events.schedule')],
                    ['number' => '04', 'title' => 'Permohonan Fasilitas', 'description' => 'Ajukan fasilitas kampus dan pantau status permohonan.', 'url' => route('campus.facility-requests.index')],
                ],
            ],
        ];

        return $roleContent[$user->role];
    }

    private function stats(User $user): array
    {
        $cid = $user->campus_id;
        $activeReg = fn ($q) => $q->whereIn('status', ['registered', 'attended']);

        return match ($user->role) {
            'admin_kampus' => [
                ['Pengguna Kampus', User::where('campus_id', $cid)->count(), 'akun terdaftar'],
                ['Kegiatan', Event::where('campus_id', $cid)->count(), Event::where('campus_id', $cid)->where('status', 'published')->count().' sedang dibuka'],
                ['Laboratorium Aktif', Laboratory::where('campus_id', $cid)->where('status', 'active')->count(), 'dari '.Laboratory::where('campus_id', $cid)->count().' ruang'],
                ['Permohonan Menunggu', FacilityRequest::where('campus_id', $cid)->where('status', 'pending')->count(), 'perlu keputusan'],
            ],
            'organisasi' => [
                ['Kegiatan Saya', Event::where('organizer_id', $user->id)->count(), 'diselenggarakan'],
                ['Total Peserta', EventRegistration::whereHas('event', fn ($q) => $q->where('organizer_id', $user->id))->where($activeReg)->count(), 'pendaftar aktif'],
                ['Kegiatan Mendatang', Event::where('organizer_id', $user->id)->whereDate('event_date', '>=', today())->whereIn('status', ['draft', 'published'])->count(), 'akan berlangsung'],
                ['Permohonan Saya', FacilityRequest::where('user_id', $user->id)->count(), FacilityRequest::where('user_id', $user->id)->where('status', 'pending')->count().' menunggu'],
            ],
            'dosen' => [
                ['Kegiatan Saya', Event::where('organizer_id', $user->id)->count(), 'diselenggarakan'],
                ['Total Peserta', EventRegistration::whereHas('event', fn ($q) => $q->where('organizer_id', $user->id))->where($activeReg)->count(), 'pendaftar aktif'],
                ['Jadwal Lab Mendatang', LabSchedule::where('campus_id', $cid)->whereDate('schedule_date', '>=', today())->where('status', 'scheduled')->count(), 'di kampus Anda'],
                ['Permohonan Saya', FacilityRequest::where('user_id', $user->id)->count(), FacilityRequest::where('user_id', $user->id)->where('status', 'approved')->count().' disetujui'],
            ],
            'petugas_fasilitas' => [
                ['Laboratorium Aktif', Laboratory::where('campus_id', $cid)->where('status', 'active')->count(), 'dari '.Laboratory::where('campus_id', $cid)->count().' ruang'],
                ['Permohonan Masuk', FacilityRequest::where('campus_id', $cid)->where('status', 'pending')->count(), 'menunggu keputusan'],
                ['Jadwal Mendatang', LabSchedule::where('campus_id', $cid)->whereDate('schedule_date', '>=', today())->where('status', 'scheduled')->count(), 'penggunaan fasilitas'],
                ['Riwayat Diproses', FacilityRequest::where('campus_id', $cid)->whereIn('status', ['approved', 'rejected'])->count(), 'disetujui / ditolak'],
            ],
            'mahasiswa' => [
                ['Kegiatan Tersedia', Event::visibleTo($user)->where('status', 'published')->whereDate('event_date', '>=', today())->count(), 'dapat diikuti'],
                ['Pendaftaran Aktif', EventRegistration::where('user_id', $user->id)->where('status', 'registered')->count(), 'kegiatan diikuti'],
                ['Permohonan Fasilitas', FacilityRequest::where('user_id', $user->id)->count(), FacilityRequest::where('user_id', $user->id)->where('status', 'pending')->count().' menunggu'],
                ['Permohonan Disetujui', FacilityRequest::where('user_id', $user->id)->where('status', 'approved')->count(), 'siap digunakan'],
            ],
            default => [],
        };
    }

    /** Panel ringkas "aktivitas terbaru" sesuai role. Setiap baris: [judul, keterangan, badge, url]. */
    private function panel(User $user): array
    {
        $cid = $user->campus_id;

        switch ($user->role) {
            case 'admin_kampus':
            case 'petugas_fasilitas':
                $rows = FacilityRequest::with(['user', 'laboratory'])
                    ->where('campus_id', $cid)->where('status', 'pending')
                    ->orderBy('request_date')->limit(5)->get()
                    ->map(fn ($r) => [
                        $r->title,
                        ($r->user?->name ?? '-').' · '.($r->laboratory?->name ?? '-').' · '.$r->request_date->translatedFormat('d M Y'),
                        'pending', 'Menunggu',
                        route('campus.facility-requests.index', ['status' => 'pending']),
                    ])->all();

                return ['title' => 'Permohonan menunggu keputusan', 'empty' => 'Tidak ada permohonan yang menunggu.', 'rows' => $rows];

            case 'dosen':
                $rows = LabSchedule::with(['laboratory'])
                    ->where('campus_id', $cid)->where('status', 'scheduled')
                    ->whereDate('schedule_date', '>=', today())
                    ->orderBy('schedule_date')->orderBy('start_time')->limit(5)->get()
                    ->map(fn ($s) => [
                        $s->course_name,
                        ($s->laboratory?->name ?? '-').' · '.$s->schedule_date->translatedFormat('d M Y').' · '.substr($s->start_time, 0, 5).'–'.substr($s->end_time, 0, 5),
                        'scheduled', 'Terjadwal',
                        route('campus.lab-schedules.index'),
                    ])->all();

                return ['title' => 'Jadwal laboratorium terdekat', 'empty' => 'Belum ada jadwal laboratorium mendatang.', 'rows' => $rows];

            default: // organisasi, mahasiswa
                $query = Event::with('campus')->whereDate('event_date', '>=', today())
                    ->orderBy('event_date')->orderBy('start_time')->limit(5);

                if ($user->role === 'organisasi') {
                    $query->where('organizer_id', $user->id)->whereIn('status', ['draft', 'published']);
                    $title = 'Kegiatan mendatang milik organisasi';
                } else {
                    $query->visibleTo($user)->where('status', 'published');
                    $title = 'Kegiatan terdekat untuk kamu';
                }

                $rows = $query->get()->map(fn ($e) => [
                    $e->title,
                    $e->campus?->name.' · '.$e->event_date->translatedFormat('d M Y').' · '.substr($e->start_time, 0, 5),
                    $e->status, $e->status_label,
                    route('campus.events.show', $e->id),
                ])->all();

                return ['title' => $title, 'empty' => 'Belum ada kegiatan mendatang.', 'rows' => $rows];
        }
    }
}
