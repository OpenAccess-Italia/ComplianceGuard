<?php

namespace App\Models\Piracy;

use Illuminate\Database\Eloquent\Model;

class FQDNs extends Model
{
    //
    protected $table = 'ps_fqdns';

    public $timestamps = false;

    protected $primaryKey = 'fqdn';

    protected $casts = [
        'fqdn' => 'string',
        'timestamp' => 'datetime',
    ];
}
