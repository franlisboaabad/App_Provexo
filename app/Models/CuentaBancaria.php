<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuentaBancaria extends Model
{
    use HasFactory;

    protected $table = 'cuentas_bancarias';

    protected $fillable = [
        'empresa_id',
        'banco',
        'tipo_cuenta',
        'numero_cuenta',
        'numero_cuenta_interbancario',
        'moneda_cuenta',
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
     * Scope para cuentas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Scope para cuenta principal
     */
    public function scopePrincipal($query)
    {
        return $query->where('es_principal', true);
    }

    /**
     * Obtener información de cuenta bancaria formateada
     */
    public function getCuentaCompletaAttribute(): string
    {
        $partes = [];
        if ($this->banco) {
            $partes[] = $this->banco;
        }
        if ($this->tipo_cuenta) {
            $partes[] = $this->tipo_cuenta;
        }
        if ($this->numero_cuenta) {
            $partes[] = $this->numero_cuenta;
        }
        if ($this->moneda_cuenta) {
            $partes[] = $this->moneda_cuenta;
        }

        return implode(' - ', $partes);
    }

    /**
     * Obtener cuenta interbancaria formateada (con espacios)
     */
    public function getCciFormateadoAttribute(): ?string
    {
        if (!$this->numero_cuenta_interbancario) {
            return null;
        }

        // Formatear CCI con espacios cada 4 dígitos para mejor legibilidad
        $cci = preg_replace('/(\d{4})(?=\d)/', '$1 ', $this->numero_cuenta_interbancario);
        return $cci;
    }
}
