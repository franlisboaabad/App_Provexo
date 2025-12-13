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
        'token_publico',
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
     * Relación con Documentos
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(DocumentoCliente::class);
    }

    /**
     * Relación con Venta (uno a uno)
     */
    public function venta()
    {
        return $this->hasOne(Venta::class);
    }

    /**
     * Verificar si la cotización tiene una venta asociada
     */
    public function tieneVenta(): bool
    {
        return $this->venta !== null;
    }

    /**
     * Verificar si la cotización está ganada
     */
    public function esGanada(): bool
    {
        return $this->estado === 'ganado';
    }

    /**
     * Verificar si la cotización está perdida
     */
    public function esPerdida(): bool
    {
        return $this->estado === 'perdido';
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
     * Scope para cotizaciones ganadas
     */
    public function scopeGanadas($query)
    {
        return $query->where('estado', 'ganado');
    }

    /**
     * Scope para cotizaciones perdidas
     */
    public function scopePerdidas($query)
    {
        return $query->where('estado', 'perdido');
    }

    /**
     * Generar número de cotización automático
     */
    public static function generarNumeroCotizacion($serieId = null): string
    {
        $serieCotizacion = null;
        $serie = 'COT';
        $correlativoInicial = 1;

        // Si se proporciona un ID de serie, usarlo directamente
        if ($serieId) {
            $serieCotizacion = \App\Models\SerieCotizacion::find($serieId);
            if ($serieCotizacion && $serieCotizacion->activa) {
                $serie = $serieCotizacion->serie;
                $correlativoInicial = $serieCotizacion->correlativo_inicial ?? 1;
            }
        } else {
            // Obtener empresa principal y su serie principal
            $empresa = \App\Models\Empresa::where('es_principal', true)->first();
            if ($empresa) {
                $seriePrincipal = $empresa->seriePrincipal;
                if ($seriePrincipal) {
                    $serieCotizacion = $seriePrincipal;
                    $serie = $seriePrincipal->serie;
                    $correlativoInicial = $seriePrincipal->correlativo_inicial ?? 1;
                }
            }
        }

        $anio = date('Y');
        $ultimaCotizacion = self::where('numero_cotizacion', 'like', $serie . '-' . $anio . '-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($ultimaCotizacion) {
            // Extraer el número de la última cotización
            $partes = explode('-', $ultimaCotizacion->numero_cotizacion);
            $ultimoNumero = (int)end($partes);
            // Usar el mayor entre el último número + 1 y el correlativo inicial
            $numero = max($ultimoNumero + 1, $correlativoInicial);
        } else {
            // Si no hay cotizaciones, usar el correlativo inicial
            $numero = $correlativoInicial;
        }

        return $serie . '-' . $anio . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}
