<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // <--- WAJIB IMPORT LOG

abstract class Controller
{
    public function globalCheck()
    {
        if (! Auth::guard('staff')->check() && ! Auth::guard('staffPerusahaan')->check() && ! Auth::guard('karyawanPerusahaan')->check()) {

            // [LOG CONTEXT] Mencatat akses tanpa login (Unauthenticated)
            // Log ini akan menangkap Trace ID dari middleware, jadi kita tahu IP mana yang mencoba akses
            Log::warning('Unauthenticated Access Attempt', [
                'action' => 'globalCheck',
                'result' => 'Redirecting to Login Page'
            ]);

            return redirect()->route('login');
        }
    }

    public function checkifLoginForStaff()
    {
        if ($redirect = $this->globalCheck()) {
            return $redirect;
        }

        if (Auth::guard('staffPerusahaan')->check()) {
            // [LOG CONTEXT] Mencatat akses salah role (Perusahaan mencoba akses halaman Staff Mitra)
            Log::warning('Unauthorized Role Access Attempt', [
                'expected_role' => 'staff_mitra',
                'actual_role' => 'staff_perusahaan',
                'user_id' => Auth::guard('staffPerusahaan')->id(),
                'redirect_to' => 'dashboard.perusahaan'
            ]);

            return redirect()->route('dashboard.perusahaan');
        }

        if (Auth::guard('karyawanPerusahaan')->check()) {
            // [LOG CONTEXT] Mencatat akses salah role
            Log::warning('Unauthorized Role Access Attempt', [
                'expected_role' => 'staff_mitra',
                'actual_role' => 'karyawan_perusahaan',
                'user_id' => Auth::guard('karyawanPerusahaan')->id(),
                'redirect_to' => 'dashboard.karyawan'
            ]);

            return redirect()->route('dashboard.karyawan');
        }

        return null;
    }

    public function checkifLoginForCompany()
    {
        if ($redirect = $this->globalCheck()) {
            return $redirect;
        }

        if (Auth::guard('staff')->check()) {
            // [LOG CONTEXT] Staff Mitra salah masuk ke halaman Perusahaan
            Log::warning('Unauthorized Role Access Attempt', [
                'expected_role' => 'staff_perusahaan',
                'actual_role' => 'staff_mitra',
                'user_id' => Auth::guard('staff')->id(),
                'redirect_to' => 'dashboard.staff'
            ]);

            return redirect()->route('dashboard.staff');
        }

        if (Auth::guard('karyawanPerusahaan')->check()) {
            Log::warning('Unauthorized Role Access Attempt', [
                'expected_role' => 'staff_perusahaan',
                'actual_role' => 'karyawan_perusahaan',
                'user_id' => Auth::guard('karyawanPerusahaan')->id(),
                'redirect_to' => 'dashboard.karyawan'
            ]);

            return redirect()->route('dashboard.karyawan');
        }

        return null;
    }

    public function checkifLoginForEmployee()
    {
        if ($redirect = $this->globalCheck()) {
            return $redirect;
        }

        if (Auth::guard('staff')->check()) {
            Log::warning('Unauthorized Role Access Attempt', [
                'expected_role' => 'karyawan_perusahaan',
                'actual_role' => 'staff_mitra',
                'user_id' => Auth::guard('staff')->id(),
                'redirect_to' => 'dashboard.staff'
            ]);

            return redirect()->route('dashboard.staff');
        }

        if (Auth::guard('staffPerusahaan')->check()) {
            Log::warning('Unauthorized Role Access Attempt', [
                'expected_role' => 'karyawan_perusahaan',
                'actual_role' => 'staff_perusahaan',
                'user_id' => Auth::guard('staffPerusahaan')->id(),
                'redirect_to' => 'dashboard.perusahaan'
            ]);

            return redirect()->route('dashboard.perusahaan');
        }

        return null;
    }
}
