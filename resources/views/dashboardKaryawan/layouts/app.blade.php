<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="icon" href="{{ asset('favicon_ico.ico') }}" type="image/x-icon">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'outfit': ['Outfit', 'sans-serif'],
                    },
                    borderRadius: {
                        'md': '0.375rem',
                    }
                },
            },
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar - hide default but keep functionality */
        .custom-scrollbar {
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }
        
        .custom-scrollbar::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }
        
        /* Custom green scrollbar for sidebar */
        .custom-scrollbar-green {
            scrollbar-width: thin; /* Firefox */
            scrollbar-color: #10b981 #f3f4f6; /* Firefox */
        }
        
        .custom-scrollbar-green::-webkit-scrollbar {
            width: 6px; /* Width of the scrollbar */
            display: block;
        }
        
        .custom-scrollbar-green::-webkit-scrollbar-track {
            background: #f3f4f6; /* Light gray background */
            border-radius: 10px;
        }
        
        .custom-scrollbar-green::-webkit-scrollbar-thumb {
            background: #10b981; /* Green thumb */
            border-radius: 10px;
            transition: background 0.3s ease;
        }
        
        .custom-scrollbar-green::-webkit-scrollbar-thumb:hover {
            background: #059669; /* Darker green on hover */
        }
        
        body {
            font-family: 'Outfit', sans-serif;
        }

        /* Shadow with blue tint */
        .shadow-blue-100 {
            box-shadow: 0 1px 3px 0 rgba(59, 130, 246, 0.1), 0 1px 2px 0 rgba(59, 130, 246, 0.06);
        }
    </style>
</head>
<body class="bg-gray-50 font-outfit">
    <div x-data="{ 
        isSidebarCollapsed: window.innerWidth < 768,
        isMobileMenuOpen: false,
        showAddForm: false,
        currentPage: 1,
        statusFilter: [],
        chartPeriod: 'week',
        
        toggleStatusFilter(status) {
            if (this.statusFilter.includes(status)) {
                this.statusFilter = this.statusFilter.filter(s => s !== status);
            } else {
                this.statusFilter.push(status);
            }
        },
        
        get filteredData() {
            if (this.statusFilter.length === 0) return this.tableData;
            return this.tableData.filter(row => this.statusFilter.includes(row.status));
        },
        
        @if (isset($data))
        @if ($dataType == 'karyawan')
        tableData: [
            
        ]
        @endif
        @if ($dataType == 'perjalanan')
        tableData: [
            @foreach ($data as $index => $item)
            {
                no: {{ $index + 1 }},
                id: '{{ $item->id }}',
                nama_karyawan: '{{ $item->karyawanPerusahaan->nama_karyawan }}',
                transportasi: '{{ $item->transportasi->nama_transportasi }}',
                bahan_bakar: '{{ $item->bahanBakar->nama_bahan_bakar }}',
                alamat: '{{ $item->alamat->alamat_rumah }}',
                tanggal_perjalanan: '{{ $item->tanggal_perjalanan }}',
                total_co2: '{{ $item->total_co2 }}',
                total_co2e: '{{ $item->total_co2e }}',
                total_ch4: '{{ $item->total_ch4 }}',
                total_n2O: '{{ $item->total_n2O }}',
                total_WTT: '{{ $item->total_WTT }}',
                total_emisi_karbon: '{{ $item->total_emisi_karbon }}',
            }@if (!$loop->last),@endif
            @endforeach
        ],
        @endif
        @if ($dataType == 'alamat')
        tableData: [
            @foreach ($data as $index => $item)
            {
                no: {{ $index + 1 }},
                id: '{{ $item->id }}',
                nama_karyawan: '{{ $item->karyawanPerusahaan->nama_karyawan }}',
                alamat: '{{ $item->alamat_rumah }}',
                longitude: '{{ $item->longitude }}',
                latitude: '{{ $item->latitude }}',
            }@if (!$loop->last),@endif
            @endforeach
        ],
        @endif
        @endif
    
    }"
    x-init="$watch('chartPeriod', () => drawChart()); $nextTick(() => drawChart())">

        <!-- Navbar -->
        @include('dashboardKaryawan.partials.navbar')
        
        <!-- Sidebar for mobile (overlay) -->
        @include('dashboardKaryawan.partials.sidebarMobile')

        <!-- Content area with sidebar -->
        <div class="flex pt-16 min-h-screen">
            <!-- Sidebar (desktop) -->
            @include('dashboardKaryawan.partials.sidebar')

            <!-- Main content -->
            <main 
                class="flex-1 p-6 transition-all duration-300" 
                :class="isSidebarCollapsed ? 'md:ml-16' : 'md:ml-64'"
            >
                <template x-if="!showAddForm">
                    <!-- Dashboard content -->
                    <div>
                        
                        @yield('content')
 
                    </div>
                </template>
            </main>
        </div>
    </div>
</body>
</html>
