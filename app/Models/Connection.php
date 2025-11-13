<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    // Campos que se pueden asignar de forma masiva
    protected $fillable = [
        'name',
        'api_url',
        'api_key',
    ];

    // Oculta el api_key si se desea al serializar
    protected $hidden = [
        'api_key',
    ];
}
