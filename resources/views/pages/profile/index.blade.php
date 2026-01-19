@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div x-data="{ 
    showEditModal: false, 
    showPasswordModal: false,
    previewImage: null,
    isUploading: false
}">
    <!-- Header Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-black/5 p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
        <p class="mt-1 text-sm text-gray-600">Kelola informasi profil Anda</p>
    </div>

    <!-- Success/Error Messages -->
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition
             class="mb-6 rounded-lg bg-green-50 p-4 border border-green-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-green-500 hover:text-green-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition
             class="mb-6 rounded-lg bg-red-50 p-4 border border-red-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="text-red-500 hover:text-red-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div x-data="{ show: true }" x-show="show" x-transition
             class="mb-6 rounded-lg bg-red-50 p-4 border border-red-200">
            <div class="flex items-start justify-between">
                <ul class="list-disc list-inside text-sm text-red-800">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button @click="show = false" class="text-red-500 hover:text-red-700 transition ml-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Profile Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Header Banner -->
        <div class="h-32 bg-gradient-to-r from-brand-maroon to-brand-maroon/80"></div>
        
        <!-- Profile Content -->
        <div class="relative px-6 pb-6">
            <!-- Profile Picture -->
            <div class="absolute -top-16 left-6">
                <div class="relative">
                    <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg overflow-hidden bg-gray-100">
                        @if(auth()->user()->image_url)
                            <img src="{{ asset('storage/' . auth()->user()->image_url) }}" 
                                 alt="Profile" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-brand-maroon/10 flex items-center justify-center">
                                <span class="text-4xl font-bold text-brand-maroon">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <!-- Edit Photo Button -->
                    <button @click="showEditModal = true" 
                            class="absolute bottom-0 right-0 w-10 h-10 bg-brand-gold hover:bg-brand-gold/90 rounded-full flex items-center justify-center shadow-md transition">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Profile Info -->
            <div class="pt-20">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ auth()->user()->name }}</h2>
                        <p class="text-gray-500">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex gap-3">
                        <button @click="showEditModal = true"
                                class="inline-flex items-center px-4 py-2 bg-brand-maroon text-white text-sm font-medium rounded-lg hover:bg-brand-maroon/90 transition shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Profil
                        </button>
                        <button @click="showPasswordModal = true"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                            Ganti Password
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="mt-6">
        <!-- Personal Info Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-brand-maroon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Informasi Pribadi
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Nama Lengkap</span>
                    <span class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Email</span>
                    <span class="text-sm font-medium text-gray-900">{{ auth()->user()->email }}</span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-sm text-gray-500">Jabatan</span>
                    <span class="text-sm font-medium text-gray-900 capitalize">{{ auth()->user()->position ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Button -->
    <div class="mt-6 mb-8">
        <button type="button" onclick="confirmLogout()" class="w-full flex items-center justify-center px-4 py-3 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition border border-red-100 font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            Keluar Akun
        </button>
    </div>

    <!-- Edit Profile Modal -->
    <div x-show="showEditModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 sm:p-0">
            <div class="fixed inset-0 bg-black/50" @click="showEditModal = false"></div>
            
            <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 mx-4 text-left"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Edit Profil</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profileUpdateForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Photo Upload -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
                        <div class="flex flex-col gap-4">
                            <!-- Image Preview & Crop Area -->
                            <div class="w-full relative" x-show="previewImage">
                                <div class="h-64 bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                                    <img :src="previewImage" id="imageToCrop" class="max-w-full h-full object-contain mx-auto">
                                </div>
                                <div class="mt-3 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs text-gray-500">
                                            <span class="font-semibold text-gray-700">Panduan:</span> Geser kotak atau foto untuk mengatur posisi.
                                        </p>
                                        <button type="button" @click="previewImage = null; destroyCropper()" class="text-xs text-red-600 hover:text-red-800 underline">Batalkan Foto</button>
                                    </div>
                                    
                                    <!-- Crop Controls -->
                                    <div class="flex items-center justify-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200">
                                        <button type="button" onclick="cropper.zoom(0.1)" class="p-1.5 text-gray-600 hover:text-brand-maroon hover:bg-white rounded transition" title="Perbesar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                        </button>
                                        <button type="button" onclick="cropper.zoom(-0.1)" class="p-1.5 text-gray-600 hover:text-brand-maroon hover:bg-white rounded transition" title="Perkecil">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path></svg>
                                        </button>
                                        <div class="w-px h-4 bg-gray-300 mx-1"></div>
                                        <button type="button" onclick="cropper.rotate(-90)" class="p-1.5 text-gray-600 hover:text-brand-maroon hover:bg-white rounded transition" title="Putar Kiri">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                        </button>
                                        <button type="button" onclick="cropper.rotate(90)" class="p-1.5 text-gray-600 hover:text-brand-maroon hover:bg-white rounded transition" title="Putar Kanan">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a8 8 0 00-8 8v2m18-10l-6 6m6-6l-6-6"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                             <!-- Current Image Display (shown when no new file selected) -->
                            <div class="flex items-center gap-4" x-show="!previewImage">
                                <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-100 flex-shrink-0">
                                    @if(auth()->user()->image_url)
                                        <img src="{{ asset('storage/' . auth()->user()->image_url) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-brand-maroon/10 flex items-center justify-center">
                                            <span class="text-xl font-bold text-brand-maroon">
                                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <!-- Real Image Input -->
                                    <input type="file" id="profile-image" accept="image/*" class="hidden"
                                           @change="
                                               const file = $event.target.files[0];
                                               if (file) {
                                                   const reader = new FileReader();
                                                   reader.onload = (e) => {
                                                       previewImage = e.target.result;
                                                       $nextTick(() => {
                                                           initCropper();
                                                       });
                                                   };
                                                   reader.readAsDataURL(file);
                                               }
                                           ">
                                <label for="profile-image" 
                                           class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 cursor-pointer transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Pilih Foto
                                    </label>
                                    <p class="text-xs text-gray-500 mt-1">JPG, PNG max 2MB</p>
                                </div>
                            </div>
                        </div>
                         <!-- Hidden input for Cropped Image Base64 -->
                        <input type="hidden" name="cropped_image" id="cropped_image">
                    </div>

                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" id="name" 
                               value="{{ old('name', auth()->user()->name) }}"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-maroon focus:border-transparent transition"
                               required>
                    </div>

                    <!-- Position (Read-only) -->
                    <div class="mb-6">
                        <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                        <input type="text" id="position" 
                               value="{{ auth()->user()->position ?? '-' }}"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-100 text-gray-500 cursor-not-allowed"
                               disabled>
                        <p class="text-xs text-gray-500 mt-1">Jabatan hanya bisa diubah oleh admin</p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showEditModal = false; previewImage = null; destroyCropper()"
                                class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            Batal
                        </button>
                        <button type="button" onclick="submitCropForm()"
                                class="px-4 py-2.5 text-sm font-medium text-white bg-brand-maroon rounded-lg hover:bg-brand-maroon/90 transition shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div x-show="showPasswordModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 sm:p-0">
            <div class="fixed inset-0 bg-black/50" @click="showPasswordModal = false"></div>
            
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 mx-4 text-left"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Ganti Password</h3>
                    <button @click="showPasswordModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Current Password -->
                    <div class="mb-4">
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                        <input type="password" name="current_password" id="current_password" 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-maroon focus:border-transparent transition"
                               required>
                    </div>

                    <!-- New Password -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <input type="password" name="password" id="password" 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-maroon focus:border-transparent transition"
                               required minlength="8">
                        <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter</p>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-maroon focus:border-transparent transition"
                               required>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showPasswordModal = false"
                                class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2.5 text-sm font-medium text-white bg-brand-maroon rounded-lg hover:bg-brand-maroon/90 transition shadow-sm">
                            Ganti Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Global variables
    let cropper = null;

    // Initialize Cropper when modal opens or image changes
    function initCropper() {
        const image = document.getElementById('imageToCrop');
        
        // Destroy existing if any
        if (cropper) {
            cropper.destroy();
        }

        // Initialize new cropper
        cropper = new Cropper(image, {
            aspectRatio: 1, // Force square for profile picture
            viewMode: 1,    // Restrict crop box to canvas
            dragMode: 'move', // Allow moving the image
            autoCropArea: 1, // Start with full image
            restore: false,
            guides: true,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
        });
    }

    // Clean up
    function destroyCropper() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        document.getElementById('profile-image').value = ""; // Reset file input
    }

    // Handle Form Submit
    // Handle Form Submit
    window.submitCropForm = function() {
        // Show Loading Swal immediately
        Swal.fire({
            title: 'Menyimpan Perubahan...',
            text: 'Mohon tunggu sebentar, sedang memproses foto.',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        // Use setTimeout to allow the UI to update/render the Swal before heavy processing
        setTimeout(() => {
            // If cropper is active, get the cropped data
            if (cropper) {
                // Get cropped canvas
                const canvas = cropper.getCroppedCanvas({
                    width: 512,  // Set decent resolution
                    height: 512,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });

                // Convert to base64
                const base64data = canvas.toDataURL('image/jpeg', 0.9);
                
                // Set to hidden input
                document.getElementById('cropped_image').value = base64data;
                
                // Submit form
                document.getElementById('profileUpdateForm').submit();
            } else {
                // No crop (shouldn't happen if image selected, but fallback)
                document.getElementById('profileUpdateForm').submit();
            }
        }, 300); // 300ms delay to ensure "Loading" is visible
    }
</script>
@endpush

<style>
    [x-cloak] { display: none !important; }
    /* Ensure cropper container has height */
    .cropper-container {
        width: 100%;
        height: 100%;
    }
</style>
@endsection
