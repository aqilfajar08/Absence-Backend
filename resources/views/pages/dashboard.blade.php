@extends('layouts.app')

@section('title', 'Dashboard - Absensi Karyawan')

@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
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
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-start gap-4">
        <div class="flex-shrink-0 bg-green-100 rounded-lg p-2 text-green-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="flex-1">
            <h3 class="text-sm font-bold text-green-900">Kebijakan Denda Keterlambatan</h3>
            <p class="mt-1 text-sm text-green-800">
                Saat ini berlaku denda sebesar 
                <span class="bg-white px-1.5 py-0.5 rounded font-bold border border-green-100">Rp {{ number_format($company->late_fee_per_minute ?? 0, 0, ',', '.') }}</span> 
                untuk setiap kelipatan 
                <span class="bg-white px-1.5 py-0.5 rounded font-bold border border-green-100">{{ $company->late_fee_interval_minutes ?? 1 }} Menit</span> 
                keterlambatan.
            </p>
        </div>
        <a href="{{ route('company.edit', 1) }}" class="flex-shrink-0 text-sm font-semibold text-green-700 hover:text-green-900 hover:underline">
            Ubah Pengaturan &rarr;
        </a>
    </div>
    @endif
    {{-- Stats Cards --}}
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
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
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
                                    {{-- Initials Avatar --}}
                                    <div class="h-10 w-10 rounded-full bg-brand-maroon/10 flex items-center justify-center">
                                        <span class="text-brand-maroon font-semibold">{{ strtoupper(substr($activity->user->name ?? 'U', 0, 2)) }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $activity->user->name ?? 'Unknown' }}</div>
                                    <div class="text-sm text-gray-500">{{ $activity->user->email ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            {{ $activity->time_in ? \Carbon\Carbon::parse($activity->time_in)->format('H:i') . ' WIB' : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            @if($activity->is_late)
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Terlambat
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Hadir
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            {{ $activity->is_late ? 'Terlambat/Telat' : 'Tepat Waktu' }}
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
@endsection
