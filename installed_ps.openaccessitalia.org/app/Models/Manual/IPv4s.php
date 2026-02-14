<?php

namespace App\Models\Manual;

use Illuminate\Database\Eloquent\Model;

class IPv4s extends Model
{
    //
    protected $table = 'manual_ipv4s';

    public $timestamps = false;

    protected $primaryKey = 'ipv4';

    protected $casts = [
        'ipv4' => 'string',
        'timestamp' => 'datetime',
    ];
}
