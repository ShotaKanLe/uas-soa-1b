<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Informasi extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function staffMitra()
    {
        return $this->belongsTo(StaffMitra::class, 'id_staff_mitra');
    }
}
