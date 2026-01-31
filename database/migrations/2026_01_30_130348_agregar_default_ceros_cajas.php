<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->integer('total_efectivo')->default(0)->change();
            $table->integer('monto_cierre_calculado')->default(0)->change();
            $table->integer('monto_cierre_real')->default(0)->change();
            $table->integer('diferencia')->default(0)->change();
        });
    }

    public function down()
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->integer('total_efectivo')->default(null)->change();
            $table->integer('monto_cierre_calculado')->default(null)->change();
            $table->integer('monto_cierre_real')->default(null)->change();
            $table->integer('diferencia')->default(null)->change();
        });
    }
};
