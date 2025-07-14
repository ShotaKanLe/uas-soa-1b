<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Route::post('/midtrans/callback', [PaymentController::class, 'midtransCallback']);

// Route::get('/test-api', function () {
//     return 'API route working';
// });

Route::middleware(['auth:staffPerusahaan'])->group(function () {
    Route::get('/company-profile', function () {
        try {
            $staff = \App\Models\StaffPerusahaan::where('id', session('staff_id'))->first();
            if (! $staff) {
                return response()->json(['error' => 'Staff not found'], 404);
            }

            $perusahaan = \App\Models\Perusahaan::with('service')->find($staff->id_perusahaan);
            if (! $perusahaan) {
                return response()->json(['error' => 'Company not found'], 404);
            }

            return response()->json($perusahaan);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    });
});
