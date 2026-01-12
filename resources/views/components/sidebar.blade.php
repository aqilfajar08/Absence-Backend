{{-- Sidebar Navigation --}}
<nav class="mt-5 flex-1 px-2 space-y-1">
    
    {{-- 1. Dashboard Menu --}}
    <a href="{{ route('home') }}" 
       class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200
              {{ request()->routeIs('home') 
                  ? 'bg-white bg-opacity-20 text-white' 
                  : 'text-white hover:bg-white hover:bg-opacity-10' }}">
        {{-- Icon Dashboard --}}
        <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>

    {{-- 2. Laporan Menu --}}
    <a href="{{ route('attendance.index') }}" 
       class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200
              {{ request()->routeIs('attendance.*') 
                  ? 'bg-white bg-opacity-20 text-white' 
                  : 'text-white hover:bg-white hover:bg-opacity-10' }}">
        {{-- Icon Laporan --}}
        <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Laporan
    </a>

    {{-- 3. Data Karyawan Menu --}}
    <a href="{{ route('user.index') }}" 
       class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200
              {{ request()->routeIs('user.*') 
                  ? 'bg-white bg-opacity-20 text-white' 
                  : 'text-white hover:bg-white hover:bg-opacity-10' }}">
        {{-- Icon Data Karyawan --}}
        <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        Data Karyawan
    </a>

    {{-- 4. Pengaturan Menu --}}
    <a href="{{ route('company.edit', 1) }}" 
       class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200
              {{ request()->routeIs('company.*') 
                  ? 'bg-white bg-opacity-20 text-white' 
                  : 'text-white hover:bg-white hover:bg-opacity-10' }}">
        {{-- Icon Pengaturan --}}
        <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
        </svg>
        Pengaturan
    </a>
    </nav>
    {{-- Profile (Bottom Sidebar) --}}
    <div class="mt-auto px-4 pt-4" x-data="{ showLogoutModal: false }">
        <div class="rounded-xl bg-white/10 border border-white/10 p-3 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-brand-gold flex items-center justify-center text-white font-semibold">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>

            <div class="min-w-0">
                <p class="text-sm font-semibold text-white truncate">
                    {{ auth()->user()->name ?? 'User' }}
                </p>
                <p class="text-xs text-white/70 truncate">
                    {{ ucfirst(auth()->user()->getRoleNames()->first() ?? 'User') }}
                </p>
            </div>
        </div>
        <button @click="showLogoutModal = true" class="w-full rounded-lg bg-white/10 hover:bg-white/15 text-white text-sm py-2 transition mt-3">
            Keluar 
        </button>
        {{-- Modal Konfirmasi Logout --}}
        <template x-teleport="body">
            <div x-show="showLogoutModal"
                x-cloak
                @click.self="showLogoutModal = false"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm"
                style="display: none;">
                
                <div @click.away="showLogoutModal = false"
                    x-show="showLogoutModal"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="bg-white rounded-2xl shadow-2xl max-w-sm w-full mx-4 overflow-hidden">
                    
                    {{-- Header --}}
                    <div class="bg-brand-maroon px-6 py-4">
                        <h3 class="text-lg font-bold text-white">Konfirmasi Keluar</h3>
                    </div>

                    {{-- Body --}}
                    <div class="px-6 py-5">
                        <p class="text-gray-700">
                            Apakah anda yakin ingin keluar dari sistem?
                        </p>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 flex gap-3 justify-end">
                        <button @click="showLogoutModal = false"
                                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                            Batal
                        </button>

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="px-4 py-2 rounded-lg bg-brand-maroon text-white hover:bg-brand-maroon/90 transition">
                                Ya
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
</nav>
