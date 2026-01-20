<?php

namespace App\DataSources;

use App\Contracts\LotsDataSourceInterface;
use App\Services\AdaraService;

class AdaraDataSource implements LotsDataSourceInterface
{
    protected AdaraService $adara;

    public function __construct(AdaraService $adara)
    {
        $this->adara = $adara;
    }

    public function getProjects(): array
    {
        return $this->adara->getProjects();
    }

    public function getPhases(int $projectId): array
    {
        return $this->adara->getPhases($projectId);
    }

    public function getStages(int $projectId, int $phaseId): array
    {
        return $this->adara->getStages($projectId, $phaseId);
    }

    public function getLots(int $projectId, int $phaseId, int $stageId): array
    {
        return $this->adara->getLots($projectId, $phaseId, $stageId);
    }
}
