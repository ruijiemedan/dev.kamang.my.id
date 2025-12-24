<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MikrotikController;


Route::prefix('mikrotik')->group(function () {
Route::get('/interfaces', [MikrotikController::class, 'interfaces']);
Route::get('/ip-addresses', [MikrotikController::class, 'ipAddresses']);
Route::get('/neighbours', [MikrotikController::class, 'neighbours']);
Route::get('/traffic/{interface}', [MikrotikController::class, 'traffic']);
});
