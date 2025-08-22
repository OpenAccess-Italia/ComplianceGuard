<?php

namespace App\Models\Manual;

use Illuminate\Database\Eloquent\Model;

class FQDNs extends Model
{
    //
    protected $table = 'manual_fqdns';

    public $timestamps = false;

    protected $casts = [
        'timestamp' => 'datetime',
        'fqdn' => 'string',
    ];

    protected $primaryKey = 'fqdn';
}
