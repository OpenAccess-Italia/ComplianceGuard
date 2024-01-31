<?php

namespace App\ADM;

use Illuminate\Database\Eloquent\Model;

class SmokingFiles extends Model
{
    //
    protected $table = 'adm_smoking_files';
    public $timestamps = false;
    protected $dates = ['timestamp'];
    protected $primaryKey = 'id';
}
