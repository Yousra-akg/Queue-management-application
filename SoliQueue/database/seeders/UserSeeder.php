<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    $file = fopen(database_path('data/users.csv'), 'r');
    $header = fgetcsv($file);
    while (($row = fgetcsv($file)) !== FALSE) {
        $data = array_combine($header, $row);
        \App\Models\User::create($data);
    }
    fclose($file);
    }
}
