@extends('dashboardPerusahaan.layouts.app')

@section('title', 'Employees')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Company Dashboard</h1>
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
        <h2 class="text-xl font-semibold text-gray-800">Employees</h2>
        <div class="flex flex-wrap gap-2">
            
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 mt-2 rounded-md border border-green-300 bg-green-50 text-green-800 shadow-sm flex items-start gap-3">
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="(row, index) in filteredData" :key="index">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="row.no"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="row.nama_karyawan"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="row.jabatan"></td>
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
            class="bg-white w-full max-w-xl max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl p-6 relative"
        >
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-4 border-b pb-3">
                <h3 class="text-xl font-bold text-gray-800">Employee Detail</h3>
                <button 
                    @click="showModal = false; confirmDelete = false" 
                    class="text-gray-400 hover:text-gray-600 text-2xl font-bold focus:outline-none"
                >
                    &times;
                </button>
            </div>

            <!-- Modal Content -->
            <div class="space-y-5 text-sm text-gray-700">
    <!-- Employee Name -->
    <div>
        <span class="block font-semibold text-gray-800">Employee Name:</span>
        <span x-text="selectedRow.nama_karyawan" class="block mt-1 text-gray-900"></span>
    </div>

    <!-- Position -->
    <div>
        <span class="block font-semibold text-gray-800">Position:</span>
        <span x-text="selectedRow.jabatan" class="block mt-1 text-gray-900"></span>
    </div>

    <!-- Email -->
    <div>
        <span class="block font-semibold text-gray-800">Email:</span>
        <span x-text="selectedRow.email" class="block mt-1 whitespace-pre-line text-gray-900"></span>
    </div>

    <!-- Gender -->
    <div>
        <span class="block font-semibold text-gray-800 mb-1">Gender:</span>
        <template x-if="selectedRow.jenis_kelamin === 'L'">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                Male
            </span>
        </template>
        <template x-if="selectedRow.jenis_kelamin === 'P'">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                Female
            </span>
        </template>
        <template x-if="selectedRow.jenis_kelamin !== 'L' && selectedRow.jenis_kelamin !== 'P'">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                Unknown
            </span>
        </template>
    </div>

    <!-- Date of Birth -->
    <div>
        <span class="block font-semibold text-gray-800">Date of Birth:</span>
        <span x-text="selectedRow.tanggal_lahir" class="block mt-1 text-gray-900"></span>
    </div>

    <!-- Image -->
    <template x-if="selectedRow.gambar_informasi">
        <div>
            <span class="block font-semibold text-gray-800 mb-1">Image:</span>
            <img :src="'/informasi_images/' + selectedRow.gambar_informasi"
                class="w-40 h-40 object-cover rounded-lg border border-gray-300 shadow-sm" alt="Image">
        </div>
    </template>
</div>


            <!-- Modal Actions -->
            <div class="flex justify-between items-center mt-6 pt-4 border-t">
                <!-- Edit Button -->
                <a 
                    :href="'{{ url('/dashboard/perusahaan/karyawan/edit') }}/' + selectedRow.id"
                    class="px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold rounded-md transition"
                >
                    Edit
                </a>

                <!-- Delete Button Group -->
                <div class="flex gap-2 items-center">
                    <!-- Confirm Prompt -->
                    <template x-if="confirmDelete">
                        <form 
                            :action="'{{ url('/dashboard/perusahaan/karyawan') }}/' + selectedRow.id" 
                            method="POST" 
                            class="flex items-center gap-2"
                        >
                            @csrf
                            @method('DELETE')

                            <span class="text-sm text-red-500 font-medium">Are you sure?</span>

                            <button 
                                type="submit"
                                class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md transition"
                            >
                                Yes, Delete
                            </button>

                            <button 
                                type="button"
                                @click="confirmDelete = false"
                                class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm rounded-md transition"
                            >
                                Cancel
                            </button>
                        </form>
                    </template>

                    <!-- Trigger Delete -->
                    <template x-if="!confirmDelete">
                        <button 
                            @click="confirmDelete = true"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-md transition"
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
