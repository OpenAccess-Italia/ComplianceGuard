<?php

namespace App\CNCPO;

use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
    //
    protected $table = 'cncpo_blacklist';
    public $timestamps = false;
    protected $primaryKey = 'id';
}
