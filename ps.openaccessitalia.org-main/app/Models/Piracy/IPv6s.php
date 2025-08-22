<?php

namespace App\Models\Piracy;

use Illuminate\Database\Eloquent\Model;

class IPv6s extends Model
{
    //
    protected $table = 'ps_ipv6s';

    public $timestamps = false;

    protected $primaryKey = 'ipv6';

    protected $casts = [
        'ipv6' => 'string',
        'timestamp' => 'datetime',
    ];
}
