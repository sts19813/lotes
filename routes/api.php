<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\PhaseController;
use App\Http\Controllers\Api\StageController;
use App\Http\Controllers\Api\LotController;

// Proyectos
Route::apiResource('projects', ProjectController::class);

// Fases
Route::apiResource('phases', PhaseController::class);

// Etapas
Route::apiResource('stages', StageController::class);

// Lotes
Route::apiResource('lots', LotController::class);

// routes/api.php
Route::get('/masterplan/map', [LotController::class, 'map']);


Route::post('/lots/import', [LotController::class, 'import']);
Route::put('/lots/{lot}/status', [LotController::class, 'updateStatus']);
Route::post('/lots/{lot}/chepina', [LotController::class, 'uploadChepina']);
