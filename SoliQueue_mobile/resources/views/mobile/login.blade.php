<!DOCTYPE html>
<html lang="fr" class="bg-[#f8fafc] font-sans">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>SoliCode Queue - Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        document.documentElement.style.opacity = "1";
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
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
        @keyframes fade-in-up {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</head>

<body class="bg-[#f8fafc] min-h-screen flex flex-col font-sans relative select-none max-w-[430px] mx-auto shadow-2xl border-x border-gray-100">

    <!-- Top Bar -->
    <header class="sticky top-0 w-full h-16 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center justify-between px-6 z-50">
        <span class="text-xl font-black text-blue-600 tracking-tighter">SoliQueue</span>
        <span class="text-[10px] font-black uppercase text-gray-400 tracking-wider">Mobile Portal</span>
    </header>

    <main class="flex-grow flex flex-col justify-center px-6 py-12 animate-fade-in-up">
        
        <!-- Brand Logo and Welcome -->
        <div class="flex flex-col items-center text-center mb-8">
            <div class="relative mb-6">
                <div class="absolute inset-0 bg-blue-500 rounded-full blur-2xl opacity-10 animate-pulse"></div>
                <div class="relative size-16 rounded-full bg-blue-50 flex items-center justify-center border border-blue-100 text-blue-600">
                    <svg class="size-8" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 11h-6" />
                    </svg>
                </div>
            </div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-2">Bienvenue !</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest leading-relaxed">
                Connectez-vous pour obtenir votre ticket d'entretien
            </p>
        </div>

        <!-- Alert messages -->
        @if(session('error') || isset($error))
            <div class="bg-red-50 border border-red-100 text-red-600 p-4 rounded-2xl text-xs font-bold mb-6 text-center animate-pulse">
                {{ session('error') ?? $error }}
            </div>
        @endif

        <!-- Login Form -->
        <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
            <form action="/login" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="cin" class="block text-[10px] font-black text-gray-900 uppercase tracking-widest mb-2.5">
                        Votre Code d'Identité (CIN)
                    </label>
                    <input type="text" name="cin" id="cin" required placeholder="Ex: AB123456" 
                        class="w-full py-4 px-5 bg-gray-50 border border-gray-200 focus:bg-white focus:border-blue-600 focus:ring-1 focus:ring-blue-600 rounded-2xl text-sm font-semibold text-gray-800 uppercase tracking-wide transition-all outline-none placeholder:text-gray-400">
                </div>

                <button type="submit" 
                    class="w-full py-4 px-6 inline-flex justify-center items-center gap-x-3 text-sm font-black rounded-2xl bg-blue-600 text-white hover:bg-blue-700 active:scale-98 transition-all duration-200 shadow-xl shadow-blue-500/20">
                    Se connecter
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14"></path>
                        <path d="m12 5 7 7-7 7"></path>
                    </svg>
                </button>
            </form>
        </div>

    </main>

    <footer class="p-6 text-center text-[10px] text-gray-400 font-medium uppercase tracking-widest border-t border-gray-50 bg-white">
        SoliQueue &copy; 2026 - Tous droits réservés
    </footer>

</body>

</html>
