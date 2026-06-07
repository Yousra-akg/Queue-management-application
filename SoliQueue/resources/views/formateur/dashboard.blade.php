<!DOCTYPE html>
<html lang="fr" class="h-full light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Formateur - SoliQueue</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 h-full font-sans">
    <!-- Navigation -->
    <header
        class="flex flex-wrap sm:justify-start sm:flex-nowrap z-50 w-full bg-white border-b border-gray-200 text-sm py-3 sm:py-0">
        <nav class="relative max-w-[85rem] w-full mx-auto px-4 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8"
            aria-label="Global">
            <div class="flex items-center justify-between">
                <a class="flex items-center gap-x-4" href="{{ route('formateur.sessions') }}">
                    <img src="{{ asset('img/logo.png') }}" alt="SoliQueue" class="h-10 w-auto">
                    <div class="h-6 w-px bg-slate-200"></div>
                </a>
            </div>
            <div class="hidden sm:block sm:order-3 sm:py-4">
                <div class="flex items-center gap-x-6">
                    <div class="flex items-center gap-x-3 bg-gray-50 p-1 rounded-full border border-gray-100">
                        <div
                            class="size-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs">
                            {{ substr(Auth::user()->nom, 0, 1) }}
                        </div>
                        <div class="text-left pr-3">
                            <p class="text-[10px] font-black text-gray-800 uppercase tracking-tighter leading-none">
                                {{ Auth::user()->nom }}
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('formateur.logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="text-[10px] font-black text-gray-400 hover:text-red-500 uppercase tracking-widest transition-colors flex items-center gap-2 group">
                            <span>Quitter</span>
                            <svg class="size-4 group-hover:translate-x-1 transition-transform"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    @cannot('manage_queue')

    @endcannot

    <!-- Main Content -->
    <main x-data="dashboardManager({{ json_encode($tickets) }}, {{ json_encode($session) }}, '{{ csrf_token() }}', '{{ route('formateur.reorder', $session->id) }}', {{ auth()->user()->can('manage_queue') ? 'true' : 'false' }})" class="max-w-[85rem] mx-auto py-10 px-4 sm:px-6 lg:px-8 animate-fade-in">
        <!-- Dashboard Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div class="space-y-4">
                <div
                    class="inline-flex items-center gap-x-2 py-1 px-3 bg-blue-50 text-blue-700 rounded-lg text-[10px] font-black uppercase tracking-widest border border-blue-100">
                    <span class="size-2 rounded-full bg-blue-600 animate-pulse"></span>
                    Session Live
                </div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tighter uppercase">
                    {{ $session->nom }}
                </h1>
                <div class="flex items-center gap-x-6">
                    <div class="flex items-center gap-x-2 text-slate-500">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span class="text-sm font-bold uppercase tracking-tight">
                            {{ \Carbon\Carbon::parse($session->heureDebut)->format('H:i') }} —
                            {{ \Carbon\Carbon::parse($session->heureFin)->format('H:i') }}
                        </span>
                    </div>
                    <div
                        class="flex items-center gap-x-2 text-slate-900 bg-white border border-slate-200 py-1.5 px-4 rounded-xl shadow-sm">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Code:</span>
                        <span
                            class="text-sm font-black tracking-widest text-blue-600">{{ $session->codePresence }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-x-4">
                <div class="bg-white border border-slate-100 rounded-2xl px-6 py-4 shadow-sm flex items-center gap-x-4">
                    <div class="size-3 rounded-full bg-amber-500 animate-pulse shadow-[0_0_15px_rgba(245,158,11,0.5)]">
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest leading-none mb-1">En
                            attente</p>
                        <p class="text-xl font-black text-slate-900 leading-none"><span x-text="waitingCount"></span>
                            Candidats</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions & Toolbar -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-x-3">
                @can('manage_queue')
                    <button type="button" @click="callNext()"
                        class="py-3 px-6 inline-flex justify-center items-center gap-x-2 text-sm font-bold rounded-2xl border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none transition-all duration-300 shadow-xl shadow-blue-500/20 transform hover:scale-[1.02] active:scale-[0.98]">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                        Appeler le suivant
                    </button>
                @endcan
            </div>
            <div class="flex items-center gap-3 w-full max-w-sm">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-4">
                        <svg class="size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                    </div>
                    <input type="text" x-model="searchQuery"
                        class="py-2 px-4 ps-11 block w-full border-gray-200 rounded-xl text-sm focus:border-blue-500 focus:ring-blue-500 bg-white shadow-sm"
                        placeholder="Rechercher par nom...">
                </div>
            </div>
        </div>

        <!-- Candidate List Section -->
        <div class="bg-white border border-gray-100 rounded-[2.5rem] p-6 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 size-64 bg-blue-50 rounded-full blur-3xl opacity-20 -mr-32 -mt-32"></div>

            <div class="relative z-10">
                <div class="flex items-center justify-between mb-6 px-4">
                    <h2 class="font-black text-gray-800 uppercase tracking-widest text-xs">File d'attente active</h2>
                    @can('manage_queue')
                        <span class="text-[10px] text-gray-400 font-bold uppercase italic">Glissez pour réorganiser la
                            priorité</span>
                    @endcan
                </div>

                <div id="candidate-list" class="space-y-3 min-h-[100px]">
                    <template x-for="ticket in filteredTickets" :key="ticket.id">
                        <div :data-id="ticket.id"
                            class="candidate-card bg-white border border-gray-100 rounded-2xl p-4 transition-all duration-300"
                            :class="{ 
                                'bg-blue-50/50 border-blue-200 border-2 shadow-md': ticket.statut === 'en cours',
                                'bg-emerald-50/50 border-emerald-200 opacity-60': ticket.statut === 'terminée',
                                'bg-red-50/50 border-red-200 opacity-60': ticket.statut === 'absent'
                            }">
                            <div class="flex items-center gap-x-4 flex-grow">
                                @can('manage_queue')
                                    <div
                                        class="drag-handle text-gray-300 hover:text-gray-400 cursor-grab active:cursor-grabbing">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <circle cx="9" cy="5" r="1" />
                                            <circle cx="9" cy="12" r="1" />
                                            <circle cx="9" cy="19" r="1" />
                                            <circle cx="15" cy="5" r="1" />
                                            <circle cx="15" cy="12" r="1" />
                                            <circle cx="15" cy="19" r="1" />
                                        </svg>
                                    </div>
                                @endcan
                                <div class="size-12 rounded-full flex items-center justify-center font-bold text-base transition-all duration-500 shadow-sm"
                                    :class="{
                                        'bg-blue-600 text-white shadow-blue-500/30': ticket.statut === 'en cours',
                                        'bg-emerald-600 text-white': ticket.statut === 'terminée',
                                        'bg-red-600 text-white': ticket.statut === 'absent',
                                        'bg-gray-100 text-gray-600': ticket.statut === 'en attente'
                                    }" x-text="ticket.candidat.prenom[0] + ticket.candidat.nom[0]">
                                </div>
                                <div class="flex-grow">
                                    <h4 class="font-bold text-lg transition-colors"
                                        :class="ticket.statut === 'terminée' ? 'text-gray-400 line-through' : 'text-gray-900'">
                                        <span x-text="ticket.candidat.prenom"></span> <span
                                            x-text="ticket.candidat.nom"></span>
                                    </h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <template x-if="ticket.statut === 'en cours'">
                                            <span
                                                class="inline-flex items-center gap-x-1.5 py-1 px-2 rounded-lg text-[10px] font-black uppercase tracking-tighter bg-blue-600 text-white shadow-sm">
                                                <span
                                                    class="size-1.5 inline-block rounded-full bg-white animate-pulse"></span>
                                                En cours d'entretien
                                            </span>
                                        </template>
                                        <template x-if="ticket.statut === 'terminée'">
                                            <span
                                                class="inline-flex items-center gap-x-1.5 py-1 px-2 rounded-lg text-[10px] font-black uppercase tracking-tighter bg-emerald-600 text-white">
                                                <svg class="size-2.5" xmlns="http://www.w3.org/2000/svg" width="24"
                                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M20 6 9 17l-5-5" />
                                                </svg>
                                                Terminé
                                            </span>
                                        </template>
                                        <template x-if="ticket.statut === 'absent'">
                                            <span
                                                class="inline-flex items-center gap-x-1.5 py-1 px-2 rounded-lg text-[10px] font-black uppercase tracking-tighter bg-red-600 text-white">
                                                <svg class="size-2.5" xmlns="http://www.w3.org/2000/svg" width="24"
                                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                                </svg>
                                                Absent
                                            </span>
                                        </template>
                                        <template x-if="ticket.statut === 'en attente'">
                                            <span
                                                class="inline-flex items-center gap-x-1.5 py-1 px-2 rounded-lg text-[10px] font-black uppercase tracking-tighter bg-gray-100 text-gray-600 border border-gray-200">
                                                En attente
                                            </span>
                                        </template>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest pl-2"
                                            x-text="'N° ' + ticket.numeroOrdre"></span>
                                    </div>
                                </div>
                                @can('manage_queue')
                                    <div class="flex items-center">
                                        <div class="relative inline-flex w-full min-w-[150px]"
                                            x-data="{ openStatut: false }"
                                            @click.away="openStatut = false">
                                            <button type="button" @click="openStatut = !openStatut"
                                                class="w-full py-2 px-4 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl text-sm font-bold text-gray-700 transition-colors flex justify-between items-center gap-x-3 focus:outline-none focus:border-[#1A73E8] focus:ring-1 focus:ring-[#1A73E8] shadow-sm">
                                                <span x-text="ticket.statut.charAt(0).toUpperCase() + ticket.statut.slice(1)"></span>
                                                <svg :class="{ 'rotate-180': openStatut }"
                                                    class="size-4 text-gray-400 transition-transform duration-200"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="3" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                                                </svg>
                                            </button>

                                            <div x-show="openStatut" style="display: none;"
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="opacity-0 transform scale-95"
                                                x-transition:enter-end="opacity-100 transform scale-100"
                                                class="origin-top-left absolute left-0 top-full mt-2 min-w-full w-max z-[101] bg-white shadow-xl rounded-2xl p-2 border border-gray-100">
                                                <a @click.prevent="updateStatus(ticket, 'en attente'); openStatut = false"
                                                    class="flex items-center gap-x-3 py-2.5 px-3 rounded-xl text-sm font-bold text-gray-700 hover:bg-[#F8F9FA] cursor-pointer">
                                                    En attente
                                                </a>
                                                <a @click.prevent="updateStatus(ticket, 'en cours'); openStatut = false"
                                                    class="flex items-center gap-x-3 py-2.5 px-3 rounded-xl text-sm font-bold text-gray-700 hover:bg-[#F8F9FA] cursor-pointer">
                                                    En cours
                                                </a>
                                                <a @click.prevent="updateStatus(ticket, 'terminée'); openStatut = false"
                                                    class="flex items-center gap-x-3 py-2.5 px-3 rounded-xl text-sm font-bold text-gray-700 hover:bg-[#F8F9FA] cursor-pointer">
                                                    Terminée
                                                </a>
                                                <a @click.prevent="updateStatus(ticket, 'absent'); openStatut = false"
                                                    class="flex items-center gap-x-3 py-2.5 px-3 rounded-xl text-sm font-bold text-red-600 hover:bg-red-50 cursor-pointer">
                                                    Absent
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                            </div>
                        </div>
                    </template>
                    <div x-show="filteredTickets.length === 0" class="py-20 text-center">
                        <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">Aucun candidat dans la file
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <x-chat-widget />
</body>

</html>