@extends('dashboardKaryawan.layouts.app')

@section('title', 'Employees')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Employee Dashboard</h1>
    <p class="text-gray-500">Welcome back, Employee! Here's what's happening.</p>
</div>

<div 
    class="bg-white rounded-md shadow-sm shadow-blue-100 border border-gray-300 p-6 mb-6"
    x-data="{ 
        showModal: false,
        selectedRow: {},
    }"
>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
        <h2 class="text-xl font-semibold text-gray-800">Employee Commutes</h2>
        <div class="flex flex-wrap gap-2">
            <!-- Tombol Absen -->
                <button 
                    @if($sudahAbsen != null) disabled @endif
                    onclick="openAbsenModal()" 
                    class="inline-flex @if($sudahAbsen != null) opacity-50 cursor-not-allowed @endif items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded transition"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 2a8 8 0 108 8 8 8 0 00-8-8zM9 11V7h2v4zm0 2h2v2H9z" />
                    </svg>
                    Take Attedance
                </button>


                {{-- <!-- Filter atau info tambahan -->
                <select 
                    class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="today">Hari Ini</option>
                    <option value="week">Minggu Ini</option>
                    <option value="month">Bulan Ini</option>
                </select> --}}

                <!-- Status Absensi Hari Ini -->
                @if($sudahAbsen != null)
                <span class="px-3 py-1.5 bg-blue-100 text-blue-700 text-sm rounded-md font-medium">
                    Status : Attedance Taken
                </span>
                @else
                <span class="px-3 py-1.5 bg-blue-100 text-blue-700 text-sm rounded-md font-medium">
                    Status : Attedance Has Not Beeen Taken
                </span>
                @endif
        </div>
    </div>

    {{-- Success Message --}}
        @if (session('success'))
        <div class="mb-6 p-4 rounded-lg border border-green-400 bg-green-50 text-green-800 flex items-center gap-3 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        @endif

    {{-- No Data Message --}}
    <div x-show="filteredData.length === 0" class="text-center text-gray-500 py-12 text-lg font-medium">
        No data have been founded
    </div>

    {{-- Data Table --}}
    <div x-show="filteredData.length > 0" class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Transportation</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fuel</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Carbon Emissions (Kg CO2e)</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Trip Date</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="(row, index) in filteredData" :key="index">
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="row.no"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700" x-text="row.transportasi"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700" x-text="row.bahan_bakar"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700" x-text="row.total_emisi_karbon"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700" x-text="row.tanggal_perjalanan"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                            <button 
                                @click="selectedRow = row; showModal = true; confirmDelete = false"
                                class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md transition"
                            >
                                Detail
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $data->links('vendor.pagination.custom') }}
    </div>

    {{-- <div class="overflow-x-auto">
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

    {{ $data->links('vendor.pagination.custom') }} --}}

    <!-- Modal Detail -->
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
            <h3 class="text-2xl font-bold text-gray-800">Trip Detail</h3>
            <button @click="showModal = false; confirmDelete = false" class="text-gray-500 hover:text-gray-700 text-xl">
                &times;
            </button>
        </div>

        <!-- Modal Content -->
        <div class="bg-white rounded-xl shadow-md p-6 space-y-6 text-sm text-gray-700">
            <!-- Trip Information -->
            <div>
                {{-- <h2 class="text-lg font-semibold text-gray-900 border-b pb-2 mb-4">Trip Information</h2> --}}
                <div class="grid grid-cols-2 gap-y-3 gap-x-8">
                    <div>
                        <p class="text-gray-500">Transportation</p>
                        <p class="font-medium text-gray-900" x-text="selectedRow.transportasi"></p>
                    </div>
                    <div>
                        <p class="text-gray-500">Fuel</p>
                        <p class="font-medium text-gray-900" x-text="selectedRow.bahan_bakar"></p>
                    </div>
                    <div>
                        <p class="text-gray-500">Address</p>
                        <p class="font-medium text-gray-900" x-text="selectedRow.alamat"></p>
                    </div>
                    <div>
                        <p class="text-gray-500">Trip Date</p>
                        <p class="font-medium text-gray-900" x-text="selectedRow.tanggal_perjalanan"></p>
                    </div>
                </div>
            </div>

            <!-- Emission Data -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 border-b pb-2 mb-4">Emission Data</h2>
                <div class="grid grid-cols-2 gap-y-3 gap-x-8">
                    <div>
                        <p class="text-gray-500">Total CO<sub>2</sub></p>
                        <p class="font-medium text-gray-900">
                            <span x-text="selectedRow.total_co2"></span> kg CO<sub>2</sub>e
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500">Total CH<sub>4</sub></p>
                        <p class="font-medium text-gray-900">
                            <span x-text="selectedRow.total_ch4"></span> kg CO<sub>2</sub>e
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500">Total N<sub>2</sub>O</p>
                        <p class="font-medium text-gray-900">
                            <span x-text="selectedRow.total_n2O"></span> kg CO<sub>2</sub>e
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500">Total CO<sub>2</sub>e</p>
                        <p class="font-medium text-gray-900">
                            <span x-text="selectedRow.total_co2e"></span> kg CO<sub>2</sub>e
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500">Total WTT</p>
                        <p class="font-medium text-gray-900">
                            <span x-text="selectedRow.total_WTT"></span> kg CO<sub>2</sub>e
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500">Total Carbon Emission</p>
                        <p class="font-medium text-gray-900">
                            <span x-text="selectedRow.total_emisi_karbon"></span> kg CO<sub>2</sub>e
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Actions -->
        <div class="flex justify-between items-center mt-6 pt-4 border-t">
            <!-- Edit Button -->
            <a 
                :href="'{{ url('/dashboard/karyawan/perjalanan/edit') }}/' + selectedRow.id"
                class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold rounded-lg"
            >
                Edit
            </a>

            <!-- Delete Button Group -->
            {{-- <div class="flex gap-2 items-center">
                <!-- Confirm Prompt -->
                <template x-if="confirmDelete">
                    <form :action="'{{ url('/dashboard/karyawan/perjalanan') }}/' + selectedRow.id" method="POST" class="flex items-center gap-2">
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
            </div> --}}
        </div>
    </div>
