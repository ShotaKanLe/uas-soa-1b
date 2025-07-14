@extends('dashboardStaff.layouts.app')

@section('title', 'Add Data Fuels')

@section('content')

<div class="bg-white rounded-md shadow-sm shadow-blue-100 border border-gray-300 p-6 mb-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
        <h2 class="text-xl font-semibold text-gray-800">Add New Data</h2>
        <a href="{{ route('bahanBakar.index') }}">
            <button class="flex items-center gap-2 px-4 py-2 border border-green-500 text-green-600 rounded-md hover:bg-green-50">
                Back to Dashboard
            </button>
        </a>
    </div>

    <form id="form-id" method="POST" action="{{ route('bahanBakar.store') }}" enctype="multipart/form-data"
    class="bg-white p-6 rounded-xl shadow-md space-y-8">
    @csrf
    <!-- Grid: Input Fields -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Fuel Name -->
        <div>
            <label for="fuel_name" class="block text-sm font-medium text-gray-700 mb-1">
                Fuel Name <span class="text-red-500">*</span>
            </label>
            <input type="text" id="fuel_name" name="fuel_name" value="{{ old('fuel_name') }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                placeholder="Fuel Name" />
            @error('fuel_name')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Fuel Type -->
        <div class="relative">
            <label for="fuel_type" class="block text-sm font-medium text-gray-700 mb-1">
                Fuel Type <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <select id="fuel_type" name="fuel_type" required
                    class="appearance-none w-full px-4 py-2 pr-10 border border-gray-300 rounded-md shadow-sm bg-white text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">Select Fuel Type</option>

                    <optgroup label="Fossil Fuels">
                        <option value="Gasoline" {{ old('fuel_type') == 'Gasoline' ? 'selected' : '' }}>Gasoline (Bensin)</option>
                        <option value="Diesel" {{ old('fuel_type') == 'Diesel' ? 'selected' : '' }}>Diesel (Solar)</option>
                        <option value="Kerosene" {{ old('fuel_type') == 'Kerosene' ? 'selected' : '' }}>Kerosene / Avtur</option>
                        <option value="Fuel Oil" {{ old('fuel_type') == 'Fuel Oil' ? 'selected' : '' }}>Fuel Oil</option>
                        <option value="Naphtha" {{ old('fuel_type') == 'Naphtha' ? 'selected' : '' }}>Naphtha</option>
                        <option value="Heavy Fuel Oil" {{ old('fuel_type') == 'Heavy Fuel Oil' ? 'selected' : '' }}>Heavy Fuel Oil (HFO)</option>
                        <option value="Natural Gas" {{ old('fuel_type') == 'Natural Gas' ? 'selected' : '' }}>Natural Gas</option>
                        <option value="LPG" {{ old('fuel_type') == 'LPG' ? 'selected' : '' }}>LPG</option>
                        <option value="CNG" {{ old('fuel_type') == 'CNG' ? 'selected' : '' }}>CNG</option>
                        <option value="Coal" {{ old('fuel_type') == 'Coal' ? 'selected' : '' }}>Coal (Batu Bara)</option>
                        <option value="Lignite" {{ old('fuel_type') == 'Lignite' ? 'selected' : '' }}>Lignite</option>
                        <option value="Peat" {{ old('fuel_type') == 'Peat' ? 'selected' : '' }}>Peat (Gambut)</option>
                    </optgroup>
                    <optgroup label="Biofuels">
                        <option value="Bioethanol" {{ old('fuel_type') == 'Bioethanol' ? 'selected' : '' }}>Bioethanol</option>
                        <option value="Biodiesel" {{ old('fuel_type') == 'Biodiesel' ? 'selected' : '' }}>Biodiesel</option>
                        <option value="HVO" {{ old('fuel_type') == 'HVO' ? 'selected' : '' }}>HVO</option>
                        <option value="Biogas" {{ old('fuel_type') == 'Biogas' ? 'selected' : '' }}>Biogas</option>
                        <option value="Syngas" {{ old('fuel_type') == 'Syngas' ? 'selected' : '' }}>Syngas</option>
                        <option value="Wood Biomass" {{ old('fuel_type') == 'Wood Biomass' ? 'selected' : '' }}>Wood Biomass</option>
                        <option value="Wood Pellets" {{ old('fuel_type') == 'Wood Pellets' ? 'selected' : '' }}>Wood Pellets</option>
                        <option value="Bagasse" {{ old('fuel_type') == 'Bagasse' ? 'selected' : '' }}>Bagasse</option>
                    </optgroup>
                    <optgroup label="Alternative / Synthetic Fuels">
                        <option value="Hydrogen" {{ old('fuel_type') == 'Hydrogen' ? 'selected' : '' }}>Hydrogen</option>
                        <option value="Ammonia" {{ old('fuel_type') == 'Ammonia' ? 'selected' : '' }}>Ammonia</option>
                        <option value="E-Fuel" {{ old('fuel_type') == 'E-Fuel' ? 'selected' : '' }}>E-Fuel</option>
                    </optgroup>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 20 20" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7l3-3 3 3m0 6l-3 3-3-3" />
                    </svg>
                </div>
            </div>
            @error('fuel_type')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Other Inputs -->
        @foreach ([
            ['id' => 'cost', 'label' => 'Cost per-Liter (IDR)', 'placeholder' => '10000 (IDR)'],
            ['id' => 'co2perliter', 'label' => 'CO2 per-Liter', 'placeholder' => '0.5 (kg CO2e/L)'],
            ['id' => 'ch4perliter', 'label' => 'CH4 per-Liter', 'placeholder' => '0.02 (kg CH4e/L)'],
            ['id' => 'n2Operliter', 'label' => 'N2O per-Liter', 'placeholder' => '0.01 (kg N2Oe/L)'],
            ['id' => 'WTTperliter', 'label' => 'WTT per-Liter', 'placeholder' => '0.03 (kg CO2e/L WTT)'],
            ['id' => 'rerata_konsumsi_literperkm', 'label' => 'Comsumption Rate', 'placeholder' => '0.03 (l/km)'],
        ] as $field)
        <div>
            <label for="{{ $field['id'] }}" class="block text-sm font-medium text-gray-700 mb-1">
                {{ $field['label'] }} <span class="text-red-500">*</span>
            </label>
            <input type="text" id="{{ $field['id'] }}" name="{{ $field['id'] }}" value="{{ old($field['id']) }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                placeholder="{{ $field['placeholder'] }}" />
            @error($field['id'])
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
        @endforeach
    </div>

    <!-- Notifikasi Sukses -->
    @if (session('success'))
    <div class="flex items-start gap-3 p-4 bg-green-50 border border-green-200 text-green-800 rounded-md shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Tombol Aksi -->
    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
        <a href="{{ route('perjalananKaryawanPerusahaan.index') }}">
            <button type="button" class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                Cancel
            </button>
        </a>
        <button type="submit" class="px-4 py-2 bg-[#39AA80] text-white text-sm rounded-md hover:bg-[#2F7B63] shadow-sm">
            Add Data
        </button>
    </div>
</form>

</div>

@endsection
