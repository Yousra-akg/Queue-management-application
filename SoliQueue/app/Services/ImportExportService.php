<?php

namespace App\Services;

use App\Models\Candidat;
use App\Models\Entretien;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;

class ImportExportService
{
    public function exportCandidats(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Candidats');

        // En-têtes
        $sheet->setCellValue('A1', 'CIN');
        $sheet->setCellValue('B1', 'Nom');
        $sheet->setCellValue('C1', 'Prenom');
        $sheet->setCellValue('D1', 'Score QCM');
        $sheet->setCellValue('E1', 'Entretien');
        $sheet->setCellValue('F1', 'Est Present');
        $sheet->setCellValue('G1', 'Photo');

        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        $rowNumber = 2;
        $candidats = Candidat::with('entretien')->get();
        foreach ($candidats as $candidat) {
            $sheet->setCellValue('A' . $rowNumber, $candidat->cin);
            $sheet->setCellValue('B' . $rowNumber, $candidat->nom);
            $sheet->setCellValue('C' . $rowNumber, $candidat->prenom);
            $sheet->setCellValue('D' . $rowNumber, $candidat->scoreQCM);
            $sheet->setCellValue('E' . $rowNumber, $candidat->entretien ? $candidat->entretien->nom : 'Non affecté');
            $sheet->setCellValue('F' . $rowNumber, $candidat->is_present ? 'Oui' : 'Non');
            
            if ($candidat->photo) {
                $photoPath = public_path('storage/' . $candidat->photo);
                if (!file_exists($photoPath) && filter_var($candidat->photo, FILTER_VALIDATE_URL)) {
                    try {
                        $tempFile = tempnam(sys_get_temp_dir(), 'img');
                        file_put_contents($tempFile, file_get_contents($candidat->photo));
                        $photoPath = $tempFile;
                    } catch (\Exception $e) {
                        $photoPath = null;
                    }
                }
                
                if ($photoPath && file_exists($photoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Photo');
                    $drawing->setDescription('Photo');
                    $drawing->setPath($photoPath);
                    $drawing->setHeight(36);
                    $drawing->setCoordinates('G' . $rowNumber);
                    $drawing->setWorksheet($sheet);
                    
                    $sheet->getRowDimension($rowNumber)->setRowHeight(40);
                }
            }
            $rowNumber++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('G')->setWidth(18);

        return $spreadsheet;
    }

    public function importCandidats(string $path, string $extension): array
    {
        $importedCount = 0;
        $updatedCount = 0;

        $normalizeHeaders = function($headers) {
            return array_map(function($h) {
                $h = strtolower(trim(str_replace(['"', "'"], '', $h)));
                $h = str_replace([' ', '_', '-', 'é', 'è', 'ê', 'à'], ['', '', '', 'e', 'e', 'e', 'a'], $h);
                return $h;
            }, $headers);
        };

        DB::beginTransaction();
        try {
            if (in_array($extension, ['xlsx', 'xls'])) {
                $spreadsheet = IOFactory::load($path);
                $worksheet = $spreadsheet->getSheet(0);
                $rows = $worksheet->toArray();

                if (empty($rows)) {
                    throw new \Exception('Le fichier Excel est vide.');
                }

                $headers = array_shift($rows);
                $headers = $normalizeHeaders($headers);

                $hasScore = false;
                foreach ($headers as $h) {
                    if (str_contains($h, 'score') || str_contains($h, 'qcm') || str_contains($h, 'note')) {
                        $hasScore = true;
                        break;
                    }
                }

                if (!in_array('cin', $headers) || !in_array('nom', $headers) || !in_array('prenom', $headers) || !$hasScore) {
                    throw new \Exception('Structure du fichier invalide. Les colonnes obligatoires (CIN, Nom, Prenom, Score QCM) sont manquantes.');
                }

                $drawingsMap = [];
                foreach ($worksheet->getDrawingCollection() as $drawing) {
                    $coord = $drawing->getCoordinates();
                    if (preg_match('/([A-Z]+)(\d+)/', $coord, $matches)) {
                        $rowNum = intval($matches[2]);
                        $imageBytes = null;
                        $ext = 'png';
                        if ($drawing instanceof Drawing) {
                            $drawingPath = $drawing->getPath();
                            if (strpos($drawingPath, 'zip://') === 0 || file_exists($drawingPath)) {
                                $imageBytes = @file_get_contents($drawingPath);
                                $ext = strtolower(pathinfo($drawingPath, PATHINFO_EXTENSION) ?: 'png');
                                if (strpos($ext, '#') !== false) {
                                    $ext = explode('#', $ext)[0];
                                }
                                $ext = preg_replace('/[^a-zA-Z0-9]/', '', $ext) ?: 'png';
                            }
                        } elseif ($drawing instanceof MemoryDrawing) {
                            ob_start();
                            call_user_func($drawing->getRenderingFunction(), $drawing->getImageResource());
                            $imageBytes = ob_get_contents();
                            ob_end_clean();
                            switch ($drawing->getMimeType()) {
                                case MemoryDrawing::MIMETYPE_PNG:
                                    $ext = 'png';
                                    break;
                                case MemoryDrawing::MIMETYPE_GIF:
                                    $ext = 'gif';
                                    break;
                                case MemoryDrawing::MIMETYPE_JPEG:
                                    $ext = 'jpg';
                                    break;
                            }
                        }
                        if ($imageBytes) {
                            $filename = 'candidats/' . uniqid() . '_' . $rowNum . '.' . $ext;
                            Storage::disk('public')->put($filename, $imageBytes);
                            $drawingsMap[$rowNum] = $filename;
                        }
                    }
                }

                $rowNumber = 2;
                foreach ($rows as $row) {
                    if (empty($row) || count($row) < 3) {
                        $rowNumber++;
                        continue;
                    }

                    $data = array_combine(array_slice($headers, 0, count($row)), $row);

                    $cin = trim($data['cin'] ?? '');
                    $nom = trim($data['nom'] ?? '');
                    $prenom = trim($data['prenom'] ?? '');

                    $scoreQCM = 0;
                    foreach ($data as $key => $val) {
                        if (str_contains($key, 'score') || str_contains($key, 'qcm') || str_contains($key, 'note')) {
                            $scoreQCM = floatval(str_replace(',', '.', trim($val)));
                            break;
                        }
                    }

                    $photoPath = $drawingsMap[$rowNumber] ?? null;
                    if (!$photoPath) {
                        $photoVal = trim($data['photo'] ?? $data['image'] ?? $data['avatar'] ?? '');
                        if (!empty($photoVal)) {
                            if (filter_var($photoVal, FILTER_VALIDATE_URL)) {
                                try {
                                    $imageContent = file_get_contents($photoVal);
                                    if ($imageContent !== false) {
                                        $filename = 'candidats/' . uniqid() . '_' . basename(parse_url($photoVal, PHP_URL_PATH));
                                        Storage::disk('public')->put($filename, $imageContent);
                                        $photoPath = $filename;
                                    }
                                } catch (\Exception $e) {
                                }
                            } else {
                                $photoPath = $photoVal;
                            }
                        }
                    }

                    if (empty($cin) || empty($nom) || empty($prenom)) {
                        $rowNumber++;
                        continue;
                    }

                    $candidat = Candidat::where('cin', $cin)->first();

                    if ($candidat) {
                        $updateData = [
                            'nom' => $nom,
                            'prenom' => $prenom,
                            'scoreQCM' => $scoreQCM
                        ];
                        if ($photoPath) {
                            $updateData['photo'] = $photoPath;
                        }
                        $candidat->update($updateData);
                        $updatedCount++;
                    } else {
                        Candidat::create([
                            'cin' => $cin,
                            'nom' => $nom,
                            'prenom' => $prenom,
                            'scoreQCM' => $scoreQCM,
                            'photo' => $photoPath,
                            'is_present' => false
                        ]);
                        $importedCount++;
                    }
                    $rowNumber++;
                }
            } else {
                $handle = fopen($path, 'r');
                if ($handle === false) {
                    throw new \Exception('Impossible d\'ouvrir le fichier.');
                }

                $firstLine = fgets($handle);
                $numSemicolons = substr_count($firstLine, ';');
                $numCommas = substr_count($firstLine, ',');
                $delimiter = $numSemicolons >= $numCommas ? ';' : ',';

                rewind($handle);
                $bom = fread($handle, 3);
                if ($bom !== "\xEF\xBB\xBF") {
                    rewind($handle);
                }

                $headers = fgetcsv($handle, 0, $delimiter);
                if (!$headers) {
                    fclose($handle);
                    throw new \Exception('Le fichier CSV est vide ou mal formaté.');
                }

                $headers = $normalizeHeaders($headers);

                $hasScore = false;
                foreach ($headers as $h) {
                    if (str_contains($h, 'score') || str_contains($h, 'qcm') || str_contains($h, 'note')) {
                        $hasScore = true;
                        break;
                    }
                }

                if (!in_array('cin', $headers) || !in_array('nom', $headers) || !in_array('prenom', $headers) || !$hasScore) {
                    fclose($handle);
                    throw new \Exception('Structure du fichier invalide. Les colonnes obligatoires (CIN, Nom, Prenom, Score QCM) sont manquantes.');
                }

                while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                    if (count($row) < 3) continue;

                    $data = array_combine(array_slice($headers, 0, count($row)), $row);

                    $cin = trim($data['cin'] ?? '');
                    $nom = trim($data['nom'] ?? '');
                    $prenom = trim($data['prenom'] ?? '');

                    $scoreQCM = 0;
                    foreach ($data as $key => $val) {
                        if (str_contains($key, 'score') || str_contains($key, 'qcm') || str_contains($key, 'note')) {
                            $scoreQCM = floatval(str_replace(',', '.', trim($val)));
                            break;
                        }
                    }

                    $photoPath = null;
                    $photoVal = trim($data['photo'] ?? $data['image'] ?? $data['avatar'] ?? '');
                    if (!empty($photoVal)) {
                        if (filter_var($photoVal, FILTER_VALIDATE_URL)) {
                            try {
                                $imageContent = file_get_contents($photoVal);
                                if ($imageContent !== false) {
                                    $filename = 'candidats/' . uniqid() . '_' . basename(parse_url($photoVal, PHP_URL_PATH));
                                    Storage::disk('public')->put($filename, $imageContent);
                                    $photoPath = $filename;
                                }
                            } catch (\Exception $e) {
                            }
                        } else {
                            $photoPath = $photoVal;
                        }
                    }

                    if (empty($cin) || empty($nom) || empty($prenom)) {
                        continue;
                    }

                    $candidat = Candidat::where('cin', $cin)->first();

                    if ($candidat) {
                        $updateData = [
                            'nom' => $nom,
                            'prenom' => $prenom,
                            'scoreQCM' => $scoreQCM
                        ];
                        if ($photoPath) {
                            $updateData['photo'] = $photoPath;
                        }
                        $candidat->update($updateData);
                        $updatedCount++;
                    } else {
                        Candidat::create([
                            'cin' => $cin,
                            'nom' => $nom,
                            'prenom' => $prenom,
                            'scoreQCM' => $scoreQCM,
                            'photo' => $photoPath,
                            'is_present' => false
                        ]);
                        $importedCount++;
                    }
                }
                fclose($handle);
            }

            DB::commit();

            return [
                'imported' => $importedCount,
                'updated' => $updatedCount
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function exportEntretiens(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Entretiens');

        $sheet->setCellValue('A1', 'Nom de la entretien');
        $sheet->setCellValue('B1', 'Date Entretien');
        $sheet->setCellValue('C1', 'Heure Debut');
        $sheet->setCellValue('D1', 'Heure Fin');
        $sheet->setCellValue('E1', 'Capacite Max');
        $sheet->setCellValue('F1', 'Code Presence');
        $sheet->setCellValue('G1', 'Statut');
        $sheet->setCellValue('H1', 'Nombre Candidats');

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        $rowNumber = 2;
        $entretiens = Entretien::withCount('candidats')->get();
        foreach ($entretiens as $entretien) {
            $sheet->setCellValue('A' . $rowNumber, $entretien->nom);
            $sheet->setCellValue('B' . $rowNumber, $entretien->dateEntretien);
            $sheet->setCellValue('C' . $rowNumber, $entretien->heureDebut);
            $sheet->setCellValue('D' . $rowNumber, $entretien->heureFin);
            $sheet->setCellValue('E' . $rowNumber, $entretien->capaciteMax);
            $sheet->setCellValue('F' . $rowNumber, $entretien->codePresence);
            $sheet->setCellValue('G' . $rowNumber, $entretien->statut);
            $sheet->setCellValue('H' . $rowNumber, $entretien->candidats_count);
            $rowNumber++;
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }
}

