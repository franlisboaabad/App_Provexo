<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cotizacion;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:admin.reportes.index')->only('dashboard');
    }

    /**
     * Dashboard principal de reportes con gráficos
     */
    public function dashboard(Request $request)
    {
        // Obtener filtros de fecha (por defecto últimos 6 meses)
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->subMonths(6)->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));

        // Datos para gráfico de ventas mensuales
        $ventasMensuales = $this->obtenerVentasMensuales($fechaInicio, $fechaFin);

        // Datos para gráfico de estados de cotizaciones
        $estadosCotizaciones = $this->obtenerEstadosCotizaciones();

        // Datos para gráfico de top clientes
        $topClientes = $this->obtenerTopClientes($fechaInicio, $fechaFin, 5);

        return view('admin.reportes.dashboard', compact(
            'ventasMensuales',
            'estadosCotizaciones',
            'topClientes',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Obtener datos de ventas mensuales
     */
    private function obtenerVentasMensuales($fechaInicio, $fechaFin)
    {
        $ventas = Venta::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'),
                DB::raw('SUM(monto_vendido) as total_vendido'),
                DB::raw('SUM(adelanto) as total_adelanto'),
                DB::raw('SUM(monto_vendido - adelanto) as total_restante')
            )
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        $meses = [];
        $montosVendidos = [];
        $adelantos = [];
        $restantes = [];

        foreach ($ventas as $venta) {
            $fecha = Carbon::createFromFormat('Y-m', $venta->mes);
            $meses[] = $fecha->format('M Y');
            $montosVendidos[] = (float) $venta->total_vendido;
            $adelantos[] = (float) $venta->total_adelanto;
            $restantes[] = (float) $venta->total_restante;
        }

        return [
            'meses' => $meses,
            'montos_vendidos' => $montosVendidos,
            'adelantos' => $adelantos,
            'restantes' => $restantes
        ];
    }

    /**
     * Obtener distribución de estados de cotizaciones
     */
    private function obtenerEstadosCotizaciones()
    {
        $estados = Cotizacion::select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->get();

        $labels = [];
        $datos = [];
        $colores = [];

        $coloresMap = [
            'pendiente' => '#17a2b8',    // info
            'aprobada' => '#28a745',     // success
            'rechazada' => '#dc3545',    // danger
            'vencida' => '#ffc107',      // warning
            'ganado' => '#20c997',       // teal
            'perdido' => '#6c757d'       // secondary
        ];

        foreach ($estados as $estado) {
            $labels[] = ucfirst($estado->estado);
            $datos[] = (int) $estado->total;
            $colores[] = $coloresMap[$estado->estado] ?? '#6c757d'; // default secondary
        }

        return [
            'labels' => $labels,
            'datos' => $datos,
            'colores' => $colores
        ];
    }

    /**
     * Obtener top clientes por monto vendido
     */
    private function obtenerTopClientes($fechaInicio, $fechaFin, $limite = 5)
    {
        $clientes = Venta::whereBetween('ventas.created_at', [$fechaInicio, $fechaFin])
            ->join('cotizaciones', 'ventas.cotizacion_id', '=', 'cotizaciones.id')
            ->join('clientes', 'cotizaciones.cliente_id', '=', 'clientes.id')
            ->join('users', 'clientes.user_id', '=', 'users.id')
            ->select(
                'users.name as nombre_cliente',
                DB::raw('SUM(ventas.monto_vendido) as total_vendido'),
                DB::raw('COUNT(ventas.id) as total_ventas')
            )
            ->groupBy('clientes.id', 'users.name')
            ->orderBy('total_vendido', 'desc')
            ->limit($limite)
            ->get();

        $nombres = [];
        $montos = [];

        foreach ($clientes as $cliente) {
            $nombres[] = $cliente->nombre_cliente;
            $montos[] = (float) $cliente->total_vendido;
        }

        return [
            'nombres' => $nombres,
            'montos' => $montos
        ];
    }
}
