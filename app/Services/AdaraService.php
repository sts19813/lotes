<?php

namespace App\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;

class AdaraService
{
    public function request($endpoint, $params = [])
{
    try {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'X-API-KEY' => config('services.adara.key'),
        ])
        ->withoutVerifying()
        ->timeout(5)
        ->get(config('services.adara.url') . $endpoint, $params);

        if ($response->successful()) {
            return $response->json();
        }

        // Error HTTP controlado (400, 500, etc.)
        Log::warning('Adara API error', [
            'endpoint' => $endpoint,
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        return [];

    } catch (\Throwable $e) {

        // ❌ Evita pantalla roja
        Log::error('Adara API connection failed', [
            'endpoint' => $endpoint,
            'error' => $e->getMessage()
        ]);

        return [];
    }
}

    public function getProjects()
    {
        return $this->request("/projects");
    }

    public function getPhases($projectId)
    {
        return $this->request("/projects/$projectId/phases");
    }

    public function getStages($projectId, $phaseId)
    {
        return $this->request("/projects/$projectId/phases/$phaseId/stages");
    }

    public function getLots($projectId, $phaseId, $stageId)
    {
        return $this->request("/projects/$projectId/phases/$phaseId/stages/$stageId/lots", [
            'per_page' => 9999
        ]);
    }

    //obtiene el nombre del Proyecto por medio del Id del mismo
    public function getProjectName(int $projectId): ?string
    {
        $projects = $this->getProjects();
        foreach ($projects as $p) {
            if ((int)$p['id'] === $projectId) return $p['name'] ?? null;
        }
        return null;
    }

    //obtiene el nombre de la fase por medio del Id del proyecto y de la misma fase
    public function getPhaseName(int $projectId, int $phaseId): ?string
    {
        $phases = $this->getPhases($projectId);
        foreach ($phases as $f) {
            if ((int)$f['id'] === $phaseId) return $f['name'] ?? null;
        }
        return null;
    }

    /**
     * Obtiene el nombre de la etapa de adara por medio de el id proyecto, fase y de la misma etapa a obener nombre.
     */
    public function getStageName(int $projectId, int $phaseId, int $stageId): ?string
    {
        $stages = $this->getStages($projectId, $phaseId);
        foreach ($stages as $s) {
            if ((int)$s['id'] === $stageId) return $s['name'] ?? null;
        }
        return null;
    }
}
