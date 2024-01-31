<?php

namespace App\ADM;

use Illuminate\Database\Eloquent\Model;

class SmokingBlacklist extends Model
{
    //
    protected $table = 'adm_smoking_blacklist';
    public $timestamps = false;
    protected $primaryKey = 'id';
}
