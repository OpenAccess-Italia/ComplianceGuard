<?php

namespace App\Piracy;

use Illuminate\Database\Eloquent\Model;

class TicketItemsLog extends Model
{
    //
    protected $table = 'ps_ticket_items_log';
    public $timestamps = false;
    protected $dates = ['timestamp'];
    protected $primaryKey = 'id';
}
