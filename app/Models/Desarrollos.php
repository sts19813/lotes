<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desarrollos extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'total_lots',
        'svg_image',
        'png_image',
        'project_id',
        'phase_id',
        'stage_id',
    ];
}
