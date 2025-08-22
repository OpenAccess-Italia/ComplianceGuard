<?php

namespace App\Models\CNCPO;

use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    //
    protected $table = 'cncpo_files';

    public $timestamps = false;

    protected $casts = [
        'timestamp' => 'datetime',
        'blacklist_timestamp' => 'datetime',
    ];

    protected $primaryKey = 'id';
}
