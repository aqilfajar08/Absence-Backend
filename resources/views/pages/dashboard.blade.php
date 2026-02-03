@extends('layouts.app')

@section('title', 'Dashboard - Absensi Karyawan')

@section('page-title', 'Dashboard')

@section('content')
@if(auth()->user()->hasRole('admin'))
    <div class="space-y-6">
        <!-- Header Dashboard Admin -->
        <div class="bg-white rounded-2xl shadow-sm border border-black/5 p-6 flex items-start justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Selamat Datang, {{ auth()->user()->name ?? 'User' }}!
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Semoga harimu menyenangkan! Berikut ringkasan absensi hari ini.
                </p>
            </div>

            <div class="hidden sm:block" x-data="{ time: new Date() }" x-init="setInterval(() => time = new Date(), 1000)">
                <div class="bg-brand-maroon text-white px-4 py-2 rounded-xl text-right shadow-lg shadow-brand-maroon/20 min-w-[120px]">
                    <div class="text-[10px] uppercase tracking-wider font-medium opacity-80 border-b border-white/20 pb-1 mb-1">
                        {{ now()->translatedFormat('l, d M Y') }}
                    </div>
                    <div class="flex items-baseline justify-end gap-1">
                        <span class="text-xl font-bold font-mono tracking-tight" x-text="time.toLocaleTimeString('id-ID', {timeZone: 'Asia/Makassar', hour12: false})"></span>
                        <span class="text-[10px] font-medium opacity-70">WITA</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Kebijakan Denda --}}
        @if(isset($company))
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-green-100 p-2 rounded-lg text-green-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Kebijakan Potongan Keterlambatan</h3>
                        <p class="text-xs text-gray-500">Tiering potongan GPH berdasarkan waktu kehadiran</p>
                    </div>
                </div>
                <a href="{{ route('company.edit', 1) }}" class="text-xs font-semibold text-brand-maroon hover:text-brand-maroon/80 hover:underline">
                    Ubah Pengaturan &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                {{-- Tier 1 --}}
                <div class="p-3 bg-green-50 border border-green-100 rounded-lg flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            <span class="text-sm font-bold text-green-900">Terlambat 1</span>
                        </div>
                        <div class="text-xs text-green-700 font-medium bg-white/60 px-2 py-1 rounded inline-block">
                            {{ \Carbon\Carbon::parse($company->time_in)->addMinute()->format('H:i') }} - {{ \Carbon\Carbon::parse($company->late_threshold_1)->format('H:i') }}
                        </div>
                    </div>
                    <div class="mt-3 pt-2 border-t border-green-200/60 flex justify-between items-center">
                        <span class="text-xs text-green-600">Potongan GPH</span>
                        <span class="text-sm font-bold text-green-800">{{ 100 - ($company->gph_late_1_percent ?? 75) }}%</span>
                    </div>
                </div>

                {{-- Tier 2 --}}
                <div class="p-3 bg-yellow-50 border border-yellow-100 rounded-lg flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                            <span class="text-sm font-bold text-yellow-900">Terlambat 2</span>
                        </div>
                        <div class="text-xs text-yellow-800 font-medium bg-white/60 px-2 py-1 rounded inline-block">
                            {{ \Carbon\Carbon::parse($company->late_threshold_1)->addMinute()->format('H:i') }} - {{ \Carbon\Carbon::parse($company->late_threshold_2)->format('H:i') }}
                        </div>
                    </div>
                    <div class="mt-3 pt-2 border-t border-yellow-200/60 flex justify-between items-center">
                        <span class="text-xs text-yellow-700">Potongan GPH</span>
                        <span class="text-sm font-bold text-yellow-900">{{ 100 - ($company->gph_late_2_percent ?? 70) }}%</span>
                    </div>
                </div>

                {{-- Tier 3 --}}
                <div class="p-3 bg-orange-50 border border-orange-100 rounded-lg flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                            <span class="text-sm font-bold text-orange-900">Terlambat 3</span>
                        </div>
                        <div class="text-xs text-orange-800 font-medium bg-white/60 px-2 py-1 rounded inline-block">
                            {{ \Carbon\Carbon::parse($company->late_threshold_2)->addMinute()->format('H:i') }} - {{ \Carbon\Carbon::parse($company->late_threshold_3)->format('H:i') }}
                        </div>
                    </div>
                    <div class="mt-3 pt-2 border-t border-orange-200/60 flex justify-between items-center">
                        <span class="text-xs text-orange-700">Potongan GPH</span>
                        <span class="text-sm font-bold text-orange-900">{{ 100 - ($company->gph_late_3_percent ?? 65) }}%</span>
                    </div>
                </div>

                {{-- Tier 4 --}}
                <div class="p-3 bg-red-50 border border-red-100 rounded-lg flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            <span class="text-sm font-bold text-red-900">Setengah Hari</span>
                        </div>
                        <div class="text-xs text-red-800 font-medium bg-white/60 px-2 py-1 rounded inline-block">
                            > {{ \Carbon\Carbon::parse($company->late_threshold_3)->format('H:i') }}
                        </div>
                    </div>
                    <div class="mt-3 pt-2 border-t border-red-200/60 flex justify-between items-center">
                        <span class="text-xs text-red-700">Potongan GPH</span>
                        <span class="text-sm font-bold text-red-900">{{ 100 - ($company->gph_late_4_percent ?? 0) }}%</span>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        {{-- Stats Cards & Table (ADMIN) --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Card 1: Total Karyawan --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                            <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Karyawan</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">{{ $totalEmployees }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
    
            {{-- Card 2: Hadir Hari Ini --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Hadir</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">{{ $totalPresent }}</div>
                                    <div class="ml-2 text-sm text-gray-500">/ {{ $totalEmployees }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
    
            {{-- Card 3: Terlambat --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                            <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Terlambat</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">{{ $totalLate }}</div>
                                    <div class="ml-2 text-sm text-gray-500">orang</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
    
            {{-- Card 4: Tidak Hadir --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-100 rounded-lg p-3">
                            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Tidak Hadir</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">{{ $totalAbsent }}</div>
                                    <div class="ml-2 text-sm text-gray-500">orang</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        {{-- Recent Activity Table --}}
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Karyawan</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Datang</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($latestActivities as $activity)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        @if($activity->user->image_url)
                                            <img class="h-10 w-10 rounded-full object-cover border border-gray-200" 
                                                 src="{{ asset('storage/' . $activity->user->image_url) }}" 
                                                 alt="{{ $activity->user->name }}">
                                        @else
                                            {{-- Initials Avatar --}}
                                            <div class="h-10 w-10 rounded-full bg-brand-maroon/10 flex items-center justify-center">
                                                <span class="text-brand-maroon font-semibold">{{ strtoupper(substr($activity->user->name ?? 'U', 0, 2)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $activity->user->name ?? 'Unknown' }}</div>
                                        <div class="text-sm text-gray-500">{{ $activity->user->department ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $activity->time_in ? \Carbon\Carbon::parse($activity->time_in)->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                @if($activity->time_in)
                                    @php
                                        // Check manual override flag first
                                        if (!$activity->is_late) {
                                            // Manually set to "Tepat Waktu"
                                            $statusDisplay = 'tepat_waktu';
                                        } else {
                                            // Calculate lateness level based on time
                                            $timeIn = \Carbon\Carbon::parse($activity->time_in);
                                            $company = \App\Models\Company::first();
                                            $standardTime = \Carbon\Carbon::parse($company->time_in ?? '08:00')->setDate($timeIn->year, $timeIn->month, $timeIn->day)->addSeconds(59);
                                            $late1 = \Carbon\Carbon::parse($company->late_threshold_1 ?? '08:30')->setDate($timeIn->year, $timeIn->month, $timeIn->day)->addSeconds(59);
                                            $late2 = \Carbon\Carbon::parse($company->late_threshold_2 ?? '09:00')->setDate($timeIn->year, $timeIn->month, $timeIn->day)->addSeconds(59);
                                            $late3 = \Carbon\Carbon::parse($company->late_threshold_3 ?? '12:00')->setDate($timeIn->year, $timeIn->month, $timeIn->day)->addSeconds(59);
                                            
                                            if ($timeIn->gt($standardTime) && $timeIn->lte($late1)) {
                                                $statusDisplay = 'terlambat_1';
                                            } elseif ($timeIn->gt($late1) && $timeIn->lte($late2)) {
                                                $statusDisplay = 'terlambat_2';
                                            } elseif ($timeIn->gt($late2) && $timeIn->lte($late3)) {
                                                $statusDisplay = 'terlambat_3';
                                            } elseif ($timeIn->gt($late3)) {
                                                $statusDisplay = 'setengah_hari';
                                            } else {
                                                $statusDisplay = 'tepat_waktu';
                                            }
                                        }
                                    @endphp
                                    
                                    @if($statusDisplay == 'tepat_waktu')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Tepat Waktu
                                        </span>
                                    @elseif($statusDisplay == 'terlambat_1')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Terlambat 1
                                        </span>
                                    @elseif($statusDisplay == 'terlambat_2')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Terlambat 2
                                        </span>
                                    @elseif($statusDisplay == 'terlambat_3')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Terlambat 3
                                        </span>
                                    @elseif($statusDisplay == 'setengah_hari')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Setengah Hari
                                        </span>
                                    @endif
                                @elseif($activity->status == 'permission')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Izin
                                    </span>
                                @elseif($activity->status == 'sick')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                        Sakit
                                    </span>
                                @elseif($activity->status == 'leave')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Cuti
                                    </span>
                                @elseif($activity->status == 'alpha')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Alpha
                                    </span>
                                @elseif($activity->status == 'out_of_town')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        </svg>
                                        DLK
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Belum Absen
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($activity->note)
                                    <div class="max-w-xs mx-auto">
                                        <div class="inline-flex items-start gap-1.5 text-xs text-gray-600 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                                            <svg class="w-3.5 h-3.5 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                            </svg>
                                            <span class="text-left">{{ Str::limit($activity->note, 50) }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                Belum ada aktivitas absensi hari ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <!-- Tampilan Dashboard USER -->
    <div class="space-y-6 px-5" x-data="dashboardApp">
        
        <!-- 1. Header & Greeting -->
        <!-- 1. Header & Greeting (Card Style) -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mt-6 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Halo, {{ Auth::user()->name }}! 👋</h1>
                <p class="text-gray-500 text-sm mt-1" x-text="getGreeting()"></p>
            </div>
            <!-- Space kosong di kanan untuk keseimbangan atau icon user nanti -->
            <div></div>
        </div>

        <!-- 2. Clock Card -->
        <div class="bg-brand-maroon rounded-3xl p-6 text-white shadow-xl shadow-brand-maroon/20 relative overflow-hidden">
            <!-- Decorations -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16 blur-2xl"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-brand-gold/10 rounded-full -ml-12 -mb-12 blur-xl"></div>
            
            <!-- Background Seconds -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none overflow-hidden">
                <span class="text-[4rem] font-bold font-mono text-white/15 tracking-tighter select-none" 
                      x-text="time.getSeconds().toString().padStart(2, '0')">
                    00
                </span>
            </div>

            <div class="relative z-10 flex flex-col items-center justify-center py-4">
                <div class="text-sm font-medium text-brand-cream/80 mb-2 uppercase tracking-widest">
                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                </div>
                <div class="text-6xl font-bold tracking-tight font-mono" x-text="time.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})">
                    {{ date('H:i') }}
                </div>

                <!-- Lokasi dipindah ke sini -->
                <div class="flex items-center gap-1.5 mt-4 bg-white/10 px-3 py-1.5 rounded-full backdrop-blur-sm border border-white/10">
                    <svg class="w-4 h-4 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="text-xs font-medium text-white/90 truncate max-w-[200px]" x-text="locationName">Memuat lokasi...</span>
                </div>
            </div>
        </div>

        <!-- 3. Work Schedule Info -->
        <div class="grid grid-cols-2 gap-4">
            <!-- Jam Masuk -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                </div>
                <span class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Jam Masuk</span>
                <span class="text-xl font-bold text-gray-900">
                    08:00
                </span>
                <span class="text-[10px] text-gray-400 mt-1">WITA</span>
            </div>

            <!-- Jam Pulang -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </div>
                <span class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Jam Pulang</span>
                <span class="text-xl font-bold text-gray-900">
                    {{ substr($company->time_out ?? '17:00', 0, 5) }}
                </span>
                <span class="text-[10px] text-gray-400 mt-1">WITA</span>
            </div>
        </div>
        
        <!-- RESEPSIONIS: ABSEN MANUAL -->
        @if(auth()->user()->hasRole('resepsionis'))
        @php
            $todayAttendance = \App\Models\Attendance::where('user_id', auth()->id())
                                ->where('date_attendance', date('Y-m-d'))
                                ->first();
        @endphp
        <div class="mt-6" x-data="receptionistAttendance()">
            <h3 class="font-bold text-gray-800 text-lg mb-3">Kehadiran Anda</h3>
            
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                @if(!$todayAttendance)
                    {{-- Belum Absen Masuk --}}
                    <div class="text-center">
                        <p class="text-sm text-gray-500 mb-4">Anda belum melakukan absen masuk hari ini.</p>
                        <button @click="submitAttendance('in')" :disabled="loading"
                                class="w-full bg-brand-maroon text-white font-bold py-3 rounded-xl shadow-lg shadow-brand-maroon/20 hover:bg-red-800 transition-all active:scale-95 flex items-center justify-center gap-2">
                            <template x-if="loading"><svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></template>
                            <span x-text="loading ? 'Memproses...' : 'ABSEN MASUK'"></span>
                        </button>
                    </div>
                @elseif(in_array($todayAttendance->status, ['sick', 'permission', 'leave', 'alpha', 'out_of_town']))
                    @php
                        $statusMap = ['sick' => 'Sakit', 'permission' => 'Izin', 'leave' => 'Cuti', 'alpha' => 'Alpha', 'out_of_town' => 'Dinas Luar Kota'];
                        $label = $statusMap[$todayAttendance->status] ?? $todayAttendance->status;
                    @endphp
                    <div class="text-center bg-red-50 p-6 rounded-xl border border-red-100">
                        <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-3">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="font-bold text-red-900 text-lg mb-1">Status: {{ strtoupper($label) }}</h3>
                        <p class="text-sm text-red-600">Anda tercatat dengan izin tidak hadir hari ini. Absensi dinonaktifkan.</p>
                    </div>
                @elseif(!$todayAttendance->time_out)
                    {{-- Sudah Absen Masuk, Belum Pulang --}}
                    <div class="text-center">
                        <div class="bg-green-50 p-3 rounded-lg border border-green-100 mb-4 inline-block w-full">
                            <p class="text-xs text-green-600 mb-1">Masuk Pukul</p>
                            <p class="text-xl font-bold text-green-800">{{ \Carbon\Carbon::parse($todayAttendance->time_in)->format('H:i') }}</p>
                        </div>
                        <button @click="submitAttendance('out')" :disabled="loading"
                                class="w-full bg-brand-gold text-white font-bold py-3 rounded-xl shadow-lg shadow-yellow-600/20 hover:bg-yellow-600 transition-all active:scale-95 flex items-center justify-center gap-2">
                            <template x-if="loading"><svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></template>
                            <span x-text="loading ? 'Memproses...' : 'ABSEN PULANG'"></span>
                        </button>
                    </div>
                @else
                    {{-- Sudah Selesai --}}
                    <div class="grid grid-cols-2 gap-3 text-center">
                        <div class="bg-green-50 p-3 rounded-lg border border-green-100">
                            <p class="text-xs text-green-600 mb-1">Masuk</p>
                            <p class="text-lg font-bold text-green-800">{{ \Carbon\Carbon::parse($todayAttendance->time_in)->format('H:i') }}</p>
                        </div>
                        <div class="bg-red-50 p-3 rounded-lg border border-red-100">
                            <p class="text-xs text-red-600 mb-1">Pulang</p>
                            <p class="text-lg font-bold text-red-800">{{ \Carbon\Carbon::parse($todayAttendance->time_out)->format('H:i') }}</p>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <span class="inline-block px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-medium">Anda sudah menyelesaikan absensi hari ini</span>
                    </div>
                @endif
            </div>

            <script>
                function receptionistAttendance() {
                    return {
                        loading: false,
                        submitAttendance(type) {
                            const label = type === 'in' ? 'Masuk' : 'Pulang';
                            const btnColor = type === 'in' ? '#9E2A2B' : '#D97706';

                            Swal.fire({
                                title: `Konfirmasi Absen ${label}`,
                                text: "Apakah anda yakin ingin memproses absensi ini?",
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: btnColor,
                                cancelButtonColor: '#6B7280',
                                confirmButtonText: 'Ya, Lanjutkan',
                                cancelButtonText: 'Batal',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    this.loading = true;
                                    if (navigator.geolocation) {
                                        navigator.geolocation.getCurrentPosition((position) => {
                                            fetch('{{ route("attendance.receptionist") }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({
                                                    latitude: position.coords.latitude,
                                                    longitude: position.coords.longitude,
                                                    type: type
                                                })
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                this.loading = false;
                                                if(data.status === 'success') {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Berhasil',
                                                        text: data.message,
                                                        showConfirmButton: false,
                                                        timer: 1500
                                                    }).then(() => location.reload());
                                                } else {
                                                    Swal.fire('Gagal', data.message, 'error');
                                                }
                                            })
                                            .catch(error => {
                                                this.loading = false;
                                                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                                            });
                                        }, (error) => {
                                            this.loading = false;
                                            Swal.fire('Gagal', 'Gagal mendapatkan lokasi. Pastikan GPS aktif.', 'error');
                                        });
                                    } else {
                                        this.loading = false;
                                        Swal.fire('Error', 'Browser tidak mendukung Geolocation.', 'error');
                                    }
                                }
                            });
                        }
                    }
                }
            </script>
        </div>
        @endif

        <!-- 4. KALENDER RIWAYAT ABSENSI -->
        <div x-data="calendarApp()" x-init="init()" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800 text-lg">Riwayat Absensi</h3>
                <div class="flex gap-1">
                    <button @click="prevMonth()" class="p-1 rounded-full hover:bg-gray-100 text-gray-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
                    <span class="text-sm font-medium text-gray-600 w-24 text-center" x-text="monthNames[currentMonth] + ' ' + currentYear"></span>
                    <button @click="nextMonth()" class="p-1 rounded-full hover:bg-gray-100 text-gray-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div>
                <!-- Days Header -->
                <div class="grid grid-cols-7 mb-2">
                    <template x-for="day in dayNames" :key="day">
                        <div class="text-center text-[10px] font-semibold text-gray-400 py-1" x-text="day"></div>
                    </template>
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-7 gap-1">
                    <template x-for="blank in blanks" :key="blank">
                        <div class="h-10"></div>
                    </template>

                    <template x-for="date in daysInMonth" :key="date">
                        <div @click="selectDate(date)" 
                             class="h-10 rounded-lg flex flex-col items-center justify-center relative cursor-pointer transition-all border border-transparent hover:border-brand-maroon/20"
                             :class="{
                                'bg-blue-50 text-blue-600 font-bold': isToday(date),
                                'text-gray-700': !isToday(date)
                             }">
                            <span class="text-xs" x-text="date"></span>
                            
                            <!-- Dot Indicator -->
                            <div class="mt-0.5 w-1.5 h-1.5 rounded-full" 
                                 :class="getAttendanceStatusColor(date)"
                                 x-show="hasAttendance(date)"></div>
                        </div>
                    </template>
                </div>
            </div>
            
            <!-- Legend Kecil -->
            <div class="mt-4 flex flex-wrap gap-2 text-[10px] text-gray-500 justify-center">
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                    <span>Tepat Waktu</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                    <span>Terlambat</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                    <span>Setengah Hari</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 rounded-full bg-red-600"></div>
                    <span>Cuti/Izin/Sakit/Alpha</span>
                </div>
            </div>

            <!-- Detail Modal (Sheet) -->
            <div x-show="isModalOpen" style="display: none;"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-y-full opacity-0"
                 x-transition:enter-end="translate-y-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-y-0 opacity-100"
                 x-transition:leave-end="translate-y-full opacity-0"
                 class="fixed inset-x-0 bottom-0 z-[60] bg-white rounded-t-3xl shadow-[0_-5px_20px_rgba(0,0,0,0.15)] p-6 pb-12 border-t border-gray-100">
                
                <div class="w-12 h-1.5 bg-gray-200 rounded-full mx-auto mb-6"></div>

                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900" x-text="formatDateLong(selectedDateFull)"></h3>
                        <template x-if="selectedData">
                            <span>
                                <span x-show="['sick', 'permission', 'leave', 'alpha', 'out_of_town'].includes(selectedData.status)" 
                                    class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800" 
                                    x-text="getStatusLabel(selectedData.status)"></span>
                                <span x-show="!['sick', 'permission', 'leave', 'alpha', 'out_of_town'].includes(selectedData.status)" 
                                    class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Hadir</span>
                            </span>
                        </template>
                        <span x-show="!selectedData" class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Tidak Hadir</span>
                    </div>
                    <button @click="isModalOpen = false" class="p-2 bg-gray-50 rounded-full text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <template x-if="selectedData">
                    <div>
                        <template x-if="!['sick', 'permission', 'leave', 'alpha', 'out_of_town'].includes(selectedData.status)">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                                    <p class="text-xs text-green-600 mb-1 font-semibold">JAM MASUK</p>
                                    <p class="text-xl font-bold text-green-900" x-text="formatTime(selectedData.time_in)"></p>
                                </div>
                                <div class="bg-red-50 rounded-xl p-4 border border-red-100">
                                    <p class="text-xs text-red-600 mb-1 font-semibold">JAM PULANG</p>
                                    <p class="text-xl font-bold text-red-900" x-text="selectedData.time_out ? formatTime(selectedData.time_out) : '--:--'"></p>
                                </div>
                            </div>
                        </template>
                        <template x-if="['sick', 'permission', 'leave', 'alpha', 'out_of_town'].includes(selectedData.status)">
                            <div class="bg-red-50 rounded-xl p-4 border border-red-100 text-center">
                                <p class="text-xs text-red-600 mb-2 font-semibold uppercase">Status Kehadiran</p>
                                <p class="text-xl font-bold text-red-900" x-text="getStatusLabel(selectedData.status)"></p>
                                <p class="text-sm text-gray-600 mt-2" x-text="selectedData.note || '-'"></p>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="!selectedData">
                    <div class="text-center py-6 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                        <p class="text-gray-500 text-sm">Tidak ada data absensi.</p>
                    </div>
                </template>
            </div>
            
            <!-- Backdrop -->
            <div x-show="isModalOpen" @click="isModalOpen = false" x-transition.opacity class="fixed inset-0 bg-black/20 z-[50] backdrop-blur-[1px]" style="display: none;"></div>
        </div>

        <!-- IN-APP CHECKOUT REMINDER LOGIC -->
        <div x-data="checkoutReminderApp()" x-init="init()"></div>
        <script>
            function checkoutReminderApp() {
                return {
                    // Inject PHP data: Company checkout time & User checkout status
                    timeOut: '{{ $company->time_out ?? "17:00" }}',
                    isCheckedOut: @json(auth()->user()->attendance()->whereDate('date_attendance', today())->whereNotNull('time_out')->exists()),
                    isCheckedIn: @json(auth()->user()->attendance()->whereDate('date_attendance', today())->whereNotNull('time_in')->exists()),
                    
                    init() {
                        // Only run if user checked in BUT NOT checked out
                        if (this.isCheckedIn && !this.isCheckedOut) {
                            this.checkTime(); // Run immediately
                            setInterval(() => this.checkTime(), 30000); // Check every 30 seconds
                        }
                    },

                    checkTime() {
                        const now = new Date();
                        const [hours, minutes] = this.timeOut.split(':').map(Number);
                        
                        const checkoutTime = new Date();
                        checkoutTime.setHours(hours, minutes, 0, 0);

                        const diffMinutes = (now - checkoutTime) / 1000 / 60; // Difference in minutes

                        // Logic 1: 10 minutes BEFORE checkout (Range: -10 to -9)
                        if (diffMinutes >= -10 && diffMinutes < -9 && !sessionStorage.getItem('reminded_10_before')) {
                            this.showSwal('Persiapan Pulang!', '10 menit lagi jam pulang. Jangan lupa absen ya!', 'info');
                            sessionStorage.setItem('reminded_10_before', 'true');
                        }

                        // Logic 2: AT checkout time (Range: 0 to 1)
                        if (diffMinutes >= 0 && diffMinutes < 1 && !sessionStorage.getItem('reminded_on_time')) {
                            this.showSwal('Waktunya Pulang!', 'Sudah jam pulang nih. Hati-hati di jalan & jangan lupa absen!', 'success');
                            sessionStorage.setItem('reminded_on_time', 'true');
                        }

                        // Logic 3: 10 minutes AFTER checkout (Range: 10 to 11)
                        if (diffMinutes >= 10 && diffMinutes < 11 && !sessionStorage.getItem('reminded_10_after')) {
                            this.showSwal('Belum Absen Pulang?', 'Sudah lewat 10 menit dari jam pulang. Yuk absen sekarang!', 'warning');
                            sessionStorage.setItem('reminded_10_after', 'true');
                        }
                        
                        // Logic 4: Late Reminder (e.g. 1 hour later) - Optional, preventing spam if user opens app late at night
                        // We rely on session storage so it shows only once per session per threshold
                    },

                    showSwal(title, text, icon) {
                        Swal.fire({
                            title: title,
                            text: text,
                            icon: icon,
                            timer: 5000,
                            timerProgressBar: true,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false
                        });
                        
                        // Play notification sound if possible
                        // const audio = new Audio('/sounds/notification.mp3'); 
                        // audio.play().catch(e => console.log('Audio autoplay blocked'));
                    }
                }
            }
        </script>

    </div>
@endif
@endsection

@push('scripts')
<script>
    // Calendar Logic
    function calendarApp() {
        return {
            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            dayNames: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            currentMonth: new Date().getMonth(),
            currentYear: new Date().getFullYear(),
            daysInMonth: [],
            blanks: [],
            attendances: @json($historyAttendances ?? []), // Inject data dari backend
            isModalOpen: false,
            selectedData: null,
            selectedDateFull: null,

            init() { this.calculateDays(); },
            calculateDays() {
                let firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
                let daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                this.blanks = Array.from({ length: firstDay }, (_, i) => i);
                this.daysInMonth = Array.from({ length: daysInMonth }, (_, i) => i + 1);
            },
            prevMonth() {
                if (this.currentMonth === 0) { this.currentMonth = 11; this.currentYear--; } else { this.currentMonth--; }
                this.calculateDays();
            },
            nextMonth() {
                if (this.currentMonth === 11) { this.currentMonth = 0; this.currentYear++; } else { this.currentMonth++; }
                this.calculateDays();
            },
            isToday(date) {
                const today = new Date();
                return date === today.getDate() && this.currentMonth === today.getMonth() && this.currentYear === today.getFullYear();
            },
            getDateString(date) {
                return `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
            },
            hasAttendance(date) {
                return this.attendances[this.getDateString(date)] !== undefined;
            },
            getAttendanceStatusColor(date) {
                const data = this.attendances[this.getDateString(date)];
                if (!data) return '';
                
                // Check special statuses first
                if (['sick', 'permission', 'leave', 'alpha', 'out_of_town'].includes(data.status)) {
                    return 'bg-red-600';
                }
                
                // Check manual override (is_late flag) - priority check
                if (!data.is_late) {
                    // Manually marked as "on time"
                    return 'bg-green-500';
                }
                
                // Calculate lateness level based on actual time
                if (data.time_in) {
                    // Use company thresholds from PHP
                    const companyTimeIn = '{{ $company->time_in ?? "08:00" }}';
                    const late1Threshold = '{{ $company->late_threshold_1 ?? "08:30" }}';
                    const late2Threshold = '{{ $company->late_threshold_2 ?? "09:00" }}';
                    const late3Threshold = '{{ $company->late_threshold_3 ?? "12:00" }}';
                    
                    // Convert time to minutes for accurate comparison
                    const toMinutes = (timeStr) => {
                        const [h, m] = timeStr.substring(0, 5).split(':').map(Number); // Ensure HH:mm format
                        return h * 60 + m;
                    };
                    
                    const timeInMin = toMinutes(data.time_in);
                    const standardTimeMin = toMinutes(companyTimeIn);
                    const late3Min = toMinutes(late3Threshold);
                    
                    if (timeInMin <= standardTimeMin) {
                        return 'bg-green-500'; // Tepat Waktu
                    } else if (timeInMin > late3Min) {
                        return 'bg-orange-500'; // Setengah Hari
                    } else {
                        return 'bg-yellow-500'; // Terlambat 1, 2, or 3
                    }
                }
                
                // Default to late
                return 'bg-yellow-500';
            },
            getStatusLabel(status) {
                const map = { 'sick': 'SAKIT', 'permission': 'IZIN', 'leave': 'CUTI', 'alpha': 'ALPHA', 'out_of_town': 'DLK' };
                return map[status] || 'HADIR';
            },
            selectDate(date) {
                this.selectedDateFull = new Date(this.currentYear, this.currentMonth, date);
                this.selectedData = this.attendances[this.getDateString(date)] || null;
                this.isModalOpen = true;
            },
            formatTime(timeStr) { return timeStr.substring(0, 5); },
            formatDateLong(dateObj) {
                if(!dateObj) return '';
                return dateObj.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            }
        }
    }

    // Existing Dashboard Logic (Notification)
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboardApp', () => ({
            time: new Date(),
            timeOut: '{{ $company->time_out ?? "17:00:00" }}', 
            hasReminded: false,
            locationName: 'Mencari lokasi...',
            init() {
                // Update jam setiap detik
                setInterval(() => { this.time = new Date(); }, 1000);
                
                this.requestNotificationPermission();
                setInterval(() => { this.checkTime(); }, 60000); // Cek tiap menit

                this.getLocation();
            },
            getLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            this.fetchAddress(position.coords.latitude, position.coords.longitude);
                        },
                        (error) => {
                            console.error("Geolocation denied or error: ", error);
                            this.locationName = 'Lokasi tidak tersedia';
                        },
                        { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
                    );
                } else {
                    this.locationName = 'Tidak didukung';
                }
            },
            async fetchAddress(lat, lng) {
                try {
                    // Using Nominatim OpenStreetMap (Free, requires User-Agent header handled by browser)
                    let response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                    if (!response.ok) throw new Error('Network response was not ok');
                    let data = await response.json();
                    
                    let addr = data.address;
                    // Priority: City > Town > Village > County > State
                    let shortAddr = addr.city || addr.town || addr.village || addr.county || addr.state || 'Lokasi Terkunci';
                    
                    // Add District (Kecamatan/Suburb) if available for better detail
                    if(addr.suburb && shortAddr !== addr.suburb) {
                        shortAddr = `${addr.suburb}, ${shortAddr}`;
                    } else if (addr.city_district && shortAddr !== addr.city_district) {
                        shortAddr = `${addr.city_district}, ${shortAddr}`;
                    }

                    this.locationName = shortAddr;
                } catch (e) {
                    console.error("Geocoding fetch failed: ", e);
                     // Fallback to coordinates
                    this.locationName = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                }
            },
            requestNotificationPermission() {
                if (!("Notification" in window)) return;
                if (Notification.permission !== "granted") Notification.requestPermission();
            },
            checkTime() {
                const now = new Date();
                const [outHour, outMinute] = this.timeOut.split(':').map(Number);
                
                // Waktu 1: Jam Pulang (Default 17:00)
                const time1 = new Date();
                time1.setHours(outHour, outMinute, 0);

                // Waktu 2: Reminder Kedua (+10 menit)
                const time2 = new Date(time1);
                time2.setMinutes(time2.getMinutes() + 10);

                const attendanceToday = @json($attendanceToday);
                
                // Hanya jalankan jika sudah absen masuk & belum pulang
                if (!attendanceToday || !attendanceToday.time_in || attendanceToday.time_out) return;

                // Cek Reminder 1 (Tepat Waktu)
                // Logic: Jika waktu sekarang di antara Time1 dan Time2, dan belum diingatkan untuk Time1
                if (now >= time1 && now < time2 && !sessionStorage.getItem('reminded_time1')) {
                    this.triggerNotification('Waktunya Pulang! 🏠', 'Jam kerja selesai. Jangan lupa absen pulang.');
                    sessionStorage.setItem('reminded_time1', 'true');
                }

                // Cek Reminder 2 (Telat 10 Menit)
                // Logic: Jika waktu sekarang sudah lewat Time2, dan belum diingatkan untuk Time2
                if (now >= time2 && !sessionStorage.getItem('reminded_time2')) {
                    this.triggerNotification('🔔 Peringatan Kedua', 'Sudah lewat 10 menit dari jam pulang. Segera absen sebelum lupa!');
                    sessionStorage.setItem('reminded_time2', 'true');
                }
            },
            triggerNotification(title, body) {
                // 1. Coba Native Notification (untuk background/minimize)
                if (Notification.permission === "granted") {
                    new Notification(title, { 
                        body: body, 
                        icon: "{{ asset('img/logo_kasau.png') }}",
                        requireInteraction: true 
                    });
                }

                // 2. Tampilkan Modal Cantik (SweetAlert2)
                Swal.fire({
                    width: 480,
                    padding: '0',
                    html: `
                        <div class="relative overflow-hidden rounded-t-3xl">
                            <!-- Header dengan Background Gambar/Gradient -->
                            <div class="bg-gradient-to-br from-brand-maroon to-red-900 p-8 text-center text-white relative">
                                <div class="absolute top-0 left-0 w-full h-full opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                                <div class="bg-white/20 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm shadow-xl border border-white/30">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-bold mb-1 relative z-10">${title}</h2>
                                <p class="text-white/80 text-sm relative z-10">Kerja bagus hari ini! 💪</p>
                            </div>

                            <!-- Content Body -->
                            <div class="p-8 bg-white text-center">
                                <p class="text-gray-600 mb-6 leading-relaxed">
                                    ${body}
                                </p>
                                
                                <button onclick="window.location.href='{{ route('attendance.scan') }}'" 
                                        class="w-full bg-brand-maroon text-white font-bold py-4 rounded-xl shadow-lg shadow-brand-maroon/30 hover:bg-brand-maroon/90 transform hover:-translate-y-1 transition-all">
                                    Absen Pulang Sekarang
                                </button>
                                
                                <button onclick="Swal.close()" class="mt-4 text-gray-400 text-sm hover:text-gray-600 font-medium">
                                    Ingatkan Saya Nanti
                                </button>
                            </div>
                        </div>
                    `,
                    showConfirmButton: false, // Kita pakai tombol custom HTML di atas
                    showCloseButton: true,
                    backdrop: `
                        rgba(0,0,123,0.4)
                        url("{{ asset('img/confetti.gif') }}")
                        left top
                        no-repeat
                    `
                });
            },
            getGreeting() {
                const h = new Date().getHours();
                if(h < 11) return 'Selamat Pagi'; if(h < 15) return 'Selamat Siang'; if(h < 19) return 'Selamat Sore'; return 'Selamat Malam';
            }
        }));
    });
</script>
@endpush
