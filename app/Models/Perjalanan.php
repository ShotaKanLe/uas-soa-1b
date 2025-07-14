<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perjalanan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function hasilAnalisisEmisi()
    {
        return $this->belongsTo(HasilAnalisisEmisi::class, 'id_hasil_analisis');
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'idPerusahaan');
    }
}
