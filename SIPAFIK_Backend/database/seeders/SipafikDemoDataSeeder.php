<?php

namespace Database\Seeders;

use App\Models\Campus;
use App\Models\User;
use Illuminate\Database\Seeder;

class SipafikDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. DATA KAMPUS DEMO
        $campuses = [
            [
                'name' => 'Kampus Demo Aceh Utara',
                'username' => 'demo_aceh_utara',
                'email' => 'aceh.utara@sipafik.test',
                'status' => 'approved',
            ],
            [
                'name' => 'Kampus Demo Pidie Jaya',
                'username' => 'demo_pidie_jaya',
                'email' => 'pidiejaya@sipafik.test',
                'status' => 'approved',
            ],
            [
                'name' => 'Kampus Demo Bireuen',
                'username' => 'demo_bireuen',
                'email' => 'bireuen@sipafik.test',
                'status' => 'approved',
            ],
            [
                'name' => 'Kampus Demo Banda Aceh',
                'username' => 'demo_banda_aceh',
                'email' => 'bandaaceh@sipafik.test',
                'status' => 'approved',
            ],
            [
                'name' => 'Kampus Demo Aceh Besar',
                'username' => 'demo_aceh_besar',
                'email' => 'acehbesar@sipafik.test',
                'status' => 'approved',
            ],
            [
                'name' => 'Kampus Demo Pidie',
                'username' => 'demo_pidie',
                'email' => 'pidie@sipafik.test',
                'status' => 'pending',
            ],
            [
                'name' => 'Kampus Demo Aceh Barat',
                'username' => 'demo_aceh_barat',
                'email' => 'acehbarat@sipafik.test',
                'status' => 'pending',
            ],
            [
                'name' => 'Kampus Demo Aceh Tengah',
                'username' => 'demo_aceh_tengah',
                'email' => 'acehtengah@sipafik.test',
                'status' => 'pending',
            ],
            [
                'name' => 'Kampus Demo Lhokseumawe',
                'username' => 'demo_lhokseumawe',
                'email' => 'lhokseumawe@sipafik.test',
                'status' => 'rejected',
            ],
        ];

        foreach ($campuses as $index => $data) {
            if (Campus::count() >= 10) {
                break;
            }

            Campus::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'phone' => '08' . str_pad((string) (1100000000 + $index), 10, '0', STR_PAD_LEFT),
                    'address' => 'Alamat Demo ' . $data['name'] . ', Aceh, Indonesia',
                    'responsible_person' => 'Penanggung Jawab Demo ' . ($index + 1),
                    'identity_document' => null,
                    'status' => $data['status'],
                    'rejection_reason' => $data['status'] === 'rejected'
                        ? 'Dokumen persyaratan demo belum memenuhi ketentuan.'
                        : null,
                ]
            );
        }

        // Ambil maksimal 10 kampus untuk akun demo.
        $campusList = Campus::orderBy('id')->take(10)->get();

        // 2. DATA PENGGUNA DEMO
        $password = 'DemoSipafik2026!';

        foreach ($campusList as $index => $campus) {

            $number = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);

            $accounts = [
                [
                    'name' => 'Organisasi Demo ' . $number,
                    'role' => 'organisasi',
                    'username' => 'organisasi_demo_' . $number,
                    'email' => 'organisasi' . $number . '@sipafik.test',
                ],
                [
                    'name' => 'Petugas Fasilitas ' . $number,
                    'role' => 'petugas_fasilitas',
                    'username' => 'petugas_demo_' . $number,
                    'email' => 'petugas' . $number . '@sipafik.test',
                ],
                [
                    'name' => 'Dosen Demo ' . $number,
                    'role' => 'dosen',
                    'username' => 'dosen_demo_' . $number,
                    'email' => 'dosen' . $number . '@sipafik.test',
                ],
                [
                    'name' => 'Mahasiswa Demo ' . $number . 'A',
                    'role' => 'mahasiswa',
                    'username' => 'mahasiswa_demo_' . $number . 'a',
                    'email' => 'mahasiswa' . $number . 'a@sipafik.test',
                ],
                [
                    'name' => 'Mahasiswa Demo ' . $number . 'B',
                    'role' => 'mahasiswa',
                    'username' => 'mahasiswa_demo_' . $number . 'b',
                    'email' => 'mahasiswa' . $number . 'b@sipafik.test',
                ],
            ];

            foreach ($accounts as $account) {
                User::firstOrCreate(
                    ['email' => $account['email']],
                    [
                        'name' => $account['name'],
                        'username' => $account['username'],
                        'password' => $password,
                        'role' => $account['role'],
                        'campus_id' => $campus->id,
                        'is_active' => true,
                    ]
                );
            }
        }

        // 3. LAPORAN HASIL SEEDER
        $this->command->info('Data demo SIPAFIK selesai diperiksa.');
        $this->command->info('Total kampus: ' . Campus::count());

        $this->command->info(
            'Disetujui: ' . Campus::where('status', 'approved')->count()
        );

        $this->command->info(
            'Menunggu: ' . Campus::where('status', 'pending')->count()
        );

        $this->command->info(
            'Ditolak: ' . Campus::where('status', 'rejected')->count()
        );

        $this->command->info(
            'Total organisasi: ' . User::where('role', 'organisasi')->count()
        );

        $this->command->info(
            'Total petugas fasilitas: ' . User::where('role', 'petugas_fasilitas')->count()
        );

        $this->command->info('Total pengguna: ' . User::count());
    }
}
