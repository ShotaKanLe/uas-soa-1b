<?php

namespace App\Http\Controllers;

use App\Models\AlamatRumah;
use App\Models\KaryawanPerusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AlamatRumahController extends Controller
{
    public function index()
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        $alamats  = AlamatRumah::latest()->paginate(5);
        $dataType = 'alamat';
        // $alamats = PerjalananKaryawanPerusahaan::all();

        // return ($alamats);
        return view('dashboardPerusahaan.layouts.alamatRumah.view', ['data' => $alamats, 'dataType' => $dataType]);
    }

    public function indexAlamatKaryawan()
    {
        if ($redirect = $this->checkifLoginForEmployee()) {
            return $redirect;
        }
        $alamats = AlamatRumah::where('id_karyawan', session('id'))
            ->latest()
            ->paginate(5);

        $dataType = 'alamat';
        // $alamats = PerjalananKaryawanPerusahaan::all();

        // return ($alamats);
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
        AlamatRumah::destroy($id);

        return redirect('dashboard/perusahaan/alamat')->with('success', 'Data Successfully Deleted');
    }

    public function deleteAlamatKaryawan($id)
    {
        if ($redirect = $this->checkifLoginForEmployee()) {
            return $redirect;
        }
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

        // return ($oldData);

        return view('dashboardPerusahaan.layouts.alamatRumah.edit', ['dataKaryawan' => $karyawans, 'oldData' => $oldData, 'id' => $id]);
    }

    public function editAlamatKaryawan($id)
    {
        if ($redirect = $this->checkifLoginForEmployee()) {
            return $redirect;
        }

        // Ambil data alamat berdasarkan ID dan pastikan milik karyawan yang sedang login
        $alamatRumah = AlamatRumah::where('id', $id)
            ->where('id_karyawan', session('id'))
            ->first();

        if (!$alamatRumah) {
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

        // Validasi input
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        // Ambil data alamat berdasarkan ID dan pastikan milik karyawan yang sedang login
        $alamatRumah = AlamatRumah::where('id', $id)
            ->where('id_karyawan', session('id'))
            ->first();

        if (!$alamatRumah) {
            return redirect('/dashboard/karyawan/alamat')->with('error', 'Address data not found');
        }

        // Dapatkan detail alamat dari koordinat yang baru
        $alamatRumahBaru = $this->getLocationDetails($request->latitude, $request->longitude);

        // Update data alamat
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
        AlamatRumah::withTrashed()->where('id', $id)->restore();

        return redirect('dashboard/perusahaan/alamat')->with('success', 'Data Successfully Restored');
    }

    public function getLocationNameIndonesia($latitude, $longitude)
    {
        $apiKey = env('ORS_API_KEY');

        if (!$apiKey) {
            Log::error('API key kosong!');
            return 'Alamat tidak tersedia';
        }

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return "Koordinat: {$latitude}, {$longitude}";
        }

        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            return "Koordinat tidak valid: {$latitude}, {$longitude}";
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
            return "Koordinat: {$latitude}, {$longitude}";
        }

        $data = $response->json();

        if (empty($data['features'])) {
            return "Koordinat: {$latitude}, {$longitude}";
        }

        $properties = $data['features'][0]['properties'];

        // Format khusus Indonesia: Jalan, Kelurahan, Kecamatan, Kabupaten/Kota, Provinsi
        $parts = [];

        // 1. Nama jalan atau tempat
        if (!empty($properties['name'])) {
            $parts[] = $properties['name'];
        }

        // 2. Kelurahan/Desa (neighbourhood atau locality)
        $kelurahan = $properties['neighbourhood'] ?? $properties['locality'] ?? null;
        if ($kelurahan && !$this->isDuplicateLocation($kelurahan, $parts)) {
            $parts[] = $kelurahan;
        }

        // 3. Kecamatan/kabupaten (county) - filter untuk Indonesia
        if (!empty($properties['county'])) {
            $county = $properties['county'];

            // Hapus prefix yang umum di Indonesia
            $county = $this->cleanIndonesianLocationName($county);

            if (!$this->isDuplicateLocation($county, $parts)) {
                $parts[] = $county;
            }
        }

        // 4. Provinsi (region)
        if (!empty($properties['region'])) {
            $region = $properties['region'];
            $region = $this->cleanIndonesianLocationName($region);

            if (!$this->isDuplicateLocation($region, $parts)) {
                $parts[] = $region;
            }
        }

        $locationName = implode(', ', array_filter($parts));

        return empty($locationName) ? "Koordinat: {$latitude}, {$longitude}" : $locationName;
    }

    // Helper function untuk membersihkan nama lokasi Indonesia
    private function cleanIndonesianLocationName($name)
    {
        // Hapus prefix umum Indonesia
        $prefixes = ['Kabupaten ', 'Kota ', 'Provinsi ', 'Kec. ', 'Kel. ', 'Desa '];

        foreach ($prefixes as $prefix) {
            if (stripos($name, $prefix) === 0) {
                $name = substr($name, strlen($prefix));
                break;
            }
        }

        return trim($name);
    }

    // Helper function untuk cek duplikasi
    private function isDuplicateLocation($newLocation, $existingParts)
    {
        $newLocationLower = strtolower($this->cleanIndonesianLocationName($newLocation));

        foreach ($existingParts as $existing) {
            $existingLower = strtolower($this->cleanIndonesianLocationName($existing));

            // Cek jika sama persis atau saling mengandung
            if (
                $newLocationLower === $existingLower ||
                strpos($newLocationLower, $existingLower) !== false ||
                strpos($existingLower, $newLocationLower) !== false
            ) {
                return true;
            }
        }

        return false;

        // Validasi input koordinat
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            Log::error('Koordinat tidak valid', [
                'latitude' => $latitude,
                'longitude' => $longitude
            ]);
            return null;
        }

        // Validasi range latitude dan longitude
        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            Log::error('Koordinat di luar jangkauan yang valid', [
                'latitude' => $latitude,
                'longitude' => $longitude
            ]);
            return null;
        }

        $response = Http::timeout(10)->withHeaders([
            'Authorization' => $apiKey,
            'Content-Type' => 'application/json',
            'Accept-Language' => 'id', // Bahasa Indonesia
        ])->get('https://api.openrouteservice.org/geocode/reverse', [
            'point.lon' => (float) $longitude,
            'point.lat' => (float) $latitude,
            'size' => 1, // Hanya ambil 1 hasil teratas
            'layers' => 'address,venue,street,neighbourhood,locality,county,region,country', // Layer yang diinginkan
            'lang' => 'id', // Parameter bahasa Indonesia
        ]);

        if (!$response->successful()) {
            Log::error('ORS Geocoding API gagal:', [
                'status' => $response->status(),
                'body' => $response->body(),
                'coordinates' => ['lat' => $latitude, 'lng' => $longitude]
            ]);
            return null;
        }

        $data = $response->json();

        // Cek apakah ada hasil
        if (empty($data['features'])) {
            Log::warning('Tidak ada lokasi ditemukan untuk koordinat:', [
                'latitude' => $latitude,
                'longitude' => $longitude
            ]);
            return null;
        }

        $feature = $data['features'][0];
        $properties = $feature['properties'];

        // Susun nama lokasi dengan prioritas tertentu dan hindari duplikasi
        $locationParts = [];
        $addedParts = []; // Array untuk melacak bagian yang sudah ditambahkan

        // Ambil nama jalan/alamat jika ada
        if (!empty($properties['name']) && !in_array(strtolower($properties['name']), $addedParts)) {
            $locationParts[] = $properties['name'];
            $addedParts[] = strtolower($properties['name']);
        }

        // Ambil kelurahan/desa (prioritas neighbourhood > locality)
        if (!empty($properties['neighbourhood']) && !in_array(strtolower($properties['neighbourhood']), $addedParts)) {
            $locationParts[] = $properties['neighbourhood'];
            $addedParts[] = strtolower($properties['neighbourhood']);
        } elseif (!empty($properties['locality']) && !in_array(strtolower($properties['locality']), $addedParts)) {
            $locationParts[] = $properties['locality'];
            $addedParts[] = strtolower($properties['locality']);
        }

        // Ambil kecamatan/kabupaten - cek apakah berbeda dengan yang sudah ada
        if (!empty($properties['county']) && !in_array(strtolower($properties['county']), $addedParts)) {
            // Cek apakah county mengandung kata yang sama dengan locality/neighbourhood
            $countyLower = strtolower($properties['county']);
            $isDuplicate = false;

            foreach ($addedParts as $existingPart) {
                // Jika county mengandung bagian yang sama atau sebaliknya
                if (strpos($countyLower, $existingPart) !== false || strpos($existingPart, $countyLower) !== false) {
                    $isDuplicate = true;
                    break;
                }
            }

            if (!$isDuplicate) {
                $locationParts[] = $properties['county'];
                $addedParts[] = $countyLower;
            }
        }

        // Ambil provinsi - pastikan tidak duplikasi dengan county
        if (!empty($properties['region']) && !in_array(strtolower($properties['region']), $addedParts)) {
            $regionLower = strtolower($properties['region']);
            $isDuplicate = false;

            foreach ($addedParts as $existingPart) {
                if (strpos($regionLower, $existingPart) !== false || strpos($existingPart, $regionLower) !== false) {
                    $isDuplicate = true;
                    break;
                }
            }

            if (!$isDuplicate) {
                $locationParts[] = $properties['region'];
                $addedParts[] = $regionLower;
            }
        }

        // Ambil negara - biasanya selalu "Indonesia" untuk koordinat di Indonesia
        if (!empty($properties['country']) && strtolower($properties['country']) !== 'indonesia') {
            $locationParts[] = $properties['country'];
        }

        // Gabungkan dengan koma
        $locationName = implode(', ', array_filter($locationParts));

        // Fallback jika tidak ada nama yang ditemukan
        if (empty($locationName)) {
            $locationName = "Koordinat: {$latitude}, {$longitude}";
        }

        return $locationName;
    }

    // Fungsi alternatif yang mengembalikan data lebih lengkap
    public function getLocationDetails($latitude, $longitude)
    {
        $apiKey = env('ORS_API_KEY');

        if (!$apiKey) {
            Log::error('API key kosong!');
            return 'API key tidak tersedia';
        }

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            Log::error('Koordinat tidak valid');
            return 'Koordinat tidak valid';
        }

        $response = Http::timeout(10)->withHeaders([
            'Authorization' => $apiKey,
            'Content-Type' => 'application/json',
            'Accept-Language' => 'id', // Bahasa Indonesia
        ])->get('https://api.openrouteservice.org/geocode/reverse', [
            'point.lon' => (float) $longitude,
            'point.lat' => (float) $latitude,
            'size' => 1,
            'layers' => 'address,venue,street,neighbourhood,locality,county,region,country',
            'lang' => 'id', // Parameter bahasa Indonesia
        ]);

        if (!$response->successful()) {
            Log::error('ORS Geocoding API gagal:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return 'Gagal mengambil data lokasi';
        }

        $data = $response->json();

        if (empty($data['features'])) {
            return 'Lokasi tidak ditemukan';
        }

        $feature = $data['features'][0];
        $properties = $feature['properties'];

        // Susun alamat yang terformat dengan prioritas komponen alamat
        $addressParts = [];

        // Tambahkan nama tempat jika ada
        if (!empty($properties['name'])) {
            $addressParts[] = $properties['name'];
        }

        // Logika untuk menghindari duplikasi kota/kabupaten
        $locality = $properties['locality'] ?? '';
        $county = $properties['county'] ?? '';

        // Cek apakah locality dan county mengandung nama yang sama
        if (!empty($locality) && !empty($county)) {
            // Hapus prefix "Kota " atau "Kabupaten " untuk perbandingan
            $localityClean = str_replace(['Kota ', 'Kabupaten '], '', $locality);
            $countyClean = str_replace(['Kota ', 'Kabupaten '], '', $county);

            // Jika nama dasarnya sama, prioritaskan yang lebih spesifik (locality)
            if (stripos($localityClean, $countyClean) !== false || stripos($countyClean, $localityClean) !== false) {
                $addressParts[] = $locality; // Gunakan locality saja
            } else {
                // Jika berbeda, tambahkan keduanya
                $addressParts[] = $locality;
                $addressParts[] = $county;
            }
        } elseif (!empty($locality)) {
            $addressParts[] = $locality;
        } elseif (!empty($county)) {
            $addressParts[] = $county;
        }

        // Tambahkan provinsi
        if (!empty($properties['region'])) {
            $addressParts[] = $properties['region'];
        }

        // Tambahkan negara
        if (!empty($properties['country'])) {
            $addressParts[] = $properties['country'];
        }

        // Tambahkan kode pos jika ada
        if (!empty($properties['postalcode'])) {
            $addressParts[] = $properties['postalcode'];
        }

        // Gabungkan semua bagian alamat dengan koma
        $formattedAddress = implode(', ', array_filter($addressParts));

        // Jika tidak ada alamat yang ditemukan, gunakan koordinat
        if (empty($formattedAddress)) {
            $formattedAddress = "Koordinat: {$latitude}, {$longitude}";
        }

        return $formattedAddress;
    }

    public function getLocationName($latitude, $longitude)
    {
        $apiKey = env('ORS_API_KEY');

        if (!$apiKey) {
            Log::error('API key kosong!');
            return null;
        }

        // Validasi input koordinat
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            Log::error('Koordinat tidak valid', [
                'latitude' => $latitude,
                'longitude' => $longitude
            ]);
            return null;
        }

        // Validasi range latitude dan longitude
        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            Log::error('Koordinat di luar jangkauan yang valid', [
                'latitude' => $latitude,
                'longitude' => $longitude
            ]);
            return null;
        }

        $response = Http::timeout(10)->withHeaders([
            'Authorization' => $apiKey,
            'Content-Type' => 'application/json',
            'Accept-Language' => 'id', // Bahasa Indonesia
        ])->get('https://api.openrouteservice.org/geocode/reverse', [
            'point.lon' => (float) $longitude,
            'point.lat' => (float) $latitude,
            'size' => 1, // Hanya ambil 1 hasil teratas
            'layers' => 'address,venue,street,neighbourhood,locality,county,region,country', // Layer yang diinginkan
            'lang' => 'id', // Parameter bahasa Indonesia
        ]);

        if (!$response->successful()) {
            Log::error('ORS Geocoding API gagal:', [
                'status' => $response->status(),
                'body' => $response->body(),
                'coordinates' => ['lat' => $latitude, 'lng' => $longitude]
            ]);
            return null;
        }

        $data = $response->json();

        // Cek apakah ada hasil
        if (empty($data['features'])) {
            Log::warning('Tidak ada lokasi ditemukan untuk koordinat:', [
                'latitude' => $latitude,
                'longitude' => $longitude
            ]);
            return null;
        }

        $feature = $data['features'][0];
        $properties = $feature['properties'];

        // Susun nama lokasi dengan prioritas tertentu dan hindari duplikasi
        $locationParts = [];
        $addedParts = []; // Array untuk melacak bagian yang sudah ditambahkan

        // Ambil nama jalan/alamat jika ada
        if (!empty($properties['name']) && !in_array(strtolower($properties['name']), $addedParts)) {
            $locationParts[] = $properties['name'];
            $addedParts[] = strtolower($properties['name']);
        }

        // Ambil kelurahan/desa (prioritas neighbourhood > locality)
        if (!empty($properties['neighbourhood']) && !in_array(strtolower($properties['neighbourhood']), $addedParts)) {
            $locationParts[] = $properties['neighbourhood'];
            $addedParts[] = strtolower($properties['neighbourhood']);
        } elseif (!empty($properties['locality']) && !in_array(strtolower($properties['locality']), $addedParts)) {
            $locationParts[] = $properties['locality'];
            $addedParts[] = strtolower($properties['locality']);
        }

        // Ambil kecamatan/kabupaten - cek apakah berbeda dengan yang sudah ada
        if (!empty($properties['county']) && !in_array(strtolower($properties['county']), $addedParts)) {
            // Cek apakah county mengandung kata yang sama dengan locality/neighbourhood
            $countyLower = strtolower($properties['county']);
            $isDuplicate = false;

            foreach ($addedParts as $existingPart) {
                // Jika county mengandung bagian yang sama atau sebaliknya
                if (strpos($countyLower, $existingPart) !== false || strpos($existingPart, $countyLower) !== false) {
                    $isDuplicate = true;
                    break;
                }
            }

            if (!$isDuplicate) {
                $locationParts[] = $properties['county'];
                $addedParts[] = $countyLower;
            }
        }

        // Ambil provinsi - pastikan tidak duplikasi dengan county
        if (!empty($properties['region']) && !in_array(strtolower($properties['region']), $addedParts)) {
            $regionLower = strtolower($properties['region']);
            $isDuplicate = false;

            foreach ($addedParts as $existingPart) {
                if (strpos($regionLower, $existingPart) !== false || strpos($existingPart, $regionLower) !== false) {
                    $isDuplicate = true;
                    break;
                }
            }

            if (!$isDuplicate) {
                $locationParts[] = $properties['region'];
                $addedParts[] = $regionLower;
            }
        }

        // Ambil negara - biasanya selalu "Indonesia" untuk koordinat di Indonesia
        if (!empty($properties['country']) && strtolower($properties['country']) !== 'indonesia') {
            $locationParts[] = $properties['country'];
        }

        // Gabungkan dengan koma
        $locationName = implode(', ', array_filter($locationParts));

        // Fallback jika tidak ada nama yang ditemukan
        if (empty($locationName)) {
            $locationName = "Koordinat: {$latitude}, {$longitude}";
        }

        return $locationName;
    }
}
