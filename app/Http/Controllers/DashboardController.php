<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdaraService;

class DashboardController extends Controller
{
    private AdaraService $adara;

    public function __construct(AdaraService $adaraService)
    {
        $this->adara = $adaraService;
    }

    public function index()
    {
        $projects = $this->adara->getProjects();
        return view('dashboard.index', compact('projects'));
    }

    public function getData(Request $request)
    {
        $projectId = $request->project_id;
        $phaseId   = $request->phase_id;
        $stageId   = $request->stage_id;
        $status    = $request->status;

        $stats = [
            'total'        => 0,
            'available'    => 0,
            'sold'         => 0,
            'reserved'     => 0,
            'blocked'      => 0,
            'resume'       => []
        ];

        // Si no hay proyecto → obtener todos
        $projects = $projectId
            ? collect($this->adara->getProjects())->where('id', $projectId)
            : collect($this->adara->getProjects());

        foreach ($projects as $project) {

            $phases = $phaseId
                ? collect($this->adara->getPhases($project['id']))->where('id', $phaseId)
                : collect($this->adara->getPhases($project['id']));

            foreach ($phases as $phase) {

                $stages = $stageId
                    ? collect($this->adara->getStages($project['id'], $phase['id']))->where('id', $stageId)
                    : collect($this->adara->getStages($project['id'], $phase['id']));

                foreach ($stages as $stage) {

                    $lots = collect($this->adara->getLots($project['id'], $phase['id'], $stage['id']));

                    // Filtrado opcional por status seleccionado
                    if ($status) {
                        $lots = $lots->where('status', $status);
                    }

                    // Contadores por estado (basados en la API)
                    $total     = $lots->count();
                    $available = $lots->where('status', 'for_sale')->count();
                    $sold      = $lots->where('status', 'sold')->count();
                    $reserved  = $lots->where('status', 'reserved')->count();
                    $blocked   = $lots->where('status', 'locked_sale')->count();

                    // Acumuladores globales
                    $stats['total']     += $total;
                    $stats['available'] += $available;
                    $stats['sold']      += $sold;
                    $stats['reserved']  += $reserved;
                    $stats['blocked']   += $blocked;

                    // Agrega resumen del nivel
                    $stats['resume'][] = [
                        'project'   => $project['name'],
                        'phase'     => $phase['name'],
                        'stage'     => $stage['name'],
                        'total'     => $total,
                        'available' => $available,
                        'sold'      => $sold,
                        'reserved'  => $reserved,
                        'blocked'   => $blocked,
                    ];
                }
            }
        }

        return response()->json($stats);
    }


}
