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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->onDelete('cascade');
            $table->string('codigo_producto', 100)->unique();
            $table->string('descripcion', 255);
            $table->decimal('precio_base', 10, 2);
            $table->decimal('precio_venta', 10, 2);
            $table->decimal('impuesto', 5, 2)->default(0)->comment('Porcentaje de impuesto/IVA');
            $table->integer('stock')->default(0);
            $table->string('unidad_medida', 50)->default('unidad');
            $table->string('imagen')->nullable();
            $table->string('categoria', 100)->nullable();
            $table->string('marca', 100)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            // Índices para mejorar rendimiento
            $table->index('proveedor_id');
            $table->index('codigo_producto');
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};

