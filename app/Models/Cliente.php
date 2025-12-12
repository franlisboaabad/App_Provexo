<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'user_id',
        'celular',
        'empresa',
        'ruc',
    ];

    /**
     * Relación con User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con Cotizaciones
     */
    public function cotizaciones(): HasMany
    {
        return $this->hasMany(Cotizacion::class);
    }

    /**
     * Relación con Documentos
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(DocumentoCliente::class);
    }
}
