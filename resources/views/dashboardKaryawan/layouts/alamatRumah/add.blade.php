@extends('dashboardKaryawan.layouts.app')

@section('title', 'Add Data Company Employees Commute')

@section('content')

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<div class="bg-white rounded-md shadow-sm shadow-blue-100 border border-gray-300 p-6 mb-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
        <h2 class="text-xl font-semibold text-gray-800">Add New Address Data</h2>
        <a href="{{ route('karyawan.alamat.index') }}">
            <button class="flex items-center gap-2 px-4 py-2 border border-green-500 text-green-600 rounded-md hover:bg-green-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </button>
        </a>
    </div>

    <form id="form-id" method="POST" action="{{ route('karyawan.alamat.store') }}" class="space-y-6">
        @csrf


        <!-- Address Selection Section -->
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Home Address Location <span class="text-red-500">*</span>
                </label>
                
                <!-- Address Selection Button -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <button 
                        type="button"
                        onclick="openMapModal()" 
                        class="flex items-center justify-center gap-2 bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-md transition-colors shadow-sm"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Select Home Address
                    </button>
                    
                    <!-- Address Preview -->
                    <div id="address-preview" class="hidden flex-1 p-3 bg-gray-50 border border-gray-200 rounded-md">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Address selected - Coordinates: </span>
                            <span id="coordinates-display" class="font-mono text-xs"></span>
                        </div>
                    </div>
                </div>
                
                <p class="mt-2 text-xs text-gray-500 mt-4">
                    Click the button above to select the employee's home address on the map
                </p>
            </div>

            <!-- Hidden coordinate inputs -->
            <input type="hidden" name="latitude" id="latitude" required>
            <input type="hidden" name="longitude" id="longitude" required>
        </div>
    </form>

    @if (session('success'))
        <div class="mb-4 p-4 mt-2 rounded-md border border-green-300 bg-green-50 text-green-800 shadow-sm flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Form Actions -->
    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200">
        <a href="{{ route('alamatRumah.index') }}">
            <button type="button" class="w-full sm:w-auto px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                Cancel
            </button>
        </a>
        <button 
            type="submit" 
            form="form-id" 
            class="w-full sm:w-auto px-6 py-2 bg-[#39AA80] text-white rounded-md hover:bg-[#2F7B63] shadow-sm transition-colors flex items-center justify-center gap-2"
            id="submit-btn"
        >
            Add Address Data
        </button>
    </div>
</div>

<!-- Enhanced Map Modal -->
<div id="mapModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-4xl h-[85vh] relative flex flex-col overflow-hidden">
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
                    <h3 class="text-xl font-semibold text-gray-800">Select Employee Home Address</h3>
                    <p class="text-sm text-gray-600">Click on the map to mark the location</p>
                </div>
            </div>
            <button onclick="closeMapModal()" class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <!-- Map Container with Relative Positioning for Info Overlay -->
        <div class="relative flex-1 overflow-hidden">
            <div id="map" class="w-full h-full"></div>
            
            <!-- Selected Location Info - Positioned over the map -->
            <div id="location-info" class="hidden absolute top-4 right-4 bg-white bg-opacity-95 backdrop-blur-sm rounded-lg p-3 shadow-lg border z-10">
                <div class="flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="font-medium text-gray-700">Selected Location:</span>
                    <span id="selected-coordinates" class="font-mono text-gray-600"></span>
                </div>
            </div>
        </div>
        
        <!-- Modal Footer - Fixed at bottom -->
        <div class="flex justify-between items-center p-6 border-t border-gray-200 bg-gray-50 flex-shrink-0">
            <div class="text-sm text-gray-600">
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Click anywhere on the map to set the location
                </span>
            </div>
            <div class="flex gap-3">
                <button 
                    onclick="closeMapModal()" 
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button 
                    onclick="submitLocation()" 
                    id="confirm-location-btn"
                    class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    disabled
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Confirm Location
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let map, marker;
    let selectedLat, selectedLng;

    function openMapModal() {
        document.getElementById('mapModal').classList.remove('hidden');
        setTimeout(initMap, 100); // Delay agar modal siap
    }

    function closeMapModal() {
        document.getElementById('mapModal').classList.add('hidden');
    }

    function initMap() {
        if (map) return; // Sudah pernah dibuka
        
        // Initialize map centered on Padang, West Sumatra
        map = L.map('map').setView([-0.9471, 100.4172], 13);

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Add click event to map
        map.on('click', function(e) {
            selectedLat = e.latlng.lat;
            selectedLng = e.latlng.lng;

            // Update or create marker
            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng, {
                    icon: L.icon({
                        iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                        shadowSize: [41, 41]
                    })
                }).addTo(map);
            }

            // Show location info
            const locationInfo = document.getElementById('location-info');
            const coordinatesDisplay = document.getElementById('selected-coordinates');
            coordinatesDisplay.textContent = `${selectedLat.toFixed(6)}, ${selectedLng.toFixed(6)}`;
            locationInfo.classList.remove('hidden');

            // Enable confirm button
            document.getElementById('confirm-location-btn').disabled = false;
        });

        // Force map resize after modal is shown
        setTimeout(() => {
            map.invalidateSize();
        }, 200);
    }

    function submitLocation() {
        if (!selectedLat || !selectedLng) {
            alert("Please select a location on the map first.");
            return;
        }

        // Set hidden input values
        document.getElementById('latitude').value = selectedLat;
        document.getElementById('longitude').value = selectedLng;

        // Show address preview
        const addressPreview = document.getElementById('address-preview');
        const coordinatesDisplay = document.getElementById('coordinates-display');
        coordinatesDisplay.textContent = `${selectedLat.toFixed(6)}, ${selectedLng.toFixed(6)}`;
        addressPreview.classList.remove('hidden');

        // Close modal
        closeMapModal();

        // Show success message
        // showSuccessMessage('Location selected successfully!');
    }

    function showSuccessMessage(message) {
        // Create temporary success message
        const successDiv = document.createElement('div');
        successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2';
        successDiv.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            ${message}
        `;
        
        document.body.appendChild(successDiv);
        
        // Remove after 3 seconds
        setTimeout(() => {
            successDiv.remove();
        }, 3000);
    }

    // Form validation
    document.getElementById('form-id').addEventListener('submit', function(e) {
        const latitude = document.getElementById('latitude').value;
        const longitude = document.getElementById('longitude').value;
        
        if (!latitude || !longitude) {
            e.preventDefault();
            alert('Please select a location on the map before submitting the form.');
            return false;
        }
    });
</script>

<style>
    /* Ensure the modal has proper flexbox layout */
    #mapModal .bg-white {
        display: flex;
        flex-direction: column;
    }
    
    /* Custom modal styles */
    #mapModal {
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
    #map {
        position: relative;
        z-index: 1;
    }
    
    /* Location info overlay */
    #location-info {
        z-index: 1000;
    }
</style>

@endsection