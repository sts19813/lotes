<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DesarrollosController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ReportController;
Route::get('/', function () {
    return view('dashboard');
});

//consulta al api la vista y la tabla
Route::get('/consulta', [DesarrollosController::class, 'form'])->name('lots.form');



//manda la solicitud
Route::post('/lots/fetch', [DesarrollosController::class, 'fetch'])->name('lots.fetch');


Route::get('/lots/{lot}/configurator', [DesarrollosController::class, 'configurator'])->name('lots.configurator');
Route::post('/lots/{lot}/save-polygon', [DesarrollosController::class, 'savePolygonInfo'])->name('lots.savePolygonInfo');



Route::get('/iframe/{lot}/', [DesarrollosController::class, 'iframe'])->name('lots.iframe');
Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');


Route::post('/Savelotes', [LoteController::class, 'store'])->name('lotes.store');



// Listar desarrollos
Route::get('/desarrollos', [DesarrollosController::class, 'index'])->name('desarrollos.index');

// Crear
Route::get('/desarrollos/create', [DesarrollosController::class, 'create'])->name('desarrollos.create');
Route::post('/desarrollos', [DesarrollosController::class, 'store'])->name('desarrollo.store');

// Editar
Route::get('/desarrollos/{desarrollo}/edit', [DesarrollosController::class, 'edit'])->name('desarrollos.edit');

// Actualizar
Route::put('/desarrollos/{desarrollo}', [DesarrollosController::class, 'update'])->name('desarrollos.update');

// Eliminar
Route::delete('/desarrollos/{desarrollo}', [DesarrollosController::class, 'destroy'])->name('desarrollos.destroy');


Route::get('/api/projects/{id}/phases', [DesarrollosController::class, 'getPhases']);
Route::get('/api/projects/{project}/phases/{phase}/stages', [DesarrollosController::class, 'getStages']);


 Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');

    // GET directo para descargar desde Admin (botón descargar)
    Route::get('/reports/{id}/download', [ReportController::class, 'download'])->name('reports.download');