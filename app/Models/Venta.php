<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $fillable = [
        'cotizacion_id',
        'monto_vendido',
        'nota',
        'estado_pedido',
        'adelanto',
        'monto_transporte',
        'nombre_transporte',
        'margen_bruto_con_transporte',
    ];

    protected $casts = [
        'monto_vendido' => 'decimal:2',
        'adelanto' => 'decimal:2',
        'monto_transporte' => 'decimal:2',
        'margen_bruto_con_transporte' => 'decimal:2',
    ];

    /**
     * Relación con Cotización
     */
    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(Cotizacion::class);
    }

    /**
     * Calcular monto restante
     */
    public function getRestanteAttribute(): float
    {
        return max(0, $this->monto_vendido - $this->adelanto);
    }

    /**
     * Calcular margen bruto con transporte
     * margen = monto_vendido - (costo_productos + monto_transporte)
     */
    public function calcularMargenBruto(): float
    {
        // Calcular costo total de productos usando precio_base_cotizacion
        $costoProductos = $this->cotizacion->productos->sum(function($productoCotizacion) {
            // Usar precio_base_cotizacion si existe, sino precio_base del producto
            $precioBase = $productoCotizacion->precio_base_cotizacion ?? $productoCotizacion->producto->precio_base ?? 0;
            return $precioBase * $productoCotizacion->cantidad;
        });

        $margen = $this->monto_vendido - ($costoProductos + $this->monto_transporte);

        // Actualizar en la base de datos
        $this->update(['margen_bruto_con_transporte' => $margen]);

        return $margen;
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
}
