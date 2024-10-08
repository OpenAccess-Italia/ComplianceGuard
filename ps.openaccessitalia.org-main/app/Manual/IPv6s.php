<?php

namespace App\Manual;

use Illuminate\Database\Eloquent\Model;

class IPv6s extends Model
{
    //
    protected $table = 'manual_ipv6s';
    public $timestamps = false;
    protected $dates = ['timestamp'];
    protected $primaryKey = 'ipv6';
    protected $casts = [
        'ipv6' => 'string',
    ];
}
