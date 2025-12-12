<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'cotizaciones';

    protected $fillable = [
        'cliente_id',
        'numero_cotizacion',
        'fecha_emision',
        'fecha_vencimiento',
        'estado',
        'subtotal',
        'descuento',
        'impuesto_total',
        'total',
        'observaciones',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'impuesto_total' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Relación con Cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relación con productos de la cotización
     */
    public function productos(): HasMany
    {
        return $this->hasMany(CotizacionProducto::class);
    }

    /**
     * Scope para cotizaciones pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope para cotizaciones aprobadas
     */
    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobada');
    }

    /**
     * Generar número de cotización automático
     */
    public static function generarNumeroCotizacion($serieId = null): string
    {
        // Si se proporciona un ID de serie, usarlo directamente
        if ($serieId) {
            $serieCotizacion = \App\Models\SerieCotizacion::find($serieId);
            if ($serieCotizacion && $serieCotizacion->activa) {
                $serie = $serieCotizacion->serie;
            } else {
                $serie = 'COT';
            }
        } else {
            // Obtener empresa principal y su serie principal
            $empresa = \App\Models\Empresa::where('es_principal', true)->first();
            if ($empresa) {
                $seriePrincipal = $empresa->seriePrincipal;
                $serie = $seriePrincipal ? $seriePrincipal->serie : 'COT';
            } else {
                $serie = 'COT';
            }
        }

        $anio = date('Y');
        $ultimaCotizacion = self::where('numero_cotizacion', 'like', $serie . '-' . $anio . '-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($ultimaCotizacion) {
            // Extraer el número de la última cotización
            $partes = explode('-', $ultimaCotizacion->numero_cotizacion);
            $numero = (int)end($partes) + 1;
        } else {
            $numero = 1;
        }

        return $serie . '-' . $anio . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}
