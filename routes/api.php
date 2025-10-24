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
