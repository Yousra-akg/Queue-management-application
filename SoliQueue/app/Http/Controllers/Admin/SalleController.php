<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalleController extends Controller
{
    public function index()
    {
        $salles = \App\Models\Salle::all();
        return view('admin.salles.index', compact('salles'));
    }

    public function store(Request $request)
    {
        $request->validate(['nom' => 'required|string|max:255|unique:salles,nom']);
        \App\Models\Salle::create($request->all());
        return redirect()->back()->with('success', 'Salle ajoutée avec succès.');
    }

    public function destroy(\App\Models\Salle $salle)
    {
        $salle->delete();
        return redirect()->back()->with('success', 'Salle supprimée avec succès.');
    }
}
