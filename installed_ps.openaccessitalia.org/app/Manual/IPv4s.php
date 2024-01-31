<?php

namespace App\Manual;

use Illuminate\Database\Eloquent\Model;

class IPv4s extends Model
{
    //
    protected $table = 'manual_ipv4s';
    public $timestamps = false;
    protected $dates = ['timestamp'];
    protected $primaryKey = 'ipv4';
    protected $casts = [
        'ipv4' => 'string',
    ];
}
