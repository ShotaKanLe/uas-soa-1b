<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HasilKonsultasi extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }

    public function hasilAnalisisEmisi()
    {
        return $this->belongsTo(HasilAnalisisEmisi::class, 'id_hasil_analisis');
    }

    public function pesan()
    {
        return $this->hasOne(Pesan::class, 'id_konsultasi');
    }
}
