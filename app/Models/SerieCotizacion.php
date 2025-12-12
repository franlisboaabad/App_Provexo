<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SerieCotizacion extends Model
{
    use HasFactory;

    protected $table = 'series_cotizacion';

    protected $fillable = [
        'empresa_id',
        'serie',
        'descripcion',
        'activa',
        'es_principal',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'es_principal' => 'boolean',
    ];

    /**
     * Relación con empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Scope para series activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Scope para serie principal
     */
    public function scopePrincipal($query)
    {
        return $query->where('es_principal', true);
    }
}
