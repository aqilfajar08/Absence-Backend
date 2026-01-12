<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Absensi Karyawan</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        /* Background image dengan overlay */
        .login-bg {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        /* Glassmorphism untuk input */
        .glass-input {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .glass-input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(212, 175, 55, 0.5);
        }
        
        .glass-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="h-full">
    {{-- Background Image dengan Slideshow Alpine.js --}}
    <div class="login-bg min-h-screen relative overflow-hidden" 
        x-data="{
            images: [
                '{{ asset('img/login/bg1.jpg') }}',
                '{{ asset('img/login/bg2.jpg') }}',
                '{{ asset('img/login/bg3.jpg') }}',
                '{{ asset('img/login/bg4.jpg') }}',
                '{{ asset('img/login/bg5.jpg') }}',
                '{{ asset('img/login/bg6.jpg') }}',
            ],
            activeImage: 0,
            init() {
                // Ganti slide setiap 5 detik (5000ms)
                setInterval(() => {
                    this.activeImage = (this.activeImage + 1) % this.images.length;
                }, 5000);
            }
        }">
        
        {{-- Loop Gambar Background dengan efek Fade --}}
        <template x-for="(image, index) in images" :key="index">
            <div x-show="activeImage === index"
                 x-transition:enter="transition ease-in-out duration-1000"
                 x-transition:enter-start="opacity-0 scale-105"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in-out duration-1000"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-105"
                 class="absolute inset-0 bg-cover bg-center transform will-change-transform"
                 :style="`background-image: url('${image}');`">
            </div>
        </template>
        
        {{-- Dark Overlay (z-10 agar di atas gambar) --}}
        <div class="absolute inset-0 bg-gradient-to-br from-black/70 via-black/50 to-black/70 z-10"></div>
        
        {{-- Content Container (z-20 agar paling atas dan bisa diklik) --}}
        <div class="relative z-20 min-h-screen flex items-center justify-center p-4">
            
            <div class="w-full max-w-6xl">
                
                {{-- Logo di pojok kiri atas (absolute positioning) --}}
                <div class="absolute top-8 left-8">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/10 backdrop-blur-md rounded-xl border border-white/20 flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('img/logo_kasau.png') }}" alt="Logo KASAU" class="w-10 h-10 object-contain">
                        </div>
                        <div class="text-white hidden sm:block">
                            <p class="font-bold text-lg leading-tight">KASAU</p>
                            <p class="text-xs opacity-80">Sinar Samudera</p>
                        </div>
                    </div>
                </div>
                
                {{-- Main Content Grid --}}
                <div class="grid md:grid-cols-2 gap-8 md:gap-12 items-center">
                    
                    {{-- Left Side: Welcome Text --}}
                    <div class="text-white space-y-6 hidden md:block">
                        <div>
                            <h1 class="text-5xl font-bold mb-4 leading-tight">
                                Selamat Datang<br>Kembali!
                            </h1>
                            <p class="text-lg text-white/80 leading-relaxed">
                                Sistem Absensi Karyawan<br>
                                <span class="text-brand-gold font-semibold">KASAU Sinar Samudera</span>
                            </p>
                        </div>
                        
                        <div class="pt-4 border-t border-white/20">
                            <p class="text-sm text-white/70">
                                Kelola kehadiran karyawan dengan mudah dan efisien
                            </p>
                        </div>
                    </div>
                    
                    {{-- Right Side: Login Form --}}
                    <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-8 md:p-10 shadow-2xl">
                        
                        {{-- Form Header --}}
                        <div class="mb-8">
                            <h2 class="text-3xl font-bold text-white mb-2">Masuk</h2>
                            <p class="text-white/70 text-sm">Masukkan email dan kata sandi yang benar untuk melanjutkan!</p>
                        </div>

                        {{-- Validation Errors --}}
                        @if ($errors->any())
                            <div x-data="{ show: true }" x-show="show" x-transition.opacity 
                                 class="mb-6 bg-red-500/10 backdrop-blur-sm border border-red-500/50 rounded-lg p-4 flex items-start animate-fadeIn">
                                <svg class="w-5 h-5 text-red-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-red-200">Gagal Masuk</h3>
                                    <p class="mt-1 text-xs text-red-300">
                                        Periksa kembali email dan kata sandi yang Anda masukkan!
                                    </p>
                                </div>
                                <button @click="show = false" class="text-red-400 hover:text-red-200 transition ml-2 p-0.5 hover:bg-red-500/20 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @endif
                        
                        {{-- Login Form --}}
                        <form method="POST" action="{{ route('login') }}" class="space-y-5">
                            @csrf
                            
                            {{-- Email Input --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-white/90 mb-2">
                                    Email
                                </label>
                                <input type="email" 
                                    id="email" 
                                    name="email" 
                                    required 
                                    autofocus
                                    class="glass-input w-full px-4 py-3 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-brand-gold/50 transition"
                                    placeholder="nama@email.com">
                            </div>
                            
                            {{-- Password Input --}}
                            <div x-data="{ showPassword: false }">
                                <label for="password" class="block text-sm font-medium text-white/90 mb-2">
                                    Kata Sandi
                                </label>
                                <div class="relative">
                                    <input :type="showPassword ? 'text' : 'password'" 
                                        id="password" 
                                        name="password" 
                                        required
                                        class="glass-input w-full px-4 py-3 pr-12 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-brand-gold/50 transition"
                                        placeholder="••••••••">
                                    
                                    {{-- Toggle Button --}}
                                    <button type="button"
                                        @click="showPassword = !showPassword"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-white/60 hover:text-white transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            {{-- Icon Mata Terbuka (password hidden) --}}
                                            <g x-show="!showPassword">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </g>
                                            {{-- Icon Mata dengan Garis (password visible) --}}
                                            <g x-show="showPassword" x-cloak>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0 1 12 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 0 1 1.563-3.029m5.858.908a3 3 0 1 1 4.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0 1 12 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 0 1-4.132 5.411m0 0L21 21"></path>
                                            </g>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            {{-- Remember Me --}}
                            <div class="flex items-center justify-between pt-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                        name="remember" 
                                        class="w-4 h-4 text-brand-gold bg-white/10 border-white/30 rounded focus:ring-brand-gold focus:ring-offset-0">
                                    <span class="ml-2 text-sm text-white/80">Ingat saya</span>
                                </label>
                            </div>
                            
                            {{-- Login Button --}}
                            <button type="submit" 
                                class="w-full bg-brand-maroon hover:bg-brand-maroon/90 text-white font-semibold py-3.5 px-4 rounded-lg transition-all shadow-lg shadow-brand-maroon/30 hover:shadow-brand-maroon/50 mt-6">
                                Masuk
                            </button>
                        </form>
                        
                        {{-- Footer --}}
                        <div class="mt-8 pt-6 border-t border-white/10 text-center">
                            <p class="text-xs text-white/50">
                                &copy; 2025 KASAU Sinar Samudera. All rights reserved.
                            </p>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</body>
</html>