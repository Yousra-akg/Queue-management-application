<!DOCTYPE html>
<html lang="fr" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sélection de Session - Portail Formateur SoliQueue</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        .geometric-bg {
            background-color: #F8F9FA;
            background-image:
                radial-gradient(#e2e8f0 1.5px, transparent 1.5px),
                radial-gradient(#e2e8f0 1.5px, #f8fafc 1.5px);
            background-size: 60px 60px;
            background-position: 0 0, 30px 30px;
        }
    </style>
</head>

<body class="bg-bgSurface min-h-screen font-sans geometric-bg">
    <!-- Header -->
    <header class="sticky top-0 z-50 w-full bg-white/80 backdrop-blur-md border-b border-slate-200">
        <nav class="max-w-[90rem] mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-x-4">
                <img src="{{ asset('img/logo.png') }}" alt="SoliQueue" class="h-10 w-auto">
                <div class="h-6 w-px bg-slate-200"></div>
            </div>

            <div class="flex items-center gap-x-6">
                <!-- User Profile Pill -->
                <div
                    class="group flex items-center gap-x-3 bg-white p-1.5 pe-4 rounded-full border border-slate-200 shadow-sm transition-all hover:shadow-md hover:border-blue-200 cursor-pointer">
                    <div
                        class="size-8 rounded-full bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-black text-xs shadow-inner ring-4 ring-blue-50 group-hover:ring-blue-100 transition-all">
                        {{ substr(Auth::user()->nom, 0, 1) }}
                    </div>
                    <div class="flex flex-col">
                        <span
                            class="text-[10px] font-black text-slate-800 uppercase tracking-widest leading-none">{{ Auth::user()->nom }}</span>
                        <span
                            class="text-[8px] font-bold text-blue-600 uppercase tracking-widest mt-0.5 opacity-80">Formateur</span>
                    </div>
                </div>

                <form action="{{ route('formateur.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="text-[10px] font-black text-slate-400 hover:text-red-500 uppercase tracking-[0.2em] transition-colors flex items-center gap-2 group">
                        <span>Déconnexion</span>
                        <svg class="size-3.5 group-hover:translate-x-1 transition-transform"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                        </svg>
                    </button>
                </form>
            </div>
        </nav>
    </header>

    <main class="max-w-[90rem] mx-auto px-6 py-12 lg:py-20 animate-fade-in">
        <!-- Hero Section -->
        <div class="text-center mb-20">
            <h1 class="text-5xl lg:text-6xl font-black text-slate-900 tracking-tighter mb-6 uppercase">
                Sessions d'entretiens
            </h1>
            <div class="flex items-center justify-center gap-4">
                <div class="h-1 w-12 bg-blue-600 rounded-full"></div>
                <p class="text-sm font-bold text-slate-400 uppercase tracking-[0.4em]">
                    GESTION EN TEMPS RÉEL
                </p>
                <div class="h-1 w-12 bg-blue-600 rounded-full"></div>
            </div>
        </div>

        <!-- Session Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-10">
            @foreach($sessions as $session)
                <div
                    class="group relative bg-white border border-slate-100 rounded-[2.5rem] p-10 transition-all duration-500 hover:shadow-[0_40px_80px_-15px_rgba(0,0,0,0.08)] hover:-translate-y-2 overflow-hidden">
                    <!-- Status Badge Overlay -->
                    <div class="absolute top-6 right-6">
                        <span
                            class="inline-flex items-center gap-x-2 py-2 px-4 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm
                            {{ $session->statut === 'en cours' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-slate-50 text-slate-400 border border-slate-100' }}">
                            @if($session->statut === 'en cours')
                                <span class="size-2 rounded-full bg-green-500 animate-pulse"></span>
                                Direct
                            @else
                                {{ $session->statut }}
                            @endif
                        </span>
                    </div>

                    <!-- Card Content -->
                    <div class="mb-10">
                        <div class="flex items-center gap-4 mb-8">
                            <div
                                class="size-14 rounded-2xl {{ $session->statut === 'en cours' ? 'bg-blue-600 text-white shadow-xl shadow-blue-200' : 'bg-slate-50 text-slate-400' }} flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                                <svg class="size-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Session</p>
                                <span
                                    class="text-xs font-black text-slate-900 uppercase">#{{ str_pad($session->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>

                        <h3
                            class="text-4xl font-black text-slate-900 leading-tight mb-4 tracking-tighter uppercase group-hover:text-blue-600 transition-colors">
                            {{ $session->nom }}
                        </h3>

                        <div class="flex flex-wrap gap-3 mt-6">
                            <div
                                class="flex items-center gap-x-2 py-1.5 px-3 bg-slate-50 border border-slate-100 rounded-xl">
                                <svg class="size-3.5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span class="text-[10px] font-black text-slate-600 uppercase tracking-tight">
                                    {{ \Carbon\Carbon::parse($session->heureDebut)->format('H:i') }} —
                                    {{ \Carbon\Carbon::parse($session->heureFin)->format('H:i') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-x-2 py-1.5 px-3 bg-blue-50 border border-blue-100 rounded-xl">
                                <svg class="size-3.5 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7.864 4.243A7.5 7.5 0 0 1 15.75 6.75V15a3 3 0 1 1-6 0V6.75a7.5 7.5 0 0 1 .114-1.282Zm0 0 .114-1.282A7.5 7.5 0 0 1 15.75 6.75V15a3 3 0 1 1-6 0V6.75a7.5 7.5 0 0 1 .114-1.282ZM9 9h.008v.008H9V9Zm0 3h.008v.008H9V12Zm0 3h.008v.008H9V15Zm3-6h.008v.008H12V9Zm0 3h.008v.008H12V12Zm0 3h.008v.008H12V15Zm3-6h.008v.008H15V9Zm0 3h.008v.008H15V12Zm0 3h.008v.008H15V15Z" />
                                </svg>
                                <span class="text-[10px] font-black text-blue-700 uppercase tracking-widest">
                                    CODE: {{ $session->codePresence }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-400 italic">
                                {{ \Carbon\Carbon::parse($session->dateEntretien)->translatedFormat('l d F Y') }}
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('formateur.dashboard', $session->id) }}"
                        class="w-full py-5 px-6 inline-flex justify-center items-center gap-x-3 text-sm font-black rounded-2xl border border-transparent {{ $session->statut === 'en cours' ? 'bg-blue-600 text-white shadow-xl shadow-blue-200' : ($session->statut === 'terminée' ? 'bg-slate-200 text-slate-700 hover:bg-slate-300' : 'bg-slate-900 text-white') }} hover:scale-[1.03] active:scale-[0.97] transition-all duration-300 uppercase tracking-widest group/btn">
                        {{ $session->statut === 'terminée' ? 'Consulter Historique' : 'Accéder au Dashboard' }}
                        <svg class="size-4 group-hover/btn:translate-x-1.5 transition-transform duration-300"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Footer Info -->
        <div class="mt-24 text-center">
            <p class="text-[10px] font-black uppercase text-slate-400 tracking-[0.4em] mb-4">SoliCode Queue Management
                System • Version 1.0.4</p>
            <div class="h-px w-24 bg-slate-200 mx-auto"></div>
        </div>
    </main>
</body>

</html>