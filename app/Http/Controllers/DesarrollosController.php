<?php

namespace App\Http\Controllers;

use App\Models\Desarrollos;
use App\Models\Lote;
use App\Models\Lot;
use Illuminate\Http\Request;
use App\Services\AdaraService;
use App\Services\FileUploadService;


class DesarrollosController extends Controller
{
    protected AdaraService $adaraService;
    protected FileUploadService $fileUploadService;

    /**
     * Constructor
     * Inyecta los servicios necesarios
     */
    public function __construct(AdaraService $adaraService, FileUploadService $fileUploadService)
    {
        $this->adaraService = $adaraService;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Página de consulta de lotes de Adara
     */
    public function form()
    {
        $projects = $this->adaraService->getProjects();
        return view('lots.form', compact('projects'));
    }

    /**
     * Listado de desarrollos públicos
     */
    public function index()
    {
        $lots = Desarrollos::orderBy('created_at', 'desc')->get();
        return view('lots.index', compact('lots'));
    }

    /**
     * Listado de desarrollos administrativos
     */
    public function admin()
    {
        $lots = Desarrollos::orderBy('updated_at', 'desc')->get();
        return view('lots.admin', compact('lots'));
    }

    /**
     * Fetch de lotes para un proyecto/fase/etapa específico
     */
    public function fetch(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer',
            'phase_id' => 'required|integer',
            'stage_id' => 'required|integer',
        ]);

        $lots = $this->adaraService->getLots(
            $request->project_id,
            $request->phase_id,
            $request->stage_id
        );

