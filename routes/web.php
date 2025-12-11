<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('auth.login');
});


Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [Dashboard::class, 'home'])->name('dashboard');

    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // Administración de usuarios y roles
    Route::resource('usuarios', UserController::class)->names('admin.usuarios');
    Route::resource('roles', RoleController::class)->names('admin.roles');

    // Administración de proveedores y clientes
    Route::resource('proveedores', ProveedorController::class)->names('admin.proveedores');
    Route::resource('clientes', ClienteController::class)->names('admin.clientes');

    // Administración de productos
    Route::resource('productos', ProductoController::class)->names('admin.productos');
});




require __DIR__ . '/auth.php';
