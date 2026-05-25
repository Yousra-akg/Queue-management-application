<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formateur;
use App\Services\FormateurService;
use Illuminate\Http\Request;

class FormateurManagementController extends Controller
{
    protected $formateurService;

    public function __construct(FormateurService $formateurService)
    {
        $this->formateurService = $formateurService;
    }

    public function index()
    {
        $formateurs = $this->formateurService->getAllWithUsers();
        return view('admin.formateurs.index', compact('formateurs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'codeInterne' => 'required|string|max:50|unique:formateurs,codeInterne',
            'specialite' => 'required|string|max:255',
        ]);

        try {
            $this->formateurService->createFormateur($request->only([
                'nom', 'email', 'password', 'codeInterne', 'specialite'
            ]));
            return redirect()->back()->with('success', 'Formateur créé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la création : ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, Formateur $formateur)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $formateur->user_id,
            'codeInterne' => 'required|string|max:50|unique:formateurs,codeInterne,' . $formateur->id,
            'specialite' => 'required|string|max:255',
        ]);

        try {
            $this->formateurService->updateFormateur($formateur->id, $request->only([
                'nom', 'email', 'password', 'codeInterne', 'specialite'
            ]));
            return redirect()->back()->with('success', 'Formateur mis à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()]);
        }
    }

    public function destroy(Formateur $formateur)
    {
        try {
            $this->formateurService->deleteFormateur($formateur->id);
            return redirect()->back()->with('success', 'Formateur supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la suppression : ' . $e->getMessage()]);
        }
    }
}
