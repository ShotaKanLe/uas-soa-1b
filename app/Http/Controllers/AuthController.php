<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\KaryawanPerusahaan;
use App\Models\StaffMitra;
use App\Models\StaffPerusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function viewLogin()
    {
        if ($redirect = $this->checkifLogin()) {
            return $redirect;
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email    = $request->email;
        $password = $request->password;
        $remember = $request->has('remember');

        // Coba login sebagai StaffMitra
        $staffMitra = StaffMitra::where('email', $email)->first();
        if ($staffMitra && Hash::check($password, $staffMitra->password)) {
            session(['role' => 'staff']);
            session(['id' => $staffMitra->id]);
            session(['name' => $staffMitra->nama_staff]);
            session(['email' => $staffMitra->email]);
            Auth::guard('staff')->login($staffMitra, $remember);

            return redirect()->route('dashboard.staff');
        }

        // Coba login sebagai StaffPerusahaan
        $staffPerusahaan = StaffPerusahaan::where('email', $email)->first();
        if ($staffPerusahaan && Hash::check($password, $staffPerusahaan->password)) {
            session(['role' => 'perusahaan']);
            session(['id' => $staffPerusahaan->id]);
            session(['name' => $staffPerusahaan->nama_staff]);
            session(['email' => $staffPerusahaan->email]);
            session(['id_perusahaan' => $staffPerusahaan->id_perusahaan]);
            Auth::guard('staffPerusahaan')->login($staffPerusahaan, $remember);

            return redirect()->route('dashboard.perusahaan');
        }

        // Coba login sebagai KaryawanPerusahaan
        $karyawan = KaryawanPerusahaan::where('email', $email)->first();
        if ($karyawan && Hash::check($password, $karyawan->password)) {
            session(['role' => 'karyawan']);
            session(['id' => $karyawan->id]);
            session(['name' => $karyawan->nama_karyawan]);
            session(['email' => $karyawan->email]);
            Auth::guard('karyawanPerusahaan')->login($karyawan, $remember);

            return redirect()->route('dashboard.karyawan');
        }

        return redirect()->back()->withErrors(['email' => 'Email atau password salah']);
    }

    public function viewRegister()
    {
        if ($redirect = $this->checkifLogin()) {
            return $redirect;
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
            'code' => 'required|string',
        ]);

        $code = Code::where('code', $validated['code'])->first();

        if (! $code) {
            return redirect()->back()->withErrors(['code' => 'Invalid code']);
        }

        if ($code->status == 'USED') {
            return redirect()->back()->withErrors(['code' => 'Code already used']);
        }

        $checkDuplicateAcc  = $this->checkDuplicateAcc($validated['email']);
        $checkDuplicateName = $this->checkDuplicateName($validated['name']);

        if ($checkDuplicateName) {
            return redirect()->back()->withErrors(['name' => 'Name already exists']);
        }
        if ($checkDuplicateAcc) {
            return redirect()->back()->withErrors(['email' => 'Email already exists']);
        }

        $idCode = $code->id;

        if ($code->code_type == 'STAFF') {
            $staffMitra             = new StaffMitra;
            $staffMitra->nama_staff = $validated['name'];
            $staffMitra->email      = $validated['email'];
            $staffMitra->password   = Hash::make($validated['password']);
            $staffMitra->id_code    = $idCode;
            $staffMitra->save();

            $code->status = 'USED';
            $code->save();
        } elseif ($code->code_type == 'PERUSAHAAN') {
            $staffPerusahaan             = new StaffPerusahaan;
            $staffPerusahaan->nama_staff = $validated['name'];
            $staffPerusahaan->email      = $validated['email'];
            $staffPerusahaan->password   = Hash::make($validated['password']);
            $staffPerusahaan->id_code    = $idCode;
            $staffPerusahaan->id_perusahaan = 1;
            $staffPerusahaan->save();

            $code->status = 'USED';
            $code->save();
        } elseif ($code->code_type == 'EMPLOYEE') {
            return redirect()->route('employee.register', ['data' => $validated]);
        }

        return redirect()->route('register', ['success' => 'Account created successfully']);
    }

    public function checkDuplicateAcc($email)
    {
        $checkDuplicateAcc = StaffMitra::where('email', $email)->first();

        if ($checkDuplicateAcc) {
            return true;
        }

        $checkDuplicateAcc = StaffPerusahaan::where('email', $email)->first();

        if ($checkDuplicateAcc) {
            return true;
        }

        $checkDuplicateAcc = KaryawanPerusahaan::where('email', $email)->first();

        if ($checkDuplicateAcc) {
            return true;
        }

        return false;
    }

    public function checkDuplicateName($name)
    {
        $checkDuplicateAcc = StaffMitra::where('nama_staff', $name)->first();

        if ($checkDuplicateAcc) {
            return true;
        }

        $checkDuplicateAcc = StaffPerusahaan::where('nama_staff', $name)->first();

        if ($checkDuplicateAcc) {
            return true;
        }

        $checkDuplicateAcc = KaryawanPerusahaan::where('nama_karyawan', $name)->first();

        if ($checkDuplicateAcc) {
            return true;
        }

        return false;
    }

    public function logout()
    {
        Auth::guard('staff')->logout();
        Auth::guard('staffPerusahaan')->logout();
        Auth::guard('karyawanPerusahaan')->logout();
        session()->forget(['role', 'id']);

        return redirect()->route('login');
    }

    public function checkifLogin()
    {
        if (Auth::guard('staff')->check()) {
            return redirect()->route('dashboard.staff');
        }

        if (Auth::guard('staffPerusahaan')->check()) {
            return redirect()->route('dashboard.perusahaan');
        }

        if (Auth::guard('karyawanPerusahaan')->check()) {
            return redirect()->route('dashboard.karyawan');
        }

        return null;
    }
}
