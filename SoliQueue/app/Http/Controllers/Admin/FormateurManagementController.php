<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Formateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class FormateurManagementController extends Controller
{
    public function index()
    {
        $formateurs = Formateur::with('user')->orderBy('id', 'desc')->get();
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

        DB::transaction(function () use ($request) {
            $user = User::create([
                'nom' => $request->nom,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            Formateur::create([
                'user_id' => $user->id,
                'codeInterne' => $request->codeInterne,
                'specialite' => $request->specialite,
            ]);
        });

        return redirect()->back()->with('success', 'Formateur créé avec succès.');
    }

    public function update(Request $request, Formateur $formateur)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $formateur->user_id,
            'codeInterne' => 'required|string|max:50|unique:formateurs,codeInterne,' . $formateur->id,
            'specialite' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $formateur) {
            $formateur->user->update([
                'nom' => $request->nom,
                'email' => $request->email,
            ]);

            if ($request->filled('password')) {
                $formateur->user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            $formateur->update([
                'codeInterne' => $request->codeInterne,
                'specialite' => $request->specialite,
            ]);
        });

        return redirect()->back()->with('success', 'Formateur mis à jour avec succès.');
    }

    public function destroy(Formateur $formateur)
    {
        DB::transaction(function () use ($formateur) {
            $user = $formateur->user;
            $formateur->delete();
            $user->delete();
        });

        return redirect()->back()->with('success', 'Formateur supprimé avec succès.');
    }
}
