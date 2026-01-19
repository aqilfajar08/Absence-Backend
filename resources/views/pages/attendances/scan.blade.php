@extends('layouts.app')

@section('title', 'Scan Absensi')

@section('content')
<div class="max-w-md mx-auto" x-data="scannerApp()" 
     data-company-lat="{{ \App\Models\Company::first()->latitude ?? 0 }}"
     data-company-lng="{{ \App\Models\Company::first()->longitude ?? 0 }}">
    
    <!-- Header Card -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 mb-6">
        <h1 class="text-xl font-bold text-gray-900">Pindai Kode QR</h1>
        <p class="text-xs text-gray-500 mt-0.5">Arahkan kamera ke kode QR yang diberikan oleh resepsionis!</p>
    </div>

    <!-- Status Indicator -->
    <div class="mb-4">
        @if(!$attendance)
            <div class="bg-green-100 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-bold text-green-700">Mode: Absen Masuk</p>
                        <p class="text-xs text-green-600">Silakan pindai kode QR untuk absen masuk.</p>
                    </div>
                </div>
            </div>
        @elseif($attendance->time_in && !$attendance->time_out)
            <div class="bg-blue-100 border-l-4 border-blue-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-bold text-blue-700">Mode: Absen Pulang</p>
                        <p class="text-xs text-blue-600">Scan QR Code lagi untuk check-out.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-gray-100 border-l-4 border-gray-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-bold text-gray-700">Sudah Selesai</p>
                        <p class="text-xs text-gray-600">Anda sudah menyelesaikan absensi hari ini.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- UI: Minta Izin Akses (Muncul di Awal) -->
    <div x-show="!permissionGranted && !isLoading" class="text-center py-8 px-4 bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="w-20 h-20 bg-brand-cream/30 text-brand-maroon rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Izin Akses Diperlukan</h3>
        <p class="text-gray-500 text-sm mb-6">
            Untuk melakukan absensi, aplikasi membutuhkan akses ke 
            <span x-show="mode === 'in'"><strong>Kamera</strong> dan</span> 
            <strong>Lokasi GPS</strong> Anda.
        </p>
        
        <button @click="requestPermission()" 
                class="w-full py-3 px-6 rounded-xl bg-brand-maroon text-white font-bold shadow-lg hover:bg-brand-maroon/90 transition-transform active:scale-95">
            Izinkan & Mulai
        </button>
    </div>
    
    <!-- UI: Loading -->
    <div x-show="isLoading" class="text-center py-12">
        <svg class="animate-spin h-10 w-10 text-brand-maroon mx-auto mb-3" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-500 font-medium" x-text="loadingText"></p>
    </div>

    <!-- UI: Scanner / Tombol Pulang (Muncul setelah izin diberikan) -->
    <div x-show="permissionGranted && !isLoading">

        <!-- Mode SCAN (Absen Masuk) -->
        <div x-show="mode === 'in'">
            <!-- Step 2: Aktivasi Kamera (Manual Trigger untuk Mobile) -->
            <div x-show="!cameraActive" class="text-center py-10 px-6 bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="w-16 h-16 bg-brand-maroon/10 text-brand-maroon rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Siap Memindai</h3>
                <p class="text-gray-500 text-sm mb-6">Lokasi berhasil didapat. Klik tombol di bawah untuk membuka kamera.</p>
                <button @click="activateCamera()" class="w-full py-3 rounded-xl bg-brand-maroon text-white font-bold shadow-lg active:scale-95 transition-all">
                    Buka Kamera
                </button>
            </div>

            <!-- Camera Viewport -->
            <div x-show="cameraActive">
                <div class="bg-black rounded-2xl overflow-hidden shadow-lg relative aspect-[3/4]">
                    <div id="reader" class="w-full h-full object-cover"></div>
                    
                    <!-- Overlay Guide -->
                    <div class="absolute inset-0 border-2 border-brand-maroon/50 z-10 pointer-events-none flex items-center justify-center">
                        <div class="w-64 h-64 border-2 border-white/50 rounded-lg relative">
                            <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-brand-maroon -mt-1 -ml-1"></div>
                            <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-brand-maroon -mt-1 -mr-1"></div>
                            <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-brand-maroon -mb-1 -ml-1"></div>
                            <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-brand-maroon -mb-1 -mr-1"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-gray-50 text-gray-600 rounded-xl text-xs flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Pastikan kamera fokus ke QR Code.
                </div>
            </div>
        </div>

        <!-- Mode BUTTON (Absen Pulang) -->
        <div x-show="mode === 'out'" class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100 text-center">
            <div class="w-20 h-20 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Pulang</h2>
            <p class="text-gray-500 text-sm mb-6">Pastikan Anda berada di area kantor sebelum menekan tombol di bawah ini.</p>
            
            <button @click="handleManualCheckout()" 
                    class="w-full py-4 px-6 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg shadow-lg shadow-blue-200 transition-all active:scale-95 flex items-center justify-center gap-2">
                Absen Pulang Sekarang
            </button>
            <p x-show="coords && companyLat" class="mt-4 text-xs text-gray-500">
                Lokasi terdeteksi: 
                <span class="font-bold text-gray-800" x-text="calculateDistance(coords.latitude, coords.longitude, companyLat, companyLng).toFixed(2) + ' km'"></span> 
                dari kantor
            </p>
        </div>
        
        <!-- Mode SELESAI -->
        <div x-show="mode === 'done'" class="text-center py-10">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900">Absensi Hari Ini Selesai</h3>
            <p class="text-gray-500 mt-2">Terima kasih atas kerja keras Anda hari ini!</p>
            <a href="{{ route('home') }}" class="mt-6 inline-block text-brand-maroon font-semibold hover:underline">Kembali ke Beranda</a>
        </div>

    </div>

