<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionDocumentosCotizacion extends Model
{
    use HasFactory;

    protected $table = 'configuracion_documentos_cotizacion';

    protected $fillable = [
        'observaciones',
        'condiciones_pago',
    ];

    /**
     * Obtener la configuración única (solo hay un registro)
     */
    public static function obtenerConfiguracion()
    {
        return self::first() ?? self::create([
            'observaciones' => '<ul>
                <li>Esta cotización tiene una validez de 30 días calendario desde la fecha de emisión.</li>
                <li>Productos sujetos a stock, este documento solo fija los precios por treinta días, pero no garantiza la disponibilidad de lo cotizado.</li>
                <li>En caso de que el cliente tenga facturas vencidas, no se procederá con el despacho de la orden de compra originada por esta cotización.</li>
                <li>En caso de que el cliente tenga facturas vencidas, no aplican descuentos, promociones y precios especiales de buen pagador.</li>
                <li>El tiempo de entrega que prevalece es el de este documento y no el de la OC del cliente, las entregas se realizarán como mínimo a partir de 1 día hábil después de confirmada la recepción de la OC del cliente por correo electrónico.</li>
            </ul>',
            'condiciones_pago' => '<ul>
                <li>50% de adelanto por transferencia bancaria para confirmar el pedido.</li>
                <li>50% restante al momento de confirmar el despacho.</li>
            </ul>',
        ]);
    }
}
