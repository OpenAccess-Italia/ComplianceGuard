<?php

namespace App\Piracy;

use Illuminate\Database\Eloquent\Model;

class APIRefreshTokens extends Model
{
    //
    protected $table = 'ps_api_refresh_tokens';
    public $timestamps = false;
    protected $dates = ['timestamp'];
    protected $primaryKey = 'id';
}
