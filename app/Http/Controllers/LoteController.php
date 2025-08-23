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
        $request->validate([
            'project_id' => 'required|integer',
            'phase_id' => 'required|integer',
            'stage_id' => 'required|integer',
            'lot_id' => 'required|integer',
            'polygonId' => 'required|string',
            'redirect' => 'nullable|boolean',
            'redirect_url' => 'nullable|url',
            'desarrollo_id' => 'required|integer', // <-- nuevo
        ]);
    
        $lote = Lote::create([
            'desarrollo_id' => $request->desarrollo_id, // <-- nuevo
            'project_id' => $request->project_id,
            'phase_id' => $request->phase_id,
            'stage_id' => $request->stage_id,
            'lote_id' => $request->lot_id,
            'selectorSVG' => $request->polygonId,
            'redirect' => $request->has('redirect') ? true : false,
            'redirect_url' => $request->redirect_url,
        ]);
        return response()->json([
            'success' => true,
            'lote' => $lote
        ]);
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
