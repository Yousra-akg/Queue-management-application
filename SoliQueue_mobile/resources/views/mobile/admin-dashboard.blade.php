<!DOCTYPE html>
<html lang="fr" class="bg-slate-900 border-x border-slate-200 shadow-2xl h-[100dvh] mx-auto w-full max-w-[430px] overflow-hidden">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>SoliCode Queue - Admin Mobile</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        solicode: {
                            blue: '#1A73E8',
                            green: '#34A853',
                            yellow: '#FBBC05',
                            red: '#EA4335',
                            dark: '#202124',
                            surface: '#F8F9FA'
                        }
                    }
                }
            }
        };
    </script>
    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .tap-scale:active { transform: scale(0.95); transition: transform 0.1s; }
        @keyframes fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
    </style>
</head>

<body class="bg-slate-50 h-[100dvh] flex flex-col font-sans relative overflow-hidden">

    <!-- Auth Screen -->
    <div id="auth-screen" class="flex-1 flex flex-col items-center justify-center p-6 bg-white z-50">
        <div class="w-full space-y-8 animate-fade-in">
            <div class="text-center">
                <div class="size-20 bg-solicode-blue rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-xl shadow-blue-200">
                    <i data-lucide="shield-check" class="text-white size-10"></i>
                </div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter">SoliCode <span class="text-solicode-blue">Admin</span></h1>
                <p class="text-slate-500 font-medium mt-2">Connectez-vous pour gérer la file</p>
            </div>

            <form id="login-form" class="space-y-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Identifiant</label>
                    <div class="relative">
                        <i data-lucide="user" class="absolute left-4 top-1/2 -translate-y-1/2 size-5 text-slate-400"></i>
                        <input type="text" placeholder="admin@solicode.com"
                            class="w-full pl-12 pr-4 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-solicode-blue focus:ring-0 outline-none transition-all font-semibold">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Mot de passe</label>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 size-5 text-slate-400"></i>
                        <input type="password" placeholder="••••••••"
                            class="w-full pl-12 pr-4 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-solicode-blue focus:ring-0 outline-none transition-all font-semibold">
                    </div>
                </div>

                <button type="button" id="login-btn"
                    class="w-full py-4 bg-solicode-blue text-white rounded-2xl font-black text-lg shadow-xl shadow-blue-500/20 tap-scale transition-all hover:bg-blue-700">
                    Se connecter
                </button>
            </form>
            <p class="text-center text-xs text-slate-400 font-medium italic">Accès restreint aux administrateurs SoliCode</p>
        </div>
    </div>

    <!-- Admin Layout -->
    <div id="admin-layout" class="hidden flex-col h-full overflow-hidden">
        <!-- Header -->
        <header class="bg-white border-b border-slate-200 py-4 px-6 fixed top-0 w-full max-w-[430px] z-40 flex items-center justify-between">
            <div class="flex items-center gap-x-2">
                <div class="size-8 bg-solicode-blue rounded-xl flex items-center justify-center">
                    <i data-lucide="layout-dashboard" class="text-white size-4"></i>
                </div>
                <span class="text-xl font-black text-slate-900 tracking-tighter">SoliCode</span>
            </div>
            <button class="size-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-500 tap-scale border border-slate-100">
                <i data-lucide="bell" class="size-5"></i>
            </button>
        </header>

        <!-- Scrollable Main Content -->
        <main class="flex-1 overflow-y-auto hide-scrollbar pt-20 pb-24 px-6 space-y-8 bg-slate-50">
            <!-- Global Stats -->
            <section class="space-y-4 animate-fade-in" style="animation-delay: 0.1s;">
                <h2 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Statistiques Globales</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white p-5 rounded-[2rem] border border-slate-200 shadow-sm">
                        <div class="size-10 bg-blue-50 rounded-2xl flex items-center justify-center mb-3">
                            <i data-lucide="users" class="text-solicode-blue size-5"></i>
                        </div>
                        <p class="text-2xl font-black text-slate-900">{{ $stats['tickets_emis'] ?? 0 }}</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Tickets Émis</p>
                    </div>
                    <div class="bg-white p-5 rounded-[2rem] border border-slate-200 shadow-sm">
                        <div class="size-10 bg-yellow-50 rounded-2xl flex items-center justify-center mb-3">
                            <i data-lucide="clock" class="text-solicode-yellow size-5"></i>
                        </div>
                        <p class="text-2xl font-black text-slate-900">{{ $stats['en_attente'] ?? 0 }}</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">En Attente</p>
                    </div>
                    <div class="bg-white p-5 rounded-[2rem] border border-slate-200 shadow-sm">
                        <div class="size-10 bg-green-50 rounded-2xl flex items-center justify-center mb-3">
                            <i data-lucide="check-circle" class="text-solicode-green size-5"></i>
                        </div>
                        <p class="text-2xl font-black text-slate-900">{{ $stats['traites'] ?? 0 }}</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Traités</p>
                    </div>
                    <div class="bg-white p-5 rounded-[2rem] border border-slate-200 shadow-sm">
                        <div class="size-10 bg-red-50 rounded-2xl flex items-center justify-center mb-3">
                            <i data-lucide="user-minus" class="text-solicode-red size-5"></i>
                        </div>
                        <p class="text-2xl font-black text-slate-900">{{ $stats['absents'] ?? 0 }}</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Absents</p>
                    </div>
                </div>
            </section>

            <!-- Sessions View -->
            <section class="space-y-4 animate-fade-in" style="animation-delay: 0.2s;">
                <div class="flex items-center justify-between px-1">
                    <h2 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">État des Sessions</h2>
                    <span class="text-[10px] font-bold text-solicode-blue bg-blue-50 px-2.5 py-1 rounded-lg uppercase">{{ count($sessions) }} Actives</span>
                </div>

                <div class="space-y-4">
                    @foreach($sessions as $session)
                    <div class="bg-white rounded-[2.5rem] border border-slate-200 p-6 flex flex-col gap-y-4 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-blue-600/5 -mr-8 -mt-8 rounded-full"></div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-x-3">
                                <div class="size-12 rounded-2xl bg-solicode-blue text-white flex items-center justify-center font-black italic text-xl">S{{ $loop->iteration }}</div>
                                <div>
                                    <h3 class="font-black text-slate-900 leading-tight">{{ $session['nom'] }}</h3>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Session #{{ $session['id'] }}</p>
                                </div>
                            </div>
                            <span class="py-1 px-3 bg-green-100 text-solicode-green rounded-full text-[9px] font-black uppercase tracking-tighter {{ $session['statut'] === 'En cours' ? 'animate-pulse' : '' }}">
                                {{ $session['statut'] }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                <p class="text-[8px] font-black text-slate-400 uppercase mb-1.5">Candidat Actuel</p>
                                <p class="text-sm font-black text-solicode-blue">{{ $session['candidat_actuel'] }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                <p class="text-[8px] font-black text-slate-400 uppercase mb-1.5">Prochain</p>
                                <p class="text-sm font-black text-slate-900">{{ $session['prochain_candidat'] }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
        </main>

        <!-- Bottom Navigation -->
        <nav class="fixed bottom-0 w-full max-w-[430px] bg-white border-t border-slate-200 z-50">
            <div class="flex justify-around items-center h-[72px] px-2 pb-safe">
                <button class="flex flex-col items-center justify-center text-solicode-blue gap-1 min-w-[64px] tap-scale">
                    <i data-lucide="bar-chart-3" class="size-6"></i>
                    <span class="text-[10px] font-bold uppercase tracking-wide leading-none">Dashboard</span>
                </button>
                <button class="flex flex-col items-center justify-center text-slate-400 hover:text-solicode-blue transition-colors gap-1 min-w-[64px] tap-scale">
                    <i data-lucide="layers" class="size-6"></i>
                    <span class="text-[10px] font-bold uppercase tracking-wide leading-none">Sessions</span>
                </button>
                <button id="logout-btn" class="flex flex-col items-center justify-center text-slate-400 hover:text-solicode-blue transition-colors gap-1 min-w-[64px] tap-scale">
                    <i data-lucide="log-out" class="size-6"></i>
                    <span class="text-[10px] font-bold uppercase tracking-wide leading-none">Quitter</span>
                </button>
            </div>
        </nav>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            const authScreen = document.getElementById('auth-screen');
            const adminLayout = document.getElementById('admin-layout');
            const loginBtn = document.getElementById('login-btn');
            const logoutBtn = document.getElementById('logout-btn');

            loginBtn.addEventListener('click', () => {
                authScreen.classList.add('opacity-0', '-translate-y-4', 'transition-all', 'duration-500');
                setTimeout(() => {
                    authScreen.style.display = 'none';
                    adminLayout.classList.remove('hidden');
                    adminLayout.classList.add('flex', 'animate-fade-in');
                    window.scrollTo(0, 0);
                }, 500);
            });

            logoutBtn.addEventListener('click', () => {
                adminLayout.classList.add('opacity-0', 'scale-95', 'transition-all', 'duration-500');
                setTimeout(() => {
                    location.reload();
                }, 500);
            });
        });
    </script>
</body>
</html>
