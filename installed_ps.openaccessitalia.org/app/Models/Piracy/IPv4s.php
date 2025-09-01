<?php

namespace App\Models\Piracy;

use Illuminate\Database\Eloquent\Model;

class IPv4s extends Model
{
    //
    protected $table = 'ps_ipv4s';

    public $timestamps = false;

    protected $primaryKey = 'ipv4';

    protected $casts = [
        'ipv4' => 'string',
        'timestamp' => 'datetime',
    ];
}
