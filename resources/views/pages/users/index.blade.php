@extends('layouts.app')

@section('title', 'Data Karyawan - Absensi Karyawan')

@section('content')
<div class="space-y-6">

    {{-- Header & Actions --}}
    <div class="bg-white rounded-2xl shadow-sm border border-black/5 p-6 mb-6">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Data Karyawan</h2>
                <p class="mt-1 text-sm text-gray-600">Kelola data seluruh karyawan dan role.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row gap-3">
                {{-- Search Form --}}
                <form action="{{ route('user.index') }}" method="GET" class="relative">
                    <input type="text" 
                           name="name" 
                           value="{{ request('name') }}"
                           placeholder="Cari karyawan..." 
                           class="w-full sm:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition text-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </form>



                <a href="{{ route('user.create') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 bg-brand-maroon text-white rounded-lg hover:bg-brand-maroon/90 transition text-sm font-medium shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Karyawan
                </a>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center justify-between">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="text-green-500 hover:text-green-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    @endif

    {{-- Users Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-black/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama & Email</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-brand-gold/20 flex items-center justify-center text-brand-gold font-bold text-sm">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($user->getRoleNames()->isNotEmpty())
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($user->getRoleNames()->first()) }}
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    User
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center space-x-3">
                                <a href="{{ route('user.edit', $user->id) }}" class="text-yellow-500 hover:text-yellow-600 transition" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                {{-- Hide Delete for Admins or Self --}}
                                @if(!$user->hasRole('admin') && auth()->id() !== $user->id)
                                <form action="{{ route('user.destroy', $user->id) }}" method="POST" class="inline-block delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                            Tidak ada data karyawan ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    </div>
</div>

{{-- Export Modal (Alpine.js) --}}
<div x-data="{ open: false }" 
     @open-modal.window="if ($event.detail === 'exportModal') open = true" 
     x-show="open" 
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;">
    
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="open = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('user.export.attendance') }}" method="GET">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Ekspor Laporan Absensi
                            </h3>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Bulan</label>
                                <select name="selected_month" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-brand-maroon focus:border-brand-maroon sm:text-sm">
                                    @php
                                        $currentMonth = \Carbon\Carbon::now();
                                        for ($i = -12; $i <= 1; $i++) {
                                            $month = $currentMonth->copy()->addMonths($i);
                                            echo '<option value="'.$month->format('Y-m').'" '.($i===0?'selected':'').'>'.$month->translatedFormat('F Y').($i===0?' (Bulan Ini)':'').'</option>';
                                        }
                                    @endphp
                                </select>
                                <p class="mt-2 text-xs text-gray-500">
                                    File Excel akan diunduh. Jika diblokir browser, pilih "Tetap Download" (Keep).
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Unduh Excel
                    </button>
                    <button type="button" @click="open = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/features-posts.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // SweetAlert for Delete Confirmation
            const deleteForms = document.querySelectorAll('.delete-form');
            
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // Prevent default submission

                    Swal.fire({
                        title: 'Hapus Karyawan?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33', // Brand maroon/red
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit(); // Submit the form if confirmed
                        }
                    });
                });
            });
        });
    </script>
@endpush
