<!DOCTYPE html>
<html lang="fr" class="h-full light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - SoliCode Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        .geometric-bg {
            background-color: #F8F9FA;
            background-image:
                radial-gradient(#e2e8f0 1.5px, transparent 1.5px),
                radial-gradient(#e2e8f0 1.5px, #f8fafc 1.5px);
            background-size: 60px 60px;
            background-position: 0 0, 30px 30px;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-4px); }
            75% { transform: translateX(4px); }
        }
        .animate-shake { animation: shake 0.2s ease-in-out 0s 2; }
    </style>
</head>
<body class="bg-bgSurface flex min-h-screen items-center py-6 font-sans geometric-bg">
    <main class="w-full max-w-[420px] mx-auto p-4 animate-slide-up">
        <div class="bg-white border border-slate-100 rounded-[3rem] shadow-[0_25px_60px_rgba(0,0,0,0.06)] p-8 sm:p-10 relative overflow-hidden transition-all duration-500 hover:shadow-blue-600/5">
            <div class="absolute top-0 right-0">
                <div class="bg-blue-600 text-white text-[9px] font-black uppercase tracking-widest py-2 px-12 rotate-45 translate-x-10 translate-y-3 shadow-lg">RESTRICTED</div>
            </div>

            <div class="text-center mb-8">
                <div class="mb-6 flex flex-col items-center">
                    <img src="{{ asset('img/logo.png') }}" alt="SoliCode" class="h-16 w-auto mb-4">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight uppercase">Administration</h1>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Console de contrôle sécurisée</p>
                </div>
            </div>

            @if($errors->any())
            <div class="mb-6 py-3.5 px-4 bg-red-50 border border-red-100 text-red-600 rounded-2xl flex items-center gap-3 animate-shake">
                <svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <p class="text-xs font-black uppercase tracking-tight">{{ $errors->first() }}</p>
            </div>
            @endif

            <form action="{{ route('admin.login') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-bold mb-2 text-slate-700">Email Administrateur</label>
                    <div class="relative group">
                        <input type="email" id="email" name="email" required
                            class="py-4 px-4 ps-12 block w-full border-slate-200 rounded-2xl text-sm font-medium focus:border-blue-600 focus:ring-blue-600 bg-slate-50/30 focus:bg-white transition-all shadow-inner @error('email') border-red-500 @enderror"
                            placeholder="admin@solicode.co" value="{{ old('email') }}">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                            <svg class="size-5 text-slate-400 group-focus-within:text-blue-600 transition-colors" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </div>
                    </div>
                    @error('email')
                        <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-bold mb-2 text-slate-700">Clé Maîtresse</label>
                    <div class="relative group">
                        <input type="password" id="password" name="password" required
                            class="py-4 px-4 ps-12 block w-full border-slate-200 rounded-2xl text-sm font-medium focus:border-blue-600 focus:ring-blue-600 bg-slate-50/30 focus:bg-white transition-all shadow-inner"
                            placeholder="••••••••">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                            <svg class="size-5 text-slate-400 group-focus-within:text-blue-600 transition-colors" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="shrink-0 mt-0.5 border-slate-200 rounded text-blue-600 focus:ring-blue-500">
                    <label for="remember" class="ms-3 text-xs font-bold text-slate-500 uppercase tracking-widest">Rester connecté</label>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full py-5 px-6 inline-flex justify-center items-center gap-x-3 text-sm font-black rounded-2xl border border-transparent bg-blue-600 text-white hover:bg-blue-700/90 focus:outline-none focus:ring-2 focus:ring-blue-600 transition-all duration-500 shadow-xl shadow-blue-600/30 transform hover:scale-[1.02] active:scale-[0.98] group uppercase tracking-widest">
                        Se connecter
                        <svg class="size-4 group-hover:translate-x-1 transition-transform" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                            <polyline points="10 17 15 12 10 7" />
                            <line x1="15" x2="3" y1="12" y2="12" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-10 text-center">
            <p class="text-[10px] font-black uppercase text-slate-400 tracking-[0.3em] mb-2">SoliCode Admin Dashboard</p>
            <div class="flex items-center justify-center gap-x-1.5">
                <svg class="size-3 text-green-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    <polyline points="9 12 11 14 15 10" />
                </svg>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Sécurisé par SSL 256-bit</span>
            </div>
        </div>
    </main>
</body>
</html>

