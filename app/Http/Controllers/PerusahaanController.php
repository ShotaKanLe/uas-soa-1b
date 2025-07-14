<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use App\Models\Service;
use Illuminate\Http\Request;

class PerusahaanController extends Controller
{
    public function index()
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }
        $perusahaans = Perusahaan::latest()->paginate(5);
        $dataType    = 'perusahaan';
        // $perjalanans = PerjalananKaryawanPerusahaan::all();

        // return ($perjalanans);
        return view('dashboardStaff.layouts.perusahaan.view', ['data' => $perusahaans, 'dataType' => $dataType]);
    }

    public function delete($id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }
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

        // return ($oldData);

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

        Perusahaan::where('id', $id)->update([
            'nama_perusahaan' => $request->company_name,
            'id_service' => $request->service_name,
            'tanggal_aktif_service' => $request->active_date,
        ]);

        return redirect('dashboard/staff/perusahaan/edit/' . $id . '')->with('success', 'Data Successfully Updated');
    }
}
