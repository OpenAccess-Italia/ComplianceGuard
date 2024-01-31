<?php

namespace App\Piracy;

use Illuminate\Database\Eloquent\Model;

class IPv4s extends Model
{
    //
    protected $table = 'ps_ipv4s';
    public $timestamps = false;
    protected $dates = ['timestamp'];
    protected $primaryKey = 'ipv4';
    protected $casts = [
        'ipv4' => 'string',
    ];
}