        return response()->json($lots);
    }

    /**
     * Formulario para crear un nuevo desarrollo
     */
    public function create()
    {
        $projects = $this->adaraService->getProjects();
        $desarrollos = Desarrollos::select('id', 'name')->get();
        return view('desarrollos.create', compact('projects', 'desarrollos'));
    }

    /**
     * Guardar un nuevo desarrollo en la base de datos
     */
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
            'plusvalia' => 'nullable|numeric|min:0|max:100',
        ]);

        $data = $request->only([
            'name','description','total_lots','project_id','phase_id','stage_id',
            'modal_color','modal_selector','color_primario','color_acento',
            'financing_months','redirect_return','redirect_next','redirect_previous',
            'plusvalia','source_type'
        ]);

        // Subir imágenes usando FileUploadService
        if ($request->hasFile('svg_image')) {
            $data['svg_image'] = $this->fileUploadService->upload($request->file('svg_image'), 'lots');
        }

        if ($request->hasFile('png_image')) {
            $data['png_image'] = $this->fileUploadService->upload($request->file('png_image'), 'lots');
        }

        Desarrollos::create($data);

        return redirect()->route('admin.index')->with('success', 'Lote creado correctamente.');
    }

    /**
     * Obtener fases de un proyecto
     */
    public function getPhases($id)
    {
        return $this->adaraService->getPhases($id);
    }

    /**
     * Obtener etapas de un proyecto/fase
     */
    public function getStages($projectId, $phaseId)
    {
        return $this->adaraService->getStages($projectId, $phaseId);
    }

    /**
     * Configurador de un desarrollo (vincula lotes con SVG)
     */
    public function configurator($id)
    {
        $lot = Desarrollos::findOrFail($id);
        $desarrollos = Desarrollos::all();
        $sourceType = $lot->source_type ?? 'adara';

        $projects = [];
        $lots = [];
        $dbLotes = [];

        if ($sourceType === 'adara') {
            $projects = $this->adaraService->getProjects();
            if ($lot->project_id && $lot->phase_id && $lot->stage_id) {
                $lots = $this->adaraService->getLots($lot->project_id, $lot->phase_id, $lot->stage_id);
            }

        $dbLotes = Lote::where('desarrollo_id', $lot->id)->get();

        } elseif ($sourceType === 'naboo') {
            $projects = Desarrollos::all();

            $lots = Lot::where('stage_id', $lot->stage_id)->get();
            $dbLotes = Lote::where([
                            'desarrollo_id' => $lot->id,
                            'project_id' => $lot->project_id,
                            'phase_id' => $lot->phase_id,
                            'stage_id' => $lot->stage_id
                        ])->get();
        }

        return view('desarrollos.configurator', compact('lot','projects','lots','dbLotes','desarrollos'))
               ->with('sourceType', $sourceType);
    }

    /**
     * Vista de iframe para mostrar lotes en SVG
     */
    public function iframe($id)
    {
        $lot = Desarrollos::findOrFail($id);
        $sourceType = $lot->source_type ?? 'adara';

        $projects = $sourceType === 'adara' ? $this->adaraService->getProjects() : Desarrollos::all();
        $lots = [];
        $dbLotes = [];

        if ($sourceType === 'adara') {
            if ($lot->project_id && $lot->phase_id && $lot->stage_id) {
                $lots = $this->adaraService->getLots($lot->project_id, $lot->phase_id, $lot->stage_id);
            }
            $dbLotes = Lote::where('desarrollo_id', $lot->id)->get();
        } elseif ($sourceType === 'naboo') {
            $lots = Lot::where('stage_id', $lot->stage_id)->get();
            $dbLotes = Lote::where([
                'desarrollo_id' => $lot->id,
                'project_id' => $lot->project_id,
                'phase_id' => $lot->phase_id,
                'stage_id' => $lot->stage_id
            ])->get();
        }

         //  Obtener financiamientos relacionados (solo activos)
        $financiamientos = $lot->financiamientos()->activos()->get();
        $templateModal = $lot->iframe_template_modal ?? 'emedos';

        return view('iframe.index', compact('lot','projects','lots','dbLotes', 'financiamientos', 'templateModal'));
    }

    /**
     * Vista para el Centro de Congresos CIC
     */
    public function clic($id){
        $lot = Desarrollos::findOrFail($id);
        
        return view('iframe.cic', compact('lot'));
    }

    /**
     * Formulario de edición de un desarrollo
     */
    public function edit($id)
    {
        $lot = Desarrollos::findOrFail($id);
        $sourceType = $lot->source_type ?? 'adara';
        $projects = [];
        $phases = [];
        $stages = [];

        if ($sourceType === 'adara') {
            $projects = $this->adaraService->getProjects();
            if ($lot->project_id) $phases = $this->adaraService->getPhases($lot->project_id);
            if ($lot->phase_id) $stages = $this->adaraService->getStages($lot->project_id, $lot->phase_id);
        }

        $desarrollos = Desarrollos::select('id', 'name')->get();

        return view('desarrollos.edit', compact('lot','projects','phases','stages','desarrollos'));
    }

    /**
     * Actualizar desarrollo existente en la basse de datos
     */
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
            'plusvalia' => 'nullable|numeric|min:0|max:100',
            'iframe_template_modal'=> 'nullable|string|max:255',
        ]);

        $data = $request->only([
            'name','description','total_lots','project_id','phase_id','stage_id',
            'modal_color','modal_selector','color_primario','color_acento',
            'financing_months','redirect_return','redirect_next','redirect_previous','plusvalia','source_type','iframe_template_modal'
        ]);

        // Manejo de archivos
        if ($request->hasFile('svg_image')) {
            $data['svg_image'] = $this->fileUploadService->upload($request->file('svg_image'), 'lots');
        }
        if ($request->hasFile('png_image')) {
            $data['png_image'] = $this->fileUploadService->upload($request->file('png_image'), 'lots');
        }

        $desarrollo->update($data);

        return redirect()->route('admin.index')->with('success', 'Desarrollo actualizado correctamente.');
    }

    /**
     * Eliminar desarrollo
     */
    public function destroy($id)
    {
        $desarrollo = Desarrollos::findOrFail($id);
        $desarrollo->delete();

        return redirect()->route('desarrollos.index')->with('success', 'Desarrollo eliminado correctamente.');
    }
}
