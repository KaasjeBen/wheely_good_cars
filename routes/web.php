<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProviderCarController;
use App\Http\Controllers\AdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/cars');

Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
Route::get('/cars/{car}', [CarController::class, 'show'])->name('cars.show');

Route::middleware('guest')->group(function () {
	Route::get('/register', [AuthController::class, 'showRegister'])->name('register.show');
	Route::post('/register', [AuthController::class, 'register'])->name('register.perform');
	Route::get('/login', [AuthController::class, 'showLogin'])->name('login.show');
	Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->prefix('provider')->name('provider.')->group(function () {
	Route::get('/cars', [ProviderCarController::class, 'index'])->name('cars.index');
	Route::get('/cars/create', [ProviderCarController::class, 'create'])->name('cars.create');
	Route::post('/cars', [ProviderCarController::class, 'store'])->name('cars.store');
	Route::get('/cars/{car}/edit', [ProviderCarController::class, 'edit'])->name('cars.edit');
	Route::patch('/cars/{car}', [ProviderCarController::class, 'update'])->name('cars.update');
	Route::delete('/cars/{car}', [ProviderCarController::class, 'destroy'])->name('cars.destroy');
	Route::patch('/cars/{car}/status', [ProviderCarController::class, 'updateStatus'])->name('cars.status');
	Route::get('/cars/{car}/pdf', [ProviderCarController::class, 'pdf'])->name('cars.pdf');
	Route::get('/rdw', [ProviderCarController::class, 'rdw'])->name('rdw.lookup');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
	Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');
	Route::get('/tag-usage', [AdminDashboardController::class, 'tagUsage'])->name('tag-usage');
	Route::get('/suspicious', [AdminDashboardController::class, 'suspicious'])->name('suspicious');
	Route::get('/metrics', [AdminDashboardController::class, 'metrics'])->name('metrics');
});
