<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\SerieCotizacionController;
use App\Http\Controllers\CuentaBancariaController;
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

// Ruta pública para ver cotización (sin autenticación)
Route::get('cotizacion/{token}', [CotizacionController::class, 'verPublica'])->name('cotizacion.publica');


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
    Route::post('productos/import', [ProductoController::class, 'import'])->name('admin.productos.import');

    // Administración de cotizaciones
    Route::resource('cotizaciones', CotizacionController::class)->names('admin.cotizaciones');
    Route::get('cotizaciones/{cotizacione}/pdf', [CotizacionController::class, 'pdf'])->name('admin.cotizaciones.pdf');
    Route::get('cotizaciones/{cotizacione}/publica', [CotizacionController::class, 'publica'])->name('admin.cotizaciones.publica');
    Route::post('cotizaciones/{cotizacione}/enviar-email', [CotizacionController::class, 'enviarEmail'])->name('admin.cotizaciones.enviar-email');

    // Administración de empresas
    Route::resource('empresas', EmpresaController::class)->names('admin.empresas');

    // Series de cotización (nested dentro de empresas)
    Route::post('empresas/{empresa}/series', [SerieCotizacionController::class, 'store'])->name('admin.series.store');
    Route::put('series/{serieCotizacion}', [SerieCotizacionController::class, 'update'])->name('admin.series.update');
    Route::delete('series/{serieCotizacion}', [SerieCotizacionController::class, 'destroy'])->name('admin.series.destroy');

    // Cuentas bancarias (nested dentro de empresas)
    Route::post('empresas/{empresa}/cuentas', [CuentaBancariaController::class, 'store'])->name('admin.cuentas.store');
    Route::put('cuentas/{cuentaBancaria}', [CuentaBancariaController::class, 'update'])->name('admin.cuentas.update');
    Route::delete('cuentas/{cuentaBancaria}', [CuentaBancariaController::class, 'destroy'])->name('admin.cuentas.destroy');
});




require __DIR__ . '/auth.php';
