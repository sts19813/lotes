<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Financiamiento extends Model
{
    use HasFactory;

    protected $table = 'financiamientos';

    /**
     * Campos que pueden asignarse masivamente
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'meses',
        'porcentaje_enganche',
        'interes_anual',
        'descuento_porcentaje',
        'monto_minimo',
        'monto_maximo',
        'periodicidad_pago',
        'cargo_apertura',
        'penalizacion_mora',
        'plazo_gracia_meses',
        'activo',
    ];

    /**
     * Casts automáticos
     */
    protected $casts = [
        'activo' => 'boolean',
        'porcentaje_enganche' => 'decimal:2',
        'interes_anual' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'monto_minimo' => 'decimal:2',
        'monto_maximo' => 'decimal:2',
        'cargo_apertura' => 'decimal:2',
        'penalizacion_mora' => 'decimal:2',
    ];

    /**
     * Relación: un financiamiento puede aplicarse a muchos desarrollos
     */
    public function desarrollos()
    {
        return $this->belongsToMany(
            Desarrollos::class, 
            'desarrollo_financiamiento', 
            'financiamiento_id', 
            'desarrollo_id'
        );
    }

    /**
     * Accesor para mostrar el nombre completo del plan
     * Ejemplo: "Plan Oro (60 meses - 8% interés)"
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} ({$this->meses} meses - {$this->interes_anual}% interés)";
    }

    /**
     * Scope: solo modelos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
