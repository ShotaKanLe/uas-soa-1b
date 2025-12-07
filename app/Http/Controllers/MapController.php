<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // <--- WAJIB IMPORT LOG

class MapController extends Controller
{
    public function store(Request $request)
    {
        $latitude  = $request->input('latitude');
        $longitude = $request->input('longitude');

        // [LOG CONTEXT] Mencatat penerimaan data lokasi (Latitude & Longitude)
        Log::info('Storing User Location Coordinates', [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'user_id' => session('id') ?? 'guest' // Mencatat ID user atau 'guest' jika belum login
        ]);

        // Lakukan sesuatu, misal simpan ke DB atau session
        // Contoh:
        // Location::create(['lat' => $latitude, 'lng' => $longitude]);

        return back()->with('success', 'Lokasi berhasil disimpan: '.$latitude.', '.$longitude);
    }
}
