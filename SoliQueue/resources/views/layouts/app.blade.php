<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SoliQueue' }} - Portail Candidat</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <!-- Tailwind CSS (via CDN pour la démo, mais configuré pour un look premium) -->
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
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
    </style>
    @yield('styles')
</head>
<body class="bg-[#f8fafc] h-full font-sans text-slate-900 overflow-x-hidden">
    
    <div class="min-h-screen flex flex-col">
        <!-- Navigation Header -->
        <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50">
            <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="size-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="text-white size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xl font-black tracking-tight text-slate-800">Soli<span class="text-blue-600">Queue</span></span>
                </div>

                @if(Session::has('candidat_id'))
                <div class="flex items-center gap-4">
                    <div class="hidden sm:block text-right">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Connecté en tant que</p>
                        <p class="text-sm font-bold text-slate-800">{{ $candidat_name ?? 'Candidat' }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="p-2 text-slate-400 hover:text-red-500 transition-colors">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
                @endif
            </nav>
        </header>

        <!-- Main Content Area -->
        <main class="flex-grow flex items-center justify-center p-4">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="py-8 text-center border-t border-slate-200 bg-white">
            <p class="text-slate-400 text-xs font-medium tracking-wide">SoliCode © 2026 — Plateforme de Gestion des Files d'Attente</p>
        </footer>
    </div>

    @yield('scripts')
</body>
</html>
