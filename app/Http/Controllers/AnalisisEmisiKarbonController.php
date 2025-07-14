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
use Illuminate\Support\Str;

// use Barryvdh\DomPDF\PDF as PDF;

class AnalisisEmisiKarbonController extends Controller
{
    public function index()
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        $analisis = HasilAnalisisEmisi::latest()->paginate(5);
        $dataType = 'analisis';
        // $perjalanans = PerjalananKaryawanPerusahaan::all();

        // return ($perjalanans);
        return view('dashboardPerusahaan.layouts.analisisEmisiKarbon.view', ['data' => $analisis, 'dataType' => $dataType]);
    }

    public function add()
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        // $transportasis = Transportasi::all();
        // $bahanbakars = BahanBakar::all();
        // $alamats = AlamatRumah::all();
        // $karyawans = KaryawanPerusahaan::all();

        // return view('dashboardPerusahaan.layouts.perjalananKaryawanPerusahaan.add', ['dataTransportasi' => $transportasis, 'dataBahanBakar' => $bahanbakars, 'dataAlamat' => $alamats, 'dataKaryawan' => $karyawans]);
    }

    public function store(Request $request)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        // $validatedData = $request->validate([
        //     'employee_name' => 'required',
        //     'transportation' => 'required',
        //     'fuel' => 'required',
        //     'address' => 'required',
        //     'trip_date' => 'required',
        //     'trip_duration' => 'required',
        // ]);

        // // Cek duplikat berdasarkan nama karyawan dan tanggal perjalanan
        // $existing = PerjalananKaryawanPerusahaan::where('id_karyawan', $request->employee_name)
        //     ->where('tanggal_perjalanan', $request->trip_date)
        //     ->first();

        // if ($existing) {
        //     return redirect('dashboard/perusahaan/perjalanan/add')
        //         ->with('failed', 'Data sudah ada (data duplikat)');
        // }

        // $bahanBakar = BahanBakar::find($request->fuel);

        // PerjalananKaryawanPerusahaan::create([
        //     'id_karyawan' => $request->employee_name,
        //     'id_transportasi' => $request->transportation,
        //     'id_bahan_bakar' => $request->fuel,
        //     'id_perusahaan' => 1,
        //     'id_alamat' => $request->address,
        //     'tanggal_perjalanan' => $request->trip_date,
        //     'durasi_perjalanan' => $request->trip_duration,
        //     'total_emisi_karbon' => $bahanBakar->emisi_karbon_permenit * $request->trip_duration,
        // ]);

        // return redirect('dashboard/perusahaan/perjalanan/add')->with('success', 'Data Successfully Added');
    }

    public function delete($id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        HasilAnalisisEmisi::destroy($id);
        return redirect('dashboard/perusahaan/analisis')->with('success', 'Data Successfully Deleted');
    }

    public function destroy($id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        // PerjalananKaryawanPerusahaan::findOrFail($id)->delete();

        // return redirect()->back()->with('deleted', 'Data berhasil dihapus');
    }

    public function edit($id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        // $transportasis = Transportasi::all();
        // $bahanbakars = BahanBakar::all();
        // $alamats = AlamatRumah::all();
        // $karyawans = KaryawanPerusahaan::all();

        // $oldData = PerjalananKaryawanPerusahaan::find($id);

        // // return ($oldData);

        // return view('dashboardPerusahaan.layouts.perjalananKaryawanPerusahaan.edit', ['dataTransportasi' => $transportasis, 'dataBahanBakar' => $bahanbakars, 'dataAlamat' => $alamats, 'dataKaryawan' => $karyawans, 'oldData' => $oldData, 'id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        // $validatedData = $request->validate([
        //     'employee_name' => 'required',
        //     'transportation' => 'required',
        //     'fuel' => 'required',
        //     'address' => 'required',
        //     'trip_date' => 'required',
        //     'trip_duration' => 'required',
        // ]);

        // $bahanBakar = BahanBakar::find($request->fuel);

        // PerjalananKaryawanPerusahaan::where('id', $id)->update([
        //     'id_karyawan' => $request->employee_name,
        //     'id_transportasi' => $request->transportation,
        //     'id_bahan_bakar' => $request->fuel,
        //     'id_perusahaan' => 1,
        //     'id_alamat' => $request->address,
        //     'tanggal_perjalanan' => $request->trip_date,
        //     'durasi_perjalanan' => $request->trip_duration,
        //     'total_emisi_karbon' =>  $bahanBakar->emisi_karbon_permenit * $request->trip_duration,
        // ]);

        // return redirect('dashboard/perusahaan/perjalanan/edit/' . $id . '')->with('success', 'Data Successfully Updated');
    }

    public function viewAnalisis(Request $request)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }

        $query = PerjalananKaryawanPerusahaan::query();

        // Filter nama_karyawan
        if ($request->filled('nama_karyawan')) {
            $query->whereHas('karyawanPerusahaan', function ($q) use ($request) {
                $q->where('nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
            });
        }

        // Filter nama_bahan_bakar
        if ($request->filled('nama_bahan_bakar')) {
            $query->whereHas('bahanBakar', function ($q) use ($request) {
                $q->where('nama_bahan_bakar', 'like', '%' . $request->nama_bahan_bakar . '%');
            });
        }

        // Filter nama_transportasi
        if ($request->filled('nama_transportasi')) {
            $query->whereHas('transportasi', function ($q) use ($request) {
                $q->where('nama_transportasi', 'like', '%' . $request->nama_transportasi . '%');
            });
        }

        // Filter tanggal_perjalanan
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
        // Validasi input nama_analisis
        $request->validate([
            'nama_analisis' => 'required|string|max:255',
        ]);

        $filters = [
            'nama_karyawan' => $request->input('nama_karyawan'),
            'nama_transportasi' => $request->input('nama_transportasi'),
            'nama_bahan_bakar' => $request->input('nama_bahan_bakar'),
            'tanggal_perjalanan' => $request->input('tanggal_perjalanan'),
        ];

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

        // Group data untuk analisis breakdown
        $transportasiGroup = $data->groupBy('transportasi.nama_transportasi');
        $bahanBakarGroup = $data->groupBy('bahanBakar.nama_bahan_bakar');
        $karyawanGroup = $data->groupBy('karyawanPerusahaan.nama_karyawan');

        // Hitung top contributors
        $topTransportasi = $transportasiGroup->map(function ($items, $key) {
            return [
                'nama' => $key,
                'total_emisi' => $items->sum('total_emisi_karbon'),
                'jumlah_perjalanan' => $items->count(),
                'persentase' => 0 // akan dihitung di view
            ];
        })->sortByDesc('total_emisi');

        $topBahanBakar = $bahanBakarGroup->map(function ($items, $key) {
            return [
                'nama' => $key,
                'total_emisi' => $items->sum('total_emisi_karbon'),
                'jumlah_perjalanan' => $items->count(),
                'persentase' => 0 // akan dihitung di view
            ];
        })->sortByDesc('total_emisi');

        $topKaryawan = $karyawanGroup->map(function ($items, $key) {
            return [
                'nama' => $key,
                'total_emisi' => $items->sum('total_emisi_karbon'),
                'jumlah_perjalanan' => $items->count(),
                'persentase' => 0 // akan dihitung di view
            ];
        })->sortByDesc('total_emisi')->take(5); // ambil top 5 karyawan

        // Analisis trend berdasarkan tanggal (jika data mencakup rentang waktu)
        $trendData = $data->groupBy(function ($item) {
            return Carbon::parse($item->tanggal_perjalanan)->format('Y-m');
        })->map(function ($items, $key) {
            return [
                'periode' => $key,
                'total_emisi' => $items->sum('total_emisi_karbon'),
                'jumlah_perjalanan' => $items->count(),
            ];
        })->sortBy('periode');

        // Generate insights berdasarkan data
        $insights = $this->generateInsights($data, $analysisStats, $topTransportasi, $topBahanBakar);

        $analysisName = $request->input('nama_analisis');

        $staff = StaffPerusahaan::where('id', session('id'))->first();
        if (!$staff) {
            return redirect()->back()->with('error', 'User tidak ditemukan');
        }

        $company = Perusahaan::find($staff->id_perusahaan);
        $companyName = $company ? $company->nama_perusahaan : 'Unknown';

        // Buat PDF dengan data yang diperkaya
        $pdf = Pdf::loadView('dashboardPerusahaan.layouts.analisisEmisiKarbon.pdf', compact(
            'data',
            'filters',
            'analysisName',
            'companyName',
            'totalKarbon',
            'analysisStats',
            'topTransportasi',
            'topBahanBakar',
            'topKaryawan',
            'trendData',
            'insights'
        ))->setPaper('a4', 'portrait');

        // Buat folder jika belum ada
        $directory = public_path('analysis');
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Buat nama file unik
        $fileName = 'analisis_emisi_' . Str::random(10) . '.pdf';
        $filePath = $directory . '/' . $fileName;

        // Simpan file PDF
        file_put_contents($filePath, $pdf->output());

        // Simpan ke database dengan informasi statistik
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

        return redirect()->route('analisis.viewAnalisis')->with('analisis_berhasil', true);
    }

    private function generateInsights($data, $stats, $topTransportasi, $topBahanBakar)
    {
        $insights = [];

        // Insight berdasarkan rata-rata emisi
        if ($stats['rata_rata_emisi'] > 10) {
            $insights[] = [
                'type' => 'warning',
                'message' => 'Emisi rata-rata per perjalanan sangat tinggi (> 10 kg CO2e). Pertimbangkan untuk mengoptimalkan rute atau beralih ke transportasi yang lebih efisien.'
            ];
        } elseif ($stats['rata_rata_emisi'] > 5) {
            $insights[] = [
                'type' => 'caution',
                'message' => 'Emisi rata-rata per perjalanan cukup tinggi (5-10 kg CO2e). Ada ruang untuk perbaikan efisiensi.'
            ];
        } else {
            $insights[] = [
                'type' => 'positive',
                'message' => 'Emisi rata-rata per perjalanan dalam kategori baik (< 5 kg CO2e). Pertahankan praktik yang sudah berjalan.'
            ];
        }

        // Insight berdasarkan transportasi
        $topTransportasiName = $topTransportasi->first()['nama'];
        $topTransportasiEmisi = $topTransportasi->first()['total_emisi'];
        $persentaseTopTransportasi = ($topTransportasiEmisi / $stats['total_emisi_karbon']) * 100;

        if ($persentaseTopTransportasi > 50) {
            $insights[] = [
                'type' => 'info',
                'message' => "Transportasi '{$topTransportasiName}' berkontribusi {$persentaseTopTransportasi}% dari total emisi. Fokus optimalisasi pada jenis transportasi ini akan memberikan dampak signifikan."
            ];
        }

        // Insight berdasarkan bahan bakar
        $topBahanBakarName = $topBahanBakar->first()['nama'];
        $topBahanBakarEmisi = $topBahanBakar->first()['total_emisi'];
        $persentaseTopBahanBakar = ($topBahanBakarEmisi / $stats['total_emisi_karbon']) * 100;

        if ($persentaseTopBahanBakar > 60) {
            $insights[] = [
                'type' => 'recommendation',
                'message' => "Bahan bakar '{$topBahanBakarName}' menghasilkan {$persentaseTopBahanBakar}% dari total emisi. Pertimbangkan alternatif bahan bakar yang lebih ramah lingkungan."
            ];
        }

        // Insight berdasarkan efisiensi jarak
        $efisiensiEmisi = $stats['total_emisi_karbon'] / $stats['total_jarak']; // kg CO2e per km
        if ($efisiensiEmisi > 0.5) {
            $insights[] = [
                'type' => 'warning',
                'message' => "Efisiensi emisi per kilometer tergolong rendah ({$efisiensiEmisi} kg CO2e/km). Evaluasi pemilihan moda transportasi dan rute perjalanan."
            ];
        }

        return $insights;
    }

    public function downloadAnalysisPdf($filename)
    {
        // dd($filename);

        try {
            // Define the path where PDF files are stored
            // Adjust this path according to your storage structure
            $filePath = $filePath = public_path('analysis/' . $filename);

            // Alternative path if stored in public folder
            // $filePath = public_path('storage/analysis_pdfs/' . $filename);

            // Check if file exists
            if (!File::exists($filePath)) {
                return response()->json([
                    'error' => 'File not found'
                ], 404);
            }

            // Get file info
            $fileInfo = pathinfo($filePath);
            $originalName = $fileInfo['filename'];

            // Create a more user-friendly filename
            $downloadName = 'Analysis_Report_' . $originalName . '.pdf';

            // Return file download response
            return response()->download($filePath, $downloadName, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"'
            ]);

            redirect()->back()->with('success', 'File downloaded successfully.');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error downloading file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadReplyPdf($filename)
    {
        // dd($filename);

        try {
            // Define the path where PDF files are stored
            // Adjust this path according to your storage structure
            $filePath = $filePath = public_path('messages/' . $filename);

            // Alternative path if stored in public folder
            // $filePath = public_path('storage/analysis_pdfs/' . $filename);

            // Check if file exists
            if (!File::exists($filePath)) {
                return response()->json([
                    'error' => 'File not found'
                ], 404);
            }

            // Get file info
            $fileInfo = pathinfo($filePath);
            $originalName = $fileInfo['filename'];

            // Create a more user-friendly filename
            $downloadName = 'Reply_' . $originalName . '.pdf';

            // Return file download response
            return response()->download($filePath, $downloadName, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"'
            ]);

            redirect()->back()->with('success', 'File downloaded successfully.');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error downloading file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format bytes to human readable format
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
