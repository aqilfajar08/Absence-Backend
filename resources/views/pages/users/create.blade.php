@extends('layouts.app')

@section('title', 'Tambah Karyawan - Absensi Karyawan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="bg-white rounded-2xl shadow-sm border border-black/5 p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Karyawan</h1>
            <p class="mt-1 text-sm text-gray-600">Buat akun karyawan baru untuk mengakses sistem.</p>
        </div>

    </div>

    {{-- Form Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-black/5 overflow-hidden">
        <form action="{{ route('user.store') }}" method="POST" class="p-6 sm:p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Nama Lengkap --}}
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <div class="relative">
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full pl-4 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition duration-200"
                               placeholder="Contoh: Ahmad Fadli">
                    </div>
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition duration-200"
                               placeholder="email@example.com">
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Role --}}
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role (Jabatan)</label>
                    <div class="relative">
                        <select name="role" required class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition duration-200 appearance-none bg-white">
                            <option value="">-- Pilih Role --</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    @error('role')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Department --}}
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Departemen</label>
                    <input type="text" name="department" value="{{ old('department') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition duration-200"
                           placeholder="Contoh: IT, HRD, Produksi">
                    @error('department')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Gaji Pokok --}}
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gaji Pokok</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="gaji_pokok" value="{{ old('gaji_pokok') }}" min="0"
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition duration-200"
                               placeholder="0">
                    </div>
                    @error('gaji_pokok')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tunjangan --}}
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tunjangan</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="tunjangan" value="{{ old('tunjangan') }}" min="0"
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition duration-200"
                               placeholder="0">
                    </div>
                    @error('tunjangan')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative" x-data="{ show: false }">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input :type="show ? 'text' : 'password'" name="password" required
                               class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-maroon/20 focus:border-brand-maroon transition duration-200"
                               placeholder="Masukkan password">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <svg x-show="show" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Action Buttons --}}
            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('user.index') }}" class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition duration-200">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-brand-maroon text-white font-medium hover:bg-brand-maroon/90 shadow-lg shadow-brand-maroon/30 transition duration-200">
                    Simpan Karyawan
                </button>
            </div>
            
        </form>
    </div>
</div>
@endsection