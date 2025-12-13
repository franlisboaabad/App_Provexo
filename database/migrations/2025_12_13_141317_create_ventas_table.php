<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cotizacion_id')->unique()->constrained('cotizaciones')->onDelete('cascade');
            $table->decimal('monto_vendido', 10, 2)->default(0)->comment('Monto final de la venta (puede diferir del cotizado)');
            $table->text('nota')->nullable()->comment('Observaciones o notas sobre la venta');
            $table->enum('estado_pedido', ['pendiente', 'en_proceso', 'entregado', 'cancelado'])->default('pendiente')->comment('Estado del pedido/entrega');
            $table->enum('estado_entrega', ['Solicitud_recibida','En_preparación','En_transito','En_reparto','Entregado'])->default('Solicitud_recibida')->comment('Estado del proceso de entrega');
            $table->decimal('adelanto', 10, 2)->default(0)->comment('Monto de adelanto recibido');
            $table->decimal('monto_transporte', 10, 2)->default(0)->comment('Costo de transporte');
            $table->string('nombre_transporte', 255)->nullable()->comment('Nombre de la empresa/transportista');
            $table->decimal('margen_bruto_con_transporte', 10, 2)->nullable()->comment('Margen bruto calculado con transporte');
            $table->decimal('margen_neto', 10, 2)->nullable()->comment('Margen neto = margen_bruto - total_gastos');
            $table->string('codigo_seguimiento', 100)->nullable()->unique()->comment('Código de seguimiento/ticket para rastrear la venta (autogenerado)');

            // Campos de entrega
            $table->text('direccion_entrega')->nullable()->comment('Dirección completa de entrega');
            $table->string('distrito', 100)->nullable()->comment('Distrito de entrega');
            $table->string('provincia', 100)->nullable()->comment('Provincia de entrega');
            $table->string('ciudad', 100)->nullable()->comment('Ciudad de entrega');
            $table->text('referencia')->nullable()->comment('Referencias adicionales del lugar de entrega');
            $table->string('codigo_postal', 20)->nullable()->comment('Código postal');

            $table->timestamps();

            // Índices
            $table->index('cotizacion_id');
            $table->index('estado_pedido');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ventas');
    }
};
