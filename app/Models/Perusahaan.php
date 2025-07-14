<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perusahaan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function service()
    {
        return $this->belongsTo(Service::class, 'id_service');
    }

    public function code()
    {
        return $this->belongsTo(Code::class, 'id_code');
    }
}
