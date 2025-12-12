<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialPrecioProducto extends Model
{
    use HasFactory;

    protected $table = 'historial_precios_productos';

    protected $fillable = [
        'producto_id',
        'cotizacion_id',
        'usuario_id',
        'precio_base_anterior',
        'precio_base_nuevo',
        'observaciones',
    ];

    protected $casts = [
        'precio_base_anterior' => 'decimal:2',
        'precio_base_nuevo' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con Producto
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Relación con Cotizacion
     */
    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(Cotizacion::class);
    }

    /**
     * Relación con Usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Scope para obtener historial de un producto
     */
    public function scopePorProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    /**
     * Scope para obtener historial de una cotización
     */
    public function scopePorCotizacion($query, $cotizacionId)
    {
        return $query->where('cotizacion_id', $cotizacionId);
    }

    /**
     * Scope para ordenar por fecha más reciente
     */
    public function scopeRecientes($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}

