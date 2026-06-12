<!DOCTYPE html>
<html lang="fr" class="h-full light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Formateur - SoliQueue</title>
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
                <div class="bg-blue-600 text-white text-[9px] font-black uppercase tracking-widest py-2 px-12 rotate-45 translate-x-10 translate-y-3 shadow-lg">FORMATEUR</div>
            </div>

            <div class="text-center mb-8">
                <div class="mb-6 flex flex-col items-center">
                    <img src="{{ asset('img/logo.png') }}" alt="SoliQueue" class="h-16 w-auto mb-4">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight uppercase">Espace Formateur</h1>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Gestion des files d'attente</p>
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

            <form action="{{ route('formateur.login') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-bold mb-2 text-slate-700">Email Professionnel</label>
                    <div class="relative group">
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="py-4 px-4 ps-12 block w-full border-slate-200 rounded-2xl text-sm font-medium focus:border-blue-600 focus:ring-blue-600 bg-slate-50/30 focus:bg-white transition-all shadow-inner"
                            placeholder="nom@solicode.co">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                            <svg class="size-5 text-slate-400 group-focus-within:text-blue-600 transition-colors" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="16" x="2" y="4" rx="2" /><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                        </div>
                    </div>
                    @error('email')
                        <p class="text-xs text-red-600 mt-2 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-bold mb-2 text-slate-700">Mot de passe</label>
                    <div class="relative group">
                        <input type="password" name="password" id="password" required
                            class="py-4 px-4 ps-12 block w-full border-slate-200 rounded-2xl text-sm font-medium focus:border-blue-600 focus:ring-blue-600 bg-slate-50/30 focus:bg-white transition-all shadow-inner"
                            placeholder="••••••••">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                            <svg class="size-5 text-slate-400 group-focus-within:text-blue-600 transition-colors" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" /><path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </div>
                    </div>
                    @error('password')
                        <p class="text-xs text-red-600 mt-2 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full py-5 px-6 inline-flex justify-center items-center gap-x-3 text-sm font-black rounded-2xl border border-transparent bg-blue-600 text-white hover:bg-blue-700/90 focus:outline-none focus:ring-2 focus:ring-blue-600 transition-all duration-500 shadow-xl shadow-blue-600/30 transform hover:scale-[1.02] active:scale-[0.98] group uppercase tracking-widest">
                        Se connecter
                        <svg class="size-4 group-hover:translate-x-1 transition-transform" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-10 text-center">
            <p class="text-[10px] font-black uppercase text-slate-400 tracking-[0.3em] mb-2">SoliCode Formateur Portal</p>
            <div class="flex items-center justify-center gap-x-1.5 opacity-60">
                <div class="size-1.5 rounded-full bg-green-500 animate-pulse"></div>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Système Opérationnel</span>
            </div>
        </div>
    </main>
</body>
</html>

