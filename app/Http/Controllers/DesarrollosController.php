<?php

namespace App\Http\Controllers;

use App\Models\Desarrollos;
use App\Models\Lote;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DesarrollosController extends Controller
{
    public function form()
    {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'X-API-KEY' => env('ADARA_API_KEY'),
        ])
            ->withoutVerifying()
            ->get(env('ADARA_API_URL') . "/projects");

        $projects = $response->successful() ? $response->json() : [];
        return view('lots.form', compact('projects'));
    }

    public function index()
    {
        $lots = Desarrollos::orderBy('created_at', 'desc')->get();

        return view('lots.index', compact('lots'));
    }

    public function admin()
    {
        $lots = Desarrollos::orderBy('created_at', 'desc')->get();

        return view('lots.admin', compact('lots'));
    }

    //trae todos los resultados de los lotes
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
        ->withoutVerifying()
        ->get(
            env('ADARA_API_URL') . "/projects/{$request->project_id}/phases/{$request->phase_id}/stages/{$request->stage_id}/lots",
            [
                'per_page' => 9999, // <-- Aquí agregamos el parámetro
            ]
        );
    
        $lots = $response->successful() ? $response->json() : [];
    
        return response()->json($lots);
    }


    public function create()
    {

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'X-API-KEY' => env('ADARA_API_KEY'),
        ])
            ->withoutVerifying()
            ->get(env('ADARA_API_URL') . "/projects");

        $projects = $response->successful() ? $response->json() : [];
        return view('lots.create', compact('projects'));

    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_lots' => 'required|integer|min:1',
            'svg_image' => 'required|mimes:svg,xml',
            'png_image' => 'required|image|mimes:png,jpg,jpeg',
            'project_id' => 'nullable',
            'phase_id' => 'nullable',
            'stage_id' => 'nullable',
        ]);
    
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'total_lots' => $request->total_lots,
            'project_id' => $request->project_id,
            'phase_id' => $request->phase_id,
            'stage_id' => $request->stage_id,
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

        Desarrollos::create($data);

        return redirect()->route('desarrollos.index')->with('success', 'Lote creado correctamente.');
    }


    public function getPhases($id)
    {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'X-API-KEY' => env('ADARA_API_KEY'),
        ])
            ->withoutVerifying()
            ->get(env('ADARA_API_URL') . "/projects/$id/phases");

        return $response->json();
    }

    public function getStages($projectId, $phaseId)
    {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'X-API-KEY' => env('ADARA_API_KEY'),
        ])
            ->withoutVerifying()
            ->get(env('ADARA_API_URL') . "/projects/$projectId/phases/$phaseId/stages");

        return $response->json();
    }


    public function configurator($id)
    {
        $lot = Desarrollos::findOrFail($id);
    // Traer todos los desarrollos para el select de redirección
        $desarrollos = Desarrollos::all();
        // Traer todos los proyectos
        $projectsResponse = Http::withHeaders([
            'accept' => 'application/json',
            'X-API-KEY' => env('ADARA_API_KEY'),
        ])->withoutVerifying()->get(env('ADARA_API_URL') . "/projects");
    
        $projects = $projectsResponse->successful() ? $projectsResponse->json() : [];
    
        // Inicializar array de lotes vacío
        $lots = [];
    
        // Si el lote tiene proyecto, fase y etapa, traemos los lotes
        if ($lot->project_id && $lot->phase_id && $lot->stage_id) {
            $lotsResponse = Http::withHeaders([
                'accept' => 'application/json',
                'X-API-KEY' => env('ADARA_API_KEY'),
            ])->withoutVerifying()
                ->get(
                    env('ADARA_API_URL') .
                    "/projects/{$lot->project_id}/phases/{$lot->phase_id}/stages/{$lot->stage_id}/lots",
                    [
                        'per_page' => 9999
                    ]
                );
    
            $lots = $lotsResponse->successful() ? $lotsResponse->json() : [];
        }
    
        $dbLotes = Lote::where('desarrollo_id', $lot->id)->get();


        return view('lots.configurator', compact('lot', 'projects', 'lots', 'dbLotes', 'desarrollos'));
    }


    public function iframe($id)
    {
        $lot = Desarrollos::findOrFail($id);
    
        // Traer todos los proyectos
        $projectsResponse = Http::withHeaders([
            'accept' => 'application/json',
            'X-API-KEY' => env('ADARA_API_KEY'),
        ])->withoutVerifying()->get(env('ADARA_API_URL') . "/projects");
    
        $projects = $projectsResponse->successful() ? $projectsResponse->json() : [];
    
        // Inicializar array de lotes vacío
        $lots = [];
    
        // Si el lote tiene proyecto, fase y etapa, traemos los lotes
        if ($lot->project_id && $lot->phase_id && $lot->stage_id) {
            $lotsResponse = Http::withHeaders([
                'accept' => 'application/json',
                'X-API-KEY' => env('ADARA_API_KEY'),
            ])->withoutVerifying()
                ->get(
                    env('ADARA_API_URL') .
                    "/projects/{$lot->project_id}/phases/{$lot->phase_id}/stages/{$lot->stage_id}/lots",
                    [
                        'per_page' => 9999
                    ]
                );
    
            $lots = $lotsResponse->successful() ? $lotsResponse->json() : [];
        }
    
        $dbLotes = Lote::where('desarrollo_id', $lot->id)->get();


        return view('lots.iframe', compact('lot', 'projects', 'lots', 'dbLotes'));
    }


    public function edit($id)
    {
        $lot = Desarrollos::findOrFail($id); // el registro a editar
    
        // Traer todos los proyectos
        $projectsResponse = Http::withHeaders([
            'accept' => 'application/json',
            'X-API-KEY' => env('ADARA_API_KEY'),
        ])->withoutVerifying()->get(env('ADARA_API_URL') . "/projects");
        $projects = $projectsResponse->successful() ? $projectsResponse->json() : [];
    
        // Traer fases del proyecto actual si existe
        $phases = [];
        if ($lot->project_id) {
            $phasesResponse = Http::withHeaders([
                'accept' => 'application/json',
                'X-API-KEY' => env('ADARA_API_KEY'),
            ])->withoutVerifying()->get(env('ADARA_API_URL') . "/projects/{$lot->project_id}/phases");
    
            $phases = $phasesResponse->successful() ? $phasesResponse->json() : [];
        }
    
        // Traer stages de la fase actual si existe
        $stages = [];
        if ($lot->phase_id) {
            $stagesResponse = Http::withHeaders([
                'accept' => 'application/json',
                'X-API-KEY' => env('ADARA_API_KEY'),
            ])->withoutVerifying()->get(env('ADARA_API_URL') . "/projects/{$lot->project_id}/phases/{$lot->phase_id}/stages");
    
            $stages = $stagesResponse->successful() ? $stagesResponse->json() : [];
        }
    
        return view('desarrollos.edit', compact('lot', 'projects', 'phases', 'stages'));
    }

    public function update(Request $request, $id)
    {
        $desarrollo = Desarrollos::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'nullable',
            'phase_id' => 'nullable',
            'stage_id' => 'nullable',
            'total_lots' => 'required|integer|min:1',

        ]);

        $data = $request->only([
            'name', 'description', 'project_id', 'phase_id', 'stage_id', 'total_lots'
        ]);

        // Guardar SVG en public/lots si hay archivo
        if ($request->hasFile('svg_image')) {
            $svgFilename = time() . '_' . $request->file('svg_image')->getClientOriginalName();
            $request->file('svg_image')->move(public_path('lots'), $svgFilename);
            $data['svg_image'] = 'lots/' . $svgFilename;
        }

        // Guardar PNG en public/lots si hay archivo
        if ($request->hasFile('png_image')) {
            $pngFilename = time() . '_' . $request->file('png_image')->getClientOriginalName();
            $request->file('png_image')->move(public_path('lots'), $pngFilename);
            $data['png_image'] = 'lots/' . $pngFilename;
        }

        $desarrollo->update($data);

        return redirect()->route('desarrollos.index')->with('success', 'Desarrollo actualizado correctamente.');
    }

    public function destroy($id)
    {
        $desarrollo = Desarrollos::findOrFail($id);
        $desarrollo->delete();

        return redirect()->route('desarrollos.index')->with('success', 'Desarrollo eliminado correctamente.');
    }
}
