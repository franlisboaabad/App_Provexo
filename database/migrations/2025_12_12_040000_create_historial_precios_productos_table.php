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
        Schema::create('historial_precios_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('cotizacion_id')->nullable()->constrained('cotizaciones')->onDelete('set null');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->decimal('precio_base_anterior', 10, 2);
            $table->decimal('precio_base_nuevo', 10, 2);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Índices
            $table->index('producto_id');
            $table->index('cotizacion_id');
            $table->index('usuario_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_precios_productos');
    }
};

