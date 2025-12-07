<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\Perusahaan;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // <--- WAJIB IMPORT LOG
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function index()
    {
        // [LOG CONTEXT] Mencatat user melihat halaman layanan
        Log::info('Viewing Service/Payment Page', ['user_ip' => request()->ip()]);

        $dataService = Service::all();

        return view('home.services.service', ['services' => $dataService]);
    }

    public function paymentSuccess()
    {
        if (! session()->has('payment_success')) {
            // [LOG CONTEXT] Mencatat akses ilegal ke halaman sukses
            Log::warning('Unauthorized Access to Payment Success Page', ['user_ip' => request()->ip()]);
            abort(403, 'Akses tidak sah.');
        }

        session()->forget('payment_success'); // hanya bisa diakses sekali

        // [LOG CONTEXT] Mencatat pembayaran berhasil dan mulai generate kode
        Log::info('Payment Verified - Generating Access Codes', ['timestamp' => Carbon::now()]);

        $dataService = Service::all();

        $code = $this->generateCodePerusahaan();
        $codeEmp = $this->generateCodeEmployee(); // Note: Variabel ini sepertinya belum dipakai di view, tapi tetap di-generate

        return view('home.services.success', ['services' => $dataService, 'code' => $code]);
    }

    public function generateCodePerusahaan()
    {
        $codeStr = 'PERUSAHAAN-' . strtoupper(Str::random(6));

        // Cek apakah kode sudah pernah dibuat
        $checkDuplicate = Code::where('code', $codeStr)->first();

        // Jika sudah ada, panggil ulang dan return hasilnya
        if ($checkDuplicate) {
            // [LOG CONTEXT] Log collision
            Log::warning('Code Collision Detected (PERUSAHAAN) - Retrying', ['code' => $codeStr]);
            return $this->generateCodePerusahaan();
        }

        // Simpan ke database
        Code::create([
            'code' => $codeStr,
            'code_type' => 'PERUSAHAAN',
            'status' => 'UNUSED',
        ]);

        return $codeStr;
    }

    public function generateCodeEmployee()
    {
        $codeStr = 'EMPLOYEE-' . strtoupper(Str::random(6));

        // Cek apakah kode sudah pernah dibuat
        $checkDuplicate = Code::where('code', $codeStr)->first();

        // Jika sudah ada, panggil ulang dan return hasilnya
        if ($checkDuplicate) {
            // [LOG CONTEXT] Log collision
            Log::warning('Code Collision Detected (EMPLOYEE) - Retrying', ['code' => $codeStr]);
            return $this->generateCodeEmployee();
        }

        // Simpan ke database
        Code::create([
            'code' => $codeStr,
            'code_type' => 'EMPLOYEE',
            'status' => 'UNUSED',
        ]);

        return $codeStr;
    }

    public function getSnapToken(Request $request)
    {
        try {
            // [LOG CONTEXT] Mencatat inisiasi pembayaran (Checkout)
            Log::info('Initiating Midtrans Payment Request', [
                'company_name' => $request->companyName,
                'email' => $request->email,
                'service_id' => $request->idService,
                'amount' => 150000 // Hardcoded amount sesuai kode
            ]);

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
            ];

            $snapToken = Snap::getSnapToken($params);

            // [LOG CONTEXT] Log sukses mendapatkan token
            Log::info('Snap Token Generated Successfully', ['order_id' => $orderId]);

            return response()->json(['token' => $snapToken]);
        } catch (\Exception $e) {
            // [LOG CONTEXT] Log gagal
            Log::error('Midtrans Snap Token Failed', [
                'error_message' => $e->getMessage(),
                'email' => $request->email
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function checkStatus($order_id)
    {
        \Midtrans\Config::$serverKey    = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = false;

        try {
            $status = \Midtrans\Transaction::status($order_id);
            return response()->json($status);
        } catch (\Exception $e) {
            Log::error('Check Payment Status Failed', ['order_id' => $order_id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Status check failed'], 500);
        }
    }

    public function generateCodeCompany()
    {
        $codeStr = 'COMP-' . strtoupper(Str::random(6));

        $checkDuplicate = Code::where('code', $codeStr)->first();

        if ($checkDuplicate) {
            Log::warning('Code Collision Detected (COMPANY) - Retrying', ['code' => $codeStr]);
            return $this->generateCodeCompany();
        }

        Code::create([
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

        return $code; // Mengembalikan kode untuk keperluan logging di pemanggil
    }

    public function insertFromFrontend(Request $request)
    {
        try {
            // [LOG CONTEXT] Mencatat registrasi perusahaan baru
            Log::info('Registering New Company via Payment', [
                'company_name' => $request->input('companyName'),
                'email' => $request->input('email')
            ]);

            $code = $this->insertDataCompany($request);

            // [LOG CONTEXT] Registrasi sukses
            Log::info('Company Registration Successful', [
                'company_name' => $request->input('companyName'),
                'generated_company_code' => $code
            ]);

            return response()->json(['message' => 'Perusahaan berhasil disimpan']);
        } catch (\Exception $e) {
            // [LOG CONTEXT] Registrasi gagal
            Log::error('Company Registration Failed', [
                'error_message' => $e->getMessage(),
                'company_name' => $request->input('companyName')
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
