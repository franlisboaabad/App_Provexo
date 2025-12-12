<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CotizacionProducto extends Model
{
    use HasFactory;

    protected $table = 'cotizacion_productos';

    protected $fillable = [
        'cotizacion_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'precio_base_cotizacion',
        'descuento',
        'impuesto',
        'subtotal',
        'peso_unidad',
        'flete_tonelada',
        'margen_porcentaje',
        'flete_unitario',
        'costo_mas_flete',
        'total_kg',
        'margen_total',
        'flete_total',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'precio_base_cotizacion' => 'decimal:2',
        'descuento' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'peso_unidad' => 'decimal:4',
        'flete_tonelada' => 'decimal:2',
        'margen_porcentaje' => 'decimal:2',
        'flete_unitario' => 'decimal:4',
        'costo_mas_flete' => 'decimal:2',
        'total_kg' => 'decimal:4',
        'margen_total' => 'decimal:2',
        'flete_total' => 'decimal:2',
    ];

    /**
     * Relación con Cotizacion
     */
    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(Cotizacion::class);
    }

    /**
     * Relación con Producto
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Calcular subtotal
     */
    public function calcularSubtotal(): float
    {
        $precioConDescuento = $this->precio_unitario - ($this->precio_unitario * ($this->descuento / 100));
        $subtotalSinImpuesto = $precioConDescuento * $this->cantidad;
        $impuestoMonto = $subtotalSinImpuesto * ($this->impuesto / 100);

        return $subtotalSinImpuesto + $impuestoMonto;
    }
}
