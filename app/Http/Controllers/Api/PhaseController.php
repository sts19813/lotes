<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Phase;

class PhaseController extends Controller
{
    public function index()
    {
        return Phase::with('stages.lots')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string',
            'start_date' => 'nullable|date',
        ]);

        $phase = Phase::create($request->all());

        return response()->json($phase, 201);
    }

    public function show(Phase $phase)
    {
        return $phase->load('stages.lots');
    }

    public function update(Request $request, Phase $phase)
    {
        $phase->update($request->all());
        return response()->json($phase);
    }

    public function destroy(Phase $phase)
    {
        $phase->delete();
        return response()->json(null, 204);
    }
}
