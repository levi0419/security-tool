<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ScanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Route::get('/', function () {
//     return view('index');
// });

Route::get('/', [ScanController::class, 'index']);
Route::get('/scan', [ScanController::class, 'scan']);
Route::get('/results/{id}', [ScanController::class, 'show']);

Route::post('/scan-test', [ScanController::class, 'scanTest']);
Route::get('/download-results', [ScanController::class, 'downloadResults'])->name('download.results');
