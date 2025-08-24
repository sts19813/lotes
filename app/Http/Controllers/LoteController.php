<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lote;

class LoteController  extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lotes = Lote::with(['project', 'phase', 'stage'])->latest()->paginate(20);
        return view('admin.lotes.index', compact('lotes'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validación
            $request->validate([
                'project_id'   => 'nullable|integer',
                'phase_id'     => 'nullable|integer',
                'stage_id'     => 'nullable|integer',
                'lot_id'       => 'nullable|string',
                'polygonId'    => 'nullable|string',
                'redirect'     => 'nullable|boolean',
                'redirect_url' => 'nullable|string',
                'desarrollo_id'=> 'required|integer',
                'color'        => 'nullable|string|max:9',  // ejemplo: #34c759ff
                'color_active' => 'nullable|string|max:9',
            ]);
    
            // Solo tomar redirect_url si está marcado
            $redirectChecked = $request->has('redirect') && $request->redirect;
            $redirectUrl = $redirectChecked ? $request->redirect_url : null;
    
            // Crear registro
            $lote = Lote::create([
                'desarrollo_id' => $request->desarrollo_id,
                'project_id'    => $request->project_id ?: null,
                'phase_id'      => $request->phase_id ?: null,
                'stage_id'      => $request->stage_id ?: null,
                'lote_id'       => $request->lot_id ?: null,
                'selectorSVG'   => $request->polygonId,
                'redirect'      => $redirectChecked,
                'redirect_url'  => $redirectUrl,
                'color'         => $redirectChecked ? $request->color : null,
                'color_active'  => $redirectChecked ? $request->color_active : null,
            ]);
    
            return response()->json([
                'success' => true,
                'lote' => $lote
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Devolver errores de validación en JSON
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
            // Captura cualquier otro error
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Lote $lote)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'phase_id' => 'required|exists:phases,id',
            'stage_id' => 'required|exists:stages,id',
            'lote_id' => 'required|string|max:255',
            'selectorSVG' => 'required|string|max:255',
        ]);

        $lote->update($request->all());

        return back()->with('success', 'Lote actualizado correctamente');
    }

    public function destroy(Lote $lote)
    {
        $lote->delete();
        return back()->with('success', 'Lote eliminado correctamente');
    }
}
