@extends('dashboardStaff.layouts.app')

@section('title', 'Fuels')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Staff Dashboard</h1>
    <p class="text-gray-500">Welcome back, Staff! Here's what's happening.</p>
</div>

<div 
    class="bg-white rounded-md shadow-sm shadow-blue-100 border border-gray-300 p-6 mb-6"
    x-data="{ 
        showModal: false,
        selectedRow: {},
    }"
>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
        <h2 class="text-xl font-semibold text-gray-800">Fuels</h2>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('bahanBakar.add') }}">
                <button class="flex items-center gap-2 px-4 py-2 bg-[#39AA80] text-white rounded-md hover:bg-[#2F7B63] border border-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Data
                </button>
            </a>
        </div>
    </div>

    @if (session('success'))
            <div class="mb-4 p-4 rounded-md border border-green-300 bg-green-50 text-green-800 shadow-sm flex items-start gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fuel Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fuel Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost perLiter (IDR)</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="(row, index) in filteredData" :key="index">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="row.no"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="row.nama_bahan_bakar"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="row.jenis_bahan_bakar"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="row.harga_bahan_bakar"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            <div class="flex justify-center">
                                <button 
                                    @click="selectedRow = row; showModal = true"
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded"
                                >
                                    Detail
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    {{ $data->links('vendor.pagination.custom') }}

    <!-- Modal Detail -->
    <div 
        x-show="showModal" 
        x-transition 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        style="display: none;"
    >
        <div 
            @click.away="showModal = false; confirmDelete = false"
            x-data="{ confirmDelete: false }"
            class="bg-white w-full max-w-lg rounded-2xl shadow-xl p-6 relative"
        >
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h3 class="text-2xl font-bold text-gray-800">Fuel Detail</h3>
                <button @click="showModal = false; confirmDelete = false" class="text-gray-500 hover:text-gray-700 text-xl">
                    &times;
                </button>
            </div>

            <!-- Modal Content -->
            <div class="bg-white rounded-xl shadow-md p-6 space-y-6 text-sm text-gray-700">
                <!-- Fuel Information -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 border-b pb-2 mb-4">Fuel Information</h2>
                    <div class="grid grid-cols-2 gap-y-3 gap-x-8">
                        <div>
                            <p class="text-gray-500">Fuel Name</p>
                            <p class="font-medium text-gray-900" x-text="selectedRow.nama_bahan_bakar"></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Fuel Type</p>
                            <p class="font-medium text-gray-900" x-text="selectedRow.nama_bahan_bakar"></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Cost per Liter</p>
                            <p class="font-medium text-gray-900" x-text="selectedRow.nama_bahan_bakar"></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Consumption Rate</p>
                            <p class="font-medium text-gray-900" x-text="selectedRow.rerata_konsumsi_literperkm"></p>
                        </div>
                    </div>
                </div>

                <!-- Emission Data -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 border-b pb-2 mb-4">Emission Data</h2>
                    <div class="grid grid-cols-2 gap-y-3 gap-x-8">
                        <div>
                            <p class="text-gray-500">CO<sub>2</sub></p>
                            <p class="font-medium text-gray-900">
                                <span x-text="selectedRow.co2perliter"></span> kg CO<sub>2</sub>e/L
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">CH<sub>4</sub></p>
                            <p class="font-medium text-gray-900">
                                <span x-text="selectedRow.ch4perliter"></span> kg CO<sub>2</sub>e/L
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">N<sub>2</sub>O</p>
                            <p class="font-medium text-gray-900">
                                <span x-text="selectedRow.n2Operliter"></span> kg CO<sub>2</sub>e/L
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">CO<sub>2</sub>e Total</p>
                            <p class="font-medium text-gray-900">
                                <span x-text="selectedRow.co2eperliter"></span> kg CO<sub>2</sub>e/L
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">WTT</p>
                            <p class="font-medium text-gray-900">
                                <span x-text="selectedRow.WTTperliter"></span> kg CO<sub>2</sub>e/L
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="flex justify-between items-center mt-6 pt-4 border-t">
                <!-- Edit Button -->
                <a 
                    :href="'{{ url('/dashboard/staff/bahanBakar/edit') }}/' + selectedRow.id"
                    class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold rounded-lg"
                >
                    Edit
                </a>

                <!-- Delete Button Group -->
                <div class="flex gap-2 items-center">
                    <!-- Confirm Prompt -->
                    <template x-if="confirmDelete">
                        <form :action="'{{ url('/dashboard/staff/bahanBakar') }}/' + selectedRow.id" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('DELETE')

                            <span class="text-sm text-red-500">Are you sure?</span>

                            <button 
                                type="submit"
                                class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-md"
                            >
                                Yes, Delete
                            </button>

                            <button 
                                type="button"
                                @click="confirmDelete = false"
                                class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-md"
                            >
                                Cancel
                            </button>
                        </form>
                    </template>

                    <!-- Trigger Delete -->
                    <template x-if="!confirmDelete">
                        <button 
                            @click="confirmDelete = true"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg"
                        >
                            Delete
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
