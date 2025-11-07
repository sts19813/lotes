<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesarrolloFinanciamiento extends Model
{
    use HasFactory;

    protected $table = 'desarrollo_financiamiento';

    protected $fillable = [
        'desarrollo_id',
        'financiamiento_id',
        'precio_final',
        'vigencia',
        'visible_en_web',
    ];

    public function desarrollo()
    {
        return $this->belongsTo(Desarrollos::class);
    }

    public function financiamiento()
    {
        return $this->belongsTo(Financiamiento::class);
    }
}
