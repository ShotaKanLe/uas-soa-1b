<?php

namespace App\Http\Controllers;

use App\Models\AlamatRumah;
use App\Models\KaryawanPerusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // <--- WAJIB IMPORT LOG

class AlamatRumahController extends Controller
{
    public function index()
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }

        // [LOG CONTEXT] Perusahaan melihat daftar alamat karyawan
        Log::info('Viewing Employee Address List (Company)', [
            'user_id' => session('id'),
            'role' => 'perusahaan'
        ]);

        $alamats  = AlamatRumah::latest()->paginate(5);
        $dataType = 'alamat';

        return view('dashboardPerusahaan.layouts.alamatRumah.view', ['data' => $alamats, 'dataType' => $dataType]);
    }

    public function indexAlamatKaryawan()
    {
        if ($redirect = $this->checkifLoginForEmployee()) {
            return $redirect;
        }

        // [LOG CONTEXT] Karyawan melihat daftar alamat sendiri
        Log::info('Viewing Personal Address List', [
            'employee_id' => session('id')
        ]);

        $alamats = AlamatRumah::where('id_karyawan', session('id'))
            ->latest()
            ->paginate(5);

        $dataType = 'alamat';

        return view('dashboardKaryawan.layouts.alamatRumah.view', ['data' => $alamats, 'dataType' => $dataType]);
    }

    public function add()
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        $karyawans = KaryawanPerusahaan::all();

        return view('dashboardPerusahaan.layouts.alamatRumah.add', ['dataKaryawan' => $karyawans]);
    }

    public function addAlamatKaryawan()
    {
        if ($redirect = $this->checkifLoginForEmployee()) {
            return $redirect;
        }
        $karyawans = KaryawanPerusahaan::all();

        return view('dashboardKaryawan.layouts.alamatRumah.add', ['dataKaryawan' => $karyawans]);
    }

    public function store(Request $request)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        Controller::checkifLoginForCompany();
        $validatedData = $request->validate([
            'employee_name' => 'required',
            'address' => 'required',
        ]);

        // [LOG CONTEXT] Input manual alamat oleh perusahaan
        Log::info('Company Manually Adding Address', [
            'company_id' => session('id'),
            'target_employee' => $request->employee_name
        ]);

        AlamatRumah::create([
            'id_karyawan' => $request->employee_name,
            'alamat_rumah' => $request->address,
        ]);

        return redirect('dashboard/perusahaan/alamat/add')->with('success', 'Data Successfully Added');
    }

    public function storeAlamatKaryawan(Request $request)
    {
        if ($redirect = $this->checkifLoginForEmployee()) {
            return $redirect;
        }

        // [LOG CONTEXT] Karyawan menyimpan lokasi baru via Peta/Koordinat
        Log::info('Employee Saving New Location', [
            'employee_id' => session('id'),
            'coordinates' => ['lat' => $request->latitude, 'lng' => $request->longitude]
        ]);

        $alamatRumah = $this->getLocationDetails($request->latitude, $request->longitude);

        AlamatRumah::create([
            'id_karyawan' => session('id'),
            'alamat_rumah' => $alamatRumah,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect('/dashboard/karyawan/alamat/add')->with('success', 'Data Successfully Added');
    }

    public function delete($id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        Controller::checkifLoginForCompany();

        // [LOG CONTEXT] Hapus alamat
        Log::warning('Deleting Address Record', ['address_id' => $id, 'performed_by' => session('id')]);

        AlamatRumah::destroy($id);

        return redirect('dashboard/perusahaan/alamat')->with('success', 'Data Successfully Deleted');
    }

    public function deleteAlamatKaryawan($id)
    {
        if ($redirect = $this->checkifLoginForEmployee()) {
            return $redirect;
        }

        Log::warning('Employee Deleting Personal Address', ['address_id' => $id, 'employee_id' => session('id')]);

        AlamatRumah::destroy($id);

        return redirect('dashboard/karyawan/alamat')->with('success', 'Data Successfully Deleted');
    }

    public function edit($id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        $karyawans = KaryawanPerusahaan::all();
        $oldData = AlamatRumah::find($id);

        return view('dashboardPerusahaan.layouts.alamatRumah.edit', ['dataKaryawan' => $karyawans, 'oldData' => $oldData, 'id' => $id]);
    }

    public function editAlamatKaryawan($id)
    {
        if ($redirect = $this->checkifLoginForEmployee()) {
            return $redirect;
        }

        $alamatRumah = AlamatRumah::where('id', $id)
            ->where('id_karyawan', session('id'))
            ->first();

        if (!$alamatRumah) {
            Log::warning('Unauthorized Address Edit Attempt', ['employee_id' => session('id'), 'target_address_id' => $id]);
            return redirect('/dashboard/karyawan/alamat')->with('error', 'Address data not found');
        }

        return view('dashboardKaryawan.layouts.alamatRumah.edit', compact('alamatRumah'));
    }

    public function update(Request $request, string $id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        Controller::checkifLoginForCompany();
        $validatedData = $request->validate([
            'employee_name' => 'required',
            'address' => 'required',
        ]);

        Log::info('Company Updating Address', ['address_id' => $id, 'new_address' => $request->address]);

        AlamatRumah::where('id', $id)->update([
            'id_karyawan' => $request->employee_name,
            'alamat_rumah' => $request->address,
        ]);

        return redirect('dashboard/perusahaan/alamat/edit/' . $id . '')->with('success', 'Data Successfully Updated');
    }

    public function updateAlamatKaryawan(Request $request, $id)
    {
        if ($redirect = $this->checkifLoginForEmployee()) {
            return $redirect;
        }

        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $alamatRumah = AlamatRumah::where('id', $id)
            ->where('id_karyawan', session('id'))
            ->first();

        if (!$alamatRumah) {
            return redirect('/dashboard/karyawan/alamat')->with('error', 'Address data not found');
        }

        // [LOG CONTEXT] Update koordinat
        Log::info('Employee Updating Location Coordinates', [
            'address_id' => $id,
            'old_coords' => ['lat' => $alamatRumah->latitude, 'lng' => $alamatRumah->longitude],
            'new_coords' => ['lat' => $request->latitude, 'lng' => $request->longitude]
        ]);

        $alamatRumahBaru = $this->getLocationDetails($request->latitude, $request->longitude);

        $alamatRumah->update([
            'alamat_rumah' => $alamatRumahBaru,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'updated_at' => now(),
        ]);

        return redirect('/dashboard/karyawan/alamat')->with('success', 'Address data updated successfully');
    }

    public function restore(string $id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        Controller::checkifLoginForCompany();

        Log::info('Restoring Address Record', ['address_id' => $id]);

        AlamatRumah::withTrashed()->where('id', $id)->restore();

        return redirect('dashboard/perusahaan/alamat')->with('success', 'Data Successfully Restored');
    }

    // --- Helper Functions for Geocoding ---

    public function getLocationNameIndonesia($latitude, $longitude)
    {
        $apiKey = env('ORS_API_KEY');

        if (!$apiKey) {
            Log::error('ORS API Error: API Key is missing in .env');
            return 'Alamat tidak tersedia';
        }

        // [LOG CONTEXT] External Call
        Log::info('Geocoding Request (Indonesia)', ['lat' => $latitude, 'lng' => $longitude]);

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return "Koordinat: {$latitude}, {$longitude}";
        }

        $response = Http::timeout(10)->withHeaders([
            'Authorization' => $apiKey,
            'Content-Type' => 'application/json',
            'Accept-Language' => 'id',
        ])->get('https://api.openrouteservice.org/geocode/reverse', [
            'point.lon' => (float) $longitude,
            'point.lat' => (float) $latitude,
            'size' => 1,
            'layers' => 'address,venue,street,neighbourhood,locality,county,region,country',
            'lang' => 'id',
        ]);

        if (!$response->successful()) {
            Log::error('Geocoding API Failed', ['status' => $response->status(), 'body' => $response->body()]);
            return "Koordinat: {$latitude}, {$longitude}";
        }

        $data = $response->json();

        if (empty($data['features'])) {
            return "Koordinat: {$latitude}, {$longitude}";
        }

        $properties = $data['features'][0]['properties'];
        $parts = [];

        // Logic penyusunan alamat
        if (!empty($properties['name'])) {
            $parts[] = $properties['name'];
        }

        $kelurahan = $properties['neighbourhood'] ?? $properties['locality'] ?? null;
        if ($kelurahan && !$this->isDuplicateLocation($kelurahan, $parts)) {
            $parts[] = $kelurahan;
        }

        if (!empty($properties['county'])) {
            $county = $this->cleanIndonesianLocationName($properties['county']);
            if (!$this->isDuplicateLocation($county, $parts)) {
                $parts[] = $county;
            }
        }

        if (!empty($properties['region'])) {
            $region = $this->cleanIndonesianLocationName($properties['region']);
            if (!$this->isDuplicateLocation($region, $parts)) {
                $parts[] = $region;
            }
        }

        $locationName = implode(', ', array_filter($parts));

        Log::info('Geocoding Success', ['result' => $locationName]);

        return empty($locationName) ? "Koordinat: {$latitude}, {$longitude}" : $locationName;
    }

    private function cleanIndonesianLocationName($name)
    {
        $prefixes = ['Kabupaten ', 'Kota ', 'Provinsi ', 'Kec. ', 'Kel. ', 'Desa '];
        foreach ($prefixes as $prefix) {
            if (stripos($name, $prefix) === 0) {
                $name = substr($name, strlen($prefix));
                break;
            }
        }
        return trim($name);
    }

    private function isDuplicateLocation($newLocation, $existingParts)
    {
        $newLocationLower = strtolower($this->cleanIndonesianLocationName($newLocation));
        foreach ($existingParts as $existing) {
            $existingLower = strtolower($this->cleanIndonesianLocationName($existing));
            if (
                $newLocationLower === $existingLower ||
                strpos($newLocationLower, $existingLower) !== false ||
                strpos($existingLower, $newLocationLower) !== false
            ) {
                return true;
            }
        }
        return false;
    }

    public function getLocationDetails($latitude, $longitude)
    {
        $apiKey = env('ORS_API_KEY');

        if (!$apiKey) {
            Log::error('ORS API Error: API Key missing');
            return 'API key tidak tersedia';
        }

        // [LOG CONTEXT] External Call Detail
        Log::info('Detailed Geocoding Request', ['lat' => $latitude, 'lng' => $longitude]);

        $response = Http::timeout(10)->withHeaders([
            'Authorization' => $apiKey,
            'Content-Type' => 'application/json',
            'Accept-Language' => 'id',
        ])->get('https://api.openrouteservice.org/geocode/reverse', [
            'point.lon' => (float) $longitude,
            'point.lat' => (float) $latitude,
            'size' => 1,
            'layers' => 'address,venue,street,neighbourhood,locality,county,region,country',
            'lang' => 'id',
        ]);

        if (!$response->successful()) {
            Log::error('Detailed Geocoding Failed', ['status' => $response->status()]);
            return 'Gagal mengambil data lokasi';
        }

        $data = $response->json();

        if (empty($data['features'])) {
            return 'Lokasi tidak ditemukan';
        }

        $feature = $data['features'][0];
        $properties = $feature['properties'];
        $addressParts = [];

        if (!empty($properties['name'])) $addressParts[] = $properties['name'];

        $locality = $properties['locality'] ?? '';
        $county = $properties['county'] ?? '';

        if (!empty($locality) && !empty($county)) {
            $localityClean = str_replace(['Kota ', 'Kabupaten '], '', $locality);
            $countyClean = str_replace(['Kota ', 'Kabupaten '], '', $county);

            if (stripos($localityClean, $countyClean) !== false || stripos($countyClean, $localityClean) !== false) {
                $addressParts[] = $locality;
            } else {
                $addressParts[] = $locality;
                $addressParts[] = $county;
            }
        } elseif (!empty($locality)) {
            $addressParts[] = $locality;
        } elseif (!empty($county)) {
            $addressParts[] = $county;
        }

        if (!empty($properties['region'])) $addressParts[] = $properties['region'];
        if (!empty($properties['country'])) $addressParts[] = $properties['country'];
        if (!empty($properties['postalcode'])) $addressParts[] = $properties['postalcode'];

        $formattedAddress = implode(', ', array_filter($addressParts));

        if (empty($formattedAddress)) {
            $formattedAddress = "Koordinat: {$latitude}, {$longitude}";
        }

        return $formattedAddress;
    }
}
