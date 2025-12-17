@extends('dashboardStaff.layouts.app') 

@section('content')
<div class="p-4 sm:ml-64">
    <div class="mt-14">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Detail News: {{ $news->title }}</h2>
            <a href="{{ route('news.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- Kolom Kiri: Gambar (Paling menonjol) --}}
                <div class="md:col-span-1">
                    <h3 class="text-xl font-semibold mb-3 text-gray-700">Image</h3>
                    @if ($news->image)
                        {{-- Menampilkan gambar secara penuh --}}
                        {{-- Pastikan Anda sudah menjalankan php artisan storage:link --}}
                        <img src="{{ Storage::url($news->image) }}" alt="{{ $news->title }}" class="w-full h-auto object-cover rounded-lg shadow-md">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center rounded-lg text-gray-500 font-medium">
                            No Image Available
                        </div>
                    @endif
                </div>

                {{-- Kolom Kanan: Detail Data --}}
                <div class="md:col-span-2">
                    <h3 class="text-xl font-semibold mb-3 text-gray-700">News Information</h3>
                    
                    <div class="mb-4 border-b pb-2">
                        <label class="block text-sm font-medium text-gray-500">Title</label>
                        <p class="text-xl text-gray-900 font-bold">{{ $news->title }}</p>
                    </div>
                    
                    <div class="mb-4 border-b pb-2">
                        <label class="block text-sm font-medium text-gray-500">Publisher</label>
                        <p class="text-lg text-gray-900">{{ $news->publisher }}</p>
                    </div>

                    <div class="mb-6 border-b pb-2">
                        <label class="block text-sm font-medium text-gray-500">Published Date</label>
                        <p class="text-gray-900">{{ $news->created_at->format('d M Y, H:i') }}</p>
                    </div>

                    <h3 class="text-xl font-semibold mb-3 text-gray-700 mt-6 border-t pt-4">Content Detail</h3>
                    <div class="text-gray-800 leading-relaxed">
                        <p>{{ $news->content }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection