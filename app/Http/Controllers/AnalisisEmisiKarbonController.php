<?php

namespace App\Http\Controllers;

use App\Models\BahanBakar;
use App\Models\HasilAnalisisEmisi;
use App\Models\KaryawanPerusahaan;
use App\Models\PerjalananKaryawanPerusahaan;
use App\Models\Perusahaan;
use App\Models\StaffPerusahaan;
use App\Models\Transportasi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log; // <--- WAJIB: Import Facade Log
use Illuminate\Support\Str;

class AnalisisEmisiKarbonController extends Controller
{
    public function index()
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        $analisis = HasilAnalisisEmisi::latest()->paginate(5);
        $dataType = 'analisis';

        return view('dashboardPerusahaan.layouts.analisisEmisiKarbon.view', ['data' => $analisis, 'dataType' => $dataType]);
    }

    public function add()
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        // ... (kode comment sebelumnya)
    }

    public function store(Request $request)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
    }

    public function delete($id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }

        // Log penghapusan data
        Log::info('Deleting Analysis Data', ['id_analisis' => $id, 'user_id' => session('id')]);

        HasilAnalisisEmisi::destroy($id);
        return redirect('dashboard/perusahaan/analisis')->with('success', 'Data Successfully Deleted');
    }

    public function destroy($id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
    }

    public function edit($id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
    }

    public function update(Request $request, string $id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
    }

    public function viewAnalisis(Request $request)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }

        $query = PerjalananKaryawanPerusahaan::query();

        // Filter logic ...
        if ($request->filled('nama_karyawan')) {
            $query->whereHas('karyawanPerusahaan', function ($q) use ($request) {
                $q->where('nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
            });
        }
        if ($request->filled('nama_bahan_bakar')) {
            $query->whereHas('bahanBakar', function ($q) use ($request) {
                $q->where('nama_bahan_bakar', 'like', '%' . $request->nama_bahan_bakar . '%');
            });
        }
        if ($request->filled('nama_transportasi')) {
            $query->whereHas('transportasi', function ($q) use ($request) {
                $q->where('nama_transportasi', 'like', '%' . $request->nama_transportasi . '%');
            });
        }
        if ($request->filled('tanggal_perjalanan')) {
            $query->whereDate('tanggal_perjalanan', $request->tanggal_perjalanan);
        }

        $query->orderBy('tanggal_perjalanan', 'desc');
        $perjalanans = $query->paginate(5);

        $karyawans     = KaryawanPerusahaan::all();
        $bahanbakars   = BahanBakar::all();
        $transportasis = Transportasi::all();

        return view('dashboardPerusahaan.layouts.analisisEmisiKarbon.analisis', [
            'dataKaryawan' => $karyawans,
            'dataBahanBakar' => $bahanbakars,
            'dataTransportasi' => $transportasis,
            'data' => $perjalanans,
            'dataType' => 'perjalanan',
            'request' => $request,
        ]);
    }

    public function prosesAnalisis(Request $request)
    {
        $request->validate([
            'nama_analisis' => 'required|string|max:255',
        ]);

        $filters = [
            'nama_karyawan' => $request->input('nama_karyawan'),
            'nama_transportasi' => $request->input('nama_transportasi'),
            'nama_bahan_bakar' => $request->input('nama_bahan_bakar'),
            'tanggal_perjalanan' => $request->input('tanggal_perjalanan'),
        ];

        // [LOG CONTEXT] Mencatat dimulainya proses analisis
        Log::info('Analysis Process Started', [
            'analysis_name' => $request->input('nama_analisis'),
            'filters_applied' => $filters,
            'user_id' => session('id')
        ]);

        $query = PerjalananKaryawanPerusahaan::with(['karyawanPerusahaan', 'bahanBakar', 'transportasi', 'alamat']);

        if (!empty($filters['nama_karyawan'])) {
            $query->whereHas('karyawanPerusahaan', fn($q) => $q->where('nama_karyawan', 'like', '%' . $filters['nama_karyawan'] . '%'));
        }
        if (!empty($filters['nama_bahan_bakar'])) {
            $query->whereHas('bahanBakar', fn($q) => $q->where('nama_bahan_bakar', 'like', '%' . $filters['nama_bahan_bakar'] . '%'));
        }
        if (!empty($filters['nama_transportasi'])) {
            $query->whereHas('transportasi', fn($q) => $q->where('nama_transportasi', 'like', '%' . $filters['nama_transportasi'] . '%'));
        }
        if (!empty($filters['tanggal_perjalanan'])) {
            $query->whereDate('tanggal_perjalanan', $filters['tanggal_perjalanan']);
        }

        if ($query->count() == 0) {
            // [LOG CONTEXT] Warning jika data kosong
            Log::warning('Analysis Process Failed: No Data Found', ['filters' => $filters]);
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $data = $query->get();

        // Hitung total emisi karbon
        $totalKarbon = $data->sum('total_emisi_karbon');

        // Hitung statistik tambahan
        $analysisStats = [
            'total_perjalanan' => $data->count(),
            'total_emisi_karbon' => $totalKarbon,
            'rata_rata_emisi' => $totalKarbon / $data->count(),
            'total_jarak' => $data->sum('jarak_perjalanan'),
            'rata_rata_jarak' => $data->sum('jarak_perjalanan') / $data->count(),
            'total_co2' => $data->sum('total_co2'),
            'total_ch4' => $data->sum('total_ch4'),
            'total_n2o' => $data->sum('total_n2O'),
            'total_co2e' => $data->sum('total_co2e'),
            'total_wtt' => $data->sum('total_WTT'),
        ];

        // [LOG CONTEXT] Mencatat hasil perhitungan
        Log::info('Analysis Calculation Completed', [
            'total_data' => $data->count(),
            'total_emission' => $totalKarbon,
            'avg_emission' => $totalKarbon / $data->count()
        ]);

        // Group data & Top Contributors logic ...
        $transportasiGroup = $data->groupBy('transportasi.nama_transportasi');
        $bahanBakarGroup = $data->groupBy('bahanBakar.nama_bahan_bakar');
        $karyawanGroup = $data->groupBy('karyawanPerusahaan.nama_karyawan');

        $topTransportasi = $transportasiGroup->map(function ($items, $key) {
            return [
                'nama' => $key,
                'total_emisi' => $items->sum('total_emisi_karbon'),
                'jumlah_perjalanan' => $items->count(),
                'persentase' => 0
            ];
        })->sortByDesc('total_emisi');

        $topBahanBakar = $bahanBakarGroup->map(function ($items, $key) {
            return [
                'nama' => $key,
                'total_emisi' => $items->sum('total_emisi_karbon'),
                'jumlah_perjalanan' => $items->count(),
                'persentase' => 0
            ];
        })->sortByDesc('total_emisi');

        $topKaryawan = $karyawanGroup->map(function ($items, $key) {
            return [
                'nama' => $key,
                'total_emisi' => $items->sum('total_emisi_karbon'),
                'jumlah_perjalanan' => $items->count(),
                'persentase' => 0
            ];
        })->sortByDesc('total_emisi')->take(5);

        $trendData = $data->groupBy(function ($item) {
            return Carbon::parse($item->tanggal_perjalanan)->format('Y-m');
        })->map(function ($items, $key) {
            return [
                'periode' => $key,
                'total_emisi' => $items->sum('total_emisi_karbon'),
                'jumlah_perjalanan' => $items->count(),
            ];
        })->sortBy('periode');

        $insights = $this->generateInsights($data, $analysisStats, $topTransportasi, $topBahanBakar);

        $analysisName = $request->input('nama_analisis');

        $staff = StaffPerusahaan::where('id', session('id'))->first();
        if (!$staff) {
            Log::error('Analysis Failed: User Not Found in Session', ['session_id' => session('id')]);
            return redirect()->back()->with('error', 'User tidak ditemukan');
        }

        $company = Perusahaan::find($staff->id_perusahaan);
        $companyName = $company ? $company->nama_perusahaan : 'Unknown';

        // Buat PDF
        $pdf = Pdf::loadView('dashboardPerusahaan.layouts.analisisEmisiKarbon.pdf', compact(
            'data', 'filters', 'analysisName', 'companyName', 'totalKarbon', 'analysisStats',
            'topTransportasi', 'topBahanBakar', 'topKaryawan', 'trendData', 'insights'
        ))->setPaper('a4', 'portrait');

        $directory = public_path('analysis');
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $fileName = 'analisis_emisi_' . Str::random(10) . '.pdf';
        $filePath = $directory . '/' . $fileName;

        file_put_contents($filePath, $pdf->output());

        HasilAnalisisEmisi::create([
            'nama_analisis' => $analysisName,
            'id_perusahaan' => $staff->id_perusahaan,
            'tanggal_analisis' => Carbon::now(),
            'file_pdf' => $fileName,
            'total_data' => $data->count(),
            'total_emisi_karbon' => $totalKarbon,
            'rata_rata_emisi' => $analysisStats['rata_rata_emisi'],
            'filter_applied' => json_encode($filters),
        ]);

        // [LOG CONTEXT] Sukses simpan analisis
        Log::info('Analysis Saved Successfully', [
            'file_name' => $fileName,
            'company_id' => $staff->id_perusahaan
        ]);

        return redirect()->route('analisis.viewAnalisis')->with('analisis_berhasil', true);
    }

    private function generateInsights($data, $stats, $topTransportasi, $topBahanBakar)
    {
        // ... (Logic generateInsights sama persis, tidak perlu diubah) ...
        // Agar kode lebih ringkas saya tidak tulis ulang logicnya di sini karena tidak ada logging yg diperlukan di internal helper ini

        $insights = [];

        if ($stats['rata_rata_emisi'] > 10) {
            $insights[] = ['type' => 'warning', 'message' => 'Emisi rata-rata per perjalanan sangat tinggi (> 10 kg CO2e). Pertimbangkan untuk mengoptimalkan rute atau beralih ke transportasi yang lebih efisien.'];
        } elseif ($stats['rata_rata_emisi'] > 5) {
            $insights[] = ['type' => 'caution', 'message' => 'Emisi rata-rata per perjalanan cukup tinggi (5-10 kg CO2e). Ada ruang untuk perbaikan efisiensi.'];
        } else {
            $insights[] = ['type' => 'positive', 'message' => 'Emisi rata-rata per perjalanan dalam kategori baik (< 5 kg CO2e). Pertahankan praktik yang sudah berjalan.'];
        }

        $topTransportasiName = $topTransportasi->first()['nama'];
        $topTransportasiEmisi = $topTransportasi->first()['total_emisi'];
        $persentaseTopTransportasi = ($topTransportasiEmisi / $stats['total_emisi_karbon']) * 100;

        if ($persentaseTopTransportasi > 50) {
            $insights[] = ['type' => 'info', 'message' => "Transportasi '{$topTransportasiName}' berkontribusi {$persentaseTopTransportasi}% dari total emisi. Fokus optimalisasi pada jenis transportasi ini akan memberikan dampak signifikan."];
        }

        $topBahanBakarName = $topBahanBakar->first()['nama'];
        $topBahanBakarEmisi = $topBahanBakar->first()['total_emisi'];
        $persentaseTopBahanBakar = ($topBahanBakarEmisi / $stats['total_emisi_karbon']) * 100;

        if ($persentaseTopBahanBakar > 60) {
            $insights[] = ['type' => 'recommendation', 'message' => "Bahan bakar '{$topBahanBakarName}' menghasilkan {$persentaseTopBahanBakar}% dari total emisi. Pertimbangkan alternatif bahan bakar yang lebih ramah lingkungan."];
        }

        $efisiensiEmisi = $stats['total_emisi_karbon'] / $stats['total_jarak'];
        if ($efisiensiEmisi > 0.5) {
            $insights[] = ['type' => 'warning', 'message' => "Efisiensi emisi per kilometer tergolong rendah ({$efisiensiEmisi} kg CO2e/km). Evaluasi pemilihan moda transportasi dan rute perjalanan."];
        }

        return $insights;
    }

    public function downloadAnalysisPdf($filename)
    {
        try {
            $filePath = public_path('analysis/' . $filename);

            // [LOG CONTEXT] Mencatat percobaan download
            Log::info('PDF Download Attempt', [
                'filename' => $filename,
                'user_id' => session('id')
            ]);

            if (!File::exists($filePath)) {
                // [LOG CONTEXT] Error jika file hilang
                Log::error('PDF Download Failed: File Not Found', ['path' => $filePath]);
                return response()->json(['error' => 'File not found'], 404);
            }

            $fileInfo = pathinfo($filePath);
            $originalName = $fileInfo['filename'];
            $downloadName = 'Analysis_Report_' . $originalName . '.pdf';

            return response()->download($filePath, $downloadName, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"'
            ]);

        } catch (\Exception $e) {
            // [LOG CONTEXT] Exception handling
            Log::error('PDF Download Error', [
                'filename' => $filename,
                'error_message' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Error downloading file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadReplyPdf($filename)
    {
        try {
            $filePath = public_path('messages/' . $filename);

            // [LOG CONTEXT] Mencatat percobaan download reply
            Log::info('Reply PDF Download Attempt', [
                'filename' => $filename,
                'user_id' => session('id')
            ]);

            if (!File::exists($filePath)) {
                Log::error('Reply PDF Download Failed: File Not Found', ['path' => $filePath]);
                return response()->json(['error' => 'File not found'], 404);
            }

            $fileInfo = pathinfo($filePath);
            $originalName = $fileInfo['filename'];
            $downloadName = 'Reply_' . $originalName . '.pdf';

            return response()->download($filePath, $downloadName, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"'
            ]);

        } catch (\Exception $e) {
            Log::error('Reply PDF Download Error', [
                'filename' => $filename,
                'error_message' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Error downloading file: ' . $e->getMessage()
            ], 500);
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
