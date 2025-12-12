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
        Schema::table('cotizacion_productos', function (Blueprint $table) {
            // Campos de entrada manual
            $table->decimal('peso_unidad', 10, 4)->nullable()->after('subtotal')->comment('Peso por unidad en kg');
            $table->decimal('flete_tonelada', 10, 2)->nullable()->after('peso_unidad')->comment('Flete por tonelada en S/');
            $table->decimal('margen_porcentaje', 5, 2)->nullable()->after('flete_tonelada')->comment('Porcentaje de margen unitario');

            // Campos calculados
            $table->decimal('flete_unitario', 10, 4)->nullable()->after('margen_porcentaje')->comment('Flete unitario calculado');
            $table->decimal('costo_mas_flete', 10, 2)->nullable()->after('flete_unitario')->comment('Costo + flete calculado');
            $table->decimal('total_kg', 10, 4)->nullable()->after('costo_mas_flete')->comment('Total de kilogramos (cantidad × peso_unidad)');
            $table->decimal('margen_total', 10, 2)->nullable()->after('total_kg')->comment('Margen total calculado');
            $table->decimal('flete_total', 10, 2)->nullable()->after('margen_total')->comment('Flete total en S/');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cotizacion_productos', function (Blueprint $table) {
            $table->dropColumn([
                'peso_unidad',
                'flete_tonelada',
                'margen_porcentaje',
                'flete_unitario',
                'costo_mas_flete',
                'total_kg',
                'margen_total',
                'flete_total',
            ]);
        });
    }
};

