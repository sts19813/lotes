<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MigracionController extends Controller
{
    /**
     * Muestra la página principal de migración.
     */
    public function index()
    {
        return view('migracion.index');
    }

    /**
     * Simula la conexión con el sistema de Adara y la importación de datos.
     */
    public function importar(Request $request)
    {
        // ⚙️ Simulación de conexión con el sistema Adara
        sleep(2); // Simula tiempo de conexión

        // 🔄 Simulación de datos traídos
        $data = [
            'clientes_importados' => rand(10, 50),
            'proyectos_importados' => rand(3, 10),
            'unidades_importadas' => rand(100, 300),
            'estado' => 'Éxito',
        ];

        return response()->json($data);
    }
}
