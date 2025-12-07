<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // <--- WAJIB IMPORT LOG

class PerusahaanController extends Controller
{
    public function index()
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        // [LOG CONTEXT] Mencatat Staff melihat daftar perusahaan (Monitoring)
        Log::info('Viewing Registered Companies List', [
            'user_id' => session('id'),
            'role' => 'staff_mitra'
        ]);

        $perusahaans = Perusahaan::latest()->paginate(5);
        $dataType    = 'perusahaan';

        return view('dashboardStaff.layouts.perusahaan.view', ['data' => $perusahaans, 'dataType' => $dataType]);
    }

    public function delete($id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        // [LOG CONTEXT] Log penghapusan data perusahaan (Action Berisiko)
        Log::warning('Deleting Company Data', [
            'company_id' => $id,
            'performed_by_staff_id' => session('id')
        ]);

        Perusahaan::destroy($id);

        return redirect('dashboard/staff/perusahaan')->with('success', 'Data Successfully Deleted');
    }

    public function edit($id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }
        $services = Service::all();

        $oldData = Perusahaan::find($id);

        return view('dashboardStaff.layouts.perusahaan.edit', ['dataService' => $services, 'oldData' => $oldData, 'id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }
        $validatedData = $request->validate([
            'company_name' => 'required',
            'service_name' => 'required',
            'active_date' => 'required',
        ]);

        // [LOG CONTEXT] Log update data perusahaan (Misal: Perpanjang layanan atau ganti nama)
        Log::info('Updating Company Subscription/Profile', [
            'company_id' => $id,
            'staff_id' => session('id'),
            'updated_fields' => $request->only(['company_name', 'service_name', 'active_date'])
        ]);

        Perusahaan::where('id', $id)->update([
            'nama_perusahaan' => $request->company_name,
            'id_service' => $request->service_name,
            'tanggal_aktif_service' => $request->active_date,
        ]);

        return redirect('dashboard/staff/perusahaan/edit/' . $id . '')->with('success', 'Data Successfully Updated');
    }
}
