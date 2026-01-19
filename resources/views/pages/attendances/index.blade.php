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
    showManualModal: false,
    activeTab: 'present', // 'present' or 'absent'
    selectedMonth: '',
    searchQuery: '{{ $reqSearch }}',
    filterType: '{{ $currentFilterType }}',
    startDate: '{{ $reqStartDate }}',
    endDate: '{{ $reqEndDate }}',
    
    // Edit Existing
    editAttendanceId: null,
    editStatus: '',
    editNote: '',
    
    // Manual Input (Absent User)
    manualUserId: null,
    manualUserName: '',
    manualDate: '',
    manualStatus: 'sick',
    manualNote: '',

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
    },
    
    openManualModal(userId, userName, date) {
        this.manualUserId = userId;
        this.manualUserName = userName;
        this.manualDate = date;
        this.manualStatus = 'alpha'; // default
        this.manualNote = '';
        this.showManualModal = true;
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

        {{-- Tabs for Single Day View --}}
        @isset($isSingleDay)
            @if($isSingleDay)
            <div class="border-b border-gray-200 bg-white px-6">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="activeTab = 'present'"
                            :class="activeTab === 'present' ? 'border-brand-maroon text-brand-maroon' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Hadir ({{ $attendances->total() }})
                    </button>

                    <button @click="activeTab = 'absent'"
                            :class="activeTab === 'absent' ? 'border-brand-maroon text-brand-maroon' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Belum Absen ({{ $absentUsers->count() }})
                    </button>
                </nav>
            </div>
            @endif
        @endisset

        {{-- Table --}}
        <div class="overflow-x-auto">
            {{-- Table: Present Users --}}
            <table class="w-full" x-show="activeTab === 'present'">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama Karyawan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Jam Datang</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($attendances as $attendance)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        @if($attendance->user->image_url)
                                            <img src="{{ asset('storage/' . $attendance->user->image_url) }}" alt="{{ $attendance->user->name }}" class="h-10 w-10 rounded-full object-cover">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-brand-gold/20 flex items-center justify-center text-brand-gold font-bold text-sm">
                                                {{ strtoupper(substr($attendance->user->name, 0, 2)) }}
                                            </div>
                                        @endif
                                    </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $attendance->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $attendance->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($attendance->date_attendance)->locale('id')->translatedFormat('d M Y') }}</div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($attendance->date_attendance)->locale('id')->translatedFormat('l') }}</div>
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
                            @php
                                $statusLabel = '-';
                                $badgeClass = 'bg-gray-100 text-gray-800';
                                $iconSvg = '';
                                
                                // Helper SVG
                                $checkIcon = '<svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
                                $clockIcon = '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                                $infoIcon = '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                                $xIcon = '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';

                                if ($attendance->time_in) {
                                    // Check explicit lateness flag first
                                    if (!$attendance->is_late) {
                                        $statusLabel = 'Tepat Waktu';
                                        $badgeClass = 'bg-green-100 text-green-800';
                                        $iconSvg = $checkIcon;
                                    } else {
                                        // It is marked as late, calculate degree based on time
                                        $timeIn = \Carbon\Carbon::parse($attendance->time_in);
                                        
                                        $tTimeIn = \Carbon\Carbon::parse($company->time_in ?? '08:00:00')->setDate($timeIn->year, $timeIn->month, $timeIn->day)->addSeconds(59);
                                        $tLate1  = \Carbon\Carbon::parse($company->late_threshold_1 ?? '08:30:00')->setDate($timeIn->year, $timeIn->month, $timeIn->day)->addSeconds(59);
                                        $tLate2  = \Carbon\Carbon::parse($company->late_threshold_2 ?? '09:00:00')->setDate($timeIn->year, $timeIn->month, $timeIn->day)->addSeconds(59);
                                        $tLate3  = \Carbon\Carbon::parse($company->late_threshold_3 ?? '12:00:00')->setDate($timeIn->year, $timeIn->month, $timeIn->day)->addSeconds(59);

                                        if ($timeIn->gt($tTimeIn) && $timeIn->lte($tLate1)) {
                                            $statusLabel = 'Terlambat 1';
                                            $badgeClass = 'bg-blue-100 text-blue-800';
                                            $iconSvg = $clockIcon;
                                        } elseif ($timeIn->gt($tLate1) && $timeIn->lte($tLate2)) {
                                            $statusLabel = 'Terlambat 2';
                                            $badgeClass = 'bg-yellow-100 text-yellow-800';
                                            $iconSvg = $clockIcon;
                                        } elseif ($timeIn->gt($tLate2) && $timeIn->lte($tLate3)) {
                                            $statusLabel = 'Terlambat 3';
                                            $badgeClass = 'bg-orange-200 text-orange-900';
                                            $iconSvg = $clockIcon;
                                        } elseif ($timeIn->gt($tLate3)) {
                                            $statusLabel = 'Setengah Hari';
                                            $badgeClass = 'bg-red-100 text-red-800';
                                            $iconSvg = $clockIcon;
                                        } else {
                                            $statusLabel = 'Terlambat';
                                            $badgeClass = 'bg-gray-100 text-gray-800';
                                            $iconSvg = $clockIcon;
                                        }
                                    }
                                } else {
                                    // Status Only (No time_in)
                                    $redBadge = 'bg-red-600 text-white';

                                    if ($attendance->status == 'sick') {
                                        $statusLabel = 'Sakit';
                                        $badgeClass = $redBadge;
                                        $iconSvg = $infoIcon;
                                    } elseif ($attendance->status == 'permission') {
                                        $statusLabel = 'Izin';
                                        $badgeClass = $redBadge;
                                        $iconSvg = $infoIcon;
                                    } elseif ($attendance->status == 'alpha') {
                                        $statusLabel = 'Alpha';
                                        $badgeClass = $redBadge;
                                        $iconSvg = $xIcon;
                                    } elseif ($attendance->status == 'out_of_town') {
                                        $statusLabel = 'Dinas Luar Kota';
                                        $badgeClass = $redBadge;
                                        $iconSvg = $infoIcon;
                                    } elseif ($attendance->status == 'leave') {
                                        $statusLabel = 'Cuti';
                                        $badgeClass = $redBadge;
                                        $iconSvg = $infoIcon;
                                    }
                                }
                            @endphp

                            @if($statusLabel != '-')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">
                                    {!! $iconSvg !!}
                                    {{ $statusLabel }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($attendance->note)
                                <div class="max-w-xs mx-auto">
                                    <div class="inline-flex items-start gap-1.5 text-xs text-gray-600 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                                        <svg class="w-3.5 h-3.5 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                        </svg>
                                        <span class="text-left">{{ Str::limit($attendance->note, 50) }}</span>
                                    </div>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if(auth()->user()->hasRole(['admin', 'receptionist']) && $attendance->time_in)
                                @php
                                    // Determine detailed status for edit modal
                                    $editStatusVal = $attendance->status ?? ($attendance->is_late ? 'late' : 'ontime');
                                    // Fix for older records or default 'present' without is_late details
                                    if ($editStatusVal == 'present') {
                                        $editStatusVal = $attendance->is_late ? 'late' : 'ontime';
                                    }
                                @endphp
                                <button @click="editAttendanceId = {{ $attendance->id }}; editStatus = '{{ $editStatusVal }}'; editNote = '{{ addslashes($attendance->note ?? '') }}'; showEditModal = true"
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

            {{-- Table: Absent Users (Only visible in Single Day view) --}}
            @isset($isSingleDay)
            @if($isSingleDay)
            <table class="w-full" x-show="activeTab === 'absent'" style="display: none;">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama Karyawan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($absentUsers as $absentUser)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    @if($absentUser->image_url)
                                        <img src="{{ asset('storage/' . $absentUser->image_url) }}" alt="{{ $absentUser->name }}" class="h-10 w-10 rounded-full object-cover">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-sm">
                                            {{ strtoupper(substr($absentUser->name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $absentUser->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $absentUser->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            {{-- Visual Default Status --}}
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Alpa
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if(auth()->user()->hasRole(['admin', 'receptionist']))
                                <button @click="openManualModal({{ $absentUser->id }}, '{{ addslashes($absentUser->name) }}', '{{ $targetDate }}')"
                                        class="inline-flex items-center px-3 py-1.5 bg-brand-maroon hover:bg-brand-maroon/90 text-white text-xs font-medium rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </button>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                            Semua karyawan sudah absen hari ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @endif
            @endisset

        {{-- Pagination --}}
        @if($attendances->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-white" x-show="activeTab === 'present'">
            {{ $attendances->links('vendor.pagination.custom') }}
        </div>
        @endif
    </div>
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
                                    
                                    {{-- Keterangan / Alasan --}}
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Keterangan / Alasan
                                        </label>
                                        <textarea name="note" 
                                                  x-model="editNote"
                                                  rows="3" 
                                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-maroon focus:ring focus:ring-brand-maroon/20 text-sm"
                                                  placeholder="Contoh: Izin terlambat karena ada tugas di lapangan..."></textarea>
                                        <p class="text-xs text-gray-500 mt-1">Tambahkan keterangan jika diperlukan, misalnya alasan keterlambatan.</p>
                                    </div>
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

    {{-- Manual Attendance Modal --}}
    <div x-show="showManualModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="manual-modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
             <div x-show="showManualModal"
                 @click="showManualModal = false"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="showManualModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                
                 <form action="{{ route('attendance.storeManual') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-brand-maroon/10 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-brand-maroon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="manual-modal-title">
                                    Catat Kehadiran (Manual)
                                </h3>
                                <div class="mt-2 mb-4">
                                    <p class="text-sm text-gray-500">Mencatat status untuk <span class="font-bold text-gray-800" x-text="manualUserName"></span> pada tanggal <span class="font-bold" x-text="manualDate"></span>.</p>
                                </div>
                                
                                <input type="hidden" name="user_id" x-model="manualUserId">
                                <input type="hidden" name="date_attendance" x-model="manualDate">

                                <div class="space-y-3">
                                    <label class="block text-sm font-medium text-gray-700">Pilih Status</label>
                                    
                                     {{-- Dinas Luar Kota --}}
                                    <label class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                            :class="manualStatus === 'out_of_town' ? 'border-purple-500 bg-purple-50' : 'border-gray-300'">
                                        <input type="radio" name="status" value="out_of_town" x-model="manualStatus" class="h-4 w-4 text-purple-600 border-gray-300">
                                        <div class="ml-3">
                                            <span class="block text-sm font-medium text-gray-900">Dinas Luar Kota</span>
                                        </div>
                                    </label>
                                    
                                    {{-- Izin --}}
                                    <label class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                            :class="manualStatus === 'permission' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-300'">
                                        <input type="radio" name="status" value="permission" x-model="manualStatus" class="h-4 w-4 text-yellow-600 border-gray-300">
                                        <div class="ml-3">
                                            <span class="block text-sm font-medium text-gray-900">Izin</span>
                                        </div>
                                    </label>
                                    
                                    {{-- Sakit --}}
                                    <label class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                            :class="manualStatus === 'sick' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                                        <input type="radio" name="status" value="sick" x-model="manualStatus" class="h-4 w-4 text-blue-600 border-gray-300">
                                        <div class="ml-3">
                                            <span class="block text-sm font-medium text-gray-900">Sakit</span>
                                        </div>
                                    </label>

                                    {{-- Cuti --}}
                                    <label class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                            :class="manualStatus === 'leave' ? 'border-teal-500 bg-teal-50' : 'border-gray-300'">
                                        <input type="radio" name="status" value="leave" x-model="manualStatus" class="h-4 w-4 text-teal-600 border-gray-300">
                                        <div class="ml-3">
                                            <span class="block text-sm font-medium text-gray-900">Cuti</span>
                                        </div>
                                    </label>

                                    {{-- Alpa  --}}
                                    <label class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                            :class="manualStatus === 'alpha' ? 'border-red-500 bg-red-50' : 'border-gray-300'">
                                        <input type="radio" name="status" value="alpha" x-model="manualStatus" class="h-4 w-4 text-red-600 border-gray-300">
                                        <div class="ml-3">
                                            <span class="block text-sm font-medium text-gray-900">Alpa</span>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan</label>
                                    <textarea name="note" x-model="manualNote" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:ring-brand-maroon focus:border-brand-maroon" placeholder="Opsional..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-brand-maroon text-base font-medium text-white hover:bg-brand-maroon/90 sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
                        <button type="button" @click="showManualModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>
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
