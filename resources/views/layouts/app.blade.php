<!DOCTYPE html>
<html lang="en" class="h-full bg-brand-cream/20">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Absence Admin')</title>
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    
    @stack('styles')
</head>
<body class="h-full" x-data="{ sidebarOpen: false }">
    
    <div class="min-h-full">
        <!-- Sidebar for mobile -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
             style="display: none;">
        </div>

        <!-- Sidebar for desktop -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
            <div class="flex flex-col flex-grow bg-gradient-to-b from-brand-maroon to-brand-maroon/90 pt-5 pb-4 overflow-y-auto">
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0 px-4 mb-8">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('img/logo_kasau.png') }}" alt="Logo KASAU" class="w-8 h-8 object-contain">
                        </div>
                        <div class="ml-3 leading-tight">
                            <p class="text-lg font-bold text-white">Absensi Karyawan</p>
                            <p class="text-xs text-white/70">KASAU SINAR SAMUDERA</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                @include('components.sidebar')
            </div>
        </div>

        <!-- Mobile sidebar -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="fixed inset-y-0 left-0 z-50 w-64 lg:hidden flex flex-col h-full bg-brand-maroon shadow-2xl"
             style="display: none;">
            
            <div class="flex flex-col flex-1 h-full pt-5 pb-4 overflow-y-auto">
                <div class="flex items-center justify-between px-4 mb-8">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0">
                            <img src="{{ asset('img/logo_kasau.png') }}" alt="Logo KASAU" class="w-8 h-8 object-contain">
                        </div>
                        <div class="ml-3 leading-tight">
                            <p class="text-base font-bold text-white">Absensi Karyawan</p>
                            <p class="text-[10px] text-white/70">KASAU SINAR SAMUDERA</p>
                        </div>
                    </div>
                    <button @click="sidebarOpen = false" class="text-white hover:text-white/80 transition p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                @include('components.sidebar')
            </div>
        </div>

        <!-- Main content -->
        <div class="lg:pl-64 flex flex-col flex-1">
            <main class="flex-1">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>
    @stack('scripts')
    <!-- Floating button open sidebar (mobile) -->
    <button @click="sidebarOpen = true"
        class="lg:hidden fixed bottom-5 right-5 z-50 w-12 h-12 rounded-full bg-brand-maroon text-white shadow-lg flex items-center justify-center
               focus:outline-none focus:ring-2 focus:ring-brand-gold">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
