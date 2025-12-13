<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuracion_documentos_cotizacion', function (Blueprint $table) {
            $table->id();
            $table->text('observaciones')->nullable()->comment('Observaciones por defecto para las cotizaciones');
            $table->text('condiciones_pago')->nullable()->comment('Condiciones de pago por defecto para las cotizaciones');
            $table->timestamps();
        });

        // Insertar registro inicial con valores por defecto
        DB::table('configuracion_documentos_cotizacion')->insert([
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
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuracion_documentos_cotizacion');
    }
};
