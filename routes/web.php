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
use App\Http\Controllers\Admin\DocumentoClienteController;
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
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // Administración de usuarios y roles
    Route::resource('usuarios', UserController::class)->names('admin.usuarios');
    Route::resource('roles', RoleController::class)->names('admin.roles');

    // Administración de proveedores y clientes
    Route::resource('proveedores', ProveedorController::class)->names('admin.proveedores');
    Route::resource('clientes', ClienteController::class)->names('admin.clientes');

    // Administración de documentos de clientes
    Route::resource('documentos-clientes', DocumentoClienteController::class)->names('admin.documentos-clientes')->parameters([
        'documentos-clientes' => 'id'
    ]);
    Route::get('documentos-clientes/{id}/download', [DocumentoClienteController::class, 'download'])->name('admin.documentos-clientes.download');

    // Endpoint AJAX para cargar cotizaciones de un cliente
    Route::get('api/cotizaciones/cliente/{cliente}', function($cliente) {
        $cotizaciones = \App\Models\Cotizacion::where('cliente_id', $cliente)
            ->orderBy('fecha_emision', 'desc')
            ->get(['id', 'numero_cotizacion', 'fecha_emision']);

        return response()->json($cotizaciones);
    })->name('api.cotizaciones.cliente');

    // Administración de productos
    Route::resource('productos', ProductoController::class)->names('admin.productos');
    Route::post('productos/import', [ProductoController::class, 'import'])->name('admin.productos.import');
    Route::post('productos/store-ajax', [ProductoController::class, 'storeAjax'])->name('admin.productos.store-ajax');
    Route::get('productos/{producto}/historial-precios', [ProductoController::class, 'historialPrecios'])->name('admin.productos.historial-precios');

    // Administración de cotizaciones
    Route::resource('cotizaciones', CotizacionController::class)->names('admin.cotizaciones');
    Route::get('cotizaciones/{cotizacione}/pdf', [CotizacionController::class, 'pdf'])->name('admin.cotizaciones.pdf');
    Route::get('cotizaciones/{cotizacione}/publica', [CotizacionController::class, 'publica'])->name('admin.cotizaciones.publica');
    Route::post('cotizaciones/{cotizacione}/enviar-email', [CotizacionController::class, 'enviarEmail'])->name('admin.cotizaciones.enviar-email');
    Route::post('cotizaciones/{cotizacione}/cambiar-estado', [CotizacionController::class, 'cambiarEstado'])->name('admin.cotizaciones.cambiar-estado');

    // Administración de empresas
    Route::resource('empresas', EmpresaController::class)->names('admin.empresas');
    
    // Rutas helper para acceso directo a empresa principal
    Route::get('empresa-principal', function() {
        $empresa = \App\Models\Empresa::where('es_principal', true)->first();
        if (!$empresa) {
            $empresa = \App\Models\Empresa::first();
        }
        if ($empresa) {
            return redirect()->route('admin.empresas.show', $empresa);
        }
        return redirect()->route('admin.empresas.index')
            ->with('warning', 'No hay empresas registradas. Por favor, cree una empresa primero.');
    })->name('admin.empresa.principal');

    // Series de cotización (nested dentro de empresas)
    Route::post('empresas/{empresa}/series', [SerieCotizacionController::class, 'store'])->name('admin.series.store');
    Route::put('series/{serieCotizacion}', [SerieCotizacionController::class, 'update'])->name('admin.series.update');
    Route::delete('series/{serieCotizacion}', [SerieCotizacionController::class, 'destroy'])->name('admin.series.destroy');

    // Cuentas bancarias (nested dentro de empresas)
    Route::post('empresas/{empresa}/cuentas', [CuentaBancariaController::class, 'store'])->name('admin.cuentas.store');
    Route::put('cuentas/{cuentaBancaria}', [CuentaBancariaController::class, 'update'])->name('admin.cuentas.update');
    Route::delete('cuentas/{cuentaBancaria}', [CuentaBancariaController::class, 'destroy'])->name('admin.cuentas.destroy');

    // Configuración de documentos de cotización
    Route::get('configuracion-documentos-cotizacion/edit', [\App\Http\Controllers\Admin\ConfiguracionDocumentosCotizacionController::class, 'edit'])->name('admin.configuracion-documentos.edit');
    Route::put('configuracion-documentos-cotizacion', [\App\Http\Controllers\Admin\ConfiguracionDocumentosCotizacionController::class, 'update'])->name('admin.configuracion-documentos.update');
});




require __DIR__ . '/auth.php';
