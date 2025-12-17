<?php

namespace App\Http\Controllers;

use App\Models\Code;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CodeController extends Controller
{
    public function generateCode(Request $request)
    {
        $codeStr = 'STAFF-'.strtoupper(Str::random(6));

        // Cek apakah kode sudah pernah dibuat
        $checkDuplicate = Code::where('code', $codeStr)->first();

        // Jika sudah ada, panggil ulang dan return hasilnya
        if ($checkDuplicate) {
            return $this->generateCode($request);
        }

        // Simpan ke database
        $code = Code::create([
            'code' => $codeStr,
            'code_type' => 'STAFF',
            'status' => 'UNUSED',
        ]);

        return response()->json(['staff_code' => $codeStr]);
    }
}
