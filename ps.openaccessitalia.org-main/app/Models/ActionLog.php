<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionLog extends Model
{
    //
    protected $table = 'action_log';

    public $timestamps = false;

    protected $casts = [
        'timestamp' => 'datetime:d/m/Y H:i:s',
    ];
}
