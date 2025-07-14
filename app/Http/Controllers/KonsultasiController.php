<?php

namespace App\Http\Controllers;

use App\Models\HasilAnalisisEmisi;
use App\Models\HasilKonsultasi;
use App\Models\Pesan;
use App\Models\StaffPerusahaan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KonsultasiController extends Controller
{
    public function index()
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        $konsultasis = HasilKonsultasi::latest()->paginate(5);
        $dataType    = 'konsultasi';

        return view('dashboardPerusahaan.layouts.konsultasi.view', ['data' => $konsultasis, 'dataType' => $dataType]);
    }

    public function indexStaff()
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        $konsultasis = HasilKonsultasi::latest()->paginate(5);
        $dataType    = 'konsultasi';

        return view('dashboardStaff.layouts.konsultasi.view', ['data' => $konsultasis, 'dataType' => $dataType]);
    }

    public function add()
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        $analisis = HasilAnalisisEmisi::latest()->paginate(5);
        $dataType = 'analisis';

        return view('dashboardPerusahaan.layouts.konsultasi.add', ['data' => $analisis, 'dataType' => $dataType]);
    }

    public function upload(Request $request)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }

        // dd($request->all());

        $companyId = StaffPerusahaan::where('id', session()->get('id'))->first()->id_perusahaan;

        // $validatedData = $request->validate([
        //     'selected_id' => 'required',
        //     'disccussion_name' => 'required',
        //     'discussion_message' => 'required',
        // ]);

        HasilKonsultasi::create([
            'id_perusahaan' => $companyId,
            'nama_konsultasi' => $request->discussion_name,
            'tanggal_konsultasi' => Carbon::now(),
            'isi_konsultasi' => $request->discussion_message,
            'id_hasil_analisis' => $request->selected_id,
        ]);

        return redirect('dashboard/perusahaan/konsultasi/add')->with('success', 'Consultation Successfully Added');
    }

    public function delete($id)
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }
        HasilKonsultasi::destroy($id);
        return redirect('dashboard/perusahaan/konsultasi')->with('success', 'Data Successfully Deleted');
    }

    public function replyStaff(Request $request)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        $id = session('id');

        $fileName = null;

        // Cek apakah ada file yang dikirim
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Validasi opsional (bisa kamu sesuaikan)
            $request->validate([
                'file' => 'mimes:pdf,docx,jpg,jpeg,png|max:5120', // max 5MB
            ]);

            // Simpan file dengan nama acak
            $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('messages'), $fileName);
        }

        // Simpan data ke database
        Pesan::create([
            'id_staff' => $id,
            'id_konsultasi' => $request->consultation_id,
            'judul_pesan' => $request->title,
            'isi_pesan' => $request->message,
            'file_pdf' => $fileName,
        ]);

        HasilKonsultasi::where('id', $request->consultation_id)->update(['status_konsultasi' => 'CLOSED']);

        return redirect('dashboard/staff/konsultasi/')->with('success', 'Consultation Reply Successfully Added');
    }
}
