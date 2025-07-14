@extends('dashboardStaff.layouts.app')

@section('title', 'Edit Data Fuels')

@section('content')

<div class="bg-white rounded-md shadow-sm shadow-blue-100 border border-gray-300 p-6 mb-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
        <h2 class="text-xl font-semibold text-gray-800">Edit Data</h2>
        <a href="{{ route('bahanBakar.index') }}">
            <button class="flex items-center gap-2 px-4 py-2 border border-green-500 text-green-600 rounded-md hover:bg-green-50">
                Back to Dashboard
            </button>
        </a>
    </div>

    <form id="form-id" method="POST" action="{{ route('bahanBakar.update', ['id' => $oldData->id]) }}" enctype="multipart/form-data"
    class="bg-white p-6 rounded-xl shadow-md space-y-8">
    @csrf
    @method('PUT')
    <!-- Grid: Input Fields -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Fuel Name -->
        <div>
            <label for="fuel_name" class="block text-sm font-medium text-gray-700 mb-1">
                Fuel Name <span class="text-red-500">*</span>
            </label>
            <input type="text" id="fuel_name" name="fuel_name" value="{{ $oldData->nama_bahan_bakar }}" required
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
                        <option value="Gasoline" {{ ($oldData->jenis_bahan_bakar ?? '') == 'Gasoline' ? 'selected' : '' }}>Gasoline (Bensin)</option>
                        <option value="Diesel" {{ ($oldData->jenis_bahan_bakar ?? '') == 'Diesel' ? 'selected' : '' }}>Diesel (Solar)</option>
                        <option value="Kerosene" {{ ($oldData->jenis_bahan_bakar ?? '') == 'Kerosene' ? 'selected' : '' }}>Kerosene / Avtur</option>
                        <option value="Fuel Oil" {{ ($oldData->jenis_bahan_bakar ?? '') == 'Fuel Oil' ? 'selected' : '' }}>Fuel Oil</option>
                        <option value="Naphtha" {{ ($oldData->jenis_bahan_bakar ?? '') == 'Naphtha' ? 'selected' : '' }}>Naphtha</option>
                        <option value="Heavy Fuel Oil" {{ ($oldData->jenis_bahan_bakar ?? '') == 'Heavy Fuel Oil' ? 'selected' : '' }}>Heavy Fuel Oil (HFO)</option>
                        <option value="Natural Gas" {{ ($oldData->jenis_bahan_bakar ?? '') == 'Natural Gas' ? 'selected' : '' }}>Natural Gas</option>
                        <option value="LPG" {{ ($oldData->jenis_bahan_bakar ?? '') == 'LPG' ? 'selected' : '' }}>LPG</option>
                        <option value="CNG" {{ ($oldData->jenis_bahan_bakar ?? '') == 'CNG' ? 'selected' : '' }}>CNG</option>
                        <option value="Coal" {{ ($oldData->jenis_bahan_bakar ?? '') == 'Coal' ? 'selected' : '' }}>Coal (Batu Bara)</option>
                        <option value="Lignite" {{ ($oldData->jenis_bahan_bakar ?? '') == 'Lignite' ? 'selected' : '' }}>Lignite</option>
                        <option value="Peat" {{ ($oldData->jenis_bahan_bakar ?? '') == 'Peat' ? 'selected' : '' }}>Peat (Gambut)</option>
                    </optgroup>
                    <optgroup label="Biofuels">
                        <option value="Bioethanol" {{ ($oldData->jenis_bahan_bakar ?? '') == 'Bioethanol' ? 'selected' : '' }}>Bioethanol</option>
                        <option value="Biodiesel" {{ ($oldData->jenis_bahan_bakar ?? '') == 'Biodiesel' ? 'selected' : '' }}>Biodiesel</option>
                        <option value="HVO" {{ ($oldData->jenis_bahan_bakar ?? '') == 'HVO' ? 'selected' : '' }}>HVO</option>
                        <option value="Biogas" {{ ($oldData->jenis_bahan_bakar ?? '') == 'Biogas' ? 'selected' : '' }}>Biogas</option>
                        <option value="Syngas" {{ ($oldData->jenis_bahan_bakar ?? '') == 'Syngas' ? 'selected' : '' }}>Syngas</option>
                        <option value="Wood Biomass" {{ ($oldData->jenis_bahan_bakar ?? '') == 'Wood Biomass' ? 'selected' : '' }}>Wood Biomass</option>
                        <option value="Wood Pellets" {{ ($oldData->jenis_bahan_bakar ?? '') == 'Wood Pellets' ? 'selected' : '' }}>Wood Pellets</option>
                        <option value="Bagasse" {{ ($oldData->jenis_bahan_bakar ?? '') == 'Bagasse' ? 'selected' : '' }}>Bagasse</option>
                    </optgroup>
                    <optgroup label="Alternative / Synthetic Fuels">
                        <option value="Hydrogen" {{ ($oldData->jenis_bahan_bakar ?? '') == 'Hydrogen' ? 'selected' : '' }}>Hydrogen</option>
                        <option value="Ammonia" {{ ($oldData->jenis_bahan_bakar ?? '') == 'Ammonia' ? 'selected' : '' }}>Ammonia</option>
                        <option value="E-Fuel" {{ ($oldData->jenis_bahan_bakar ?? '') == 'E-Fuel' ? 'selected' : '' }}>E-Fuel</option>
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

        <!-- Cost -->
        <div>
            <label for="cost" class="block text-sm font-medium text-gray-700 mb-1">
                Cost per-Liter (IDR) <span class="text-red-500">*</span>
            </label>
            <input type="text" id="cost" name="cost" value="{{ $oldData->harga_bahan_bakar_per_liter }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                placeholder="10000 (IDR)" />
            @error('cost')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- CO2 per-Liter -->
        <div>
            <label for="co2perliter" class="block text-sm font-medium text-gray-700 mb-1">
                CO2 per-Liter <span class="text-red-500">*</span>
            </label>
            <input type="text" id="co2perliter" name="co2perliter" value="{{ $oldData->co2perliter ?? '' }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                placeholder="0.5 (kg CO2e/L)" />
            @error('co2perliter')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- CH4 per-Liter -->
        <div>
            <label for="ch4perliter" class="block text-sm font-medium text-gray-700 mb-1">
                CH4 per-Liter <span class="text-red-500">*</span>
            </label>
            <input type="text" id="ch4perliter" name="ch4perliter" value="{{ $oldData->ch4perliter ?? '' }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                placeholder="0.02 (kg CH4e/L)" />
            @error('ch4perliter')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- N2O per-Liter -->
        <div>
            <label for="n2Operliter" class="block text-sm font-medium text-gray-700 mb-1">
                N2O per-Liter <span class="text-red-500">*</span>
            </label>
            <input type="text" id="n2Operliter" name="n2Operliter" value="{{ $oldData->n2Operliter ?? '' }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                placeholder="0.01 (kg N2Oe/L)" />
            @error('n2Operliter')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- WTT per-Liter -->
        <div>
            <label for="WTTperliter" class="block text-sm font-medium text-gray-700 mb-1">
                WTT per-Liter <span class="text-red-500">*</span>
            </label>
            <input type="text" id="WTTperliter" name="WTTperliter" value="{{ $oldData->WTTperliter ?? '' }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                placeholder="0.03 (kg CO2e/L WTT)" />
            @error('WTTperliter')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- WTT per-Liter -->
        <div>
            <label for="rerata_konsumsi_literperkm" class="block text-sm font-medium text-gray-700 mb-1">
                Consumption Rate (l/km) <span class="text-red-500">*</span>
            </label>
            <input type="text" id="rerata_konsumsi_literperkm" name="rerata_konsumsi_literperkm" value="{{ $oldData->rerata_konsumsi_literperkm ?? '' }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                placeholder="0.03 (kg CO2e/L WTT)" />
            @error('rerata_konsumsi_literperkm')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
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
        <a href="{{ route('bahanBakar.index') }}">
            <button type="button" class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                Cancel
            </button>
        </a>
        <button type="submit" class="px-4 py-2 bg-[#39AA80] text-white text-sm rounded-md hover:bg-[#2F7B63] shadow-sm">
            Update Data
        </button>
    </div>
</form>

</div>

@endsection