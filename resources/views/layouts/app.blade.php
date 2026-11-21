<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Saisine')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- jQuery (required for toastr) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <style>
        /* Custom toastr styling */
        #toast-container > div {
            opacity: 1;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        .toast-success {
            background-color: #10b981 !important;
        }
        .toast-error {
            background-color: #ef4444 !important;
        }
        .toast-info {
            background-color: #3b82f6 !important;
        }
        .toast-warning {
            background-color: #f59e0b !important;
        }
    </style>
</head>
<body class="bg-gray-50" x-data="{ sidebarOpen: false }">
    @auth
    <!-- Mobile Burger Button -->
    <button 
        @click="sidebarOpen = !sidebarOpen"
        class="fixed top-5 left-5 z-50 lg:hidden p-2.5 rounded-xl bg-slate-800 shadow-xl text-white hover:bg-slate-700 transition-all duration-200"
    >
        <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <!-- Sidebar Overlay (Mobile) -->
    <div 
        x-show="sidebarOpen" 
        @click="sidebarOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-60 z-40 lg:hidden backdrop-blur-sm"
        style="display: none;"
    ></div>

    <!-- Sidebar -->
    <aside 
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed top-0 left-0 bottom-0 w-72 sidebar-modern shadow-2xl z-40 transform transition-all duration-300 ease-in-out lg:translate-x-0 border-r border-slate-700"
    >
        <!-- Logo -->
        <div class="p-6 mb-2">
            <div class="flex items-center space-x-3">
                <img 
                    src="{{ asset('logo.png') }}" 
                    alt="Logo Saisine" 
                    class="w-12 h-12 object-contain"
                    draggable="false"
                >
                <div>
                    <span class="text-xl font-bold text-white tracking-tight">Saisine</span>
                    <p class="text-xs text-slate-400">Gestion des demandes</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="px-4 space-y-1" x-data="{ 
            openSection: '{{ request()->routeIs('dashboard') || request()->routeIs('saisines.*') ? 'saisine' : (request()->routeIs('admin.mandatory-fields') ? 'configuration' : (request()->routeIs('admin.roles') || request()->routeIs('admin.users') || request()->routeIs('admin.structures') ? 'access' : 'saisine')) }}' 
        }">
            <!-- Saisine Section -->
            <div class="mb-2">
                <button 
                    @click="openSection = openSection === 'saisine' ? '' : 'saisine'"
                    class="w-full flex items-center justify-between px-4 py-3 text-slate-300 rounded-xl transition-all duration-200 hover:bg-slate-700 hover:bg-opacity-50"
                >
                    <div class="flex items-center">
                        <svg class="icon-lg text-slate-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="font-medium">Saisine</span>
                    </div>
                    <svg class="icon-sm transition-transform duration-200" :class="{ 'rotate-180': openSection === 'saisine' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                
                <div x-show="openSection === 'saisine'" x-collapse class="mt-1 space-y-1 pl-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-slate-300 rounded-xl transition-all duration-200 nav-item-hover {{ request()->routeIs('dashboard') ? 'nav-item-active text-white' : '' }}">
                        <div class="w-8 h-8 flex items-center justify-center rounded-lg {{ request()->routeIs('dashboard') ? 'bg-[--color-orange-primary] bg-opacity-20' : 'bg-slate-700 bg-opacity-50' }} mr-3">
                            <svg class="icon-md {{ request()->routeIs('dashboard') ? 'text-[--color-orange-primary]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium">Tableau de bord</span>
                    </a>
                    
                    <a href="{{ route('saisines.index') }}" class="flex items-center px-4 py-3 text-slate-300 rounded-xl transition-all duration-200 nav-item-hover {{ request()->routeIs('saisines.index') || request()->routeIs('saisines.show') || request()->routeIs('saisines.edit') ? 'nav-item-active text-white' : '' }}">
                        <div class="w-8 h-8 flex items-center justify-center rounded-lg {{ request()->routeIs('saisines.index') || request()->routeIs('saisines.show') || request()->routeIs('saisines.edit') ? 'bg-[--color-orange-primary] bg-opacity-20' : 'bg-slate-700 bg-opacity-50' }} mr-3">
                            <svg class="icon-md {{ request()->routeIs('saisines.index') || request()->routeIs('saisines.show') || request()->routeIs('saisines.edit') ? 'text-[--color-orange-primary]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium">Mes demandes</span>
                    </a>

                    <a href="{{ route('saisines.all') }}" class="flex items-center px-4 py-3 text-slate-300 rounded-xl transition-all duration-200 nav-item-hover {{ request()->routeIs('saisines.all') ? 'nav-item-active text-white' : '' }}">
                        <div class="w-8 h-8 flex items-center justify-center rounded-lg {{ request()->routeIs('saisines.all') ? 'bg-[--color-orange-primary] bg-opacity-20' : 'bg-slate-700 bg-opacity-50' }} mr-3">
                            <svg class="icon-md {{ request()->routeIs('saisines.all') ? 'text-[--color-orange-primary]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium">Liste des demandes</span>
                    </a>
                </div>
            </div>

            <!-- Configuration Section -->
            <div class="mb-2">
                <button 
                    @click="openSection = openSection === 'configuration' ? '' : 'configuration'"
                    class="w-full flex items-center justify-between px-4 py-3 text-slate-300 rounded-xl transition-all duration-200 hover:bg-slate-700 hover:bg-opacity-50"
                >
                    <div class="flex items-center">
                        <svg class="icon-lg text-slate-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="font-medium">Configuration</span>
                    </div>
                    <svg class="icon-sm transition-transform duration-200" :class="{ 'rotate-180': openSection === 'configuration' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                
                <div x-show="openSection === 'configuration'" x-collapse class="mt-1 space-y-1 pl-4">
                    {{-- href="{{ route('admin.mandatory-fields') }}"  --}}
                    <a href="{{ route('admin.mandatory-fields')}}" class="flex items-center px-4 py-3 text-slate-300 rounded-xl transition-all duration-200 nav-item-hover {{ request()->routeIs('admin.mandatory-fields') ? 'nav-item-active text-white' : '' }}">
                        <div class="w-8 h-8 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.mandatory-fields') ? 'bg-[--color-orange-primary] bg-opacity-20' : 'bg-slate-700 bg-opacity-50' }} mr-3">
                            <svg class="icon-md {{ request()->routeIs('admin.mandatory-fields') ? 'text-[--color-orange-primary]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium">Champs obligatoires</span>
                    </a>
                </div>
            </div>

            <!-- Access Section -->
            <div class="mb-2">
                <button 
                    @click="openSection = openSection === 'access' ? '' : 'access'"
                    class="w-full flex items-center justify-between px-4 py-3 text-slate-300 rounded-xl transition-all duration-200 hover:bg-slate-700 hover:bg-opacity-50"
                >
                    <div class="flex items-center">
                        <svg class="icon-lg text-slate-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span class="font-medium">Accès</span>
                    </div>
                    <svg class="icon-sm transition-transform duration-200" :class="{ 'rotate-180': openSection === 'access' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                
                <div x-show="openSection === 'access'" x-collapse class="mt-1 space-y-1 pl-4">
                    {{-- href="{{ route('admin.roles') }}" --}}
                    <a class="flex items-center px-4 py-3 text-slate-300 rounded-xl transition-all duration-200 nav-item-hover {{ request()->routeIs('admin.roles') ? 'nav-item-active text-white' : '' }}">
                        <div class="w-8 h-8 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.roles') ? 'bg-[--color-orange-primary] bg-opacity-20' : 'bg-slate-700 bg-opacity-50' }} mr-3">
                            <svg class="icon-md {{ request()->routeIs('admin.roles') ? 'text-[--color-orange-primary]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium">Rôles</span>
                    </a>

                    {{-- href="{{ route('admin.users') }}" --}}
                    <a class="flex items-center px-4 py-3 text-slate-300 rounded-xl transition-all duration-200 nav-item-hover {{ request()->routeIs('admin.users') ? 'nav-item-active text-white' : '' }}">
                        <div class="w-8 h-8 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.users') ? 'bg-[--color-orange-primary] bg-opacity-20' : 'bg-slate-700 bg-opacity-50' }} mr-3">
                            <svg class="icon-md {{ request()->routeIs('admin.users') ? 'text-[--color-orange-primary]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium">Utilisateurs</span>
                    </a>

                    {{-- href="{{ route('admin.structures') }}" --}}
                    <a class="flex items-center px-4 py-3 text-slate-300 rounded-xl transition-all duration-200 nav-item-hover {{ request()->routeIs('admin.structures') ? 'nav-item-active text-white' : '' }}">
                        <div class="w-8 h-8 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.structures') ? 'bg-[--color-orange-primary] bg-opacity-20' : 'bg-slate-700 bg-opacity-50' }} mr-3">
                            <svg class="icon-md {{ request()->routeIs('admin.structures') ? 'text-[--color-orange-primary]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium">Structures</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- User Section (Bottom) -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-700 bg-slate-900 bg-opacity-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 flex-1 min-w-0">
                    <div class="w-10 h-10 bg-gradient-to-br from-slate-600 to-slate-700 rounded-xl flex items-center justify-center ring-2 ring-slate-600">
                        <svg class="icon-lg text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-400 flex items-center gap-1.5">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </span>
                            Connecté
                        </p>
                    </div>
                </div>
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="p-2.5 hover:bg-slate-700 rounded-lg transition-all duration-200 text-slate-400 hover:text-white" title="Déconnexion">
                        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Top Bar (for notifications and mobile) -->
    <div class="fixed top-0 left-0 lg:left-72 right-0 h-16 bg-white border-b border-gray-200 z-30 flex items-center justify-between px-4 lg:px-8">
        <div class="lg:hidden w-12"></div> <!-- Spacer for burger button -->
        <div class="flex-1"></div>
        <div class="flex items-center space-x-4">
            <!-- Notification Bell -->
            <x-notification-bell 
                :unreadCount="Auth::user()->unreadNotifications->count()"
                :notifications="Auth::user()->notifications()->latest()->take(5)->get()"
            />
        </div>
    </div>

    <!-- Main Content -->
    <main class="lg:ml-72 mt-16 min-h-screen">
        @yield('content')
    </main>
    @endauth

    @guest
    <main>
        @yield('content')
    </main>
    @endguest

    <!-- Toastr Notifications -->
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if(session('info'))
            toastr.info("{{ session('info') }}");
        @endif

        @if(session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif
    </script>
</body>
</html>
