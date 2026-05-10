<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SoliQueue Admin')</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        :root {
            --primary: #1A73E8;
            --success: #34A853;
            --alert: #FBBC05;
            --danger: #EA4335;
            --bgSurface: #F8F9FA;
            --textDark: #202124;
        }
    </style>
</head>
<body class="bg-bgSurface min-h-screen font-sans antialiased text-gray-800">
    <!-- Navigation Sidebar (Desktop) -->
    <div id="application-sidebar"
        class="hs-overlay hs-overlay-open:translate-x-0 -translate-x-full transition-all duration-300 transform fixed top-0 start-0 bottom-0 z-[60] w-64 bg-[#0B1120] border-e border-slate-800 pt-7 pb-10 overflow-y-auto lg:block lg:translate-x-0 lg:end-auto lg:bottom-0">
        <div class="px-8 mt-4 mb-10">
            <div class="flex flex-col items-center">
                <img src="{{ asset('images/logo.png') }}" alt="SoliQueue Logo" class="h-24 mb-6 object-contain scale-125">
                <div class="h-px w-full bg-slate-800 opacity-50"></div>
            </div>
        </div>

        <nav class="p-0 w-full flex flex-col flex-wrap">
            <ul class="space-y-1">
                <li class="px-6 mb-2">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Aperçu</p>
                </li>
                <li>
                    <a class="w-full flex items-center gap-x-3.5 py-3 px-6 text-sm font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-primary/10 text-white border-l-4 border-primary' : 'text-slate-400 hover:text-white border-l-4 border-transparent hover:bg-slate-800/50' }}"
                        href="{{ route('admin.dashboard') }}">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="7" height="9" x="3" y="3" rx="1" />
                            <rect width="7" height="5" x="14" y="3" rx="1" />
                            <rect width="7" height="9" x="14" y="12" rx="1" />
                            <rect width="7" height="5" x="3" y="16" rx="1" />
                        </svg>
                        Dashboard
                    </a>
                </li>
                
                <li class="px-6 mt-6 mb-2">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Management</p>
                </li>
                <li>
                    <a class="w-full flex items-center gap-x-3.5 py-3 px-6 text-sm font-bold transition-all {{ request()->routeIs('admin.affectations') ? 'bg-primary/10 text-white border-l-4 border-primary' : 'text-slate-400 hover:text-white border-l-4 border-transparent hover:bg-slate-800/50' }}"
                        href="{{ route('admin.affectations') }}">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m16 16 3-8 3 8c-.87.06-1.7.21-2.46.44l-.54 1.56-2-6-2 6-.54-1.56C17.7 16.21 16.87 16.06 16 16Z" />
                            <path d="M7 21h10" />
                            <path d="M12 21V3" />
                            <path d="M7 3h10" />
                        </svg>
                        Affectations
                    </a>
                </li>
                <li>
                    <a class="w-full flex items-center gap-x-3.5 py-3 px-6 text-sm font-bold transition-all {{ request()->routeIs('admin.sessions.index') ? 'bg-primary/10 text-white border-l-4 border-primary' : 'text-slate-400 hover:text-white border-l-4 border-transparent hover:bg-slate-800/50' }}"
                        href="{{ route('admin.sessions.index') }}">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        Sessions
                    </a>
                </li>
                <li>
                    <a class="w-full flex items-center gap-x-3.5 py-3 px-6 text-sm font-bold transition-all {{ request()->routeIs('admin.candidats.index') ? 'bg-primary/10 text-white border-l-4 border-primary' : 'text-slate-400 hover:text-white border-l-4 border-transparent hover:bg-slate-800/50' }}"
                        href="{{ route('admin.candidats.index') }}">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        Candidats
                    </a>
                </li>
                <li>
                    <a class="w-full flex items-center gap-x-3.5 py-3 px-6 text-sm font-bold transition-all {{ request()->routeIs('admin.formateurs.index') ? 'bg-primary/10 text-white border-l-4 border-primary' : 'text-slate-400 hover:text-white border-l-4 border-transparent hover:bg-slate-800/50' }}"
                        href="{{ route('admin.formateurs.index') }}">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                        </svg>
                        Formateurs
                    </a>
                </li>
            </ul>

            <div class="mt-auto px-6 pt-10">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-x-3.5 py-3 px-6 text-sm font-bold text-slate-400 hover:text-red-400 transition-all border-l-4 border-transparent hover:bg-red-400/10">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" y1="12" x2="9" y2="12" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="w-full lg:ps-64 animate-fade-in">
        <div class="p-4 sm:p-6 lg:p-10">
            <!-- Top Header -->
            <div class="flex justify-between items-center mb-6">
                <!-- Breadcrumbs -->
                <ol class="flex items-center whitespace-nowrap min-w-0" aria-label="Breadcrumb">
                    <li class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center">
                        SoliQueue
                        <svg class="flex-shrink-0 mx-3 overflow-visible size-2 text-slate-300" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </li>
                    <li class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center">
                        Admin
                        <svg class="flex-shrink-0 mx-3 overflow-visible size-2 text-slate-300" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </li>
                    <li class="text-[10px] font-black text-slate-900 uppercase tracking-widest truncate" aria-current="page">
                        @yield('breadcrumb', 'Aperçu')
                    </li>
                </ol>

                <!-- Premium Profile Pill -->
                <div class="group flex items-center gap-x-3 bg-white/80 backdrop-blur-sm p-1.5 pe-4 rounded-full border border-slate-200 shadow-sm transition-all duration-300 hover:shadow-md hover:border-blue-200 hover:-translate-y-0.5 cursor-pointer">
                    <div class="size-9 rounded-full bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-black text-sm shadow-inner ring-4 ring-blue-50 group-hover:ring-blue-100 transition-all">
                        {{ substr(Auth::guard('web')->user()->nom, 0, 1) }}
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-slate-800 uppercase tracking-[0.2em] leading-none">{{ Auth::guard('web')->user()->nom }}</span>
                        <span class="text-[8px] font-bold text-blue-600 uppercase tracking-widest mt-1 opacity-80 group-hover:opacity-100 transition-opacity">Administrateur</span>
                    </div>
                </div>
            </div>

            @yield('content')
        </div>
    </div>

    <!-- Global Toast Notification -->
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed bottom-8 left-1/2 -translate-x-1/2 z-[100]">
        <div class="bg-[#0B1120] text-white px-6 py-4 rounded-full shadow-2xl flex items-center gap-3 font-black tracking-widest text-xs uppercase border border-slate-800">
            <div class="size-3 rounded-full bg-[#00D26A]"></div>
            {{ session('success') }}
        </div>
    </div>
    @endif

    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#1A73E8',
                    confirmButtonText: 'D\'accord',
                    customClass: {
                        confirmButton: 'rounded-xl px-6 py-2.5 font-bold'
                    }
                });
            @endif
        });
    </script>
</body>
</html>
