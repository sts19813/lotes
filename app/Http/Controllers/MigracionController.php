<?php

namespace App\Http\Controllers;
use App\Jobs\MigrarAdaraJob;
use App\Models\MigrationLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use App\Models\Lot;
use Illuminate\Support\Facades\Http;

class MigracionController extends Controller
{
    /**
     * Muestra la página principal de migración.
     */
    public function index()
    {
        return view('migracion.index', [
                'proyectos' => MigrationLog::where('type', 'project')->count(),
                'fases'     => MigrationLog::where('type', 'phase')->count(),
                'etapas'    => MigrationLog::where('type', 'stage')->count(),
                'lotes'     => MigrationLog::where('type', 'lot')->count(),
            ]);
    }

    /**
     * Dispara la migración completa mediante un Job (cola)
     */
    public function importar()
    {
        Log::info('🔔 Importar llamado — ejecutando migración');

        // Versión sin colas (debug):
        Bus::dispatchSync(new MigrarAdaraJob);

        return response()->json([
            'status' => 'done',
            'message' => 'Migración completada.'
        ]);
    }

    /**
     * Retorna avance en tiempo real
     */
    public function progreso()
    {
        return response()->json([
            'projects' => MigrationLog::where('type', 'project')->count(),
            'phases'   => MigrationLog::where('type', 'phase')->count(),
            'stages'   => MigrationLog::where('type', 'stage')->count(),
            'lots'     => MigrationLog::where('type', 'lot')->count(),
            'done'     => MigrationLog::where('type', 'finished')->exists()
        ]);

    }


    
    public function descargarChepinas()
    {
        // Reset progreso
        cache()->put('chepinas.total', Lot::whereNotNull('chepina')->count());
        cache()->put('chepinas.actual', 0);

        dispatch(function () {

            $lotes = Lot::whereNotNull('chepina')->get();
            $total = $lotes->count();
            $actual = 0;

            foreach ($lotes as $lot) {
                $actual++;
                cache()->put('chepinas.actual', $actual);

                $url = $lot->chepina;

                // Si NO es URL, saltar
                if (!filter_var($url, FILTER_VALIDATE_URL)) {
                    Log::warning("⚠️ Lote {$lot->id} tiene chepina NO URL: {$url}");
                    continue;
                }

                try {
                    $response = Http::withoutVerifying()->get($url);

                    if ($response->successful()) {


                        if (!is_dir(public_path('chepinas'))) {
                        mkdir(public_path('chepinas'), 0777, true);
                        }
                        // nombre real
                        $filename = 'chepina_' . $lot->id . '.jpg';

                        file_put_contents(
                            public_path('chepinas/' . $filename),
                            $response->body()
                        );

                        // actualizar BD
                        $lot->update([
                            'chepina' =>  $filename
                        ]);
                    }

                } catch (\Exception $e) {
                    Log::error("Error descargando chepina lote {$lot->id}: " . $e->getMessage());
                }
            }

            cache()->put('chepinas.finalizado', true);

        })->afterResponse(); // no bloquea la petición

        return response()->json(['status' => 'ok']);
    }


    public function progresoChepinas()
    {
        $total = cache()->get('chepinas.total', 0);
        $actual = cache()->get('chepinas.actual', 0);

        return response()->json([
            'total'      => $total,
            'actual'     => $actual,
            'porcentaje' => $total > 0 ? round(($actual / $total) * 100, 2) : 0,
            'finalizado' => cache()->get('chepinas.finalizado', false)
        ]);
    }
}
