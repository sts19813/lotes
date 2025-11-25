<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
      protected $fillable = [
        'stage_id',
        'name',
        'depth',
        'front',
        'area',
        'price_square_meter',
        'total_price',
        'status',
        'chepina',

        // Nuevos campos EXACTOS como en la migración
        'area2',             // Área 2
        'front2',            // Frente 2
        'depth2',            // Fondo 2
        'height',            // Altura
        'floor_resistance',  // Resistencia de piso
        'hanging_point',     // Punto de colgado

        'auditorium',        // Auditorio
        'school',            // Escuela
        'horseshoe',         // Herradura
        'russian_table',     // Mesa Rusa
        'banquet',           // Banquete
        'cocktail',          // Coctel

        'tour_link',         // Link Recorrido
    ];

    public function stage() {
        return $this->belongsTo(Stage::class);
    }

    public function customFields() {
        return $this->morphMany(CustomField::class, 'customizable');
    }
}
