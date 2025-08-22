<?php

namespace App\Models\Piracy;

use Illuminate\Database\Eloquent\Model;

class APILog extends Model
{
    //
    protected $table = 'ps_api_log';

    public $timestamps = false;

    protected $casts = [
        'timestamp' => 'datetime',
    ];
}
