<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\KaryawanPerusahaan;
use App\Models\StaffMitra;
use App\Models\StaffPerusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // Logging

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
        // 1. VALIDASI INPUT
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email    = $request->email;
        $password = $request->password;
        $remember = $request->has('remember');

        // Log Percobaan Login
        Log::info('Login Attempt', ['email' => $email]);

        // Coba login sebagai StaffMitra
        $staffMitra = StaffMitra::where('email', $email)->first();
        if ($staffMitra && Hash::check($password, $staffMitra->password)) {
            session(['role' => 'staff']);
            session(['id' => $staffMitra->id]);
            session(['name' => $staffMitra->nama_staff]);
            session(['email' => $staffMitra->email]);
            Auth::guard('staff')->login($staffMitra, $remember);

            // Log Login Berhasil
            Log::info('Login Success', ['role' => 'staff', 'user_id' => $staffMitra->id]);

            return redirect()->route('dashboard.staff');
        }

        // ... (Logika login role lain: StaffPerusahaan, Karyawan) ...
        // (Kode sama seperti sebelumnya untuk role lain)

        // 2. ERROR HANDLING (Login Gagal)
        Log::warning('Login Failed: Invalid Credentials', ['email' => $email]);
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
        // 1. VALIDASI INPUT
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
            'code' => 'required|string',
        ]);

        Log::info('Register Attempt', ['email' => $validated['email'], 'code' => $validated['code']]);

        // 2. ERROR HANDLING (Kode Invalid / Terpakai)
        $code = Code::where('code', $validated['code'])->first();

        if (! $code) {
            Log::warning('Register Failed: Invalid Code', ['code' => $validated['code']]);
            return redirect()->back()->withErrors(['code' => 'Invalid code']);
        }

        if ($code->status == 'USED') {
            Log::warning('Register Failed: Code Used', ['code' => $validated['code']]);
            return redirect()->back()->withErrors(['code' => 'Code already used']);
        }

        // 2. ERROR HANDLING (Duplikasi Akun)
        $checkDuplicateAcc = StaffMitra::withTrashed()->where('email')->first(); {
            Log::warning('Register Failed: Duplicate Email', ['email' => $validated['email']]);
            return redirect()->back()->withErrors(['email' => 'Email already exists']);
        }

        // Proses Simpan Data
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

            Log::info('Register Success: Staff Mitra Created', ['email' => $validated['email']]);
        }
        // ... (Logika register role lain) ...

        return redirect()->route('register', ['success' => 'Account created successfully']);
    }

    // ... (Method checkDuplicateAcc, checkDuplicateName, logout, checkifLogin tetap sama) ...
    public function logout()
    {
        $user = Auth::guard('staff')->user();
        if ($user) Log::info('User Logged Out', ['email' => $user->email]);

        Auth::guard('staff')->logout();
        // ... logout guard lain ...
        session()->forget(['role', 'id']);

        return redirect()->route('login');
    }

    // ... method helper lainnya
    public function checkDuplicateAcc($email) { /* ... */ return false; }
    public function checkDuplicateName($name) { /* ... */ return false; }
    public function checkifLogin() { /* ... */ return null; }
}
