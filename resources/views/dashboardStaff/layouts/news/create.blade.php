@extends('dashboardStaff.layouts.app') 

@section('content')
<div class="p-4 sm:ml-64">
    <div class="mt-14">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Add New News</h2>
            <a href="{{ route('news.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            {{-- Form untuk menyimpan data baru. Wajib ada enctype untuk upload gambar --}}
            <form action="{{ route('news.store') }}" method="POST" enctype="multipart/form-data">
                @csrf 

                {{-- Kolom Judul Berita (Title) --}}
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input 
                        type="text" 
                        name="title" 
                        id="title" 
                        {{-- Tambahkan border-red-500 jika ada error --}}
                        class="w-full border border-gray-300 rounded-md p-2 focus:ring-green-500 focus:border-green-500 @error('title') border-red-500 @enderror" 
                        value="{{ old('title') }}" 
                        required
                    >
                    {{-- Tampilkan error message --}}
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kolom Konten Berita (Content) --}}
                <div class="mb-4">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                    <textarea 
                        name="content" 
                        id="content" 
                        rows="6" 
                        {{-- Tambahkan border-red-500 jika ada error --}}
                        class="w-full border border-gray-300 rounded-md p-2 focus:ring-green-500 focus:border-green-500 @error('content') border-red-500 @enderror" 
                        required
                    >{{ old('content') }}</textarea>
                    {{-- Tampilkan error message --}}
                    @error('content')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Kolom Penerbit (Publisher) --}}
                {{-- Dibuat READONLY karena diasumsikan diambil dari user yang login --}}
                <div class="mb-4">
                    <label for="publisher" class="block text-sm font-medium text-gray-700 mb-2">Publisher</label>
                    <input 
                        type="text" 
                        name="publisher" 
                        id="publisher" 
                        class="w-full border border-gray-300 rounded-md p-2 bg-gray-100" 
                        {{-- Menggunakan Auth::user()->name jika login, atau 'Staff Name' sebagai fallback --}}
                        value="{{ old('publisher', Auth::user()->name ?? 'Staff Name') }}" 
                        readonly
                    >
                    {{-- Validasi Publisher (walaupun readonly, tetap divalidasi di backend) --}}
                    @error('publisher')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kolom Gambar (Image) --}}
                <div class="mb-6">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                    <input 
                        type="file" 
                        name="image" 
                        id="image" 
                        {{-- Tambahkan border-red-500 jika ada error (gunakan class yang sesuai untuk input type file) --}}
                        class="w-full text-gray-700 border border-gray-300 rounded-md p-2 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 @error('image') border-red-500 @enderror" 
                    >
                    {{-- Tampilkan error message --}}
                    @error('image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    {{-- Tambahkan panduan file --}}
                    <p class="text-xs text-gray-400 mt-1">Required. Format: JPG, JPEG, PNG, GIF. Maksimum 2MB.</p>
                </div>

                {{-- Tombol Simpan --}}
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-green-500 text-white font-medium rounded-md hover:bg-green-600 transition-colors">
                        Save News
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection