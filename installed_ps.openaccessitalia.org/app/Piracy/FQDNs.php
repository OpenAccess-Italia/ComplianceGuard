<?php

namespace App\Piracy;

use Illuminate\Database\Eloquent\Model;

class FQDNs extends Model
{
    //
    protected $table = 'ps_fqdns';
    public $timestamps = false;
    protected $dates = ['timestamp'];
    protected $primaryKey = 'fqdn';
    protected $casts = [
        'fqdn' => 'string',
    ];
}
