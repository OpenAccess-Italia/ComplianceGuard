<?php

namespace App\Piracy;

use Illuminate\Database\Eloquent\Model;

class Tickets extends Model
{
    //
    protected $table = 'ps_tickets';
    public $timestamps = false;
    protected $dates = ['timestamp'];
    protected $primaryKey = 'ticket_id';
    protected $casts = [
        'ticket_id' => 'string',
    ];
}
