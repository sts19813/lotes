<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MigrationLog  extends Model
{

    protected $fillable = [
        'type',
        'origin_id',
        'target_id',
        'status',
        'message',
    ];
}
