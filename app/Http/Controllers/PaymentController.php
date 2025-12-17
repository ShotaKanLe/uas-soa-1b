<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\Perusahaan;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function index()
    {
        $dataService = Service::all();

        return view('home.services.service', ['services' => $dataService]);
    }

    public function paymentSuccess()
    {
        if (! session()->has('payment_success')) {
            abort(403, 'Akses tidak sah.');
        }

        session()->forget('payment_success'); // hanya bisa diakses sekali

        $dataService = Service::all();

        $code = $this->generateCodePerusahaan();

        $codeEmp = $this->generateCodeEmployee();

        return view('home.services.success', ['services' => $dataService, 'code' => $code]);
    }

    public function generateCodePerusahaan()
    {
        $codeStr = 'PERUSAHAAN-'.strtoupper(Str::random(6));

        // Cek apakah kode sudah pernah dibuat
        $checkDuplicate = Code::where('code', $codeStr)->first();

        // Jika sudah ada, panggil ulang dan return hasilnya
        if ($checkDuplicate) {
            return $this->generateCodePerusahaan();
        }

        // Simpan ke database
        $code = Code::create([
            'code' => $codeStr,
            'code_type' => 'PERUSAHAAN',
            'status' => 'UNUSED',
        ]);

        return $codeStr;
    }

    public function generateCodeEmployee()
    {
        $codeStr = 'EMPLOYEE-'.strtoupper(Str::random(6));

        // Cek apakah kode sudah pernah dibuat
        $checkDuplicate = Code::where('code', $codeStr)->first();

        // Jika sudah ada, panggil ulang dan return hasilnya
        if ($checkDuplicate) {
            return $this->generateCodeEmployee();
        }

        // Simpan ke database
        $code = Code::create([
            'code' => $codeStr,
            'code_type' => 'EMPLOYEE',
            'status' => 'UNUSED',
        ]);

        return $codeStr;
    }

    public function getSnapToken(Request $request)
    {
        try {
            Config::$serverKey    = config('services.midtrans.serverKey');
            Config::$isProduction = config('services.midtrans.isProduction');
            Config::$isSanitized  = true;
            Config::$is3ds        = true;

            $request->validate([
                'companyName' => 'required',
                'email' => 'required|email',
                'latitude' => 'required',
                'longitude' => 'required',
                'idService' => 'required',
            ]);

            $orderId = uniqid(); // Order ID masih penting

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => 150000,
                ],
                'customer_details' => [
                    'first_name' => $request->input('companyName'),
                    'email' => $request->input('email'),
                ],
                'callbacks' => [
                    'finish' => route('register.success'),
                ],
                // Tidak perlu notification_url jika tidak pakai callback
            ];

            $snapToken = Snap::getSnapToken($params);

            return response()->json(['token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // MidtransController.php
    public function checkStatus($order_id)
    {
        \Midtrans\Config::$serverKey    = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = false; // atau true kalau sudah live

        $status = \Midtrans\Transaction::status($order_id);

        return response()->json($status);
    }

    public function generateCodeCompany()
    {
        $codeStr = 'COMP-'.strtoupper(Str::random(6));

        // Cek apakah kode sudah pernah dibuat
        $checkDuplicate = Code::where('code', $codeStr)->first();

        // Jika sudah ada, panggil ulang dan return hasilnya
        if ($checkDuplicate) {
            return $this->generateCodeCompany();
        }

        // Simpan ke database
        $code = Code::create([
            'code' => $codeStr,
            'code_type' => 'COMPANY',
            'status' => 'UNUSED',
        ]);

        return $codeStr;
    }

    public function insertDataCompany($request)
    {
        $latitude  = $request->input('latitude');
        $longitude = $request->input('longitude');
        $code      = $this->generateCodeCompany();

        Perusahaan::create([
            'nama_perusahaan' => $request->input('companyName'),
            'kode_perusahaan' => $code,
            'email_perusahaan' => $request->input('email'),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'tanggal_aktif_service' => Carbon::now(),
            'id_service' => $request->input('idService'),

        ]);
    }

    public function insertFromFrontend(Request $request)
    {
        try {
            $this->insertDataCompany($request);

            // dd($request->all());
            return response()->json(['message' => 'Perusahaan berhasil disimpan']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
