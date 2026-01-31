<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->integer('total_ventas')->default(0)->change();
        });
    }

    public function down()
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->integer('total_ventas')->nullable()->default(NULL)->change();
        });
    }
};
