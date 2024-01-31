<?php

namespace App\ADM;

use Illuminate\Database\Eloquent\Model;

class BettingBlacklist extends Model
{
    //
    protected $table = 'adm_betting_blacklist';
    public $timestamps = false;
    protected $primaryKey = 'id';
}
