<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    protected $fillable = ['stage_id', 'name', 'depth', 'front', 'area', 'price_square_meter', 'total_price', 'status', 'chepina'];

    public function stage() { return $this->belongsTo(Stage::class); }
    
    public function customFields() { return $this->morphMany(CustomField::class, 'customizable'); }
}
