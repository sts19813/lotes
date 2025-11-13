<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Connection;


class ConnectionController extends Controller
{
    /**
     * Mostrar listado de conexiones.
     */
    public function index()
    {
        $connections = Connection::all();
        return view('connections.index', compact('connections'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        return view('connections.create');
    }

    /**
     * Guardar una nueva conexión.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'api_url' => 'required|url',
            'api_key' => 'required|string'
        ]);

        Connection::create($request->only(['name', 'api_url', 'api_key']));

        return redirect()->route('connections.index')->with('success', 'Conexión creada exitosamente.');
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Connection $connection)
    {
        return view('connections.edit', compact('connection'));
    }

    /**
     * Actualizar una conexión existente.
     */
    public function update(Request $request, Connection $connection)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'api_url' => 'required|url',
            'api_key' => 'required|string'
        ]);

        $connection->update($request->only(['name', 'api_url', 'api_key']));

        return redirect()->route('connections.index')->with('success', 'Conexión actualizada correctamente.');
    }

    /**
     * Eliminar conexión.
     */
    public function destroy(Connection $connection)
    {
        $connection->delete();
        return redirect()->route('connections.index')->with('success', 'Conexión eliminada.');
    }
}
