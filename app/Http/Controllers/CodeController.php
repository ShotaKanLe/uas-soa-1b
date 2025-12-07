<?php

namespace App\Http\Controllers;

use App\Models\Code;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // <--- WAJIB IMPORT LOG
use Illuminate\Support\Str;

class CodeController extends Controller
{
    public function generateCode(Request $request)
    {
        // [LOG CONTEXT] Mencatat permintaan generate code
        Log::info('Generating New Staff Code Attempt', [
            'user_id' => session('id') ?? 'guest', // Log siapa yang request (jika ada session)
            'type' => 'STAFF'
        ]);

        $codeStr = 'STAFF-' . strtoupper(Str::random(6));

        // Cek apakah kode sudah pernah dibuat
        $checkDuplicate = Code::where('code', $codeStr)->first();

        // Jika sudah ada, panggil ulang dan return hasilnya
        if ($checkDuplicate) {
            // [LOG CONTEXT] Peringatan jika terjadi tabrakan kode (Collision)
            Log::warning('Code Collision Detected - Retrying', ['collision_code' => $codeStr]);

            return $this->generateCode($request);
        }

        // Simpan ke database
        $code = Code::create([
            'code' => $codeStr,
            'code_type' => 'STAFF',
            'status' => 'UNUSED',
        ]);

        // [LOG CONTEXT] Log sukses pembuatan kode
        Log::info('Staff Code Generated Successfully', [
            'code' => $codeStr,
            'code_id' => $code->id
        ]);

        return response()->json(['staff_code' => $codeStr]);
    }
}
