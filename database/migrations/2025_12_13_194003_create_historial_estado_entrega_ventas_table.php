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
        Schema::create('historial_estado_entrega_ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->enum('estado_entrega', ['registro_creado', 'recogido', 'en_bodega_origen', 'salida_almacen', 'en_transito', 'en_reparto', 'entregado'])->comment('Estado de entrega registrado');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null')->comment('Usuario que realizó el cambio');
            $table->text('observaciones')->nullable()->comment('Observaciones sobre el cambio de estado');
            $table->timestamps();

            // Índices
            $table->index('venta_id');
            $table->index('estado_entrega');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historial_estado_entrega_ventas');
    }
};
