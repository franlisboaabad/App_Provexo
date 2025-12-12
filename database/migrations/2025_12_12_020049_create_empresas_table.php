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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social', 255);
            $table->string('nombre_comercial', 255)->nullable();
            $table->string('ruc', 20)->unique();
            $table->string('direccion', 500)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('distrito', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('web', 255)->nullable();
            $table->string('representante_legal', 255)->nullable();
            $table->string('tipo_empresa', 50)->nullable()->comment('EIRL, SAC, SRL, etc.');
            $table->text('descripcion')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('activo')->default(true);
            $table->boolean('es_principal')->default(false)->comment('Indica si es la empresa principal del sistema');
            $table->timestamps();

            // Índices
            $table->index('ruc');
            $table->index('activo');
            $table->index('es_principal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
