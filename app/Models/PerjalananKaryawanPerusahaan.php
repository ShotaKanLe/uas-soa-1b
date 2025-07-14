<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerjalananKaryawanPerusahaan extends Model
{
    // protected $table = 'perjalananKaryawans';
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function karyawanPerusahaan()
    {
        return $this->belongsTo(KaryawanPerusahaan::class, 'id_karyawan');
    }

    public function transportasi()
    {
        return $this->belongsTo(Transportasi::class, 'id_transportasi');
    }

    public function bahanBakar()
    {
        return $this->belongsTo(BahanBakar::class, 'id_bahan_bakar');
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }

    public function alamat()
    {
        return $this->belongsTo(AlamatRumah::class, 'id_alamat');
    }
}
