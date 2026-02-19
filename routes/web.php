<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PollingUnitController;
use App\Http\Controllers\LgaResultController;
use App\Http\Controllers\NewResultController;
use App\Http\Controllers\AjaxController;

// Home redirects to Q1
Route::get('/', fn() => redirect()->route('polling-unit.index'));

// Q1 - View Polling Unit Results
Route::get('/polling-unit', [PollingUnitController::class, 'index'])->name('polling-unit.index');

// Q2 - View LGA Summed Results
Route::get('/lga-results', [LgaResultController::class, 'index'])->name('lga-results.index');

// Q3 - Add New Polling Unit Results
Route::get('/new-result', [NewResultController::class, 'index'])->name('new-result.index');
Route::post('/new-result', [NewResultController::class, 'store'])->name('new-result.store');

// AJAX endpoints for cascading dropdowns
Route::get('/ajax/lgas/{stateId}', [AjaxController::class, 'getLgas']);
Route::get('/ajax/wards/{lgaId}', [AjaxController::class, 'getWards']);
Route::get('/ajax/polling-units/{wardId}', [AjaxController::class, 'getPollingUnits']);
Route::get('/ajax/pu-results/{pollingUnitId}', [AjaxController::class, 'getPuResults']);
Route::get('/ajax/lga-results/{lgaId}', [AjaxController::class, 'getLgaResults']);
Route::get('/ajax/parties', [AjaxController::class, 'getParties']);
