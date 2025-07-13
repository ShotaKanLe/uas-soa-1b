@extends('dashboardPerusahaan.layouts.app')

@section('title', 'Company Employees Address')

@section('content')

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Company Dashboard</h1>
    <p class="text-gray-500">Welcome back, John! Here's what's happening.</p>
</div>

<div 
    class="bg-white rounded-md shadow-sm shadow-blue-100 border border-gray-300 p-6 mb-6"
    x-data="{ 
        showModal: false,
        selectedRow: {},
        confirmDelete: false,
        map: null,
        marker: null
    }"
>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
        <h2 class="text-xl font-semibold text-gray-800">Employee Address</h2>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="(row, index) in filteredData" :key="index">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="row.no"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="row.nama_karyawan"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="row.alamat"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            <div class="flex justify-center">
                                <button 
                                    @click="selectedRow = row; showModal = true; confirmDelete = false; $nextTick(() => { initPreviewMap(); })"
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
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

    <!-- Enhanced Map Modal for Address Preview -->
    <div 
        x-show="showModal" 
        x-transition 
        class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4"
        style="display: none;"
    >
        <div 
            @click.away="showModal = false; confirmDelete = false; destroyMap();"
            class="bg-white rounded-lg shadow-2xl w-full max-w-4xl h-[85vh] relative flex flex-col overflow-hidden"
        >
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800">Employee Address Location</h3>
                        <p class="text-sm text-gray-600">Address location preview</p>
                    </div>
                </div>
                <button @click="showModal = false; confirmDelete = false; destroyMap();" class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Employee and Address Info -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex-shrink-0">
                <div class="flex items-center gap-2 text-sm text-gray-700 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="font-medium">Employee:</span>
                    <span x-text="selectedRow.nama_karyawan"></span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="font-medium">Address:</span>
                    <span x-text="selectedRow.alamat"></span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600 mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3" />
                    </svg>
                    <span class="font-medium">Coordinates:</span>
                    <span class="font-mono" x-text="selectedRow.latitude + ', ' + selectedRow.longitude"></span>
                </div>
            </div>
            
            <!-- Map Container -->
            <div class="relative flex-1 overflow-hidden">
                <div id="previewMap" class="w-full h-full"></div>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex justify-between items-center p-6 border-t border-gray-200 bg-gray-50 flex-shrink-0">
                <div class="text-sm text-gray-600">
                    <span class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Address location preview - Read only
                    </span>
                </div>
                <div class="flex gap-3">
                    <!-- Edit Button -->
                    {{-- <a 
                        :href="'{{ url('/dashboard/perusahaan/alamat/edit') }}/' + selectedRow.id"
                        class="px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold rounded-lg flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a> --}}

                    <!-- Delete Button Group -->
                    <div class="flex gap-2 items-center">
                        <!-- Confirm Prompt -->
                        <template x-if="confirmDelete">
                            <form :action="'{{ url('/dashboard/perusahaan/alamat') }}/' + selectedRow.id" method="POST" class="flex items-center gap-2">
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
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg flex items-center gap-2"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function initPreviewMap() {
        // Wait for modal to be fully rendered
        setTimeout(() => {
            const mapContainer = document.getElementById('previewMap');
            if (!mapContainer) return;

            // Get current component instance
            const component = Alpine.$data(mapContainer.closest('[x-data]'));
            
            // Destroy existing map if it exists
            if (component.map) {
                component.map.remove();
                component.map = null;
                component.marker = null;
            }

            // Get coordinates from selected row
            const lat = parseFloat(component.selectedRow.latitude);
            const lng = parseFloat(component.selectedRow.longitude);

            // Validate coordinates
            if (isNaN(lat) || isNaN(lng)) {
                console.error('Invalid coordinates:', component.selectedRow.latitude, component.selectedRow.longitude);
                mapContainer.innerHTML = '<div class="flex items-center justify-center h-full bg-gray-100"><p class="text-gray-500">Invalid coordinates</p></div>';
                return;
            }

            // Initialize map
            component.map = L.map('previewMap').setView([lat, lng], 15);

            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(component.map);

            // Add marker
            component.marker = L.marker([lat, lng], {
                icon: L.icon({
                    iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                    shadowSize: [41, 41]
                })
            }).addTo(component.map);

            // Add popup to marker
            component.marker.bindPopup(`
                <div class="text-sm">
                    <strong>Employee Address</strong><br>
                    <strong>Name:</strong> ${component.selectedRow.nama_karyawan}<br>
                    <strong>Address:</strong> ${component.selectedRow.alamat}<br>
                    <small class="text-gray-500">
                        Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}
                    </small>
                </div>
            `).openPopup();

            // Force map resize
            setTimeout(() => {
                component.map.invalidateSize();
            }, 100);

        }, 150);
    }

    function destroyMap() {
        // Get current component instance
        const mapContainer = document.getElementById('previewMap');
        if (!mapContainer) return;
        
        const component = Alpine.$data(mapContainer.closest('[x-data]'));
        
        // Destroy existing map
        if (component.map) {
            component.map.remove();
            component.map = null;
            component.marker = null;
        }
    }
</script>

<style>
    /* Custom modal styles */
    .fixed.inset-0 {
        backdrop-filter: blur(4px);
    }
    
    /* Leaflet popup custom styles */
    .leaflet-popup-content-wrapper {
        border-radius: 8px;
    }
    
    /* Loading animation for map */
    .leaflet-container {
        background: #f8f9fa;
    }
    
    /* Ensure map container takes full available space */
    #previewMap {
        position: relative;
        z-index: 1;
    }
    
    /* Ensure modal has proper flexbox layout */
    .bg-white.rounded-lg {
        display: flex;
        flex-direction: column;
    }
</style>

@endsection