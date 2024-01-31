<?php

namespace App\Manual;

use Illuminate\Database\Eloquent\Model;

class FQDNs extends Model
{
    //
    protected $table = 'manual_fqdns';
    public $timestamps = false;
    protected $dates = ['timestamp'];
    protected $primaryKey = 'fqdn';
    protected $casts = [
        'fqdn' => 'string',
    ];
}
