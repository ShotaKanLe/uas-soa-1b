<?php

namespace App\Http\Controllers;

use App\Models\Informasi;
use App\Models\Service;
use Illuminate\Support\Facades\Log; // <--- WAJIB IMPORT LOG

class HomeController extends Controller
{
    public function index()
    {
        // [LOG CONTEXT] Mencatat akses halaman utama (Landing Page)
        // Log ini berguna untuk memantau trafik pengunjung website (Public Traffic)
        Log::info('Public Landing Page Accessed', [
            'visitor_ip' => request()->ip(),
            'user_agent' => request()->header('User-Agent')
        ]);

        $dataService = Service::all();

        $dataInformasi = Informasi::all();

        return view('home.app', ['dataService' => $dataService, 'dataInformasi' => $dataInformasi]);
    }
}
