<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventaris;

class InventarisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inventaries = [
            ['judul' => 'Camera', 'specification' => 'canggih', 'condition' => baik, 'status' => 'Tersedia'],
            ['judul' => 'Gitar', 'specification' => 'Terhubung_Blueetooth', 'condition' => rusak_ringan, 'status' => 'dipinjam'],
            ['judul' => 'Mic', 'specification' => 'Tahan_banting', 'condition' => rusak_berat, 'status' => 'dalam_perbaikan'],
            ['judul' => 'Television', 'specification' => '14_inchi', 'condition' => baik, 'status' => 'tersedia'],

        ];

        foreach ($inventariss as $inventaris) {
            Book::create($inventaris);
        }
    }
}
