<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Carbon Emission Analysis Report</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            box-sizing: border-box;
        }

        @page {
            margin: 15mm 12mm;
            size: A4;
        }

        body {
            font-size: 9px;
            color: #1a202c;
            line-height: 1.4;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Fixed Header - Menggunakan flexbox dan background solid */
        .header {
            background-color: #39AA80 !important;
            background: #39AA80 !important;
            color: white !important;
            padding: 16px 20px;
            margin-bottom: 20px;
            text-align: center;
            page-break-inside: avoid;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .header h1 {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 6px 0;
            color: white !important;
        }

        .header .company-name {
            font-size: 12px;
            font-weight: 500;
            margin: 0 0 6px 0;
            color: white !important;
        }

        .header .date {
            font-size: 10px;
            margin: 0;
            color: white !important;
        }

        /* Fixed Summary Grid - Menggunakan table untuk kompatibilitas PDF */
        .summary-grid {
            width: 100%;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .summary-row {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .summary-card {
            display: table-cell;
            width: 25%;
            background-color: #39AA80 !important;
            background: #39AA80 !important;
            color: white !important;
            padding: 14px 8px;
            text-align: center;
            font-size: 8px;
            border: 1px solid #207e5b;
            vertical-align: middle;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .summary-card h3 {
            margin: 0 0 6px 0;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
            color: white !important;
        }

        .summary-card .value {
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 4px 0;
            color: white !important;
        }

        .summary-card .unit {
            font-size: 7px;
            color: white !important;
        }

        /* Main Content Grid - Menggunakan table untuk kompatibilitas PDF */
        .main-content {
            width: 100%;
            margin-bottom: 20px;
        }

        .main-content-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 15px 0;
        }

        .left-column, .right-column {
            width: 50%;
            vertical-align: top;
        }

        .content-section {
            margin-bottom: 15px;
        }

        /* Filter Section */
        .filter-section {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            margin-bottom: 12px;
        }

        .filter-section h3 {
            margin: 0 0 10px 0;
            font-size: 11px;
            font-weight: 600;
            color: #39AA80;
        }

        .filter-grid {
            display: block;
        }

        .filter-item {
            font-size: 8px;
            margin-bottom: 4px;
            display: block;
        }

        .filter-item strong {
            color: #39AA80;
            font-weight: 600;
            display: inline;
            margin-right: 4px;
        }

        .filter-item span {
            color: #4a5568;
        }

        /* Statistics Table */
        .stats-section {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 12px;
            margin-bottom: 12px;
        }

        .stats-section h3 {
            margin: 0 0 10px 0;
            font-size: 11px;
            font-weight: 600;
            color: #1a202c;
            border-bottom: 2px solid #39AA80;
            padding-bottom: 4px;
        }

        .stats-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }

        .stats-table th,
        .stats-table td {
            border: 1px solid #e2e8f0;
            padding: 8px 6px;
            text-align: left;
        }

        .stats-table th {
            background-color: #39AA80 !important;
            color: white !important;
            font-weight: 600;
            font-size: 7px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .stats-table .stat-value {
            font-weight: 600;
            color: #1a202c;
        }

        /* Breakdown Section */
        .breakdown-section {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 12px;
            margin-bottom: 12px;
        }

        .breakdown-section h3 {
            margin: 0 0 10px 0;
            font-size: 11px;
            font-weight: 600;
            color: #1a202c;
            border-bottom: 2px solid #39AA80;
            padding-bottom: 4px;
        }

        .breakdown-grid {
            display: block;
        }

        .breakdown-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px;
            margin-bottom: 10px;
        }

        .breakdown-card:last-child {
            margin-bottom: 0;
        }

        .breakdown-card h4 {
            margin: 0 0 8px 0;
            font-size: 9px;
            font-weight: 600;
            color: #39AA80;
        }

        .breakdown-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 8px;
        }

        .breakdown-item:last-child {
            border-bottom: none;
        }

        .breakdown-item .label {
            color: #4a5568;
            font-weight: 500;
        }

        .breakdown-item .value {
            font-weight: 600;
            color: #1a202c;
        }

        /* Insights Section */
        .insights-section {
            background-color: #f0f9f4;
            border: 2px solid #39AA80;
            padding: 15px;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .insights-section h3 {
            margin: 0 0 10px 0;
            font-size: 11px;
            font-weight: 600;
            color: #39AA80;
        }

        .insights-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .insights-list li {
            padding: 4px 0;
            font-size: 8px;
            color: #374151;
            position: relative;
            padding-left: 12px;
            line-height: 1.4;
            margin-bottom: 3px;
        }

        .insights-list li:before {
            content: "•";
            color: #39AA80;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            padding: 10px 0;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 7px;
            color: #6b7280;
        }

        .footer p {
            margin: 0 0 2px 0;
        }

        /* Utility Classes */
        .text-green { color: #39AA80; }
        .text-red { color: #e53e3e; }
        .text-orange { color: #dd6b20; }
        .font-bold { font-weight: 600; }

        /* Print optimizations */
        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .header,
            .summary-card,
            .stats-table th {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .summary-grid,
            .breakdown-section,
            .filter-section,
            .stats-section,
            .insights-section {
                page-break-inside: avoid;
            }
            
            /* Pastikan warna background tetap muncul di PDF */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .title {
                font-size: 20px;
                font-weight: bold;
                margin-bottom: 10px;
                color: #39AA80;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <h1 style="color: #39AA80; text-align: center;">Analysis of Carbon Emissions Employee Commutes</h1>

    <!-- Fixed Header -->
    <div class="header">
        <h1>{{ $analysisName }}</h1>
        <p class="company-name">{{ $companyName }}</p>
        <p class="date">Report generated on {{ \Carbon\Carbon::now()->format('F d, Y') }}</p>
    </div>

    <!-- Fixed Summary Cards -->
    <div class="summary-grid">
        <div class="summary-row">
            <div class="summary-card">
                <h3>Total Emissions</h3>
                <p class="value">{{ number_format($totalKarbon, 1) }}</p>
                <p class="unit">kg CO2e</p>
            </div>
            <div class="summary-card">
                <h3>Data Points</h3>
                <p class="value">{{ count($data) }}</p>
                <p class="unit">trips</p>
            </div>
            <div class="summary-card">
                <h3>Avg per Trip</h3>
                <p class="value">{{ number_format($totalKarbon / count($data), 1) }}</p>
                <p class="unit">kg CO2e</p>
            </div>
            <div class="summary-card">
                <h3>Total Distance</h3>
                <p class="value">{{ number_format($data->sum('jarak_perjalanan'), 0) }}</p>
                <p class="unit">km</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <table class="main-content-table">
            <tr>
                <td class="left-column">
                    <!-- Statistics Table -->
                    <div class="stats-section">
                        <h3>Emission Statistics</h3>
                        <table class="stats-table">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th>Value</th>
                                    <th>Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalJarak = $data->sum('jarak_perjalanan');
                                    $avgJarak = $totalJarak / count($data);
                                    $totalCO2 = $data->sum('total_co2');
                                    $totalCH4 = $data->sum('total_ch4');
                                    $totalN2O = $data->sum('total_n2O');
                                    $totalWTT = $data->sum('total_WTT');
                                @endphp
                                <tr>
                                    <td>Avg Distance</td>
                                    <td class="stat-value">{{ number_format($avgJarak, 1) }}</td>
                                    <td>km</td>
                                </tr>
                                <tr>
                                    <td>Total CO2</td>
                                    <td class="stat-value">{{ number_format($totalCO2, 1) }}</td>
                                    <td>kg</td>
                                </tr>
                                <tr>
                                    <td>Total CH4</td>
                                    <td class="stat-value">{{ number_format($totalCH4, 2) }}</td>
                                    <td>kg</td>
                                </tr>
                                <tr>
                                    <td>Total N2O</td>
                                    <td class="stat-value">{{ number_format($totalN2O, 2) }}</td>
                                    <td>kg</td>
                                </tr>
                                <tr>
                                    <td>Total WTT</td>
                                    <td class="stat-value">{{ number_format($totalWTT, 1) }}</td>
                                    <td>kg CO2e</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
                <td class="right-column">
                    <!-- Breakdown Analysis -->
                    <div class="breakdown-section">
                        <h3>Emission Breakdown</h3>
                        <div class="breakdown-grid">
                            <!-- By Transportation -->
                            <div class="breakdown-card">
                                <h4>By Transport</h4>
                                @php
                                    $transportasiGroup = $data->groupBy('transportasi.nama_transportasi');
                                @endphp
                                @foreach($transportasiGroup as $nama => $items)
                                <div class="breakdown-item">
                                    <span class="label">{{ $nama }}</span>
                                    <span class="value">{{ number_format($items->sum('total_emisi_karbon'), 1) }}</span>
                                </div>
                                @endforeach
                            </div>

                            <!-- By Fuel Type -->
                            <div class="breakdown-card">
                                <h4>By Fuel Type</h4>
                                @php
                                    $bahanBakarGroup = $data->groupBy('bahanBakar.nama_bahan_bakar');
                                @endphp
                                @foreach($bahanBakarGroup as $nama => $items)
                                <div class="breakdown-item">
                                    <span class="label">{{ $nama }}</span>
                                    <span class="value">{{ number_format($items->sum('total_emisi_karbon'), 1) }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Full Width Key Insights -->
    <div class="insights-section">
        <h3>Key Findings & Recommendations</h3>
        <ul class="insights-list">
            @php
                $topTransportasi = $transportasiGroup->sortByDesc(function($items) {
                    return $items->sum('total_emisi_karbon');
                })->first();
                $topTransportasiName = $transportasiGroup->sortByDesc(function($items) {
                    return $items->sum('total_emisi_karbon');
                })->keys()->first();
                
                $topBahanBakar = $bahanBakarGroup->sortByDesc(function($items) {
                    return $items->sum('total_emisi_karbon');
                })->first();
                $topBahanBakarName = $bahanBakarGroup->sortByDesc(function($items) {
                    return $items->sum('total_emisi_karbon');
                })->keys()->first();
                
                $avgEmisi = $totalKarbon / count($data);
            @endphp
            <li>Highest emitting transport: <strong>{{ $topTransportasiName }}</strong> ({{ number_format($topTransportasi->sum('total_emisi_karbon'), 1) }} kg CO2e)</li>
            <li>Highest emitting fuel: <strong>{{ $topBahanBakarName }}</strong> ({{ number_format($topBahanBakar->sum('total_emisi_karbon'), 1) }} kg CO2e)</li>
            <li>Average emissions per trip: <strong>{{ number_format($avgEmisi, 1) }} kg CO2e</strong></li>
            <li>Total distance covered: <strong>{{ number_format($totalJarak, 0) }} km</strong></li>
            @if($avgEmisi > 5)
            <li class="text-red">Average emissions are high (>5 kg CO2e). Consider route optimization.</li>
            @elseif($avgEmisi > 2)
            <li class="text-orange">Average emissions are moderate (2-5 kg CO2e). Room for improvement.</li>
            @else
            <li class="text-green">Average emissions are low (<2 kg CO2e). Maintain current practices.</li>
            @endif
        </ul>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Generated by {{ $companyName }}'s Carbon Emission Analysis System | Analysis based on {{ count($data) }} employee business trips</p>
    </div>
</body>
</html>