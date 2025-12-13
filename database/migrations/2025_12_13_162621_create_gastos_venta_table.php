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
        Schema::create('gastos_venta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->string('descripcion', 255)->comment('Descripción del gasto (ej: Transporte, Embalaje, Seguro, etc.)');
            $table->decimal('monto', 10, 2)->default(0)->comment('Monto del gasto');
            $table->date('fecha')->nullable()->comment('Fecha del gasto');
            $table->text('observaciones')->nullable()->comment('Observaciones adicionales sobre el gasto');
            $table->timestamps();

            // Índices
            $table->index('venta_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gastos_venta');
    }
};
