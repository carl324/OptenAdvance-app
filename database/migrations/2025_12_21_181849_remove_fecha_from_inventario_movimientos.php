<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('inventario_movimientos', function (Blueprint $table) {
            $table->dropColumn('fecha');
        });
    }

    public function down()
    {
        Schema::table('inventario_movimientos', function (Blueprint $table) {
            $table->timestamp('fecha')->useCurrent();
        });
    }
};
