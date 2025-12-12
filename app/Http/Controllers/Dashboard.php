<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cotizacion;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function home(Request $request)
    {
        $user = $request->user();

        // Si el usuario es Cliente, mostrar dashboard simplificado
        if ($user->hasRole('Cliente')) {
            $cliente = $user->cliente;
            
            if (!$cliente) {
                // Si no tiene perfil de cliente, redirigir al perfil
                return redirect()->route('profile.edit')
                    ->with('warning', 'Por favor completa tu perfil de cliente.');
            }

            // Contar solo las cotizaciones del cliente
            $totalCotizaciones = Cotizacion::where('cliente_id', $cliente->id)->count();
            $cotizacionesPendientes = Cotizacion::where('cliente_id', $cliente->id)
                ->where('estado', 'pendiente')
                ->count();

            return view('dashboard-cliente', compact(
                'totalCotizaciones',
                'cotizacionesPendientes'
            ));
        }

        // Dashboard completo para administradores
        // Verificar permiso
        if (!$user->can('admin.home')) {
            abort(403, 'No tienes permiso para acceder al dashboard.');
        }

        // Estadísticas principales
        $usuariosActivos = User::where('activo', true)->count();
        $cotizacionesPendientes = Cotizacion::pendientes()->count();
        $totalCotizaciones = Cotizacion::count();
        $totalProductos = Producto::activos()->count();

        // Actividad semanal (últimos 7 días)
        $actividadSemanal = [];
        $diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            $diaSemana = $diasSemana[$fecha->dayOfWeek];

            $actividadSemanal[] = [
                'dia' => $diaSemana,
                'cotizaciones' => Cotizacion::whereDate('created_at', $fecha->format('Y-m-d'))->count(),
                'productos' => Producto::whereDate('created_at', $fecha->format('Y-m-d'))->count(),
                'usuarios' => User::whereDate('created_at', $fecha->format('Y-m-d'))->count(),
            ];
        }

        // Cotizaciones recientes (últimas 5)
        $cotizacionesRecientes = Cotizacion::with('cliente.user')
            ->latest()
            ->limit(5)
            ->get();

        // Productos recientes (últimos 5)
        $productosRecientes = Producto::with('proveedor.user')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'usuariosActivos',
            'cotizacionesPendientes',
            'totalCotizaciones',
            'totalProductos',
            'actividadSemanal',
            'cotizacionesRecientes',
            'productosRecientes'
        ));
    }
}
