<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DesarrollosController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Http\Middleware\AdminMiddleware;

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
            ->name('lots.configurator');

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
});

// =========================
// API Endpoints
// =========================
Route::get('/api/projects/{id}/phases', [DesarrollosController::class, 'getPhases']);
Route::get('/api/projects/{project}/phases/{phase}/stages', [DesarrollosController::class, 'getStages']);

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
