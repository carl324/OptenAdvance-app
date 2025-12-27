<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ventas_detalle', function (Blueprint $table) {
            $table->decimal('iva', 10, 2)->default(0)->after('precio_unitario');
        });
    }

    public function down()
    {
        Schema::table('ventas_detalle', function (Blueprint $table) {
            $table->dropColumn('iva');
        });
    }
};