<?php

namespace App\Models\Piracy;

use Illuminate\Database\Eloquent\Model;

class TicketItemsLog extends Model
{
    //
    protected $table = 'ps_ticket_items_log';

    public $timestamps = false;

    protected $casts = [
        'timestamp' => 'datetime',
    ];
}