</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    function scannerApp() {
        return {
            isLoading: false, // Default false, tunggu user klik
            permissionGranted: false,
            loadingText: 'Memuat...',
            coords: null,
            html5QrCode: null,
            mode: '{{ !$attendance ? "in" : ($attendance->time_in && !$attendance->time_out ? "out" : "done") }}',
            cameraActive: false,
            // Koordinat Kantor (Dari DOM Data Attribute)
            companyLat: parseFloat(document.querySelector('[x-data]').dataset.companyLat) || 0,
            companyLng: parseFloat(document.querySelector('[x-data]').dataset.companyLng) || 0,

            async init() {
                // Cek Security Context (HTTPS/Localhost)
                const isSecure = window.isSecureContext;
                
                if (!isSecure) {
                    // ... (Security alert logic stays same, removed for brevity) ...
                    Swal.fire({ title: 'Akses Tidak Aman', text: 'Gunakan HTTPS agar fitur berjalan.', icon: 'warning' }); 
                }

                if (this.mode === 'done') {
                    this.permissionGranted = true;
                    return;
                }

                // 🚀 SPEED OPTIMIZATION: Cek Cache Lokasi
                const cached = sessionStorage.getItem('attendance_coords');
                if (cached) {
                    try {
                        const parsed = JSON.parse(cached);
                        // Validasi sederhana (pastikan ada lat/lng)
                        if (parsed.latitude && parsed.longitude) {
                            this.coords = parsed;
                            this.permissionGranted = true; // Langsung skip loading
                            
                            // Update lokasi terbaru di background (Silent Mode)
                            this.requestLocation(true); 
                            return;
                        }
                    } catch (e) { console.log('Cache invalid'); }
                }

                // Normal Flow (First Time)
                if (navigator.permissions && navigator.permissions.query) {
                    try {
                        const result = await navigator.permissions.query({ name: 'geolocation' });
                        if (result.state === 'granted') {
                            this.isLoading = true;
                            this.loadingText = 'Mengambil lokasi...';
                            this.requestLocation();
                        }
                    } catch (e) {
                        console.log('Permission API check failed:', e);
                    }
                }
            },

            requestPermission() {
                this.isLoading = true;
                this.loadingText = 'Meminta izin lokasi...';
                this.requestLocation();
            },

            requestLocation(silent = false) {
                if (!navigator.geolocation) {
                    if (!silent) this.showError('Browser tidak mendukung Geolocation.');
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        // Simpan ke Cache
                        const newCoords = {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude
                        };
                        this.coords = newCoords;
                        sessionStorage.setItem('attendance_coords', JSON.stringify(newCoords));
                        
                        // Sukses
                        this.permissionGranted = true;
                        this.isLoading = false;

                        if (this.mode === 'in') {
                            this.cameraActive = false;
                        }
                    },
                    (error) => {
                        if (silent) return; // Jika background update gagal, abaikan saja
                        
                        let msg = 'Gagal mendapatkan lokasi.';
                        if (!window.isSecureContext) {
                            msg = 'Browser memblokir lokasi karena koneksi tidak aman (HTTP). Gunakan HTTPS.';
                        } else if (error.code === 1) {
                            msg = 'Izin lokasi ditolak. Silakan reset izin browser dan coba lagi.';
                        } else if (error.code === 2) {
                            msg = 'Posisi tidak tersedia (GPS mati/tidak akurat).';
                        } else if (error.code === 3) {
                            msg = 'Waktu permintaan lokasi habis. Coba lagi.';
                        }
                        this.showError(msg);
                    },
                    { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
                );
            },

            activateCamera() {
                this.cameraActive = true;
                this.$nextTick(() => {
                    this.startScanner();
                });
            },

            startScanner() {
                // Gunakan Core API untuk kontrol penuh & error handling
                // Hapus instance lama jika ada (prevent error 'already rendering')
                if (this.html5QrCode) {
                    try { this.html5QrCode.clear(); } catch(e){}
                }

                this.html5QrCode = new Html5Qrcode("reader");
                const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                
                // Minta akses kamera belakang (environment) secara eksplisit
                this.html5QrCode.start(
                    { facingMode: "environment" }, 
                    config,
                    (decodedText, decodedResult) => {
                        // Sukses Scan
                        this.submitAttendance(decodedText);
                    },
                    (errorMessage) => {
                        // Scanning... (ignore parse errors usually)
                    }
                ).catch((err) => {
                    console.error("Camera Start Error:", err);
                    let msg = "Gagal membuka kamera: " + err;
                    
                    if (err.name === 'NotAllowedError' || String(err).includes('Permission')) {
                        msg = "Izin kamera ditolak browser. Cek pengaturan situs (Site Settings) di browser anda.";
                    } else if (err.name === 'NotFoundError') {
                        msg = "Kamera tidak ditemukan.";
                    }
                    
                    this.showError(msg);
                });
            },

            handleManualCheckout() {
                if (!this.coords) {
                    this.showError('Lokasi hilang. Coba refresh.');
                    return;
                }
                this.submitAttendance('CHECKOUT_BUTTON');
            },

            submitAttendance(qrCode) {
                // Stop scanner segera setelah scan
                if (this.html5QrCode) {
                    this.html5QrCode.stop().then(() => {
                        this.html5QrCode.clear();
                    }).catch(err => console.warn('Failed to stop scanner', err));
                }
                
                this.isLoading = true;
                this.loadingText = 'Memproses absensi...';

                fetch('{{ route("attendance.process_scan") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        latitude: this.coords.latitude,
                        longitude: this.coords.longitude,
                        qr_code: qrCode
                    })
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Terjadi kesalahan');
                    return data;
                })
                .then(data => {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#7f1d1d'
                    }).then(() => {
                        window.location.href = "{{ route('home') }}";
                    });
                })
                .catch(error => {
                    this.isLoading = false;
                    Swal.fire({
                        title: 'Gagal!',
                        text: error.message,
                        icon: 'error',
                        confirmButtonText: 'Coba Lagi',
                        confirmButtonColor: '#7f1d1d'
                    }).then(() => {
                        if (this.mode === 'in') window.location.reload(); 
                    });
                });
            },

            showError(msg) {
                this.isLoading = false;
                Swal.fire({
                    title: 'Akses Ditolak',
                    text: msg,
                    icon: 'warning',
                    confirmButtonText: 'Coba Lagi',
                    confirmButtonColor: '#7f1d1d'
                }).then(() => {
                    // Reset ke state awal biar muncul tombol lagi
                    this.permissionGranted = false;
                });
            },
            
            // Helper: Hitung jarak (Haversine Formula) - Frontend Only
            calculateDistance(lat1, lon1, lat2, lon2) {
                if (!lat1 || !lon1 || !lat2 || !lon2) return 0;
                
                const R = 6371; // Radius bumi (km)
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLon = (lon2 - lon1) * Math.PI / 180;
                const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                        Math.sin(dLon/2) * Math.sin(dLon/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                return R * c; // Jarak dalam KM
            }
        }
    }
</script>
@endpush
@endsection
