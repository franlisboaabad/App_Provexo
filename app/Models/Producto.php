<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'proveedor_id',
        'codigo_producto',
        'descripcion',
        'precio_base',
        'precio_venta',
        'impuesto',
        'stock',
        'unidad_medida',
        'imagen',
        'categoria',
        'marca',
        'activo',
    ];

    /**
     * Casts para tipos de datos
     */
    protected $casts = [
        'precio_base' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'stock' => 'integer',
        'activo' => 'boolean',
    ];

    /**
     * Relación con Proveedor
     */
    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    /**
     * Scope para productos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para productos con stock
     */
    public function scopeConStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope para buscar por código
     */
    public function scopePorCodigo($query, $codigo)
    {
        return $query->where('codigo_producto', $codigo);
    }
}
