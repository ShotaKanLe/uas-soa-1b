<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HasilAnalisisEmisi extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function perusahaan()
    {
        return $this->BelongsTo(Perusahaan::class, 'id_perusahaan');
    }
}
