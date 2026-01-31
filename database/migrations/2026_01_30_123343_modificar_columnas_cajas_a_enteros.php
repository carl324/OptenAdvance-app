<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->integer('monto_apertura')->change();
            $table->integer('total_ventas')->change();
            $table->integer('total_efectivo')->change();
            $table->integer('monto_cierre_calculado')->change();
            $table->integer('monto_cierre_real')->change();
            $table->integer('diferencia')->change();
        });
    }

    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->decimal('monto_apertura', 12, 2)->change();
            $table->decimal('total_ventas', 12, 2)->change();
            $table->decimal('total_efectivo', 12, 2)->change();
            $table->decimal('monto_cierre_calculado', 12, 2)->change();
            $table->decimal('monto_cierre_real', 12, 2)->change();
            $table->decimal('diferencia', 12, 2)->change();
        });
    }
};