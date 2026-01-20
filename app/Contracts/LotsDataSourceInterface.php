<?php

namespace App\Contracts;

interface LotsDataSourceInterface
{
    /**
     * Obtiene todos los proyectos
     */
    public function getProjects(): array;

    /**
     * Obtiene fases de un proyecto
     */
    public function getPhases(int $projectId): array;

    /**
     * Obtiene etapas de una fase
     */
    public function getStages(int $projectId, int $phaseId): array;

    /**
     * Obtiene lotes de una etapa
     */
    public function getLots(int $projectId, int $phaseId, int $stageId): array;
}
