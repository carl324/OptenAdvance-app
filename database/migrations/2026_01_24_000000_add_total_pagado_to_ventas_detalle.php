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
        if (!Schema::hasTable('ventas_detalle')) {
            return;
        }

        Schema::table('ventas_detalle', function (Blueprint $table) {
            // Añadir columna total_pagado con valor por defecto 0.00 para mantener compatibilidad
            $table->decimal('total_pagado', 10, 2)->default(0.00)->after('subtotal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('ventas_detalle')) {
            return;
        }

        Schema::table('ventas_detalle', function (Blueprint $table) {
            if (Schema::hasColumn('ventas_detalle', 'total_pagado')) {
                $table->dropColumn('total_pagado');
            }
        });
    }
};
