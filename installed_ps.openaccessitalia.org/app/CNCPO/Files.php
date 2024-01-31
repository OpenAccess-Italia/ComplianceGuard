<?php

namespace App\CNCPO;

use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    //
    protected $table = 'cncpo_files';
    public $timestamps = false;
    protected $dates = ['timestamp','blacklist_timestamp'];
    protected $primaryKey = 'id';
}
