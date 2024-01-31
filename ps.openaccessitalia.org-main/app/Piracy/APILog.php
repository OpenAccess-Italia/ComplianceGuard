<?php

namespace App\Piracy;

use Illuminate\Database\Eloquent\Model;

class APILog extends Model
{
    //
    protected $table = 'ps_api_log';
    public $timestamps = false;
    protected $dates = ['timestamp'];
    protected $primaryKey = 'id';
}
