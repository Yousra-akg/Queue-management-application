@extends('layouts.admin')

@section('title', 'Gestion des Entretiens - SoliQueue Admin')
@section('breadcrumb', 'Gestion Entretiens')

@section('content')
    <div x-data="entretiensManager({{ json_encode($entretiens) }}, {{ json_encode($salles) }})" x-init="init()" class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter uppercase">Gestion des Entretiens</h1>
                <p class="text-sm text-slate-400 font-medium">Assignation des candidats aux jours d'entretien</p>
            </div>
        </div>



        <!-- Bottom: Entretien Management Table -->
        <div class="mt-12">
            <div class="flex flex-col sm:flex-row justify-between items-end sm:items-center mb-8 gap-4 px-2">
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Gérez toutes les entretiens
                        d'entretien</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.entretiens.export') }}"
                        class="py-3 px-6 bg-slate-100 text-slate-700 text-xs font-black rounded-2xl uppercase hover:bg-slate-200 transition-all flex items-center gap-2">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Exporter les entretiens
                    </a>
                    <button @click="openAddEntretienModal()"
                        class="py-3 px-6 bg-[#1A73E8] text-white text-xs font-black rounded-2xl uppercase hover:bg-blue-700 transition-all shadow-xl shadow-blue-100 flex items-center gap-2">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                        Ajouter une entretien
                    </button>
                </div>
            </div>

            <!-- Filters Bar -->
            <div class="mb-6 flex flex-wrap gap-4 items-center">
                <div class="relative flex-grow min-w-[300px]">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                        <svg class="size-4.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                    </div>
                    <input type="text" x-model="searchQuery"
                        class="py-3 ps-12 pe-4 block w-full bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors placeholder:text-gray-400 shadow-sm"
                        placeholder="Rechercher par titre de entretien...">
                </div>

                <div class="relative z-10 w-48" x-data="{ openMainStatut: false }" @click.away="openMainStatut = false">
                    <button type="button" @click="openMainStatut = !openMainStatut"
                        class="w-full py-3 px-4 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl text-sm font-medium text-gray-700 transition-colors flex justify-between items-center focus:outline-none focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] shadow-sm">
                        <span
                            x-text="statusFilter === 'all' ? 'Tous les statuts' : statusFilter.charAt(0).toUpperCase() + statusFilter.slice(1)"
                            class="capitalize">Tous les statuts</span>
                        <svg :class="{ 'rotate-180': openMainStatut }"
                            class="size-4 text-gray-500 transition-transform duration-200"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6"></path>
                        </svg>
                    </button>

                    <div x-show="openMainStatut" style="display: none;"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        class="origin-top-right absolute right-0 top-full mt-2 min-w-48 w-full z-[101] bg-white shadow-xl rounded-xl p-2 border border-gray-100">
                        <a @click.prevent="statusFilter = 'all'; openMainStatut = false"
                            class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer">Tous
                            les statuts</a>
                        <a @click.prevent="statusFilter = 'planifiée'; openMainStatut = false"
                            class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer">Planifiée</a>
                        <a @click.prevent="statusFilter = 'en cours'; openMainStatut = false"
                            class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer">En
                            cours</a>
                        <a @click.prevent="statusFilter = 'terminée'; openMainStatut = false"
                            class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer">Terminée</a>
                        <a @click.prevent="statusFilter = 'annulée'; openMainStatut = false"
                            class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer">Annulée</a>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden flex flex-col">
                <div class="overflow-x-auto">
                    <table class="w-full text-start">
                        <thead class="bg-slate-50/50 border-b border-slate-100">
                            <tr>
                                <th
                                    class="ps-4 py-3 text-start text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">
                                    Date</th>
                                <th
                                    class="px-3 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">
                                    Capacité</th>
                                <th
                                    class="px-3 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">
                                    Horaire</th>
                                <th
                                    class="px-3 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">
                                    Code</th>
                                <th
                                    class="px-3 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">
                                    Statut</th>
                                <th
                                    class="px-3 py-3 text-start text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">
                                    Salles & Formateurs</th>
                                <th
                                    class="px-3 py-3 text-end pe-4 text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <template x-for="entretien in paginatedEntretiens" :key="entretien.id">
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="ps-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="size-8 rounded-xl bg-blue-50 text-[#1A73E8] flex items-center justify-center border border-blue-100 group-hover:scale-110 transition-transform">
                                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20" />
                                                </svg>
                                            </div>
                                            <span class="text-xs font-bold text-slate-600 uppercase"
                                                x-text="formatDate(entretien.dateEntretien)"></span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span
                                            class="inline-flex items-center justify-center size-7 rounded-lg bg-blue-50 text-[#1A73E8] text-[11px] font-black border border-blue-100"
                                            x-text="entretien.capaciteMax"></span>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <div
                                            class="inline-flex items-center gap-1 px-2 py-1 bg-slate-100 rounded-lg text-[10px] font-bold text-slate-600">
                                            <span x-text="entretien.heureDebut.substring(0,5)"></span> - <span
                                                x-text="entretien.heureFin.substring(0,5)"></span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span
                                            class="px-2 py-1 bg-slate-900 text-white rounded-md text-[10px] font-black tracking-widest"
                                            x-text="entretien.codePresence"></span>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span :class="{
                                            'bg-green-100 text-green-700': entretien.statut === 'en cours',
                                            'bg-blue-100 text-[#1A73E8]': entretien.statut === 'planifiée',
                                            'bg-slate-100 text-slate-500': entretien.statut === 'terminée',
                                            'bg-red-100 text-red-600': entretien.statut === 'annulée'
                                        }"
                                            class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-[10px] font-black uppercase">
                                            <span class="size-1.5 rounded-full" :class="{
                                                'bg-green-500': entretien.statut === 'en cours',
                                                'bg-[#1A73E8]': entretien.statut === 'planifiée',
                                                'bg-slate-400': entretien.statut === 'terminée',
                                                'bg-red-500': entretien.statut === 'annulée'
                                            }"></span>
                                            <span x-text="entretien.statut"></span>
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 text-start">
                                        <div class="flex flex-col gap-0.5">
                                            <template x-for="aff in getAffectationsFormat(entretien)" :key="aff.salleNom">
                                                <div class="text-[10px] leading-tight max-w-[150px] truncate" :title="aff.salleNom + ' : ' + aff.formateurs">
                                                    <span class="font-black text-slate-800" x-text="aff.salleNom + ':'"></span>
                                                    <span class="font-medium text-slate-500" x-text="aff.formateurs"></span>
                                                </div>
                                            </template>
                                            <div x-show="getAffectationsFormat(entretien).length === 0" class="text-[10px] italic text-slate-400">Aucune affectation</div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-end pe-4">
                                        <div class="flex justify-end gap-1.5">
                                            <button @click="editEntretien(entretien)"
                                                class="size-7 rounded-md bg-blue-50 border border-blue-100 text-[#1A73E8] hover:bg-[#1A73E8] hover:text-white transition-all flex items-center justify-center">
                                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                                    <path d="m15 5 4 4" />
                                                </svg>
                                            </button>
                                            <button @click="deleteEntretien(entretien.id)"
                                                class="size-7 rounded-md bg-red-50 border border-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center">
                                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 6h18" />
                                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
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
        </div>



        <!-- Add/Edit Entretien Modal -->
        <div x-show="showEntretienModal" x-cloak
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-sm w-full h-full min-h-screen"
            @click.self="showEntretienModal = false">
            <div
                class="bg-white rounded-[2rem] shadow-2xl w-full max-w-xl max-h-[90vh] flex flex-col animate-in fade-in zoom-in duration-200 overflow-hidden">
                <!-- Header (Fixed) -->
                <div class="px-8 pt-8 sm:px-10 sm:pt-10 pb-6 relative shrink-0">
                    <button @click="showEntretienModal = false"
                        class="absolute top-6 right-6 p-2.5 bg-gray-100 hover:bg-gray-200 rounded-full text-gray-500 transition-colors">
                        <svg class="size-4.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>

                    <h1 class="text-3xl font-black text-gray-900 tracking-tight"
                        x-text="entretienForm.id ? 'Modifier Entretien' : 'Nouvelle Entretien'"></h1>
                    <p class="text-[11px] text-gray-400 font-black uppercase tracking-widest mt-2">Configuration Rapide</p>
                </div>

                <!-- Form -->
                <form
                    :action="entretienForm.id ? '{{ url('admin/entretiens') }}/' + entretienForm.id : '{{ route('admin.entretiens.store') }}'"
                    method="POST" class="flex flex-col flex-1 overflow-hidden">
                    @csrf
                    <template x-if="entretienForm.id">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <!-- Scrollable Body -->
                    <div
                        class="px-8 sm:px-10 pb-4 overflow-y-auto overflow-x-hidden space-y-6 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-300">
                        <!-- Date & Capacity -->
                        <div class="grid sm:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Date</label>
                                <input type="date" name="dateEntretien" x-model="entretienForm.dateEntretien" required
                                    class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors shadow-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Capacité</label>
                                <input type="number" name="capaciteMax" x-model="entretienForm.capaciteMax" required
                                    placeholder="60"
                                    class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors placeholder:text-gray-400 shadow-sm">
                            </div>
                        </div>

                        <!-- Horaires & Statut -->
                        <div class="grid sm:grid-cols-3 gap-6">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Début</label>
                                <input type="time" name="heureDebut" x-model="entretienForm.heureDebut" required
                                    class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors shadow-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Fin</label>
                                <input type="time" name="heureFin" x-model="entretienForm.heureFin" required
                                    class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors shadow-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Statut</label>
                                <input type="hidden" name="statut" x-model="entretienForm.statut">
                                <div class="relative inline-flex w-full" x-data="{ openStatut: false }"
                                    @click.away="openStatut = false">
                                    <button type="button" @click="openStatut = !openStatut"
                                        class="w-full py-3 px-4 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl text-sm font-medium text-gray-700 transition-colors flex justify-between items-center focus:outline-none focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] shadow-sm">
                                        <span
                                            x-text="entretienForm.statut.charAt(0).toUpperCase() + entretienForm.statut.slice(1)"
                                            class="capitalize">Statut</span>
                                        <svg :class="{ 'rotate-180': openStatut }"
                                            class="size-4 text-gray-400 transition-transform duration-200"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="m6 9 6 6-6"></path>
                                        </svg>
                                    </button>

                                    <div x-show="openStatut" style="display: none;"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 transform scale-95"
                                        x-transition:enter-end="opacity-100 transform scale-100"
                                        class="origin-top-left absolute left-0 top-full mt-2 min-w-48 w-full z-[101] bg-white shadow-xl rounded-2xl p-2 border border-gray-100">
                                        <a @click.prevent="entretienForm.statut = 'planifiée'; openStatut = false"
                                            class="flex items-center gap-x-3.5 py-2.5 px-3 rounded-xl text-sm font-bold text-gray-700 hover:bg-[#F8F9FA] cursor-pointer">Planifiée</a>
                                        <a @click.prevent="entretienForm.statut = 'en cours'; openStatut = false"
                                            class="flex items-center gap-x-3.5 py-2.5 px-3 rounded-xl text-sm font-bold text-gray-700 hover:bg-[#F8F9FA] cursor-pointer">En
                                            cours</a>
                                        <a @click.prevent="entretienForm.statut = 'terminée'; openStatut = false"
                                            class="flex items-center gap-x-3.5 py-2.5 px-3 rounded-xl text-sm font-bold text-gray-700 hover:bg-[#F8F9FA] cursor-pointer">Terminée</a>
                                        <a @click.prevent="entretienForm.statut = 'annulée'; openStatut = false"
                                            class="flex items-center gap-x-3.5 py-2.5 px-3 rounded-xl text-sm font-bold text-gray-700 hover:bg-[#F8F9FA] cursor-pointer">Annulée</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Affectations Formateurs/Salles -->
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-[10px] font-black text-gray-900 uppercase tracking-widest">Affectation Salles & Formateurs</label>
                                <button type="button" @click="addAffectation()" class="text-xs text-blue-600 hover:text-blue-800 font-bold flex items-center gap-1">
                                    <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Ajouter
                                </button>
                            </div>
                            <div class="space-y-3">
                                <template x-for="(affectation, index) in entretienForm.affectations" :key="index">
                                    <div class="flex gap-3 items-end p-3 bg-gray-50 rounded-xl border border-gray-100 relative group">
                                        <div class="flex-[2]">
                                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Formateurs</label>
                                            <select multiple :name="`affectations[${index}][formateur_id][]`" x-model="affectation.formateur_id" required 
                                                class="hidden"
                                                data-hs-select='{
                                                  "placeholder": "Sélectionner les formateurs...",
                                                  "toggleTag": "<button type=\"button\" aria-expanded=\"false\"></button>",
                                                  "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-2.5 ps-4 pe-9 flex text-nowrap w-full cursor-pointer bg-white border border-gray-200 text-gray-700 rounded-lg text-start text-sm hover:bg-gray-50 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8]",
                                                  "dropdownClasses": "mt-2 z-50 w-full max-h-72 p-1 space-y-0.5 bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-300",
                                                  "optionClasses": "py-2 px-4 w-full text-sm text-gray-700 cursor-pointer hover:bg-gray-100 rounded-md focus:outline-none focus:bg-gray-100",
                                                  "optionTemplate": "<div class=\"flex justify-between items-center w-full\"><span data-title></span><span class=\"hidden hs-selected:block\"><svg class=\"shrink-0 size-3.5 text-[#1A73E8]\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"20 6 9 17 4 12\"/></svg></span></div>",
                                                  "extraMarkup": "<div class=\"absolute top-1/2 inset-e-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-400\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                                                }'>
                                                @foreach($formateurs as $formateur)
                                                    <option value="{{ $formateur->id }}">{{ $formateur->nom }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="flex-1">
                                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Salle</label>
                                            <select :name="`affectations[${index}][salle_id]`" x-model="affectation.salle_id" required 
                                                class="hidden"
                                                data-hs-select='{
                                                  "placeholder": "Sélectionner la salle...",
                                                  "toggleTag": "<button type=\"button\" aria-expanded=\"false\"></button>",
                                                  "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-2.5 ps-4 pe-9 flex text-nowrap w-full cursor-pointer bg-white border border-gray-200 text-gray-700 rounded-lg text-start text-sm hover:bg-gray-50 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8]",
                                                  "dropdownClasses": "mt-2 z-50 w-full max-h-72 p-1 space-y-0.5 bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-300",
                                                  "optionClasses": "py-2 px-4 w-full text-sm text-gray-700 cursor-pointer hover:bg-gray-100 rounded-md focus:outline-none focus:bg-gray-100",
                                                  "optionTemplate": "<div class=\"flex justify-between items-center w-full\"><span data-title></span><span class=\"hidden hs-selected:block\"><svg class=\"shrink-0 size-3.5 text-[#1A73E8]\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"20 6 9 17 4 12\"/></svg></span></div>",
                                                  "extraMarkup": "<div class=\"absolute top-1/2 inset-e-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-400\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                                                }'>
                                                <option value="">Sélectionner</option>
                                                @foreach($salles as $salle)
                                                    <option value="{{ $salle->id }}">{{ $salle->nom }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="button" @click="removeAffectation(index)" x-show="entretienForm.affectations.length > 1" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Code Generator Widget -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Code de
                                Présence</label>
                            <div class="flex items-center gap-3">
                                <input type="text" name="codePresence" x-model="entretienForm.codePresence" readonly required
                                    class="w-full py-4 px-5 bg-[#F8F9FA] border-transparent rounded-2xl text-3xl font-black text-[#1A73E8] tracking-widest focus:ring-0">

                                <button type="button" @click="generateEntretienCode()"
                                    class="flex-shrink-0 p-5 bg-[#F8F9FA] text-[#202124] rounded-2xl hover:bg-gray-200 transition-colors"
                                    title="Régénérer le code">
                                    <svg class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="3" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Actions Footer (Fixed) -->
                    <div class="px-8 sm:px-10 py-6 shrink-0 flex gap-4 justify-end">
                        <button type="button" @click="showEntretienModal = false"
                            class="px-8 py-3.5 text-sm font-bold text-[#202124] bg-[#F8F9FA] hover:bg-gray-200 rounded-2xl transition-colors">
                            Annuler
                        </button>
                        <button type="submit"
                            class="px-8 py-3.5 text-sm font-bold text-white bg-[#1A73E8] hover:bg-blue-700 shadow-xl shadow-blue-200 rounded-2xl transition-all">
                            <span x-text="entretienForm.id ? 'Mettre à jour' : 'Publier la entretien'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>




@endsection
