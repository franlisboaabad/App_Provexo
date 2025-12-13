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
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('numero_cotizacion', 100)->unique();
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada', 'vencida', 'ganado', 'perdido'])->default('pendiente');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('impuesto_total', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Índices
            $table->index('cliente_id');
            $table->index('numero_cotizacion');
            $table->index('estado');
            $table->index('fecha_emision');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};
