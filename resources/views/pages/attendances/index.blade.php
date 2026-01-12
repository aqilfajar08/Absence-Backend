@extends('layouts.app')

@section('title', 'Laporan - Absensi Karyawan')

@section('content')
@php
    $reqStartDate = request('start_date');
    $reqEndDate = request('end_date');
    $reqSearch = request('search');
    $today = date('Y-m-d');
    
    $currentFilterType = 'all';
    if ($reqStartDate == $today && $reqEndDate == $today) {
        $currentFilterType = 'today';
    } elseif (!empty($reqStartDate) && !empty($reqEndDate)) {
        $currentFilterType = 'custom';
    }
@endphp
<div x-data="{ 
    showDeleteModal: false,
    showEditModal: false,
    selectedMonth: '',
    searchQuery: '{{ $reqSearch }}',
    filterType: '{{ $currentFilterType }}',
    startDate: '{{ $reqStartDate }}',
    endDate: '{{ $reqEndDate }}',
    editAttendanceId: null,
    editStatus: '',
    updateFilter() {
        if (this.filterType === 'today') {
            const today = new Date().toISOString().split('T')[0];
            this.startDate = today;
            this.endDate = today;
            this.$nextTick(() => this.$refs.filterForm.submit());
        } else if (this.filterType === 'all') {
            this.startDate = '';
            this.endDate = '';
            this.searchQuery = '';
            this.$nextTick(() => this.$refs.filterForm.submit());
        }
    }
}">
    {{-- Page Header Card --}}
    <div class="mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-black/5 p-6 flex items-start justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Laporan Absensi
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Kelola dan pantau data kehadiran karyawan
                </p>
            </div>
            <button @click="showDeleteModal = true"
                    class="inline-flex items-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Hapus Data Bulanan
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Absensi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $attendances->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $attendances->where('date_attendance', date('Y-m-d'))->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $attendances->where('date_attendance', '>=', date('Y-m-01'))->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Halaman</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $attendances->currentPage() }}/{{ $attendances->lastPage() }}</p>
                </div>
            </div>
        </div>
    </div>

        {{-- Alerts --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 flex items-start justify-between">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="text-green-500 hover:text-green-700 transition ml-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 flex items-start justify-between">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
        </div>
        <button @click="show = false" class="text-red-500 hover:text-red-700 transition ml-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    @endif

    {{-- Table Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <form action="{{ route('attendance.index') }}" method="GET" x-ref="filterForm" class="flex flex-col lg:flex-row lg:items-center gap-4">
                {{-- Search --}}
                <div class="flex-1 max-w-md relative">
                    <input type="text" 
                           name="search"
                           x-model="searchQuery"
                           placeholder="Cari nama karyawan..."
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                {{-- Filter Options --}}
                <div class="flex flex-col sm:flex-row gap-3">
                    <select x-model="filterType"
                            @change="updateFilter()"
                            class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition bg-white min-w-[140px]">
                        <option value="all">Semua Hari</option>
                        <option value="today">Hari Ini</option>
                        <option value="custom">Custom</option>
                    </select>

                    <template x-if="filterType === 'custom'">
                        <div class="flex items-center gap-2 animate-fadeIn">
                            <input type="date" 
                                   x-model="startDate"
                                   class="px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition text-sm">
                            <span class="text-gray-500 font-medium">-</span>
                            <input type="date" 
                                   x-model="endDate"
                                   class="px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition text-sm">
                            
                            <button type="submit"
                                    class="px-4 py-2.5 bg-brand-maroon text-white font-medium rounded-lg hover:bg-brand-maroon/90 transition shadow-sm">
                                Filter
                            </button>
                        </div>
                    </template>
                    
                    {{-- Hidden inputs for form submission --}}
                    <input type="hidden" name="start_date" x-model="startDate">
                    <input type="hidden" name="end_date" x-model="endDate">
                </div>

                <div class="ml-auto">
                    {{-- Tombol Export CSV --}}
                    {{-- request()->query() akan otomatis membawa filter (tanggal/search) yang sedang aktif ke link export --}}
                    <a href="{{ route('attendance.export', request()->query()) }}" target="_blank"
                    class="inline-flex items-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-medium shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Ekspor ke Excel
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama Karyawan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Jam Datang</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Denda</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($attendances as $attendance)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-brand-gold/20 flex items-center justify-center text-brand-gold font-bold text-sm">
                                            {{ strtoupper(substr($attendance->user->name, 0, 2)) }}
                                        </div>
                                    </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $attendance->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $attendance->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($attendance->date_attendance)->format('d M Y') }}</div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($attendance->date_attendance)->format('l') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($attendance->time_in)
                            <div class="text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($attendance->time_in)->format('H:i') }}
                            </div>
                            @else
                            <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($attendance->time_in && $attendance->is_late)
                                @php
                                    $lateFee = 0;
                                    $minutesLate = 0;
                                    if($attendance->is_late && $attendance->time_in) {
                                        $timeIn = \Carbon\Carbon::parse($attendance->time_in);
                                        $cutoffTime = \Carbon\Carbon::parse($company->time_in ?? '09:00:00');
                                        
                                        // Samakan tanggal agar hanya membandingkan JAM
                                        $cutoffTime->setDate($timeIn->year, $timeIn->month, $timeIn->day);

                                        // Jika Absen LEBIH DARI Jam Masuk -> BENAR TERLAMBAT
                                        if ($timeIn->gt($cutoffTime)) {
                                            $minutesLate = ceil($timeIn->diffInMinutes($cutoffTime));
                                        } else {
                                            // Jika Absen <= Jam Masuk TAPI status 'Late' (Data aneh/manual)
                                            // Kita anggap 0 menit saja biar ga minus
                                            $minutesLate = 0;
                                        }

                                        // --- UPDATE TERAKHIR: PAKSA ABSOLUT BIAR GAK MINUS APAPUN YANG TERJADI ---
                                        $minutesLate = abs($minutesLate); 

                                        if ($minutesLate > 0) {
                                            $interval = max(1, $company->late_fee_interval_minutes ?? 1); // Hindari division by zero
                                            $feePerInterval = $company->late_fee_per_minute ?? 1000;
                                            
                                            $intervals = ceil($minutesLate / $interval);
                                            $lateFee = $intervals * $feePerInterval;
                                        }
                                    }
                                @endphp
                                <div class="text-sm">
                                    <div class="font-semibold text-red-600">Rp {{ number_format(abs($lateFee), 0, ',', '.') }}</div>
                                    <div class="text-xs text-gray-500">Terlambat {{ abs($minutesLate) }} menit</div>
                                </div>
                            @else
                                <span class="text-sm text-gray-500">Rp 0</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($attendance->time_in)
                                @if($attendance->is_late)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Terlambat
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Tepat Waktu
                                    </span>
                                @endif
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if(auth()->user()->hasRole(['admin', 'receptionist']))
                                <button @click="editAttendanceId = {{ $attendance->id }}; editStatus = '{{ $attendance->is_late ? 'late' : 'ontime' }}'; showEditModal = true"
                                        class="inline-flex items-center px-3 py-1.5 bg-brand-maroon hover:bg-brand-maroon/90 text-white text-xs font-medium rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </button>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
                            <p class="mt-1 text-sm text-gray-500">Belum ada data absensi yang tercatat.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

        {{-- Pagination --}}
        @if($attendances->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-white">
            {{ $attendances->links('vendor.pagination.custom') }}
        </div>
        @endif
    </div>

    {{-- Delete Modal --}}
    <div x-show="showDeleteModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background overlay --}}
            <div x-show="showDeleteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showDeleteModal = false"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            {{-- Modal panel --}}
            <div x-show="showDeleteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                
                <form action="{{ route('attendance.deleteByMonth') }}" method="POST" id="deleteForm">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Hapus Data Absensi Bulanan
                                </h3>
                                <div class="mt-4">
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                                        <p class="text-sm text-red-800">
                                            <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan. Semua data absensi untuk bulan yang dipilih dari SEMUA pengguna akan dihapus secara permanen.
                                        </p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="deleteMonth" class="block text-sm font-medium text-gray-700 mb-2">
                                            Pilih Bulan yang Akan Dihapus
                                        </label>
                                        <input type="month" 
                                               x-model="selectedMonth"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                                               id="deleteMonth" 
                                               name="month" 
                                               required 
                                               min="2020-01" 
                                               max="2030-12">
                                        <p class="mt-2 text-xs text-gray-500">Format: YYYY-MM (contoh: 2025-12 untuk Desember 2025)</p>
                                    </div>
                                    
                                    <p class="text-sm text-gray-600">
                                        Ini akan menghapus <strong>SEMUA data absensi</strong> untuk bulan yang dipilih dari <strong>SEMUA pengguna</strong>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                        <button type="button"
                        @click="confirmDelete()"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus Semua Data
                        </button>
                        <button type="button"
                            @click="showDeleteModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-maroon sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Status Modal --}}
    <div x-show="showEditModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="edit-modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background overlay --}}
            <div x-show="showEditModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showEditModal = false"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            {{-- Modal panel --}}
            <div x-show="showEditModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                
                <form :action="`/attendance/${editAttendanceId}/update-status`" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-brand-maroon/10 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-brand-maroon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="edit-modal-title">
                                    Edit Status Kehadiran
                                </h3>
                                <div class="mt-4">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">
                                            Pilih Status Kehadiran
                                        </label>
                                        <div class="space-y-3">
                                            <label class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                                   :class="editStatus === 'ontime' ? 'border-green-500 bg-green-50' : 'border-gray-300'">
                                                <input type="radio" 
                                                       name="status" 
                                                       value="ontime" 
                                                       x-model="editStatus"
                                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                                <div class="ml-3 flex items-center">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Tepat Waktu
                                                    </span>
                                                </div>
                                            </label>
                                            
                                            <label class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                                   :class="editStatus === 'late' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-300'">
                                                <input type="radio" 
                                                       name="status" 
                                                       value="late" 
                                                       x-model="editStatus"
                                                       class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300">
                                                <div class="ml-3 flex items-center">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Terlambat
                                                    </span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <p class="text-sm text-gray-500 mt-3">
                                        Ubah status kehadiran karyawan sesuai dengan kondisi aktual.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                        <button type="submit"
                                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-brand-maroon text-base font-medium text-white hover:bg-brand-maroon/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-maroon sm:ml-3 sm:w-auto sm:text-sm transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Perubahan
                        </button>
                        <button type="button"
                                @click="showEditModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-maroon sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>
@endsection
@push('scripts')
<script>
    // ... script flatpickr sebelumnya ...

    function confirmDelete() {
        // Ambil nilai bulan yang dipilih
        const monthInput = document.getElementById('deleteMonth');
        if (!monthInput.value) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Bulan',
                text: 'Silakan pilih bulan yang ingin dihapus datanya terlebih dahulu.',
                confirmButtonColor: '#9f1239' // brand-maroon
            });
            return;
        }

        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Semua data absensi pada bulan terpilih akan dihapus PERMANEN dan tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading saat proses hapus
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang menghapus data...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit form secara manual
                document.getElementById('deleteForm').submit();
            }
        });
    }
</script>
@endpush
