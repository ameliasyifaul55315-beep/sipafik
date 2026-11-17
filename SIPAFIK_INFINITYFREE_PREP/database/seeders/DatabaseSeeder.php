<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Semua seeder di bawah ini IDEMPOTENT (aman dijalankan berulang, tidak menimpa/menghapus data).
 * Untuk hosting PRODUKSI cukup jalankan SuperAdminSeeder saja:
 *   php artisan db:seed --class=SuperAdminSeeder
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,
            SipafikDemoDataSeeder::class,
            DemoUsersSeeder::class,
            SipafikModuleDemoSeeder::class,
        ]);
    }
}
