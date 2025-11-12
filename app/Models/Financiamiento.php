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
        'visible',

        // Porcentajes principales
        'porcentaje_enganche',
        'porcentaje_financiamiento',
        'porcentaje_saldo',

        // Descuentos e intereses
        'descuento_porcentaje',
        'financiamiento_interes',
        'financiamiento_cuota_apertura',

        // Enganche
        'enganche_diferido',
        'enganche_num_pagos',

        // Financiamiento
        'financiamiento_meses',

        // Anualidad
        'tiene_anualidad',
        'porcentaje_anualidad',
        'numero_anualidades',
        'pagos_por_anualidad',

        // Saldo / Contado
        'saldo_diferido',
        'saldo_num_pagos',

        // Estado
        'activo',
    ];

    /**
     * Casts automáticos
     */
    protected $casts = [
        'visible' => 'boolean',
        'activo' => 'boolean',

        'porcentaje_enganche' => 'decimal:2',
        'porcentaje_financiamiento' => 'decimal:2',
        'porcentaje_saldo' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'financiamiento_interes' => 'decimal:2',
        'financiamiento_cuota_apertura' => 'decimal:2',
        'porcentaje_anualidad' => 'decimal:2',

        'enganche_diferido' => 'boolean',
        'tiene_anualidad' => 'boolean',
        'saldo_diferido' => 'boolean',
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
     * Accesor para mostrar nombre completo del plan
     * Ejemplo: "Plan Premium (60 meses - 2.5% interés anual)"
     */
    public function getNombreCompletoAttribute()
    {
        $interes = $this->financiamiento_interes ? "{$this->financiamiento_interes}%" : "0%";
        $meses = $this->financiamiento_meses ?? 0;
        return "{$this->nombre} ({$meses} meses - {$interes} interés)";
    }

    /**
     * Scope: solo planes activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true)
                    ->select('*'); 
    }
}
