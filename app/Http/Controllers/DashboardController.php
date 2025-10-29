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
        return view('dashboards.index', compact('projects'));
    }

    public function getData(Request $request)
    {
        $projectId = $request->project_id;
        $phaseId   = $request->phase_id;
        $stageId   = $request->stage_id;
        $filterStatus = $request->status;

        $stats = [
            'total'        => 0,
            'available'    => 0,
            'sold'         => 0,
            'reserved'     => 0,
            'blocked'      => 0,
            'resume'       => []
        ];

        // 🔥 Mapeo real API → Dashboard
        $statusMap = [
            'for_sale'    => 'available',
            'sold'        => 'sold',
            'reserved'    => 'reserved',
            'locked_sale' => 'blocked'
        ];

        // 👉 Cargar lotes SOLO UNA VEZ por Stage ✅
        $projects = collect($this->adara->getProjects());

        if ($projectId) {
            $projects = $projects->where('id', intval($projectId));
        }

        foreach ($projects as $project) {
            $phases = collect($this->adara->getPhases($project['id']));

            if ($phaseId) {
                $phases = $phases->where('id', intval($phaseId));
            }

            foreach ($phases as $phase) {
                $stages = collect($this->adara->getStages($project['id'], $phase['id']));

                if ($stageId) {
                    $stages = $stages->where('id', intval($stageId));
                }

                foreach ($stages as $stage) {

                    // ✅ Solo una consulta por stage
                    $lots = collect($this->adara->getLots($project['id'], $phase['id'], $stage['id']))
                        ->map(function ($lot) use ($statusMap) {
                            $lot['mapped_status'] = $statusMap[$lot['status']] ?? 'unknown';
                            return $lot;
                        });

                    // ✅ Filtrando en memoria
                    if ($filterStatus) {
                        $lots = $lots->where('mapped_status', $filterStatus);
                    }

                    // ✅ Contadores correctos
                    $stats['total']     += $lots->count();
                    $stats['available'] += $lots->where('mapped_status', 'available')->count();
                    $stats['sold']      += $lots->where('mapped_status', 'sold')->count();
                    $stats['reserved']  += $lots->where('mapped_status', 'reserved')->count();
                    $stats['blocked']   += $lots->where('mapped_status', 'blocked')->count();

                    // ✅ Resumen para tabla
                    $stats['resume'][] = [
                        'project'   => $project['name'],
                        'phase'     => $phase['name'],
                        'stage'     => $stage['name'],
                        'total'     => $lots->count(),
                        'available' => $lots->where('mapped_status', 'available')->count(),
                        'sold'      => $lots->where('mapped_status', 'sold')->count(),
                        'reserved'  => $lots->where('mapped_status', 'reserved')->count(),
                        'blocked'   => $lots->where('mapped_status', 'blocked')->count(),
                    ];
                }
            }
        }

        return response()->json($stats);
    }



}
