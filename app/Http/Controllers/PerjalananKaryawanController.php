<?php

namespace App\Http\Controllers;

use App\Models\AlamatRumah;
use App\Models\BahanBakar;
use App\Models\KaryawanPerusahaan;
use App\Models\PerjalananKaryawanPerusahaan;
use App\Models\Perusahaan;
use App\Models\Transportasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // <--- WAJIB IMPORT LOG

class PerjalananKaryawanController extends Controller
{
    public function index(Request $request)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }

        // [LOG CONTEXT] Log akses data perjalanan oleh perusahaan
        Log::info('Viewing Employee Travel History (Company)', [
            'user_id' => session('id'),
            'filters' => $request->only(['nama_karyawan', 'tanggal_perjalanan'])
        ]);

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

        return view('dashboardPerusahaan.layouts.perjalananKaryawanPerusahaan.view', [
            'dataKaryawan' => $karyawans,
            'dataBahanBakar' => $bahanbakars,
            'dataTransportasi' => $transportasis,
            'data' => $perjalanans,
            'dataType' => 'perjalanan',
            'request' => $request,
        ]);
    }

    public function absen(Request $request)
    {
        if ($redirect = $this->checkifLoginForEmployee()) {
            return $redirect;
        }

        $validatedData = $request->validate([
            'alamat_rumah' => 'required',
            'bahan_bakar' => 'required',
            'transportasi' => 'required',
        ]);

        // [LOG CONTEXT] Mulai proses absen & kalkulasi
        Log::info('Employee Commute Check-in Started', [
            'employee_id' => session('id'),
            'transport_mode' => $request->transportasi,
            'fuel_id' => $request->bahan_bakar
        ]);

        $idPerusahaan = KaryawanPerusahaan::where('id', session('id'))->first()->id_perusahaan;

        // Ambil data faktor emisi (Data Enrichment)
        $bahanBakarData = BahanBakar::where('id', $request->bahan_bakar)->first();

        $emisiKarbonPermenit = $bahanBakarData->emisi_karbon_permenit;
        $co2 = $bahanBakarData->co2perliter;
        $ch4 = $bahanBakarData->ch4perliter;
        $n2O = $bahanBakarData->n2Operliter;
        $co2e = $bahanBakarData->co2eperliter;
        $WTT = $bahanBakarData->WTTperliter;
        $consumpstion_rate = $bahanBakarData->rerata_konsumsi_literperkm;

        $alamatRumah = AlamatRumah::find($request->alamat_rumah);
        $perusahaan  = Perusahaan::find($idPerusahaan);

        if (! $alamatRumah || ! $perusahaan) {
            Log::error('Check-in Failed: Location Data Missing', ['employee_id' => session('id')]);
            return response()->json(['error' => 'Data lokasi tidak ditemukan.'], 404);
        }

        $start = [
            'lat' => (float) $alamatRumah->latitude,
            'lng' => (float) $alamatRumah->longitude,
        ];

        $end = [
            'lat' => (float) $perusahaan->latitude,
            'lng' => (float) $perusahaan->longitude,
        ];

        // Hitung Jarak (External Call)
        $jarakPerjalanan = $this->hitungJarakPerjalanan($start, $end);

        if ($jarakPerjalanan === null) {
            // [LOG CONTEXT] Gagal hitung jarak
            Log::error('Check-in Failed: Distance Calculation Error', ['employee_id' => session('id')]);
            return redirect()->back()->with('error', 'Gagal menghitung jarak perjalanan.');
        }

        // Kalkulasi Emisi
        $totalco2 = $co2 * $jarakPerjalanan * $consumpstion_rate;
        $totalch4 = $ch4 * $jarakPerjalanan * $consumpstion_rate;
        $totaln2O = $n2O * $jarakPerjalanan * $consumpstion_rate;
        $totalco2e = $co2e * $jarakPerjalanan * $consumpstion_rate;
        $totalWTT = $WTT * $jarakPerjalanan * $consumpstion_rate;

        $emisiKarbon = $totalco2 + $totalch4 + $totaln2O + $totalco2e;

        PerjalananKaryawanPerusahaan::create([
            'id_karyawan' => session('id'),
            'id_transportasi' => $request->transportasi,
            'id_bahan_bakar' => $request->bahan_bakar,
            'id_alamat' => $request->alamat_rumah,
            'id_perusahaan' => $idPerusahaan,
            'tanggal_perjalanan' => Carbon::now(),
            'jarak_perjalanan' => $jarakPerjalanan,
            'total_co2' => $totalco2,
            'total_ch4' => $totalch4,
            'total_n2O' => $totaln2O,
            'total_co2e' => $totalco2e,
            'total_WTT' => $totalWTT,
            'total_emisi_karbon' => $emisiKarbon,
        ]);

        // [LOG CONTEXT] Absen Sukses dengan detail emisi
        Log::info('Employee Commute Check-in Success', [
            'employee_id' => session('id'),
            'distance_km' => $jarakPerjalanan,
            'total_emission_kg' => $emisiKarbon
        ]);

        return redirect('dashboard/karyawan/')->with('success', 'Absen Successfully Taken');
    }

    public function hitungJarakPerjalanan($start, $end)
    {
        $apiKey = env('ORS_API_KEY');

        if (! $apiKey) {
            Log::error('ORS API Error: API Key is missing in .env');
            return null;
        }

        $coordinates = [
            [(float) $start['lng'], (float) $start['lat']],
            [(float) $end['lng'], (float) $end['lat']],
        ];

        // [LOG CONTEXT] Mencatat External Call ke OpenRouteService
        Log::info('External API Call: OpenRouteService Directions', [
            'start_coords' => $start,
            'end_coords' => $end
        ]);

        $response = Http::timeout(10)->withHeaders([
            'Authorization' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openrouteservice.org/v2/directions/driving-car', [
            'coordinates' => $coordinates,
        ]);

        if (! $response->successful()) {
            // [LOG CONTEXT] Log jika API Eksternal gagal (penting untuk debugging)
            Log::error('External API Call Failed: OpenRouteService', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return null;
        }

        $data = $response->json();
        $distance = round($data['routes'][0]['summary']['distance'] / 1000, 2);

        // [LOG CONTEXT] Log hasil API eksternal
        Log::info('External API Call Success', ['calculated_distance_km' => $distance]);

        return $distance;
    }

    public function indexKaryawan(Request $request)
    {
        if ($redirect = $this->checkifLoginForEmployee()) {
            return $redirect;
        }

        // [LOG CONTEXT] Log akses history karyawan (self-service)
        Log::info('Viewing Personal Travel History', ['employee_id' => session('id')]);

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

        return view('dashboardKaryawan.layouts.perjalananKaryawanPerusahaan.view', [
            'dataKaryawan' => $karyawans,
            'dataBahanBakar' => $bahanbakars,
            'dataTransportasi' => $transportasis,
            'data' => $perjalanans,
            'dataType' => 'perjalanan',
            'request' => $request,
        ]);
    }

    public function add()
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        $transportasis = Transportasi::all();
        $bahanbakars   = BahanBakar::all();
        $alamats       = AlamatRumah::all();
        $karyawans     = KaryawanPerusahaan::all();

        return view('dashboardPerusahaan.layouts.perjalananKaryawanPerusahaan.add', ['dataTransportasi' => $transportasis, 'dataBahanBakar' => $bahanbakars, 'dataAlamat' => $alamats, 'dataKaryawan' => $karyawans]);
    }

    public function store(Request $request)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        $validatedData = $request->validate([
            'employee_name' => 'required',
            'transportation' => 'required',
            'fuel' => 'required',
            'address' => 'required',
            'trip_date' => 'required',
            'trip_duration' => 'required',
        ]);

        // Cek duplikat
        $existing = PerjalananKaryawanPerusahaan::where('id_karyawan', $request->employee_name)
            ->where('tanggal_perjalanan', $request->trip_date)
            ->first();

        if ($existing) {
            // [LOG CONTEXT] Log percobaan input duplikat
            Log::warning('Duplicate Travel Data Input Attempt', [
                'employee_id' => $request->employee_name,
                'date' => $request->trip_date
            ]);
            return redirect('dashboard/perusahaan/perjalanan/add')
                ->with('failed', 'Data sudah ada (data duplikat)');
        }

        $bahanBakar = BahanBakar::find($request->fuel);

        PerjalananKaryawanPerusahaan::create([
            'id_karyawan' => $request->employee_name,
            'id_transportasi' => $request->transportation,
            'id_bahan_bakar' => $request->fuel,
            'id_perusahaan' => 1,
            'id_alamat' => $request->address,
            'tanggal_perjalanan' => $request->trip_date,
            'durasi_perjalanan' => $request->trip_duration,
            'total_emisi_karbon' => $bahanBakar->emisi_karbon_permenit * $request->trip_duration,
        ]);

        // [LOG CONTEXT] Log input manual data perjalanan
        Log::info('Manual Travel Data Entry Success', ['employee_id' => $request->employee_name]);

        return redirect('dashboard/perusahaan/perjalanan/add')->with('success', 'Data Successfully Added');
    }

    public function delete($id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }

        // [LOG CONTEXT] Log hapus data
        Log::warning('Deleting Travel Record', ['record_id' => $id, 'user_id' => session('id')]);

        PerjalananKaryawanPerusahaan::destroy($id);

        return redirect('dashboard/perusahaan/perjalanan')->with('success', 'Data Successfully Deleted');
    }

    public function destroy($id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        PerjalananKaryawanPerusahaan::findOrFail($id)->delete();

        return redirect()->back()->with('deleted', 'Data berhasil dihapus');
    }

    public function edit($id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        $transportasis = Transportasi::all();
        $bahanbakars   = BahanBakar::all();
        $alamats       = AlamatRumah::all();
        $karyawans     = KaryawanPerusahaan::all();

        $oldData = PerjalananKaryawanPerusahaan::find($id);

        return view('dashboardPerusahaan.layouts.perjalananKaryawanPerusahaan.edit', ['dataTransportasi' => $transportasis, 'dataBahanBakar' => $bahanbakars, 'dataAlamat' => $alamats, 'dataKaryawan' => $karyawans, 'oldData' => $oldData, 'id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        $validatedData = $request->validate([
            'employee_name' => 'required',
            'transportation' => 'required',
            'fuel' => 'required',
            'address' => 'required',
            'trip_date' => 'required',
            'trip_duration' => 'required',
        ]);

        // [LOG CONTEXT] Log update data
        Log::info('Updating Travel Record', ['record_id' => $id, 'user_id' => session('id')]);

        $bahanBakar = BahanBakar::find($request->fuel);

        PerjalananKaryawanPerusahaan::where('id', $id)->update([
            'id_karyawan' => $request->employee_name,
            'id_transportasi' => $request->transportation,
            'id_bahan_bakar' => $request->fuel,
            'id_perusahaan' => 1,
            'id_alamat' => $request->address,
            'tanggal_perjalanan' => $request->trip_date,
            'durasi_perjalanan' => $request->trip_duration,
            'total_emisi_karbon' => $bahanBakar->emisi_karbon_permenit * $request->trip_duration,
        ]);

        return redirect('dashboard/perusahaan/perjalanan/edit/' . $id . '')->with('success', 'Data Successfully Updated');
    }
}
