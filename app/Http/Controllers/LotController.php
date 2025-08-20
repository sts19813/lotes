<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // <-- importante

class LotController extends Controller
{
    public function form()
    {
        return view('lots.form');
    }

    public function index()
    {
        $lots = Lot::orderBy('created_at', 'desc')->get();

        return view('lots.index', compact('lots'));
    }

    public function fetch(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer',
            'phase_id' => 'required|integer',
            'stage_id' => 'required|integer',
        ]);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'X-API-KEY' => env('ADARA_API_KEY'),
        ])
            ->withoutVerifying() // <- omite la verificación SSL
            ->get(
                env('ADARA_API_URL')
                . "/projects/{$request->project_id}/phases/{$request->phase_id}/stages/{$request->stage_id}/lots"
            );
        if ($response->successful()) {
            $lots = $response->json();
            return view('lots.form', [
                'lots' => $lots,
                'project_id' => $request->project_id,
                'phase_id' => $request->phase_id,
                'stage_id' => $request->stage_id,
            ]);
        }

        return back()->with('error', 'Error al consultar la API: ' . $response->status());
    }


    public function create()
    {
        return view('lots.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_lots' => 'required|integer|min:1',
            'svg_image' => 'required|mimes:svg,xml',
            'png_image' => 'required|image|mimes:png,jpg,jpeg',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'total_lots' => $request->total_lots,
        ];

        // Guardar SVG en public/lots
        if ($request->hasFile('svg_image')) {
            $svgFilename = time() . '_' . $request->file('svg_image')->getClientOriginalName();
            $request->file('svg_image')->move(public_path('lots'), $svgFilename);
            $data['svg_image'] = 'lots/' . $svgFilename; // ruta relativa desde public
        }

        // Guardar PNG en public/lots
        if ($request->hasFile('png_image')) {
            $pngFilename = time() . '_' . $request->file('png_image')->getClientOriginalName();
            $request->file('png_image')->move(public_path('lots'), $pngFilename);
            $data['png_image'] = 'lots/' . $pngFilename; // ruta relativa desde public
        }

        Lot::create($data);

        return redirect()->route('desarrollos.index')->with('success', 'Lote creado correctamente.');
    }
}
