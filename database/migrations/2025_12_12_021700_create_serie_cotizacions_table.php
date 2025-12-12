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
        Schema::create('series_cotizacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('serie', 10)->comment('Código de la serie (ej: COT, FACT, etc.)');
            $table->string('descripcion', 255)->nullable()->comment('Descripción de la serie');
            $table->boolean('activa')->default(true)->comment('Indica si la serie está activa');
            $table->boolean('es_principal')->default(false)->comment('Serie principal de la empresa');
            $table->timestamps();

            // Índices
            $table->index('empresa_id');
            $table->index('serie');
            $table->unique(['empresa_id', 'serie']); // No duplicar serie en la misma empresa
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('series_cotizacion');
    }
};
