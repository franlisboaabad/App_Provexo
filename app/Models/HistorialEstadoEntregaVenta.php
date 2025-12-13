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
     * Obtener texto del estado de entrega (usa método centralizado del Model Venta)
     */
    public function getEstadoEntregaTextoAttribute(): string
    {
        return Venta::getTextoEstadoEntregaCliente($this->estado_entrega);
    }
}
