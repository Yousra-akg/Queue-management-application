<!DOCTYPE html>
<html lang="fr" class="bg-[#f8fafc] font-sans">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>SoliCode Queue - Portail Candidat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .tap-scale:active { transform: scale(0.95); transition: transform 0.1s; }
        @keyframes slide-up {
            from { transform: translateY(100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .animate-slide-up { animation: slide-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
</head>

<body class="bg-[#f8fafc] min-h-screen flex flex-col font-sans relative select-none max-w-[430px] mx-auto shadow-2xl border-x border-gray-100">

    <!-- Notification Banner -->
    <div id="notif-banner"
        class="hidden absolute top-0 inset-x-0 z-[100] bg-blue-600 py-4 px-6 text-center shadow-lg transition-transform duration-500 -translate-y-full">
        <p class="text-white text-xs font-black flex items-center justify-center gap-x-2 uppercase tracking-tight">
            <span class="relative flex size-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                <span class="relative inline-flex rounded-full size-2 bg-white"></span>
            </span>
            Présence requise au centre
        </p>
    </div>

    <!-- Header -->
    <header
        class="sticky top-0 w-full bg-white border-b border-gray-100 py-4 px-6 z-40 flex items-center justify-between">
        <a class="text-xl font-black text-blue-600 tracking-tighter" href="#">SoliCode</a>
        <div class="flex items-center gap-x-3">
            <span class="text-[10px] font-black text-gray-400 uppercase truncate max-w-[100px]">{{ $studentName }}</span>
            <div
                class="size-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold border border-blue-100 text-xs italic">
                {{ strtoupper(substr($studentName, 0, 2)) }}</div>
        </div>
    </header>

    <main class="flex-grow pt-8 pb-32 px-6">
        <div class="max-w-md mx-auto space-y-6">

            <!-- Success Header -->
            <div id="initial-header" class="text-center transition-all duration-500 mt-4">
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Félicitations !</h1>
                <p class="text-sm text-gray-500 mt-2 font-medium">Votre accès pour l'entretien est prêt.</p>
            </div>

            <!-- Main Interactive Card -->
            <div
                class="bg-white border border-gray-100 rounded-[2.5rem] shadow-xl shadow-blue-500/5 p-8 text-center transition-all duration-500 relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-1.5 bg-blue-600"></div>

                <p class="text-[10px] font-black uppercase tracking-[0.4em] text-gray-400 mb-6">Votre Numéro</p>
                <h2 class="text-7xl font-black text-blue-900 tracking-tighter mb-10 tabular-nums">#{{ str_pad($ticket['numeroOrdre'] ?? 0, 2, '0', STR_PAD_LEFT) }}</h2>

                <!-- Timer Section -->
                <div id="timer-section"
                    class="bg-blue-50/50 rounded-3xl p-6 border border-blue-50 mb-8 transition-all duration-500">
                    <p class="text-[9px] font-black text-blue-500 uppercase tracking-widest mb-4">Début dans :</p>
                    <div class="flex justify-center items-center gap-5">
                        <div class="flex flex-col items-center">
                            <span id="days" class="text-3xl font-black text-blue-600">00</span>
                            <span class="text-[8px] font-black text-blue-400 uppercase mt-1">Jours</span>
                        </div>
                        <span class="text-xl font-bold text-blue-200 mb-4">:</span>
                        <div class="flex flex-col items-center">
                            <span id="hours" class="text-3xl font-black text-blue-600">00</span>
                            <span class="text-[8px] font-black text-blue-400 uppercase mt-1">H</span>
                        </div>
                        <span class="text-xl font-bold text-blue-200 mb-4">:</span>
                        <div class="flex flex-col items-center">
                            <span id="minutes" class="text-3xl font-black text-blue-600">00</span>
                            <span class="text-[8px] font-black text-blue-400 uppercase mt-1">Min</span>
                        </div>
                        <span class="text-xl font-bold text-blue-200 mb-4">:</span>
                        <div class="flex flex-col items-center">
                            <span id="seconds" class="text-3xl font-black text-blue-600">00</span>
                            <span class="text-[8px] font-black text-blue-400 uppercase mt-1">Sec</span>
                        </div>
                    </div>
                </div>

                <!-- Presence Action -->
                <div id="presence-action-area" class="space-y-4">
                    <button type="button" id="presence-btn" disabled
                        class="w-full py-4 px-6 inline-flex justify-center items-center gap-x-3 text-lg font-black rounded-2xl bg-gray-100 text-gray-400 cursor-not-allowed transition-all duration-500 tap-scale">
                        <svg class="size-6 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                        Je suis présent(e)
                    </button>

                    <!-- Confirmation Badge -->
                    <div id="presence-confirmed-badge"
                        class="hidden animate-bounce inline-flex items-center gap-x-2 py-3 px-6 rounded-2xl bg-green-50 text-green-700 font-extrabold text-xs shadow-sm border border-green-100 mx-auto uppercase">
                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Confirmé à <span id="confirm-time">--:--</span>
                    </div>

                    <p id="presence-hint" class="text-[10px] text-gray-400 font-black uppercase tracking-widest px-4">
                        Activé après le compte à rebours
                    </p>
                </div>
            </div>

            <!-- Queue Section (Live) -->
            <div id="queue-section"
                class="hidden opacity-0 transform translate-y-8 transition-all duration-700 space-y-4">
                <div class="flex items-center justify-between px-2">
                    <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest">En direct</h3>
                    <span class="text-[10px] font-bold text-blue-600 italic">Session {{ $sessionInfo['nom'] ?? 'Active' }}</span>
                </div>

                <div class="bg-white border border-gray-100 rounded-[2.5rem] shadow-sm overflow-hidden">
                    <div id="queue-list" class="divide-y divide-gray-50">
                        <!-- AJAX content will populate here -->
                        <div class="p-6 text-center text-sm font-medium text-gray-400">
                            <i>Chargement de la file d'attente...</i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer Simulation Info -->
    <footer
        class="fixed bottom-0 w-full max-w-[430px] p-6 bg-white/90 backdrop-blur-md border-t border-gray-100 z-50 text-center">
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">SoliCode Queue System</p>
        <div class="flex justify-center gap-3">
            <div class="size-2 bg-blue-600 rounded-full"></div>
            <div class="size-2 bg-blue-600 rounded-full"></div>
            <div class="w-8 h-2 bg-blue-600 rounded-full"></div>
        </div>
    </footer>

    <!-- Verification Modal -->
    <div id="hs-presence-modal"
        class="hidden absolute inset-0 z-[110] bg-[#0f172a]/80 backdrop-blur-md p-4 transition-opacity duration-300 opacity-0"
        role="dialog" aria-modal="true">
        <div class="h-full flex flex-col justify-end">
            <div id="modal-content"
                class="w-full bg-white rounded-[3rem] p-8 shadow-2xl transform transition-all duration-300 translate-y-full">
                <div class="w-12 h-1.5 bg-gray-100 rounded-full mx-auto mb-8"></div>

                <div class="text-center mb-10">
                    <div
                        class="size-16 rounded-3xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-6">
                        <svg class="size-10" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-2">Code de présence</h3>
                    <p class="text-sm font-medium text-gray-500 px-6">Saisissez le code à 4 chiffres affiché à l'entrée.
                    </p>
                    <p id="validation-error" class="text-xs font-bold text-red-500 mt-2 hidden"></p>
                </div>

                <div class="flex justify-center mb-12">
                    <input type="text" id="presence-code" maxlength="4"
                        class="block w-full max-w-[240px] py-6 text-center text-5xl font-black text-gray-900 bg-gray-50 border-4 border-gray-100 rounded-3xl focus:border-blue-600 focus:ring-0 focus:outline-none transition-all tracking-[0.3em] uppercase"
                        placeholder="0000" inputmode="text" autocapitalize="characters">
                </div>

                <div class="space-y-3">
                    <button type="button" id="confirm-presence-btn"
                        class="w-full py-5 px-6 text-lg font-black rounded-[1.5rem] bg-blue-600 text-white shadow-xl shadow-blue-500/25 tap-scale">
                        Confirmer
                    </button>
                    <button type="button" id="close-modal"
                        class="w-full py-3 px-6 text-sm font-black text-gray-400 uppercase tracking-widest">Annuler</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();

            @php
                $startTime = \Carbon\Carbon::parse(($sessionInfo['dateEntretien'] ?? now()->toDateString()) . ' ' . ($sessionInfo['heureDebut'] ?? '00:00:00'));
                $startTimeMs = $startTime->timestamp * 1000;
            @endphp
            let targetDate = new Date({{ $startTimeMs }});
            
            // Si la date est déjà passée, afficher le bouton "présent" directement
            if (targetDate <= new Date()) {
                switchToLiveState();
            }

            const timerEls = {
                days: document.getElementById('days'),
                hours: document.getElementById('hours'),
                minutes: document.getElementById('minutes'),
                seconds: document.getElementById('seconds')
            };

            const updateTimer = () => {
                const diff = targetDate - new Date();
                if (diff <= 0) {
                    clearInterval(timerInterval);
                    switchToLiveState();
                    return;
                }
                timerEls.days.textContent = String(Math.floor(diff / 86400000)).padStart(2, '0');
                timerEls.hours.textContent = String(Math.floor((diff % 86400000) / 3600000)).padStart(2, '0');
                timerEls.minutes.textContent = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
                timerEls.seconds.textContent = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
            };

            let timerInterval;
            if (targetDate > new Date()) {
                timerInterval = setInterval(updateTimer, 1000);
                updateTimer();
            }

            function switchToLiveState() {
                // Si on est déjà en mode live, on ne fait rien
                if(document.getElementById('timer-section').classList.contains('hidden')) return;

                // Animation de sortie du chrono
                document.getElementById('timer-section').classList.add('opacity-0', '-translate-y-4');
                document.getElementById('initial-header').classList.add('opacity-0', '-translate-y-4');
                
                setTimeout(() => {
                    document.getElementById('timer-section').classList.add('hidden');
                    document.getElementById('initial-header').classList.add('hidden');

                    // Affichage de la bannière
                    const banner = document.getElementById('notif-banner');
                    banner.classList.remove('hidden');
                    setTimeout(() => banner.classList.remove('-translate-y-full'), 10);

                    // Activation du bouton
                    const btn = document.getElementById('presence-btn');
                    btn.disabled = false;
                    btn.classList.replace('bg-gray-100', 'bg-blue-600');
                    btn.classList.replace('text-gray-400', 'text-white');
                    btn.classList.add('shadow-xl', 'shadow-blue-500/20', 'animate-pulse');

                    document.getElementById('presence-hint').classList.replace('text-gray-400', 'text-blue-600');
                    document.getElementById('presence-hint').textContent = "C'est le moment d'entrer !";
                }, 500);
            }

            // Modal Logic
            const modal = document.getElementById('hs-presence-modal');
            const content = document.getElementById('modal-content');
            const confirmBtn = document.getElementById('confirm-presence-btn');

            const openM = () => {
                modal.classList.remove('hidden');
                setTimeout(() => { modal.classList.add('opacity-100'); content.classList.replace('translate-y-full', 'translate-y-0'); }, 10);
                document.getElementById('presence-code').focus();
            };

            const closeM = () => {
                modal.classList.remove('opacity-100');
                content.classList.replace('translate-y-0', 'translate-y-full');
                setTimeout(() => modal.classList.add('hidden'), 300);
            };

            document.getElementById('presence-btn').onclick = openM;
            document.getElementById('close-modal').onclick = closeM;

            confirmBtn.onclick = async () => {
                const code = document.getElementById('presence-code').value;
                confirmBtn.innerText = 'Vérification...';
                
                try {
                    const response = await fetch("/validate-presence", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ code_presence: code, ticket_id: {{ $ticket['id'] }} })
                    });

                    const data = await response.json();
                    if (data.success) {
                        closeM();
                        document.getElementById('presence-btn').classList.add('hidden');
                        document.getElementById('presence-hint').classList.add('hidden');
                        document.getElementById('presence-confirmed-badge').classList.remove('hidden');
                        document.getElementById('confirm-time').textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                        document.getElementById('queue-section').classList.remove('hidden');
                        setTimeout(() => document.getElementById('queue-section').classList.remove('opacity-0', 'translate-y-8'), 100);
                        
                        loadLiveQueue();
                        setInterval(loadLiveQueue, 5000); // refresh every 5s
                    } else {
                        throw new Error(data.message || 'Code invalide');
                    }
                } catch (e) {
                    const errorEl = document.getElementById('validation-error');
                    errorEl.textContent = e.message;
                    errorEl.classList.remove('hidden');
                    confirmBtn.innerText = 'Confirmer';
                }
            };
            
            async function loadLiveQueue() {
                try {
                    const res = await fetch("/live-queue?session_id=" + {{ $ticket['session_id'] }});
                    const data = await res.json();
                    
                    if (data.success && data.data) {
                        let html = '';
                        const myTicketId = {{ $ticket['id'] }};
                        
                        // Trouver le premier "en attente" qui sera le "Suivant"
                        const nextIndex = data.data.findIndex(item => item.statut === 'en attente');
                        
                        data.data.forEach((item, index) => {
                            const isMe = item.id === myTicketId;
                            const isCurrent = item.statut === 'en cours';
                            const isNext = (index === nextIndex);
                            
                            if (isMe) {
                                // Style spécial pour la carte "Moi"
                                let statusColor = 'text-blue-500';
                                if (isCurrent) statusColor = 'text-green-600';
                                if (isNext) statusColor = 'text-green-500';

                                let cardBorder = isCurrent ? 'border-green-600' : (isNext ? 'border-green-400' : 'border-blue-600');
                                let badgeColor = isCurrent ? 'bg-green-600' : (isNext ? 'bg-green-400' : 'bg-blue-600');

                                html += `
                                <div class="p-5 bg-white border-y-2 ${cardBorder} relative z-10 shadow-lg transition-all">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-x-4">
                                            <div class="size-11 rounded-2xl ${badgeColor} text-white flex items-center justify-center font-black text-lg italic tracking-tighter shadow-md">
                                                ${item.numeroOrdre}
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-x-2">
                                                    <p class="text-base font-black text-gray-900 leading-none">${item.candidat ? item.candidat.nom + ' ' + item.candidat.prenom : 'Moi'}</p>
                                                    <span class="py-1 px-2 bg-blue-100 text-blue-600 rounded-lg text-[8px] font-black uppercase">Moi</span>
                                                </div>
                                                <p class="text-[10px] font-bold ${statusColor} mt-1 uppercase tracking-tight">
                                                    ${isCurrent ? 'C\'est votre tour !' : (isNext ? 'Vous êtes le prochain !' : 'Patientez...')}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                            } else {
                                const opacity = index > 5 ? 'opacity-40' : 'opacity-100';
                                
                                // Couleurs selon le statut
                                let bgColor = 'bg-white';
                                let circleColor = 'bg-gray-50 text-gray-400';
                                let statusText = item.statut;
                                let statusTextColor = 'text-gray-400';
                                let nameTextColor = 'text-gray-900';

                                if (item.statut === 'terminée') {
                                    bgColor = 'bg-green-800 text-white'; // Vert foncé
                                    circleColor = 'bg-green-950 text-green-200';
                                    statusText = "Terminée";
                                    statusTextColor = 'text-green-300';
                                    nameTextColor = 'text-white';
                                } else if (isCurrent) {
                                    bgColor = 'bg-green-500 text-white'; // Vert un peu clair
                                    circleColor = 'bg-green-700 text-white';
                                    statusText = "En cours";
                                    statusTextColor = 'text-green-100';
                                    nameTextColor = 'text-white';
                                } else if (isNext) {
                                    bgColor = 'bg-green-100'; // Vert plus clair
                                    circleColor = 'bg-green-300 text-green-900';
                                    statusText = "Suivant";
                                    statusTextColor = 'text-green-600';
                                    nameTextColor = 'text-green-900';
                                }
                                
                                html += `
                                <div class="p-4 ${bgColor} flex items-center justify-between ${opacity} transition-all border-b border-gray-50">
                                    <div class="flex items-center gap-x-3">
                                        <div class="size-9 rounded-2xl ${circleColor} flex items-center justify-center font-black text-[10px] italic">
                                            ${item.numeroOrdre}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold ${nameTextColor}">${item.candidat ? item.candidat.nom + ' ' + item.candidat.prenom : 'Candidat'}</p>
                                            <p class="text-[9px] font-black uppercase tracking-tighter ${statusTextColor}">${statusText}</p>
                                        </div>
                                    </div>
                                    ${isCurrent ? '<span class="relative flex size-2 mr-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span><span class="relative inline-flex rounded-full size-2 bg-white"></span></span>' : ''}
                                </div>`;
                            }
                        });
                        
                        document.getElementById('queue-list').innerHTML = html;
                    }
                } catch (e) {
                    console.error("Erreur chargement file", e);
                }
            }
        });
    </script>
</body>

</html>
