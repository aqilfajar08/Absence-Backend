@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
<div x-data="calendarApp()" x-init="init()" class="pb-24">
    
    <!-- Header Controls -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4 flex items-center justify-between">
        <button @click="prevMonth()" class="p-2 rounded-full hover:bg-gray-100 text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <h2 class="text-lg font-bold text-gray-800" x-text="monthNames[currentMonth] + ' ' + currentYear"></h2>
        <button @click="nextMonth()" class="p-2 rounded-full hover:bg-gray-100 text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
    </div>

    <!-- Calendar Grid -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <!-- Day Names -->
        <div class="grid grid-cols-7 mb-2">
            <template x-for="day in dayNames" :key="day">
                <div class="text-center text-xs font-semibold text-gray-400 py-2" x-text="day"></div>
            </template>
        </div>

        <!-- Dates -->
        <div class="grid grid-cols-7 gap-1">
            <!-- Empty cells for offset -->
            <template x-for="blank in blanks" :key="blank">
                <div class="h-14 sm:h-20"></div>
            </template>

            <!-- Days -->
            <template x-for="date in daysInMonth" :key="date">
                <div @click="selectDate(date)" 
                     class="h-14 sm:h-20 border rounded-lg flex flex-col items-center justify-start py-1 relative cursor-pointer transition-all hover:border-brand-maroon hover:shadow-md"
                     :class="{
                        'bg-blue-50 border-blue-200': isToday(date),
                        'bg-white border-gray-100': !isToday(date)
                     }">
                    
                    <!-- Date Number -->
                    <span class="text-sm font-medium" 
                          :class="{'text-blue-600 font-bold': isToday(date), 'text-gray-700': !isToday(date)}" 
                          x-text="date"></span>

                    <!-- Attendance Indicator (Dots) -->
                    <div class="mt-1 flex gap-0.5" x-show="hasAttendance(date)">
                        <!-- Dot Masuk -->
                        <div class="w-1.5 h-1.5 rounded-full"
                             :class="getAttendanceStatusColor(date)"></div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-4 flex flex-wrap gap-4 justify-center text-xs text-gray-500">
        <div class="flex items-center gap-1"><div class="w-3 h-3 rounded-full bg-green-500"></div> Tepat Waktu</div>
        <div class="flex items-center gap-1"><div class="w-3 h-3 rounded-full bg-yellow-500"></div> Terlambat</div>
        <div class="flex items-center gap-1"><div class="w-3 h-3 rounded-full bg-red-500"></div> Izin/Sakit</div>
    </div>

    <!-- Detail Modal (Sheet) -->
    <div x-show="isModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full opacity-0"
         class="fixed inset-x-0 bottom-0 z-50 bg-white rounded-t-3xl shadow-[0_-5px_20px_rgba(0,0,0,0.15)] p-6 pb-24 border-t border-gray-100"
         style="display: none;">
        
        <!-- Drag Handle -->
        <div class="w-12 h-1.5 bg-gray-200 rounded-full mx-auto mb-6"></div>

        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-xl font-bold text-gray-900" x-text="formatDateLong(selectedDateFull)"></h3>
                <!-- Status Badge -->
                <span x-show="selectedData" class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Hadir
                </span>
                <span x-show="!selectedData" class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    Tidak Hadir / Libur
                </span>
            </div>
            <button @click="isModalOpen = false" class="p-2 bg-gray-50 rounded-full text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <template x-if="selectedData">
            <div class="grid grid-cols-2 gap-4">
                <!-- Check In -->
                <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                    <p class="text-xs text-green-600 mb-1 font-semibold tracking-wide">JAM MASUK</p>
                    <p class="text-xl font-bold text-green-900" x-text="formatTime(selectedData.time_in)"></p>
                </div>

                <!-- Check Out -->
                <div class="bg-red-50 rounded-xl p-4 border border-red-100">
                    <p class="text-xs text-red-600 mb-1 font-semibold tracking-wide">JAM PULANG</p>
                    <p class="text-xl font-bold text-red-900" x-text="selectedData.time_out ? formatTime(selectedData.time_out) : '--:--'"></p>
                </div>
            </div>
        </template>
        
        <template x-if="!selectedData">
            <div class="text-center py-6 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                <p class="text-gray-500 text-sm">Anda tidak melakukan absensi pada tanggal ini.</p>
            </div>
        </template>
    </div>

    <!-- Backdrop -->
    <div x-show="isModalOpen" 
         @click="isModalOpen = false"
         x-transition.opacity
         class="fixed inset-0 bg-black/20 z-40 backdrop-blur-[1px]"
         style="display: none;"></div>

</div>

@push('scripts')
<script>
    function calendarApp() {
        return {
            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            dayNames: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            currentMonth: new Date().getMonth(),
            currentYear: new Date().getFullYear(),
            daysInMonth: [],
            blanks: [],
            
            // Data dari Backend (Laravel Blade injection)
            attendances: @json($attendances),

            // Modal State
            isModalOpen: false,
            selectedData: null,
            selectedDateFull: null,

            init() {
                this.calculateDays();
            },

            calculateDays() {
                let firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
                let daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                
                this.blanks = Array.from({ length: firstDay }, (_, i) => i);
                this.daysInMonth = Array.from({ length: daysInMonth }, (_, i) => i + 1);
            },

            prevMonth() {
                if (this.currentMonth === 0) {
                    this.currentMonth = 11;
                    this.currentYear--;
                } else {
                    this.currentMonth--;
                }
                this.calculateDays();
            },

            nextMonth() {
                if (this.currentMonth === 11) {
                    this.currentMonth = 0;
                    this.currentYear++;
                } else {
                    this.currentMonth++;
                }
                this.calculateDays();
            },

            isToday(date) {
                const today = new Date();
                return date === today.getDate() && 
                       this.currentMonth === today.getMonth() && 
                       this.currentYear === today.getFullYear();
            },

            getDateString(date) {
                // Return YYYY-MM-DD format
                return `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
            },

            hasAttendance(date) {
                const dateStr = this.getDateString(date);
                return this.attendances[dateStr] !== undefined;
            },

            getAttendanceStatusColor(date) {
                const dateStr = this.getDateString(date);
                const data = this.attendances[dateStr];
                if (!data) return '';

                // Cek status terlambat
                if (data.is_late || data.status === 'Terlambat') {
                    return 'bg-yellow-500';
                }
                
                return 'bg-green-500'; // Tepat Waktu
            },

            selectDate(date) {
                const dateStr = this.getDateString(date);
                this.selectedDateFull = new Date(this.currentYear, this.currentMonth, date);
                this.selectedData = this.attendances[dateStr] || null;
                this.isModalOpen = true; // Open modal regardless of data presence
            },
            
            // Helper Formats
            formatTime(timeStr) {
                // timeStr format HH:mm:ss
                return timeStr.substring(0, 5);
            },
            
            formatDateLong(dateObj) {
                if(!dateObj) return '';
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                return dateObj.toLocaleDateString('id-ID', options);
            }
        }
    }
</script>
@endpush
@endsection
