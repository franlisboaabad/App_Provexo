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
            $table->decimal('adelanto', 10, 2)->default(0)->comment('Monto de adelanto recibido');
            $table->decimal('monto_transporte', 10, 2)->default(0)->comment('Costo de transporte');
            $table->string('nombre_transporte', 255)->nullable()->comment('Nombre de la empresa/transportista');
            $table->decimal('margen_bruto_con_transporte', 10, 2)->nullable()->comment('Margen bruto calculado con transporte');
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
