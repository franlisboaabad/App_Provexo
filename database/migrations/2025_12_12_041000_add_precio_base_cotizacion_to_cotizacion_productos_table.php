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
            $table->decimal('precio_base_cotizacion', 10, 2)->nullable()->after('precio_unitario')->comment('Precio base usado en esta cotización');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cotizacion_productos', function (Blueprint $table) {
            $table->dropColumn('precio_base_cotizacion');
        });
    }
};

