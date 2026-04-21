@extends('layouts.app', ['candidat_name' => $candidat->prenom . ' ' . $candidat->nom])

@section('content')
<div class="max-w-3xl w-full py-10">
    <div class="space-y-8">
        
        <!-- Welcome Header -->
        <div id="initial-header" class="text-center">
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Bonjour {{ $candidat->prenom }} !</h1>
            <p class="text-slate-500 font-medium mt-2">Votre place est réservée dans la file d'attente.</p>
        </div>

        <!-- Ticket Card -->
        <div class="bg-white rounded-[3rem] shadow-2xl shadow-blue-900/10 border border-slate-100 overflow-hidden">
            <div class="p-8 sm:p-16 text-center">
                <p class="text-xs font-black uppercase tracking-[0.3em] text-slate-400 mb-6">Numéro de Ticket</p>
                
                <div class="relative inline-block mb-12">
                    <h2 class="text-8xl sm:text-[10rem] font-black text-slate-900 tracking-tighter leading-none">
                        SOLI-<span class="text-blue-600">{{ $ticket->numeroOrdre }}</span>
                    </h2>
                    <div class="absolute -top-4 -right-4 size-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 animate-pulse">
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                    </div>
                </div>

                <!-- Timer Section -->
                <div id="timer-section" class="bg-slate-50 rounded-[2rem] p-8 border border-slate-100 mb-10 transition-all duration-700">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-6">L'entretien commence dans :</p>
                    
                    <div class="flex justify-center items-center gap-4 sm:gap-10">
                        <div class="flex flex-col items-center min-w-[70px]">
                            <span id="days" class="text-4xl sm:text-6xl font-black text-blue-600">00</span>
                            <span class="text-[10px] font-black uppercase tracking-[0.1em] text-slate-400 mt-2">Jours</span>
                        </div>
                        <div class="text-3xl font-black text-slate-200 mb-6">:</div>
                        <div class="flex flex-col items-center min-w-[70px]">
                            <span id="hours" class="text-4xl sm:text-6xl font-black text-blue-600">00</span>
                            <span class="text-[10px] font-black uppercase tracking-[0.1em] text-slate-400 mt-2">Heures</span>
                        </div>
                        <div class="text-3xl font-black text-slate-200 mb-6">:</div>
                        <div class="flex flex-col items-center min-w-[70px]">
                            <span id="minutes" class="text-4xl sm:text-6xl font-black text-blue-600">00</span>
                            <span class="text-[10px] font-black uppercase tracking-[0.1em] text-slate-400 mt-2">Min</span>
                        </div>
                        <div class="text-3xl font-black text-slate-200 mb-6 hidden sm:block">:</div>
                        <div class="hidden sm:flex flex-col items-center min-w-[70px]">
                            <span id="seconds" class="text-4xl sm:text-6xl font-black text-blue-600">00</span>
                            <span class="text-[10px] font-black uppercase tracking-[0.1em] text-slate-400 mt-2">Sec</span>
                        </div>
                    </div>
                </div>

                <!-- Action Area -->
                <div id="action-area" class="max-w-sm mx-auto">
                    @if($candidat->is_present)
                        <div class="py-5 px-8 rounded-2xl bg-green-50 text-green-700 font-black border border-green-100 flex items-center justify-center gap-3">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            PRÉSENCE CONFIRMÉE
                        </div>
                    @else
                        <button type="button" id="presence-btn" disabled
                            class="w-full py-5 px-8 bg-slate-100 text-slate-400 rounded-2xl text-xl font-black cursor-not-allowed transition-all duration-500 shadow-xl shadow-slate-200/50 flex items-center justify-center gap-3">
                            <svg class="size-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Je suis présent(e)
                        </button>
                        <p id="presence-hint" class="mt-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Le bouton s'activera au début de la session</p>
                    @endif
                </div>
            </div>

            <div class="bg-blue-600 p-6 text-center">
                <p class="text-blue-100 text-sm font-bold tracking-wide">Gardez cette page ouverte pour suivre votre tour en temps réel.</p>
            </div>
        </div>

        <!-- Info Footer -->
        <div class="text-center">
            <p class="text-slate-400 text-sm font-medium">Session d'entretien : <span class="text-slate-800 font-bold">{{ $session->nom }}</span></p>
            <p class="text-slate-400 text-xs mt-1">Date : {{ $session->dateEntretien }} à {{ $session->heureDebut }}</p>
        </div>
    </div>
</div>

<!-- Loader Modal (Hidden) -->
<div id="loader-overlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center">
    <div class="bg-white p-10 rounded-[2rem] text-center max-w-sm w-full mx-4 shadow-2xl">
        <div class="size-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-6"></div>
        <h3 class="text-xl font-black text-slate-800">Confirmation en cours...</h3>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- CONFIGURATION ---
        const targetDate = new Date('{{ $session->dateEntretien }}T{{ $session->heureDebut }}');
        
        const timerEls = {
            days: document.getElementById('days'),
            hours: document.getElementById('hours'),
            minutes: document.getElementById('minutes'),
            seconds: document.getElementById('seconds')
        };
        const timerSection = document.getElementById('timer-section');
        const presenceBtn = document.getElementById('presence-btn');
        const presenceHint = document.getElementById('presence-hint');
        const loaderOverlay = document.getElementById('loader-overlay');

        function updateTimer() {
            const now = new Date();
            const diff = targetDate - now;

            if (diff <= 0) {
                clearInterval(timerInterval);
                activatePresenceButton();
                return;
            }

            const d = Math.floor(diff / (1000 * 60 * 60 * 24));
            const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);

            timerEls.days.textContent = String(d).padStart(2, '0');
            timerEls.hours.textContent = String(h).padStart(2, '0');
            timerEls.minutes.textContent = String(m).padStart(2, '0');
            timerEls.seconds.textContent = String(s).padStart(2, '0');
        }

        function activatePresenceButton() {
            if (!presenceBtn) return;
            
            presenceBtn.disabled = false;
            presenceBtn.classList.remove('bg-slate-100', 'text-slate-400', 'cursor-not-allowed');
            presenceBtn.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-700', 'shadow-blue-600/30', 'animate-pulse');
            
            presenceHint.textContent = "Cliquez ci-dessus pour confirmer votre arrivée";
            presenceHint.classList.replace('text-slate-400', 'text-blue-600');
            
            // On peut aussi masquer le timer ou changer son style
            timerSection.classList.add('opacity-50', 'scale-95');
        }

        const timerInterval = setInterval(updateTimer, 1000);
        updateTimer();

        // --- PRESENCE HANDLER ---
        if (presenceBtn) {
            presenceBtn.addEventListener('click', async () => {
                loaderOverlay.classList.replace('hidden', 'flex');
                
                try {
                    const response = await fetch('{{ route('candidat.mark-presence') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                } catch (error) {
                    alert("Une erreur est survenue lors de la validation.");
                    loaderOverlay.classList.replace('flex', 'hidden');
                }
            });
        }
    });
</script>
@endsection
