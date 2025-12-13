<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $fillable = [
        'cotizacion_id',
        'monto_vendido',
        'nota',
        'estado_pedido',
        'estado_entrega',
        'adelanto',
        'monto_transporte',
        'nombre_transporte',
        'margen_bruto_con_transporte',
        'margen_neto',
        'codigo_seguimiento',
        'direccion_entrega',
        'distrito',
        'provincia',
        'ciudad',
        'referencia',
        'codigo_postal',
    ];

    protected $casts = [
        'monto_vendido' => 'decimal:2',
        'adelanto' => 'decimal:2',
        'monto_transporte' => 'decimal:2',
        'margen_bruto_con_transporte' => 'decimal:2',
        'margen_neto' => 'decimal:2',
    ];

    /**
     * Relación con Cotización
     */
    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(Cotizacion::class);
    }

    /**
     * Relación con Gastos
     */
    public function gastos(): HasMany
    {
        return $this->hasMany(GastoVenta::class);
    }

    /**
     * Relación con Historial de Estados de Entrega
     */
    public function historialEstadosEntrega(): HasMany
    {
        return $this->hasMany(HistorialEstadoEntregaVenta::class)->orderBy('created_at', 'asc');
    }

    /**
     * Obtener total de gastos
     */
    public function getTotalGastosAttribute(): float
    {
        return $this->gastos->sum('monto');
    }

    /**
     * Calcular monto restante
     */
    public function getRestanteAttribute(): float
    {
        return max(0, $this->monto_vendido - $this->adelanto);
    }

    /**
     * Calcular márgenes (bruto y neto)
     * margen_bruto = monto_vendido - (costo_productos + total_gastos)
     * margen_neto = margen_bruto - total_gastos
     *
     * Nota: Según la especificación, margen_neto resta los gastos nuevamente del margen_bruto
     */
    public function calcularMargenes(): array
    {
        // Calcular costo total de productos usando precio_base_cotizacion
        $costoProductos = $this->cotizacion->productos->sum(function($productoCotizacion) {
            // Usar precio_base_cotizacion si existe, sino precio_base del producto
            $precioBase = $productoCotizacion->precio_base_cotizacion ?? $productoCotizacion->producto->precio_base ?? 0;
            return $precioBase * $productoCotizacion->cantidad;
        });

        // Calcular total de gastos
        $totalGastos = $this->total_gastos;

        // Calcular margen bruto: monto_vendido - (costo_productos + total_gastos)
        $margenBruto = $this->monto_vendido - ($costoProductos + $totalGastos);

        // Calcular margen neto: margen_bruto - total_gastos
        // Esto resulta en: monto_vendido - costo_productos - 2*total_gastos
        $margenNeto = $margenBruto - $totalGastos;

        // Actualizar en la base de datos
        $this->update([
            'margen_bruto_con_transporte' => $margenBruto,
            'margen_neto' => $margenNeto
        ]);

        return [
            'margen_bruto' => $margenBruto,
            'margen_neto' => $margenNeto,
            'total_gastos' => $totalGastos,
            'costo_productos' => $costoProductos
        ];
    }

    /**
     * Calcular margen bruto con transporte (método legacy - mantener para compatibilidad)
     * @deprecated Usar calcularMargenes() en su lugar
     */
    public function calcularMargenBruto(): float
    {
        $margenes = $this->calcularMargenes();
        return $margenes['margen_bruto'];
    }

    /**
     * Scope para pedidos pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado_pedido', 'pendiente');
    }

    /**
     * Scope para pedidos entregados
     */
    public function scopeEntregados($query)
    {
        return $query->where('estado_pedido', 'entregado');
    }

    /**
     * ============================================
     * MÉTODOS ESTÁTICOS PARA ESTADOS DE ENTREGA
     * Centralizados para facilitar mantenimiento
     * ============================================
     */

    /**
     * Obtener configuración completa de estados de entrega
     * Este es el único lugar donde se definen los estados
     *
     * @return array Array con la configuración de cada estado
     */
    public static function getConfiguracionEstadosEntrega(): array
    {
        return [
            'Solicitud_recibida' => [
                'texto' => 'Solicitud Recibida',
                'texto_cliente' => 'Recibimos tu pedido',
                'badge_class' => 'secondary',
                'icono' => 'fa-inbox',
            ],
            'En_preparación' => [
                'texto' => 'En Preparación',
                'texto_cliente' => 'Preparando tu pedido',
                'badge_class' => 'primary',
                'icono' => 'fa-box',
            ],
            'En_transito' => [
                'texto' => 'En Tránsito',
                'texto_cliente' => 'En tránsito',
                'badge_class' => 'warning',
                'icono' => 'fa-truck',
            ],
            'En_reparto' => [
                'texto' => 'En Reparto',
                'texto_cliente' => 'En reparto',
                'badge_class' => 'info',
                'icono' => 'fa-route',
            ],
            'Entregado' => [
                'texto' => 'Entregado',
                'texto_cliente' => 'Pedido entregado',
                'badge_class' => 'success',
                'icono' => 'fa-check-double',
            ],
        ];
    }

    /**
     * Obtener string de estados separados por comas (para validaciones)
     *
     * @return string
     */
    public static function getEstadosEntregaString(): string
    {
        return implode(',', array_keys(self::getConfiguracionEstadosEntrega()));
    }

    /**
     * Obtener estado por defecto
     *
     * @return string
     */
    public static function getEstadoEntregaDefault(): string
    {
        return 'Solicitud_recibida';
    }

    /**
     * Obtener texto del estado (para admin)
     *
     * @param string|null $estado
     * @return string
     */
    public static function getTextoEstadoEntrega(?string $estado): string
    {
        if (!$estado) {
            return '';
        }

        $config = self::getConfiguracionEstadosEntrega();
        return $config[$estado]['texto'] ?? $estado;
    }

    /**
     * Obtener texto del estado para cliente
     *
     * @param string|null $estado
     * @return string
     */
    public static function getTextoEstadoEntregaCliente(?string $estado): string
    {
        if (!$estado) {
            return '';
        }

        $config = self::getConfiguracionEstadosEntrega();
        return $config[$estado]['texto_cliente'] ?? $estado;
    }

    /**
     * Obtener clase CSS para badge del estado
     *
     * @param string|null $estado
     * @return string
     */
    public static function getBadgeClassEstadoEntrega(?string $estado): string
    {
        if (!$estado) {
            return 'secondary';
        }

        $config = self::getConfiguracionEstadosEntrega();
        return $config[$estado]['badge_class'] ?? 'secondary';
    }


    /**
     * Obtener array de estados para timeline (frontend)
     *
     * @return array
     */
    public static function getEstadosEntregaParaTimeline(): array
    {
        $config = self::getConfiguracionEstadosEntrega();
        $timeline = [];

        foreach ($config as $valor => $data) {
            $timeline[] = [
                'valor' => $valor,
                'texto' => $data['texto_cliente'],
                'icono' => $data['icono'],
            ];
        }

        return $timeline;
    }

    /**
     * Obtener array de estados con texto para dropdowns
     *
     * @return array ['valor' => 'Texto']
     */
    public static function getEstadosEntregaParaSelect(): array
    {
        $config = self::getConfiguracionEstadosEntrega();
        $select = [];

        foreach ($config as $valor => $data) {
            $select[$valor] = $data['texto'];
        }

        return $select;
    }

    /**
     * ============================================
     * ACCESSORS (usan los métodos estáticos)
     * ============================================
     */

    /**
     * Obtener texto del estado de entrega
     */
    public function getEstadoEntregaTextoAttribute(): string
    {
        return self::getTextoEstadoEntrega($this->estado_entrega);
    }

    /**
     * Obtener clase CSS para el badge del estado de entrega
     */
    public function getEstadoEntregaBadgeClassAttribute(): string
    {
        return self::getBadgeClassEstadoEntrega($this->estado_entrega);
    }

    /**
     * Generar código de seguimiento automático
     * Formato: ORD-YYYY-NNNN (ej: ORD-2025-0001)
     */
    public static function generarCodigoSeguimiento(): string
    {
        $prefijo = 'ORD';
        $anio = date('Y');

        // Buscar el último código de seguimiento del año actual
        $ultimaVenta = self::where('codigo_seguimiento', 'like', $prefijo . '-' . $anio . '-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($ultimaVenta && $ultimaVenta->codigo_seguimiento) {
            // Extraer el número del último código
            $partes = explode('-', $ultimaVenta->codigo_seguimiento);
            $ultimoNumero = (int)end($partes);
            $numero = $ultimoNumero + 1;
        } else {
            // Si no hay ventas con código, empezar desde 1
            $numero = 1;
        }

        return $prefijo . '-' . $anio . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}
