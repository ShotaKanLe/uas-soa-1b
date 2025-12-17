<?php

namespace App\Http\Controllers;

use App\Models\KaryawanPerusahaan;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }
        $services = Service::latest()->paginate(5);
        $dataType = 'service';
        // $services = PerjalananKaryawanPerusahaan::all();

        // return ($services);
        return view('dashboardStaff.layouts.service.view', ['data' => $services, 'dataType' => $dataType]);
    }

    public function add()
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        return view('dashboardStaff.layouts.service.add');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }
        $validatedData = $request->validate([
            'service_name' => 'required',
            'service_duration' => 'required',
            'service_price' => 'required',
            'service_description' => 'required',
        ]);

        // Simpan data ke database
        Service::create([
            'nama_service' => $request->service_name,
            'durasi_service' => $request->service_duration,
            'harga_service' => $request->service_price,
            'deskripsi_service' => $request->service_description,
            'id_staff_mitra' => session('id'),
        ]);

        return redirect('dashboard/staff/service/add')->with('success', 'Data Successfully Added');
    }

    public function delete($id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }
        Service::destroy($id);

        return redirect('dashboard/staff/service')->with('success', 'Data Successfully Deleted');
    }

    public function edit($id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }
        $karyawans = KaryawanPerusahaan::all();

        $oldData = Service::find($id);

        // return ($oldData);

        return view('dashboardStaff.layouts.service.edit', ['dataKaryawan' => $karyawans, 'oldData' => $oldData, 'id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        $validatedData = $request->validate([
            'service_name' => 'required',
            'service_duration' => 'required',
            'service_price' => 'required',
            'service_description' => 'required',
        ]);

        // Update data ke database
        Service::where('id', $id)->update([
            'nama_service' => $request->service_name,
            'durasi_service' => $request->service_duration,
            'harga_service' => $request->service_price,
            'deskripsi_service' => $request->service_description,
        ]);

        return redirect('dashboard/staff/service/edit/'.$id)->with('success', 'Data Successfully Updated');
    }

    public function restore(string $id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }
        Service::withTrashed()->where('id', $id)->restore();

        return redirect('dashboard/perusahaan/service')->with('success', 'Data Successfully Restored');
    }
}
