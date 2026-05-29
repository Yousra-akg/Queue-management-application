<!DOCTYPE html>
<html lang="fr" class="bg-[#f8fafc] font-sans">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>SoliCode Queue - Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Force l'affichage si le CDN met du temps à charger
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
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

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
    <header
        class="sticky top-0 w-full h-16 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center justify-between px-6 z-50">
        <a class="text-xl font-black text-blue-600 tracking-tighter" href="#">SoliCode</a>
        <div
            class="size-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs border border-blue-100 italic">
            {{ strtoupper(substr($etudiant['nom'] ?? 'C', 0, 1)) }}
        </div>
    </header>

    <main class="flex-grow pb-32 px-6 flex flex-col justify-center animate-fade-in-up">
        
        @if(session('error'))
            <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm font-bold mb-4 text-center">
                {{ session('error') }}
            </div>
        @endif

        <!-- Success Header -->
        <div class="flex flex-col items-center text-center mt-8">
            <div class="relative mb-6">
                <div class="absolute inset-0 bg-green-500 rounded-full blur-2xl opacity-10 animate-pulse"></div>
                <div
                    class="relative size-20 rounded-full bg-white border-4 border-green-50 flex items-center justify-center shadow-lg shadow-green-100">
                    <svg class="size-10 text-green-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-2">Félicitations !</h1>
            <p class="text-sm text-gray-500 font-medium mb-8 px-4 leading-relaxed">
                Vous avez réussi le QCM avec succès. Votre profil a été validé par notre système.
            </p>

            <!-- Candidate Info Card -->
            <div class="w-full bg-white rounded-3xl border border-gray-100 p-6 shadow-sm mb-8 text-left space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Candidat</p>
                        <p class="text-lg font-bold text-gray-800 tracking-tight">{{ $etudiant['nom'] ?? 'Candidat' }} {{ $etudiant['prenom'] ?? '' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">ID QCM</p>
                        <p class="text-xs font-black text-blue-600 font-mono">#QCM-CANDIDAT-{{ $etudiant['id'] }}</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-50 flex items-center justify-between">
                    <span
                        class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-[10px] font-black bg-green-50 text-green-700 uppercase tracking-tight">
                        <span class="size-1.5 inline-block rounded-full bg-green-600"></span>
                        QCM Validé
                    </span>
                    <span class="text-[10px] text-gray-400 font-bold italic">Prêt pour ticket</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Fixed Bottom CTA -->
    <footer class="fixed bottom-0 w-full max-w-[430px] p-6 bg-white border-t border-gray-100 z-50">
        <form action="/generate-ticket" method="POST">
            @csrf
            <input type="hidden" name="etudiant_id" value="{{ $etudiant['id'] ?? '' }}">
            <input type="hidden" name="etudiant_name" value="{{ trim(($etudiant['nom'] ?? '') . ' ' . ($etudiant['prenom'] ?? '')) }}">
            <button type="submit"
                class="w-full py-4 px-6 inline-flex justify-center items-center gap-x-3 text-lg font-black rounded-2xl bg-blue-600 text-white hover:bg-blue-700 active:scale-95 transition-all duration-300 shadow-xl shadow-blue-500/25 group">
                Obtenir ma place
                <svg class="size-5 transition-transform duration-300 group-hover:translate-x-1"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z">
                    </path>
                    <path d="M13 5v2"></path>
                    <path d="M13 17v2"></path>
                    <path d="M13 11v2"></path>
                </svg>
            </button>
        </form>
        <p class="mt-4 text-center text-[10px] text-gray-400 font-medium px-8 uppercase tracking-widest">
            Validation présence requise sur place
        </p>
    </footer>

</body>

</html>
