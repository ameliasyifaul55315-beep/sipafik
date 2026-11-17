<?php

namespace Database\Seeders;

use App\Models\Campus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\FacilityRequest;
use App\Models\Laboratory;
use App\Models\LabSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Data demo modul (admin kampus, laboratorium, jadwal, permohonan, kegiatan, pendaftaran)
 * untuk kampus demo yang berstatus APPROVED. IDEMPOTENT: memakai firstOrCreate dengan kunci alami,
 * tidak menghapus / menimpa data apa pun, dan tidak mengubah password akun yang sudah ada.
 * Data lama dengan campus_id NULL tidak disentuh.
 */
class SipafikModuleDemoSeeder extends Seeder
{
    private const PASSWORD = 'DemoSipafik2026!';

    public function run(): void
    {
        $anchor = Carbon::create(2026, 11, 17); // tanggal acuan tetap -> kunci data deterministik
        $d = fn (int $days) => $anchor->copy()->addDays($days)->toDateString();

        // 1. Akun Admin Kampus untuk kampus demo (id 2..10). Aktif hanya bila kampus approved
        //    (sama seperti perilaku Super Admin saat menyetujui/menolak kampus).
        $demoCampuses = Campus::where('username', 'like', 'demo\_%')->orderBy('id')->get();

        foreach ($demoCampuses as $campus) {
            $nn = str_pad((string) $campus->id, 2, '0', STR_PAD_LEFT);
            User::firstOrCreate(
                ['email' => "adminkampus{$nn}@sipafik.test"],
                [
                    'name' => "Admin Kampus Demo {$nn}",
                    'username' => "adminkampus_demo_{$nn}",
                    'password' => self::PASSWORD,
                    'role' => 'admin_kampus',
                    'campus_id' => $campus->id,
                    'is_active' => $campus->status === 'approved',
                ]
            );
        }

        // 2. Data modul hanya untuk kampus approved.
        foreach ($demoCampuses->where('status', 'approved') as $i => $campus) {
            $nn = str_pad((string) $campus->id, 2, '0', STR_PAD_LEFT);

            $admin = User::where('email', "adminkampus{$nn}@sipafik.test")->first();
            $petugas = User::where('email', "petugas{$nn}@sipafik.test")->first();
            $dosen = User::where('email', "dosen{$nn}@sipafik.test")->first();
            $org = User::where('email', "organisasi{$nn}@sipafik.test")->first();
            $mhsA = User::where('email', "mahasiswa{$nn}a@sipafik.test")->first();
            $mhsB = User::where('email', "mahasiswa{$nn}b@sipafik.test")->first();

            if (! $admin || ! $petugas || ! $dosen || ! $org || ! $mhsA || ! $mhsB) {
                $this->command?->warn("Akun demo kampus {$nn} tidak lengkap, dilewati.");
                continue;
            }

            // --- Laboratorium (4 per kampus; satu nonaktif) ---
            $labDefs = [
                ['01', 'Laboratorium Komputer', 'Gedung Teknik Lt. 1', 30, 'active', ['Komputer', 'Proyektor', 'AC', 'WiFi']],
                ['02', 'Laboratorium Jaringan', 'Gedung Teknik Lt. 2', 25, 'active', ['Router', 'Switch', 'Server', 'AC']],
                ['03', 'Ruang Seminar', 'Gedung Rektorat Lt. 3', 60, 'active', ['Proyektor', 'Sound System', 'AC']],
                ['04', 'Laboratorium Multimedia', 'Gedung Seni Lt. 1', 20, 'inactive', ['Komputer Grafis', 'Kamera', 'Studio Mini']],
            ];
            $labs = [];
            foreach ($labDefs as [$no, $name, $loc, $cap, $status, $fac]) {
                $labs[$no] = Laboratory::firstOrCreate(
                    ['campus_id' => $campus->id, 'code' => "LAB-{$nn}-{$no}"],
                    ['name' => $name, 'location' => $loc, 'description' => "{$name} {$campus->name}.", 'capacity' => $cap, 'facilities' => $fac, 'status' => $status]
                );
            }

            // --- Jadwal laboratorium (5 per kampus, pagi hari; tidak bentrok dengan permohonan siang) ---
            $scheduleDefs = [
                [$labs['01'], 1, '08:00', '10:00', 'Pemrograman Web', 'TI-5A', 'praktikum'],
                [$labs['01'], 2, '10:30', '12:30', 'Basis Data', 'TI-3B', 'praktikum'],
                [$labs['02'], 3, '08:00', '11:00', 'Jaringan Komputer', 'TI-5B', 'praktikum'],
                [$labs['03'], 4, '09:00', '11:00', 'Seminar Proposal', 'TI-7A', 'kegiatan'],
                [$labs['02'], 5, '08:00', '10:00', 'Keamanan Informasi', 'TI-7B', 'perkuliahan'],
            ];
            foreach ($scheduleDefs as [$lab, $off, $start, $end, $course, $class, $type]) {
                LabSchedule::firstOrCreate(
                    ['campus_id' => $campus->id, 'laboratory_id' => $lab->id, 'schedule_date' => $d($off), 'start_time' => $start.':00'],
                    ['lecturer_id' => $dosen->id, 'course_name' => $course, 'class_name' => $class, 'end_time' => $end.':00', 'schedule_type' => $type, 'status' => 'scheduled', 'notes' => 'Data demo SIPAFIK']
                );
            }

            // --- Permohonan fasilitas (5 per kampus: 2 approved, 2 pending, 1 rejected) ---
            $requestDefs = [
                [$mhsA, $labs['01'], 'Latihan Tim Lomba Pemrograman', 'Persiapan lomba pemrograman tingkat provinsi.', 2, '13:00', '15:00', 20, 'approved', $petugas, 'Disetujui. Harap menjaga kebersihan ruangan.'],
                [$dosen, $labs['02'], 'Pelatihan Dasar Jaringan', 'Pelatihan jaringan untuk mahasiswa semester awal.', 3, '13:00', '15:00', 20, 'approved', $admin, 'Disetujui oleh Admin Kampus.'],
                [$org, $labs['03'], 'Seminar Teknologi Kampus', 'Seminar terbuka yang diselenggarakan organisasi.', 6, '09:00', '12:00', 50, 'pending', null, null],
                [$mhsB, $labs['01'], 'Belajar Kelompok Basis Data', 'Belajar kelompok menjelang ujian.', 7, '13:00', '15:00', 15, 'pending', null, null],
                [$mhsA, $labs['02'], 'Uji Coba Server Pribadi', 'Eksperimen konfigurasi server di luar jam kuliah.', 4, '13:00', '14:00', 10, 'rejected', $petugas, 'Ditolak: laboratorium dijadwalkan untuk pemeliharaan perangkat.'],
            ];
            foreach ($requestDefs as [$user, $lab, $title, $purpose, $off, $start, $end, $count, $status, $reviewer, $notes]) {
                FacilityRequest::firstOrCreate(
                    ['campus_id' => $campus->id, 'user_id' => $user->id, 'title' => $title],
                    [
                        'laboratory_id' => $lab->id, 'purpose' => $purpose, 'request_date' => $d($off),
                        'start_time' => $start.':00', 'end_time' => $end.':00', 'participants_count' => $count,
                        'status' => $status, 'approved_by' => $reviewer?->id, 'review_notes' => $notes,
                    ]
                );
            }

            // --- Kegiatan (6 per kampus) ---
            $eventDefs = [
                [$org, 'Seminar Nasional Teknologi', 'seminar', 'Ruang Seminar', 10, '09:00', '12:00', 100, 'public', 'published'],
                [$dosen, 'Workshop Pengembangan Aplikasi Web', 'workshop', 'Laboratorium Komputer', 14, '08:30', '15:00', 40, 'campus', 'published'],
                [$org, 'Pelatihan Kepemimpinan Mahasiswa', 'pelatihan', 'Aula Kampus', 7, '08:00', '16:00', 30, 'campus', 'published'],
                [$org, 'Lomba Karya Tulis Ilmiah', 'lomba', 'Gedung Rektorat', 21, '09:00', '14:00', 60, 'public', 'published'],
                [$dosen, 'Bakti Sosial Mahasiswa', 'bakti_sosial', 'Desa Binaan', -5, '07:30', '12:00', 25, 'campus', 'completed'],
                [$org, 'Rapat Kerja Organisasi', 'lainnya', 'Ruang Rapat', 30, '13:00', '16:00', 20, 'campus', 'draft'],
            ];
            $events = [];
            foreach ($eventDefs as $k => [$organizer, $title, $cat, $loc, $off, $start, $end, $quota, $vis, $status]) {
                $events[$k + 1] = Event::firstOrCreate(
                    ['campus_id' => $campus->id, 'title' => "{$title} {$campus->name}"],
                    [
                        'organizer_id' => $organizer->id,
                        'description' => "{$title} yang diselenggarakan di {$campus->name}. Terbuka untuk mahasiswa. (Data demo SIPAFIK)",
                        'category' => $cat, 'location' => $loc, 'event_date' => $d($off),
                        'start_time' => $start.':00', 'end_time' => $end.':00',
                        'quota' => $quota, 'visibility' => $vis, 'status' => $status,
                    ]
                );
            }

            // --- Pendaftaran peserta (6 per kampus) ---
            $regDefs = [
                [$events[1], $mhsA, 'registered'],
                [$events[2], $mhsA, 'registered'],
                [$events[3], $mhsA, 'cancelled'],
                [$events[1], $mhsB, 'registered'],
                [$events[3], $mhsB, 'registered'],
                [$events[5], $mhsB, 'attended'],
            ];
            foreach ($regDefs as [$event, $student, $status]) {
                EventRegistration::firstOrCreate(
                    ['event_id' => $event->id, 'user_id' => $student->id],
                    ['campus_id' => $event->campus_id, 'status' => $status, 'registered_at' => $anchor->copy()->subDays(2)]
                );
            }
        }

        // 3. Pendaftaran lintas kampus pada kegiatan PUBLIK (mahasiswa A kampus N -> kegiatan publik kampus berikutnya).
        $approved = $demoCampuses->where('status', 'approved')->values();
        foreach ($approved as $i => $campus) {
            $next = $approved[($i + 1) % $approved->count()];
            if ($next->id === $campus->id) {
                continue;
            }
            $nn = str_pad((string) $campus->id, 2, '0', STR_PAD_LEFT);
            $student = User::where('email', "mahasiswa{$nn}a@sipafik.test")->first();
            $event = Event::where('campus_id', $next->id)->where('visibility', 'public')->where('status', 'published')->orderBy('id')->first();
            if ($student && $event) {
                EventRegistration::firstOrCreate(
                    ['event_id' => $event->id, 'user_id' => $student->id],
                    ['campus_id' => $event->campus_id, 'status' => 'registered', 'registered_at' => $anchor->copy()->subDay()]
                );
            }
        }

        $this->command?->info('Data demo modul selesai. Kegiatan: '.Event::count()
            .', Pendaftaran: '.EventRegistration::count()
            .', Laboratorium: '.Laboratory::count()
            .', Jadwal: '.LabSchedule::count()
            .', Permohonan: '.FacilityRequest::count());
    }
}
