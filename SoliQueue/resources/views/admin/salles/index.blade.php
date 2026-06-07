@extends('layouts.admin')

@section('header')
<div class="flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestion des Salles</h1>
</div>
@endsection

@section('content')
<div class="flex flex-col gap-6">
    <!-- Formulaire d'ajout -->
    <div class="bg-white dark:bg-neutral-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ajouter une Salle</h2>
        <form action="{{ route('admin.salles.store') }}" method="POST" class="flex gap-4 items-end">
            @csrf
            <div class="flex-1">
                <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Nom de la salle</label>
                <input type="text" name="nom" id="nom" class="py-2.5 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400" placeholder="Ex: Salle de Réunion A" required>
            </div>
            <button type="submit" class="py-2.5 px-6 inline-flex justify-center items-center gap-2 rounded-lg border border-transparent font-semibold bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all text-sm">
                Ajouter
            </button>
        </form>
    </div>

    <!-- Liste des salles -->
    <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
            <thead class="bg-gray-50 dark:bg-neutral-900">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                @foreach($salles as $salle)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                        {{ $salle->nom }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <form action="{{ route('admin.salles.destroy', $salle->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @if($salles->isEmpty())
                <tr>
                    <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-neutral-400">Aucune salle n'a été ajoutée.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
