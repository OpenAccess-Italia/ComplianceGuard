<?php

namespace App\Models\ADM;

use Illuminate\Database\Eloquent\Model;

class SmokingFiles extends Model
{
    //
    protected $table = 'adm_smoking_files';

    public $timestamps = false;

    protected $casts = [
        'timestamp' => 'datetime',
    ];
}
