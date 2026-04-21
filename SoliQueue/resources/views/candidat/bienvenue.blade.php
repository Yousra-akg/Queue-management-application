@extends('layouts.app', ['candidat_name' => $candidat->prenom . ' ' . $candidat->nom])

@section('content')
<div class="max-w-2xl w-full">
    <!-- Success Card -->
    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-green-900/10 border border-slate-100 p-8 sm:p-12 relative overflow-hidden">
        
        <!-- Decoration -->
        <div class="absolute -top-10 -right-10 size-40 bg-green-50 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 -left-10 size-40 bg-blue-50 rounded-full blur-3xl"></div>

        <div class="relative text-center">
            <!-- Icon -->
            <div class="inline-flex size-20 bg-green-100 rounded-3xl items-center justify-center mb-8 text-green-600 animate-bounce">
                <svg class="size-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </div>

            <h1 class="text-4xl font-black text-slate-800 tracking-tight mb-4">Félicitations !</h1>
            <p class="text-xl text-slate-500 font-medium max-w-md mx-auto leading-relaxed">
                Ravi de vous voir, <span class="text-blue-600 font-black">{{ $candidat->prenom }}</span>. Votre profil a été validé avec succès pour cet entretien.
            </p>

            <!-- Status Badge -->
            <div class="mt-8 inline-flex items-center gap-3 px-6 py-3 bg-slate-50 rounded-2xl border border-slate-100">
                <span class="size-3 bg-green-500 rounded-full animate-pulse"></span>
                <span class="text-sm font-black text-slate-600 uppercase tracking-widest">QCM Validé</span>
            </div>

            <!-- CTA -->
            <div class="mt-12">
                <a href="{{ route('candidat.ticket-details') }}" 
                    class="group inline-flex items-center gap-4 px-10 py-5 bg-slate-900 text-white rounded-2xl text-xl font-black shadow-2xl shadow-slate-900/20 hover:bg-blue-600 hover:-translate-y-1 transition-all duration-300">
                    Voir mon ticket
                    <svg class="size-6 transition-transform group-hover:translate-x-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4 4H3"></path></svg>
                </a>
            </div>

            <p class="mt-8 text-slate-400 text-sm font-bold flex items-center justify-center gap-2">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Vous allez être redirigé vers votre file d'attente.
            </p>
        </div>
    </div>
</div>
@endsection
