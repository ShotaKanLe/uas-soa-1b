<?php

namespace App\Http\Controllers;

use App\Models\Informasi;
use Illuminate\Http\Request;
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
        // $informasis = Informasi::all();

        // return ($informasis);
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

        $imageName = null;

        if ($request->hasFile('gambar_informasi')) {
            $image     = $request->file('gambar_informasi');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();

            // Simpan langsung ke public/informasi_images
            $image->move(public_path('informasi_images'), $imageName);
        }

        // Simpan data ke database
        Informasi::create([
            'judul_informasi' => $request->information_name,
            'isi_informasi' => $request->content,
            'gambar_informasi' => $imageName,
            'id_staff_mitra' => session('id'),
        ]);

        return redirect('dashboard/staff/informasi/add')->with('success', 'Data Successfully Added');
    }

    public function delete($id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }
        Informasi::destroy($id);

        return redirect('dashboard/staff/informasi')->with('success', 'Data Successfully Deleted');
    }

    public function edit($id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }
        $oldData = Informasi::find($id);

        // return ($oldData);

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

        $informasi = Informasi::findOrFail($id);
        $imageName = $informasi->gambar_informasi; // Gunakan gambar lama sebagai default

        if ($request->hasFile('gambar_informasi')) {
            // Hapus gambar lama jika ada
            if ($informasi->gambar_informasi && file_exists(public_path('informasi_images/' . $informasi->gambar_informasi))) {
                unlink(public_path('informasi_images/' . $informasi->gambar_informasi));
            }

            $image     = $request->file('gambar_informasi');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();

            // Simpan gambar baru ke public/informasi_images
            $image->move(public_path('informasi_images'), $imageName);
        }

        // Update data ke database
        $informasi->update([
            'judul_informasi' => $request->information_name,
            'isi_informasi' => $request->content,
            'gambar_informasi' => $imageName,
        ]);

        return redirect('dashboard/staff/informasi/edit/' . $id)->with('success', 'Data Successfully Updated');
    }

    public function restore(string $id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }
        Informasi::withTrashed()->where('id', $id)->restore();

        return redirect('dashboard/perusahaan/informasi')->with('success', 'Data Successfully Restored');
    }
}
