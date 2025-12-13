<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialEstadoEntregaVenta extends Model
{
    use HasFactory;

    protected $table = 'historial_estado_entrega_ventas';

    protected $fillable = [
        'venta_id',
        'estado_entrega',
        'usuario_id',
        'observaciones',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con Venta
     */
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    /**
     * Relación con Usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener texto del estado de entrega
     */
    public function getEstadoEntregaTextoAttribute(): string
    {
        $estados = [
            'registro_creado' => 'Recibimos tu pedido',
            'recogido' => 'Pedido confirmado',
            'en_bodega_origen' => 'Preparando producto',
            'salida_almacen' => 'Listo para enviar',
            'en_transito' => 'En tránsito',
            'en_reparto' => 'En reparto',
            'entregado' => 'Pedido entregado',
        ];

        return $estados[$this->estado_entrega] ?? $this->estado_entrega;
    }
}
