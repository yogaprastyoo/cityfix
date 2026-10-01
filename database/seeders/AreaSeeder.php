<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            'Asrama Nusantara', 'Asrama Putra', 'Masjid Utama', 'Gedung A', 'Gedung B',
            'Gedung SMP', 'Gedung IIS', 'Asrama Putri A', 'Asrama Putri B', 'Asrama Putri C',
        ];

        foreach ($areas as $area) {
            Area::firstOrCreate(['name' => $area]);
        }
    }
}
