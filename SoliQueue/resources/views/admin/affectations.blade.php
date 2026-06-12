@extends('layouts.admin')

@section('title', 'Dashboard d\'Affectation - SoliQueue Admin')
@section('breadcrumb', 'Affectations')

@section('content')
<div x-data="affectationsManager({{ json_encode($availableCandidates) }}, {{ json_encode($entretiens) }})" x-init="init()" class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tighter uppercase">Dashboard d'affectation</h1>
            <p class="text-sm text-slate-400 font-medium">Assignation des candidats aux jours d'entretien</p>
        </div>
    </div>

    <!-- Split View Layout -->
    <div class="grid lg:grid-cols-2 gap-8 items-start">
        <!-- Left: Available Candidates List -->
        <div class="bg-white border border-slate-200 rounded-[2.5rem] shadow-sm flex flex-col h-[600px] overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" @change="toggleAllCandidates" :checked="allSelected"
                        class="size-5 rounded border-slate-300 text-[#1A73E8] focus:ring-[#1A73E8] cursor-pointer transition-transform group-active:scale-95">
                    <div>
                        <h2 class="text-lg font-black text-slate-900 uppercase">Candidats Disponibles</h2>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Glissez vers une entretien</p>
                    </div>
                </label>
                <button @click="showAddCandidateModal = true"
                    class="size-9 flex items-center justify-center rounded-xl bg-[#1A73E8] text-white hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 flex-shrink-0"
                    title="Ajouter un candidat">
                    <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14" />
                        <path d="M12 5v14" />
                    </svg>
                </button>
            </div>

            <div class="overflow-y-auto p-4 space-y-3 flex-grow" id="candidate-pool">
                <template x-for="candidate in availableCandidates" :key="candidate.id">
                    <div x-show="candidate && candidate.nom" :id="'candidate-' + candidate.id" :data-id="candidate.id"
                        class="candidate-item flex items-center justify-between p-4 bg-slate-50 border border-slate-100 rounded-2xl group hover:border-[#1A73E8] transition-all cursor-move select-none"
                        :class="candidate.selected ? 'border-[#1A73E8] bg-blue-50' : ''"
                        @click="candidate.selected = !candidate.selected">
                        <div class="flex items-center gap-4">
                            <div class="size-11 rounded-xl bg-blue-100 text-[#1A73E8] flex items-center justify-center font-black italic shadow-sm" 
                                 x-text="candidate.prenom ? candidate.prenom[0] + candidate.nom[0] : '??'"></div>
                            <div>
                                <p class="text-sm font-black text-slate-900 uppercase tracking-tight" x-text="candidate.prenom + ' ' + candidate.nom"></p>
                                <p class="text-[10px] font-black text-[#1A73E8] uppercase tracking-widest bg-blue-50 px-2 py-0.5 rounded-lg inline-block mt-1">Score: <span x-text="candidate.scoreQCM"></span>/100</p>
                            </div>
                        </div>
                        <div class="text-slate-300 group-hover:text-[#1A73E8] transition-all transform group-hover:translate-x-1">
                            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </div>
                    </div>
                </template>
                <div x-show="availableCandidates.length === 0" class="py-10 text-center text-slate-400">
                    <p class="text-sm italic">Aucun candidat disponible</p>
                </div>
            </div>
        </div>

        <!-- Right: Entretien Assignment -->
        <div class="bg-white border border-slate-200 rounded-[2.5rem] shadow-sm flex flex-col h-[600px] overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-black text-slate-900 uppercase">Affectation Entretiens</h2>
                    <span class="py-1 px-3 bg-blue-50 text-[#1A73E8] rounded-full text-[10px] font-black uppercase">
                        <span x-text="entretiens.length"></span> Entretiens Disponibles
                    </span>
                </div>

                <!-- Tabs Navigation -->
                <nav class="flex space-x-2 bg-slate-50 p-1.5 rounded-2xl overflow-x-auto" aria-label="Tabs">
                    <template x-for="s in entretiens" :key="s.id">
                        <button type="button" 
                            @click="selectedEntretienId = s.id"
                            class="py-2 px-4 inline-flex items-center gap-x-2 text-xs font-black uppercase rounded-xl transition-all whitespace-nowrap"
                            :class="selectedEntretienId == s.id ? 'bg-white text-[#1A73E8] shadow-sm' : 'bg-transparent text-slate-400 hover:text-slate-600'"
                            x-text="s.dateEntretien"></button>
                    </template>
                </nav>
            </div>

            <div class="p-6 overflow-y-auto flex-grow flex flex-col" x-show="selectedEntretien">
                <div class="capacity-container flex justify-between items-center mb-6 p-4 bg-[#F8F9FA] border border-slate-100 rounded-2xl">
                    <div class="flex-grow mr-4">
                        <p class="text-xs font-black text-slate-900 tracking-tight uppercase" x-text="'Capacité ' + selectedEntretien.dateEntretien"></p>
                        <div class="w-full h-2 bg-slate-200 rounded-full mt-2 overflow-hidden">
                            <div class="bg-[#1A73E8] h-full rounded-full transition-all duration-500" 
                                 :style="'width: ' + (selectedEntretien.candidats_count / selectedEntretien.capaciteMax * 100) + '%'">
                            </div>
                        </div>
                    </div>
                    <span class="text-sm font-black text-slate-900" x-text="selectedEntretien.candidats_count + '/' + selectedEntretien.capaciteMax"></span>
                </div>

                <div class="flex-grow flex flex-col min-h-0">
                    <div id="drop-zone" class="drop-zone flex-grow overflow-y-auto border-2 border-dashed border-slate-200 rounded-[2.5rem] p-6 flex flex-col gap-3 transition-all [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-200"
                        :class="isDragging ? 'border-[#1A73E8] bg-blue-50/50' : ''">
                        
                        <div x-show="selectedEntretien && selectedEntretien.candidats.length === 0" class="flex flex-col items-center justify-center h-full text-center py-10 opacity-40">
                            <div class="size-20 bg-white border border-slate-100 rounded-3xl shadow-sm flex items-center justify-center text-slate-300 mb-6">
                                <svg class="size-10" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v14"/><path d="m5 10 7 7 7-7"/><path d="M20 21H4"/></svg>
                            </div>
                            <p class="text-xs text-slate-500 font-black uppercase tracking-widest italic">Déposez ici les candidats</p>
                        </div>

                        <template x-if="selectedEntretien">
                            <template x-for="c in selectedEntretien.candidats" :key="c.id">
                                <div class="flex items-center justify-between p-5 bg-white border border-slate-100 rounded-2xl shadow-sm animate-in fade-in zoom-in duration-300 hover:border-[#1A73E8] transition-colors group">
                                    <div class="flex items-center gap-4">
                                        <div class="size-10 rounded-xl bg-slate-50 text-xs font-black flex items-center justify-center text-slate-500 italic shadow-inner" x-text="c.prenom[0] + c.nom[0]"></div>
                                        <div>
                                            <p class="text-sm font-black text-slate-800 uppercase tracking-tight" x-text="c.prenom + ' ' + c.nom"></p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase" x-text="c.cin"></p>
                                                <span class="size-1.5 rounded-full" :class="c.is_present ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]' : 'bg-slate-300'"></span>
                                                <span class="text-[9px] font-black uppercase tracking-wider" :class="c.is_present ? 'text-green-600' : 'text-slate-400'" x-text="c.is_present ? 'Présent' : 'Absent'"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <button x-show="selectedEntretien.statut !== 'terminée' || !c.is_present" @click="unassignCandidate(c.id)" class="size-8 flex items-center justify-center rounded-lg text-slate-200 hover:text-red-500 hover:bg-red-50 transition-all opacity-0 group-hover:opacity-100" title="Retirer de la entretien">
                                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                        </template>
                    </div>
                </div>
            </div>

            <div x-show="!selectedEntretienId" class="flex-grow flex items-center justify-center p-12 text-center text-slate-400 opacity-60">
                <div class="space-y-4">
                    <svg class="size-16 mx-auto text-slate-200" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <p class="text-sm font-bold uppercase tracking-widest text-slate-300">Sélectionnez une entretien pour commencer l'affectation</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Add Candidate Modal -->
    <div x-show="showAddCandidateModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-sm w-full h-full min-h-screen" @click.self="showAddCandidateModal = false">
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-xl max-h-[90vh] flex flex-col animate-in fade-in zoom-in duration-200 overflow-hidden">
            <!-- Header (Fixed) -->
            <div class="px-8 pt-8 sm:px-10 sm:pt-10 pb-6 relative shrink-0">
                <button @click="showAddCandidateModal = false" class="absolute top-6 right-6 p-2.5 bg-gray-100 hover:bg-gray-200 rounded-full text-gray-500 transition-colors">
                    <svg class="size-4.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
                
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Ajouter un Candidat</h1>
                <p class="text-[11px] text-gray-400 font-black uppercase tracking-widest mt-2">Nouveau candidat QCM</p>
            </div>

            <form action="{{ route('admin.candidates.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <!-- Scrollable Body -->
                <div class="px-8 sm:px-10 pb-4 overflow-y-auto space-y-6 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-300">
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Prénom</label>
                            <input type="text" name="prenom" required placeholder="Lila"
                                class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors placeholder:text-gray-400 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Nom</label>
                            <input type="text" name="nom" required placeholder="Mansouri"
                                class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors placeholder:text-gray-400 shadow-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">CIN</label>
                        <input type="text" name="cin" required placeholder="AB123456"
                            class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 uppercase transition-colors placeholder:text-gray-400 shadow-sm">
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Score QCM (/100)</label>
                        <input type="number" name="scoreQCM" min="0" max="100" step="0.1" required placeholder="85"
                            class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors placeholder:text-gray-400 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Photo de profil (Optionnel)</label>
                        <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors border border-gray-200 rounded-xl bg-white shadow-sm cursor-pointer file:cursor-pointer">
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
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



@endsection

