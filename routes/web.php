<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DesarrollosController;
use App\Http\Controllers\API\AdaraController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\View\PhaseViewController;
use App\Http\Controllers\View\StageViewController;
use App\Http\Controllers\View\LotViewController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanciamientoController;

use App\Http\Controllers\View\ProjectViewController;
Route::view('/', 'login');

// =========================
// Autenticación con Google
// =========================
Route::get('/google-auth/redirect', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/google-auth/callback', function () {
    $user_google = Socialite::driver('google')
        ->stateless()
        ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
        ->user();

    $user = User::updateOrCreate(
        ['google_id' => $user_google->id],
        [
            'name'  => $user_google->name,
            'email' => $user_google->email,
        ]
    );

    Auth::login($user);

    return redirect()->intended('/desarrollos');
});


Route::get('/unauthorized', function () {
    return view('unauthorized'); // <-- aquí apunta tu blade
})->name('unauthorized');

// =========================
// Rutas del panel admin
// =========================
Route::middleware(['auth', AdminMiddleware::class])
        ->group(function () {

        Route::get('/admin', [DesarrollosController::class, 'admin'])
            ->name('admin.index');

        Route::get('/consulta', [DesarrollosController::class, 'form'])
            ->name('lots.form');

        // Mandar la solicitud
        Route::post('/lots/fetch', [DesarrollosController::class, 'fetch'])
            ->name('lots.fetch');

        Route::get('/lots/{lot}/configurator', [DesarrollosController::class, 'configurator'])
            ->name('desarrollos.configurator');

        Route::post('/lots/{lot}/save-polygon', [DesarrollosController::class, 'savePolygonInfo'])
            ->name('lots.savePolygonInfo');

        // Leads
        Route::post('/leads', [LeadController::class, 'store'])
            ->name('leads.store');

        // Lotes
        Route::post('/Savelotes', [LoteController::class, 'store'])
            ->name('lotes.store');

        // =========================
        // CRUD Desarrollos
        // =========================
        Route::get('/desarrollos/create', [DesarrollosController::class, 'create'])
            ->name('desarrollos.create');

        Route::get('/desarrollos', [DesarrollosController::class, 'index'])
            ->name('desarrollos.index');

        Route::post('/desarrollos', [DesarrollosController::class, 'store'])
            ->name('desarrollo.store');

        Route::get('/desarrollos/{desarrollo}/edit', [DesarrollosController::class, 'edit'])
            ->name('desarrollos.edit');

        Route::put('/desarrollos/{desarrollo}', [DesarrollosController::class, 'update'])
            ->name('desarrollos.update');

        Route::delete('/desarrollos/{desarrollo}', [DesarrollosController::class, 'destroy'])
            ->name('desarrollos.destroy');

        //Adara Catálogo
        Route::get('/projects', [ProjectViewController::class, 'index'])->name('projects.index');
        Route::get('/phases', [PhaseViewController::class, 'index'])->name('phases.index');
        Route::get('/stages', [StageViewController::class, 'index'])->name('stages.index');
        Route::get('/lotsAdara', [LotViewController::class, 'index'])->name('lots.index');

        //dashboard
        Route::get('/dashboards', [DashboardController::class, 'index'])->name('dashboards.index');
        Route::post('/dashboards/data', [DashboardController::class, 'getData'])->name('dashboards.data');

        //Financiamiento
        Route::get('/financiamientos', [FinanciamientoController::class, 'index'])->name('financiamientos.index');
        Route::get('/financiamientos/data', [FinanciamientoController::class, 'data'])->name('financiamientos.data');
        Route::post('/financiamientos', [FinanciamientoController::class, 'store'])->name('financiamientos.store');
        Route::get('/financiamientos/{financiamiento}/edit', [FinanciamientoController::class, 'edit'])->name('financiamientos.edit');
        Route::put('/financiamientos/{financiamiento}', [FinanciamientoController::class, 'update'])->name('financiamientos.update');
        Route::delete('/financiamientos/{financiamiento}', [FinanciamientoController::class, 'destroy'])->name('financiamientos.destroy');
        // Página para crear financiamiento
        Route::get('/financiamientos/create', [FinanciamientoController::class, 'create'])->name('financiamientos.create');
    });

// =========================
// API Endpoints Adara
// =========================
Route::prefix('api')->group(function () {
    Route::get('/projects/{id}/phases', [AdaraController::class, 'phases']);
    Route::get('/projects/{project}/phases/{phase}/stages', [AdaraController::class, 'stages']);
    Route::get('/projects/{project}/phases/{phase}/stages/{stage}/lots', [AdaraController::class, 'lots']);
});


// =========================
// Reports
// =========================
Route::get('/reports/generate', [ReportController::class, 'generate'])
     ->name('reports.generate');

Route::get('/reports/{report}/download', [ReportController::class, 'download'])
    ->name('reports.download');

Route::get('/reports', [ReportController::class, 'index'])
    ->name('reports.index');
    
Route::get('/iframe/{lot}/', [DesarrollosController::class, 'iframe'])
    ->name('lots.iframe');
    
// =========================
// Auth Routes
// =========================
require __DIR__ . '/auth.php';
