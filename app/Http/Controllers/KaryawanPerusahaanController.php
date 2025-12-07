<?php

namespace App\Http\Controllers;

use App\Models\AlamatRumah;
use App\Models\BahanBakar;
use App\Models\KaryawanPerusahaan;
use App\Models\PerjalananKaryawanPerusahaan;
use App\Models\Transportasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // <--- WAJIB IMPORT LOG

class KaryawanPerusahaanController extends Controller
{
    public function index()
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }

        // [LOG CONTEXT] Mencatat akses daftar karyawan oleh perusahaan
        Log::info('Viewing Company Employee List', [
            'user_id' => session('id'),
            'role' => 'perusahaan'
        ]);

        $karyawans = KaryawanPerusahaan::latest()->paginate(5);
        $dataType  = 'karyawan';

        return view('dashboardPerusahaan.layouts.karyawan.view', ['data' => $karyawans, 'dataType' => $dataType]);
    }

    public function homeKaryawan(Request $request)
    {
        if ($redirect = $this->checkifLoginForEmployee()) {
            return $redirect;
        }

        // [LOG CONTEXT] Mencatat aktivitas dashboard karyawan & filter yang digunakan
        Log::info('Employee Dashboard Access', [
            'user_id' => session('id'),
            'filters_applied' => $request->only(['nama_karyawan', 'nama_bahan_bakar', 'nama_transportasi', 'tanggal_perjalanan'])
        ]);

        $query = PerjalananKaryawanPerusahaan::query();

        // Filter nama_karyawan
        if ($request->filled('nama_karyawan')) {
            $query->whereHas('karyawanPerusahaan', function ($q) use ($request) {
                $q->where('nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
            });
        }

        // Filter nama_bahan_bakar
        if ($request->filled('nama_bahan_bakar')) {
            $query->whereHas('bahanBakar', function ($q) use ($request) {
                $q->where('nama_bahan_bakar', 'like', '%' . $request->nama_bahan_bakar . '%');
            });
        }

        // Filter nama_transportasi
        if ($request->filled('nama_transportasi')) {
            $query->whereHas('transportasi', function ($q) use ($request) {
                $q->where('nama_transportasi', 'like', '%' . $request->nama_transportasi . '%');
            });
        }

        // Filter tanggal_perjalanan
        if ($request->filled('tanggal_perjalanan')) {
            $query->whereDate('tanggal_perjalanan', $request->tanggal_perjalanan);
        }

        $query->orderBy('tanggal_perjalanan', 'desc');

        $perjalanans = $query->paginate(5);

        $karyawans     = KaryawanPerusahaan::all();
        $bahanbakars   = BahanBakar::all();
        $transportasis = Transportasi::all();

        $sudahAbsen = PerjalananKaryawanPerusahaan::where('id_karyawan', session('id'))->where('tanggal_perjalanan', Carbon::now()->format('Y-m-d'))->first();

        $alamats = AlamatRumah::all();

        return view('dashboardKaryawan.layouts.karyawan.home', [
            'dataKaryawan' => $karyawans,
            'dataBahanBakar' => $bahanbakars,
            'dataTransportasi' => $transportasis,
            'data' => $perjalanans,
            'dataType' => 'perjalanan',
            'request' => $request,
            'dataAlamat' => $alamats,
            'sudahAbsen' => $sudahAbsen
        ]);
    }

    public function delete($id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }

        // [LOG CONTEXT] Log penghapusan data karyawan
        Log::warning('Deleting Employee Record', [
            'employee_id' => $id,
            'performed_by' => session('id')
        ]);

        KaryawanPerusahaan::destroy($id);

        return redirect('dashboard/perusahaan/karyawan')->with('success', 'Data Successfully Deleted');
    }

    public function edit($id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        $oldData = KaryawanPerusahaan::find($id);

        return view('dashboardPerusahaan.layouts.karyawan.edit', ['oldData' => $oldData, 'id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        $validatedData = $request->validate([
            'employee_name' => 'required',
            'position' => 'required',
            'email' => 'required',
            'gender' => 'required',
            'birth_date' => 'required',
        ]);

        // [LOG CONTEXT] Log update data profil karyawan
        Log::info('Updating Employee Profile', [
            'employee_id' => $id,
            'updated_by' => session('id'),
            'new_position' => $request->position // Contoh log perubahan jabatan
        ]);

        KaryawanPerusahaan::where('id', $id)->update([
            'nama_karyawan' => $request->employee_name,
            'jabatan' => $request->position,
            'email' => $request->email,
            'jenis_kelamin' => $request->gender,
            'tanggal_lahir' => $request->birth_date,
        ]);

        return redirect('dashboard/perusahaan/karyawan/edit/' . $id . '')->with('success', 'Data Successfully Updated');
    }

    public function restore(string $id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }

        // [LOG CONTEXT] Log restore data karyawan
        Log::info('Restoring Employee Record', [
            'employee_id' => $id,
            'performed_by' => session('id')
        ]);

        KaryawanPerusahaan::withTrashed()->where('id', $id)->restore();

        return redirect('dashboard/perusahaan/service')->with('success', 'Data Successfully Restored');
    }
}
