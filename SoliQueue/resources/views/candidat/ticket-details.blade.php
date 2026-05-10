@extends('layouts.app', ['candidat_name' => $candidat->prenom . ' ' . $candidat->nom])

@section('styles')
<style>
    .transition-all-slow {
        transition: all 0.5s ease;
    }
    .scale-up {
        transform: scale(1.02);
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .animate-shake {
        animation: shake 0.2s ease-in-out 0s 2;
    }
</style>
@endsection

@section('content')
@php
    $startTime = \Carbon\Carbon::parse($session->dateEntretien . ' ' . $session->heureDebut);
    $confirmed = (bool) $candidat->is_present;

@endphp

<div class="max-w-[38rem] mx-auto px-4 py-6 sm:py-10 space-y-6 w-full" id="main-container">

    <!-- [STATE: INITIAL] Success Message -->
    <div id="initial-header" class="text-center transition-all duration-500 {{ $confirmed ? 'hidden' : '' }}">
        <h1 class="text-xl font-bold text-[#202124] tracking-tight">Félicitations pour votre réussite !</h1>
        <p class="text-slate-500 mt-1 text-sm font-medium">Votre profil est validé. Voici votre accès pour l'entretien.</p>
    </div>

    <!-- Main Interactive Card -->
    <div class="bg-white border border-slate-200 rounded-[1.8rem] shadow-2xl shadow-blue-900/10 overflow-hidden transition-all duration-700 relative animate-slide-up">
        <div class="p-6 sm:p-10 text-center">
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Votre Code Ticket</p>
            <h2 class="text-6xl font-black text-[#202124] tracking-tighter sm:text-7xl mb-8">
                SOLI-<span class="text-[#1A73E8]">{{ $ticket->numeroOrdre }}</span>
            </h2>

            <!-- [STATE: WAITING] Timer Section -->
            @if(!$confirmed)
            <div id="timer-section"
                class="bg-slate-50 rounded-2xl p-5 sm:p-6 border border-slate-100 mb-6 transition-all duration-500">
                <p class="text-slate-400 font-black text-[9px] uppercase tracking-[0.2em] mb-4">L'entretien commence dans :</p>
                <div class="flex justify-center items-center gap-4 sm:gap-8">
                    <div class="flex flex-col items-center">
                        <span id="days" class="text-3xl sm:text-4xl font-black text-[#1A73E8] tracking-tighter">00</span>
                        <span class="text-[8px] font-black uppercase tracking-widest text-slate-400 mt-1">Jours</span>
                    </div>
                    <div class="text-xl font-black text-slate-200 mb-4">:</div>
                    <div class="flex flex-col items-center">
                        <span id="hours" class="text-3xl sm:text-4xl font-black text-[#1A73E8] tracking-tighter">00</span>
                        <span class="text-[8px] font-black uppercase tracking-widest text-slate-400 mt-1">Heures</span>
                    </div>
                    <div class="text-xl font-black text-slate-200 mb-4">:</div>
                    <div class="flex flex-col items-center">
                        <span id="minutes" class="text-3xl sm:text-4xl font-black text-[#1A73E8] tracking-tighter">00</span>
                        <span class="text-[8px] font-black uppercase tracking-widest text-slate-400 mt-1">Min</span>
                    </div>
                    <div class="text-xl font-black text-slate-200 mb-4 hidden sm:block">:</div>
                    <div class="hidden sm:flex flex-col items-center">
                        <span id="seconds" class="text-3xl sm:text-4xl font-black text-[#1A73E8] tracking-tighter">00</span>
                        <span class="text-[8px] font-black uppercase tracking-widest text-slate-400 mt-1">Sec</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Presence Button / Badge Area -->
            <div id="presence-action-area" class="max-w-xs mx-auto space-y-3 transition-all duration-500">
                @if(!$confirmed)
                <button type="button" id="presence-btn" disabled
                    class="w-full py-4 px-6 inline-flex justify-center items-center gap-x-2 text-base font-black rounded-xl border border-transparent bg-slate-100 text-slate-400 cursor-not-allowed transition-all duration-500 shadow-lg shadow-slate-200/50">
                    <svg class="flex-shrink-0 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    Je suis présent(e)
                </button>
                <p id="presence-hint" class="text-[9px] font-black uppercase tracking-widest text-slate-400">
                    Activé automatiquement après le timer
                </p>
                @endif

                <!-- Confirmation Badge -->
                <div id="presence-confirmed-badge"
                    class="{{ $confirmed ? 'inline-flex' : 'hidden' }} animate-bounce items-center gap-x-2 py-3 px-6 rounded-xl bg-green-50 text-[#34A853] font-black text-xs shadow-sm border border-green-100 mx-auto">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    Présence confirmée à <span id="confirm-time">{{ ($confirmed && $candidat->updated_at) ? $candidat->updated_at->format('H:i') : '--:--' }}</span>
                </div>
            </div>
        </div>

        <div id="info-footer"
            class="p-4 border-t transition-all duration-500 text-center {{ $confirmed ? 'bg-green-50 border-green-100' : 'bg-blue-50/50 border-blue-100' }}">
            <p id="footer-text" class="text-xs font-bold {{ $confirmed ? 'text-[#34A853]' : 'text-blue-800' }}">
                {{ $confirmed ? "C'est votre tour bientôt. Restez vigilant !" : "Conservez cette page ouverte. Le tableau s'affichera ici le jour J." }}
            </p>
        </div>
    </div>

    <!-- [STATE: LIVE] Queue Table -->
    <div id="queue-section"
        class="{{ $confirmed ? '' : 'hidden opacity-0 translate-y-6' }} transition-all duration-700 bg-white border border-slate-200 rounded-[1.8rem] shadow-xl overflow-hidden mt-8 text-sm">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <h3 class="font-black text-slate-800 uppercase tracking-widest text-[11px]">File d'attente en temps réel</h3>
            <span class="flex size-1.5">
                <span class="animate-ping absolute inline-flex size-1.5 rounded-full bg-[#34A853] opacity-75"></span>
                <span class="relative inline-flex size-1.5 rounded-full bg-[#34A853]"></span>
            </span>
        </div>
        <div class="overflow-hidden">
            <table class="w-full divide-y divide-slate-100">
                <tbody id="queue-body" class="divide-y divide-slate-100 font-bold">
                    <!-- Les lignes seront injectées par JS -->
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Presence Modal -->
<div id="presence-modal"
    class="hidden fixed inset-0 z-[110] overflow-y-auto bg-slate-900/60 backdrop-blur-sm p-4 transition-opacity duration-300 opacity-0"
    role="dialog" aria-modal="true">
    <div class="min-h-full flex items-center justify-center py-4">
        <div id="modal-content"
            class="w-full max-w-lg bg-white border border-slate-200 shadow-2xl rounded-[2.5rem] overflow-hidden transform transition-all duration-300 scale-95 opacity-0">
            <div class="h-3 bg-gradient-to-r from-green-500 to-green-600"></div>
            <div class="p-8 sm:p-12 text-center">
                <div class="mb-8">
                    <span class="inline-flex justify-center items-center size-16 rounded-2xl bg-green-50 text-green-600 animate-pulse">
                        <svg class="size-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </span>
                </div>
                <h3 class="mb-2 text-2xl font-black text-slate-800 tracking-tight">Vérification</h3>
                <p class="text-slate-500 font-medium mb-8 leading-relaxed text-base">Veuillez saisir le code d'arrivée.</p>
                
                <div class="flex justify-center gap-x-3 mb-8">
                    <input type="text" id="presence-code-input" maxlength="4"
                        class="block w-48 py-4 px-3 text-center text-4xl font-black text-slate-800 bg-slate-50 border-4 border-slate-100 rounded-2xl focus:border-blue-600 focus:ring-blue-600 focus:outline-none transition-all tracking-[0.4em] placeholder-slate-200"
                        placeholder="••••">
                </div>

                <div class="flex flex-col gap-3">
                    <button type="button" id="confirm-presence-btn"
                        class="py-4 px-6 text-base font-black rounded-xl bg-green-600 text-white hover:bg-green-700 shadow-xl shadow-green-600/20 transform hover:-translate-y-0.5 transition-all text-sm">
                        Confirmer
                    </button>
                    <button type="button" id="close-modal-btn"
                        class="py-2 px-6 text-[10px] font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-colors">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let pollInterval;
        // --- DATA ---
        const targetTimestamp = {{ $startTime->timestamp }} * 1000;
        const isAlreadyPresent = {{ $confirmed ? 'true' : 'false' }};
        const candidatId = {{ $candidat->id }};

        // --- ELEMENTS ---
        const timerEls = {
            days: document.getElementById('days'),
            hours: document.getElementById('hours'),
            minutes: document.getElementById('minutes'),
            seconds: document.getElementById('seconds')
        };
        const timerSection = document.getElementById('timer-section');
        const initialHeader = document.getElementById('initial-header');
        const notifBanner = document.getElementById('notif-banner');
        const presenceBtn = document.getElementById('presence-btn');
        const presenceHint = document.getElementById('presence-hint');
        const presenceActionArea = document.getElementById('presence-action-area');
        const infoFooter = document.getElementById('info-footer');
        const queueSection = document.getElementById('queue-section');
        const queueBody = document.getElementById('queue-body');
        const modal = document.getElementById('presence-modal');
        const modalContent = document.getElementById('modal-content');
        const confirmBtn = document.getElementById('confirm-presence-btn');
        const closeBtn = document.getElementById('close-modal-btn');
        const presenceCodeInput = document.getElementById('presence-code-input');
        const confirmedBadge = document.getElementById('presence-confirmed-badge');
        const confirmTimeEl = document.getElementById('confirm-time');

        // --- TIMER LOGIC ---
        let timerInterval;
        if (!isAlreadyPresent) {
            const updateTimer = () => {
                const diff = targetTimestamp - Date.now();

                if (diff <= 0) {
                    if (timerInterval) clearInterval(timerInterval);
                    switchToLiveState();
                    return;
                }

                if(timerEls.days) {
                    const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const s = Math.floor((diff % (1000 * 60)) / 1000);

                    timerEls.days.textContent = String(d).padStart(2, '0');
                    timerEls.hours.textContent = String(h).padStart(2, '0');
                    timerEls.minutes.textContent = String(m).padStart(2, '0');
                    timerEls.seconds.textContent = String(s).padStart(2, '0');
                }
            };
            
            // Check immediately before setting interval to prevent 1-second lag
            updateTimer();
            if (targetTimestamp - Date.now() > 0) {
                timerInterval = setInterval(updateTimer, 1000);
            }
        } else {
            startPolling();
        }

        // --- STATE TRANSITIONS ---
        function switchToLiveState() {
            if (timerSection) {
                timerSection.classList.add('opacity-0', '-translate-y-8');
                setTimeout(() => timerSection.classList.add('hidden'), 500);
            }
            if (initialHeader) {
                initialHeader.classList.add('opacity-0', '-translate-y-8');
                setTimeout(() => initialHeader.classList.add('hidden'), 500);
            }

            if (notifBanner) {
                notifBanner.classList.remove('hidden');
            }

            if (presenceBtn) {
                presenceBtn.disabled = false;
                presenceBtn.classList.remove('bg-slate-100', 'text-slate-400', 'cursor-not-allowed', 'shadow-slate-200/50');
                presenceBtn.classList.add('bg-[#1A73E8]', 'text-white', 'hover:bg-blue-700', 'shadow-blue-600/30', 'animate-pulse');
            }
            if (presenceHint) {
                presenceHint.textContent = "C'est le moment ! Validez votre présence maintenant.";
                presenceHint.classList.replace('text-slate-400', 'text-[#1A73E8]');
            }

            if (infoFooter) {
                infoFooter.classList.replace('bg-blue-50/50', 'bg-green-50');
                infoFooter.classList.replace('border-blue-100', 'border-green-100');
            }
            const footerText = document.getElementById('footer-text');
            if (footerText) {
                footerText.textContent = "C'est votre tour bientôt. Restez vigilant !";
                footerText.classList.replace('text-blue-800', 'text-[#34A853]');
            }
        }


        async function fetchQueue() {
            try {
                const response = await fetch('{{ route('candidat.queue-status') }}');
                const result = await response.json();
                if (result.success) {
                    renderQueue(result.queue);
                    
                    // Si le candidat change de statut (ex: passe à "en cours"), 
                    // on peut aussi mettre à jour l'UI globale ici si besoin.
                }
            } catch (error) {
                console.error("Erreur polling queue:", error);
            }
        }

        function startPolling() {
            if (pollInterval) clearInterval(pollInterval);
            fetchQueue(); // Premier appel immédiat
            pollInterval = setInterval(fetchQueue, 3000); // Poll toutes les 3 secondes
        }

        function renderQueue(tickets) {
            queueBody.innerHTML = '';
            let enAttenteCount = 0;

            tickets.forEach((t, index) => {
                let rowClass = 'bg-white';
                let statusBadgeClass = '';
                let statusText = '';
                let isMe = t.candidat_id === candidatId;

                if (t.statut === 'en cours') {
                    rowClass = 'bg-[#10b981] text-white font-bold shadow-sm z-20'; // Vert principal, moderne
                    statusBadgeClass = 'bg-white/20 border border-white/30 text-white';
                    statusText = 'En cours';
                } else if (t.statut === 'en attente') {
                    enAttenteCount++;
                    if (enAttenteCount <= 3) {
                        rowClass = 'bg-emerald-50/80 text-emerald-800 border-b border-emerald-100'; // Vert trÃ¨s clair et calme
                        statusBadgeClass = 'bg-emerald-100 text-emerald-700 border border-emerald-200';
                        statusText = 'Suivant';
                    } else {
                        rowClass = 'bg-white text-slate-400';
                        statusBadgeClass = 'bg-slate-50 text-slate-400 border border-slate-100';
                        statusText = 'En attente';
                    }
                } else if (t.statut === 'terminé' || t.statut === 'terminée') {
                    rowClass = 'bg-[#064e3b] text-white font-bold'; // Vert foncÃ© pour terminÃ©
                    statusBadgeClass = 'bg-white/10 border border-white/30 text-white';
                    statusText = 'TerminÃ©';
                }

                if (isMe) {
                    rowClass += ' border-l-[6px] border-blue-500 ring-1 ring-blue-100';
                }

                const initials = (t.candidat.prenom[0] + t.candidat.nom[0]).toUpperCase();

                const row = `
                    <tr class="${rowClass} transition-all duration-300">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-black w-24 text-[11px]">Pos ${index + 1}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-x-4">
                                <div class="size-9 rounded-full ${isMe ? 'bg-blue-100 text-[#1A73E8]' : 'bg-white/20'} flex items-center justify-center font-bold text-xs shadow-inner">
                                    ${initials}
                                </div>
                                <div>
                                    <p class="font-bold text-xs">${t.candidat.prenom} ${t.candidat.nom}</p>
                                    ${isMe ? '<p class="text-[9px] text-[#1A73E8] font-black uppercase">C\'est vous</p>' : ''}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-end">
                            ${statusText ? `<span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest ${statusBadgeClass}">${statusText}</span>` : ''}
                        </td>
                    </tr>
                `;
                queueBody.innerHTML += row;
            });

            // Show queue section
            queueSection.classList.remove('hidden');
            setTimeout(() => queueSection.classList.remove('opacity-0', 'translate-y-6'), 50);
        }

        // --- MODAL LOGIC ---
        function openModal() {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            setTimeout(() => {
                modal.classList.add('opacity-100');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
            presenceCodeInput.focus();
        }

        function closeModal() {
            modal.classList.remove('opacity-100');
            modalContent.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }, 300);
        }

        if (presenceBtn) {
            presenceBtn.addEventListener('click', () => {
                if (!presenceBtn.disabled) openModal();
            });
        }
        
        if (closeBtn) closeBtn.addEventListener('click', closeModal);

        confirmBtn.addEventListener('click', async () => {
            const code = presenceCodeInput.value;
            if (!code) return;

            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<span class="inline-block animate-spin border-2 border-white border-t-transparent rounded-full size-4 mr-2"></span>';

            try {
                const response = await fetch('{{ route('candidat.mark-presence') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ code: code })
                });

                const result = await response.json();

                if (result.success) {
                    closeModal();
                    switchToConfirmedState(result.time, result.queue);
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                presenceCodeInput.classList.add('border-red-500', 'text-red-600', 'animate-shake');
                confirmBtn.innerHTML = 'Erreur';
                confirmBtn.classList.replace('bg-green-600', 'bg-red-600');
                
                setTimeout(() => {
                    presenceCodeInput.classList.remove('border-red-500', 'text-red-600', 'animate-shake');
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = 'Confirmer';
                    confirmBtn.classList.replace('bg-red-600', 'bg-[#34A853]');
                }, 1000);
            }
        });

        function switchToConfirmedState(time, queueData) {
            confirmTimeEl.textContent = time;

            // UI Changes
            if (presenceBtn) {
                presenceBtn.classList.add('hidden');
                presenceBtn.style.display = 'none';
            }
            if (presenceHint) {
                presenceHint.classList.add('hidden');
                presenceHint.style.display = 'none';
            }
            confirmedBadge.classList.remove('hidden');
            confirmedBadge.classList.add('inline-flex');

            // Header & Footer
            const globalBanner = document.getElementById('notif-banner');
            const notifText = document.getElementById('notif-text');
            if (globalBanner) {
                globalBanner.classList.remove('hidden');
                if (notifText) {
                    notifText.textContent = "C'est le jour de votre entretien ! Bonne chance pour votre passage.";
                }
            }

            const footerText = document.getElementById('footer-text');
            footerText.textContent = "C'est votre tour bientôt. Restez vigilant !";
            infoFooter.classList.replace('bg-blue-50/50', 'bg-green-50');
            infoFooter.classList.replace('border-blue-100', 'border-green-100');
            footerText.classList.replace('text-blue-800', 'text-[#34A853]');

            // Render Queue
            renderQueue(queueData);
            startPolling();
        }

        // Si déjà présent au chargement
        if (isAlreadyPresent) {
            const initialQueue = @json($queue);
            if (initialQueue && initialQueue.length > 0) {
                renderQueue(initialQueue);
            }
            const globalBanner = document.getElementById('notif-banner');
            if (globalBanner) {
                globalBanner.classList.remove('hidden');
            }
        }
    });
</script>
@endsection
