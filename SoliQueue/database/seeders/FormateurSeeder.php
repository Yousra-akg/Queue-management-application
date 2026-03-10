<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Formateur;

class FormateurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    $file = fopen(database_path('data/formateurs.csv'), 'r');
    $header = fgetcsv($file);
    while (($row = fgetcsv($file)) !== FALSE) {
        $data = array_combine($header, $row);
        Formateur::create($data);
    }
    fclose($file);
    }
}
