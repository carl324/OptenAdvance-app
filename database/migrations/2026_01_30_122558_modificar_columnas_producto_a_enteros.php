<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->integer('precio')->change();
            $table->integer('iva')->change();
            $table->integer('precio_con_iva')->change();
        });
    }

    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('precio', 10, 2)->change();
            $table->decimal('iva', 5, 2)->change();
            $table->decimal('precio_con_iva', 10, 2)->change();
        });
    }
};
