<div class="fixed bottom-0 left-0 z-50 w-full h-16 bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] pb-safe">
    <div class="h-full max-w-lg mx-auto relative flex justify-between items-center px-6">
        
        {{-- 1. Home --}}
        <a href="{{ route('home') }}" 
           class="flex flex-col items-center justify-center min-w-[60px] h-full {{ request()->routeIs('home') ? 'text-brand-maroon' : 'text-gray-400 hover:text-brand-maroon' }}">
            <div class="p-1.5 rounded-xl {{ request()->routeIs('home') ? 'bg-brand-cream/50' : '' }}">
                @if(request()->routeIs('home'))
                    <!-- Solid Icon (Active) -->
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" />
                        <path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z" />
                    </svg>
                @else
                    <!-- Outline Icon (Inactive) -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                @endif
            </div>
            <span class="text-[10px] font-medium mt-1">Beranda</span>
        </a>

        {{-- 2. SCAN (Floating Button - Center Absolute) --}}
        {{-- 2. ACTION BUTTON (Floating) --}}
        <div class="absolute left-1/2 transform -translate-x-1/2 -top-6">
            @if(auth()->user()->hasRole('resepsionis'))
                {{-- GENERATE QR (RESEPSIONIS) --}}
                <a href="{{ route('attendance.create-qr') }}" 
                   class="flex items-center justify-center w-16 h-16 bg-brand-maroon text-white rounded-full shadow-lg border-4 border-white transition-transform active:scale-95 hover:bg-red-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" />
                    </svg>
                </a>
                <span class="absolute -bottom-5 left-1/2 transform -translate-x-1/2 text-[10px] font-medium text-gray-500 whitespace-nowrap">Absensi</span>
            @else
                {{-- SCAN QR (KARYAWAN) --}}
                <a href="{{ route('attendance.scan') }}" 
                   class="flex items-center justify-center w-16 h-16 bg-brand-maroon text-white rounded-full shadow-lg border-4 border-white transition-transform active:scale-95 hover:bg-red-800">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" />
                    </svg>
                </a>
                <span class="absolute -bottom-6 left-1/2 transform -translate-x-1/2 text-[10px] font-medium text-gray-500 whitespace-nowrap">Pindai</span>
            @endif
        </div>

        {{-- 3. Profil --}}
        <a href="{{ route('profile.index') }}" 
           class="flex flex-col items-center justify-center min-w-[60px] h-full {{ request()->routeIs('profile.*') ? 'text-brand-maroon' : 'text-gray-400 hover:text-brand-maroon' }}">
            <div class="p-1.5 rounded-xl {{ request()->routeIs('profile.*') ? 'bg-brand-cream/50' : '' }}">
                <svg class="w-6 h-6 {{ request()->routeIs('profile.*') ? 'fill-current' : 'stroke-current fill-none' }}" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <span class="text-[10px] font-medium mt-1">Profil</span>
        </a>

    </div>
</div>
