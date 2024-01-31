<?php

namespace App\Piracy;

use Illuminate\Database\Eloquent\Model;

class APIAccessTokens extends Model
{
    //
    protected $table = 'ps_api_access_tokens';
    public $timestamps = false;
    protected $dates = ['timestamp'];
    protected $primaryKey = 'id';
}
