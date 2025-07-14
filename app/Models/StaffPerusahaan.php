<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class StaffPerusahaan extends Authenticatable
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function code()
    {
        return $this->belongsTo(Code::class, 'id_code');
    }
}
