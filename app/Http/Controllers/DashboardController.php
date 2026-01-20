<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdaraService;
use App\Factories\LotsDataSourceFactory;

class DashboardController extends Controller
{
    private AdaraService $adara;

    public function __construct(AdaraService $adaraService)
    {
        $this->adara = $adaraService;
    }

    public function index(Request $request)
    {
        $source = $request->get('source', 'adara');
        $dataSource = LotsDataSourceFactory::make($source);

        $projects = $dataSource->getProjects();

        return view('dashboards.index', compact('projects', 'source'));
    }

    public function getData(Request $request)
    {
        $source = $request->get('source', 'adara');
        $dataSource = LotsDataSourceFactory::make($source);

        // 🔑 FILTROS CORRECTAMENTE NORMALIZADOS
        $projectId = $request->filled('project_id') ? (int) $request->project_id : null;
        $phaseId = $request->filled('phase_id') ? (int) $request->phase_id : null;
        $stageId = $request->filled('stage_id') ? (int) $request->stage_id : null;
        $filterStatus = $request->filled('status') ? $request->status : null;

        $stats = [
            'total' => 0,
            'available' => 0,
            'sold' => 0,
            'reserved' => 0,
            'blocked' => 0,
            'resume' => []
        ];

        $statusMap = [
            'for_sale' => 'available',
            'sold' => 'sold',
            'reserved' => 'reserved',
            'locked_sale' => 'blocked'
        ];

        $projects = collect($dataSource->getProjects());

        if ($projectId !== null) {
            $projects = $projects->where('id', $projectId);
        }

        foreach ($projects as $project) {
            $phases = collect($dataSource->getPhases($project['id']));

            if ($phaseId !== null) {
                $phases = $phases->where('id', $phaseId);
            }

            foreach ($phases as $phase) {
                $stages = collect(
                    $dataSource->getStages($project['id'], $phase['id'])
                );

                if ($stageId !== null) {
                    $stages = $stages->where('id', $stageId);
                }

                foreach ($stages as $stage) {
                    $lots = collect(
                        $dataSource->getLots(
                            $project['id'],
                            $phase['id'],
                            $stage['id']
                        )
                    )->map(function ($lot) use ($statusMap) {
                        $lot['mapped_status'] = $statusMap[$lot['status']] ?? 'unknown';
                        return $lot;
                    });

                    if ($filterStatus) {
                        $lots = $lots->where('mapped_status', $filterStatus);
                    }

                    $total = $lots->count();
                    $available = $lots->where('mapped_status', 'available')->count();
                    $sold = $lots->where('mapped_status', 'sold')->count();
                    $reserved = $lots->where('mapped_status', 'reserved')->count();
                    $blocked = $lots->where('mapped_status', 'blocked')->count();

                    $stats['total'] += $total;
                    $stats['available'] += $available;
                    $stats['sold'] += $sold;
                    $stats['reserved'] += $reserved;
                    $stats['blocked'] += $blocked;

                    $stats['resume'][] = [
                        'project' => $project['name'],
                        'phase' => $phase['name'],
                        'stage' => $stage['name'],
                        'total' => $total,
                        'available' => $available,
                        'sold' => $sold,
                        'reserved' => $reserved,
                        'blocked' => $blocked,
                    ];
                }
            }
        }

        return response()->json($stats);
    }
}
