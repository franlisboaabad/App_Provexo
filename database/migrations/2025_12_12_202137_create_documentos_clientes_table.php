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
        Schema::create('documentos_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('cotizacion_id')->nullable()->constrained('cotizaciones')->onDelete('set null');
            $table->string('titulo', 255);
            $table->string('nombre_archivo', 255);
            $table->string('ruta_archivo', 500);
            $table->enum('tipo_documento', ['factura', 'contrato', 'garantia', 'orden_compra', 'otro'])->default('otro');
            $table->string('numero_documento', 100)->nullable();
            $table->date('fecha_documento')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            // Índices
            $table->index('cliente_id');
            $table->index('cotizacion_id');
            $table->index('tipo_documento');
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documentos_clientes');
    }
};
