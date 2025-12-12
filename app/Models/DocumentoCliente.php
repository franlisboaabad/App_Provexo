<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoCliente extends Model
{
    use HasFactory;

    protected $table = 'documentos_clientes';

    protected $fillable = [
        'cliente_id',
        'cotizacion_id',
        'titulo',
        'nombre_archivo',
        'ruta_archivo',
        'tipo_documento',
        'numero_documento',
        'fecha_documento',
        'usuario_id',
        'observaciones',
        'activo',
    ];

    protected $casts = [
        'fecha_documento' => 'date',
        'activo' => 'boolean',
    ];

    /**
     * Relación con Cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relación con Cotización
     */
    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(Cotizacion::class);
    }

    /**
     * Relación con Usuario (quien subió el documento)
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
