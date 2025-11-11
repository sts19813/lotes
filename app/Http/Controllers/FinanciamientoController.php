<?php

namespace App\Http\Controllers;

use App\Models\Financiamiento;
use App\Models\Desarrollos;
use Illuminate\Http\Request;

class FinanciamientoController extends Controller
{
    /**
     * Mostrar vista principal
     */
    public function index()
    {
        $desarrollos = Desarrollos::orderBy('created_at', 'desc')->get();
        return view('financiamientos.index', compact('desarrollos'));
    }

    public function create()
    {
        $desarrollos = Desarrollos::all(); // Trae los desarrollos disponibles
        return view('financiamientos.create', compact('desarrollos'));
    }

    /**
     * Retornar datos en formato JSON para DataTable
     */
    public function data()
    {
        $data = Financiamiento::with('desarrollos:id,name')->get();

        return response()->json([
            'data' => $data
        ]);
    }

    /**
     * Guardar nuevo financiamiento
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
            'visible' => 'required|boolean',

            // Porcentajes principales
            'porcentaje_enganche' => 'required|numeric|min:0',
            'porcentaje_financiamiento' => 'required|numeric|min:0',
            'porcentaje_saldo' => 'required|numeric|min:0',

            // Interés y descuentos
            'descuento_porcentaje' => 'nullable|numeric|min:0',
            'financiamiento_interes' => 'nullable|numeric|min:0',
            'financiamiento_cuota_apertura' => 'nullable|numeric|min:0',

            // Enganche
            'enganche_diferido' => 'required|boolean',
            'enganche_num_pagos' => 'nullable|integer|min:1',

            // Financiamiento
            'financiamiento_meses' => 'nullable|integer|min:1',

            // Anualidad
            'tiene_anualidad' => 'required|boolean',
            'porcentaje_anualidad' => 'nullable|numeric|min:0',
            'numero_anualidades' => 'nullable|integer|min:0',
            'pagos_por_anualidad' => 'nullable|integer|min:0',

            // Saldo / Contado
            'saldo_diferido' => 'required|boolean',
            'saldo_num_pagos' => 'nullable|integer|min:1',

            // Estado
            'activo' => 'sometimes|boolean',

            // Relación
            'desarrollos' => 'nullable|array',
            'desarrollos.*' => 'exists:desarrollos,id',
        ]);

        $validated['activo'] = $request->input('activo', true);

        $financiamiento = Financiamiento::create($validated);

        if (!empty($validated['desarrollos'])) {
            $financiamiento->desarrollos()->sync($validated['desarrollos']);
        }

        return response()->json([
            'success' => true,
            'financiamiento' => $financiamiento->load('desarrollos')
        ]);
    }

    /**
     * Editar financiamiento (retorna datos JSON)
     */
    public function edit($id)
    {
        $financiamiento = Financiamiento::with('desarrollos')->findOrFail($id);
        $desarrollos = Desarrollos::all();

        // Aseguramos los booleanos sean 0 o 1 para el frontend
        $financiamiento->visible = (int) $financiamiento->visible;
        $financiamiento->enganche_diferido = (int) $financiamiento->enganche_diferido;
        $financiamiento->tiene_anualidad = (int) $financiamiento->tiene_anualidad;
        $financiamiento->saldo_diferido = (int) $financiamiento->saldo_diferido;

        return view('financiamientos.edit', compact('financiamiento', 'desarrollos'));
    }

    /**
     * Actualizar financiamiento existente
     */
    public function update(Request $request, Financiamiento $financiamiento)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
            'visible' => 'required|boolean',

            'porcentaje_enganche' => 'required|numeric|min:0',
            'porcentaje_financiamiento' => 'required|numeric|min:0',
            'porcentaje_saldo' => 'required|numeric|min:0',

            'descuento_porcentaje' => 'nullable|numeric|min:0',
            'financiamiento_interes' => 'nullable|numeric|min:0',
            'financiamiento_cuota_apertura' => 'nullable|numeric|min:0',

            'enganche_diferido' => 'required|boolean',
            'enganche_num_pagos' => 'nullable|integer|min:1',

            'financiamiento_meses' => 'nullable|integer|min:1',

            'tiene_anualidad' => 'required|boolean',
            'porcentaje_anualidad' => 'nullable|numeric|min:0',
            'numero_anualidades' => 'nullable|integer|min:0',
            'pagos_por_anualidad' => 'nullable|integer|min:0',

            'saldo_diferido' => 'required|boolean',
            'saldo_num_pagos' => 'nullable|integer|min:1',

            'activo' => 'sometimes|boolean',

            'desarrollos' => 'nullable|array',
            'desarrollos.*' => 'exists:desarrollos,id',
        ]);

        $financiamiento->update($validated);

        if (!empty($validated['desarrollos'])) {
            $financiamiento->desarrollos()->sync($validated['desarrollos']);
        } else {
            $financiamiento->desarrollos()->detach();
        }

        return response()->json([
            'success' => true,
            'financiamiento' => $financiamiento->load('desarrollos')
        ]);
    }

    /**
     * Eliminar financiamiento
     */
    public function destroy(Financiamiento $financiamiento)
    {
        $financiamiento->desarrollos()->detach();
        $financiamiento->delete();

        return response()->json(['success' => true]);
    }
}
