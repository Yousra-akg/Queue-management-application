<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Entretien;

class EntretienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = fopen(database_path('data/entretiens.csv'), 'r');
        $header = fgetcsv($file);
        while (($row = fgetcsv($file)) !== FALSE) {
            $data = array_combine($header, $row);
            Entretien::create($data);
        }
        fclose($file);
    }
}


