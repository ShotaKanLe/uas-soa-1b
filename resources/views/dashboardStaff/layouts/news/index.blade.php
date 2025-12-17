@extends('dashboardStaff.layouts.app') 

@section('content')
<div class="p-4 sm:ml-64">
    <div class="mt-14">
        
        {{-- BLOK ERROR HANDLING (ALERTS) START --}}

        {{-- 1. Success Alert --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        {{-- 2. Error Alert (Untuk menangani error umum dari Controller) --}}
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        
        {{-- BLOK ERROR HANDLING (ALERTS) END --}}

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">News Management</h2>
            <a href="{{ route('news.create') }}" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 flex items-center">
                <i class="fas fa-plus mr-2"></i> Add News
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NO</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NEWS TITLE</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CONTENT PREVIEW</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PUBLISHER</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ACTION</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($news as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->title }}</td>
                        <td class="px-6 py-4">
                            {{ Str::limit($item->content, 50) }} 
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->publisher }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center">
                            {{-- Tombol Detail --}}
                            <a href="{{ route('news.show', $item->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Detail</a>

                            {{-- Tombol Edit --}}
                            <a href="{{ route('news.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>

                            {{-- Form untuk Delete --}}
                            <form action="{{ route('news.delete', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this news?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                            No news data available.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection