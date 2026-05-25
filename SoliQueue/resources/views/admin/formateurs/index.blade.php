@extends('layouts.admin')

@section('title', 'Gestion des Formateurs - SoliQueue Admin')
@section('breadcrumb', 'Gestion Formateurs')

@section('content')
<div x-data="formateurManager({{ json_encode($formateurs) }})" x-init="init()" class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tighter uppercase">Gestion des Formateurs</h1>
            <p class="text-sm text-slate-400 font-medium">Gérez la liste de tous les formateurs</p>
        </div>
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
            Ajouter un formateur
        </button>
    </div>

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
            placeholder="Rechercher par nom, email, spécialité ou code interne...">
    </div>

    <!-- Table Container -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="overflow-x-auto">
            <table class="w-full text-start">
                <thead class="bg-slate-50/50 border-b border-slate-100">
                    <tr>
                        <th class="ps-8 py-4 text-start text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Formateur</th>
                        <th class="px-6 py-4 text-start text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Spécialité</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Code Interne</th>
                        <th class="px-6 py-4 text-end pe-8 text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <template x-for="f in paginatedFormateurs" :key="f.id">
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="ps-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="size-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black border border-indigo-100 group-hover:scale-110 transition-transform" 
                                         x-text="f.user.nom[0]"></div>
                                    <div>
                                        <p class="text-sm font-black text-slate-900 uppercase tracking-tighter" x-text="f.user.nom"></p>
                                        <p class="text-[10px] text-slate-400 font-bold" x-text="f.user.email"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="px-3 py-1 bg-slate-50 text-slate-600 rounded-lg text-[10px] font-bold border border-slate-200 uppercase tracking-widest" x-text="f.specialite"></span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1 bg-blue-50 text-[#1A73E8] text-[10px] font-black border border-blue-100 rounded-lg" x-text="f.codeInterne"></span>
                            </td>
                            <td class="px-6 py-5 text-end pe-8">
                                <div class="flex justify-end gap-2">
                                    <button @click="editFormateur(f)" class="size-8 rounded-lg bg-blue-50 border border-blue-100 text-[#1A73E8] hover:bg-[#1A73E8] hover:text-white transition-all flex items-center justify-center">
                                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" /><path d="m15 5 4 4" /></svg>
                                    </button>
                                    <button @click="deleteFormateur(f.id)" class="size-8 rounded-lg bg-red-50 border border-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center">
                                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18" /><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" /><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredFormateurs.length === 0">
                        <td colspan="4" class="py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="size-12 opacity-50 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                <p class="text-sm font-bold uppercase tracking-widest">Aucun formateur trouvé</p>
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

    <!-- Add Formateur Modal -->
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-sm w-full h-full min-h-screen" @click.self="showAddModal = false">
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-xl max-h-[90vh] flex flex-col animate-in fade-in zoom-in duration-200 overflow-hidden">
            <!-- Header (Fixed) -->
            <div class="px-8 pt-8 sm:px-10 sm:pt-10 pb-6 relative shrink-0">
                <button @click="showAddModal = false" class="absolute top-6 right-6 p-2.5 bg-gray-100 hover:bg-gray-200 rounded-full text-gray-500 transition-colors">
                    <svg class="size-4.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
                
                <h1 class="text-3xl font-black text-gray-900 tracking-tight" x-text="formateurForm.id ? 'Modifier Formateur' : 'Ajouter un Formateur'"></h1>
                <p class="text-[11px] text-gray-400 font-black uppercase tracking-widest mt-2" x-text="formateurForm.id ? 'Mise à jour des informations' : 'Nouveau formateur dans le système'"></p>
            </div>

            <form :action="formateurForm.id ? '{{ url('admin/formateurs') }}/' + formateurForm.id : '{{ route('admin.formateurs.store') }}'" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <template x-if="formateurForm.id">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <!-- Scrollable Body -->
                <div class="px-8 sm:px-10 pb-4 overflow-y-auto space-y-6 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-300">
                    <div>
                        <label class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Nom Complet</label>
                        <input type="text" name="nom" x-model="formateurForm.nom" required placeholder="Ahmed Amrani"
                            class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors placeholder:text-gray-400 shadow-sm">
                    </div>
                    
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Email</label>
                            <input type="email" name="email" x-model="formateurForm.email" required placeholder="ahmed@solicode.co"
                                class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors placeholder:text-gray-400 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Mot de passe</label>
                            <input type="password" name="password" :required="!formateurForm.id" placeholder="••••••••"
                                class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors placeholder:text-gray-400 shadow-sm">
                            <template x-if="formateurForm.id">
                                <p class="text-[9px] text-slate-400 mt-1 uppercase font-bold italic">Laissez vide pour conserver l'actuel</p>
                            </template>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Spécialité</label>
                            <input type="text" name="specialite" x-model="formateurForm.specialite" required placeholder="Développement Mobile"
                                class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors placeholder:text-gray-400 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Code Interne</label>
                            <input type="text" name="codeInterne" x-model="formateurForm.codeInterne" required placeholder="FORM-001"
                                class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors placeholder:text-gray-400 shadow-sm">
                        </div>
                    </div>
                </div>

                <!-- Actions Footer (Fixed) -->
                <div class="px-8 sm:px-10 py-6 shrink-0 flex gap-4 justify-end">
                    <button type="button" @click="showAddModal = false"
                        class="px-8 py-3.5 text-sm font-bold text-[#202124] bg-[#F8F9FA] hover:bg-gray-200 rounded-2xl transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                        class="px-8 py-3.5 text-sm font-bold text-white bg-[#1A73E8] hover:bg-blue-700 shadow-xl shadow-blue-200 rounded-2xl transition-all">
                        <span x-text="formateurForm.id ? 'Mettre à jour' : 'Ajouter'"></span>
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
            <h3 class="text-xl font-black text-gray-900 mb-2">Supprimer ce formateur ?</h3>
            <p class="text-sm text-gray-500 font-medium mb-8">Cette action est irréversible. Le compte utilisateur associé sera également supprimé.</p>
            <div class="flex gap-3 justify-center">
                <button type="button" @click="showDeleteModal = false" class="px-6 py-3 text-sm font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-2xl transition-colors">Annuler</button>
                <button type="button" @click="confirmDelete()" class="px-6 py-3 text-sm font-bold text-white bg-red-600 hover:bg-red-700 shadow-lg shadow-red-200 rounded-2xl transition-all">Oui, supprimer</button>
            </div>
        </div>
    </div>
</div>



@endsection
