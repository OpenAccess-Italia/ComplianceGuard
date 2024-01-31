<?php

namespace App\ADM;

use Illuminate\Database\Eloquent\Model;

class BettingFiles extends Model
{
    //
    protected $table = 'adm_betting_files';
    public $timestamps = false;
    protected $dates = ['timestamp'];
    protected $primaryKey = 'id';
}
