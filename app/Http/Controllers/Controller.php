<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    public function globalCheck()
    {
        if (! Auth::guard('staff')->check() && ! Auth::guard('staffPerusahaan')->check() && ! Auth::guard('karyawanPerusahaan')->check()) {
            return redirect()->route('login');
        }
    }

    public function checkifLoginForStaff()
    {
        if ($redirect = $this->globalCheck()) {
            return $redirect;
        }

        if (Auth::guard('staffPerusahaan')->check()) {
            return redirect()->route('dashboard.perusahaan');
        }

        if (Auth::guard('karyawanPerusahaan')->check()) {
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
            return redirect()->route('dashboard.staff');
        }

        if (Auth::guard('karyawanPerusahaan')->check()) {
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
            return redirect()->route('dashboard.staff');
        }

        if (Auth::guard('staffPerusahaan')->check()) {
            return redirect()->route('dashboard.perusahaan');
        }

        return null;
    }
}
