<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MapController extends Controller
{
    public function store(Request $request)
    {
        $latitude  = $request->input('latitude');
        $longitude = $request->input('longitude');

        // Lakukan sesuatu, misal simpan ke DB atau session
        // Contoh:
        // Location::create(['lat' => $latitude, 'lng' => $longitude]);

        return back()->with('success', 'Lokasi berhasil disimpan: '.$latitude.', '.$longitude);
    }
}
