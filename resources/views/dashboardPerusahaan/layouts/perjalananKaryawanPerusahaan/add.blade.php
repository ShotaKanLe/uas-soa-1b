@extends('dashboardPerusahaan.layouts.app')

@section('title', 'Add Consultations Data')

@section('content')

<div class="bg-white rounded-md shadow-sm shadow-blue-100 border border-gray-300 p-6 mb-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
        <h2 class="text-xl font-semibold text-gray-800">Add New Data</h2>
        <a href="{{ route('perjalananKaryawanPerusahaan.index') }}">
            <button class="flex items-center gap-2 px-4 py-2 border border-green-500 text-green-600 rounded-md hover:bg-green-50">
                Back to Dashboard
            </button>
        </a>
    </div>

    <form id="form-id" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4" method="POST" action="{{ route('perjalananKaryawanPerusahaan.store') }}">
        @csrf
        
        <!-- Employee Name -->
        <div>
            <label for="employee_name" class="block text-sm font-medium text-gray-700 mb-1">Employee Name <span class="text-red-500">*</span></label>
            <select id="employee_name" name="employee_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                @foreach ($dataKaryawan as $item)
                    <option {{ old('employee_name') == $item->id_karyawan ? 'selected' : ''}} value="{{$item->id}}">{{$item->nama_karyawan}}</option>
                @endforeach
            </select>
            @error('employee_name')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Transportation -->
        <div>
            <label for="transportation" class="block text-sm font-medium text-gray-700 mb-1">Transportation <span class="text-red-500">*</span></label>
            <select id="transportation" name="transportation" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                @foreach ($dataTransportasi as $item)
                    <option {{ old('transportation') == $item->id_transportasi ? 'selected' : ''}} value="{{$item->id}}">{{$item->nama_transportasi}}</option>
                @endforeach
            </select>
            @error('transportation')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Fuel -->
        <div>
            <label for="fuel" class="block text-sm font-medium text-gray-700 mb-1">Fuel <span class="text-red-500">*</span></label>
            <select id="fuel" name="fuel" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                @foreach ($dataBahanBakar as $item)
                    <option {{ old('fuel') == $item->id_bahan_bakar ? 'selected' : ''}} value="{{$item->id}}">{{$item->nama_bahan_bakar}}</option>
                @endforeach
            </select>
            @error('fuel')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Address -->
        <div>
            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
            <select id="address" name="address" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                @foreach ($dataAlamat as $item)
                    <option {{ old('address') == $item->id_alamat ? 'selected' : ''}} value="{{$item->id}}">{{$item->alamat_rumah}}</option>
                @endforeach
            </select>
            @error('address')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Trip Date -->
        <div>
            <label for="trip_date" class="block text-sm font-medium text-gray-700 mb-1">Trip Date</label>
            <input type="date" id="trip_date" name="trip_date" value="{{ old('trip_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" />
            @error('trip_date')
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

    @if (session('failed'))
        <div class="mb-4 p-4 rounded-md border border-red-300 bg-red-50 text-red-800 shadow-sm flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span>{{ session('failed') }}</span>
        </div>
    @endif

    <!-- Form Actions -->
    <div class="flex justify-end gap-3 pt-6">
        <a href="{{ route('perjalananKaryawanPerusahaan.index') }}">
            <button type="button" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </button>
        </a>
        <button type="submit" form="form-id" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 shadow-sm">
            Add Data
        </button>
    </div>
</div>

@endsection
