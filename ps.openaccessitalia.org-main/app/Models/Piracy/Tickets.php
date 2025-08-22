<?php

namespace App\Models\Piracy;

use Illuminate\Database\Eloquent\Model;

class Tickets extends Model
{
    //
    protected $table = 'ps_tickets';

    public $timestamps = false;

    protected $primaryKey = 'ticket_id';

    protected $casts = [
        'ticket_id' => 'string',
        'timestamp' => 'datetime',
    ];
}
