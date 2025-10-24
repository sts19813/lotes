<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;

class LotViewController extends Controller
{
    public function index()
    {
        // 🔹 Cargar todos los proyectos para el combo
        $projects = Project::select('id', 'name')->get();

        // 🔹 Opcional: cargar fases y etapas vacías, se llenarán dinámicamente según selección
        $phases = collect(); // inicialmente vacío
        $stages = collect(); // inicialmente vacío

        // 🔹 Retornar la vista con los combos
        return view('api.lots.index', compact('projects', 'phases', 'stages'));
    }
}
