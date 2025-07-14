<?php

namespace App\Http\Controllers;

use App\Models\Informasi;
use App\Models\Service;

class HomeController extends Controller
{
    public function index()
    {
        $dataService = Service::all();

        $dataInformasi = Informasi::all();

        return view('home.app', ['dataService' => $dataService, 'dataInformasi' => $dataInformasi]);
    }
}
