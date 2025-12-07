<?php

namespace App\Http\Controllers;

use App\Models\HasilAnalisisEmisi;
use App\Models\HasilKonsultasi;
use App\Models\Pesan;
use App\Models\StaffPerusahaan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // <--- WAJIB IMPORT LOG

class KonsultasiController extends Controller
{
    public function index()
    {
        if ($redirect = $this->checkifLoginForCompany()) {
            return $redirect;
        }

        // [LOG CONTEXT] Log akses daftar konsultasi oleh perusahaan
        Log::info('Viewing Consultation List (Company)', [
            'user_id' => session('id'),
            'role' => 'perusahaan'
        ]);

        $konsultasis = HasilKonsultasi::latest()->paginate(5);
        $dataType    = 'konsultasi';

        return view('dashboardPerusahaan.layouts.konsultasi.view', ['data' => $konsultasis, 'dataType' => $dataType]);
    }

    public function indexStaff()
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        // [LOG CONTEXT] Log akses daftar konsultasi oleh staff
        Log::info('Viewing Consultation Queue (Staff)', [
            'user_id' => session('id'),
            'role' => 'staff_mitra'
        ]);

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

        $companyId = StaffPerusahaan::where('id', session()->get('id'))->first()->id_perusahaan;

        // [LOG CONTEXT] Mencatat permintaan konsultasi baru
        Log::info('New Consultation Request Submitted', [
            'company_id' => $companyId,
            'topic' => $request->discussion_name,
            'related_analysis_id' => $request->selected_id
        ]);

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

        // [LOG CONTEXT] Log penghapusan tiket konsultasi
        Log::warning('Deleting Consultation Ticket', [
            'consultation_id' => $id,
            'user_id' => session('id')
        ]);

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

        // [LOG CONTEXT] Log staff mulai membalas
        Log::info('Staff Replying to Consultation', [
            'staff_id' => $id,
            'consultation_id' => $request->consultation_id
        ]);

        // Cek apakah ada file yang dikirim
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $request->validate([
                'file' => 'mimes:pdf,docx,jpg,jpeg,png|max:5120', // max 5MB
            ]);

            $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('messages'), $fileName);

            // [LOG CONTEXT] Log attachment file
            Log::info('Consultation Reply Attachment Uploaded', ['file_name' => $fileName]);
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

        // [LOG CONTEXT] Log penutupan tiket konsultasi
        Log::info('Consultation Ticket Closed', [
            'consultation_id' => $request->consultation_id,
            'status' => 'CLOSED'
        ]);

        return redirect('dashboard/staff/konsultasi/')->with('success', 'Consultation Reply Successfully Added');
    }
}
