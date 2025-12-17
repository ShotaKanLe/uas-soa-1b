@extends('dashboardStaff.layouts.app') 

@section('content')
<div class="p-4 sm:ml-64">
    <div class="mt-14">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Edit News: {{ $news->title }}</h2>
            <a href="{{ route('news.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            {{-- Form dengan action ke route news.update dan menggunakan method PUT --}}
            <form action="{{ route('news.update', $news->id) }}" method="POST" enctype="multipart/form-data">
                @csrf 
                @method('PUT') 

                {{-- Kolom Judul Berita (Title) --}}
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input 
                        type="text" 
                        name="title" 
                        id="title" 
                        {{-- Styling error yang lebih jelas --}}
                        class="w-full border border-gray-300 rounded-md p-2 focus:ring-green-500 focus:border-green-500 @error('title') border-red-500 @enderror" 
                        value="{{ old('title', $news->title) }}" 
                        required
                    >
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
                        {{-- Styling error yang lebih jelas --}}
                        class="w-full border border-gray-300 rounded-md p-2 focus:ring-green-500 focus:border-green-500 @error('content') border-red-500 @enderror" 
                        required
                    >{{ old('content', $news->content) }}</textarea>
                    @error('content')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Kolom Penerbit (Publisher) --}}
                <div class="mb-4">
                    <label for="publisher" class="block text-sm font-medium text-gray-700 mb-2">Publisher</label>
                    <input 
                        type="text" 
                        name="publisher" 
                        id="publisher" 
                        {{-- Publisher dibuat readonly dan menggunakan styling yang sesuai --}}
                        class="w-full border border-gray-300 rounded-md p-2 bg-gray-100" 
                        value="{{ old('publisher', $news->publisher) }}" 
                        readonly
                    >
                    @error('publisher')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kolom Gambar (Image) --}}
                <div class="mb-6">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Change Image (Optional)</label>
                    <input 
                        type="file" 
                        name="image" 
                        id="image" 
                        {{-- Styling error yang lebih konsisten --}}
                        class="w-full text-gray-700 border border-gray-300 rounded-md p-2 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 @error('image') border-red-500 @enderror"
                    >
                    
                    {{-- Tampilkan Error Message --}}
                    @error('image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    {{-- Tampilkan Gambar Saat Ini (Preview) --}}
                    @if ($news->image)
                        <div class="mt-4">
                            <p class="text-sm text-gray-500 mb-1">Current Image:</p>
                            {{-- Tampilkan thumbnail --}}
                            <img src="{{ Storage::url($news->image) }}" alt="Current Image" class="h-20 w-auto object-cover rounded-md shadow-md">
                        </div>
                    @endif

                    <p class="text-xs text-gray-400 mt-2">Leave blank to keep current image. Format: JPG, JPEG, PNG, GIF. Maksimum 2MB.</p>
                </div>

                {{-- Tombol Simpan --}}
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition-colors">
                        Update News
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection