@extends('layouts.app')

@section('content')
<div class="w-full max-w-md mx-auto animate-slide-up">
  <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden dark:bg-neutral-900 dark:border-neutral-700">
    <div class="p-8 sm:p-12">
      <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center mb-6">
          <img src="{{ asset('img/logo.png') }}" alt="SoliCode" class="h-16 w-auto">
        </div>
        <h1 class="block text-3xl font-bold text-gray-800 dark:text-white">Connexion Candidat</h1>
        <p class="mt-3 text-sm text-gray-600 dark:text-neutral-400">
          Veuillez saisir votre numéro de CIN pour accéder à votre espace.
        </p>
      </div>

      <div class="mt-5">
        <form action="{{ route('login') }}" method="POST">
          @csrf
          <div class="grid gap-y-6">
            <!-- Form Group -->
            <div>
              <label for="cin" class="block text-sm font-semibold mb-2 dark:text-white">Numéro de CIN</label>
              <div class="relative">
                <input type="text" id="cin" name="cin" class="py-3 px-4 block w-full border-gray-200 rounded-xl text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600 @error('cin') border-red-500 @enderror" required aria-describedby="cin-error" placeholder="Ex: AB123456" value="{{ old('cin') }}" autofocus>
                @error('cin')
                <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                  <svg class="size-5 text-red-500" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                  </svg>
                </div>
                @enderror
              </div>
              @error('cin')
                <p class="text-xs text-red-600 mt-2" id="cin-error">{{ $message }}</p>
              @enderror
            </div>
            <!-- End Form Group -->

            <div class="flex items-center">
              <div class="flex">
                <input id="remember" name="remember" type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800" {{ old('remember') ? 'checked' : '' }}>
              </div>
              <div class="ms-3">
                <label for="remember" class="text-sm dark:text-white">Se souvenir de moi</label>
              </div>
            </div>

            <button type="submit" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-xl border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none transition-all">
              Accéder à mon espace
              <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <p class="mt-6 text-center text-sm text-gray-500 dark:text-neutral-500">
    Besoin d'aide ? Contactez l'administration de SoliCode.
  </p>
</div>
@endsection
