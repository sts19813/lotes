<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use App\Services\AdaraService;
use App\Models\Project;
use App\Models\Phase;
use App\Models\Stage;
use App\Models\Lot;
use App\Models\MigrationLog;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class MigrarAdaraJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(AdaraService $adara)
    {
        Log::info("🟦 Iniciando proceso de migración desde Job.");

        try {
            $projects = $adara->getProjects();
            Log::info("📌 Total de proyectos a migrar: " . count($projects));
        } catch (\Exception $e) {
            Log::error("❌ ERROR obteniendo proyectos de Adara: " . $e->getMessage());
            return;
        }

        Log::info("🔄 Iniciando migración de proyectos…");

        foreach ($projects as $p) {

            try {
                Log::info("➡️ Procesando proyecto: {$p['name']} (Adara ID: {$p['id']})");

                $project = Project::firstOrCreate(
                    ['name' => $p['name']],
                    [
                        'user_id' => 3,
                        'email' => $p['email'] ?? null,
                        'phone' => $p['phone'] ?? null,
                    ]
                );

                Log::info("✔ Proyecto migrado: {$project->id}");

                MigrationLog::create([
                    'type' => 'project',
                    'origin_id' => $p['id'],
                    'target_id' => $project->id,
                    'status' => 'done'
                ]);
            } catch (\Exception $e) {
                Log::error("❌ Error migrando proyecto {$p['id']}: " . $e->getMessage());

                MigrationLog::create([
                    'type' => 'project',
                    'origin_id' => $p['id'],
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);

                continue; // saltar fases si el proyecto falló
            }

            // ======================
            // FASES
            // ======================
            try {
                $phases = $adara->getPhases($p['id']);
                Log::info("📌 Fases encontradas: " . count($phases));
            } catch (\Exception $e) {
                Log::error("❌ ERROR obteniendo fases: " . $e->getMessage());
                continue;
            }

            foreach ($phases as $f) {

                try {
                    Log::info("➡️ Procesando fase: {$f['name']}");

                    $phase = Phase::firstOrCreate(
                        ['project_id' => $project->id, 'name' => $f['name']],
                        ['start_date' => now()]
                    );

                    Log::info("✔ Fase migrada: {$phase->id}");

                    MigrationLog::create([
                        'type' => 'phase',
                        'origin_id' => $f['id'],
                        'target_id' => $phase->id,
                        'status' => 'done'
                    ]);
                } catch (\Exception $e) {
                    Log::error("❌ Error migrando fase {$f['id']}: " . $e->getMessage());
                    continue;
                }

                // ======================
                // ETAPAS
                // ======================
                try {
                    $stages = $adara->getStages($p['id'], $f['id']);
                    Log::info("📌 Etapas encontradas: " . count($stages));
                } catch (\Exception $e) {
                    Log::error("❌ ERROR obteniendo etapas: " . $e->getMessage());
                    continue;
                }

                foreach ($stages as $s) {

                    try {
                        Log::info("➡️ Procesando etapa: {$s['name']}");

                        $stage = Stage::firstOrCreate(
                            ['phase_id' => $phase->id, 'name' => $s['name']],
                            [
                                'credit_scheme_id' => null,
                                'enterprise_id' => null,
                            ]
                        );

                        Log::info("✔ Etapa migrada: {$stage->id}");

                        MigrationLog::create([
                            'type' => 'stage',
                            'origin_id' => $s['id'],
                            'target_id' => $stage->id,
                            'status' => 'done'
                        ]);
                    } catch (\Exception $e) {
                        Log::error("❌ Error migrando etapa {$s['id']}: " . $e->getMessage());
                        continue;
                    }

                    // ======================
                    // LOTES
                    // ======================
                    try {
                        $lots = $adara->getLots($p['id'], $f['id'], $s['id']);
                        Log::info("📌 Lotes encontrados: " . count($lots));
                    } catch (\Exception $e) {
                        Log::error("❌ ERROR obteniendo lotes: " . $e->getMessage());
                        continue;
                    }

                    foreach ($lots as $lote) {

                        try {
                            Log::info("➡️ Migrando lote: {$lote['name']}");

                            $lot = Lot::firstOrCreate(
                            [
                                'stage_id' => $stage->id,
                                'id_lote'  => $lote['id'],
                            ],
                            [
                                'name' => $lote['name'],
                                'depth' => $lote['depth'] ?? null,
                                'front' => $lote['front'] ?? null,
                                'area' => $lote['area'] ?? null,
                                'price_square_meter' => $lote['price_square_meter'] ?? null,
                                'total_price' => $lote['total_price'] ?? null,
                                'status' => $lote['status'] ?? 'available',
                                'chepina' => $lote['chepina'] ?? null,
                            ]
                            );

                            Log::info("✔ Lote migrado: {$lot->id}");

                            MigrationLog::create([
                                'type' => 'lot',
                                'origin_id' => $lote['id'],
                                'target_id' => $lot->id,
                                'status' => 'done'
                            ]);
                        } catch (\Exception $e) {
                            Log::error("❌ Error migrando lote {$lote['id']}: " . $e->getMessage());
                            continue;
                        }
                    } // lots
                } // stages
            } // phases
        } // projects

        Log::info("🟩 Migración completada correctamente.");

        MigrationLog::create([
            'type' => 'finished',
            'status' => 'done'
        ]);
    }
}
