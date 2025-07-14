<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pesan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function staff()
    {
        return $this->belongsTo(Service::class, 'id_staff');
    }

    public function konsultasi()
    {
        return $this->belongsTo(Code::class, 'id_konsultasi');
    }
}
