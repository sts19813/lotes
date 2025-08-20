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

Route::get('/desarrollos', [LotController::class, 'index'])->name('desarrollos.index');

Route::get('/desarrollos/create', [LotController::class, 'create'])->name('desarrollos.create');
Route::post('/desarrollos', [LotController::class, 'store'])->name('desarrollo.store');