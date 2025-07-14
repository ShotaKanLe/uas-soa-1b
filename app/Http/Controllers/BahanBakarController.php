<?php

namespace App\Http\Controllers;

use App\Models\BahanBakar;
use Illuminate\Http\Request;

class BahanBakarController extends Controller
{
    public function index()
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }
        $bahanBakars = BahanBakar::latest()->paginate(5);
        $dataType    = 'bahanBakar';
        // $bahanBakars = PerjalananKaryawanPerusahaan::all();

        // return ($alamats);
        return view('dashboardStaff.layouts.bahanBakar.view', ['data' => $bahanBakars, 'dataType' => $dataType]);
    }

    public function add()
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        return view('dashboardStaff.layouts.bahanBakar.add');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }
        $validatedData = $request->validate([
            'fuel_name' => 'required',
            'fuel_type' => 'required',
            'cost' => 'required',
            'co2perliter' => 'required',
            'ch4perliter' => 'required',
            'n2Operliter' => 'required',
            'WTTperliter' => 'required',
            'rerata_konsumsi_literperkm' => 'required',
        ]);

        $GWP_CH4 = 25;
        $GWP_N2O = 298;

        $CO2eperliter = $request->co2perliter
            + ($request->ch4perliter * $GWP_CH4)
            + ($request->n2Operliter * $GWP_N2O);

        bahanBakar::create([
            'nama_bahan_bakar' => $request->fuel_name,
            'jenis_bahan_bakar' => $request->fuel_type,
            'harga_bahan_bakar_per_liter' => $request->cost,
            'co2perliter' => $request->co2perliter,
            'ch4perliter' => $request->ch4perliter,
            'n2Operliter' => $request->n2Operliter,
            'Co2eperliter' => $CO2eperliter,
            'WTTperliter' => $request->WTTperliter,
            'rerata_konsumsi_literperkm' => $request->rerata_konsumsi_literperkm,
        ]);

        return redirect('dashboard/staff/bahanBakar/add')->with('success', 'Data Successfully Added');
    }

    public function delete($id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }
        BahanBakar::destroy($id);

        return redirect('dashboard/staff/bahanBakar')->with('success', 'Data Successfully Deleted');
    }

    public function edit($id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }
        $oldData = BahanBakar::find($id);

        // return ($oldData);

        return view('dashboardStaff.layouts.bahanBakar.edit', ['oldData' => $oldData, 'id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }

        $validatedData = $request->validate([
            'fuel_name' => 'required',
            'fuel_type' => 'required',
            'cost' => 'required',
            'co2perliter' => 'required',
            'ch4perliter' => 'required',
            'n2Operliter' => 'required',
            'WTTperliter' => 'required',
            'rerata_konsumsi_literperkm' => 'required',
        ]);

        $GWP_CH4 = 25;
        $GWP_N2O = 298;

        $CO2eperliter = $request->co2perliter
            + ($request->ch4perliter * $GWP_CH4)
            + ($request->n2Operliter * $GWP_N2O);

        BahanBakar::where('id', $id)->update([
            'nama_bahan_bakar' => $request->fuel_name,
            'jenis_bahan_bakar' => $request->fuel_type,
            'harga_bahan_bakar_per_liter' => $request->cost,
            'co2perliter' => $request->co2perliter,
            'ch4perliter' => $request->ch4perliter,
            'n2Operliter' => $request->n2Operliter,
            'Co2eperliter' => $CO2eperliter,
            'WTTperliter' => $request->WTTperliter,
            'rerata_konsumsi_literperkm' => $request->rerata_konsumsi_literperkm,
        ]);

        return redirect('dashboard/staff/bahanBakar/edit/' . $id . '')->with('success', 'Data Successfully Updated');
    }

    public function restore(string $id)
    {
        if ($redirect = $this->checkifLoginForStaff()) {
            return $redirect;
        }
        BahanBakar::withTrashed()->where('id', $id)->restore();

        return redirect('dashboard/staff/bahanBakar')->with('success', 'Data Successfully Restored');
    }
}
