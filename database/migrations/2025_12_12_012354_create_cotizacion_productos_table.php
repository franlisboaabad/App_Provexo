<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cotizacion_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cotizacion_id')->constrained('cotizaciones')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('precio_base_cotizacion', 10, 2)->nullable()->comment('Precio base usado en esta cotización');
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('impuesto', 5, 2)->default(0)->comment('Porcentaje de impuesto aplicado');
            $table->decimal('subtotal', 10, 2);

            // Campos de flete (entrada manual)
            $table->decimal('peso_unidad', 10, 4)->nullable()->comment('Peso por unidad en kg');
            $table->decimal('flete_tonelada', 10, 2)->nullable()->comment('Flete por tonelada en S/');
            $table->decimal('margen_porcentaje', 5, 2)->nullable()->comment('Porcentaje de margen unitario');

            // Campos calculados de flete
            $table->decimal('flete_unitario', 10, 4)->nullable()->comment('Flete unitario calculado');
            $table->decimal('costo_mas_flete', 10, 2)->nullable()->comment('Costo + flete calculado');
            $table->decimal('total_kg', 10, 4)->nullable()->comment('Total de kilogramos (cantidad × peso_unidad)');
            $table->decimal('margen_total', 10, 2)->nullable()->comment('Margen total calculado');
            $table->decimal('flete_total', 10, 2)->nullable()->comment('Flete total en S/');

            $table->timestamps();

            // Índices
            $table->index('cotizacion_id');
            $table->index('producto_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacion_productos');
    }
};
