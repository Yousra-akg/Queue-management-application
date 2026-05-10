@extends('layouts.admin')

@section('title', 'Gestion des Sessions - SoliQueue Admin')
@section('breadcrumb', 'Gestion Sessions')

@section('content')
    <div x-data="sessionManager()" x-init="init()" class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter uppercase">Gestion des Sessions</h1>
                <p class="text-sm text-slate-400 font-medium">Assignation des candidats aux jours d'entretien</p>
            </div>
        </div>



        <!-- Bottom: Session Management Table -->
        <div class="mt-12">
            <div class="flex flex-col sm:flex-row justify-between items-end sm:items-center mb-8 gap-4 px-2">
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Gérez toutes les sessions
                        d'entretien</p>
                </div>
                <button @click="openAddSessionModal()"
                    class="py-3 px-6 bg-[#1A73E8] text-white text-xs font-black rounded-2xl uppercase hover:bg-blue-700 transition-all shadow-xl shadow-blue-100 flex items-center gap-2">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14" />
                        <path d="M12 5v14" />
                    </svg>
                    Ajouter une session
                </button>
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
                        placeholder="Rechercher par titre de session...">
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
                                    class="ps-8 py-4 text-start text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">
                                    Nom de la Session</th>
                                <th
                                    class="px-6 py-4 text-start text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">
                                    Date</th>
                                <th
                                    class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">
                                    Capacité</th>
                                <th
                                    class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">
                                    Horaire</th>
                                <th
                                    class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">
                                    Code</th>
                                <th
                                    class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">
                                    Statut</th>
                                <th
                                    class="px-6 py-4 text-end pe-8 text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <template x-for="session in paginatedSessions" :key="session.id">
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="ps-8 py-5">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="size-10 rounded-xl bg-blue-50 text-[#1A73E8] flex items-center justify-center border border-blue-100 group-hover:scale-110 transition-transform">
                                                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24"
                                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path
                                                        d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20" />
                                                </svg>
                                            </div>
                                            <p class="text-sm font-black text-slate-900 uppercase" x-text="session.nom"></p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="text-xs font-bold text-slate-600"
                                            x-text="formatDate(session.dateEntretien)"></span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span
                                            class="inline-flex items-center justify-center size-9 rounded-xl bg-blue-50 text-[#1A73E8] text-xs font-black border border-blue-100"
                                            x-text="session.capaciteMax"></span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 rounded-lg text-[10px] font-bold text-slate-600">
                                            <span x-text="session.heureDebut.substring(0,5)"></span> - <span
                                                x-text="session.heureFin.substring(0,5)"></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span
                                            class="px-3 py-1 bg-slate-900 text-white rounded-lg text-xs font-black tracking-widest"
                                            x-text="session.codePresence"></span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span :class="{
                                            'bg-green-100 text-green-700': session.statut === 'en cours',
                                            'bg-blue-100 text-[#1A73E8]': session.statut === 'planifiée',
                                            'bg-slate-100 text-slate-500': session.statut === 'terminée',
                                            'bg-red-100 text-red-600': session.statut === 'annulée'
                                        }"
                                            class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-[10px] font-black uppercase">
                                            <span class="size-1.5 rounded-full" :class="{
                                                'bg-green-500': session.statut === 'en cours',
                                                'bg-[#1A73E8]': session.statut === 'planifiée',
                                                'bg-slate-400': session.statut === 'terminée',
                                                'bg-red-500': session.statut === 'annulée'
                                            }"></span>
                                            <span x-text="session.statut"></span>
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-end pe-8">
                                        <div class="flex justify-end gap-2">
                                            <button @click="editSession(session)"
                                                class="size-8 rounded-lg bg-blue-50 border border-blue-100 text-[#1A73E8] hover:bg-[#1A73E8] hover:text-white transition-all flex items-center justify-center">
                                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                                    <path d="m15 5 4 4" />
                                                </svg>
                                            </button>
                                            <button @click="deleteSession(session.id)"
                                                class="size-8 rounded-lg bg-red-50 border border-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center">
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



        <!-- Add/Edit Session Modal -->
        <div x-show="showSessionModal" x-cloak
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-sm w-full h-full min-h-screen"
            @click.self="showSessionModal = false">
            <div
                class="bg-white rounded-[2rem] shadow-2xl w-full max-w-xl max-h-[90vh] flex flex-col animate-in fade-in zoom-in duration-200 overflow-hidden">
                <!-- Header (Fixed) -->
                <div class="px-8 pt-8 sm:px-10 sm:pt-10 pb-6 relative shrink-0">
                    <button @click="showSessionModal = false"
                        class="absolute top-6 right-6 p-2.5 bg-gray-100 hover:bg-gray-200 rounded-full text-gray-500 transition-colors">
                        <svg class="size-4.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>

                    <h1 class="text-3xl font-black text-gray-900 tracking-tight"
                        x-text="sessionForm.id ? 'Modifier Session' : 'Nouvelle Session'"></h1>
                    <p class="text-[11px] text-gray-400 font-black uppercase tracking-widest mt-2">Configuration Rapide</p>
                </div>

                <!-- Form -->
                <form
                    :action="sessionForm.id ? '{{ url('admin/sessions') }}/' + sessionForm.id : '{{ route('admin.sessions.store') }}'"
                    method="POST" class="flex flex-col flex-1 overflow-hidden">
                    @csrf
                    <template x-if="sessionForm.id">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <!-- Scrollable Body -->
                    <div
                        class="px-8 sm:px-10 pb-4 overflow-y-auto overflow-x-hidden space-y-6 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-300">
                        <!-- Nom -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Titre
                                de la session</label>
                            <input type="text" name="nom" x-model="sessionForm.nom" required
                                placeholder="Ex: Session Printemps - J5"
                                class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors placeholder:text-gray-400 shadow-sm">
                        </div>

                        <!-- Date & Capacity -->
                        <div class="grid sm:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Date</label>
                                <input type="date" name="dateEntretien" x-model="sessionForm.dateEntretien" required
                                    class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors shadow-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Capacité</label>
                                <input type="number" name="capaciteMax" x-model="sessionForm.capaciteMax" required
                                    placeholder="60"
                                    class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors placeholder:text-gray-400 shadow-sm">
                            </div>
                        </div>

                        <!-- Horaires & Statut -->
                        <div class="grid sm:grid-cols-3 gap-6">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Début</label>
                                <input type="time" name="heureDebut" x-model="sessionForm.heureDebut" required
                                    class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors shadow-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Fin</label>
                                <input type="time" name="heureFin" x-model="sessionForm.heureFin" required
                                    class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors shadow-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Statut</label>
                                <input type="hidden" name="statut" x-model="sessionForm.statut">
                                <div class="relative inline-flex w-full" x-data="{ openStatut: false }"
                                    @click.away="openStatut = false">
                                    <button type="button" @click="openStatut = !openStatut"
                                        class="w-full py-3 px-4 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl text-sm font-medium text-gray-700 transition-colors flex justify-between items-center focus:outline-none focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] shadow-sm">
                                        <span
                                            x-text="sessionForm.statut.charAt(0).toUpperCase() + sessionForm.statut.slice(1)"
                                            class="capitalize">Statut</span>
                                        <svg :class="{ 'rotate-180': openStatut }"
                                            class="size-4 text-gray-400 transition-transform duration-200"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="m6 9 6 6 6-6"></path>
                                        </svg>
                                    </button>

                                    <div x-show="openStatut" style="display: none;"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 transform scale-95"
                                        x-transition:enter-end="opacity-100 transform scale-100"
                                        class="origin-top-left absolute left-0 top-full mt-2 min-w-48 w-full z-[101] bg-white shadow-xl rounded-2xl p-2 border border-gray-100">
                                        <a @click.prevent="sessionForm.statut = 'planifiée'; openStatut = false"
                                            class="flex items-center gap-x-3.5 py-2.5 px-3 rounded-xl text-sm font-bold text-gray-700 hover:bg-[#F8F9FA] cursor-pointer">Planifiée</a>
                                        <a @click.prevent="sessionForm.statut = 'en cours'; openStatut = false"
                                            class="flex items-center gap-x-3.5 py-2.5 px-3 rounded-xl text-sm font-bold text-gray-700 hover:bg-[#F8F9FA] cursor-pointer">En
                                            cours</a>
                                        <a @click.prevent="sessionForm.statut = 'terminée'; openStatut = false"
                                            class="flex items-center gap-x-3.5 py-2.5 px-3 rounded-xl text-sm font-bold text-gray-700 hover:bg-[#F8F9FA] cursor-pointer">Terminée</a>
                                        <a @click.prevent="sessionForm.statut = 'annulée'; openStatut = false"
                                            class="flex items-center gap-x-3.5 py-2.5 px-3 rounded-xl text-sm font-bold text-gray-700 hover:bg-[#F8F9FA] cursor-pointer">Annulée</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Code Generator Widget -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Code de
                                Présence</label>
                            <div class="flex items-center gap-3">
                                <input type="text" name="codePresence" x-model="sessionForm.codePresence" readonly required
                                    class="w-full py-4 px-5 bg-[#F8F9FA] border-transparent rounded-2xl text-3xl font-black text-[#1A73E8] tracking-widest focus:ring-0">

                                <button type="button" @click="generateSessionCode()"
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
                        <button type="button" @click="showSessionModal = false"
                            class="px-8 py-3.5 text-sm font-bold text-[#202124] bg-[#F8F9FA] hover:bg-gray-200 rounded-2xl transition-colors">
                            Annuler
                        </button>
                        <button type="submit"
                            class="px-8 py-3.5 text-sm font-bold text-white bg-[#1A73E8] hover:bg-blue-700 shadow-xl shadow-blue-200 rounded-2xl transition-all">
                            <span x-text="sessionForm.id ? 'Mettre à jour' : 'Publier la session'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function sessionManager() {
                return {
                    sessions: @json($sessions),
                    searchQuery: '',
                    statusFilter: 'all',
                    showSessionModal: false,
                    sessionForm: {
                        id: null,
                        nom: '',
                        dateEntretien: '',
                        capaciteMax: '',
                        heureDebut: '',
                        heureFin: '',
                        codePresence: '',
                        statut: 'planifiée'
                    },


                    get filteredSessions() {
                        return this.sessions.filter(s => {
                            const matchesSearch = s.nom.toLowerCase().includes(this.searchQuery.toLowerCase());
                            const matchesStatus = this.statusFilter === 'all' || s.statut === this.statusFilter;
                            return matchesSearch && matchesStatus;
                        });
                    },

                    // Pagination
                    currentPage: 1,
                    itemsPerPage: 10,
                    get paginatedSessions() {
                        const start = (this.currentPage - 1) * this.itemsPerPage;
                        const end = start + this.itemsPerPage;
                        return this.filteredSessions.slice(start, end);
                    },
                    get totalPages() {
                        return Math.ceil(this.filteredSessions.length / this.itemsPerPage) || 1;
                    },
                    get pages() {
                        let pages = [];
                        for(let i = 1; i <= this.totalPages; i++) pages.push(i);
                        return pages;
                    },
                    nextPage() { if(this.currentPage < this.totalPages) this.currentPage++; },
                    prevPage() { if(this.currentPage > 1) this.currentPage--; },
                    goToPage(p) { this.currentPage = p; },

                    init() {
                        this.$watch('searchQuery', () => this.currentPage = 1);
                        this.$watch('statusFilter', () => this.currentPage = 1);
                    },



                    formatDate(dateStr) {
                        const date = new Date(dateStr);
                        return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' });
                    },

                    editSession(session) {
                        this.sessionForm = { ...session };
                        this.showSessionModal = true;
                    },

                    async deleteSession(id) {
                        const result = await Swal.fire({
                            title: 'Supprimer la session ?',
                            text: 'Cette action est irréversible.',
                            icon: 'error',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#f3f4f6',
                            confirmButtonText: 'Oui, supprimer',
                            cancelButtonText: 'Annuler'
                        });
                        if (!result.isConfirmed) return;

                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/sessions/${id}`;
                        form.innerHTML = `
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                            <input type="hidden" name="_method" value="DELETE">
                        `;
                        document.body.appendChild(form);
                        form.submit();
                    },

                    openAddSessionModal() {
                        this.resetSessionForm();
                        this.generateSessionCode();
                        this.showSessionModal = true;
                    },

                    generateSessionCode() {
                        this.sessionForm.codePresence = Math.floor(1000 + Math.random() * 9000).toString();
                    },

                    resetSessionForm() {
                        this.sessionForm = {
                            id: null,
                            nom: '',
                            dateEntretien: '',
                            capaciteMax: '60',
                            heureDebut: '09:00',
                            heureFin: '17:00',
                            codePresence: '',
                            statut: 'planifiée'
                        };
                    }
                }
            }
        </script>
    @endpush


@endsection