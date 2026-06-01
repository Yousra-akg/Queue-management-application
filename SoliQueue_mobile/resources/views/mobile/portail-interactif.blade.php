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
        <a href="#">
            <img src="{{ asset('img/logo.png') }}" alt="SoliQueue Logo" class="h-8 w-auto">
        </a>
        <div class="flex items-center gap-x-3">
            <!-- Notification Bell -->
            <div class="relative">
                <button type="button" id="notif-btn" class="relative p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span id="notif-badge" class="absolute top-1 right-1 flex h-2.5 w-2.5 hidden">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                    </span>
                </button>
                
                <!-- Notification Dropdown -->
                <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-72 bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden z-[150]">
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                        <span class="font-black text-gray-800 uppercase tracking-widest text-[9px]">Notifications</span>
                        <span id="notif-count" class="bg-red-50 text-red-600 text-[8px] font-black px-2 py-0.5 rounded-full">0 non lue(s)</span>
                    </div>
                    <div id="notif-list" class="max-h-60 overflow-y-auto divide-y divide-gray-50">
                        <div class="p-6 text-center text-gray-400 text-xs font-medium">
                            Aucune notification récente.
                        </div>
                    </div>
                </div>
            </div>

            <span class="text-[10px] font-black text-gray-400 uppercase truncate max-w-[100px]">{{ $studentName }}</span>
            <div
                class="size-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold border border-blue-100 text-xs italic shrink-0">
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

            @php
                $confirmTime = isset($ticket['updated_at']) ? \Carbon\Carbon::parse($ticket['updated_at'])->timezone(config('app.timezone', 'UTC'))->format('h:i A') : '--:--';
            @endphp

            <!-- Main Interactive Card -->
            <div id="presence-card"
                class="bg-white border border-gray-100 rounded-[2.5rem] shadow-xl shadow-blue-500/5 p-8 text-center transition-all duration-500 relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-1.5 bg-blue-600"></div>

                <p class="text-[10px] font-black uppercase tracking-[0.4em] text-gray-400 mb-6">Votre Numéro</p>
                <h2 class="text-7xl font-black text-blue-900 tracking-tighter mb-10 tabular-nums">#{{ str_pad($ticket['numeroOrdre'] ?? 0, 2, '0', STR_PAD_LEFT) }}</h2>

                <!-- Timer Section -->
                <div id="timer-section"
                    class="bg-blue-50/50 rounded-3xl p-6 border border-blue-50 mb-8 transition-all duration-500 {{ $ticket['statut'] !== 'en attente' ? 'hidden' : '' }}">
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
                        class="w-full py-4 px-6 inline-flex justify-center items-center gap-x-3 text-lg font-black rounded-2xl bg-gray-100 text-gray-400 cursor-not-allowed transition-all duration-500 tap-scale {{ $ticket['statut'] !== 'en attente' ? 'hidden' : '' }}">
                        <svg class="size-6 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                        Je suis présent(e)
                    </button>

                    <!-- Confirmation Badge -->
                    <div id="presence-confirmed-badge"
                        class="{{ $ticket['statut'] !== 'en attente' ? '' : 'hidden' }} animate-bounce inline-flex items-center gap-x-2 py-3 px-6 rounded-2xl bg-green-50 text-green-700 font-extrabold text-xs shadow-sm border border-green-100 mx-auto uppercase">
                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Confirmé à <span id="confirm-time">{{ $confirmTime }}</span>
                    </div>

                    <p id="presence-hint" class="text-[10px] text-gray-400 font-black uppercase tracking-widest px-4 {{ $ticket['statut'] !== 'en attente' ? 'hidden' : '' }}">
                        Activé après le compte à rebours
                    </p>
                </div>
            </div>

            <!-- Queue Section (Live) -->
            <div id="queue-section"
                class="{{ $ticket['statut'] !== 'en attente' ? '' : 'hidden opacity-0 transform translate-y-8' }} transition-all duration-700 space-y-4">
                <div class="flex items-center justify-between px-2">
                    <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest">En direct</h3>
                    <span class="text-[10px] font-bold text-blue-600 italic">Session {{ $sessionInfo['nom'] ?? 'Active' }}</span>
                </div>

                <div class="bg-[#0c4a34] rounded-[2.5rem] shadow-sm overflow-hidden border border-gray-100">
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
                        
                        // Cacher l'en-tête initial et la carte de présence
                        document.getElementById('initial-header').classList.add('hidden');
                        if (document.getElementById('presence-card')) {
                            document.getElementById('presence-card').classList.add('hidden');
                        }

                        document.getElementById('queue-section').classList.remove('hidden');
                        
                        loadLiveQueue();
                        if (!window.queueInterval) {
                            window.queueInterval = setInterval(loadLiveQueue, 5000);
                        }
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

            // Notification dropdown logic
            const notifBtn = document.getElementById('notif-btn');
            const notifDropdown = document.getElementById('notif-dropdown');
            const notifBadge = document.getElementById('notif-badge');
            const notifCount = document.getElementById('notif-count');
            const notifList = document.getElementById('notif-list');

            if (notifBtn) {
                notifBtn.onclick = (e) => {
                    e.stopPropagation();
                    notifDropdown.classList.toggle('hidden');
                };
            }

            document.addEventListener('click', () => {
                if (notifDropdown) notifDropdown.classList.add('hidden');
            });

            if (notifDropdown) {
                notifDropdown.onclick = (e) => e.stopPropagation();
            }

            // Priority alert elements
            const priorityAlertModal = document.getElementById('priority-alert-modal');
            const priorityAlertTitle = document.getElementById('priority-alert-title');
            const priorityAlertMessage = document.getElementById('priority-alert-message');
            const priorityAlertDismiss = document.getElementById('priority-alert-dismiss');
            let currentPriorityAlertId = null;

            if (priorityAlertDismiss) {
                priorityAlertDismiss.onclick = async () => {
                    if (currentPriorityAlertId) {
                        await markNotificationAsRead(currentPriorityAlertId);
                    }
                    priorityAlertModal.classList.add('hidden');
                };
            }

            async function markNotificationAsRead(notifId) {
                try {
                    const response = await fetch(`/notifications/${notifId}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    const resData = await response.json();
                    if (resData.success) {
                        loadLiveQueue();
                    }
                } catch (err) {
                    console.error("Erreur marquage notification:", err);
                }
            }
            window.markNotificationAsRead = markNotificationAsRead;
            
            async function loadLiveQueue() {
                try {
                    const res = await fetch("/live-queue?session_id=" + {{ $ticket['session_id'] }} + "&candidate_id=" + {{ $ticket['candidat_id'] }});
                    const data = await res.json();
                    
                    if (data.success && data.data) {
                        let html = '';
                        const myTicketId = {{ $ticket['id'] }};
                        
                        // Trouver tous les index des tickets "en attente" pour marquer les 3 premiers comme "Suivant"
                        const pendingTickets = [];
                        data.data.forEach((item, index) => {
                            if (item.statut === 'en attente') {
                                pendingTickets.push(index);
                            }
                        });
                        const nextThreeIndices = pendingTickets.slice(0, 3);
                        
                        data.data.forEach((item, index) => {
                            const isMe = item.id === myTicketId;
                            const isCurrent = item.statut === 'en cours';
                            const isNext = nextThreeIndices.includes(index);
                            
                            let bgColor = 'bg-white';
                            let posColor = 'text-gray-400';
                            let circleColor = 'bg-gray-100 text-gray-500';
                            let nameTextColor = 'text-gray-700 font-bold';
                            let vousColor = 'text-blue-500 font-black';
                            let badgeClass = 'bg-gray-50 text-gray-400 border-gray-100';
                            let statusText = 'EN ATTENTE';
                            
                            if (item.statut === 'terminée' || item.statut === 'terminé') {
                                bgColor = 'bg-[#0c4a34] text-white border-b border-white/5';
                                posColor = 'text-green-200';
                                circleColor = 'bg-white/10 text-white';
                                nameTextColor = 'text-white font-bold';
                                badgeClass = 'border-white/20 text-white bg-white/5';
                                statusText = 'TERMINÉ';
                            } else if (isCurrent) {
                                bgColor = 'bg-[#10b981] text-white border-b border-white/5';
                                posColor = 'text-green-100';
                                circleColor = 'bg-white text-[#10b981]';
                                nameTextColor = 'text-white font-extrabold';
                                vousColor = 'text-blue-200 font-extrabold';
                                badgeClass = 'border-white/30 text-white bg-white/20';
                                statusText = 'EN COURS';
                            } else if (isNext) {
                                bgColor = 'bg-[#e6fcf5] border-b border-[#c3fae8]';
                                posColor = 'text-[#0c4a34]';
                                circleColor = 'bg-white text-[#0c4a34] border border-[#c3fae8]';
                                nameTextColor = 'text-[#0c4a34] font-bold';
                                vousColor = 'text-blue-600 font-black';
                                badgeClass = 'bg-[#c3fae8] text-[#0c4a34] border-[#c3fae8]';
                                statusText = 'SUIVANT';
                            } else {
                                bgColor = 'bg-white border-b border-gray-50';
                                posColor = 'text-gray-400';
                                circleColor = 'bg-gray-100 text-gray-400';
                                nameTextColor = 'text-gray-900';
                                vousColor = 'text-blue-500 font-black';
                                badgeClass = 'bg-gray-50 text-gray-400 border-gray-100';
                                statusText = 'EN ATTENTE';
                            }

                            // Bordure d'accentuation si c'est Moi
                            let borderAccent = '';
                            if (isMe) {
                                borderAccent = 'border-l-[6px] border-blue-500';
                            }

                            // Calcul des initiales
                            let initials = '??';
                            if (item.candidat && item.candidat.prenom && item.candidat.nom) {
                                initials = (item.candidat.prenom[0] + item.candidat.nom[0]).toUpperCase();
                            } else if (isMe) {
                                initials = 'ME';
                            }
                            
                            const fullName = item.candidat ? (item.candidat.prenom + ' ' + item.candidat.nom) : 'Candidat';
                            
                            html += `
                            <div class="px-6 py-4 flex items-center justify-between transition-all ${bgColor} ${borderAccent}">
                                <div class="flex items-center gap-x-4">
                                    <span class="text-xs font-black w-14 ${posColor}">Pos ${index + 1}</span>
                                    <div class="size-9 rounded-full ${circleColor} flex items-center justify-center font-bold text-xs shadow-inner shrink-0">
                                        ${initials}
                                    </div>
                                    <div>
                                        <p class="font-bold text-xs ${nameTextColor}">${fullName}</p>
                                        ${isMe ? `<p class="text-[9px] ${vousColor} font-black uppercase">C'est vous</p>` : ''}
                                    </div>
                                </div>
                                <div class="flex items-center gap-x-2">
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border ${badgeClass}">
                                        ${statusText}
                                    </span>
                                    ${isCurrent ? '<span class="relative flex size-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span><span class="relative inline-flex rounded-full size-2 bg-white"></span></span>' : ''}
                                </div>
                            </div>`;
                        });
                        
                        document.getElementById('queue-list').innerHTML = html;

                        // Gestion des notifications
                        const unreadNotifs = data.notifications || [];
                        if (unreadNotifs.length > 0) {
                            notifBadge.classList.remove('hidden');
                            notifCount.textContent = `${unreadNotifs.length} non lue(s)`;
                            
                            let notifHtml = '';
                            unreadNotifs.forEach(n => {
                                // Déclenchement de l'alerte prioritaire si urgent (insensible à la casse/accents)
                                const titreClean = n.titre.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                                const msgClean = n.message.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                                const isUrgent = titreClean.includes("tour") || 
                                                 titreClean.includes("termine") || 
                                                 titreClean.includes("cours") || 
                                                 titreClean.includes("ordre") || 
                                                 titreClean.includes("passage") || 
                                                 titreClean.includes("position") ||
                                                 msgClean.includes("tour") ||
                                                 msgClean.includes("termine");

                                if (isUrgent) {
                                    if (currentPriorityAlertId !== n.id && priorityAlertModal.classList.contains('hidden')) {
                                        currentPriorityAlertId = n.id;
                                        priorityAlertTitle.textContent = n.titre;
                                        priorityAlertMessage.textContent = n.message;
                                        priorityAlertModal.classList.remove('hidden');
                                        setTimeout(() => {
                                            priorityAlertModal.firstElementChild.classList.remove('scale-95', 'opacity-0');
                                            priorityAlertModal.firstElementChild.classList.add('scale-100', 'opacity-100');
                                        }, 10);
                                    }
                                }

                                notifHtml += `
                                    <div class="p-4 hover:bg-gray-50/50 transition-colors flex justify-between items-start gap-x-3">
                                        <div class="space-y-1">
                                            <p class="text-xs font-black text-gray-800">${n.titre}</p>
                                            <p class="text-[10px] text-gray-500 font-medium leading-relaxed">${n.message}</p>
                                        </div>
                                        <button onclick="markNotificationAsRead(${n.id})" class="text-[9px] font-black text-blue-600 hover:text-blue-700 uppercase tracking-wider shrink-0 mt-0.5">
                                            OK
                                        </button>
                                    </div>`;
                            });
                            notifList.innerHTML = notifHtml;
                        } else {
                            notifBadge.classList.add('hidden');
                            notifCount.textContent = '0 non lue(s)';
                            notifList.innerHTML = `
                                <div class="p-6 text-center text-gray-400 text-xs font-medium">
                                    Aucune notification récente.
                                </div>`;
                        }
                    }
                } catch (e) {
                    console.error("Erreur chargement file", e);
                }
            }

            loadLiveQueue();
            window.queueInterval = setInterval(loadLiveQueue, 5000);
        });
    </script>

    <!-- Priority Alert Modal -->
    <div id="priority-alert-modal" class="hidden fixed inset-0 z-[120] bg-black/60 backdrop-blur-sm flex items-center justify-center p-6">
        <div class="w-full max-w-sm bg-white rounded-3xl p-6 shadow-2xl text-center transform scale-95 opacity-0 transition-all duration-300">
            <div class="size-14 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="size-8 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </div>
            <h4 id="priority-alert-title" class="text-lg font-black text-gray-900 mb-2"></h4>
            <p id="priority-alert-message" class="text-sm font-medium text-gray-500 mb-6"></p>
            <button type="button" id="priority-alert-dismiss" class="w-full py-4 px-6 text-sm font-black rounded-2xl bg-blue-600 text-white shadow-lg tap-scale">
                J'AI COMPRIS
            </button>
        </div>
    </div>
</body>

</html>
