<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmpCarbon - Service Registration Success</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.clientKey') }}"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'outfit': ['-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
                    },
                    borderRadius: {
                        'md': '0.375rem',
                    },
                    animation: {
                        'fadeIn': 'fadeIn 0.5s ease-in-out',
                        'slideUp': 'slideUp 0.6s ease-out',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        }
                    }
                },
            },
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            min-height: 100vh;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .success-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.05);
        }

        .code-display {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 2px dashed #10b981;
            position: relative;
            overflow: hidden;
        }

        .code-display::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 70%);
            animation: pulse-slow 3s ease-in-out infinite;
        }

        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }

        .floating-elements::before,
        .floating-elements::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .floating-elements::before {
            top: -100px;
            left: -100px;
            animation-delay: -3s;
        }

        .floating-elements::after {
            bottom: -100px;
            right: -100px;
            animation-delay: -1s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .icon-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        }

        .copy-button {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .copy-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.2);
        }
    </style>
</head>
<body class="font-outfit">
    <div class="floating-elements"></div>
    
    <div x-data="{ 
        code: '{{ $code ?? 'COMP-2024-001' }}',
        copied: false,
        
        copyCode() {
            navigator.clipboard.writeText(this.code).then(() => {
                this.copied = true;
                setTimeout(() => {
                    this.copied = false;
                }, 2000);
            });
        }
    }" x-init="setTimeout(() => { $el.classList.add('animate-fadeIn') }, 100)">
        
        <!-- Header -->
        <header class="bg-white/80 backdrop-blur-sm border-b border-gray-200/50 shadow-sm sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <button class="mr-2 p-2 rounded-md text-gray-600 hover:bg-gray-100 transition-colors">
                            <a href="{{ url('/') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                        </button>
                        <div class="flex items-center">
                            <a href="{{ url('/') }}" class="flex items-center">
                                <div class="h-8 w-8 rounded-md flex items-center justify-center mr-2">
                                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-cover">
                                </div>
                                <span class="font-bold text-xl text-[#39AA80]">ComCarbon</span>
                            </a>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-800">Registration Success</h1>
                    </div>
                    <div class="w-24"></div>
                </div>
            </div>
        </header>

        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Success Card -->
            <div class="success-card rounded-2xl p-8 mb-6 relative overflow-hidden animate-slideUp">
                <!-- Success Icon -->
                <div class="text-center mb-8">
                    <div class="icon-success w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Registration Successful!</h2>
                    <p class="text-gray-600 text-lg">Your company account has been successfully created</p>
                </div>

                <!-- Company Code Section -->
                <div class="mb-8">
                    <div class="text-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Your Company Code</h3>
                        <p class="text-gray-600">Use this code for staff registration in the application</p>
                    </div>
                    
                    <div class="code-display rounded-xl p-6 relative">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="text-center">
                                    <div class="text-3xl font-mono font-bold text-[#39AA80] mb-2" x-text="code"></div>
                                    <p class="text-sm text-gray-500">Company Registration Code</p>
                                </div>
                            </div>
                            <button 
                                @click="copyCode()"
                                class="copy-button ml-4 bg-[#39AA80] hover:bg-[#2d8a64] text-white px-4 py-2 rounded-lg flex items-center gap-2 font-medium"
                            >
                                <svg x-show="!copied" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <svg x-show="copied" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Information Cards -->
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <!-- Email Notification -->
                    {{-- <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                        <div class="flex items-start gap-3">
                            <div class="bg-blue-100 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-blue-900 mb-1">Email Confirmation</h4>
                                <p class="text-sm text-blue-700">Invoice details have been sent to your company email address</p>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Next Steps -->
                    {{-- <div class="bg-green-50 border border-green-200 rounded-xl p-6">
                        <div class="flex items-start gap-3">
                            <div class="bg-green-100 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-green-900 mb-1">Next Steps</h4>
                                <p class="text-sm text-green-700">Create staff accounts using your company code</p>
                            </div>
                        </div>
                    </div> --}}
                </div>

                <!-- Action Button -->
                <div class="text-center">
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-3 gradient-bg text-white px-8 py-4 rounded-xl font-semibold text-lg hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        <span>Create Staff Account Now</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </main>
    </div>

    <!-- Map Modal (keeping original functionality) -->
    <div id="mapModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl shadow-2xl w-[90%] h-[80%] relative overflow-hidden">
            <div class="p-4 font-bold border-b bg-gray-50">Tentukan Lokasi</div>
            <div id="map" class="w-full h-[80%]"></div>
            <div class="p-4 flex justify-end gap-2 border-t bg-gray-50">
                <button onclick="closeMapModal()" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg transition-colors">Batal</button>
                <button onclick="submitLocation()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">OK</button>
            </div>
        </div>
    </div>

    <!-- Status Modal (keeping original functionality) -->
    <div id="status-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white w-full max-w-md rounded-xl shadow-2xl p-6 relative">
            <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
                <h3 class="text-xl font-semibold text-gray-900" id="modal-title">Status</h3>
                <button id="modal-close" class="text-gray-500 hover:text-gray-700 text-2xl leading-none font-semibold">&times;</button>
            </div>
            <p class="text-sm text-gray-700" id="modal-message">Pesan akan muncul di sini.</p>
            <div class="mt-5 text-right">
                <button id="modal-ok" class="bg-[#39AA80] hover:bg-[#2d8a64] text-white px-4 py-2 rounded-lg transition-colors">
                    OK
                </button>
            </div>
        </div>
    </div>

    <!-- Keep all original scripts -->
    <script>
        let map, marker;
        let selectedLat, selectedLng;

        function openMapModal() {
            document.getElementById('mapModal').classList.remove('hidden');
            setTimeout(initMap, 100);
        }

        function closeMapModal() {
            document.getElementById('mapModal').classList.add('hidden');
        }

        function initMap() {
            if (map) return;
            map = L.map('map').setView([-0.9471, 100.4172], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            map.on('click', function(e) {
                selectedLat = e.latlng.lat;
                selectedLng = e.latlng.lng;

                if (marker) {
                    marker.setLatLng(e.latlng);
                } else {
                    marker = L.marker(e.latlng).addTo(map);
                }
            });
        }

        function submitLocation() {
            if (!selectedLat || !selectedLng) {
                alert("Silakan pilih lokasi di peta terlebih dahulu.");
                return;
            }

            document.getElementById('latitude').value = selectedLat;
            document.getElementById('longitude').value = selectedLng;

            closeMapModal();
        }

        function showModal(title, message) {
            const modal = document.getElementById('status-modal');
            const titleEl = document.getElementById('modal-title');
            const messageEl = document.getElementById('modal-message');

            titleEl.textContent = title;
            messageEl.textContent = message;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            const modal = document.getElementById('status-modal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('modal-close').addEventListener('click', closeModal);
            document.getElementById('modal-ok').addEventListener('click', closeModal);
        });
    </script>
</body>
</html>