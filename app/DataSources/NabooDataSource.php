<?php

namespace App\DataSources;

use App\Contracts\LotsDataSourceInterface;
use App\Models\Project;
use App\Models\Phase;
use App\Models\Stage;
use App\Models\Lot;

class NabooDataSource implements LotsDataSourceInterface
{
    /**
     * Obtiene todos los proyectos
     */
    public function getProjects(): array
    {
        return Project::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
            ->map(fn ($p) => [
                'id'   => $p->id,
                'name' => $p->name,
            ])
            ->toArray();
    }

    /**
     * Obtiene las fases de un proyecto
     */
    public function getPhases(int $projectId): array
    {
        return Phase::query()
            ->where('project_id', $projectId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
            ->map(fn ($f) => [
                'id'   => $f->id,
                'name' => $f->name,
            ])
            ->toArray();
    }

    /**
     * Obtiene las etapas de una fase
     */
    public function getStages(int $projectId, int $phaseId): array
    {
        return Stage::query()
            ->where('phase_id', $phaseId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
            ->map(fn ($s) => [
                'id'   => $s->id,
                'name' => $s->name,
            ])
            ->toArray();
    }

    /**
     * Obtiene los lotes de una etapa
     */
    public function getLots(int $projectId, int $phaseId, int $stageId): array
    {
        return Lot::query()
            ->where('stage_id', $stageId)
            ->select('id', 'name', 'status')
            ->get()
            ->map(function ($lot) {
                return [
                    'id'     => $lot->id,
                    'name'   => $lot->name,
                    'status' => $this->mapStatus($lot->status),
                ];
            })
            ->toArray();
    }

    /**
     * Normaliza estatus de Naboo a estatus del dashboard
     */
    protected function mapStatus(string $status): string
    {
        return match ($status) {
            'available', 'for_sale' => 'for_sale',
            'sold'                  => 'sold',
            'reserved'              => 'reserved',
            'blocked', 'locked'     => 'locked_sale',
            default                 => 'unknown',
        };
    }
}
