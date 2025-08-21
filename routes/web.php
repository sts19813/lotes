<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LotController;



Route::get('/', function () {
    return view('dashboard');
});

//consulta al api la vista y la tabla
Route::get('/consulta', [LotController::class, 'form'])->name('lots.form');
//manda la solicitud
Route::post('/lots/fetch', [LotController::class, 'fetch'])->name('lots.fetch');
Route::get('/lots/{lot}/configurator', [LotController::class, 'configurator'])->name('lots.configurator');
Route::post('/lots/{lot}/save-polygon', [LotController::class, 'savePolygonInfo'])->name('lots.savePolygonInfo');

Route::get('/desarrollos', [LotController::class, 'index'])->name('desarrollos.index');

Route::get('/desarrollos/create', [LotController::class, 'create'])->name('desarrollos.create');
Route::post('/desarrollos', [LotController::class, 'store'])->name('desarrollo.store');

Route::get('/api/projects/{id}/phases', [LotController::class, 'getPhases']);
Route::get('/api/projects/{project}/phases/{phase}/stages', [LotController::class, 'getStages']);


