<?php

namespace App\Http\Controllers;
use App\Models\Financiamiento;
use App\Models\Desarrollos;

use Illuminate\Http\Request;

class FinanciamientoController extends Controller
{
    public function index()
    {
        $desarrollos = Desarrollos::orderBy('created_at', 'desc')->get();

        return view('financiamientos.index', compact('desarrollos'));

    }

    public function data()
    {
        $data = Financiamiento::with('desarrollos:id,name')->get();

        return response()->json([
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        // Validación
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
            'meses' => 'required|integer|min:1',
            'porcentaje_enganche' => 'required|numeric|min:0',
            'interes_anual' => 'required|numeric|min:0',
            'descuento_porcentaje' => 'nullable|numeric|min:0',
            'monto_minimo' => 'nullable|numeric|min:0',
            'monto_maximo' => 'nullable|numeric|min:0',
            'periodicidad_pago' => 'required|in:mensual,bimestral,trimestral',
            'cargo_apertura' => 'nullable|numeric|min:0',
            'penalizacion_mora' => 'nullable|numeric|min:0',
            'plazo_gracia_meses' => 'nullable|integer|min:0',
            'activo' => 'required|boolean',
            'desarrollos' => 'nullable|array',
            'desarrollos.*' => 'exists:desarrollos,id',
        ]);

        // Crear financiamiento
        $financiamiento = Financiamiento::create($validated);

        // Relacionar desarrollos si existen
        if (!empty($validated['desarrollos'])) {
            $financiamiento->desarrollos()->sync($validated['desarrollos']);
        }

        return response()->json([
            'success' => true,
            'financiamiento' => $financiamiento->load('desarrollos')
        ]);
    }


    public function edit(Financiamiento $financiamiento)
    {
        $desarrollos = Desarrollos::orderBy('created_at', 'desc')->get();
        return response()->json([
            'financiamiento' => $financiamiento->load('desarrollos'),
            'desarrollos' => $desarrollos
        ]);
    }

    public function update(Request $request, Financiamiento $financiamiento)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
            'meses' => 'required|integer|min:1',
            'porcentaje_enganche' => 'required|numeric|min:0',
            'interes_anual' => 'required|numeric|min:0',
            'descuento_porcentaje' => 'nullable|numeric|min:0',
            'monto_minimo' => 'nullable|numeric|min:0',
            'monto_maximo' => 'nullable|numeric|min:0',
            'periodicidad_pago' => 'required|in:mensual,bimestral,trimestral',
            'cargo_apertura' => 'nullable|numeric|min:0',
            'penalizacion_mora' => 'nullable|numeric|min:0',
            'plazo_gracia_meses' => 'nullable|integer|min:0',
            'activo' => 'required|boolean',
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

    public function destroy(Financiamiento $financiamiento)
    {
        $financiamiento->desarrollos()->detach();
        $financiamiento->delete();

        return response()->json(['success' => true]);
    }
}
