<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // En MySQL/MariaDB necesitamos modificar la columna directamente
        DB::statement("ALTER TABLE cotizaciones MODIFY COLUMN estado ENUM('pendiente', 'aprobada', 'rechazada', 'vencida', 'ganado', 'perdido') DEFAULT 'pendiente'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revertir al enum original
        DB::statement("ALTER TABLE cotizaciones MODIFY COLUMN estado ENUM('pendiente', 'aprobada', 'rechazada', 'vencida') DEFAULT 'pendiente'");
    }
};
