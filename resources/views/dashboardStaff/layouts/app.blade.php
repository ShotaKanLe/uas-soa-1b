<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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
        @if ($dataType == 'perusahaan')
        tableData: [
            @foreach ($data as $index => $item)
            {
                no: {{ $index + 1 }},
                id: '{{ $item->id }}',
                nama_perusahaan: '{{ $item->nama_perusahaan }}',
                nama_service: '{{ $item->service->nama_service }}',
                tgl_aktif: '{{ $item->tanggal_aktif_service }}',
                alamat: '{{ $item->alamat }}',
            }@if (!$loop->last),@endif
            @endforeach
        ],
        @endif
        @if ($dataType == 'service')
        tableData: [
            @foreach ($data as $index => $item)
            {
                no: {{ $index + 1 }},
                id: '{{ $item->id }}',
                nama_service: '{{ $item->nama_service }}',
                durasi_service: '{{ $item->durasi_service }} Days',
                harga_service: 'Rp. {{ $item->harga_service }}',
                pembuat_service: '{{ $item->staffMitra->nama_staff }}',
                deskripsi_service: '{{ $item->deskripsi_service }}',
            }@if (!$loop->last),@endif
            @endforeach
        ],
        @endif
        @if ($dataType == 'bahanBakar')
        tableData: [
            @foreach ($data as $index => $item)
            {
                no: {{ $index + 1 }},
                id: '{{ $item->id }}',
                nama_bahan_bakar: '{{ $item->nama_bahan_bakar }}',
                jenis_bahan_bakar: '{{ $item->jenis_bahan_bakar }}',
                co2perliter: '{{ $item->co2perliter }}',
                ch4perliter: '{{ $item->ch4perliter }}',
                n2Operliter: '{{ $item->n2Operliter }}',
                co2eperliter: '{{ $item->Co2eperliter }}',
                WTTperliter: '{{ $item->WTTperliter }}',
                rerata_konsumsi_literperkm: '{{ $item->rerata_konsumsi_literperkm }} l/km',
                harga_bahan_bakar: 'Rp. {{ $item->harga_bahan_bakar_per_liter }}',
                created_at: '{{ $item->created_at }}',
                updated_at: '{{ $item->updated_at }}',
            }@if (!$loop->last),@endif
            @endforeach
        ],
        @endif
        @if ($dataType == 'informasi')
        tableData: [
            @foreach ($data as $index => $item)
            {
                no: {{ $index + 1 }},
                id: '{{ $item->id }}',
                judul_informasi: '{{ $item->judul_informasi }}',
                nama_staff: '{{ $item->staffMitra->nama_staff }}',
                isi_informasi: '{{ $item->isi_informasi }}',
                tag: '{{ $item->tag }}',
                gambar_informasi: '{{ $item->gambar_informasi }}',
                created_at: '{{ $item->created_at }}',
                updated_at: '{{ $item->updated_at }}',
            }@if (!$loop->last),@endif
            @endforeach
        ],
        @endif
        @if ($dataType == 'konsultasi')
        tableData: [
            @foreach ($data as $index => $item)
            {
                no: {{ $index + 1 }},
                id: '{{ $item->id }}',
                nama_perusahaan: '{{ $item->perusahaan->nama_perusahaan }}',
                tanggal_konsultasi: '{{ $item->tanggal_konsultasi }}',
                isi_konsultasi: '{{ $item->isi_konsultasi }}',
                id_hasil_analisis: '{{ $item->id_hasil_analisis }}',
                nama_konsultasi: '{{ $item->nama_konsultasi }}',
                status_konsultasi: '{{ $item->status_konsultasi }}',
                nama_analisis: '{{ $item->hasilAnalisisEmisi->nama_analisis }}',
                created_at: '{{ $item->created_at }}',
                updated_at: '{{ $item->updated_at }}',
            }@if (!$loop->last),@endif
            @endforeach
        ],
        @endif
        @endif
        
        // Chart data
        chartData: {
            day: {
                labels: ['12AM', '3AM', '6AM', '9AM', '12PM', '3PM', '6PM', '9PM'],
                values: [12, 19, 15, 25, 32, 38, 33, 28]
            },
            week: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                values: [28, 32, 36, 30, 25, 40, 35]
            },
            month: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                values: [85, 100, 90, 120]
            },
            year: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                values: [200, 190, 210, 240, 280, 250, 290, 300, 270, 320, 310, 350]
            }
        },
        
        drawChart() {
            this.$nextTick(() => {
                const canvas = this.$refs.chartCanvas;
                if (!canvas) return;
                
                const ctx = canvas.getContext('2d');
                if (!ctx) return;
                
                // Clear canvas
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                // Set canvas dimensions
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width;
                canvas.height = rect.height;
                
                // Chart dimensions
                const padding = 40;
                const chartWidth = rect.width - padding * 2;
                const chartHeight = rect.height - padding * 2;
                
                // Get data for the selected period
                const data = this.chartData[this.chartPeriod];
                const { labels, values } = data;
                
                // Find max value for scaling
                const maxValue = Math.max(...values);
                
                // Draw axes
                ctx.beginPath();
                ctx.strokeStyle = '#e5e7eb'; // gray-200
                ctx.lineWidth = 1;
                ctx.moveTo(padding, padding);
                ctx.lineTo(padding, chartHeight + padding);
                ctx.lineTo(chartWidth + padding, chartHeight + padding);
                ctx.stroke();
                
                // Draw grid lines
                const gridLines = 5;
                ctx.beginPath();
                ctx.strokeStyle = '#f3f4f6'; // gray-100
                ctx.lineWidth = 1;
                for (let i = 1; i <= gridLines; i++) {
                    const y = padding + (chartHeight / gridLines) * i;
                    ctx.moveTo(padding, y);
                    ctx.lineTo(chartWidth + padding, y);
                }
                ctx.stroke();
                
                // Draw data points and lines
                const pointSpacing = chartWidth / (labels.length - 1);
                
                // Draw line
                ctx.beginPath();
                ctx.strokeStyle = '#10b981'; // green-500
                ctx.lineWidth = 2;
                ctx.moveTo(padding, chartHeight + padding - (values[0] / maxValue) * chartHeight);
                
                for (let i = 1; i < labels.length; i++) {
                    const x = padding + pointSpacing * i;
                    const y = chartHeight + padding - (values[i] / maxValue) * chartHeight;
                    ctx.lineTo(x, y);
                }
                ctx.stroke();
                
                // Draw points
                for (let i = 0; i < labels.length; i++) {
                    const x = padding + pointSpacing * i;
                    const y = chartHeight + padding - (values[i] / maxValue) * chartHeight;
                    
                    ctx.beginPath();
                    ctx.fillStyle = '#ffffff'; // white
                    ctx.strokeStyle = '#10b981'; // green-500
                    ctx.lineWidth = 2;
                    ctx.arc(x, y, 4, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.stroke();
                }
                
                // Draw labels
                ctx.fillStyle = '#6b7280'; // gray-500
                ctx.font = '12px Outfit, sans-serif';
                ctx.textAlign = 'center';
                
                for (let i = 0; i < labels.length; i++) {
                    const x = padding + pointSpacing * i;
                    const y = chartHeight + padding + 20;
                    ctx.fillText(labels[i], x, y);
                }
                
                // Draw values on y-axis
                ctx.textAlign = 'right';
                for (let i = 0; i <= gridLines; i++) {
                    const value = Math.round((maxValue / gridLines) * (gridLines - i));
                    const y = padding + (chartHeight / gridLines) * i + 4;
                    ctx.fillText(value.toString(), padding - 10, y);
                }
            });
        }
    }"
    x-init="$watch('chartPeriod', () => drawChart()); $nextTick(() => drawChart())">

        <!-- Navbar -->
        @include('dashboardStaff.partials.navbar')
        
        <!-- Sidebar for mobile (overlay) -->
        @include('dashboardStaff.partials.sidebarMobile')

        <!-- Content area with sidebar -->
        <div class="flex pt-16 min-h-screen">
            <!-- Sidebar (desktop) -->
            @include('dashboardStaff.partials.sidebar')

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
