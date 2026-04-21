@extends('layouts.app')

@section('content')
<div class="w-full max-w-md">
    <!-- Login Card -->
    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-blue-900/10 border border-slate-100 overflow-hidden relative">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-600 to-indigo-600"></div>
        
        <div class="p-8 sm:p-12">
            <!-- Header -->
            <div class="text-center mb-10">
                <div class="inline-flex size-16 bg-blue-50 rounded-2xl items-center justify-center mb-6 text-blue-600">
                    <svg class="size-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Bienvenue</h1>
                <p class="text-slate-400 font-medium mt-2">Prêt pour votre entretien SoliCode ?</p>
            </div>

            @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl text-red-600 text-sm font-bold flex items-center gap-3">
                <svg class="size-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('error') }}
            </div>
            @endif

            <!-- Form -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="cin" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Numéro de CIN</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-300 group-focus-within:text-blue-600 transition-colors">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm5 3a3 3 0 01-3 3H9a3 3 0 01-3-3v-1h10v1z"></path></svg>
                        </div>
                        <input type="text" name="cin" id="cin" required autofocus
                            class="block w-full pl-12 pr-4 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl text-lg font-bold text-slate-800 placeholder-slate-300 focus:bg-white focus:border-blue-600 focus:outline-none transition-all"
                            placeholder="Ex: AB123456" value="{{ old('cin') }}">
                    </div>
                </div>

                <button type="submit" 
                    class="w-full py-4 bg-blue-600 text-white rounded-2xl text-lg font-black shadow-xl shadow-blue-600/30 hover:bg-blue-700 hover:-translate-y-1 active:translate-y-0 transition-all duration-300 flex items-center justify-center gap-3">
                    Se connecter
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </button>
            </form>
        </div>
    </div>
    
    <p class="mt-8 text-center text-slate-400 text-sm font-medium">
        Accès réservé aux candidats sélectionnés.
    </p>
</div>
@endsection
