<?php

namespace App\Http\Controllers;

use App\Models\Informasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // <--- WAJIB IMPORT LOG
use Illuminate\Support\Str;

class InformasiController extends Controller
{
    public function index()
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }
        $informasis = Informasi::latest()->paginate(5);
        $dataType   = 'informasi';

        return view('dashboardStaff.layouts.informasi.view', ['data' => $informasis, 'dataType' => $dataType]);
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
        $validatedData = $request->validate([
            'information_name' => 'required',
            'content' => 'required',
            'gambar_informasi' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // [LOG CONTEXT] Mencatat upaya pembuatan informasi baru
        Log::info('Creating New Information Entry', [
            'title' => $request->information_name,
            'user_id' => session('id')
        ]);

        $imageName = null;

        if ($request->hasFile('gambar_informasi')) {
            $image     = $request->file('gambar_informasi');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();

            // Simpan langsung ke public/informasi_images
            $image->move(public_path('informasi_images'), $imageName);

            // [LOG CONTEXT] Log upload gambar sukses
            Log::info('Information Image Uploaded', ['image_name' => $imageName]);
        }

        // Simpan data ke database
        $informasi = Informasi::create([
            'judul_informasi' => $request->information_name,
            'isi_informasi' => $request->content,
            'gambar_informasi' => $imageName,
            'id_staff_mitra' => session('id'),
        ]);

        // [LOG CONTEXT] Log sukses
        Log::info('Information Created Successfully', ['info_id' => $informasi->id]);

        return redirect('dashboard/staff/informasi/add')->with('success', 'Data Successfully Added');
    }

    public function delete($id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        // [LOG CONTEXT] Log penghapusan informasi
        Log::warning('Deleting Information', ['info_id' => $id, 'user_id' => session('id')]);

        Informasi::destroy($id);

        return redirect('dashboard/staff/informasi')->with('success', 'Data Successfully Deleted');
    }

    public function edit($id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }
        $oldData = Informasi::find($id);

        return view('dashboardStaff.layouts.informasi.edit', ['oldData' => $oldData, 'id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        $validatedData = $request->validate([
            'information_name' => 'required',
            'content' => 'required',
            'gambar_informasi' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // [LOG CONTEXT] Log awal update
        Log::info('Updating Information Entry', ['info_id' => $id, 'user_id' => session('id')]);

        $informasi = Informasi::findOrFail($id);
        $imageName = $informasi->gambar_informasi; // Gunakan gambar lama sebagai default

        if ($request->hasFile('gambar_informasi')) {
            // Hapus gambar lama jika ada
            if ($informasi->gambar_informasi && file_exists(public_path('informasi_images/' . $informasi->gambar_informasi))) {
                unlink(public_path('informasi_images/' . $informasi->gambar_informasi));

                // [LOG CONTEXT] Log penggantian gambar (hapus lama)
                Log::info('Old Information Image Deleted', ['old_image' => $informasi->gambar_informasi]);
            }

            $image     = $request->file('gambar_informasi');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();

            // Simpan gambar baru ke public/informasi_images
            $image->move(public_path('informasi_images'), $imageName);

            // [LOG CONTEXT] Log gambar baru
            Log::info('New Information Image Uploaded', ['new_image' => $imageName]);
        }

        // Update data ke database
        $informasi->update([
            'judul_informasi' => $request->information_name,
            'isi_informasi' => $request->content,
            'gambar_informasi' => $imageName,
        ]);

        // [LOG CONTEXT] Update sukses
        Log::info('Information Updated Successfully', ['info_id' => $id]);

        return redirect('dashboard/staff/informasi/edit/' . $id)->with('success', 'Data Successfully Updated');
    }

    public function restore(string $id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        // [LOG CONTEXT] Restore data
        Log::info('Restoring Information', ['info_id' => $id, 'user_id' => session('id')]);

        Informasi::withTrashed()->where('id', $id)->restore();

        // Note: Saya sesuaikan redirectnya ke dashboard staff agar konsisten,
        // tapi jika memang harus ke perusahaan, silakan ubah kembali.
        return redirect('dashboard/staff/informasi')->with('success', 'Data Successfully Restored');
    }
}
