@extends('dashboardStaff.layouts.app')

@section('title', 'Update Data Companys')
@section('content')

<div class="bg-white rounded-md shadow-sm shadow-blue-100 border border-gray-300 p-6 mb-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
        <h2 class="text-xl font-semibold text-gray-800">Update Data</h2>
        <a href="{{ route('perusahaan.index') }}">
            <button class="flex items-center gap-2 px-4 py-2 border border-green-500 text-green-600 rounded-md hover:bg-green-50">
                Back to Dashboard
            </button>
        </a>
    </div>

    <form id="form-id" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4" action="{{ route('perusahaan.update', ['id' => $oldData->id]) }}" method="POST">
        @csrf
        @method('PUT')
        {{-- Company Name --}}
        <div>
            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Company Name<span class="text-red-500">*</span></label>
            <input type="text" id="company_name" name="company_name" value="{{ $oldData->nama_perusahaan }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="Company Name" />
            @error('company_name')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- ID Service -->
        <div>
            <label for="service_name" class="block text-sm font-medium text-gray-700 mb-1">Employee Name <span class="text-red-500">*</span></label>
            <select id="service_name" name="service_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                @foreach ($dataService as $item)
                    <option {{ $oldData->id_service == $item->id ? '' : 'selected'}} value="{{$item->id}}">{{$item->nama_service}}</option>
                @endforeach
            </select>
            @error('service_name')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Active Date --}}
        <div>
            <label for="active_date" class="block text-sm font-medium text-gray-700 mb-1">Active Date Service<span class="text-red-500">*</span></label>
            <input type="date" id="active_date" name="active_date" value="{{ $oldData->tanggal_aktif_service }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="Active Date Service" />
            @error('active_date')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </form>

    @if (session('success'))
        <div class="mb-4 p-4 rounded-md border border-green-300 bg-green-50 text-green-800 shadow-sm flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Form Actions -->
    <div class="flex justify-end gap-3 pt-6">
        <a href="{{ route('alamatRumah.index') }}">
            <button type="button" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </button>
        </a>
        <button type="submit" form="form-id" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 shadow-sm">
            Update Data
        </button>
    </div>
</div>

@endsection
