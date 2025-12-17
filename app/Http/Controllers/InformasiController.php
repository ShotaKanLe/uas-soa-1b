<?php

namespace App\Http\Controllers;

use App\Models\Informasi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InformasiController extends Controller
{
    public function index()
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        try {
            $correlationId = session('correlation_id');
            $authToken     = session('auth_token');

            Log::info('Fetching informasi list', [
                'correlation_id' => $correlationId,
                'auth_token' => $authToken,
                'staff_id' => session('id'),
            ]);

            $informasis = Informasi::latest()->paginate(5);
        } catch (Exception $e) {
            Log::error('Error fetching informasi list', ['error' => $e->getMessage()]);

            return redirect()->back()->with('error', 'Failed to fetch data');
        }

        return view('dashboardStaff.layouts.informasi.view', ['data' => $informasis, 'dataType' => 'informasi']);
    }

    public function add()
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        return view('dashboardStaff.layouts.informasi.add');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        Log::info('Storing informasi', [
            'staff_id' => session('id'),
            'correlation_id' => session('correlation_id'),
            'auth_token' => session('auth_token'),
        ]);

        $validatedData = $request->validate([
            'information_name' => 'required',
            'content' => 'required',
            'gambar_informasi' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $imageName = null;

            if ($request->hasFile('gambar_informasi')) {
                $image     = $request->file('gambar_informasi');
                $imageName = Str::uuid().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('informasi_images'), $imageName);
            }

            Informasi::create([
                'judul_informasi' => $request->information_name,
                'isi_informasi' => $request->content,
                'gambar_informasi' => $imageName,
                'id_staff_mitra' => session('id'),
            ]);
        } catch (Exception $e) {
            Log::error('Error storing informasi', ['error' => $e->getMessage()]);

            return redirect()->back()->with('error', 'Failed to save data');
        }

        return redirect('dashboard/staff/informasi/add')->with('success', 'Data Successfully Added');
    }

    public function delete($id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        Log::info('Deleting informasi', [
            'staff_id' => session('id'),
            'correlation_id' => session('correlation_id'),
            'auth_token' => session('auth_token'),
        ]);

        try {
            Informasi::destroy($id);
        } catch (Exception $e) {
            Log::error('Error deleting informasi', ['error' => $e->getMessage()]);

            return redirect()->back()->with('error', 'Failed to delete data');
        }

        return redirect('dashboard/staff/informasi')->with('success', 'Data Successfully Deleted');
    }

    public function edit($id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        try {
            $oldData = Informasi::find($id);
        } catch (Exception $e) {
            Log::error('Error fetching edit data', ['error' => $e->getMessage()]);

            return redirect()->back()->with('error', 'Failed to fetch edit data');
        }

        return view('dashboardStaff.layouts.informasi.edit', ['oldData' => $oldData, 'id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        Log::info('Updating informasi', [
            'staff_id' => session('id'),
            'correlation_id' => session('correlation_id'),
            'auth_token' => session('auth_token'),
        ]);

        $validatedData = $request->validate([
            'information_name' => 'required',
            'content' => 'required',
            'gambar_informasi' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $informasi = Informasi::findOrFail($id);
            $imageName = $informasi->gambar_informasi;

            if ($request->hasFile('gambar_informasi')) {
                if ($informasi->gambar_informasi && file_exists(public_path('informasi_images/'.$informasi->gambar_informasi))) {
                    unlink(public_path('informasi_images/'.$informasi->gambar_informasi));
                }

                $image     = $request->file('gambar_informasi');
                $imageName = Str::uuid().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('informasi_images'), $imageName);
            }

            $informasi->update([
                'judul_informasi' => $request->information_name,
                'isi_informasi' => $request->content,
                'gambar_informasi' => $imageName,
            ]);
        } catch (Exception $e) {
            Log::error('Error updating informasi', ['error' => $e->getMessage()]);

            return redirect()->back()->with('error', 'Failed to update data');
        }

        return redirect('dashboard/staff/informasi/edit/'.$id)->with('success', 'Data Successfully Updated');
    }

    public function restore(string $id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        try {
            Informasi::withTrashed()->where('id', $id)->restore();
        } catch (Exception $e) {
            Log::error('Error restoring informasi', ['error' => $e->getMessage()]);

            return redirect()->back()->with('error', 'Failed to restore data');
        }

        return redirect('dashboard/perusahaan/informasi')->with('success', 'Data Successfully Restored');
    }
}
