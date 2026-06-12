@extends('layouts.app', ['candidat_name' => $candidat->prenom . ' ' . $candidat->nom])

@section('content')
<div class="max-w-xl mx-auto w-full px-4 sm:px-6 lg:px-8">
    <!-- Card Container -->
    <div class="bg-white border border-gray-200 rounded-3xl shadow-sm p-6 sm:p-8 animate-slide-up">


        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm font-medium text-center flex items-center justify-center gap-2 animate-shake">
                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        @if($candidat->entretien_id)
            <!-- Success Header -->
            <div class="text-center mb-6">
                <div class="inline-flex justify-center items-center size-[72px] rounded-full border-4 border-green-50 bg-green-100 mb-6 group transition-all duration-300 hover:scale-110">
                    <svg class="flex-shrink-0 size-10 text-green-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="m9 12 2 2 4-4" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 sm:text-4xl tracking-tight">
                    Félicitations !
                </h1>
                <p class="mt-3 text-lg text-gray-500">
                    Ravi de vous voir, <span class="text-blue-600 font-bold">{{ $candidat->prenom }}</span>. Votre profil a été validé avec succès pour cet entretien.
                </p>
            </div>

            <!-- Candidate Card Info -->
            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 mb-8">
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Candidat</p>
                            <p class="text-lg font-bold text-gray-800">{{ $candidat->prenom }} {{ $candidat->nom }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">CIN</p>
                            <p class="text-lg font-bold text-gray-800">{{ $candidat->cin }}</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200 flex items-center justify-between">
                        <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <span class="size-1.5 inline-block rounded-full bg-green-800"></span>
                            QCM Validé
                        </span>
                        <span class="text-xs text-gray-400 italic">Redirection vers votre file...</span>
                    </div>
                </div>
            </div>

            <!-- CTA Button -->
            <div class="grid">
                <a href="{{ route('candidat.ticket-details') }}" 
                    class="py-4 px-8 inline-flex justify-center items-center gap-x-3 text-lg font-bold rounded-2xl border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] shadow-xl shadow-blue-600/30 group">
                    Voir mon ticket
                    <svg class="flex-shrink-0 size-5 transition-transform duration-300 group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z" />
                        <path d="M13 5v2" />
                        <path d="M13 17v2" />
                        <path d="M13 11v2" />
                    </svg>
                </a>
            </div>

            <p class="mt-6 text-center text-sm text-gray-400">
                <svg class="inline-block size-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Vous allez être redirigé vers votre file d'attente.
            </p>
        @else
            <!-- Pending State Header -->
            <div class="text-center mb-6">
                <div class="inline-flex justify-center items-center size-[72px] rounded-full border-4 border-yellow-50 bg-yellow-100 mb-6 group transition-all duration-300 hover:scale-110">
                    <svg class="flex-shrink-0 size-10 text-yellow-600 animate-pulse" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 sm:text-4xl tracking-tight">
                    En attente d'affectation
                </h1>
                <p class="mt-3 text-lg text-gray-500">
                    Bonjour <span class="text-blue-600 font-bold">{{ $candidat->prenom }}</span>. Votre profil est en cours de traitement par l'administration. Veuillez patienter.
                </p>
            </div>

            <!-- Candidate Card Info -->
            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 mb-8">
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Candidat</p>
                            <p class="text-lg font-bold text-gray-800">{{ $candidat->prenom }} {{ $candidat->nom }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">CIN</p>
                            <p class="text-lg font-bold text-gray-800">{{ $candidat->cin }}</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200 flex items-center justify-between">
                        <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <span class="size-1.5 inline-block rounded-full bg-yellow-800 animate-ping"></span>
                            Dossier en traitement
                        </span>
                    </div>
                </div>
            </div>

            <!-- CTA Button -->
            <div class="grid">
                <button disabled
                    class="py-4 px-8 inline-flex justify-center items-center gap-x-3 text-lg font-bold rounded-2xl border border-transparent bg-gray-100 text-gray-400 cursor-not-allowed transition-all duration-300">
                    Ticket non disponible
                </button>
            </div>

            <p class="mt-6 text-center text-sm text-gray-400">
                <svg class="inline-block size-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Revenez vérifier plus tard ou attendez les instructions.
            </p>
        @endif
    </div>
</div>

@endsection

