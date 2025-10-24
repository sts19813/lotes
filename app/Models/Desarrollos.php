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
        'path_logo',
        'project_id',
        'phase_id',
        'stage_id',
        'modal_color',
        'modal_selector',
        'color_primario',
        'color_acento',
        'financing_months',
        'redirect_return',
        'redirect_next',
        'redirect_previous',
        'plusvalia'
    ];
}
