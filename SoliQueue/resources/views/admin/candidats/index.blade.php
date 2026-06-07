@extends('layouts.admin')

@section('title', 'Gestion des Candidats - SoliQueue Admin')
@section('breadcrumb', 'Gestion Candidats')

@section('content')
<div x-data="candidatManager({{ json_encode($candidats) }})" x-init="init()" class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tighter uppercase">Gestion des Candidats</h1>
            <p class="text-sm text-slate-400 font-medium">Gérez la liste de tous les candidats inscrits</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.candidats.export') }}"
                class="py-3 px-6 bg-slate-100 text-slate-700 text-xs font-black rounded-2xl uppercase hover:bg-slate-200 transition-all flex items-center gap-2">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Exporter
            </a>
            <button @click="showImportModal = true"
                class="py-3 px-6 bg-slate-100 text-slate-700 text-xs font-black rounded-2xl uppercase hover:bg-slate-200 transition-all flex items-center gap-2">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Importer
            </button>
            <button @click="openAddModal()"
                class="py-3 px-6 bg-[#1A73E8] text-white text-xs font-black rounded-2xl uppercase hover:bg-blue-700 transition-all shadow-xl shadow-blue-100 flex items-center gap-2">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M19 8v6" />
                    <path d="M22 11h-6" />
                </svg>
                Ajouter un candidat
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-xs font-bold rounded-2xl flex items-center gap-2">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 text-xs font-bold rounded-2xl flex flex-col gap-1">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <svg class="size-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Filters Bar -->
    <div class="mb-6 relative max-w-full">
        <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
            <svg class="size-4.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8" />
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3" />
            </svg>
        </div>
        <input type="text" x-model="searchQuery"
            class="py-3 ps-12 pe-4 block w-full bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors placeholder:text-gray-400 shadow-sm"
            placeholder="Rechercher par nom, prénom ou CIN...">
    </div>

    <!-- Table Container -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="overflow-x-auto">
            <table class="w-full text-start">
                <thead class="bg-slate-50/50 border-b border-slate-100">
                    <tr>
                        <th class="ps-8 py-4 text-start text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Candidat</th>
                        <th class="px-6 py-4 text-start text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">CIN</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Score QCM</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Entretien Affectée</th>
                        <th class="px-6 py-4 text-end pe-8 text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <template x-for="candidat in paginatedCandidats" :key="candidat.id">
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="ps-8 py-5">
                                <div class="flex items-center gap-4">
                                    <template x-if="candidat.photo">
                                        <img :src="'/storage/' + candidat.photo" alt="Photo" class="size-10 rounded-xl object-cover border border-slate-200 group-hover:scale-110 transition-transform shadow-sm">
                                    </template>
                                    <template x-if="!candidat.photo">
                                        <div class="size-10 rounded-xl bg-blue-50 text-[#1A73E8] flex items-center justify-center font-black border border-blue-100 group-hover:scale-110 transition-transform shadow-sm" 
                                             x-text="(candidat.prenom[0] || '') + (candidat.nom[0] || '')"></div>
                                    </template>
                                    <div>
                                        <p class="text-sm font-medium text-slate-900" x-text="candidat.prenom + ' ' + candidat.nom"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-xs font-bold text-slate-600 uppercase" x-text="candidat.cin"></span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex items-center justify-center size-9 rounded-xl bg-slate-50 text-[#1A73E8] text-xs font-black border border-slate-200" x-text="candidat.scoreQCM"></span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <template x-if="candidat.entretien">
                                    <span class="inline-flex items-center px-3 py-1 bg-green-50 text-green-700 rounded-lg text-[10px] font-bold border border-green-200" x-text="candidat.entretien.nom"></span>
                                </template>
                                <template x-if="!candidat.entretien">
                                    <span class="inline-flex items-center px-3 py-1 bg-slate-50 text-slate-500 rounded-lg text-[10px] font-bold border border-slate-200">Non affecté</span>
                                </template>
                            </td>
                            <td class="px-6 py-5 text-end pe-8">
                                <div class="flex justify-end gap-2">
                                    <button @click="editCandidat(candidat)" class="size-8 rounded-lg bg-blue-50 border border-blue-100 text-[#1A73E8] hover:bg-[#1A73E8] hover:text-white transition-all flex items-center justify-center">
                                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" /><path d="m15 5 4 4" /></svg>
                                    </button>
                                    <button @click="deleteCandidat(candidat.id)" class="size-8 rounded-lg bg-red-50 border border-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center">
                                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18" /><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" /><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredCandidats.length === 0">
                        <td colspan="5" class="py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="size-12 opacity-50 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                <p class="text-sm font-bold uppercase tracking-widest">Aucun candidat trouvé</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="py-4 px-6 border-t border-slate-100 flex items-center justify-center gap-2" x-show="totalPages > 0">
            <button @click="prevPage()" :disabled="currentPage === 1" class="p-2 text-slate-400 hover:text-slate-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </button>
            <template x-for="page in pages" :key="page">
                <button @click="goToPage(page)" 
                    :class="{'bg-blue-600 text-white shadow-md': currentPage === page, 'text-slate-600 hover:bg-slate-50': currentPage !== page}"
                    class="size-8 flex items-center justify-center rounded-lg text-sm font-bold transition-all" x-text="page">
                </button>
            </template>
            <button @click="nextPage()" :disabled="currentPage === totalPages" class="p-2 text-slate-400 hover:text-slate-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6 6-6"/></svg>
            </button>
        </div>
    </div>

    <!-- Add Candidate Modal -->
    <div x-show="showAddCandidateModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-sm w-full h-full min-h-screen" @click.self="showAddCandidateModal = false">
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-xl max-h-[90vh] flex flex-col animate-in fade-in zoom-in duration-200 overflow-hidden">
            <!-- Header (Fixed) -->
            <div class="px-8 pt-8 sm:px-10 sm:pt-10 pb-6 relative shrink-0">
                <button @click="showAddCandidateModal = false" class="absolute top-6 right-6 p-2.5 bg-gray-100 hover:bg-gray-200 rounded-full text-gray-500 transition-colors">
                    <svg class="size-4.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
                
                <h1 class="text-3xl font-black text-gray-900 tracking-tight" x-text="candidatForm.id ? 'Modifier Candidat' : 'Ajouter un Candidat'"></h1>
                <p class="text-[11px] text-gray-400 font-black uppercase tracking-widest mt-2" x-text="candidatForm.id ? 'Mise à jour des informations' : 'Nouveau candidat QCM'"></p>
            </div>

            <form :action="candidatForm.id ? '{{ url('admin/candidats') }}/' + candidatForm.id : '{{ route('admin.candidates.store') }}'" method="POST" class="flex flex-col flex-1 overflow-hidden" enctype="multipart/form-data">
                @csrf
                <template x-if="candidatForm.id">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <!-- Scrollable Body -->
                <div class="px-8 sm:px-10 pb-4 overflow-y-auto space-y-6 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-300">
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Prénom</label>
                            <input type="text" name="prenom" x-model="candidatForm.prenom" required placeholder="Lila"
                                class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors placeholder:text-gray-400 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Nom</label>
                            <input type="text" name="nom" x-model="candidatForm.nom" required placeholder="Mansouri"
                                class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors placeholder:text-gray-400 shadow-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">CIN</label>
                        <input type="text" name="cin" x-model="candidatForm.cin" required placeholder="AB123456"
                            class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 uppercase transition-colors placeholder:text-gray-400 shadow-sm">
                    </div>
                    
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Score QCM (/100)</label>
                            <input type="number" name="scoreQCM" x-model="candidatForm.scoreQCM" min="0" max="100" step="0.1" required placeholder="85"
                                class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors placeholder:text-gray-400 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Photo (Optionnel)</label>
                            <input type="file" name="photo" accept="image/png, image/jpeg, image/jpg"
                                class="w-full py-2.5 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors shadow-sm file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        </div>
                    </div>
                </div>

                <!-- Actions Footer (Fixed) -->
                <div class="px-8 sm:px-10 py-6 shrink-0 flex gap-4 justify-end">
                    <button type="button" @click="showAddCandidateModal = false"
                        class="px-8 py-3.5 text-sm font-bold text-[#202124] bg-[#F8F9FA] hover:bg-gray-200 rounded-2xl transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                        class="px-8 py-3.5 text-sm font-bold text-white bg-[#1A73E8] hover:bg-blue-700 shadow-xl shadow-blue-200 rounded-2xl transition-all">
                        <span x-text="candidatForm.id ? 'Mettre à jour' : 'Ajouter'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-sm w-full h-full min-h-screen" @click.self="showDeleteModal = false">
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-sm animate-in fade-in zoom-in duration-200 overflow-hidden text-center p-8">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 mb-6">
                <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-xl font-black text-gray-900 mb-2">Supprimer ce candidat ?</h3>
            <p class="text-sm text-gray-500 font-medium mb-8">Cette action est irréversible. Le candidat et ses données seront effacés.</p>
            <div class="flex gap-3 justify-center">
                <button type="button" @click="showDeleteModal = false" class="px-6 py-3 text-sm font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-2xl transition-colors">Annuler</button>
                <button type="button" @click="confirmDelete()" class="px-6 py-3 text-sm font-bold text-white bg-red-600 hover:bg-red-700 shadow-lg shadow-red-200 rounded-2xl transition-all">Oui, supprimer</button>
            </div>
        </div>
    </div>

    <!-- Import Candidates Modal -->
    <div x-show="showImportModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm w-full h-full min-h-screen" @click.self="showImportModal = false">
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md animate-in fade-in zoom-in duration-200 overflow-hidden">
            <div class="px-8 pt-8 pb-4 relative shrink-0">
                <button @click="showImportModal = false" class="absolute top-6 right-6 p-2.5 bg-gray-100 hover:bg-gray-200 rounded-full text-gray-500 transition-colors">
                    <svg class="size-4.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">Importer les Candidats</h2>
                <p class="text-[11px] text-gray-400 font-black uppercase tracking-widest mt-2">Formats supportés : Excel (.xlsx, .xls) ou CSV</p>
            </div>

            <form action="{{ route('admin.candidats.import') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                @csrf
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-900 uppercase tracking-widest">Sélectionner un fichier</label>
                    <input type="file" name="file" accept=".csv,.xlsx,.xls" required
                        class="w-full py-2.5 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors shadow-sm file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                </div>
                
                <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl space-y-2 text-[11px] font-medium text-slate-500 leading-relaxed">
                    <p class="font-black text-slate-700 uppercase tracking-wider text-[9px] mb-1">Structure attendue (Excel ou CSV) :</p>
                    <p>La première ligne (en-tête) doit contenir :</p>
                    <code class="block bg-slate-200 p-2 rounded text-slate-800 font-bold overflow-x-auto whitespace-nowrap">CIN;Nom;Prenom;Score QCM</code>
                    <p>Pour le CSV, le séparateur point-virgule (;) ou virgule (,) est détecté automatiquement.</p>
                </div>

                <div class="flex gap-4 justify-end pt-4">
                    <button type="button" @click="showImportModal = false"
                        class="px-6 py-3 text-xs font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                        class="px-6 py-3 text-xs font-bold text-white bg-[#1A73E8] hover:bg-blue-700 shadow-xl shadow-blue-200 rounded-xl transition-all">
                        Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



@endsection

