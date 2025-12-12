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
        Schema::create('cuentas_bancarias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('banco', 255)->comment('Nombre del banco');
            $table->string('tipo_cuenta', 50)->nullable()->comment('Ahorros, Corriente, etc.');
            $table->string('numero_cuenta', 50)->comment('Número de cuenta bancaria');
            $table->string('numero_cuenta_interbancario', 50)->nullable()->comment('CCI (Código de Cuenta Interbancario)');
            $table->string('moneda_cuenta', 10)->default('PEN')->comment('PEN, USD, etc.');
            $table->boolean('activa')->default(true)->comment('Indica si la cuenta está activa');
            $table->boolean('es_principal')->default(false)->comment('Cuenta principal de la empresa');
            $table->timestamps();

            // Índices
            $table->index('empresa_id');
            $table->index('banco');
            $table->index('activa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas_bancarias');
    }
};
