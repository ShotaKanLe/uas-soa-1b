<?php

namespace App\Http\Controllers;

use App\Models\KaryawanPerusahaan;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // <--- WAJIB IMPORT LOG
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        // [LOG CONTEXT] Mencatat akses daftar layanan oleh Staff
        Log::info('Viewing Service Catalog (Staff)', [
            'user_id' => session('id'),
            'role' => 'staff_mitra'
        ]);

        $services = Service::latest()->paginate(5);
        $dataType = 'service';

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

        // [LOG CONTEXT] Log pembuatan layanan baru
        Log::info('Creating New Service Package', [
            'service_name' => $request->service_name,
            'price' => $request->service_price,
            'duration' => $request->service_duration,
            'created_by' => session('id')
        ]);

        // Simpan data ke database
        $service = Service::create([
            'nama_service' => $request->service_name,
            'durasi_service' => $request->service_duration,
            'harga_service' => $request->service_price,
            'deskripsi_service' => $request->service_description,
            'id_staff_mitra' => session('id'),
        ]);

        // [LOG CONTEXT] Log sukses
        Log::info('Service Created Successfully', ['service_id' => $service->id]);

        return redirect('dashboard/staff/service/add')->with('success', 'Data Successfully Added');
    }

    public function delete($id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        // [LOG CONTEXT] Log penghapusan layanan
        Log::warning('Deleting Service Package', [
            'service_id' => $id,
            'performed_by' => session('id')
        ]);

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

        // [LOG CONTEXT] Log update layanan
        Log::info('Updating Service Package', [
            'service_id' => $id,
            'updated_by' => session('id'),
            'new_price' => $request->service_price
        ]);

        // Update data ke database
        Service::where('id', $id)->update([
            'nama_service' => $request->service_name,
            'durasi_service' => $request->service_duration,
            'harga_service' => $request->service_price,
            'deskripsi_service' => $request->service_description,
        ]);

        return redirect('dashboard/staff/service/edit/' . $id)->with('success', 'Data Successfully Updated');
    }

    public function restore(string $id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        // [LOG CONTEXT] Log restore layanan
        Log::info('Restoring Service Package', [
            'service_id' => $id,
            'performed_by' => session('id')
        ]);

        Service::withTrashed()->where('id', $id)->restore();

        // Note: Saya arahkan kembali ke dashboard staff agar konsisten dengan method index/delete
        return redirect('dashboard/staff/service')->with('success', 'Data Successfully Restored');
    }
}
