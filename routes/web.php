<?php

use App\Inertia\ApiSupportControllers\ApiSupportObjectInfrastructureSelectController;
use App\Inertia\Incidents\Controllers\IncidentsInertiaController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['web'])
    ->prefix('app')
    ->name('app.')
    ->group(function () {
        Route::get('/incidents', [IncidentsInertiaController::class, 'index'])
            ->name('incidents.index');
        Route::get(
            '/api/object-infrastructures/select',
            ApiSupportObjectInfrastructureSelectController::class
        )->name('api.object-infrastructures.select');
    });
Route::get('/inertia-test', function () {
    return Inertia::render('Incidents/IndexIncidents');
});

