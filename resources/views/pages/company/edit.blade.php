@extends('layouts.app')

@section('title', 'Pengaturan - Absensi Karyawan')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="bg-white rounded-2xl shadow-sm border border-black/5 p-6 flex items-start justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                Pengaturan Perusahaan
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Atur jam kerja dan kebijakan denda keterlambatan.
            </p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 flex items-center justify-between">
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

    {{-- Form --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('company.update', $company->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-6 md:p-8 space-y-8">


                {{-- Section 2: Jam Kerja & Denda --}}
                <div class="pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-brand-maroon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Jam Kerja & Denda
                    </h3>
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Masuk Kantor</label>
                                <input type="time" name="time_in" value="{{ $company->time_in }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition duration-200">
                                <p class="mt-1 text-xs text-gray-500">Karyawan absen setelah jam ini dianggap terlambat.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Pulang Kantor</label>
                                <input type="time" name="time_out" value="{{ $company->time_out }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition duration-200">
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-sm font-semibold text-gray-900 mb-4">Kebijakan Denda Keterlambatan</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tarif Denda (Rp)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input type="number" name="late_fee_per_minute" value="{{ $company->late_fee_per_minute }}" 
                                               class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition duration-200">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Nominal denda yang dikenakan.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Hitung Setiap (Menit)</label>
                                    <div class="relative">
                                        <input type="number" name="late_fee_interval_minutes" value="{{ $company->late_fee_interval_minutes ?? 1 }}" min="1"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition duration-200">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Menit</span>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Contoh: Jika 15 menit, maka denda dihitung per kelipatan 15 menit keterlambatan.</p>
                                </div>
                            </div>
                            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">Simulasi Perhitungan</h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <p>Jika tarif <strong>Rp {{ number_format($company->late_fee_per_minute ?? 0, 0, ',', '.') }}</strong> per <strong>{{ $company->late_fee_interval_minutes ?? 1 }} menit</strong>:</p>
                                            <ul class="list-disc pl-5 mt-1 space-y-1">
                                                <li>Terlambat {{ $company->late_fee_interval_minutes ?? 1 }} menit = Rp {{ number_format($company->late_fee_per_minute ?? 0, 0, ',', '.') }}</li>
                                                <li>Terlambat {{ ($company->late_fee_interval_minutes ?? 1) * 2 }} menit = Rp {{ number_format(($company->late_fee_per_minute ?? 0) * 2, 0, ',', '.') }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="px-6 py-4 flex items-center justify-end border-gray-200">
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2.5 bg-brand-maroon text-white font-medium rounded-lg hover:bg-brand-maroon/90 transition shadow-lg shadow-brand-maroon/30 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
