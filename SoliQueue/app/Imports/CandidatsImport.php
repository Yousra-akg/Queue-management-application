<?php

namespace App\Imports;

use App\Models\Candidat;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class CandidatsImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Try to match common headers
        $nom = $row['nom'] ?? $row['last_name'] ?? $row['name'] ?? null;
        $prenom = $row['prenom'] ?? $row['first_name'] ?? '';
        $cin = $row['cin'] ?? $row['id'] ?? null;
        
        $scoreQCM = 0;
        foreach ($row as $key => $value) {
            $k = strtolower(trim($key));
            if (str_contains($k, 'qcm') || str_contains($k, 'score') || str_contains($k, 'note') || str_contains($k, 'resultat')) {
                $scoreQCM = $value;
                break;
            }
        }

        if (!$cin || !$nom) {
            return null; // Skip invalid rows
        }

        // Avoid duplicate CIN
        $existing = Candidat::where('cin', $cin)->first();
        if ($existing) {
            return null;
        }

        return new Candidat([
            'nom'      => $nom,
            'prenom'   => $prenom,
            'cin'      => $cin,
            'scoreQCM' => is_numeric($scoreQCM) ? $scoreQCM : 0,
        ]);
    }
}
