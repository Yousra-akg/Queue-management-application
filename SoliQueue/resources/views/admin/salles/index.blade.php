@extends('layouts.admin')

@section('title', 'Gestion des Salles - SoliQueue Admin')
@section('breadcrumb', 'Gestion Salles')

@section('content')
<div x-data="{
        showModal: false,
        salleForm: { id: null, nom: '' },
        openAddModal() {
            this.salleForm = { id: null, nom: '' };
            this.showModal = true;
        },
        editSalle(id, nom) {
            this.salleForm = { id, nom };
            this.showModal = true;
        },
        deleteSalle(id) {
            if(confirm('Supprimer cette salle ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ url('admin/salles') }}/' + id;
                form.innerHTML = `
                    <input type='hidden' name='_token' value='{{ csrf_token() }}'>
                    <input type='hidden' name='_method' value='DELETE'>
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    }" class="space-y-8">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tighter uppercase">Gestion des Salles</h1>
            <p class="text-sm text-slate-400 font-medium">Création et gestion des salles d'entretiens</p>
        </div>
    </div>

    <!-- Management Table Area -->
    <div class="mt-12">
        <div class="flex flex-col sm:flex-row justify-between items-end sm:items-center mb-8 gap-4 px-2">
            <div>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Gérez vos salles</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button @click="openAddModal()" class="py-3 px-6 bg-[#1A73E8] text-white text-xs font-black rounded-2xl uppercase hover:bg-blue-700 transition-all shadow-xl shadow-blue-100 flex items-center gap-2">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14" />
                        <path d="M12 5v14" />
                    </svg>
                    Ajouter une salle
                </button>
            </div>
        </div>

        <!-- Table Container -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden flex flex-col">
            <div class="overflow-x-auto">
                <table class="w-full text-start">
                    <thead class="bg-slate-50/50 border-b border-slate-100">
                        <tr>
                            <th class="ps-8 py-4 text-start text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Nom de la Salle</th>
                            <th class="px-6 py-4 text-end pe-8 text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($salles as $salle)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="ps-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-xl bg-blue-50 text-[#1A73E8] flex items-center justify-center border border-blue-100 group-hover:scale-110 transition-transform">
                                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                                            <line x1="3" y1="9" x2="21" y2="9"/>
                                            <line x1="9" y1="21" x2="9" y2="9"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-black text-slate-900 uppercase">{{ $salle->nom }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-end pe-8">
                                <div class="flex justify-end gap-2">
                                    <button @click="editSalle({{ $salle->id }}, '{{ addslashes($salle->nom) }}')" class="size-8 rounded-lg bg-blue-50 border border-blue-100 text-[#1A73E8] hover:bg-[#1A73E8] hover:text-white transition-all flex items-center justify-center">
                                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                            <path d="m15 5 4 4" />
                                        </svg>
                                    </button>
                                    <button @click="deleteSalle({{ $salle->id }})" class="size-8 rounded-lg bg-red-50 border border-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center">
                                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18" />
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @if($salles->isEmpty())
                        <tr>
                            <td colspan="2" class="px-6 py-8 text-center text-sm font-bold text-gray-400">
                                Aucune salle n'a été ajoutée.
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Ajouter/Modifier -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-sm w-full h-full min-h-screen" @click.self="showModal = false">
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col animate-in fade-in zoom-in duration-200 overflow-hidden">
            <div class="px-8 pt-8 sm:px-10 sm:pt-10 pb-6 relative shrink-0">
                <button @click="showModal = false" class="absolute top-6 right-6 p-2.5 bg-gray-100 hover:bg-gray-200 rounded-full text-gray-500 transition-colors">
                    <svg class="size-4.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight" x-text="salleForm.id ? 'Modifier Salle' : 'Nouvelle Salle'"></h1>
                <p class="text-[11px] text-gray-400 font-black uppercase tracking-widest mt-2">Configuration Rapide</p>
            </div>

            <form :action="salleForm.id ? '{{ url('admin/salles') }}/' + salleForm.id : '{{ route('admin.salles.store') }}'" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <template x-if="salleForm.id">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="px-8 sm:px-10 pb-4 overflow-y-auto space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2">Nom de la salle</label>
                        <input type="text" name="nom" x-model="salleForm.nom" required placeholder="Ex: Salle de Réunion A" class="w-full py-3 px-4 bg-white border border-gray-200 focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] rounded-xl text-sm font-medium text-gray-700 transition-colors placeholder:text-gray-400 shadow-sm">
                    </div>
                </div>

                <div class="px-8 sm:px-10 py-6 shrink-0 flex gap-4 justify-end">
                    <button type="button" @click="showModal = false" class="px-8 py-3.5 text-sm font-bold text-[#202124] bg-[#F8F9FA] hover:bg-gray-200 rounded-2xl transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="px-8 py-3.5 text-sm font-bold text-white bg-[#1A73E8] hover:bg-blue-700 shadow-xl shadow-blue-200 rounded-2xl transition-all">
                        <span x-text="salleForm.id ? 'Mettre à jour' : 'Ajouter'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
