<?php

namespace App\Http\Controllers;

use App\Models\AlamatRumah;
use App\Models\BahanBakar;
use App\Models\KaryawanPerusahaan;
use App\Models\PerjalananKaryawanPerusahaan;
use App\Models\Transportasi;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KaryawanPerusahaanController extends Controller
{
    public function index()
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }

        Log::info('Fetching karyawan list', [
            'company_id'     => session('id'),
            'correlation_id' => session('correlation_id'),
            'auth_token'     => session('auth_token'),
        ]);

        try {
            $karyawans = KaryawanPerusahaan::latest()->paginate(5);
        } catch (Exception $e) {
            Log::error('Error fetching karyawan list', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to fetch data');
        }

        return view('dashboardPerusahaan.layouts.karyawan.view', [
            'data' => $karyawans,
            'dataType' => 'karyawan',
        ]);
    }

    public function homeKaryawan(Request $request)
    {
        if ($redirect = $this->checkifLoginForEmployee()) {
            return $redirect;
        }

        Log::info('Fetching perjalanan karyawan', [
            'employee_id'    => session('id'),
            'correlation_id' => session('correlation_id'),
            'auth_token'     => session('auth_token'),
            'filters'        => $request->all(),
        ]);

        try {
            $query = PerjalananKaryawanPerusahaan::query();

            if ($request->filled('nama_karyawan')) {
                $query->whereHas('karyawanPerusahaan', function ($q) use ($request) {
                    $q->where('nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
                });
            }

            if ($request->filled('nama_bahan_bakar')) {
                $query->whereHas('bahanBakar', function ($q) use ($request) {
                    $q->where('nama_bahan_bakar', 'like', '%' . $request->nama_bahan_bakar . '%');
                });
            }

            if ($request->filled('nama_transportasi')) {
                $query->whereHas('transportasi', function ($q) use ($request) {
                    $q->where('nama_transportasi', 'like', '%' . $request->nama_transportasi . '%');
                });
            }

            if ($request->filled('tanggal_perjalanan')) {
                $query->whereDate('tanggal_perjalanan', $request->tanggal_perjalanan);
            }

            $query->orderBy('tanggal_perjalanan', 'desc');

            $perjalanans = $query->paginate(5);

            $karyawans     = KaryawanPerusahaan::all();
            $bahanbakars   = BahanBakar::all();
            $transportasis = Transportasi::all();
            $alamats       = AlamatRumah::all();

            $sudahAbsen = PerjalananKaryawanPerusahaan::where('id_karyawan', session('id'))
                ->where('tanggal_perjalanan', Carbon::now()->format('Y-m-d'))
                ->first();
        } catch (Exception $e) {
            Log::error('Error fetching perjalanan karyawan', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to fetch data');
        }

        return view('dashboardKaryawan.layouts.karyawan.home', [
            'dataKaryawan'     => $karyawans,
            'dataBahanBakar'   => $bahanbakars,
            'dataTransportasi' => $transportasis,
            'data'             => $perjalanans,
            'dataType'         => 'perjalanan',
            'request'          => $request,
            'dataAlamat'       => $alamats,
            'sudahAbsen'       => $sudahAbsen,
        ]);
    }

    public function delete($id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }

        Log::info('Deleting karyawan', [
            'company_id'     => session('id'),
            'karyawan_id'    => $id,
            'correlation_id' => session('correlation_id'),
            'auth_token'     => session('auth_token'),
        ]);

        try {
            KaryawanPerusahaan::destroy($id);
        } catch (Exception $e) {
            Log::error('Error deleting karyawan', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to delete data');
        }

        return redirect('dashboard/perusahaan/karyawan')
            ->with('success', 'Data Successfully Deleted');
    }

    public function edit($id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }

        Log::info('Fetching edit karyawan data', [
            'company_id'     => session('id'),
            'karyawan_id'    => $id,
            'correlation_id' => session('correlation_id'),
        ]);

        try {
            $oldData = KaryawanPerusahaan::findOrFail($id);
        } catch (Exception $e) {
            Log::error('Error fetching karyawan edit data', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to fetch edit data');
        }

        return view('dashboardPerusahaan.layouts.karyawan.edit', [
            'oldData' => $oldData,
            'id'      => $id,
        ]);
    }

    public function update(Request $request, string $id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }

        Log::info('Updating karyawan', [
            'company_id'     => session('id'),
            'karyawan_id'    => $id,
            'correlation_id' => session('correlation_id'),
        ]);

        $validatedData = $request->validate([
            'employee_name' => 'required',
            'position'      => 'required',
            'email'         => 'required',
            'gender'        => 'required',
            'birth_date'    => 'required',
        ]);

        try {
            KaryawanPerusahaan::where('id', $id)->update([
                'nama_karyawan'  => $request->employee_name,
                'jabatan'        => $request->position,
                'email'          => $request->email,
                'jenis_kelamin'  => $request->gender,
                'tanggal_lahir'  => $request->birth_date,
            ]);
        } catch (Exception $e) {
            Log::error('Error updating karyawan', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to update data');
        }

        return redirect('dashboard/perusahaan/karyawan/edit/' . $id)
            ->with('success', 'Data Successfully Updated');
    }

    public function restore(string $id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }

        Log::info('Restoring karyawan', [
            'company_id'     => session('id'),
            'karyawan_id'    => $id,
            'correlation_id' => session('correlation_id'),
        ]);

        try {
            KaryawanPerusahaan::withTrashed()->where('id', $id)->restore();
        } catch (Exception $e) {
            Log::error('Error restoring karyawan', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to restore data');
        }

        return redirect('dashboard/perusahaan/service')
            ->with('success', 'Data Successfully Restored');
    }
}