</div> 

</div>

<!-- Modal Form Absen -->
<div 
    id="modalFormAbsen"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50"
>
    <div 
        class="bg-white w-full max-w-xl max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl p-6 relative"
    >
        <!-- Modal Header -->
        <div class="flex justify-between items-center mb-4 border-b pb-3">
            <h3 class="text-xl font-bold text-gray-800">Attendance Form</h3>
            <button 
                onclick="closeAbsenModal()" 
                class="text-gray-400 hover:text-gray-600 text-2xl font-bold focus:outline-none"
            >
                &times;
            </button>
        </div>

        <!-- Modal Form Content -->
        <form action="{{ route('absen') }}" method="POST" class="space-y-4 text-sm text-gray-700">
            @csrf

            <!-- Select Transportasi -->
            <div>
                <label for="transportasi" class="block font-semibold text-gray-800 mb-1">Transportation</label>
                <select name="transportasi" id="transportasi" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Transportation</option>
                    @foreach ($dataTransportasi as $transportasi)
                        <option value="{{ $transportasi->id }}">{{ $transportasi->nama_transportasi }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Select Bahan Bakar -->
            <div>
                <label for="bahan_bakar" class="block font-semibold text-gray-800 mb-1">Fuel</label>
                <select name="bahan_bakar" id="bahan_bakar" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Fuel</option>
                    @foreach ($dataBahanBakar as $bahanBakar)
                        <option value="{{ $bahanBakar->id }}">{{ $bahanBakar->nama_bahan_bakar}}</option>
                    @endforeach
                </select>
            </div>

            <!-- Select Alamat Rumah -->
            <div>
                <label for="alamat_rumah" class="block font-semibold text-gray-800 mb-1">Address</label>
                <select name="alamat_rumah" id="alamat_rumah" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Address</option>
                    @foreach ($dataAlamat as $alamat)
                        <option value="{{ $alamat->id }}">{{ $alamat->alamat_rumah }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end mt-6 pt-4 border-t">
                <button 
                    type="submit"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-md transition"
                >
                    Take Attendance
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAbsenModal() {
        document.getElementById('modalFormAbsen').classList.remove('hidden');
        document.getElementById('modalFormAbsen').classList.add('flex');
    }

    function closeAbsenModal() {
        document.getElementById('modalFormAbsen').classList.add('hidden');
        document.getElementById('modalFormAbsen').classList.remove('flex');
    }

    // Tutup modal saat klik di luar konten
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('modalFormAbsen');
        if (e.target === modal) {
            closeAbsenModal();
        }
    });
</script>


@endsection
