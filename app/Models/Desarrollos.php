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
        'source_type',
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
        'plusvalia',
        'svg_image',
        'png_image'
    ];

    /**
     * Relación: un desarrollo puede tener muchos planes de financiamiento
     */
    public function financiamientos()
    {
        return $this->belongsToMany(
            \App\Models\Financiamiento::class,
            'desarrollo_financiamiento',
            'desarrollo_id',
            'financiamiento_id'
        );
    }
}
