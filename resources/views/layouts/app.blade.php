<!DOCTYPE html>
<html lang="en" class="{{ auth()->user()->hasRole('admin') ? 'h-full' : '' }} bg-brand-cream/20">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Prevent caching of protected pages --}}
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>@yield('title', 'Absence Admin')</title>
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Cropper.js -->
    <link  href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Admin Layout: Geser konten ke kanan sebesar lebar sidebar di desktop */
        @media (min-width: 1024px) {
            .admin-main-content {
                margin-left: 16rem !important;
            }
        }
    </style>
    
    @stack('styles')

    <link rel="manifest" href="/manifest.json">

    <!-- Apple PWA support -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Absensi">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
</head>
@php
    $isAdmin = auth()->user()->hasRole('admin');
@endphp
<body class="{{ $isAdmin ? 'h-full' : '' }} overflow-x-hidden" x-data="{ sidebarOpen: false }">

    @if(!$isAdmin)
    <!-- SPLASH SCREEN / LOADING SCREEN (Mobile/User Only) -->
    <div id="app-loading-screen" class="fixed inset-0 z-[9999] bg-white flex flex-col items-center justify-center transition-opacity duration-700 ease-in-out" style="display: none;">
        <!-- Full Screen Custom Splash Image -->
        <img src="{{ asset('img/splash-page.jpg') }}" class="w-full h-full object-cover">
    </div>

    <script>
        // Check if splash has already been shown in this session
        // If NOT shown, display it immediately
        if (!sessionStorage.getItem('splashShown')) {
            document.getElementById('app-loading-screen').style.display = 'flex';
            
            window.addEventListener('load', function() {
                setTimeout(function() {
                    const loader = document.getElementById('app-loading-screen');
                    if (loader) {
                        loader.style.opacity = '0';
                        setTimeout(() => {
                            loader.remove();
                            // Set flag so it doesn't show again on navigation
                            sessionStorage.setItem('splashShown', 'true');
                        }, 700);
                    }
                }, 1500);
            });
        } else {
            // Safety: ensure it's removed from DOM if it somehow stayed (though style=none prevents visual)
            const loader = document.getElementById('app-loading-screen');
            if(loader) loader.remove();
        }
    </script>
    @endif
    
    <div class="{{ $isAdmin ? 'min-h-full' : '' }} flex flex-col">
        @if($isAdmin)
            <!-- Mobile Top Bar (ADMIN ONLY) -->
            <div class="lg:hidden bg-brand-maroon text-white flex items-center justify-between px-4 py-3 shadow-md z-30 sticky top-0">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-white rounded flex items-center justify-center p-1">
                        <img src="{{ asset('img/logo_kasau.png') }}" class="w-full h-full object-contain">
                    </div>
                    <span class="font-bold text-lg">KASAU Absensi</span>
                </div>
                <button @click="sidebarOpen = true" class="p-2 -mr-2 text-white focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Sidebar for mobile (ADMIN ONLY) -->
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

            <!-- Sidebar for desktop (ADMIN ONLY) -->
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

            <!-- Mobile sidebar content (ADMIN ONLY) -->
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
        @endif

        <!-- Main content -->
        <div class="flex flex-col flex-1 min-h-screen" @if($isAdmin) style="margin-left: 256px;" @endif>
            <!-- DEBUG: isAdmin = {{ $isAdmin ? 'TRUE' : 'FALSE' }} -->
            <main class="{{ $isAdmin ? 'flex-1' : '' }}">
                <div class="{{ $isAdmin ? 'py-6' : 'pt-6' }}" @if(!$isAdmin) style="padding-bottom: calc(4rem + env(safe-area-inset-bottom, 0px));" @endif>
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    @if(!$isAdmin)
        @include('components.bottom-nav')
        @include('components.install-pwa-prompt')
    @endif

    <!-- Global Logout Form -->
    <form id="global-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
        @csrf
    </form>

    @stack('scripts')
<script>
    // Force reload when navigating back (bfcache)
    window.addEventListener('pageshow', function (event) {
        if (event.persisted || (window.performance && window.performance.getEntriesByType('navigation')[0].type === 'back_forward')) {
            window.location.reload();
        }
    });

    // Global Logout Function
    // Make sure this is in the global scope
    window.confirmLogout = function() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda akan keluar dari aplikasi ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Keluar!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Unregister service workers to avoid cached pages after logout
                if (navigator.serviceWorker) {
                    navigator.serviceWorker.getRegistrations().then(regs => {
                        Promise.all(regs.map(r => r.unregister())).then(() => {
                            const form = document.getElementById('global-logout-form');
                            if (form) form.submit();
                        });
                    });
                } else {
                    const form = document.getElementById('global-logout-form');
                    if (form) form.submit();
                }
            }
        })
    }
</script>
    
    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
