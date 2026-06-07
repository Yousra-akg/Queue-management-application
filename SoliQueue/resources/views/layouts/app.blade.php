<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="color-scheme: light;">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SoliQueue') }}</title>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body class="bg-gray-50 h-screen font-sans antialiased text-gray-800">
    <div id="app" class="min-h-full flex flex-col">
        <!-- Navbar (Uniquement visible si connecté) -->
        @auth
        <!-- Notification Banner (Controlled by JS) -->
        <div id="notif-banner" class="hidden bg-[#1A73E8] py-2 text-center text-[11px] sm:text-xs font-bold text-white tracking-wide shadow-md z-[60] relative transition-all duration-500">
            <span class="inline-flex items-center gap-x-1.5">
                <span class="size-2 inline-block rounded-full bg-white animate-pulse shadow-sm"></span>
                <span id="notif-text">
                    @if(isset($confirmed) && $confirmed)
                        C'est le jour de votre entretien ! Bonne chance pour votre passage.
                    @else
                        C'est le jour de votre entretien ! Signalez votre arrivée au centre.
                    @endif
                </span>
            </span>
        </div>

        <header class="flex flex-wrap sm:justify-start sm:flex-nowrap w-full bg-white text-sm py-4 border-b border-gray-200 shadow-sm relative z-50">
            <nav class="max-w-[85rem] w-full mx-auto px-4 sm:flex sm:items-center sm:justify-between" aria-label="Global">
                <div class="flex items-center justify-between">
                    <a class="flex-none" href="{{ route('candidat.bienvenue') }}">
                        <img src="{{ asset('img/logo.png') }}" alt="SoliCode" class="h-10 w-auto">
                    </a>
                </div>
                
                <div class="flex flex-row items-center gap-4 mt-4 sm:justify-end sm:mt-0 sm:ps-5">
                    <div id="navbar-notif-target" class="flex items-center"></div>
                    <div class="flex items-center gap-x-3">
                        <span class="text-sm font-semibold text-gray-500 capitalize">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</span>
                        @if(Auth::user()->photo)
                            <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="Avatar" class="size-[38px] rounded-full object-cover ring-2 ring-white shadow-sm">
                        @else
                            <div class="inline-flex items-center justify-center size-[38px] rounded-full bg-blue-50 text-blue-600 font-bold uppercase ring-2 ring-white">
                                {{ substr(Auth::user()->prenom, 0, 1) }}{{ substr(Auth::user()->nom, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="pl-3 border-l border-gray-200">
                        <form action="{{ route('logout') }}" method="POST" class="inline m-0">
                            @csrf
                            <button type="submit" aria-label="Déconnexion" class="inline-flex items-center justify-center size-8 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </nav>
        </header>
        @endauth

        <!-- Content -->
        <main class="flex-grow flex items-center justify-center p-6 h-full animate-fade-in">
            <div class="w-full">
                @yield('content')
            </div>
        </main>
    </div>
    @stack('modals')
    @yield('scripts')
    <!-- Chat Widget IA -->
    <x-chat-widget />
</body>
</html>

