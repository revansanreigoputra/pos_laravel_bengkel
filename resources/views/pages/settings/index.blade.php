@extends('layouts.master')

@section('title', 'Pengaturan Bengkel')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-primary px-6 py-4">
                <h2 class="text-xl font-semibold text-white">Pengaturan Bengkel</h2>
            </div>
            
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Logo Bengkel -->
                {{-- <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo Bengkel</label>
                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <div class="flex-shrink-0">
                            @if($settings->logo_path)
                                <img id="logo-preview" src="{{ asset('storage/' . $settings->logo_path) }}" 
                                    alt="Logo" class="h-20 w-20 object-cover rounded-lg border">
                            @else
                                <img id="logo-preview" src="" alt="Logo" 
                                    class="hidden h-20 w-20 object-cover rounded-lg border">
                                <div class="h-20 w-20 bg-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-400">
                                    No Logo
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" name="logo" id="logo" 
                                class="block w-full text-sm text-gray-500 
                                       file:mr-4 file:py-2 file:px-4 
                                       file:rounded-full file:border-0 
                                       file:text-sm file:font-semibold 
                                       file:bg-blue-50 file:text-blue-700 
                                       hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, GIF. Maksimal 2MB</p>
                        </div>
                    </div>
                </div> --}}

                <!-- Nama Bengkel -->
                <div>
                    <label for="nama_bengkel" class="block text-sm font-medium text-gray-700">Nama Bengkel</label>
                    <input type="text" name="nama_bengkel" id="nama_bengkel" 
                        value="{{ old('nama_bengkel', $settings->nama_bengkel ?? 'BengkelKu') }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <!-- Alamat Bengkel -->
                <div>
                    <label for="alamat_bengkel" class="block text-sm font-medium text-gray-700">Alamat Bengkel</label>
                    <textarea name="alamat_bengkel" id="alamat_bengkel" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('alamat_bengkel', $settings->alamat_bengkel ?? 'Jl. Contoh No. 123, Godean, Yogyakarta') }}</textarea>
                </div>

                <!-- Telepon Bengkel -->
                <div>
                    <label for="telepon_bengkel" class="block text-sm font-medium text-gray-700">Telepon Bengkel</label>
                    <input type="text" name="telepon_bengkel" id="telepon_bengkel" 
                        value="{{ old('telepon_bengkel', $settings->telepon_bengkel ?? '0812-3456-7890') }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <!-- Email Bengkel -->
                <div>
                    <label for="email_bengkel" class="block text-sm font-medium text-gray-700">Email Bengkel</label>
                    <input type="email" name="email_bengkel" id="email_bengkel" 
                        value="{{ old('email_bengkel', $settings->email_bengkel ?? 'info@bengkelku.com') }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <!-- Tombol Simpan -->
                <div class="flex justify-end">
                    <button type="submit" 
                        class="inline-flex items-center px-4 py-2 text-sm font-medium 
                               text-white bg-indigo-600 rounded-md shadow-sm 
                               hover:bg-indigo-700 focus:outline-none 
                               focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Preview logo saat upload
    document.getElementById('logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const logoPreview = document.getElementById('logo-preview');
                logoPreview.src = e.target.result;
                logoPreview.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
