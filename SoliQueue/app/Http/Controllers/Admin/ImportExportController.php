<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ImportExportService;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportExportController extends Controller
{
    protected $importExportService;

    public function __construct(ImportExportService $importExportService)
    {
        $this->importExportService = $importExportService;
    }

    public function exportCandidats(): StreamedResponse
    {
        $spreadsheet = $this->importExportService->exportCandidats();
        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="export_candidats_' . date('Y-m-d') . '.xlsx"');

        return $response;
    }

    public function importCandidats(Request $request)
    {
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_INI_SIZE) {
            $maxSize = ini_get('upload_max_filesize');
            return back()->withErrors(['file' => "Le fichier est trop volumineux. La taille maximale autorisée par PHP est de {$maxSize}. Veuillez compresser les images ou modifier 'upload_max_filesize' dans votre php.ini."]);
        }

        if ($request->isMethod('post') && empty($_POST) && empty($_FILES) && $request->headers->get('content-length') > 0) {
            $maxPost = ini_get('post_max_size');
            return back()->withErrors(['file' => "La requête est trop volumineuse (limite post_max_size de {$maxPost} dépassée)."]);
        }

        $request->validate([
            'file' => 'required|file'
        ], [
            'file.required' => 'Veuillez sélectionner un fichier à importer.',
            'file.file' => 'Le fichier n\'a pas pu être téléchargé.',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, ['csv', 'txt', 'xlsx', 'xls'])) {
            return back()->withErrors(['file' => 'Format de fichier non supporté. Veuillez importer un fichier CSV, TXT, XLSX ou XLS.']);
        }

        try {
            $counts = $this->importExportService->importCandidats($path, $extension);
            $importedCount = $counts['imported'];
            $updatedCount = $counts['updated'];

            return back()->with('success', "Génial ! La base de données a été mise à jour : $importedCount nouveaux candidats ont été ajoutés et $updatedCount fiches candidats ont été actualisées avec succès.");
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Erreur lors du traitement du fichier : ' . $e->getMessage()]);
        }
    }

    public function exportEntretiens(): StreamedResponse
    {
        $spreadsheet = $this->importExportService->exportEntretiens();
        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="export_entretiens_' . date('Y-m-d') . '.xlsx"');

        return $response;
    }
}

