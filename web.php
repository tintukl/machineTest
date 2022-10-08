<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/deposit', [App\Http\Controllers\HomeController::class, 'deposit'])->name('deposit');
Route::get('/transfer', [App\Http\Controllers\HomeController::class, 'transfer'])->name('transfer');
Route::get('/withdraw', [App\Http\Controllers\HomeController::class, 'withdraw'])->name('withdraw');
Route::get('/statement', [App\Http\Controllers\HomeController::class, 'statement'])->name('statement');
Route::post('/acc-deposit', [App\Http\Controllers\HomeController::class, 'depositMoney'])->name('acc-deposit');
Route::get('/acc-transfer', [App\Http\Controllers\HomeController::class, 'transferMoney'])->name('acc-transfer');
Route::get('/acc-withdraw', [App\Http\Controllers\HomeController::class, 'withdrawMoney'])->name('acc-withdraw');
Route::get('/acc-statement', [App\Http\Controllers\HomeController::class, 'moneyStatement'])->name('acc-statement');
