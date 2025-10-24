<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lot;

class LotController extends Controller
{

    public function index(Request $request)
    {
        $query = Lot::with(['stage.phase.project']);

        if ($request->project_id) {
            $query->whereHas('stage.phase.project', function($q) use ($request) {
                $q->where('id', $request->project_id);
            });
        }
        if ($request->phase_id) {
            $query->whereHas('stage.phase', function($q) use ($request) {
                $q->where('id', $request->phase_id);
            });
        }
        if ($request->stage_id) {
            $query->where('stage_id', $request->stage_id);
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'stage_id' => 'required|exists:stages,id',
            'name' => 'required|string',
            'depth' => 'nullable|numeric',
            'front' => 'nullable|numeric',
            'area' => 'nullable|numeric',
            'price_square_meter' => 'nullable|numeric',
            'total_price' => 'nullable|numeric',
            'status' => 'nullable|string',
            'chepina' => 'nullable|string'
        ]);

        $lot = Lot::create($request->all());

        return response()->json(
            $lot->load(['stage.phase.project', 'customFields']),
            201
        );
    }

    public function show(Lot $lot)
    {
        return $lot->load(['stage.phase.project', 'customFields']);
    }

    public function update(Request $request, Lot $lot)
    {
        $lot->update($request->all());
        return response()->json($lot->load(['stage.phase.project', 'customFields']));
    }

    public function destroy(Lot $lot)
    {
        $lot->delete();
        return response()->json(null, 204);
    }
}
