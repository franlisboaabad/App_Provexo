<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresas';

    protected $fillable = [
        'razon_social',
        'nombre_comercial',
        'ruc',
        'direccion',
        'ciudad',
        'provincia',
        'distrito',
        'telefono',
        'celular',
        'email',
        'web',
        'representante_legal',
        'tipo_empresa',
        'descripcion',
        'logo',
        'activo',
        'es_principal',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'es_principal' => 'boolean',
    ];

    /**
     * Scope para empresas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para empresa principal
     */
    public function scopePrincipal($query)
    {
        return $query->where('es_principal', true);
    }

    /**
     * Relación con series de cotización
     */
    public function seriesCotizacion(): HasMany
    {
        return $this->hasMany(SerieCotizacion::class);
    }

    /**
     * Relación con cuentas bancarias
     */
    public function cuentasBancarias(): HasMany
    {
        return $this->hasMany(CuentaBancaria::class);
    }

    /**
     * Obtener serie principal de cotización
     */
    public function seriePrincipal()
    {
        return $this->hasOne(SerieCotizacion::class)->where('es_principal', true);
    }

    /**
     * Obtener cuenta bancaria principal
     */
    public function cuentaPrincipal()
    {
        return $this->hasOne(CuentaBancaria::class)->where('es_principal', true);
    }

    /**
     * Obtener dirección completa
     */
    public function getDireccionCompletaAttribute()
    {
        $partes = array_filter([
            $this->direccion,
            $this->distrito,
            $this->provincia,
            $this->ciudad,
        ]);

        return implode(', ', $partes);
    }

    /**
     * Obtener el nombre a mostrar (nombre comercial o razón social)
     */
    public function getNombreMostrarAttribute()
    {
        return $this->nombre_comercial ?? $this->razon_social;
    }

}
