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
                            {{-- Tepat Waktu & Jam Pulang --}}
                            {{-- Jam Masuk --}}
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Masuk Kantor</label>
                                <input type="time" name="time_in" value="{{ $company->time_in ?? '08:00' }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition duration-200">
                                <p class="mt-2 text-xs text-green-600 font-medium">GPH dipotong 0%</p>
                            </div>

                            {{-- Jam Pulang --}}
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Pulang Kantor</label>
                                <input type="time" name="time_out" value="{{ $company->time_out ?? '17:00' }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition duration-200">
                                <p class="mt-2 text-xs text-gray-500">Batas jam absen pulang.</p>
                            </div>

                            {{-- Terlambat 1 --}}
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Batas Terlambat 1</label>
                                <div class="flex gap-3">
                                    <div class="w-2/3">
                                        <input type="time" name="late_threshold_1" value="{{ $company->late_threshold_1 ?? '08:30' }}" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition duration-200">
                                    </div>
                                    <div class="w-1/3 relative">
                                        <input type="number" name="gph_late_1_percent" value="{{ 100 - ($company->gph_late_1_percent ?? 75) }}" min="0" max="100"
                                            class="w-full pl-3 pr-8 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition duration-200">
                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">%</span>
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Persentase GPH yang dipotong jika lewat jam ini.</p>
                            </div>

                            {{-- Terlambat 2 --}}
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Batas Terlambat 2</label>
                                <div class="flex gap-3">
                                    <div class="w-2/3">
                                        <input type="time" name="late_threshold_2" value="{{ $company->late_threshold_2 ?? '09:00' }}" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition duration-200">
                                    </div>
                                    <div class="w-1/3 relative">
                                        <input type="number" name="gph_late_2_percent" value="{{ 100 - ($company->gph_late_2_percent ?? 70) }}" min="0" max="100"
                                            class="w-full pl-3 pr-8 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition duration-200">
                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">%</span>
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Persentase GPH yang dipotong jika lewat jam ini.</p>
                            </div>

                            {{-- Terlambat 3 --}}
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Batas Terlambat 3</label>
                                <div class="flex gap-3">
                                    <div class="w-2/3">
                                        <input type="time" name="late_threshold_3" value="{{ $company->late_threshold_3 ?? '12:00' }}" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition duration-200">
                                    </div>
                                    <div class="w-1/3 relative">
                                        <input type="number" name="gph_late_3_percent" value="{{ 100 - ($company->gph_late_3_percent ?? 65) }}" min="0" max="100"
                                            class="w-full pl-3 pr-8 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition duration-200">
                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">%</span>
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Persentase GPH yang dipotong jika lewat jam ini.</p>
                            </div>

                            {{-- Setengah Hari --}}
                            <div class="bg-white p-4 rounded-lg border border-gray-200 md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Setengah Hari (Lewat Batas Terlambat 3)</label>
                                <div class="flex gap-3 items-center">
                                    <span class="text-sm text-gray-600">Otomatis berlaku jika lewat batas 3.</span>
                                    <div class="w-32 relative">
                                        <input type="number" name="gph_late_4_percent" value="{{ 100 - ($company->gph_late_4_percent ?? 0) }}" min="0" max="100"
                                            class="w-full pl-3 pr-8 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition duration-200">
                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">%</span>
                                    </div>
                                    <span class="text-sm text-gray-600">Potongan GPH</span>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-sm font-semibold text-gray-900 mb-4">Kebijakan Denda Keterlambatan</h4>
                            <p class="text-sm text-gray-600">
                                Kebijakan denda saat ini menggunakan sistem persentase potongan Gaji Pokok Harian (GPH) berdasarkan jam keterlambatan.
                            </p>
                        </div> --}}
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
