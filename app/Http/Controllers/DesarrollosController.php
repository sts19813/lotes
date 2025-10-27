<?php

namespace App\Http\Controllers;

use App\Models\Desarrollos;
use App\Models\Lote;//donde guarda la configuracion del svg
use App\Models\Lot;//informacion del lote, m2 precio 

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
        return view('desarrollos.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_lots' => 'required|integer|min:1',
            'svg_image' => 'nullable|mimes:svg,xml',
            'png_image' => 'nullable|image|mimes:png,jpg,jpeg',
            'project_id' => 'nullable|integer',
            'phase_id' => 'nullable|integer',
            'stage_id' => 'nullable|integer',
            'modal_color' => 'nullable|string|max:10',
            'modal_selector' => 'nullable|string|max:255',
            'color_primario' => 'nullable|string|max:50',
            'color_acento' => 'nullable|string|max:50',
            'financing_months' => 'nullable|integer|min:0',
            'redirect_return' => 'nullable|string|max:255',
            'redirect_next' => 'nullable|string|max:255',
            'redirect_previous' => 'nullable|string|max:255',
            'plusvalia' => 'nullable|numeric|min:0|max:100'
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'total_lots' => $request->total_lots,
            'project_id' => $request->project_id,
            'phase_id' => $request->phase_id,
            'stage_id' => $request->stage_id,
            'modal_color' => $request->modal_color,
            'modal_selector' => $request->modal_selector,
            'color_primario' => $request->color_primario,
            'color_acento' => $request->color_acento,
            'financing_months' => $request->financing_months,
            'redirect_return' => $request->redirect_return,
            'redirect_next' => $request->redirect_next,
            'redirect_previous' => $request->redirect_previous,
            'plusvalia' => $request->plusvalia,
            'source_type' => $request->source_type
        ];

        // Guardar SVG en public/lots
        if ($request->hasFile('svg_image')) {
            $svgFilename = time() . '_' . $request->file('svg_image')->getClientOriginalName();
            $request->file('svg_image')->move(public_path('lots'), $svgFilename);
            $data['svg_image'] = 'lots/' . $svgFilename;
        }

        // Guardar PNG en public/lots
        if ($request->hasFile('png_image')) {
            $pngFilename = time() . '_' . $request->file('png_image')->getClientOriginalName();
            $request->file('png_image')->move(public_path('lots'), $pngFilename);
            $data['png_image'] = 'lots/' . $pngFilename;
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

        $sourceType = $lot->source_type ?? 'adara';

        // Inicializar variables
        $projects = [];
        $lots = [];
        $dbLotes = [];

        if ($sourceType === 'adara') {
            // Traer proyectos de Adara
            $projectsResponse = Http::withHeaders([
                'accept' => 'application/json',
                'X-API-KEY' => env('ADARA_API_KEY'),
            ])->withoutVerifying()->get(env('ADARA_API_URL') . "/projects");

            $projects = $projectsResponse->successful() ? $projectsResponse->json() : [];

            // Traer lotes de la API si se tienen project_id, phase_id y stage_id
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
        } elseif ($sourceType === 'naboo') {
            // Proyectos locales (si los necesitas en un select)
            $projects = Desarrollos::all();

            $lots = Lote::where('desarrollo_id', $lot->id)
                ->where('project_id', $lot->project_id)
                ->where('phase_id', $lot->phase_id)
                ->where('stage_id', $lot->stage_id)
                ->get(); 
            // Lotes locales filtrados por proyecto/fase/etapa
            $dbLotes = Lot::where('stage_id', $lot->stage_id)->get();  
        }

        return view('desarrollos.configurator', compact('lot', 'projects', 'lots', 'dbLotes', 'desarrollos'))
            ->with('sourceType', $sourceType);
    }



    public function iframe($id)
    {
        $lot = Desarrollos::findOrFail($id);
        $sourceType = $lot->source_type ?? 'adara';

        // Traer todos los proyectos
        $projectsResponse = Http::withHeaders([
            'accept' => 'application/json',
            'X-API-KEY' => env('ADARA_API_KEY'),
        ])->withoutVerifying()->get(env('ADARA_API_URL') . "/projects");
    
        $projects = $projectsResponse->successful() ? $projectsResponse->json() : [];
    
        // Inicializar array de lotes vacío
        $lots = [];
    
        if ($sourceType === 'adara') {
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
        }elseif ($sourceType === 'naboo') {
            // Proyectos locales (si los necesitas en un select)
            $projects = Desarrollos::all();

            // Lotes locales filtrados por proyecto/fase/etapa
            $dbLotes = Lote::where('desarrollo_id', $lot->id)
            ->where('project_id', $lot->project_id)
            ->where('phase_id', $lot->phase_id)
            ->where('stage_id', $lot->stage_id)
            ->get(); 
            $lots =  Lot::where('stage_id', $lot->stage_id)->get();
        }
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
            'total_lots' => 'required|integer|min:1',
            'svg_image' => 'nullable|mimes:svg,xml',
            'png_image' => 'nullable|image|mimes:png,jpg,jpeg',
            'project_id' => 'nullable|integer',
            'phase_id' => 'nullable|integer',
            'stage_id' => 'nullable|integer',
            'modal_color' => 'nullable|string|max:10',
            'modal_selector' => 'nullable|string|max:255',
            'color_primario' => 'nullable|string|max:50',
            'color_acento' => 'nullable|string|max:50',
            'financing_months' => 'nullable|integer|min:0',
            'redirect_return' => 'nullable|string|max:255',
            'redirect_next' => 'nullable|string|max:255',
            'redirect_previous' => 'nullable|string|max:255',
            'plusvalia' => 'nullable|numeric|min:0|max:100'
        ]);

        $data = $request->only([
            'name', 'description', 'total_lots', 'project_id', 'phase_id', 'stage_id',
            'modal_color', 'modal_selector', 'color_primario', 'color_acento',
            'financing_months', 'redirect_return', 'redirect_next', 'redirect_previous', 'plusvalia', 'source_type'
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
