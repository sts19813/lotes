<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'area',
        'price_square_meter',
        'down_payment_percent',
        'financing_months',
        'annual_appreciation',
        'chepina',
        'lead_name',
        'lead_phone',
        'lead_email',
        'city',
        'precio_total',
        'enganche_porcentaje',
        'enganche_monto',
        'mensualidad',
        'plusvalia_total',
        'roi',
        'years_data', // json de los años
        'chepina_url',
        'desarrollo_id',
        'desarrollo_name',
        'phase_id',
        'stage_id',
        'source_type',
    ];

    protected $casts = [
        'years_data' => 'array', // Laravel convierte array a JSON automáticamente
    ];
}
