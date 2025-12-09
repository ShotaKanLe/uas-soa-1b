<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\KaryawanPerusahaan;
use App\Models\StaffMitra;
use App\Models\StaffPerusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
        // 1. Validasi Input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->email;
        $password = $request->password;
        $remember = $request->has('remember');

        Log::info('Login Attempt', ['email' => $email]);

        // --- CEK LOGIN STAFF MITRA (PUNYA KAMU) ---
        $staffMitra = StaffMitra::where('email', $email)->first();
        if ($staffMitra && Hash::check($password, $staffMitra->password)) {
            // Set Session
            session(['role' => 'staff']);
            session(['id' => $staffMitra->id]);
            session(['name' => $staffMitra->nama_staff]);
            session(['email' => $staffMitra->email]);

            // Login Guard Staff
            Auth::guard('staff')->login($staffMitra, $remember);

            Log::info('Login Success', ['role' => 'staff_mitra', 'user_id' => $staffMitra->id]);
            return redirect()->route('dashboard.staff');
        }

        // --- CEK LOGIN STAFF PERUSAHAAN (INI YANG HILANG TADI) ---
        $staffPerusahaan = StaffPerusahaan::where('email', $email)->first();
        if ($staffPerusahaan && Hash::check($password, $staffPerusahaan->password)) {
            // Set Session
            session(['role' => 'perusahaan']);
            session(['id' => $staffPerusahaan->id]);
            session(['name' => $staffPerusahaan->nama_staff_perusahaan]);
            session(['email' => $staffPerusahaan->email]);

            // Login Guard Staff Perusahaan
            Auth::guard('staffPerusahaan')->login($staffPerusahaan, $remember);

            Log::info('Login Success', ['role' => 'staff_perusahaan', 'user_id' => $staffPerusahaan->id]);
            return redirect()->route('dashboard.perusahaan');
        }

        // --- CEK LOGIN KARYAWAN PERUSAHAAN (INI JUGA HILANG TADI) ---
        $karyawan = KaryawanPerusahaan::where('email', $email)->first();
        if ($karyawan && Hash::check($password, $karyawan->password)) {
            // Set Session
            session(['role' => 'karyawan']);
            session(['id' => $karyawan->id]);
            session(['name' => $karyawan->nama_karyawan]);
            session(['email' => $karyawan->email]);

            // Login Guard Karyawan
            Auth::guard('karyawanPerusahaan')->login($karyawan, $remember);

            Log::info('Login Success', ['role' => 'karyawan', 'user_id' => $karyawan->id]);
            return redirect()->route('dashboard.karyawan');
        }

        // Jika semua gagal
        Log::warning('Login Failed: Invalid Credentials', ['email' => $email]);
        return redirect()->back()->withErrors(['email' => 'Email atau password salah']);
    }

    // ... (Sisa fungsi viewRegister, register, dll biarkan seperti update terakhir kamu)

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

        // Cek Kode
        $code = Code::where('code', $validated['code'])->first();

        if (! $code) {
            return redirect()->back()->withErrors(['code' => 'Invalid code']);
        }

        if ($code->status == 'USED') {
            return redirect()->back()->withErrors(['code' => 'Code already used']);
        }

        // Cek Duplikasi Akun
        if ($this->checkDuplicateAcc($validated['email'])) {
             return redirect()->back()->withErrors(['email' => 'Email already exists']);
        }

        $idCode = $code->id;

        // Register Staff Mitra
        if ($code->code_type == 'STAFF') {
            $staffMitra = new StaffMitra;
            $staffMitra->nama_staff = $validated['name'];
            $staffMitra->email = $validated['email'];
            $staffMitra->password = Hash::make($validated['password']);
            $staffMitra->id_code = $idCode;
            $staffMitra->save();

            $code->status = 'USED';
            $code->save();

            Log::info('Register Success: Staff Mitra', ['email' => $validated['email']]);
        }
        // Logic register role lain jika ada, tambahkan disini...

        return redirect()->route('register', ['success' => 'Account created successfully']);
    }

    public function checkDuplicateAcc($email)
    {
        // Pengecekan dengan withTrashed (Solusi Soft Delete)
        if (StaffMitra::withTrashed()->where('email', $email)->first()) return true;
        if (StaffPerusahaan::where('email', $email)->first()) return true;
        if (KaryawanPerusahaan::where('email', $email)->first()) return true;

        return false;
    }

    public function logout()
    {
        session()->forget(['role', 'id', 'name', 'email']);

        Auth::guard('staff')->logout();
        Auth::guard('staffPerusahaan')->logout();
        Auth::guard('karyawanPerusahaan')->logout();

        return redirect()->route('login');
    }

    public function checkifLogin()
    {
        if (Auth::guard('staff')->check()) return redirect()->route('dashboard.staff');
        if (Auth::guard('staffPerusahaan')->check()) return redirect()->route('dashboard.perusahaan');
        if (Auth::guard('karyawanPerusahaan')->check()) return redirect()->route('dashboard.karyawan');
        return null;
    }
}
