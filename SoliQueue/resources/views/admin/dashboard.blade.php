@extends('layouts.admin')

@section('title', 'Dashboard - SoliQueue Stats')
@section('breadcrumb', 'Aperçu Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tighter uppercase italic">Dashboard</h1>
            <p class="text-[11px] text-gray-400 font-bold uppercase tracking-widest mt-1">Analyse des performances en temps réel</p>
        </div>
        <a href="{{ route('admin.sessions.index') }}"
            class="py-3 px-6 inline-flex items-center gap-x-3 text-xs font-black rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition-all shadow-lg shadow-blue-100/50">
            <svg class="size-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M5 12h14" />
                <path d="M12 5v14" />
            </svg>
            Gérer les Sessions
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Candidats -->
        <div class="group flex flex-col bg-white border border-gray-100 shadow-sm rounded-[2rem] p-6 transition-all hover:shadow-md">
            <div class="flex items-center gap-x-4">
                <div class="size-12 flex-shrink-0 inline-flex items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                    <svg class="size-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-black uppercase text-gray-400 tracking-widest mb-1">Candidats</p>
                    <h3 class="text-2xl font-black text-gray-900 tracking-tight">{{ $totalCandidats }}</h3>
                </div>
            </div>
        </div>

        <!-- Sessions -->
        <div class="group flex flex-col bg-white border border-gray-100 shadow-sm rounded-[2rem] p-6 transition-all hover:shadow-md">
            <div class="flex items-center gap-x-4">
                <div class="size-12 flex-shrink-0 inline-flex items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                    <svg class="size-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-black uppercase text-gray-400 tracking-widest mb-1">Sessions</p>
                    <h3 class="text-2xl font-black text-gray-900 tracking-tight">{{ $totalSessions }}</h3>
                </div>
            </div>
        </div>

        <!-- Terminés -->
        <div class="group flex flex-col bg-white border border-gray-100 shadow-sm rounded-[2rem] p-6 transition-all hover:shadow-md">
            <div class="flex items-center gap-x-4">
                <div class="size-12 flex-shrink-0 inline-flex items-center justify-center rounded-2xl bg-green-50 text-green-600">
                    <svg class="size-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-black uppercase text-gray-400 tracking-widest mb-1">Sessions terminées</p>
                    <h3 class="text-2xl font-black text-gray-900 tracking-tight">{{ $sessionsTerminees }}</h3>
                </div>
            </div>
        </div>

        <!-- Taux Présence -->
        <div class="group flex flex-col bg-white border border-gray-100 shadow-sm rounded-[2rem] p-6 transition-all hover:shadow-md">
            <div class="flex items-center gap-x-4">
                <div class="size-12 flex-shrink-0 inline-flex items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                    <svg class="size-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21.21 15.89A10 10 0 1 1 8 2.83" />
                        <path d="M22 12A10 10 0 0 0 12 2v10z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-black uppercase text-gray-400 tracking-widest mb-1">Présence</p>
                    <h3 class="text-2xl font-black text-gray-900 tracking-tight">{{ $tauxPresence }}%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Monitoring Grid -->
    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Session Fill Rate -->
        <div class="bg-white border border-gray-100 shadow-sm rounded-3xl p-8">
            <div class="flex justify-between items-center mb-8">
                <h4 class="text-lg font-black text-gray-900 uppercase tracking-tight italic">Remplissage des Sessions</h4>
                <a href="{{ route('admin.affectations') }}" class="text-[10px] font-black text-[#1A73E8] uppercase hover:underline tracking-widest">Voir tout</a>
            </div>
            <div class="space-y-8">
                @foreach($sessions as $session)
                @php
                    $fillRate = $session->capaciteMax > 0 ? ($session->candidats_count / $session->capaciteMax) * 100 : 0;
                    $colorFull = $fillRate >= 100 ? 'bg-indigo-600' : ($fillRate > 50 ? 'bg-blue-600' : 'bg-emerald-500');
                @endphp
                <div>
                    <div class="flex justify-between items-center mb-2.5">
                        <div class="flex flex-col">
                            <span class="text-[11px] font-black text-gray-900 uppercase tracking-widest">{{ $session->nom }}</span>
                        </div>
                        <span class="text-[10px] font-black text-gray-500 uppercase">{{ $session->candidats_count }}/{{ $session->capaciteMax }}</span>
                    </div>
                    <div class="relative w-full h-2.5 bg-gray-100 rounded-full overflow-hidden shadow-inner border border-gray-50/50">
                        <div class="{{ $colorFull }} h-full transition-all duration-1000 shadow-lg" style="width: {{ $fillRate }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white border border-gray-100 shadow-sm rounded-3xl p-8">
            <h4 class="text-lg font-black text-gray-900 uppercase tracking-tight italic mb-8">Activité Recente</h4>
            <div class="relative">
                <div class="absolute top-0 bottom-0 left-[19px] w-px bg-gray-100"></div>
                <ul class="space-y-10 relative">
                    @foreach($activites as $act)
                    <li class="flex gap-x-6">
                        <div class="size-10 rounded-xl bg-{{ $act['couleur'] }}/10 flex items-center justify-center text-{{ $act['couleur'] }} flex-shrink-0 z-10 border-4 border-white shadow-sm">
                            @if($act['type'] == 'session')
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            @elseif($act['type'] == 'candidat')
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            @else
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $act['titre'] }}</p>
                            <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-widest">{{ $act['temps'] }}</p>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
