<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lot;

class LotController extends Controller
{
    public function index()
    {
        return Lot::with('customFields')->get();
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
            'chepina' => 'nullable|string',
        ]);

        $lot = Lot::create($request->all());

        if ($request->has('custom_fields')) {
            foreach ($request->custom_fields as $cf) {
                $lot->customFields()->create($cf);
            }
        }

        return response()->json($lot->load('customFields'), 201);
    }

    public function show(Lot $lot)
    {
        return $lot->load('customFields');
    }

    public function update(Request $request, Lot $lot)
    {
        $lot->update($request->all());

        if ($request->has('custom_fields')) {
            foreach ($request->custom_fields as $cf) {
                $lot->customFields()->updateOrCreate(
                    ['code' => $cf['code']],
                    ['value' => $cf['value']]
                );
            }
        }

        return response()->json($lot->load('customFields'));
    }

    public function destroy(Lot $lot)
    {
        $lot->delete();
        return response()->json(null, 204);
    }
}
