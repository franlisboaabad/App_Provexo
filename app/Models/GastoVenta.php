<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GastoVenta extends Model
{
    use HasFactory;

    protected $table = 'gastos_venta';

    protected $fillable = [
        'venta_id',
        'descripcion',
        'monto',
        'fecha',
        'observaciones',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha' => 'date',
    ];

    /**
     * Relación con Venta
     */
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }
}
