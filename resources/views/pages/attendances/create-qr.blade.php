@extends('layouts.app')

@section('title', 'Buat QR Code')

@section('content')
<div class="min-h-full flex flex-col items-center p-6 pb-24">
    
    {{-- 1. QR CARD (Restored Design) --}}
    <div class="w-full max-w-sm bg-white rounded-3xl shadow-xl overflow-hidden relative mb-8 shrink-0">
        <div class="h-32 bg-brand-maroon flex items-center justify-center relative">
            <h1 class="text-2xl font-bold text-white tracking-wide">ABSENSI KARYAWAN</h1>
            <div class="absolute -bottom-6 w-full flex justify-center">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center p-1 shadow-sm">
                    <img src="{{ asset('img/logo_kasau.png') }}" class="w-full h-full object-contain">
                </div>
            </div>
        </div>

        <div class="pt-10 pb-8 px-6 flex flex-col items-center text-center">
            <p class="text-sm text-gray-500 mb-6">Tunjukkan QR Code ini kepada karyawan untuk melakukan absensi.</p>

            <div class="bg-white p-4 rounded-xl shadow-inner border border-gray-100 relative group">
                <div id="qrcode" class="opacity-90 group-hover:opacity-100 transition-opacity"></div>
                <!-- Loading State -->
                <div id="loading" class="absolute inset-0 flex items-center justify-center bg-white">
                    <svg class="animate-spin h-8 w-8 text-brand-maroon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            <div class="mt-8 flex flex-col gap-2 w-full">
                <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                    <span class="text-xs text-gray-400 uppercase tracking-wider block mb-1">Tanggal Berlaku</span>
                    <span class="text-lg font-mono font-bold text-gray-800">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                </div>
                
                <div class="bg-green-50 rounded-lg p-3 border border-green-100">
                    <div class="flex items-center justify-center gap-2 text-green-700">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="text-sm font-semibold">QR Code Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monitoring Section -->
    <div class="w-full max-w-lg" x-data="{ 
        editNoteId: null, 
        noteContent: '',
        openNoteModal(id, currentNote) {
            this.editNoteId = id;
            this.noteContent = currentNote || '';
            this.isModalOpen = true;
        },
        isModalOpen: false
    }">
        <div class="flex items-center justify-between mb-3 px-1">
            <h2 class="text-lg font-bold text-gray-800">Absensi Hari Ini</h2>
            <div class="text-xs text-gray-500 bg-white px-2 py-1 rounded-md shadow-sm border border-gray-100">
                Total: <span class="font-bold text-brand-maroon">{{ $todayAttendances->count() }}</span>
            </div>
        </div>

        <div class="space-y-3 pb-24">
            @forelse($todayAttendances as $attendance)
            @php
                // Logika Status
                $statusColor = 'bg-green-100 text-green-800';
                $statusText = 'Tepat Waktu';
                
                if (in_array($attendance->status, ['sick', 'permission', 'leave', 'alpha', 'out_of_town'])) {
                    $map = [
                        'sick' => 'Sakit', 
                        'permission' => 'Izin', 
                        'leave' => 'Cuti', 
                        'alpha' => 'Alpha', 
                        'out_of_town' => 'Dinas Luar Kota'
                    ];
                    $statusText = strtoupper($map[$attendance->status] ?? $attendance->status);
                    $statusColor = 'bg-red-600 text-white';
                } elseif($attendance->is_late && $attendance->time_in) {
                    $timeIn = \Carbon\Carbon::parse($attendance->time_in);
                    // threshold dari company
                    $late1 = \Carbon\Carbon::parse($company->late_threshold_1 ?? '08:15');
                    $late2 = \Carbon\Carbon::parse($company->late_threshold_2 ?? '08:30');
                    $late3 = \Carbon\Carbon::parse($company->late_threshold_3 ?? '08:45');
                    
                    if ($timeIn->gt($late3)) {
                        $statusText = 'Setengah Hari'; // Tier 4 (Merah)
                        $statusColor = 'bg-red-100 text-red-800';
                    } elseif ($timeIn->gt($late2)) {
                        $statusText = 'Terlambat 3'; // Orange
                        $statusColor = 'bg-orange-100 text-orange-800';
                    } elseif ($timeIn->gt($late1)) {
                        $statusText = 'Terlambat 2'; // Yellow
                        $statusColor = 'bg-yellow-100 text-yellow-800';
                    } else {
                        $statusText = 'Terlambat 1'; // Green-Yellowish
                        $statusColor = 'bg-yellow-50 text-yellow-700';
                    }
                }
            @endphp
            
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 relative">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-3">
                        <div class="h-10 w-10 rounded-full bg-brand-maroon/10 flex items-center justify-center shrink-0">
                            <span class="text-brand-maroon font-bold text-sm">{{ strtoupper(substr($attendance->user->name, 0, 2)) }}</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 leading-tight">{{ $attendance->user->name }}</h3>
                            <p class="text-xs text-gray-500 mb-1">{{ $attendance->user->position ?? 'Karyawan' }}</p>
                            
                            <!-- Badges -->
                            <div class="flex flex-wrap gap-1 mb-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $statusColor }}">
                                    {{ $statusText }}
                                </span>
                                @if($attendance->time_out)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600">
                                    Pulang: {{ \Carbon\Carbon::parse($attendance->time_out)->format('H:i') }}
                                </span>
                                @endif
                            </div>

                            <!-- Note Display -->
                            @if($attendance->note)
                            <div class="mt-2 text-xs text-gray-600 bg-gray-50 p-2 rounded border border-gray-100 flex items-start gap-1">
                                <svg class="w-3 h-3 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                <span>{{ $attendance->note }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col items-end gap-2">
                         <span class="text-xs font-mono font-bold text-gray-700 bg-gray-50 px-1.5 py-0.5 rounded border border-gray-200">
                            {{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '-' }}
                        </span>
                        
                        <!-- Edit Note Button -->
                        <button @click="openNoteModal({{ $attendance->id }}, '{{ addslashes($attendance->note) }}')" 
                                class="p-1.5 rounded-full bg-gray-50 text-gray-400 hover:text-brand-maroon hover:bg-red-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8 bg-white/50 rounded-xl border border-dashed border-gray-300">
                <p class="text-gray-500 text-sm">Belum ada yang absen hari ini.</p>
            </div>
            @endforelse
        </div>

        <!-- Note Modal -->
        <div x-show="isModalOpen" style="display: none;"
             class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="isModalOpen = false"></div>
            
            <!-- Modal Content -->
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm relative z-10 p-6 animate-in zoom-in-95 duration-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Catatan Absensi</h3>
                
                <form :action="'{{ url('attendance') }}/' + editNoteId + '/update-note'" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Alasan</label>
                        <textarea name="note" rows="3" x-model="noteContent" 
                                  class="w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-maroon focus:ring focus:ring-brand-maroon/20 text-sm"
                                  placeholder="Contoh: Izin terlambat karena ban bocor..."></textarea>
                    </div>
                    
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="isModalOpen = false" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm bg-brand-maroon text-white rounded-lg hover:bg-red-800 shadow-sm shadow-brand-maroon/30">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qrContainer = document.getElementById('qrcode');
        const loading = document.getElementById('loading');
        const token = "{{ $qrToken }}";

        // Generate QR Code
        setTimeout(() => {
            new QRCode(qrContainer, {
                text: token,
                width: 200,
                height: 200,
                colorDark : "#800000", // Brand Maroon
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
            loading.style.display = 'none';
        }, 500); // Small delay for visual effect
    });
</script>
@endpush
@endsection
