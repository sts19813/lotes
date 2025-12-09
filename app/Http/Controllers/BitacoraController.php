<?php

namespace App\Http\Controllers;
use App\Models\MigrationLog;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    public function index()
    {
        // 1. Cargar logs de migración desde DB
        $migrationLogs = MigrationLog::orderBy('id', 'desc')->get();

        // 2. Leer log de Laravel desde archivo
        $logPath = storage_path('logs/laravel.log');
        $laravelLogs = [];

        if (File::exists($logPath)) {

            // Leer el archivo completo
            $content = File::get($logPath);

            // Separar por líneas que empiecen con [
            preg_match_all('/\[(.*?)\]\s([a-zA-Z0-9_.]+)\.(\w+):\s(.*)/m', $content, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $laravelLogs[] = [
                    'datetime' => $match[1],            // 2025-01-20 15:43:12
                    'env'      => $match[2],            // local / production
                    'level'    => strtoupper($match[3]), // ERROR, INFO, WARNING
                    'message'  => $match[4],            // mensaje limpio
                ];
            }

            // Ordenar: más recientes primero
            $laravelLogs = array_reverse($laravelLogs);
        }

        return view('bitacora.index', compact('migrationLogs', 'laravelLogs'));
    }
}