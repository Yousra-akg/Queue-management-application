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
    $startTime = \Carbon\Carbon::parse($entretien->dateEntretien . ' ' . $entretien->heureDebut);
    $confirmed = (bool) $candidat->is_present;

@endphp

<div x-data="ticketManager({{ $startTime->timestamp }} * 1000, {{ $confirmed ? 'true' : 'false' }}, {{ $candidat->id }}, {{ json_encode($queue) }}, '{{ route('candidat.mark-presence') }}', '{{ route('candidat.queue-status') }}', '{{ csrf_token() }}')" class="max-w-[38rem] mx-auto px-4 py-6 sm:py-10 space-y-6 w-full" id="main-container">

    <!-- Teleported Notification Bell to Navbar -->
    <template x-teleport="#navbar-notif-target">
        <div class="relative">
            <button @click="showNotifDropdown = !showNotifDropdown" type="button" class="relative p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all duration-300">
                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <span x-show="unreadCount > 0" class="absolute top-1.5 right-1.5 flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                </span>
            </button>
            
            <!-- Notification Dropdown -->
            <div x-show="showNotifDropdown" @click.away="showNotifDropdown = false" class="absolute right-0 mt-2 w-80 bg-white border border-slate-200 rounded-2xl shadow-2xl overflow-hidden z-[150]" style="display: none;">
                <div class="px-4 py-3 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                    <span class="font-black text-slate-800 uppercase tracking-widest text-[10px]">Notifications</span>
                    <span class="bg-red-50 text-red-600 text-[9px] font-black px-2 py-0.5 rounded-full" x-text="unreadCount + ' non lue(s)'"></span>
                </div>
                <div class="max-h-60 overflow-y-auto divide-y divide-slate-100">
                    <template x-if="notifications.length === 0">
                        <div class="p-6 text-center text-slate-400 text-xs font-medium">
                            Aucune notification récente.
                        </div>
                    </template>
                    <template x-for="n in notifications" :key="n.id">
                        <div class="p-4 hover:bg-slate-50/50 transition-colors flex justify-between items-start gap-x-3">
                            <div class="space-y-1">
                                <p class="text-xs font-black text-slate-800" x-text="n.titre"></p>
                                <p class="text-[11px] text-slate-500 font-medium leading-relaxed" x-text="n.message"></p>
                            </div>
                            <button @click="markAsRead(n.id)" class="text-[9px] font-black text-[#1A73E8] hover:text-blue-700 uppercase tracking-wider shrink-0 mt-0.5">
                                OK
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>

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

            <!-- Timer removed -->

            <!-- Presence Button / Badge Area -->
            <div id="presence-action-area" class="max-w-xs mx-auto space-y-3 transition-all duration-500">
                @if(!$confirmed)
                <button type="button" id="presence-btn"
                    class="w-full py-4 px-6 inline-flex justify-center items-center gap-x-2 text-base font-black rounded-xl border border-transparent bg-[#1A73E8] text-white hover:bg-blue-700 shadow-blue-600/30 animate-pulse transition-all duration-500 shadow-lg">
                    <svg class="flex-shrink-0 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    Je suis présent(e)
                </button>
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

    <!-- Priority Alert Modal -->
    <template x-teleport="body">
        <template x-if="priorityAlert">
            <div class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm animate-fade-in">
                <div class="bg-white border-2 border-red-500 rounded-[2.5rem] shadow-2xl max-w-md w-full overflow-hidden transform transition-all duration-300">
                    <div class="h-3 bg-gradient-to-r from-red-500 via-orange-500 to-yellow-500 animate-pulse"></div>
                    <div class="p-8 sm:p-10 text-center">
                        <div class="mb-6 flex justify-center">
                            <span class="inline-flex justify-center items-center size-16 rounded-full bg-red-50 text-red-600 animate-bounce">
                                <svg class="size-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            </span>
                        </div>
                        <h3 class="mb-2 text-2xl font-black text-red-600 tracking-tight" x-text="priorityAlert.titre"></h3>
                        <p class="text-slate-600 font-bold mb-8 leading-relaxed text-sm" x-text="priorityAlert.message"></p>
                        
                        <button type="button" @click="dismissPriorityAlert(priorityAlert.id)"
                            class="w-full py-4 px-6 text-base font-black rounded-xl bg-red-600 hover:bg-red-700 text-white shadow-xl shadow-red-600/20 transform hover:-translate-y-0.5 transition-all">
                            J'ai compris !
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </template>

</div>

@endsection

@push('modals')
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
                
                <div class="flex justify-center gap-x-2 sm:gap-x-3 mb-8" id="otp-container">
                    <input type="text" maxlength="1" class="otp-input w-14 h-16 sm:w-16 sm:h-20 text-center text-3xl sm:text-4xl font-black text-slate-800 bg-slate-50 border-2 border-slate-200 rounded-2xl focus:border-[#1A73E8] focus:ring-4 focus:ring-blue-600/20 focus:outline-none transition-all placeholder-slate-300 shadow-sm" placeholder="•">
                    <input type="text" maxlength="1" class="otp-input w-14 h-16 sm:w-16 sm:h-20 text-center text-3xl sm:text-4xl font-black text-slate-800 bg-slate-50 border-2 border-slate-200 rounded-2xl focus:border-[#1A73E8] focus:ring-4 focus:ring-blue-600/20 focus:outline-none transition-all placeholder-slate-300 shadow-sm" placeholder="•">
                    <input type="text" maxlength="1" class="otp-input w-14 h-16 sm:w-16 sm:h-20 text-center text-3xl sm:text-4xl font-black text-slate-800 bg-slate-50 border-2 border-slate-200 rounded-2xl focus:border-[#1A73E8] focus:ring-4 focus:ring-blue-600/20 focus:outline-none transition-all placeholder-slate-300 shadow-sm" placeholder="•">
                    <input type="text" maxlength="1" class="otp-input w-14 h-16 sm:w-16 sm:h-20 text-center text-3xl sm:text-4xl font-black text-slate-800 bg-slate-50 border-2 border-slate-200 rounded-2xl focus:border-[#1A73E8] focus:ring-4 focus:ring-blue-600/20 focus:outline-none transition-all placeholder-slate-300 shadow-sm" placeholder="•">
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

@endpush

@section('scripts')

@endsection

