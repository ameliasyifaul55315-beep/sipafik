<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['Tatia Alza', 'tatia.alza.demo@sipafik.test', 'tatia_demo', 'mahasiswa'],
            ['Cipa', 'cipa.demo@sipafik.test', 'cipa_demo', 'mahasiswa'],
            ['Aulia Putri', 'aulia.demo@sipafik.test', 'aulia_demo', 'mahasiswa'],
            ['Nabila Zahra', 'nabila.demo@sipafik.test', 'nabila_demo', 'mahasiswa'],
            ['Rani Safitri', 'rani.demo@sipafik.test', 'rani_demo', 'mahasiswa'],
            ['Dinda Amelia', 'dinda.demo@sipafik.test', 'dinda_demo', 'mahasiswa'],
            ['Fajar Ramadhan', 'fajar.demo@sipafik.test', 'fajar_demo', 'mahasiswa'],
            ['Rizky Maulana', 'rizky.demo@sipafik.test', 'rizky_demo', 'mahasiswa'],
            ['Nadia Putri', 'nadia.demo@sipafik.test', 'nadia_demo', 'mahasiswa'],
            ['BEM UNIGHA', 'bem.demo@sipafik.test', 'bem_unigha', 'organisasi'],
            ['Himpunan Teknik Informatika', 'hima.demo@sipafik.test', 'hima_ti', 'organisasi'],
            ['Rahmat Hidayat', 'rahmat.demo@sipafik.test', 'rahmat_demo', 'dosen'],
            ['Siti Nurhaliza', 'siti.demo@sipafik.test', 'siti_demo', 'dosen'],
            ['Andi Saputra', 'andi.demo@sipafik.test', 'andi_demo', 'petugas_fasilitas'],
        ];

        foreach ($accounts as [$name, $email, $username, $role]) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'username' => $username,
                    'password' => 'DemoSipafik2026!',
                    'role' => $role,
                    'campus_id' => 1,
                    'is_active' => true,
                ]
            );
        }

        $total = User::count();

        $this->command->info('14 akun demo berhasil diperiksa/dibuat.');
        $this->command->info("Total pengguna sekarang: {$total}");
    }
}
