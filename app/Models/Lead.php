<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company',
        'email',
        'phone',
        'event_type',
        'estimated_date',
        'message',
        'project_id',
        'phase_id',
        'stage_id',
        'lot_number',
        'lots'
    ];
}
